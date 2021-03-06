<?php
	namespace DbmContent;

	class DbmObjectRelation extends DbmPost {

		function __construct($id) {
			//echo("\DbmContent\DbmPost::__construct<br />");
			
			parent::__construct($id);
		}
		
		public static function test_import() {
			echo("Imported \DbmContent\DbmPost<br />");
		}
	}
?>
