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
			
		}
		
		public function hook_set_relation($data, $post_id) {
			//echo("\DbmContent\ChangePostHooks::hook_set_relation<br />");
			
			$parent_slug = $data['path'];
			$ids = $data['value'];
			
			$parent = dbm_get_relation_by_path($parent_slug);
			
			dbm_replace_relations($post_id, $parent, $ids);
		}
		
		public static function test_import() {
			echo("Imported \DbmContent\ChangePostHooks<br />");
		}
	}
?>