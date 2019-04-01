<?php
	namespace DbmContent;
	
	use \WP_Query;
	
	// \DbmContent\ChangePostHooks
	class ChangePostHooks {
		
		function __construct() {
			//echo("\DbmContent\ChangePostHooks::__construct<br />");
			
			
		}
		
		protected function register_hook_for_type($type, $hook_name) {
			add_action('wprr/admin/change_post/'.$type, array($this, $hook_name), 10, 2);
		}
		
		public function register() {
			//echo("\DbmContent\ChangePostHooks::register<br />");
			
			$this->register_hook_for_type('dbm/relation', 'hook_set_relation');
			$this->register_hook_for_type('dbm/autoDbmContent', 'hook_auto_dbm_content');
			$this->register_hook_for_type('dbm/inAdminGrouping', 'hook_in_admin_grouping');
			
		}
		
		public function hook_set_relation($data, $post_id) {
			//echo("\DbmContent\ChangePostHooks::hook_set_relation<br />");
			
			$parent_slug = $data['path'];
			$ids = $data['value'];
			
			$parent = dbm_get_relation_by_path($parent_slug);
			
			dbm_replace_relations($post_id, $parent, $ids);
		}
		
		public function hook_auto_dbm_content($data, $post_id) {
			
			$post = get_post($post_id);
			
			$dbm_content_object = dbm_get_content_object_for_type_and_relation($post_id);
			
			do_action('dbm_content/parse_dbm_content', $dbm_content_object, $post_id, $post);
		}
		
		public function hook_in_admin_grouping($data, $post_id) {
			
			$path = 'admin-grouping/'.$data['value'];
			
			var_dump($path);
			
			$parent = dbm_get_post_id_by_type_and_relation('any', array($path));
			
			var_dump($parent);
			
			if($parent) {
				$args = array(
					'ID' => $post_id,
					'post_parent' => $parent
				);
				
				var_dump($args);
				
				wp_update_post($args);
			}
		}
		
		public static function test_import() {
			echo("Imported \DbmContent\ChangePostHooks<br />");
		}
	}
?>