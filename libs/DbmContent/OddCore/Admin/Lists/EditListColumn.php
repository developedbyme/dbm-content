<?php
	namespace DbmContent\OddCore\Admin\Lists;
	
	// \DbmContent\OddCore\Admin\Lists\EditListColumn
	class EditListColumn {
		
		public $name = 'Master ingredient';
		
		function __construct() {
			//echo("\OddCore\Admin\Lists\EditListColumn::__construct<br />");
			
			
		}
		
		public function output($out, $term_id) {
			//echo("\OddCore\Admin\Lists\EditListColumn::output<br />");
			
			return "METODO";
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Admin\Lists\EditListColumn<br />");
		}
	}
?>