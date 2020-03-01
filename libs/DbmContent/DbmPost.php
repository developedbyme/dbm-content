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
			
			$term_ids = dbm_get_ids_from_terms(dbm_get_relation_by_path($path));
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
		
		public static function test_import() {
			echo("Imported \DbmContent\DbmPost<br />");
		}
	}
?>
