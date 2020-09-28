<?php
	namespace DbmContent\Admin\Setup;
	
	// \DbmContent\Admin\Setup\FieldSetup
	class FieldSetup {
		
		protected $for_type = null;
		protected $type = 'string';
		protected $name = null;
		protected $storage_type = null;
		protected $meta = array();
		
		function __construct() {
			//echo("\OddCore\Admin\Setup\FieldSetup::__construct<br />");
			
			
		}
		
		public function set_for_type($for_type) {
			$this->for_type = $for_type;
			
			return $this;
		}
		
		public function set_type($type) {
			$this->type = $type;
			
			return $this;
		}
		
		public function set_name($name) {
			$this->name = $name;
			
			return $this;
		}
		
		public function set_meta($key, $value) {
			$this->meta[$key] = $value;
			
			return $this;
		}
		
		public function setup_meta_storage() {
			
			$this->setup_meta_storage_with_name($this->name);
			
			return $this;
		}
		
		public function setup_meta_storage_with_name($meta_name) {
			
			$this->storage_type = 'meta';
			$this->set_meta('dbmtc_meta_name', $meta_name);
			
			return $this;
		}
		
		public function setup_relation_type($parent_term) {
			$this->set_type('relation');
			
			$this->set_meta('subtree', $parent_term);
			
			
			return $this;
		}
		
		public function setup_multiple_relation_type($parent_term) {
			$this->set_type('multiple-relation');
			
			$this->set_meta('subtree', $parent_term);
			
			
			return $this;
		}
		
		public function setup_post_type($post_type, $selection) {
			$this->set_type('post-relation');
			$this->set_meta('postType', $post_type);
			$this->set_meta('selection', $selection);
			
			return $this;
		}
		
		public function setup_single_relation_storage($parent_term, $use_slug = false) {
			
			$this->storage_type = 'single-relation';
			$this->setup_relation_type($parent_term);
			$this->set_meta('dbmtc_relation_path', $parent_term);
			$this->set_meta('dbmtc_relation_use_slug', $use_slug);
			
			return $this;
		}
		
		public function setup_multiple_relation_storage($parent_term, $use_slug = false) {
			
			$this->storage_type = 'multiple-relation';
			$this->setup_multiple_relation_type($parent_term);
			$this->set_meta('dbmtc_relation_path', $parent_term);
			$this->set_meta('dbmtc_relation_use_slug', $use_slug);
			
			return $this;
		}
		
		public function save() {
			//echo("\OddCore\Admin\Setup\FieldSetup::save<br />");
			
			dbmtc_setup_field_template($this->for_type, $this->name, $this->type, $this->storage_type, $this->meta);
			
			return $this;
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Admin\Setup\FieldSetup<br />");
		}
	}
?>