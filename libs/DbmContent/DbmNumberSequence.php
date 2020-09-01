<?php
	namespace DbmContent;
	
	use \DbmContent\DbmPost;

	class DbmNumberSequence extends DbmPost {

		protected $id = array();

		function __construct($id) {
			//echo("\DbmContent\DbmNumberSequence::__construct<br />");
			
			parent::__construct($id);
		}
		
		public function generate_next_number() {
			
			global $wpdb;
			
			$post_id = $this->get_id();
			
			$increment_statement = "";
			$increment_statement .= "UPDATE {$wpdb->postmeta} SET meta_value = @sequencenumber := meta_value+1 WHERE post_id=%d AND meta_key='currentSequenceNumber' LIMIT 1;\n\n";
			$increment_statement .= "SELECT @sequencenumber;";
			
			$safe_statement = $wpdb->prepare($increment_statement, $post_id);
			
			$results = $wpdb->dbh->multi_query($safe_statement);
			
			$wpdb->dbh->next_result();
			$result = $wpdb->dbh->store_result();
			$sequence_number = (int)$result->fetch_row()[0];
			$result->free();
			
			$full_id = '';
			$padding = (int)$this->get_meta('padding');
			if($padding) {
				$full_id = str_pad($sequence_number, $padding, "0", STR_PAD_LEFT);
			}
			else {
				$full_id = $sequence_number;
			}
			
			if($this->get_meta('prefix')) {
				$full_id = $this->get_meta('prefix').$full_id;
			}
			if($this->get_meta('suffix')) {
				$full_id = $full_id.$this->get_meta('suffix');
			}
			
			$order_number_id = dbm_create_data('Sequence number '.$full_id, 'sequence-number');
			$order_number = dbm_get_post($order_number_id);
			
			$order_number->update_meta('number', $sequence_number);
			$order_number->update_meta('fullIdentifier', $full_id);
			$order_number->change_status('private');
			$order_number->add_outgoing_relation_by_name($post_id, 'in');
			
			return $order_number;
		}
		
		public static function test_import() {
			echo("Imported \DbmContent\DbmNumberSequence<br />");
		}
	}
?>
