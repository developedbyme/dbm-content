<?php
	namespace DbmContent;

	use \DbmContent\OddCore\PluginBase;

	class Plugin extends PluginBase {

		protected $owned_relation_terms = array();

		function __construct() {
			//echo("\DbmContent\Plugin::__construct<br />");

			parent::__construct();
			
			//$this->add_javascript('dbm_content-main', DBM_CONTENT_URL.'/assets/js/main.js');
		}
		
		public function add_owned_relation_term($owned_relation_term) {
			$this->owned_relation_terms[] = $owned_relation_term;
			
			return $this;
		}

		protected function create_pages() {
			//echo("\DbmContent\Plugin::create_pages<br />");
			
			$wprr_configuration_data = array(
				'sitePath' => get_site_url(),
				'themePath' => get_stylesheet_directory_uri(),
				'restPath' => esc_url_raw( rest_url() ),
				'initialMRouterData' => array(),
				'imageSizes' => array(),
				'nonce' => wp_create_nonce( 'wp_rest' )
			);
			
			$current_page = new \DbmContent\OddCore\Admin\Pages\ReactPage();
			$current_page->set_names('DBM Relations Manager', 'Relations Manager','dbm_relations_manager');
			$current_page->set_component('relationsManagerPage', array());
			$current_page->add_javascript('lba-mag-admin', get_template_directory_uri().'/assets/js/admin.js');
			$current_page->add_javascript_data('lba-mag-admin', 'wprrWpConfiguration', $wprr_configuration_data);
			$current_page->add_css('lba-mag-admin', get_template_directory_uri().'/assets/css/admin-style.css');
			
			$this->add_page($current_page);
		}
		
		public function filter_get_taxonomies($taxonomies) {
			$taxonomies[] = 'dbm_type';
			$taxonomies[] = 'dbm_relation';
			
			return $taxonomies;
		}

		protected function create_custom_post_types() {
			//echo("\DbmContent\Plugin::create_custom_post_types<br />");
			
			$current_custom_post_type = new \DbmContent\Admin\CustomPostTypes\AreaCustomPostType();
			$type_taxonomy = $current_custom_post_type->get_owned_taxonomy('dbm_type');
			$relation_taxonomy = $current_custom_post_type->get_owned_taxonomy('dbm_relation');
			$this->add_custom_post_type($current_custom_post_type);
			
			$current_custom_post_type = new \DbmContent\Admin\CustomPostTypes\AdditionalCustomPostType();
			$current_custom_post_type->add_taxonomy('dbm_type');
			$current_custom_post_type->add_taxonomy('dbm_relation');
			$current_custom_post_type->add_taxonomy('category');
			$current_custom_post_type->add_taxonomy('post_tag');
			$this->add_custom_post_type($current_custom_post_type);
			
			$current_custom_post_type = new \DbmContent\Admin\CustomPostTypes\DataCustomPostType();
			$current_custom_post_type->add_taxonomy('dbm_type');
			$current_custom_post_type->add_taxonomy('dbm_relation');
			$this->add_custom_post_type($current_custom_post_type);
			
			$post_types_with_taxonomies = apply_filters('dbm_content/post_types_with_taxonomies', array('post', 'page', 'attachment'));
			
			foreach($post_types_with_taxonomies as $post_type) {
				$current_custom_post_type = new \DbmContent\OddCore\Admin\CustomPostTypes\AlreadyRegisteredCustomPostTypePost();
				$current_custom_post_type->set_names($post_type);
				$current_custom_post_type->add_taxonomy('dbm_type');
				$current_custom_post_type->add_taxonomy('dbm_relation');
				$this->add_custom_post_type($current_custom_post_type);
			}
		}

		protected function create_additional_hooks() {
			//echo("\DbmContent\Plugin::create_additional_hooks<br />");

			$this->add_additional_hook(new \DbmContent\ChangePostHooks());
		}

		protected function create_rest_api_end_points() {
			//echo("\DbmContent\Plugin::create_rest_api_end_points<br />");

			$api_namespace = 'dbm-content';
			
			
		}
		
		public function register_hooks() {
			parent::register_hooks();
			
			add_action('dbm_content/parse_dbm_content', array($this, 'hook_parse_dbm_content'), 10, 3);
			
			add_filter( 'mce_external_plugins', array($this, 'mce_external_plugins'), 10, 1 );
		}
		
		public function mce_external_plugins( $plugin_array ){
			$plugin_array['oa_generic'] = DBM_CONTENT_URL . '/libs/tinymce-plugins/oa_generic/editor_plugin_src.js';

			return $plugin_array;
		}
		
		public function get_full_term_slug($term, $taxonomy) {
			$return_string = $term->slug;
			if($term->parent !== 0) {
				$parent_term = get_term_by('id', $term->parent, $taxonomy);
				$return_string = $this->get_full_term_slug($parent_term, $taxonomy).'/'.$return_string;
			}
			return $return_string;
		}
		
		public function hook_parse_dbm_content($dbm_content, $post_id, $post) {
			//echo("\DbmContent\Plugin::hook_parse_dbm_content<br />");
			
			if(isset($dbm_content['dbm']) && isset($dbm_content['dbm']['type'])) {
				$type_ids = $dbm_content['dbm']['type'];
				
				$registered_type_ids = get_post_meta($post_id, 'dbm_registered_types', true);
				
				if($registered_type_ids && !empty($registered_type_ids)) {
					$removed_types = array_diff($registered_type_ids, $type_ids);
				}
				else {
					$removed_types = array();
				}
					
				
				
				wp_set_post_terms($post_id, $type_ids, 'dbm_type', false);
				update_post_meta($post_id, 'dbm_registered_types', $type_ids);
				
				foreach($removed_types as $term_id) {
					$term = get_term_by('id', $term_id, 'dbm_type');
					if($term) {
						$full_term_slug = $this->get_full_term_slug($term, 'dbm_type');
						
						$action_name = 'dbm_content/type_removed/'.$full_term_slug;
						do_action($action_name, $post_id, $post);
					}
				}
				
				foreach($type_ids as $term_id) {
					$term = get_term_by('id', $term_id, 'dbm_type');
					if($term) {
						$full_term_slug = $this->get_full_term_slug($term, 'dbm_type');
						
						$action_name = 'dbm_content/type_set/'.$full_term_slug;
						do_action($action_name, $post_id, $post);
					}
				}
			}
			
			if(isset($dbm_content['dbm']) && isset($dbm_content['dbm']['relation'])) {
				foreach($dbm_content['dbm']['relation'] as $parent_slug => $ids) {
					
					$parent_term = get_term_by('slug', $parent_slug, 'dbm_relation');
					
					$current_terms = wp_get_post_terms($post_id, 'dbm_relation');
					foreach($current_terms as $current_term) {
						if(!term_is_ancestor_of($parent_term, $current_term, 'dbm_relation')) {
							$ids[] = $current_term->term_id;
						}
					}
				
					wp_set_post_terms($post_id, $ids, 'dbm_relation', false);
				}
			}
			
			if(isset($dbm_content['dbm']) && isset($dbm_content['dbm']['primaryCategory'])) {
				update_post_meta($post_id, 'dbm_primary_taxonomy_term_category', $dbm_content['dbm']['primaryCategory']);
			}
			
			if(isset($dbm_content['dbm']) && isset($dbm_content['dbm']['relatedCategory'])) {
				update_post_meta($post_id, 'dbm_related_taxonomy_term_category', $dbm_content['dbm']['relatedCategory']);
			}
		}
		
		protected function create_filters() {
			//echo("\DbmContent\Plugin::create_filters<br />");
			
			$custom_range_filters = new \DbmContent\CustomRangeFilters();
			
			add_filter('m_router_data/custom_range_query_dbm-relations', array($custom_range_filters, 'query_relations_legacy'), 10, 2);
			add_filter('wprr/range_query/relation', array($custom_range_filters, 'query_relations'), 10, 2);
			add_filter('wprr/range_query/byOwnedRelation', array($custom_range_filters, 'query_by_owned_relation'), 10, 2);
			add_filter('wprr/range_query/relationOwner', array($custom_range_filters, 'query_by_relation_owner'), 10, 2);
			
			add_filter('wprr/range_query/languageTerm', array($custom_range_filters, 'query_languageTerm'), 10, 2);
			
			add_filter('m_router_data/custom_range_query_dbm-relation-manager-items', array($custom_range_filters, 'query_relation_manager_items'), 10, 2);
			add_filter('m_router_data/custom_range_encode_dbm-relation-manager-items', array($custom_range_filters, 'encode_relation_manager_items'), 10, 3);
			
			add_filter('m_router_data/custom_item_get_global-relation', array($custom_range_filters, 'custom_item_get_global_relation'), 10, 3);
			add_filter('m_router_data/custom_item_encode_global-relation', array($custom_range_filters, 'custom_item_encode_global_relation'), 10, 3);
			
			add_filter('dbm_content/get_taxonomies', array($this, 'filter_get_taxonomies'), 10, 1);
			add_filter('m_router_data/encode_post_add_ons', array($custom_range_filters, 'encode_post_add_ons'), 10, 2);
			
			add_filter('m_router_data/encode_term', array($custom_range_filters, 'encode_term'), 10, 3);
			add_filter('m_router_data/encode_term_link', array($custom_range_filters, 'encode_term'), 10, 3);
			
			
			add_filter( 'theme_page_templates', array($this, 'filter_global_page_templates'), 10, 1 );
		}
		
		public function filter_global_page_templates($post_templates) {
			
			$global_pages_parent_term = dbm_get_relation(array('global-pages'));
			
			if($global_pages_parent_term) {
				$global_pages_terms = \DbmContent\OddCore\Utils\TaxonomyFunctions::get_all_children_of_term($global_pages_parent_term->term_id, 'dbm_relation');
				
				foreach($global_pages_terms as $term) {
					$post_templates['template-global-'.($term->slug).'.php'] = ($term->name).' (global)';
				}
			}
			
			$global_pages_parent_term = dbm_get_relation(array('page-templates'));
			
			if($global_pages_parent_term) {
				$global_pages_terms = \DbmContent\OddCore\Utils\TaxonomyFunctions::get_all_children_of_term($global_pages_parent_term->term_id, 'dbm_relation');
				
				foreach($global_pages_terms as $term) {
					$post_templates['template-'.($term->slug).'.php'] = ($term->name);
				}
			}
			
			return $post_templates;
		}

		public function hook_admin_enqueue_scripts() {
			//echo("\DbmContent\Plugin::hook_admin_enqueue_scripts<br />");
			
			parent::hook_admin_enqueue_scripts();
		
			$screen = get_current_screen();
			
			$localized_data = array(
				'screen' => $screen,
				'restApiBaseUrl' => get_home_url().'/wp-json/'
			);
			
			$postData = null;
			if($screen && $screen->base === 'post' && function_exists('mrouter_encode_post')) {
				$postData = mrouter_encode_post(get_post());
			}
			
			$localized_data['postData'] = $postData;
			
			if(function_exists('mrouter_encode_all_taxonomies')) {
				$localized_data['taxonomies'] = mrouter_encode_all_taxonomies();
			}
		
			wp_enqueue_script( 'dbm-content-admin-main', DBM_CONTENT_URL . '/assets/js/admin.js');
			wp_localize_script(
				'dbm-content-admin-main',
				'oaWpAdminData',
				$localized_data
			);
		}
		
		public function hook_save_post($post_id, $post, $update) {
			//echo("\DbmContent\Plugin::hook_save_post<br />");

			if(wp_is_post_revision($post_id)) {
				return;
			}

			remove_action('save_post', array($this, 'hook_save_post'));

			parent::hook_save_post($post_id, $post, $update);
			
			if(isset($_POST['dbm_content'])) {
				
				$dbm_content_object = json_decode(stripslashes($_POST['dbm_content']), true);
				
				do_action('dbm_content/parse_dbm_content', $dbm_content_object, $post_id, $post);
			}
			
		}



		public static function test_import() {
			echo("Imported \DbmContent\Plugin<br />");
		}
	}
?>
