<?php
	namespace DbmContent\Admin\CustomPostTypes;
	
	use \DbmContent\OddCore\Admin\CustomPostTypes\CustomPostTypePost;
	
	// \DbmContent\Admin\CustomPostTypes\AreaCustomPostType
	class AreaCustomPostType extends CustomPostTypePost {
		
		function __construct() {
			//echo("\OddCore\Admin\CustomPostTypes\AreaCustomPostType::__construct<br />");
			
			$this->set_names('dbm_area', 'area');
			
			$this->set_argument('public', false);
			$this->set_argument('publicly_queryable', true);
			$this->set_argument('show_ui', true);
			$this->set_argument('exclude_from_search', true);
			$this->set_argument('show_in_nav_menus', false);
			$this->set_argument('has_archive', false);
			$this->set_argument('rewrite', false);
			$this->set_argument('hierarchical', true);
			
			$this->set_argument('supports', array( 'title', 'editor', 'thumbnail', 'page-attributes'));
			
			$current_taxonomy = $this->create_taxonomy('dbm_type', 'content type', true);
			$current_taxonomy->set_argument('public', false);
			//$current_taxonomy->set_argument('show_ui', false);
			//$current_taxonomy->set_argument('show_in_nav_menus', false);
			
			$current_taxonomy = $this->create_taxonomy('dbm_relation', 'content relation', true);
			$current_taxonomy->set_argument('public', false);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Admin\CustomPostTypes\AreaCustomPostType<br />");
		}
	}
?>