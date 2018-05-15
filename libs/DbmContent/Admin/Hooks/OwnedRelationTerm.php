<?php
	namespace DbmContent\Admin\Hooks;
	
	// \DbmContent\Admin\Hooks\OwnedRelationTerm
	class OwnedRelationTerm {
		
		protected $_type = null;
		protected $_type_group = null;
		protected $_relation_group = null;
		protected $_add_term_to_owner_post = false;
		
		function __construct() {
			//echo("\OddCore\Admin\Hooks\OwnedRelationTerm::__construct<br />");
			
			
		}
		
		public function setup($type, $relation_group) {
			$this->_type = $type;
			$this->_type_group = $type;
			$this->_relation_group = $relation_group;
		}
		
		public function set_add_term_to_owner_post($add = true) {
			$this->_add_term_to_owner_post = $add;
		}
		
		public function set_type_group($type_group) {
			$this->_type_group = $type_group;
		}
		
		public function register_hooks() {
			add_action('dbm_content/type_set/'.$this->_type, array($this, 'hook_type_set'), 10, 2);
			add_action('dbm_content/type_removed/'.$this->_type, array($this, 'hook_type_removed'), 10, 2);
		}
		
		protected function get_parent_term($current_post, $meta_name) {
			
			while($current_post->post_parent) {
				$current_post = get_post($current_post->post_parent);
				if(!$current_post) {
					break;
				}
				
				$term_id_post_meta = get_post_meta($current_post->ID, $meta_name, true);
				if(is_numeric($term_id_post_meta)) {
					$term_id = intVal($term_id_post_meta);
					$current_term = get_term_by('id', $term_id, 'dbm_relation');
					if($current_term) {
						return $term_id;
					}
				}
			}
			
			$parent_term = get_term_by('slug', $this->_relation_group, 'dbm_relation');
			return $parent_term->term_id;
		}
		
		public function hook_type_set($post_id, $post) {
			
			$meta_name = 'dbm_relation_term_'.$this->_type_group;
			
			$term_id_meta = get_post_meta($post_id, $meta_name, true);
			if(is_numeric($term_id_meta)) {
				$term_id = intVal($term_id_meta);
				$current_term = get_term_by('id', $term_id, 'dbm_relation');
				if(!$current_term) {
					$term_id = false;
				}
			}
			else {
				$term_id = false;
			}
			
			$parent_id = $this->get_parent_term($post, $meta_name);
			
			if(!$term_id) {
				$new_term = wp_insert_term($post->post_title, 'dbm_relation', array('parent' => $parent_id));
				if(is_wp_error($new_term)) {
					//METODO: handle this better
					return;
				}
				else {
					$term_id = $new_term['term_id'];
					update_post_meta($post_id, $meta_name, $term_id);
					if(function_exists('update_field')) {
						$term = get_term_by('id', $term_id, 'dbm_relation');
						update_field('dbm_taxonomy_page', $post_id, $term);
					}
				}
			}
			else {
				wp_update_term($term_id, 'dbm_relation', array('name' => $post->post_title, 'parent' => $parent_id));
			}
			
			if($this->_add_term_to_owner_post) {
				wp_set_post_terms($post_id, $term_id, 'dbm_relation', true);
			}
		}
		
		public function hook_type_removed($post_id, $post) {
			
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Admin\Hooks\OwnedRelationTerm<br />");
		}
	}
?>