<?php
	namespace DbmContent;

	class DbmPost {

		protected $id = array();

		function __construct($id) {
			//echo("\DbmContent\DbmPost::__construct<br />");
			
			$this->id = $id;
		}
		
		public function get_id() {
			return $this->id;
		}
		
		public function data_api_post() {
			return wprr_get_data_api()->wordpress()->get_post($this->get_id());
		}
		
		public function get_title() {
			return get_post_field('post_title', $this->get_id());
		}
		
		public function get_content() {
			return apply_filters('the_content', get_post_field('post_content', $this->get_id()));
		}
		
		public function get_post_type() {
			return get_post_type($this->id);
		}
		
		public function exists() {
			return get_post_status($this->id) !== false;
		}
		
		public function change_status($status) {
			wprr_performance_tracker()->start_meassure('DbmPost change_status');
			global $wpdb;
			$wpdb->update( $wpdb->posts, array('post_status' => $status), array('ID' => $this->get_id()));
				
			wprr_performance_tracker()->stop_meassure('DbmPost change_status');
			
			return $this;
		}
		
		public function publish() {
			$this->change_status('publish');
			
			return $this;
		}
		
		public function make_private() {
			$this->change_status('private');
			
			return $this;
		}
		
		public function get_status() {
			return get_post_status($this->get_id());
		}
		
		public function get_permalink() {
			return get_the_permalink($this->get_id());
		}
		
		protected function update_post_data($field, $value) {
			return wp_update_post(array(
				'ID' => $this->get_id(),
				$field => $value
			));
		}
		
		public function set_title($title) {
			$this->update_post_data('post_title', $title);
			
			return $this;
		}
		
		public function add_meta($field, $value) {
			add_post_meta($this->get_id(), $field, $value);
			
			return $this;
		}
		
		public function update_meta($field, $value) {
			update_post_meta($this->get_id(), $field, $value);
			
			return $this;
		}
		
		public function get_meta($field) {
			return get_post_meta($this->get_id(), $field, true);
		}
		
		public function get_enum_identifier($type) {
			$type_id = dbm_new_query('dbm_data')->include_private()->include_only_type('type/enum-type')->add_meta_query('identifier', $type)->get_post_id();
			if($type_id) {
				$enum_ids = $this->object_relation_query('in:for:enum');
				foreach($enum_ids as $enum_id) {
					$enum_post = dbmtc_get_group($enum_id);
					if(in_array($type_id, $enum_post->object_relation_query('in:for:type/enum-type'))) {
						return $enum_post->get_field_value('identifier');
					}
				}
			}
			return null;
		}
		
		public function add_type($id) {
			
			wp_set_post_terms($this->get_id(), array($id), 'dbm_type', true);
			
			return $this;
		}
		
		public function add_type_by_name($path) {
			
			$term_ids = dbm_get_ids_from_terms(array(dbm_get_type_by_path($path)));
			wp_set_post_terms($this->get_id(), $term_ids, 'dbm_type', true);
			
			return $this;
		}
		
		public function add_relation($id) {
			
			wp_set_post_terms($this->get_id(), array($id), 'dbm_relation', true);
			
			return $this;
		}
		
		public function add_relation_by_name($path) {
			
			$term_ids = dbm_get_ids_from_terms(array(dbm_get_relation_by_path($path)));
			wp_set_post_terms($this->get_id(), $term_ids, 'dbm_relation', true);
			
			return $this;
		}
		
		public function replace_relations($path, $new_term_ids) {
			
			$old_term_ids = $this->get_relation($path);
			
			wp_remove_object_terms($this->get_id(), $old_term_ids, 'dbm_relation');
			wp_set_post_terms($this->get_id(), $new_term_ids, 'dbm_relation', true);
			
			return $this;
		}
		
		public function set_single_relation_by_name($path) {
			
			$path_array = explode('/', $path);
			$child_name = array_pop($path_array);
			$parent_path = implode('/', $path_array);
			
			dbm_set_single_relation_by_name($this->get_id(), $parent_path, $child_name);
			
			return $this;
		}
		
		public function add_relations_from_post($post_id, $path) {
			
			$copy_post = dbm_get_post($post_id);
			
			$term_ids = $copy_post->get_relation($path);
			wp_set_post_terms($this->get_id(), $term_ids, 'dbm_relation', true);
			
			return $this;
		}
		
		public function get_types() {
			$return_array = array();
		
			$current_terms = wp_get_post_terms($this->get_id(), 'dbm_type');
			foreach($current_terms as $current_term) {
				$return_array[] = $current_term->term_id;
			}
		
			return $return_array;
		}
		
		public function has_type_by_name($path) {
			$term = \DbmContent\OddCore\Utils\TaxonomyFunctions::get_term_by_slugs(explode('/', $path), 'dbm_type');
			if($term) {
				$types = $this->get_types();
				return in_array($term->term_id, $types);
			}
			
			return false;
		}
		
		public function get_subtypes($path) {
			$return_array = array();
		
			$parent_term = \DbmContent\OddCore\Utils\TaxonomyFunctions::get_term_by_slugs(explode('/', $path), 'dbm_type');
		
			$current_terms = wp_get_post_terms($this->get_id(), 'dbm_type');
			foreach($current_terms as $current_term) {
				if($current_term->parent === $parent_term->term_id) {
					$return_array[] = $current_term->term_id;
				}
			}
		
			return $return_array;
		}
		
		public function get_relation($path) {
			$return_array = array();
		
			$parent_term = \DbmContent\OddCore\Utils\TaxonomyFunctions::get_term_by_slugs(explode('/', $path), 'dbm_relation');
		
			$current_terms = wp_get_post_terms($this->get_id(), 'dbm_relation');
			foreach($current_terms as $current_term) {
				if($current_term->parent === $parent_term->term_id) {
					$return_array[] = $current_term->term_id;
				}
			}
		
			return $return_array;
		}
		
		public function get_single_relation($path) {
			$relations = $this->get_relation($path);
			if(!empty($relations)) {
				return $relations[0];
			}
		
			return null;
		}
		
		public function dbm_get_owned_relation($group) {
			$meta_name = 'dbm_relation_term_'.$group;
			$term_id = (int)$this->get_meta($meta_name, true);
		
			if($term_id) {
				return get_term_by('id', $term_id, 'dbm_relation');
			}
		
			return null;
		}
		
		public function add_outgoing_relation_by_name($to_object_id, $type_path, $start_time = -1) {
			$related_post = wprr_get_data_api()->wordpress()->get_post($to_object_id);
			$relation = $this->data_api_post()->editor()->add_outgoing_relation_by_name($related_post, $type_path, $start_time, true);
			
			return $new_relation_id;
		}
		
		public function add_incoming_relation_by_name($from_object_id, $type_path, $start_time = -1) {
			$related_post = wprr_get_data_api()->wordpress()->get_post($to_object_id);
			$this->data_api_post()->editor()->add_outgoing_relation_by_name($related_post, $type_path, $start_time, true);
			
			return $new_relation_id;
		}
		
		public function add_user_relation($user_id, $type_path) {
			$new_relation_id = dbm_create_object_user_relation($this->get_id(), $user_id, $type_path);
			
			delete_post_meta($this->get_id(), 'dbm/userRelations');
			
			return $new_relation_id;
		}
		
		public function end_outgoing_relations_to_type($type_path, $object_type, $at_time = -1) {
			//var_dump('end_outgoing_relations_to_type');
			
			$this->data_api_post()->editor()->end_all_outgoing_relations_by_name($type_path, $object_type, $at_time);
			
			return $this;
		}
		
		public function end_incoming_relations_from_type($type_path, $object_type, $at_time = -1) {
			//var_dump('end_incoming_relations_from_type');
			
			$this->data_api_post()->editor()->end_all_incoming_relations_by_name($type_path, $object_type, $at_time);
			
			return $this;
		}
		
		public function set_order($new_order, $for_type) {
			
			$order = $this->data_api_post()->editor()->set_order($new_order, $for_type);
			
			return $order->get_id();
		}
		
		public function replace_object_property_value($property_name, $value) {
			$property_id = $this->get_single_object_property($property_name);
			
			if(!$property_id) {
				$property_id = dbm_create_data('Object property '.$property_name.' for '.$this->get_id(), 'object-property');
				$post = dbm_get_post($property_id);
				$post->add_type_by_name('identifiable-item');
				$post->add_type_by_name('value-item');
				
				$post->update_meta('identifier', $property_name);
				
				$post->add_outgoing_relation_by_name($this->get_id(), 'for', time());
				
				$post->make_private();
			}
			else {
				$post = dbm_get_post($property_id);
			}
			
			$post->update_meta('value', $value);
			
			return $property_id;
		}
		
		public function replace_linked_object_property($property_name, $pointing_to_id) {
			$property_id = $this->get_single_object_property($property_name);
			
			if(!$property_id) {
				$property_id = dbm_create_data('Object property '.$property_name.' for '.$this->get_id(), 'object-property');
				$post = dbm_get_post($property_id);
				$post->add_type_by_name('identifiable-item');
				$post->add_type_by_name('object-property/linked-object-property');
				
				$post->update_meta('identifier', $property_name);
				$post->add_outgoing_relation_by_name($this->get_id(), 'for', time());
				
				$post->make_private();
			}
			else {
				$post = dbm_get_post($property_id);
				$post->end_outgoing_relations_to_type('pointing-to', null);
			}
			
			$post->add_outgoing_relation_by_name($pointing_to_id, 'pointing-to', time());
			
			return $property_id;
		}
		
		public function get_single_object_property($identifier) {
			
			$post = wprr_get_data_api()->wordpress()->get_post($this->get_id())->single_object_relation_query_with_meta_filter('in:for:object-property', 'identifier', $identifier);
			
			if($post) {
				return $post->get_id();
			}
			
			return 0;
		}
		
		public function get_object_relation_query_without_settings() {
			return dbm_new_query('dbm_object_relation')->set_field('post_status', array('publish', 'private'));
		}
		
		public function add_time_query(&$dbm_query, $time = -1) {
			if($time !== false) {
				if($time === -1) {
					$time = time();
				}
				
				$start_query = array('relation' => 'OR');
				$start_query[] = $dbm_query->create_meta_query_data('startAt', -1, '=', 'numeric');
				$start_query[] = $dbm_query->create_meta_query_data('startAt', $time, '<=', 'numeric');
				
				$end_query = array('relation' => 'OR');
				$end_query[] = $dbm_query->create_meta_query_data('endAt', -1, '=', 'numeric');
				$end_query[] = $dbm_query->create_meta_query_data('endAt', $time, '>', 'numeric');
				
				$dbm_query->add_meta_query_data($start_query);
				$dbm_query->add_meta_query_data($end_query);
			}
			
			return $dbm_query;
		}
		
		public function get_users_by_relation($type_path, $time = -1) {
			
			$user_ids = array();
			$relations = $this->get_encoded_user_relations();
			$relations = $this->filter_by_connection_type($relations, $type_path);
			$relations = $this->filter_by_time($relations, $time);
			
			foreach($relations as $relation) {
				$user_ids[] = $relation['toId'];
			}
			
			return $user_ids;
		}
		
		public function get_single_user_by_relation($type_path, $time = -1) {
			$user_ids = $this->get_users_by_relation($type_path, $time);
			
			if(!empty($user_ids)) {
				return $user_ids[0];
			}
			
			return 0;
		}
		
		public function get_user_relation_ids() {
			$dbm_query = $this->get_object_relation_query_without_settings()->add_meta_query('fromId', $this->get_id())->add_type_by_path('object-user-relation');
			$dbm_query->set_field('post_status', array('publish', 'private'));
			
			$relation_ids = $dbm_query->get_post_ids();
			
			return $relation_ids;
		}
		
		public function get_encoded_user_relations() {
			$cached_value = get_post_meta($this->get_id(), 'dbm/userRelations', true);
			if($cached_value) {
				return $cached_value;
			}
			
			$encoded_relations = array();
			
			$this_id = $this->get_id();
			$all_ids = $this->get_user_relation_ids();
			foreach($all_ids as $id) {
				
				$relation_post = dbm_get_post($id);
				$to_id = (int)$relation_post->get_meta('toId');
				
				$current_object = array(
					'id' => $id,
					'fromId' => $this_id,
					'toId' => $to_id,
					'connectionType' => \DbmContent\OddCore\Utils\TaxonomyFunctions::get_term_slugs_from_ids($relation_post->get_subtypes('object-user-relation'), 'dbm_type')[0],
					'fromTypes' => \DbmContent\OddCore\Utils\TaxonomyFunctions::get_full_term_slugs_from_ids($this->get_types(), 'dbm_type'),
					'startAt' => (int)get_post_meta($id, 'startAt', true),
					'endAt' => (int)get_post_meta($id, 'endAt', true),
					'status' => $relation_post->get_status()
				);
				$encoded_relations[] = $current_object;
			}
			
			update_post_meta($this->get_id(), 'dbm/userRelations', $encoded_relations);
			
			return $encoded_relations;
		}
		
		public function filter_by_connection_type($relations, $connection_type) {
			$return_array = array();
			
			foreach($relations as $relation) {
				if($relation['connectionType'] === $connection_type) {
					$return_array[] = $relation;
				}
			}
			
			return $return_array;
		}
		
		public function filter_by_time($relations, $time = -1) {
			if($time === false) {
				return $relations;
			}
			
			if($time === -1) {
				$time = time();
			}
			
			$return_array = array();
			foreach($relations as $relation) {
				
				$start_at = $relation['startAt'];
				$end_at = $relation['endAt'];
				if(($start_at === -1 || $start_at <= $time) && ($end_at === -1 || $end_at > $time)) {
					$return_array[] = $relation;
				}
			}
			
			return $return_array;
		}
		
		public function get_encoded_incoming_relations() {
			
			$encoded_relations = array();
			
			$this_id = $this->get_id();
			
			$object_types = $this->data_api_post()->get_taxonomy_terms('dbm_type');
			$encoded_this_types = array_map(function($item) {return $item->get_path();}, $object_types);
			
			$types = $this->data_api_post()->get_incoming_direction()->get_types();
			foreach($types as $name => $type) {
				
				$encoded_connection_type = $name;
				
				$relations = $type->get_all_relations();
				
				foreach($relations as $relation) {
					
					$relation_post = wprr_get_data_api()->wordpress()->get_post($relation->get_object_id());
					$object_types = $relation_post->get_taxonomy_terms('dbm_type');
					$encoded_related_types = array_map(function($item) {return $item->get_path();}, $object_types);
					
					$current_object = array(
						'id' => $relation->get_id(),
						'fromId' => $relation->get_object_id(),
						'toId' => $this_id,
						'connectionType' => $encoded_connection_type,
						'fromTypes' => $encoded_related_types,
						'toTypes' => $encoded_this_types,
						'startAt' => $relation->start_at,
						'endAt' => $relation->end_at,
						'status' => $relation_post->get_data('post_status')
					);
					
					$encoded_relations[] = $current_object;
				}
			}
			
			return $encoded_relations;
		}
		
		public function get_encoded_outgoing_relations() {

			$encoded_relations = array();
			
			$this_id = $this->get_id();
			
			$object_types = $this->data_api_post()->get_taxonomy_terms('dbm_type');
			$encoded_this_types = array_map(function($item) {return $item->get_path();}, $object_types);
			
			$types = $this->data_api_post()->get_outgoing_direction()->get_types();
			foreach($types as $name => $type) {
				
				$encoded_connection_type = $name;
				
				$relations = $type->get_all_relations();
				
				foreach($relations as $relation) {
					
					$relation_post = wprr_get_data_api()->wordpress()->get_post($relation->get_object_id());
					$object_types = $relation_post->get_taxonomy_terms('dbm_type');
					$encoded_related_types = array_map(function($item) {return $item->get_path();}, $object_types);
					
					$current_object = array(
						'id' => $relation->get_id(),
						'fromId' => $this_id,
						'toId' => $relation->get_object_id(),
						'connectionType' => $encoded_connection_type,
						'fromTypes' => $encoded_this_types,
						'toTypes' => $encoded_related_types,
						'startAt' => $relation->start_at,
						'endAt' => $relation->end_at,
						'status' => $relation_post->get_data('post_status')
					);
					
					$encoded_relations[] = $current_object;
				}
			}
			
			return $encoded_relations;
		}
		
		public function resolve_outgoing_relations($relations) {
			$ids = array_map(function($item) {return $item['toId'];}, $relations);
			
			return $ids;
		}
		
		public function resolve_incoming_relations($relations) {
			$ids = array_map(function($item) {return $item['fromId'];}, $relations);
			
			return $ids;
		}
		
		public function get_outgoing_relations($type_path, $object_type, $time = -1) {
			
			$relations = wprr_get_data_api()->wordpress()->get_post($this->get_id())->get_outgoing_direction()->get_type($type_path)->get_relations($object_type, $time);
			$ids = array_map(function($item) {return $item->get_id();}, $relations);
			
			return $ids;
		}
		
		public function get_single_outgoing_relation($type_path, $object_type, $time = -1) {
			
			$ids = $this->get_outgoing_relations($type_path, $object_type, $time);
			$count = count($ids);
			if($count > 0) {
				if($count > 1) {
					//METODO: error message
				}
				
				return $ids[0];
			}
			//METODO: error message
			
			return null;
		}
		
		public function get_all_outgoing_relations() {
			$relations = $this->data_api_post()->get_outgoing_direction()->get_all_relations();
			$ids = array_map(function($item) {return $item->get_id();}, $relations);
			
			return $ids;
		}
		
		public function get_all_outgoing_relations_at_any_time($include_drafts = false) {
			$relations = $this->data_api_post()->get_outgoing_direction()->get_all_relations();
			$ids = array_map(function($item) {return $item->get_id();}, $relations);
			
			if($include_drafts) {
				$draft_ids = $this->data_api_post()->get_outgoing_direction()->get_draft_ids();
				$ids = array_merge($ids, $draft_ids);
			}
			
			return $this->group_object_relations($ids);
		}
		
		public function get_incoming_relations($type_path, $object_type, $time = -1) {
			$relations = wprr_get_data_api()->wordpress()->get_post($this->get_id())->get_incoming_direction()->get_type($type_path)->get_relations($object_type, $time);
			$ids = array_map(function($item) {return $item->get_id();}, $relations);
			
			return $ids;
		}
		
		public function get_single_incoming_relation($type_path, $object_type, $time = -1) {
			$ids = $this->get_incoming_relations($type_path, $object_type, $time);
			$count = count($ids);
			if($count > 0) {
				if($count > 1) {
					//METODO: error message
				}
				
				return $ids[0];
			}
			//METODO: error message
			
			return null;
		}
		
		public function get_all_incoming_relations() {
			$relations = $this->data_api_post()->get_incoming_direction()->get_all_relations();
			$ids = array_map(function($item) {return $item->get_id();}, $relations);
			
			return $ids;
		}
		
		public function get_all_incoming_relations_at_any_time($include_drafts = false) {
			
			$relations = $this->data_api_post()->get_incoming_direction()->get_all_relations();
			$ids = array_map(function($item) {return $item->get_id();}, $relations);
			
			if($include_drafts) {
				$draft_ids = $this->data_api_post()->get_incoming_direction()->get_draft_ids();
				$ids = array_merge($ids, $draft_ids);
			}
			
			return $this->group_object_relations($ids);
		}
		
		protected function group_object_relations($relation_ids) {
			$return_array = array();
			
			$object_relation_term = dbm_get_type_by_path('object-relation');
			
			$cut_length = strlen('object-relation/');
			
			foreach($relation_ids as $relation_id) {
				$current_terms = wp_get_post_terms($relation_id, 'dbm_type');
				
				foreach($current_terms as $current_term) {
					if(term_is_ancestor_of($object_relation_term, $current_term, 'dbm_type')) {
						$path = \DbmContent\OddCore\Utils\TaxonomyFunctions::get_full_term_slug($current_term, 'dbm_type');
						
						$path = substr($path, $cut_length);
					
						if(!isset($return_array[$path])) {
							$return_array[$path] = array();
						}
					
						$return_array[$path][] = $relation_id;
					}
				}
			}
			
			return $return_array;
		}
		
		public function object_relation_query($path) {
			
			$data_api = wprr_get_data_api();
			$posts = $data_api->wordpress()->get_post($this->get_id())->object_relation_query($path);
			
			$current_ids = array_map(function($post) {return $post->get_id();}, $posts);
			
			return $current_ids;
		}
		
		public function get_incoming_objects_in_order($relation_type, $data_type, $order) {
			$relation_ids = $this->get_incoming_relations($relation_type, $data_type);
			$relation_ids = $this->get_in_sorted_order($relation_ids, $order);
			return $this->resolve_incoming_relations_by_id($relation_ids);
		}
		
		public function get_outgoing_objects_in_order($relation_type, $data_type, $order) {
			$relation_ids = $this->get_outgoing_relations($relation_type, $data_type);
			$relation_ids = $this->get_in_sorted_order($relation_ids, $order);
			return $this->resolve_outgoing_relations_by_id($relation_ids);
		}
		
		public function get_single_object_relation_field_value($path, $field_name) {
			$ids = $this->object_relation_query($path);
			if(empty($ids)) {
				return null;
			}
			
			$post = dbmtc_get_group($ids[0]);
			return $post->get_field_value($field_name);
		}
		
		public function get_order($for_type) {
			
			$order_ids = $this->get_outgoing_relations('relation-order-by', 'relation-order');
			
			foreach($order_ids as $order_id) {
				
				$order_data_id = get_post_meta($order_id, 'toId', true);
				$current_for_type = get_post_meta($order_data_id, 'forType', true);
				
				if($current_for_type === $for_type) {
					return get_post_meta($order_data_id, 'order', true);
				}
			}
			
			return array();
		}
		
		public function get_in_sorted_order($ids, $order_type) {
			$order = $this->get_order($order_type);
			
			$sorted_ids = array_intersect($order, $ids);
			$rest_ids = array_diff($ids, $sorted_ids);
			
			$return_array = array_merge($sorted_ids, $rest_ids);
			
			return $return_array;
		}
		
		public function get_all_ids_from_hierarchy($hierarchy_items) {
			$return_array = array();
			foreach($hierarchy_items as $hierarchy_item) {
				$return_array[] = $hierarchy_item["id"];
				$return_array = array_merge($return_array, $this->get_all_ids_from_hierarchy($hierarchy_item["children"]));
			}
			
			return $return_array;
		}
		
		public function filter_out_missing_hierarchy_items(&$hierarchy_items, $exisiting_ids) {
			$length = count($hierarchy_items);
			for($i = 0; $i < $length; $i++) {
				$current_id = $hierarchy_items[i]["id"];
				if(!in_array($current_id, $exisiting_ids)) {
					unset($hierarchy_items[i]);
				}
				else {
					$this->filter_out_missing_hierarchy_items($hierarchy_items[i]["children"], $exisiting_ids);
				}
			}
		}
		
		public function get_in_sorted_in_hierarchy_order($ids, $order_type) {
			//var_dump("get_in_sorted_in_hierarchy_order");
			//var_dump($ids, $order_type);
			
			$hierarchy_items = $this->get_order($order_type);
			$this->filter_out_missing_hierarchy_items($hierarchy_items, $ids);
			
			$order_ids = $this->get_all_ids_from_hierarchy($hierarchy_items);
			
			$sorted_ids = array_intersect($order_ids, $ids);
			$rest_ids = array_diff($ids, $sorted_ids);
			
			if(!empty($rest_ids)) {
				foreach($rest_ids as $rest_id) {
					$hierarchy_items[] = array('id' => $rest_id, 'children' => array());
				}
			}
			
			return $hierarchy_items;
		}
		
		public function resolve_incoming_relations_by_id($relation_ids) {
			$return_array = array();
			foreach($relation_ids as $relation_id) {
				$return_array[] = (int)get_post_meta($relation_id, 'fromId', true);
			}
			
			return $return_array;
		}
		
		public function resolve_outgoing_relations_by_id($relation_ids) {
			$return_array = array();
			foreach($relation_ids as $relation_id) {
				$return_array[] = (int)get_post_meta($relation_id, 'toId', true);
			}
			
			return $return_array;
		}
		
		public function resolve_incoming_hierarch_relations($hierarchy_items) {
			$return_array = array();
			
			foreach($hierarchy_items as $hierarchy_item) {
				$encoded_item = array();
				$encoded_item['id'] = (int)get_post_meta($hierarchy_item['id'], 'fromId', true);
				$encoded_item['children'] = $this->resolve_incoming_hierarch_relations($hierarchy_item['children']);
				
				$return_array[] = $encoded_item;
			}
			
			return $return_array;
		}
		
		
		public function resolve_outgoing_hierarch_relations($hierarchy_items) {
			$return_array = array();
			
			foreach($hierarchy_items as $hierarchy_item) {
				$encoded_item = array();
				$encoded_item['id'] = (int)get_post_meta($hierarchy_item['id'], 'toId', true);
				$encoded_item['children'] = $this->resolve_outgoing_hierarch_relations($hierarchy_item['children']);
				
				$return_array[] = $encoded_item;
			}
			
			return $return_array;
		}
		
		public function clear_cache() {
			
			delete_post_meta($this->get_id(), 'dbm/userRelations');
			delete_post_meta($this->get_id(), 'dbm/objectRelations/incoming');
			delete_post_meta($this->get_id(), 'dbm/objectRelations/outgoing');
			
			return $this;
			
		}
		
		public function get_remove_items(&$remove_collection) {
			
			if($remove_collection->has_processed($this->get_id())) {
				return $remove_collection;
			}
			
			$remove_collection->add_item($this->get_id());
			
			$grouped_outgoing = $this->get_all_outgoing_relations_at_any_time(true);
			
			foreach($grouped_outgoing as $outgoing_ids) {
				foreach($outgoing_ids as $id) {
					$relation_post = dbm_get_object_relation($id);
					$relation_post->get_remove_items($remove_collection);
				
					$relatated_id = (int)get_post_meta($id, 'toId', true);
					$remove_collection->clear_cache($relatated_id);
				}
			}
			
			
			$grouped_incoming = $this->get_all_incoming_relations_at_any_time(true);
			
			foreach($grouped_incoming as $incoming_ids) {
				foreach($incoming_ids as $id) {
					$relation_post = dbm_get_object_relation($id);
					$relation_post->get_remove_items($remove_collection);
				
					$relatated_id = (int)get_post_meta($id, 'fromId', true);
					$remove_collection->clear_cache($relatated_id);
				}
			}
			
			$user_relation_ids = $this->get_user_relation_ids();
			foreach($user_relation_ids as $user_relation_id) {
				$user_relation_post = dbm_get_object_relation($user_relation_id);
				$user_relation_post->get_remove_items($remove_collection);
			}
			
			if(isset($grouped_incoming['field-for'])) {
				$field_relation_ids = $grouped_incoming['field-for'];
				foreach($field_relation_ids as $field_relation_id) {
					$field_id = (int)get_post_meta($field_relation_id, 'fromId', true);
					$field_post = dbm_get_post($field_id);
					$field_post->get_remove_items($remove_collection);
				}
			}
			
			if(isset($grouped_incoming['message-in'])) {
				$message_relation_ids = $grouped_incoming['message-in'];
				foreach($message_relation_ids as $message_relation_id) {
					$message_id = (int)get_post_meta($message_relation_id, 'fromId', true);
					$message_post = dbm_get_post($message_id);
					$message_post->get_remove_items($remove_collection);
				}
			}
			
			if(isset($grouped_outgoing['relation-order-by'])) {
				$order_relation_ids = $grouped_outgoing['relation-order-by'];
				foreach($order_relation_ids as $order_relation_id) {
					$order_id = (int)get_post_meta($order_relation_id, 'toId', true);
					$order_post = dbm_get_post($order_id);
					$order_post->get_remove_items($remove_collection);
				}
			}
			
			return $remove_collection;
		}
		
		public static function test_import() {
			echo("Imported \DbmContent\DbmPost<br />");
		}
	}
?>
