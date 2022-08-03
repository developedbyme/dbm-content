<?php
	namespace DbmContent;
	
	use \WP_Query;
	
	// \DbmContent\ChangePostHooks
	class ChangePostHooks {
		
		function __construct() {
			//echo("\DbmContent\ChangePostHooks::__construct<br />");
			
			
		}
		
		protected function register_hook_for_type($type, $hook_name = null) {
			
			if(!$hook_name) {
				$hook_type = $type;
				$hook_type = implode('_', explode('/', $hook_type));
				$hook_name = 'change_'.$hook_type;
			}
			
			add_action('wprr/admin/change_post/dbm/'.$type, array($this, $hook_name), 10, 3);
		}
		
		public function register() {
			//echo("\DbmContent\ChangePostHooks::register<br />");
			
			$this->register_hook_for_type('relation', 'hook_set_relation');
			$this->register_hook_for_type('autoDbmContent', 'hook_auto_dbm_content');
			$this->register_hook_for_type('inAdminGrouping', 'hook_in_admin_grouping');
			$this->register_hook_for_type('addTermFromOwner', 'hook_add_term_from_owner');
			
			$this->register_hook_for_type('addIncomingRelation', 'hook_addIncomingRelation');
			$this->register_hook_for_type('addOutgoingRelation', 'hook_addOutgoingRelation');
			
			$this->register_hook_for_type('addTypeRelation');
			
			$this->register_hook_for_type('endIncomingRelations');
			$this->register_hook_for_type('endOutgoingRelations');
			
			$this->register_hook_for_type('replaceIncomingRelation');
			$this->register_hook_for_type('replaceOutgoingRelation');
			
			$this->register_hook_for_type('order', 'hook_order');
			
			$this->register_hook_for_type('addObjectUserRelation', 'hook_addObjectUserRelation');
			
			$this->register_hook_for_type('process/skipPart');
			$this->register_hook_for_type('process/skipPart/byIdentifier');
			$this->register_hook_for_type('process/completePart');
			$this->register_hook_for_type('process/completePart/byIdentifier');
			
			$this->register_hook_for_type('clearCache');
			$this->register_hook_for_type('createUserFromItem');
			
		}
		
		protected function get_relation_terms($data, $parent_path = null) {
			$terms = $data['value'];
			if(!is_array($terms)) {
				$terms = explode(',', $terms);
			}
			
			if(isset($data['field'])) {
				switch($data['field']) {
					case 'slugPath':
						$terms = \DbmContent\OddCore\Utils\TaxonomyFunctions::get_ids_from_terms(\DbmContent\OddCore\Utils\TaxonomyFunctions::get_terms_by_slug_paths($terms, 'dbm_relation'));
						break;
					case 'slugPathFromParent':
						if($parent_path) {
							$new_terms = array();
							foreach($terms as $term) {
								$new_terms[] = $parent_path.'/'.$term;
							}
						}
						$terms = $new_terms;
						$terms = \DbmContent\OddCore\Utils\TaxonomyFunctions::get_ids_from_terms(\DbmContent\OddCore\Utils\TaxonomyFunctions::get_terms_by_slug_paths($terms, 'dbm_relation'));
						break;
				}
			}
			
			return $terms;
		}
		
		public function hook_set_relation($data, $post_id, $logger) {
			//echo("\DbmContent\ChangePostHooks::hook_set_relation<br />");
			
			$parent_slug = $data['path'];
			$ids = $this->get_relation_terms($data, $parent_slug);
			
			$parent = dbm_get_relation_by_path($parent_slug);
			
			dbm_replace_relations($post_id, $parent, $ids);
		}
		
		public function hook_auto_dbm_content($data, $post_id, $logger) {
			
			$post = get_post($post_id);
			
			$dbm_content_object = dbm_get_content_object_for_type_and_relation($post_id);
			
			do_action('dbm_content/parse_dbm_content', $dbm_content_object, $post_id, $post);
		}
		
		public function hook_in_admin_grouping($data, $post_id, $logger) {
			
			$path = 'admin-grouping/'.$data['value'];
			
			$parent = dbm_get_post_id_by_type_and_relation('any', array($path));
			
			if($parent) {
				$args = array(
					'ID' => $post_id,
					'post_parent' => $parent
				);
				
				wp_update_post($args);
			}
			else {
				$logger->add_log('No admin group '.$path);
			}
		}
		
		public function hook_add_term_from_owner($data, $post_id, $logger) {
			//echo("\DbmContent\ChangePostHooks::hook_add_term_from_owner<br />");
			
			$owner_id = $data['value'];
			
			$owner = get_post($owner_id);
			if($owner) {
				$meta_name = 'dbm_relation_term_'.$data['group'];
				$term_id = (int)get_post_meta($owner_id, $meta_name, true);
				
				if($term_id) {
					wp_add_object_terms($post_id, array($term_id), 'dbm_relation');
				}
				else {
					$logger->add_log('No owned term for group '.$data['group']);
				}
			}
			else {
				$logger->add_log('No owner');
			}
		}
		
		public function hook_addIncomingRelation($data, $post_id, $logger) {
			$related_id = (int)$data['value'];
			$type = $data['relationType'];
			
			$relation_id = dbm_create_draft_object_relation($related_id, $post_id, $type);
			if(isset($data['makePrivate']) && $data['makePrivate']) {
				$dbm_post = dbm_get_post($relation_id);
				$dbm_post->change_status('private');
			}
			
			delete_post_meta($post_id, 'dbm/objectRelations/incoming');
			delete_post_meta($related_id, 'dbm/objectRelations/outgoing');
			
			$logger->add_return_data('relationId', $relation_id);
		}
		
		public function hook_addOutgoingRelation($data, $post_id, $logger) {
			$related_id = (int)$data['value'];
			$type = $data['relationType'];
			
			$relation_id = dbm_create_draft_object_relation($post_id, $related_id, $type);
			if(isset($data['makePrivate']) && $data['makePrivate']) {
				$dbm_post = dbm_get_post($relation_id);
				$dbm_post->change_status('private');
			}
			
			delete_post_meta($post_id, 'dbm/objectRelations/outgoing');
			delete_post_meta($related_id, 'dbm/objectRelations/incoming');
			
			$prefix = '';
			if(isset($data['returnPrefix']) && $data['returnPrefix']) {
				$prefix = $data['returnPrefix'].'/';
			}
			
			$logger->add_return_data($prefix.'relationId', $relation_id);
		}
		
		public function change_addTypeRelation($data, $post_id, $logger) {
			//var_dump("change_addTypeRelation");
			$type_name = $data['value'];
			$type = $data['type'];
			
			$related_id = dbmtc_get_or_create_type($type, $type_name);
			
			$relation_id = dbm_create_draft_object_relation($related_id, $post_id, 'for');
			$dbm_post = dbm_get_post($relation_id);
			$dbm_post->change_status('private');
			
			delete_post_meta($post_id, 'dbm/objectRelations/incoming');
			delete_post_meta($related_id, 'dbm/objectRelations/outgoing');
			
			$logger->add_return_data('relationId', $relation_id);
		}
		
		public function change_endIncomingRelations($data, $post_id, $logger) {
			$type = $data['relationType'];
			$object_type = $data['objectType'];
			
			$post = dbm_get_post($post_id);
			$current_time = time();
			
			$has_relation = false;
			$existing_relations = $post->get_encoded_incoming_relations_by_type($type, $object_type);
			foreach($existing_relations as $existing_relation) {
				$existing_relation_id = $existing_relation["id"];
				$existing_relation_group = dbmtc_get_group($existing_relation_id);
				
				$existing_relation_group->set_field('endAt', $current_time, 'Ending incoming relations');
			}
			
			delete_post_meta($post_id, 'dbm/objectRelations/incoming');
			delete_post_meta($related_id, 'dbm/objectRelations/outgoing');
			
		}
		
		public function change_endOutgoingRelations($data, $post_id, $logger) {
			$type = $data['relationType'];
			$object_type = $data['objectType'];
			
			$post = dbm_get_post($post_id);
			$current_time = time();
			
			$has_relation = false;
			$existing_relations = $post->get_encoded_outgoing_relations_by_type($type, $object_type);
			foreach($existing_relations as $existing_relation) {
				$existing_relation_id = $existing_relation["id"];
				$existing_relation_group = dbmtc_get_group($existing_relation_id);
				
				$existing_relation_group->set_field('endAt', $current_time, 'Replacement of outgoing relation');
			}
			
			delete_post_meta($related_id, 'dbm/objectRelations/incoming');
			delete_post_meta($post_id, 'dbm/objectRelations/outgoing');
		}
		
		public function change_replaceIncomingRelation($data, $post_id, $logger) {
			$related_id = (int)$data['value'];
			$type = $data['relationType'];
			$object_type = $data['objectType'];
			
			$post = dbm_get_post($post_id);
			$current_time = time();
			
			$has_relation = false;
			$existing_relations = $post->get_encoded_incoming_relations_by_type($type, $object_type);
			foreach($existing_relations as $existing_relation) {
				$existing_relation_id = $existing_relation["id"];
				$existing_relation_group = dbmtc_get_group($existing_relation_id);
				if($existing_relation["fromId"] == $related_id) {
					if((int)$existing_relation_group->get_field_value('endAt') !== -1) {
						$existing_relation_group->set_field('endAt', -1, 'Replacement of incoming relation');
					}
					$relation_id = $existing_relation_id;
					$has_relation = true;
				}
				else {
					$existing_relation_group->set_field('endAt', $current_time, 'Replacement of incoming relation');
				}
			}
			
			if(!$has_relation) {
				$relation_id = dbm_create_draft_object_relation($related_id, $post_id, $type);
				$dbm_post = dbmtc_get_group($relation_id);
				$dbm_post->change_status('private');
				$dbm_post->set_field('startAt', $current_time, 'Replacement of incoming relation');
			}
			
			delete_post_meta($post_id, 'dbm/objectRelations/incoming');
			delete_post_meta($related_id, 'dbm/objectRelations/outgoing');
			
			$prefix = '';
			if(isset($data['returnPrefix']) && $data['returnPrefix']) {
				$prefix = $data['returnPrefix'].'/';
			}
			
			$logger->add_return_data($prefix.'relationId', $relation_id);
		}
		
		public function change_replaceOutgoingRelation($data, $post_id, $logger) {
			$related_id = (int)$data['value'];
			$type = $data['relationType'];
			$object_type = $data['objectType'];
			
			$post = dbm_get_post($post_id);
			$current_time = time();
			
			$has_relation = false;
			$existing_relations = $post->get_encoded_outgoing_relations_by_type($type, $object_type);
			foreach($existing_relations as $existing_relation) {
				$existing_relation_id = $existing_relation["id"];
				$existing_relation_group = dbmtc_get_group($existing_relation_id);
				if($existing_relation["toId"] == $related_id) {
					if((int)$existing_relation_group->get_field_value('endAt') !== -1) {
						$existing_relation_group->set_field('endAt', -1, 'Replacement of outgoing relation');
					}
					$relation_id = $existing_relation_id;
					$has_relation = true;
				}
				else {
					$existing_relation_group->set_field('endAt', $current_time, 'Replacement of outgoing relation');
				}
			}
			
			if(!$has_relation) {
				$relation_id = dbm_create_draft_object_relation($post_id, $related_id, $type);
				$dbm_post = dbmtc_get_group($relation_id);
				$dbm_post->change_status('private');
				$dbm_post->set_field('startAt', $current_time, 'Replacement of outgoing relation');
			}
			
			delete_post_meta($related_id, 'dbm/objectRelations/incoming');
			delete_post_meta($post_id, 'dbm/objectRelations/outgoing');
			
			$prefix = '';
			if(isset($data['returnPrefix']) && $data['returnPrefix']) {
				$prefix = $data['returnPrefix'].'/';
			}
			
			$logger->add_return_data($prefix.'relationId', $relation_id);
		}
		
		public function hook_addObjectUserRelation($data, $post_id, $logger) {
			$related_id = (int)$data['value'];
			$type = $data['relationType'];
			
			$dbm_post = dbm_get_post($post_id);
			$relation_id = dbm_create_draft_object_user_relation($post_id, $related_id, $type);
			
			if(isset($data['makePrivate']) && $data['makePrivate']) {
				$dbm_post = dbm_get_post($relation_id);
				$dbm_post->change_status('private');
			}
			
			delete_post_meta($post_id, 'dbm/userRelations');
			
			$logger->add_return_data('relationId', $relation_id);
		}
		
		public function hook_order($data, $post_id, $logger) {
			$new_order = $data['value'];
			$for_type = $data['forType'];
			
			$dbm_post = dbm_get_post($post_id);
			
			$has_updated = false;
			$order_ids = $dbm_post->get_outgoing_relations('relation-order-by', 'relation-order');
			foreach($order_ids as $order_id) {
				$order_post_id = get_post_meta($order_id, 'toId', true);
				$current_type = get_post_meta($order_post_id, 'forType', true);
				
				if($for_type === $current_type) {
					update_post_meta($order_post_id, 'order', $new_order);
					$has_updated = true;
					$logger->add_return_data('orderId', $order_post_id);
					break;
				}
			}
			
			if(!$has_updated) {
				$order_id = dbm_create_data('Order '.$for_type.' for '.$post_id, 'relation-order', 'relation-orders');
				update_post_meta($order_id, 'order', $new_order);
				update_post_meta($order_id, 'forType', $for_type);
				$dbm_post->add_outgoing_relation_by_name($order_id, 'relation-order-by');
				
				$dbm_order_post = dbm_get_post($order_id);
				$dbm_order_post->change_status('private');
				
				$logger->add_return_data('orderId', $order_id);
			}
		}
		
		public function change_process_skipPart($data, $post_id, $logger) {
			//echo("change_process_skipPart");
			
			$part_id = $data['value'];
			
			dbm_get_process_for_item($post_id)->skip_part($part_id);
		}
		
		public function change_process_skipPart_byIdentifier($data, $post_id, $logger) {
			//echo("change_process_skipPart_byIdentifier");
			
			$part_identifier = $data['value'];
			
			dbm_get_process_for_item($post_id)->skip_part_by_identifier($part_identifier);
		}
		
		public function change_process_completePart($data, $post_id, $logger) {
			//echo("change_process_completePart");
			
			$part_id = $data['value'];
			
			dbm_get_process_for_item($post_id)->complete_part($part_id);
		}
		
		public function change_process_completePart_byIdentifier($data, $post_id, $logger) {
			//echo("change_process_completePart_byIdentifier");
			
			$part_identifier = $data['value'];
			
			dbm_get_process_for_item($post_id)->complete_part_by_identifier($part_identifier);
		}
		
		public function change_clearCache($data, $post_id, $logger) {
			//echo("change_clearCache");
			
			$post = dbm_get_post($post_id);
			$post->clear_cache();
		}
		
		public function change_createUserFromItem($data, $post_id, $logger) {
			//echo("change_createUserFromItem");
			
			$post = dbmtc_get_group($post_id);
			
			$existing_relation = $post->get_single_user_by_relation('user-for');
			if(!$existing_relation) {
				
				//METODO: check that we are ok to add a user
				
				$email = $post->get_field_value('email'); //METODO: apply filters
				 //METODO: this function can throw
				
				if($email) {
					$user = dbmtc_get_user($email);
					if($user) {
						$post->add_user_relation($user->ID, 'user-for');
						$logger->add_return_data('userId', $user->ID);
					}
					else {
						$user_id = wp_create_user($email, wp_generate_password(), $email);
						
						$post->add_user_relation($user_id, 'user-for');
						
						$name = $post->get_field_value('name'); //METODO: this function can throw
						
						if($name) {
							wp_update_user(array(
								'ID' => $user_id,
								'first_name' => $name['firstName'],
								'last_name' => $name['lastName'],
								'display_name' => $name['firstName'].' '.$name['lastName'],
							));
						}
						
						//METODO: add setup hook
						
						$logger->add_return_data('userId', $user_id);
					}
				}
				
				
			}
			else {
				$logger->add_return_data('userId', $existing_relation);
			}
		}
		
		
		public static function test_import() {
			echo("Imported \DbmContent\ChangePostHooks<br />");
		}
	}
?>