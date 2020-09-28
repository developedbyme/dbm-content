<?php
	namespace DbmContent\Admin\CustomPostTypes;
	
	use \DbmContent\OddCore\Admin\CustomPostTypes\CustomPostTypePost;
	
	// \DbmContent\Admin\CustomPostTypes\ObjectRelationCustomPostType
	class ObjectRelationCustomPostType extends CustomPostTypePost {
		
		function __construct() {
			//echo("\OddCore\Admin\CustomPostTypes\ObjectRelationCustomPostType::__construct<br />");
			
			$this->set_names('dbm_object_relation', 'Object relation');
			
			$this->set_argument('public', false);
			$this->set_argument('publicly_queryable', false);
			$this->set_argument('show_ui', true);
			$this->set_argument('exclude_from_search', true);
			$this->set_argument('show_in_nav_menus', false);
			$this->set_argument('has_archive', false);
			$this->set_argument('hierarchical', false);
			
			$this->set_argument('supports', array( 'title', 'page-attributes' ));
			
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Admin\CustomPostTypes\ObjectRelationCustomPostType<br />");
		}
	}
?>