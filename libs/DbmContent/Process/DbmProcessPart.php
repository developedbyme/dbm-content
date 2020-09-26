<?php
	namespace DbmContent\Process;
	
	use \DbmContent\DbmPost;

	class DbmProcessPart extends DbmPost {

		function __construct($id) {
			//echo("\DbmContent\DbmProcessPart::__construct<br />");
			
			parent::__construct($id);
		}
		
		public function get_type() {
			return $this->get_meta('type');
		}
		
		public function get_identifier() {
			return $this->get_meta('identifier');
		}
		
		public static function test_import() {
			echo("Imported \DbmContent\DbmProcessPart<br />");
		}
	}
?>
