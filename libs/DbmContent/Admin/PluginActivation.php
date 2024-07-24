<?php
	namespace DbmContent\Admin;

	// \DbmContent\Admin\PluginActivation
	class PluginActivation {
		
		static function add_term($path, $name) {
			$temp_array = explode(':', $path);
			
			$taxonomy = $temp_array[0];
			$path = explode('/', $temp_array[1]);
			
			\DbmContent\OddCore\Utils\TaxonomyFunctions::add_term($name, $path, $taxonomy);
		}
		
		public static function run_setup() {
			
			remove_all_actions('pre_get_posts');
			
			$editor = wprr_get_data_api()->wordpress()->editor();
			
			$editor->create_object_relation_types(
				'for',
				'in',
				'from',
				'has',
				'part-of',
				'version-of',
				'latest-version-of',
				'translation-of',
				'by',
				'during',
				'of',
				'at',
				'to',
				'following',
				'completed',
				'skipped',
				'started',
				'owned-by',
				'relation-order-by',
				'number-sequence-for',
				'pointing-to',
				'available-at',
				'based-on'
			);
			
			$editor->create_object_user_relation_types(
				'user-for',
				'by'
			);
			
			if(taxonomy_exists('dbm_relation')) {
				
				self::add_term('dbm_type:trash-log', 'Trash log');
				
				self::add_term('dbm_type:object-relation', 'Object relation');
				self::add_term('dbm_type:object-user-relation', 'Object user relation');
				
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
					
					$current_type = $setup_manager->create_data_type('description-item')->set_name('Description item');
					$current_type->add_field("description")->setup_meta_storage();
					
					$current_type = $setup_manager->create_data_type('value-item')->set_name('Value item');
					$current_type->add_field("value")->set_type('json')->setup_meta_storage();
					
					$current_type = $setup_manager->create_data_type('file-value-item')->set_name('File value item');
					$current_type->add_field("value")->set_type('file')->setup_meta_storage();
					
					$current_type = $setup_manager->create_data_type('object-property')->set_name('Object property');
					$current_type = $setup_manager->create_data_type('object-property/linked-object-property')->set_name('Linked object property');
					
					$current_type = $setup_manager->create_data_type('image')->set_name('Image');
					$current_type->add_field("value")->set_type('image')->setup_meta_storage();
					$current_type->add_field("title")->setup_meta_storage();
					$current_type->add_field("description")->setup_meta_storage();
				
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
					
					$current_type = $setup_manager->create_data_type('group')->set_name('Group');
					$current_type = $setup_manager->create_data_type('enum')->set_name('Enum');
					
					$current_type = $setup_manager->create_data_type('representation')->set_name('Representation');
					$current_type->add_field("url")->setup_meta_storage();
					
					$current_type = $setup_manager->create_data_type('type')->set_name('Type');
					$current_type->add_field("name")->setup_meta_storage();
					$current_type->add_field("identifier")->setup_meta_storage();
					
					$current_type = $setup_manager->create_data_type('type/header-type')->set_name('Header type');
					$current_type = $setup_manager->create_data_type('type/footer-type')->set_name('Footer type');
					$current_type = $setup_manager->create_data_type('type/hero-type')->set_name('Hero type');
					
					$current_type = $setup_manager->create_data_type('type/representation-type')->set_name('Representation type');
					
					$current_type = $setup_manager->create_data_type('type/group-type')->set_name('Group type');
					
					$current_type = $setup_manager->create_data_type('type/enum-type')->set_name('Enum type');
					
					$current_type = $setup_manager->create_data_type('type/timezone')->set_name('Timezone');
					$current_type = $setup_manager->create_data_type('type/language')->set_name('Language');
					$current_type = $setup_manager->create_data_type('type/currency')->set_name('Currency');
					$current_type = $setup_manager->create_data_type('type/data-format')->set_name('Data format');
					
					$current_type = $setup_manager->create_data_type('group/translations-group')->set_name('Translations group');
					
					$current_type = $setup_manager->create_data_type('product')->set_name('Product');
					
					$current_type = $setup_manager->create_data_type('post-type')->set_name('Post type');
					$post_types = get_post_types();
					foreach($post_types as $post_type) {
						$current_type = $setup_manager->create_data_type('post-type/'.$post_type)->set_name($post_type);
					}
					
					$current_type = $setup_manager->create_data_type('settings')->set_name('Settings');
					$current_type->add_field("data")->set_type('json')->setup_meta_storage();
					
					$current_type = $setup_manager->create_data_type('settings/page-settings')->set_name('Page settings');
					
					$current_type = $setup_manager->create_data_type('settings/data-source')->set_name('Data source');
					$current_type->add_field("dataName")->setup_meta_storage();
					
					$current_type = $setup_manager->create_data_type('settings/data-source/loaded-data-source')->set_name('Loaded data source');
					$current_type = $setup_manager->create_data_type('settings/data-source/static-data-source')->set_name('Static data source');
					
					$setup_manager->save_all();
				}
			}
		}
		
		public static function test_import() {
			echo("Imported \Admin\CustomPostTypes\PluginActivation<br />");
		}
	}
?>
