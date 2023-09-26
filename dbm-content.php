<?php 
	/*
	Plugin Name: DBM content
	Plugin URI: http://developedbyme.com
	Description: Content functionality
	Version: 1.4.0
	Author: Mattias Ekenedahl
	Author URI: http://developedbyme.com
	License: MIT
	*/
	
	define("DBM_CONTENT_DOMAIN", "dbm_content");
	define("DBM_CONTENT_TEXTDOMAIN", "dbm_content");
	define("DBM_CONTENT_MAIN_FILE", __FILE__);
	define("DBM_CONTENT_DIR", untrailingslashit(dirname(__FILE__)));
	define("DBM_CONTENT_URL", untrailingslashit(plugins_url('',__FILE__)));
	define("DBM_CONTENT_VERSION", '1.4.0');
	
	require_once(DBM_CONTENT_DIR."/libs/DbmContent/bootstrap.php");
	
	require_once(DBM_CONTENT_DIR."/register-acf-fields.php");
	
	global $DbmContentPlugin;
	$DbmContentPlugin = new \DbmContent\Plugin();

	require_once(DBM_CONTENT_DIR."/external-functions.php");
	
	function dbm_content_compat_plugin_activate() {
		global $DbmContentPlugin;
		$DbmContentPlugin->activation_setup();
	}
	register_activation_hook( __FILE__, 'dbm_content_compat_plugin_activate' );
?>