<?php
	namespace DbmContent\Admin\Setup;
	
	// \DbmContent\Admin\Setup\TermSetup
	class TermSetup {
		
		protected $slug = null;
		protected $taxonomy = null;
		protected $name = null;
		
		protected $children = array();
		
		function __construct() {
			//echo("\OddCore\Admin\Setup\TermSetup::__construct<br />");
			
			
		}
		
		public function set_taxonomy($taxonomy) {
			$this->taxonomy = $taxonomy;
			
			return $this;
		}
		
		public function set_slug($slug) {
			$this->slug = $slug;
			
			return $this;
		}
		
		public function set_name($name) {
			$this->name = $name;
			
			return $this;
		}
		
		public function create_term($name, $slug = null) {
			
			if(!$slug) {
				$slug = sanitize_title_with_dashes(remove_accents($name));
			}
			
			$new_term = new TermSetup();
			
			$new_term->set_taxonomy($this->taxonomy);
			$new_term->set_name($name);
			$new_term->set_slug($this->slug.'/'.$slug);
			
			$this->children[$slug] = $new_term;
			
			return $new_term;
		}
		
		public function create_terms($names) {
			$return_array = array();
			foreach($names as $name) {
				$return_array[] = $this->create_term($name);
			}
			
			return $return_array;
		}
		
		public function save() {
			//echo("\OddCore\Admin\Setup\TermSetup::save<br />");
			
			$path = explode('/', $this->slug);
			\DbmContent\OddCore\Utils\TaxonomyFunctions::add_term($this->name, $path, $this->taxonomy);
			
			foreach($this->children as $child) {
				$child->save();
			}
			
			return $this;
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Admin\Setup\TermSetup<br />");
		}
	}
?>