<?php
	namespace DbmContent;

	class DbmObjectRelation extends DbmPost {

		function __construct($id) {
			//echo("\DbmContent\DbmPost::__construct<br />");
			
			parent::__construct($id);
		}
		
		public function get_from_id() {
			return (int)$this->get_meta('fromId');
			update_post_meta($new_id, 'toId', $to_object_id);
		}
		
		public function get_to_id() {
			return (int)$this->get_meta('toId');
		}
		
		public function start_at($time) {
			$this->update_meta('startAt', $time);
			
			return $this;
		}
		
		public function start_now() {
			$time = time();
			
			$this->start_at($time);
			
			return $this;
		}
		
		public function end_at($time) {
			$this->update_meta('endAt', $time);
			
			return $this;
		}
		
		public function end_now() {
			$time = time();
			
			$this->end_at($time);
			
			return $this;
		}
		
		public function clear_cache_for_links() {
			
			delete_post_meta($this->get_from_id(), 'dbm/objectRelations/outgoing');
			delete_post_meta($this->get_to_id(), 'dbm/objectRelations/incoming');
			
			return $this;
		}
		
		public static function test_import() {
			echo("Imported \DbmContent\DbmPost<br />");
		}
	}
?>
