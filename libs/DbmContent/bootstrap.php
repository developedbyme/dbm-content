<?php

function DbmContent_Autoloader( $class ) {
	//echo("DbmContent_Autoloader<br />");
	
	$namespace_length = strlen("DbmContent");
	
	// Is a DbmContent class
	if ( substr( $class, 0, $namespace_length ) != "DbmContent" ) {
		return false;
	}

	// Uses namespace
	if ( substr( $class, 0, $namespace_length+1 ) == "DbmContent\\" ) {

		$path = explode( "\\", $class );
		unset( $path[0] );

		$class_file = trailingslashit( dirname( __FILE__ ) ) . implode( "/", $path ) . ".php";

	}

	// Doesn't use namespaces
	elseIf ( substr( $class, 0, $namespace_length+1 ) == "DbmContent_" ) {

		$path = explode( "_", $class );
		unset( $path[0] );

		$class_file = trailingslashit( dirname( __FILE__ ) ) . implode( "/", $path ) . ".php";

	}

	// Get class
	if ( isset($class_file) && is_file( $class_file ) ) {

		require_once( $class_file );
		return true;

	}

	// Fallback to error
	return false;

}

spl_autoload_register("DbmContent_Autoloader"); // Register autoloader