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
		
		public function get_statuses() {
			$return_array = array();
			$names = array('skipped', 'completed', 'started');
			foreach($names as $name) {
				$return_array[$name] = $this->object_relation_query('out:'.$name.':process-part');
			}
			
			return $return_array;
		}
		
		public function get_current_part() {
			$parts = $this->get_parts();
			
			$skipped_parts = $this->object_relation_query('out:skipped:process-part');
			$completed_parts = $this->object_relation_query('out:completed:process-part');
			
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
				$started_parts = $this->object_relation_query('out:started:process-part');
				if(!in_array($current_part->get_id(), $started_parts)) {
					$this->start_part($current_part->get_id());
				}
			}
			
			return $this;
		}
		
		public function start_part($id) {
			$this->add_outgoing_relation_by_name($id, 'started');
			
			$part = dbm_get_process_part($id);
			$type = $part->get_type();
			if($type) {
				do_action('dbm_content/process/start_part/'.$type, $id, $this->get_id());
			}
			
			do_action('dbm_content/process/start_part', $id, $this->get_id());
			
			return $this;
		}
		
		public function skip_part($id, $start_next = true) {
			$this->add_outgoing_relation_by_name($id, 'skipped');
			
			$part = dbm_get_process_part($id);
			$type = $part->get_type();
			if($type) {
				do_action('dbm_content/process/skip_part/'.$type, $id, $this->get_id());
			}
			
			do_action('dbm_content/process/skip_part', $id, $this->get_id());
			
			if($start_next) {
				$this->start_next_part();
			}
			
			return $this;
		}
		
		public function skip_part_by_indentifier($identifier, $start_next = true) {
			$part = $this->get_part_by_identifier($identifier);
			if($part) {
				$this->skip_part($part->get_id(), $start_next);
			}
		}
		
		public function complete_part($id, $start_next = true) {
			$this->add_outgoing_relation_by_name($id, 'completed');
			
			$part = dbm_get_process_part($id);
			$type = $part->get_type();
			if($type) {
				do_action('dbm_content/process/complete_part/'.$type, $id, $this->get_id());
			}
			
			do_action('dbm_content/process/complete_part', $id, $this->get_id());
			
			if($start_next) {
				$this->start_next_part();
			}
			
			return $this;
		}
		
		public function get_part_by_identifier($identifier) {
			$parts = $this->get_parts();
			foreach($parts as $part) {
				if($part->get_identifier() === $identifier) {
					return $part;
				}
			}
			
			return null;
		}
		
		public function complete_part_by_indentifier($identifier, $start_next = true) {
			$part = $this->get_part_by_identifier($identifier);
			if($part) {
				$this->complete_part($part->get_id(), $start_next);
			}
		}
		
		public static function test_import() {
			echo("Imported \DbmContent\DbmProcessForItem<br />");
		}
	}
?>
