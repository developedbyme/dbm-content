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
		
		public function get_title() {
			return get_post_field('post_title', $this->get_id());
		}
		
		public function get_content() {
			return apply_filters('the_content', get_post_field('post_content', $this->get_id()));
		}
		
		public function change_status($status) {
			wp_update_post(array(
				'ID' => $this->get_id(),
				'post_status' => $status
			));
			
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
		
		public function add_outgoing_relation_by_name($to_object_id, $type_path) {
			$new_relation_id = dbm_create_object_relation($this->get_id(), $to_object_id, $type_path);
			
			return $new_relation_id;
		}
		
		public function add_incoming_relation_by_name($from_object_id, $type_path) {
			$new_relation_id = dbm_create_object_relation($from_object_id, $this->get_id(), $type_path);
			
			return $new_relation_id;
		}
		
		public function add_user_relation($user_id, $type_path) {
			$new_relation_id = dbm_create_object_user_relation($this->get_id(), $user_id, $type_path);
			
			return $new_relation_id;
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
		
		public function get_user_relations_query($type_path, $time = -1) {
			$dbm_query = $this->get_object_relation_query_without_settings()->add_type_by_path('object-user-relation')->add_type_by_path('object-user-relation/'.$type_path);
			$this->add_time_query($dbm_query, $time);
			$dbm_query->add_meta_query('fromId', $this->get_id());
			
			return $dbm_query;
		}
		
		public function get_users_by_relation($type_path, $time = -1) {
			$query = $this->get_user_relations_query($type_path, $time);
			
			$user_ids = array();
			$relation_ids = $query->get_post_ids();
			foreach($relation_ids as $relation_id) {
				$user_ids[] = get_post_meta($relation_id, 'toId', true);
			}
			
			return $user_ids;
		}
		
		public function get_object_relation_query($type_path, $time = -1) {
			
			$dbm_query = $this->get_object_relation_query_without_settings()->add_type_by_path('object-relation')->add_type_by_path('object-relation/'.$type_path);
			$this->add_time_query($dbm_query, $time);
			
			return $dbm_query;
		}
		
		public function filter_by_object_type($relation_ids, $object_field, $object_type) {
			$return_array = array();
			
			$term = dbm_get_type_by_path($object_type);
			if($term) {
				foreach($relation_ids as $relation_id) {
				
					$post_id = get_post_meta($relation_id, $object_field, true);
					if(has_term($term->term_id, 'dbm_type', $post_id)) {
						$return_array[] = $relation_id;
					}
				}
			}
			
			return $return_array;
		}
		
		public function get_outgoing_relations($type_path, $object_type, $time = -1) {
			$ids = $this->get_object_relation_query($type_path)->add_meta_query('fromId', $this->get_id())->get_post_ids();
			
			$ids = $this->filter_by_object_type($ids, 'toId', $object_type);
			
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
		
		public function get_all_outgoing_relations($time = -1) {
			$dbm_query = $this->get_object_relation_query_without_settings()->add_meta_query('fromId', $this->get_id())->add_type_by_path('object-relation');
			$this->add_time_query($dbm_query, $time);
			
			$relation_ids = $dbm_query->get_post_ids();
			
			return $this->group_object_relations($relation_ids);
		}
		
		public function get_incoming_relations($type_path, $object_type, $time = -1) {
			$ids = $this->get_object_relation_query($type_path)->add_meta_query('toId', $this->get_id())->get_post_ids();
			
			$ids = $this->filter_by_object_type($ids, 'fromId', $object_type);
			
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
			
			$dbm_query = $this->get_object_relation_query_without_settings()->add_meta_query('toId', $this->get_id())->add_type_by_path('object-relation');
			$this->add_time_query($dbm_query, $time);
			
			$relation_ids = $dbm_query->get_post_ids();
			
			return $this->group_object_relations($relation_ids);
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
			
			$current_ids = array($this->get_id());
			$current_ids = self::object_relation_query_from_ids($current_ids, $path);
			
			return $current_ids;
		}
		
		static public function object_relation_query_from_ids($ids, $path) {
			
			$path_parts = explode(',', $path);
			
			$current_ids = $ids;
			
			foreach($path_parts as $path_part) {
				$part_parts = explode(':', $path_part);
				
				$direction = $part_parts[0];
				$type = $part_parts[1];
				$object_type = $part_parts[2];
				$time = (isset($part_parts[3])) ? (int)$part_parts[3] : -1;
				
				$check_function = $direction === 'in' ? 'get_incoming_relations' : 'get_outgoing_relations';
				$meta_name = $direction === 'in' ? 'fromId' : 'toId';
				
				$new_ids = array();
				foreach($current_ids as $current_id) {
					$dbm_post = dbm_get_post($current_id);
					$new_relation_ids = $dbm_post->$check_function($type, $object_type, $time);
					foreach($new_relation_ids as $new_relation_id) {
						$new_ids[] = (int)get_post_meta($new_relation_id, $meta_name, true);
					}
				}
				
				$current_ids = array_unique($new_ids);
			}
			
			return $current_ids;
		}
		
		public static function test_import() {
			echo("Imported \DbmContent\DbmPost<br />");
		}
	}
?>
