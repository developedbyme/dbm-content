<?php
	namespace DbmContent;

	class RemoveCollection {
		
		protected $origin_id = 0;
		protected $items = array();
		protected $cache_items = array();
		
		function __construct() {
			//echo("\DbmContent\RemoveCollection::__construct<br />");
			
		}
		
		public function has_processed($post_id) {
			return in_array($post_id, $this->items);
		}
		
		public function set_origin_id($id) {
			$this->origin_id = $id;
		}
		
		public function add_item($post_id) {
			$this->items[] = $post_id;
		}
		
		public function clear_cache($post_id) {
			$this->cache_items[] = $post_id;
		}
		
		public function perform_remove_all() {
			
			global $dbm_skip_trash_cleanup;
			$previous_setting = $dbm_skip_trash_cleanup;
			$dbm_skip_trash_cleanup = true;
			
			$cached_items = array_diff($this->cache_items, $this->items);
			
			$remove_id  = $this->origin_id;
			
			$title = 'Removal of '.$remove_id.':'.get_the_title($remove_id);
			
			//$trash_log_id = dbm_create_data($title, 'trash-log');
			//$trash_log_post = dbm_get_post($trash_log_id);
			
			//$trash_log_post->update_meta('origin', $this->origin_id);
			//$trash_log_post->update_meta('removedItems', $this->items);
			//$trash_log_post->update_meta('clearCache', $cached_items);
			
			foreach($this->items as $remove_id) {
				wp_trash_post($remove_id);
			}
			
			foreach($cached_items as $cached_item_id) {
				dbm_clear_post_cache($cached_item_id);
			}
			
			//$trash_log_post->make_private();
			
			$dbm_skip_trash_cleanup = $previous_setting;
		}
		
		public static function test_import() {
			echo("Imported \DbmContent\RemoveCollection<br />");
		}
	}
?>
