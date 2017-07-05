<?php
	namespace DbmContent\OddCore\Admin\MetaData;
	
	use \DbmContent\OddCore\Admin\MetaData\ReactPostMetaDataBox;
	
	// \DbmContent\OddCore\Admin\MetaData\ReactPostMetaDataHiddenBox
	class ReactPostMetaDataHiddenBox extends ReactPostMetaDataBox {

		
		function __construct() {
			//echo("\OddCore\Admin\MetaData\ReactPostMetaDataHiddenBox::__construct<br />");
			
			parent::__construct();
		}
		
		public function output_box_start($post) {
			?>
				<div>
					<div id="<?php echo($this->_holder_id); ?>">
			<?php
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Admin\MetaData\ReactPostMetaDataHiddenBox<br />");
		}
	}
?>