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
			
			$current_custom_post_type = new \DbmContent\Admin\CustomPostTypes\ObjectRelationCustomPostType();
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
			
			$current_end_point = new \DbmContent\OddCore\RestApi\ReactivatePluginEndpoint();
			$current_end_point->set_plugin($this);
			$current_end_point->add_headers(array('Access-Control-Allow-Origin' => '*'));
			$current_end_point->setup('reactivate-plugin', $api_namespace, 1, 'GET');
			$this->_rest_api_end_points[] = $current_end_point;
		}
		
		public function register_hooks() {
			parent::register_hooks();
			
			add_action('dbm_content/parse_dbm_content', array($this, 'hook_parse_dbm_content'), 10, 3);
			add_action('dbmtc/internal_message/group_field_set', array($this, 'hook_dbmtc_internal_message_group_field_set'), 10, 5);
			
			add_action('dbm_content/clear_post_cache', array($this, 'hook_clear_post_cache'), 10, 1);
		}
		
		public function get_full_term_slug($term, $taxonomy) {
			$return_string = $term->slug;
			if($term->parent !== 0) {
				$parent_term = get_term_by('id', $term->parent, $taxonomy);
				$return_string = $this->get_full_term_slug($parent_term, $taxonomy).'/'.$return_string;
			}
			return $return_string;
		}
		
		public function hook_clear_post_cache($post_id) {
			//echo("hook_clear_post_cache");
			//var_dump($post_id);
			
			$post = dbm_get_post($post_id);
			$post->clear_cache();
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
			
			add_filter('dbm_content/get_menu_positions', array($this, 'filter_add_menu_position'), 10, 1);
			
			add_filter('wprr/admin/create_post/apply_data_type', array($this, 'filter_create_post_apply_data_type'), 10, 3);
		}
		
		public function filter_add_menu_position($menu_positions) {
			
			$menu_positions_term = dbm_get_relation_by_path('menu-position');
			if($menu_positions_term) {
				$parent_term_id = $menu_positions_term->term_id;
				//var_dump($menu_positions_term);
				$term_ids = get_term_children($parent_term_id, 'dbm_relation');
				foreach($term_ids as $term_id) {
					$parent_term = get_term_by('id', $term_id, 'dbm_relation');
					if($parent_term->parent === $parent_term_id) {
						$position_ids = get_term_children($term_id, 'dbm_relation');
						foreach($position_ids as $position_id) {
							$term = get_term_by('id', $position_id, 'dbm_relation');
							$menu_positions[$parent_term->slug.'_'.$term->slug] = $parent_term->name.': '.$term->name;
						}
					}
				}
			}
			
			return $menu_positions;
		}
		
		public function hook_init() {

			parent::hook_init();
			
			$menu_positions = array();
			
			$menu_positions = apply_filters('dbm_content/get_menu_positions', $menu_positions);
			
			register_nav_menus($menu_positions);
		}
		
		public function hook_save_post($post_id, $post, $update) {
			//echo("\DbmContent\Plugin::hook_save_post<br />");

			if(wp_is_post_revision($post_id)) {
				return;
			}
			
			if(in_array($post_id, $this->save_hooks_triggered)) {
				return;
			}
			
			wprr_performance_tracker()->start_meassure('DbmContent\Plugin hook_save_post');
			
			parent::hook_save_post($post_id, $post, $update);
			
			if(isset($_POST['dbm_content'])) {
				
				$dbm_content_object = json_decode(stripslashes($_POST['dbm_content']), true);
				
				do_action('dbm_content/parse_dbm_content', $dbm_content_object, $post_id, $post);
			}
			
			delete_post_meta($post_id, 'dbm/objectRelations/incoming');
			delete_post_meta($post_id, 'dbm/objectRelations/outgoing');
			
			if($post->post_type === 'dbm_object_relation') {
				delete_post_meta(get_post_meta($post_id, 'toId', true), 'dbm/objectRelations/incoming');
				delete_post_meta(get_post_meta($post_id, 'fromId', true), 'dbm/objectRelations/outgoing');
			}
			
			if($post->post_status === 'trash') {
				if(!dbm_has_post_type($post_id, 'trash-log')) {
					global $dbm_skip_trash_cleanup;
					if(!$dbm_skip_trash_cleanup) {
						dbm_trash_item($post_id);
					}
				}
			}
			else {
				dbm_add_post_type($post_id, 'post-type/'.$post->post_type);
			}
			
			wprr_performance_tracker()->stop_meassure('DbmContent\Plugin hook_save_post');
		}
		
		public function hook_dbmtc_internal_message_group_field_set($group, $field, $value, $user_id, $message) {
			
			if($field === 'name') {
				if(
					$group->has_type_by_name('process') ||
					$group->has_type_by_name('process-part') ||
					$group->has_type_by_name('content-section') ||
					$group->has_type_by_name('template-position') ||
					$group->has_type_by_name('content-template') ||
					$group->has_type_by_name('type') ||
					$group->has_type_by_name('named-item')
				) {
					global $wpdb;
					$wpdb->update( $wpdb->posts, array('post_title' => $value), array('ID' => $group->get_id()));
				}
			}
			
			if($group->has_type_by_name('object-relation')) {
				delete_post_meta($group->get_meta('toId'), 'dbm/objectRelations/incoming');
				delete_post_meta($group->get_meta('fromId'), 'dbm/objectRelations/outgoing');
			}
			else if($group->has_type_by_name('object-user-relation')) {
				delete_post_meta($group->get_meta('fromId'), 'dbm/userRelations');
			}
		}
		
		public function filter_create_post_apply_data_type($post_id, $data_type, $data) {
			dbm_add_post_type($post_id, $data_type);
		}
		
		public function activation_setup() {
			\DbmContent\Admin\PluginActivation::run_setup();
		}
		
		public static function test_import() {
			echo("Imported \DbmContent\Plugin<br />");
		}
	}
?>
