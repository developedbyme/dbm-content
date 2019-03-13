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
			$this->register_hook_for_type('dbm/addTermFromOwner', 'hook_add_term_from_owner');
			
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
		
		public function hook_add_term_from_owner($data, $post_id) {
			//echo("\DbmContent\ChangePostHooks::hook_add_term_from_owner<br />");
			
			$owner_id = $data['value'];
			
			$owner = get_post($owner_id);
			if($owner) {
				$meta_name = 'dbm_relation_term_'.$data['group'];
				$term_id = (int)get_post_meta($owner_id, $meta_name, true);
				wp_add_object_terms($post_id, array($term_id), 'dbm_relation');
			}
		}
		
		public static function test_import() {
			echo("Imported \DbmContent\ChangePostHooks<br />");
		}
	}
?>