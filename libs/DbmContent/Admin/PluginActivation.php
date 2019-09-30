<?php
	namespace DbmContent\Admin;

	// \DbmContent\Admin\PluginActivation
	class PluginActivation {
		
		static function create_page_at_path($path, $title, $post_type = 'page') {
			
			$page = get_page_by_path($path, OBJECT, $post_type);
			if($page) {
				/*
				$args = array(
					'ID' => $page->ID,
					'post_title' => $title,
				);
				
				wp_update_post($args);
				*/
				return $page->ID;
			}
			
			$term_array = explode('/', $path);
			
			$slug = array_pop($term_array);
			$parent_id = 0;
			if(count($term_array) > 0 ) {
				$parent_path = implode('/', $term_array);
				
				$parent = get_page_by_path($parent_path, OBJECT, $post_type);
				if($parent) {
					$parent_id = $parent->ID;
				}
				else {
					trigger_error("No parent at ".$parent_path, E_USER_ERROR);
				}
			}
			
			$args = array(
				'post_type' => $post_type,
				'post_parent' => $parent_id,
				'post_name' => $slug,
				'post_title' => $title,
				'post_status' => 'publish'
			);
			
			$post_id = wp_insert_post($args);
			
			return $post_id;
		}
		
		static function add_term($path, $name) {
			$temp_array = explode(':', $path);
			
			$taxonomy = $temp_array[0];
			$path = explode('/', $temp_array[1]);
			
			\DbmContent\OddCore\Utils\TaxonomyFunctions::add_term($name, $path, $taxonomy);
		}
		
		static function get_term_by_path($path) {
			$temp_array = explode(':', $path);
			
			$taxonomy = $temp_array[0];
			$path = explode('/', $temp_array[1]);
			
			return \DbmContent\OddCore\Utils\TaxonomyFunctions::get_term_by_slugs($path, $taxonomy);
		}
		
		static function add_terms_to_post($term_paths, $post_id) {
			foreach($term_paths as $term_path) {
				$current_term = self::get_term_by_path($term_path);
				if($current_term) {
					wp_set_post_terms($post_id, $current_term->term_id, $current_term->taxonomy, true);
				}
				else {
					//METODO: error message
				}
			}
			
			return $post_id;
		}
		
		public static function create_global_term_and_page($slug, $title, $post_type = 'page', $parent_id = 0) {
			$relation_path = 'dbm_relation:global-pages/'.$slug;
			self::add_term($relation_path, $title);
			$current_page_id = self::create_page($slug, $title, $post_type, $parent_id);
			update_post_meta($current_page_id, '_wp_page_template', 'template-global-'.$slug.'.php');
			self::add_terms_to_post(array($relation_path), $current_page_id);
			
			return $current_page_id;
		}
		
		public static function create_user($login, $first_name = '', $last_name = '') {
			$existing_user = get_user_by('login', $login);
			
			if($existing_user) {
				return $existing_user->ID;
			}
			
			$args = array(
				'user_login' => $login,
				'user_pass' => wp_generate_password(),
				'first_name' => $first_name,
				'last_name' => $last_name,
				'display_name' => $first_name
			);
			
			$new_user_id = wp_insert_user($args);
			
			return $new_user_id;
		}
		
		public static function run_setup() {
			
			remove_all_actions('pre_get_posts');
			
			/*
			$current_term_id = self::add_term('dbm_relation:global-pages', 'Global pages');
			
			$current_term_id = self::add_term('dbm_relation:page-templates', 'Page templates');
			
			$current_term_id = self::add_term('dbm_relation:languages', 'Languages');
			
			$languages = apply_filters( 'wpml_active_languages', NULL, 'skip_missing=0&orderby=id&order=desc' );
			if($languages) {
				foreach($languages as $language) {
					$current_term_id = self::add_term('dbm_relation:languages/'.$language['code'], $language['translated_name']);
				}
			}
			
			$current_term_id = self::add_term('dbm_relation:menu-position', 'Menu position');
			$current_term_id = self::add_term('dbm_relation:menu-position/side-menu', 'Side menu');
			$current_term_id = self::add_term('dbm_relation:menu-position/side-menu/default', 'Default side menu');
			$current_term_id = self::add_term('dbm_relation:menu-position/top-menu', 'Top menu');
			$current_term_id = self::add_term('dbm_relation:menu-position/top-menu/default', 'Default top menu');
			$current_term_id = self::add_term('dbm_relation:menu-position/footer-menu', 'Footer menu');
			$current_term_id = self::add_term('dbm_relation:menu-position/footer-menu/default', 'Default footer menu');
			*/
			
		}
		
		public static function test_import() {
			echo("Imported \Admin\CustomPostTypes\PluginActivation<br />");
		}
	}
?>
