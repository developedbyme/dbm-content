<?php
	namespace DbmContent;
	
	use \WP_Query;
	
	// \DbmContent\ChangePostHooks
	class ChangePostHooks {
		
		function __construct() {
			//echo("\DbmContent\ChangePostHooks::__construct<br />");
			
			
		}
		
		protected function register_hook_for_type($type, $hook_name) {
			add_action('wprr/admin/change_post/'.$type, array($this, $hook_name), 10, 3);
		}
		
		public function register() {
			//echo("\DbmContent\ChangePostHooks::register<br />");
			
			$this->register_hook_for_type('dbm/relation', 'hook_set_relation');
			$this->register_hook_for_type('dbm/autoDbmContent', 'hook_auto_dbm_content');
			$this->register_hook_for_type('dbm/inAdminGrouping', 'hook_in_admin_grouping');
			$this->register_hook_for_type('dbm/addTermFromOwner', 'hook_add_term_from_owner');
			
			$this->register_hook_for_type('dbm/addIncomingRelation', 'hook_addIncomingRelation');
			$this->register_hook_for_type('dbm/addOutgoingRelation', 'hook_addOutgoingRelation');
			
			$this->register_hook_for_type('dbm/addObjectUserRelation', 'hook_addObjectUserRelation');
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
		}
		
		public function hook_add_term_from_owner($data, $post_id, $logger) {
			//echo("\DbmContent\ChangePostHooks::hook_add_term_from_owner<br />");
			
			$owner_id = $data['value'];
			
			$owner = get_post($owner_id);
			if($owner) {
				$meta_name = 'dbm_relation_term_'.$data['group'];
				$term_id = (int)get_post_meta($owner_id, $meta_name, true);
				
				wp_add_object_terms($post_id, array($term_id), 'dbm_relation');
			}
		}
		
		public function hook_addIncomingRelation($data, $post_id, $logger) {
			$related_id = $data['value'];
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
			$related_id = $data['value'];
			$type = $data['relationType'];
			
			$relation_id = dbm_create_draft_object_relation($post_id, $related_id, $type);
			if(isset($data['makePrivate']) && $data['makePrivate']) {
				$dbm_post = dbm_get_post($relation_id);
				$dbm_post->change_status('private');
			}
			
			delete_post_meta($post_id, 'dbm/objectRelations/outgoing');
			delete_post_meta($related_id, 'dbm/objectRelations/incoming');
			
			$logger->add_return_data('relationId', $relation_id);
		}
		
		public function hook_addObjectUserRelation($data, $post_id, $logger) {
			$related_id = $data['value'];
			$type = $data['relationType'];
			
			$dbm_post = dbm_get_post($post_id);
			$relation_id = $dbm_post->add_user_relation($related_id, $type);
			
			$logger->add_return_data('relationId', $relation_id);
		}
		
		public static function test_import() {
			echo("Imported \DbmContent\ChangePostHooks<br />");
		}
	}
?>