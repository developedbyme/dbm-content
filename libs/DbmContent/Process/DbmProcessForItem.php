<?php
	namespace DbmContent\Process;
	
	use \DbmContent\DbmPost;

	class DbmProcessForItem extends DbmPost {
		
		function __construct($id) {
			//echo("\DbmContent\DbmProcessForItem::__construct<br />");
			
			parent::__construct($id);
		}
		
		public function get_processes() {
			
			$return_array = array();
			
			$processes = $this->get_outgoing_relations('following', 'process');
			
			foreach($processes as $process_id) {
				$to_id = (int)get_post_meta($process_id, 'toId', true);
				$process = dbm_get_process($to_id);
				$return_array[] = $process;
			}
			
			return $return_array;
		}
		
		public function get_parts() {
			$return_array = array();
			
			$processes = $this->get_processes();
			
			foreach($processes as $process) {
				$parts = $process->get_parts();
				$return_array = array_merge($return_array, $parts);
			}
			
			return $return_array;
		}
		
		public function get_current_part() {
			$parts = $this->get_parts();
			
			$skipped_parts = $this->get_outgoing_relations('skipped', 'process-part');
			$completed_parts = $this->get_outgoing_relations('completed', 'process-part');
			
			$ignored_array = array_merge($skipped_parts, $completed_parts);
			
			foreach($parts as $part) {
				if(!in_array($part->get_id(), $ignored_array)) {
					return $part;
				}
			}
			
			return null;
		}
		
		public function start_next_part() {
			$current_part = $this->get_current_part();
			
			if($current_part) {
				$started_parts = $this->get_outgoing_relations('started', 'process-part');
				if(!in_array($current_part->get_id(), $started_parts)) {
					$this->start_part($current_part->get_id());
				}
			}
			
			return $this;
		}
		
		public function start_part($id) {
			$this->add_outgoing_relation_by_name($id, 'started');
			
			do_action('dbm_content/process/start_part', $id, $this->get_id());
			
			return $this;
		}
		
		public function skip_part($id, $start_next = true) {
			$this->add_outgoing_relation_by_name($id, 'skipped');
			
			do_action('dbm_content/process/skip_part', $id, $this->get_id());
			
			if($start_next) {
				$this->start_next_part();
			}
			
			return $this;
		}
		
		public function complete_part($id, $start_next = true) {
			$this->add_outgoing_relation_by_name($id, 'completed');
			
			do_action('dbm_content/process/complete_part', $id, $this->get_id());
			
			if($start_next) {
				$this->start_next_part();
			}
			
			return $this;
		}
		
		public static function test_import() {
			echo("Imported \DbmContent\DbmProcessForItem<br />");
		}
	}
?>
