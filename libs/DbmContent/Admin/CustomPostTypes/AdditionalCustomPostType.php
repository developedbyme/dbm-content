<?php
	namespace DbmContent\Admin\CustomPostTypes;
	
	use \DbmContent\OddCore\Admin\CustomPostTypes\CustomPostTypePost;
	
	// \DbmContent\Admin\CustomPostTypes\AdditionalCustomPostType
	class AdditionalCustomPostType extends CustomPostTypePost {
		
		function __construct() {
			//echo("\OddCore\Admin\CustomPostTypes\AdditionalCustomPostType::__construct<br />");
			
			$this->set_names('dbm_additional', 'additional page');
			
			$this->set_argument('public', true);
			$this->set_argument('publicly_queryable', true);
			$this->set_argument('show_ui', true);
			$this->set_argument('exclude_from_search', true);
			$this->set_argument('show_in_nav_menus', false);
			$this->set_argument('has_archive', false);
			$this->set_argument('rewrite', array('slug' => 'a'));
			$this->set_argument('hierarchical', true);
			$this->set_argument('show_in_rest', true);
			
			$this->set_argument('supports', array( 'title', 'excerpt', 'editor', 'thumbnail', 'comments', 'page-attributes'));
			
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Admin\CustomPostTypes\AdditionalCustomPostType<br />");
		}
	}
?>