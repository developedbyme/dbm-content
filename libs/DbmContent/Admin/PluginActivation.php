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
			
			if(taxonomy_exists('dbm_relation')) {
				
				self::add_term('dbm_type:trash-log', 'Trash log');
				
				self::add_term('dbm_type:object-relation', 'Object relation');
				self::add_term('dbm_type:object-relation/for', 'For');
				self::add_term('dbm_type:object-relation/in', 'In');
				self::add_term('dbm_type:object-relation/from', 'From');
				self::add_term('dbm_type:object-relation/has', 'Has');
				self::add_term('dbm_type:object-relation/part-of', 'Part of');
				self::add_term('dbm_type:object-relation/version-of', 'Version of');
				self::add_term('dbm_type:object-relation/latest-version-of', 'Latest version of');
				self::add_term('dbm_type:object-relation/translation-of', 'Translation of');
				self::add_term('dbm_type:object-relation/by', 'By');
				self::add_term('dbm_type:object-relation/during', 'During');
				self::add_term('dbm_type:object-relation/of', 'Of');
				self::add_term('dbm_type:object-relation/at', 'At');
				self::add_term('dbm_type:object-relation/to', 'To');
				self::add_term('dbm_type:object-relation/following', 'Following');
				self::add_term('dbm_type:object-relation/completed', 'Completed');
				self::add_term('dbm_type:object-relation/skipped', 'Skipped');
				self::add_term('dbm_type:object-relation/started', 'Started');
				self::add_term('dbm_type:object-relation/owned-by', 'Owned by');
				self::add_term('dbm_type:object-relation/relation-order-by', 'Relation order by');
				self::add_term('dbm_type:object-relation/number-sequence-for', 'Number sequence for');
				self::add_term('dbm_type:object-relation/pointing-to', 'Pointing to');
				self::add_term('dbm_type:object-relation/available-at', 'Available at');
				
				self::add_term('dbm_type:object-user-relation', 'Object user relation');
				self::add_term('dbm_type:object-user-relation/user-for', 'User for');
				self::add_term('dbm_type:object-user-relation/by', 'By');
				
				$current_term_id = self::add_term('dbm_relation:global-pages', 'Global pages');
			
				$current_term_id = self::add_term('dbm_relation:page-templates', 'Page templates');
			
				$current_term_id = self::add_term('dbm_relation:languages', 'Languages');
			
				$languages = apply_filters( 'wpml_active_languages', NULL, 'skip_missing=0&orderby=id&order=desc' );
				if($languages) {
					foreach($languages as $language) {
						$current_term_id = self::add_term('dbm_relation:languages/'.$language['code'], $language['translated_name']);
					}
				}
				
				$current_term_id = self::add_term('dbm_relation:content-section-type', 'Content section type');
				$current_term_id = self::add_term('dbm_relation:content-section-type/text', 'Text');
				
				$current_term_id = self::add_term('dbm_relation:menu-position', 'Menu position');
				$current_term_id = self::add_term('dbm_relation:menu-position/side-menu', 'Side menu');
				$current_term_id = self::add_term('dbm_relation:menu-position/side-menu/default', 'Default side menu');
				$current_term_id = self::add_term('dbm_relation:menu-position/top-menu', 'Top menu');
				$current_term_id = self::add_term('dbm_relation:menu-position/top-menu/default', 'Default top menu');
				$current_term_id = self::add_term('dbm_relation:menu-position/footer-menu', 'Footer menu');
				$current_term_id = self::add_term('dbm_relation:menu-position/footer-menu/default', 'Default footer menu');
				
				
				if(function_exists('dbmtc_setup_field_template')) {
					$setup_manager = dbm_setup_get_manager();
				
					$current_type = $setup_manager->create_data_type('relation-order')->set_name('Relation order');
					$current_type->add_field("order")->set_type('json')->setup_meta_storage();
					$current_type->add_field("forType")->setup_meta_storage();
				
					$current_type = $setup_manager->create_data_type('number-sequence')->set_name('Number sequence');
					$current_type->add_field("prefix")->setup_meta_storage();
					$current_type->add_field("suffix")->setup_meta_storage();
					$current_type->add_field("padding")->set_type('number')->setup_meta_storage();
				
					$current_type = $setup_manager->create_data_type('sequence-number')->set_name('Sequence number');
					$current_type->add_field("number")->setup_meta_storage();
					$current_type->add_field("fullIdentifier")->setup_meta_storage();
				
					$current_type = $setup_manager->create_data_type('process')->set_name('Process');
					$current_type->add_field("name")->setup_meta_storage();
				
					$current_type = $setup_manager->create_data_type('process-part')->set_name('Process part');
					$current_type->add_field("name")->setup_meta_storage();
					$current_type->add_field("description")->setup_meta_storage();
					$current_type->add_field("type")->setup_meta_storage();
					$current_type->add_field("identifier")->setup_meta_storage();
				
					$current_type = $setup_manager->create_data_type('global-item')->set_name('Global item');
					$current_type->add_field("identifier")->setup_meta_storage();
				
					$current_type = $setup_manager->create_data_type('identifiable-item')->set_name('Identifiable item');
					$current_type->add_field("identifier")->setup_meta_storage();
					
					$current_type = $setup_manager->create_data_type('named-item')->set_name('Named item');
					$current_type->add_field("name")->setup_meta_storage();
				
					$current_type = $setup_manager->create_data_type('content-section')->set_name('Content section');
					$current_type->add_field("name")->setup_meta_storage();
					$current_type->add_field("title")->setup_meta_storage();
					$current_type->add_field("content")->setup_meta_storage();
					$current_type->add_field("type")->setup_single_relation_storage('content-section-type');
				
					$current_type = $setup_manager->create_data_type('content-template')->set_name('Content template');
					$current_type->add_field("name")->setup_meta_storage();
					$current_type->add_field("title")->setup_meta_storage();
					$current_type->add_field("content")->setup_meta_storage();
					$current_type->add_field("type")->setup_single_relation_storage('content-section-type');
				
					$current_type = $setup_manager->create_data_type('template-position')->set_name('Template position');
					$current_type->add_field("name")->setup_meta_storage();
					$current_type->add_field("identifier")->setup_meta_storage();
					$current_type->add_field("description")->setup_meta_storage();
					
					$current_type = $setup_manager->create_data_type('instance')->set_name('Instance');
					
					$current_type = $setup_manager->create_data_type('type')->set_name('Type');
					$current_type->add_field("name")->setup_meta_storage();
					$current_type->add_field("identifier")->setup_meta_storage();
					
					$current_type = $setup_manager->create_data_type('product')->set_name('Product');
					
					$setup_manager->save_all();
				}
			}
		}
		
		public static function test_import() {
			echo("Imported \Admin\CustomPostTypes\PluginActivation<br />");
		}
	}
?>
