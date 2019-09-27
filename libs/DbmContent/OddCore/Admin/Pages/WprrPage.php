<?php
	namespace DbmContent\OddCore\Admin\Pages;
	
	use DbmContent\OddCore\Admin\Pages\Page as Page;
	
	// \DbmContent\OddCore\Admin\Pages\WprrPage
	class WprrPage extends Page {
		
		protected $_component_name = null;
		protected $_data = null;
		
		function __construct() {
			//echo("\OddCore\Admin\Pages\WprrPage::__construct<br />");
		}
		
		public function set_component($name, $data = null) {
			$this->_component_name = $name;
			$this->_data = $data;
			
			return $this;
		}
		
		protected function get_react_data() {
			
			return $this->_data;
		}
		
		public function output() {
			//echo("\OddCore\Admin\Pages\WprrPage::output<br />");
			
			wprr_output_module($this->_component_name, $this->_data);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Admin\Pages\WprrPage<br />");
		}
	}
?>