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
		
		protected function get_free_slug($wanted_slug, $taken_slugs) {
			
			$matching_slugs = array();
			
			foreach($taken_slugs as $taken_slug) {
				if(strpos($taken_slug, $wanted_slug) === 0) {
					$matching_slugs[] = $taken_slug;
				}
			}
			
			if(!empty($matching_slugs)) {
				
				$slug_length = strlen($wanted_slug);
				$next_value = 2;
				foreach($matching_slugs as $taken_slug) {
					$current_count = (int)substr($taken_slug, $slug_length+1);
					if($current_count) {
						$next_value = max($next_value, $current_count+1);
					}
				}
				
				$wanted_slug .= '-'.$next_value;
			}
			
			return $wanted_slug;
		}
		
		public function hook_type_set($post_id, $post) {
			//echo('hook_type_set');
			
			global $sitepress, $wpml_post_translations;
			
			$meta_name = 'dbm_relation_term_'.$this->_type_group;
			
			$original_id = $post_id;
			if($sitepress && $wpml_post_translations) {
				if($sitepress->is_translated_post_type(get_post_type($post_id))) {
					$original_id = $wpml_post_translations->get_original_element($post_id);
					if($original_id === null) {
						//METODO: this needs to be checked, sometimes it's triggered before elements are ready and sometimes after, return should be there if before
						$original_id = $post_id;
						//return;
					}
				}
			}
			$is_original = ($post_id === $original_id);
			
			$term_id_meta = get_post_meta($original_id, $meta_name, true);
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
			
			$parent_id = $this->get_parent_term(get_post($original_id), $meta_name);
			
			if(!$term_id) {
				
				$temporary_slug = 'dbm-auto-'.uniqid().'-'.sanitize_title($post->post_title);
				$siblings = get_terms(array(
					'taxonomy' => 'dbm_relation',
					'hide_empty' => false,
					'parent' => $parent_id
				));
				
				if($siblings) {
					$wanted_slug = $this->get_free_slug($temporary_slug, wp_list_pluck($siblings, 'slug'));
				}
				
				$new_term = wp_insert_term($post->post_title, 'dbm_relation', array('slug' => $temporary_slug, 'parent' => $parent_id));
				if(is_wp_error($new_term)) {
					//MENOTE: this should never happend as we are generating unique slugs
					//METODO: throw error
					return;
				}
				else {
					$term_id = $new_term['term_id'];
					update_post_meta($post_id, $meta_name, $term_id);
					
					$current_try = 0;
					
					$wanted_slug = sanitize_title($post->post_title);
					
					if($siblings) {
						$wanted_slug = $this->get_free_slug($wanted_slug, wp_list_pluck($siblings, 'slug'));
					}
					
					$possible_error = wp_update_term($term_id, 'dbm_relation', array('slug' => $wanted_slug));
					
					if(function_exists('update_field')) {
						$term = get_term_by('id', $term_id, 'dbm_relation');
						update_field('dbm_taxonomy_page', $post_id, $term);
					}
				}
			}
			else {
				if($is_original) {
					wp_update_term($term_id, 'dbm_relation', array('name' => $post->post_title, 'parent' => $parent_id));
				}
				else {
					update_post_meta($post_id, $meta_name, $term_id);
				}
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