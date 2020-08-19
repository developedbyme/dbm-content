<?php
	namespace DbmContent\Admin\Setup;
	
	// \DbmContent\Admin\Setup\DataTypeSetup
	class DataTypeSetup {
		
		protected $type = null;
		protected $name = null;
		
		protected $fields = array();
		
		protected $types = array();
		
		function __construct() {
			//echo("\OddCore\Admin\Setup\DataTypeSetup::__construct<br />");
			
			
		}
		
		public function set_type($type) {
			$this->type = $type;
			
			return $this;
		}
		
		public function set_name($name) {
			$this->name = $name;
			
			return $this;
		}
		
		public function create_subtype($type) {
			if(!isset($this->types[$type])) {
				$new_type = new \DbmContent\Admin\Setup\DataTypeSetup();
				$new_type->set_type($this->type.'/'.$type);
				$new_type->set_name($type);
				$this->types[$type] = $new_type;
			}
			
			return $this->types[$type];
		}
		
		public function create_subtypes_by_name($names) {
			$return_array = array();
			
			foreach($names as $name) {
				$slug = sanitize_title_with_dashes(remove_accents($name));
				
				$return_array[] = $this->create_subtype($slug)->set_name($name);
			}
			
			return $return_array;
		}
		
		public function add_field($name) {
			if(!isset($this->fields[$name])) {
				$new_field = new \DbmContent\Admin\Setup\FieldSetup();
				$new_field->set_for_type($this->type);
				$new_field->set_name($name);
				$this->fields[$name] = $new_field;
			}
			
			return $this->fields[$name];
		}
		
		public function save_term() {
			$path = explode('/', $this->type);
			\DbmContent\OddCore\Utils\TaxonomyFunctions::add_term($this->name, $path, 'dbm_type');
			
			return $this;
		}
		
		public function save() {
			//echo("\OddCore\Admin\Setup\DataTypeSetup::save<br />");
			$this->save_term();
			
			foreach($this->fields as $field) {
				$field->save();
			}
			
			foreach($this->types as $type) {
				$type->save();
			}
			
			return $this;
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Admin\Setup\DataTypeSetup<br />");
		}
	}
?>