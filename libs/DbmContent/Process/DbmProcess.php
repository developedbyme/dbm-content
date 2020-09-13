<?php
	namespace DbmContent\Process;
	
	use \DbmContent\DbmPost;

	class DbmProcess extends DbmPost {

		function __construct($id) {
			//echo("\DbmContent\DbmProcess::__construct<br />");
			
			parent::__construct($id);
		}
		
		public function get_parts() {
			$return_array = array();
			
			$unsorted_process_parts = $this->get_incoming_relations('in', 'process-part');
			
			$process_parts = $this->get_in_sorted_order($unsorted_process_parts, 'parts');
			var_dump($unsorted_process_parts, $process_parts);
			
			foreach($process_parts as $process_part_id) {
				$from_id = (int)get_post_meta($process_part_id, 'toId', true);
				$part = dbm_get_process_part($from_id);
				$return_array[] = $part;
			}
			
			return $return_array;
		}
		
		public function add_following_item($id) {
			$this->add_incoming_relation_by_name($id, 'following');
			
			return $this;
		}
		
		public static function test_import() {
			echo("Imported \DbmContent\DbmProcess<br />");
		}
	}
?>
