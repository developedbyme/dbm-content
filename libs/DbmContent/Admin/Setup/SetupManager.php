<?php
	namespace DbmContent\Admin\Setup;
	
	// \DbmContent\Admin\Setup\SetupManager
	class SetupManager {
		
		protected $types = array();
		protected $relations = array();
		
		function __construct() {
			//echo("\OddCore\Admin\Setup\SetupManager::__construct<br />");
			
			
		}
		
		public function create_data_type($type) {
			if(!isset($this->types[$type])) {
				$new_type = new \DbmContent\Admin\Setup\DataTypeSetup();
				$new_type->set_type($type);
				$new_type->set_name($type);
				$this->types[$type] = $new_type;
			}
			
			return $this->types[$type];
		}
		
		public function create_relation($name, $slug = null) {
			
			if(!$slug) {
				$slug = sanitize_title_with_dashes(remove_accents($name));
			}
			
			if(isset($this->relations[$slug])) {
				return $this->relations[$slug];
			}
			
			$new_term = new \DbmContent\Admin\Setup\TermSetup();
			
			$new_term->set_taxonomy('dbm_relation');
			$new_term->set_name($name);
			$new_term->set_slug($slug);
			
			$this->relations[$slug] = $new_term;
			
			return $new_term;
		}
		
		public function create_object_relations($names) {
			$type = $this->create_data_type('object-relation')->set_name('Object relation');
			return $type->create_subtypes_by_name($names);
		}
		
		public function save_all() {
			foreach($this->types as $type) {
				$type->save();
			}
			
			foreach($this->relations as $relation) {
				$relation->save();
			}
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Admin\Setup\SetupManager<br />");
		}
	}
?>