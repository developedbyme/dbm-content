<?php
	namespace DbmContent;

	use \DbmContent\OddCore\PluginBase;

	class Plugin extends PluginBase {

		function __construct() {
			//echo("\DbmContent\Plugin::__construct<br />");

			parent::__construct();

			//$this->add_javascript('dbm_content-main', DBM_CONTENT_URL.'/assets/js/main.js');
		}

		protected function create_pages() {
			//echo("\DbmContent\Plugin::create_pages<br />");

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


		}

		protected function create_rest_api_end_points() {
			//echo("\DbmContent\Plugin::create_rest_api_end_points<br />");

			$api_namespace = 'dbm-content';
			
			
		}
		
		protected function create_filters() {
			//echo("\DbmContent\Plugin::create_filters<br />");
			
			$custom_range_filters = new \DbmContent\CustomRangeFilters();
			
			add_filter('m_router_data/custom_range_query_dbm-relations', array($custom_range_filters, 'query_relations'), 10, 2);
			add_filter('m_router_data/custom_range_encode_dbm-relations', array($custom_range_filters, 'encode_relations'), 10, 1);
			
			add_filter('dbm_content/get_taxonomies', array($this, 'filter_get_taxonomies'), 10, 1);
			add_filter('m_router_data/encode_post_add_ons', array($custom_range_filters, 'encode_post_add_ons'), 10, 2);
			
			add_filter('m_router_data/encode_term', array($custom_range_filters, 'encode_term'), 10, 3);
		}
		
		

		public function hook_admin_enqueue_scripts() {
			//echo("\DbmContent\Plugin::hook_admin_enqueue_scripts<br />");

			parent::hook_admin_enqueue_scripts();
			
		}



		public static function test_import() {
			echo("Imported \DbmContent\Plugin<br />");
		}
	}
?>
