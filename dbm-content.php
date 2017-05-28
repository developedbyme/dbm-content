<?php 
	/*
	Plugin Name: DBM content
	Plugin URI: http://developedbyme.com
	Description: Content functionality
	Version: 0.1.0
	Author: Mattias Ekenedahl
	Author URI: http://developedbyme.com
	*/
	
	define("DBM_CONTENT_DOMAIN", "dbm_content");
	define("DBM_CONTENT_TEXTDOMAIN", "dbm_content");
	define("DBM_CONTENT_MAIN_FILE", __FILE__);
	define("DBM_CONTENT_DIR", untrailingslashit(dirname(__FILE__)));
	define("DBM_CONTENT_URL", untrailingslashit(plugins_url('',__FILE__)));
	define("DBM_CONTENT_VERSION", '0.1.0');
	
	require_once(DBM_CONTENT_DIR."/libs/DbmContent/bootstrap.php");

	$DbmContentPlugin = new \DbmContent\Plugin();

	require_once(DBM_CONTENT_DIR."/external-functions.php");
?>