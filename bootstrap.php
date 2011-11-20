<?php

// autoload all files in /lib
spl_autoload_register( function ( $class_name ) { 
	// get class name without namespace
	$class_name = strtolower( array_pop( explode( '\\', $class_name ) ) );
	// library directory
	$lib = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR;
	
	// register all possible paths for the class
	$possibilities = array(
		$lib . $class_name . '.php'
	);
	
	// search for the class
	foreach ( $possibilities as $file ) {
		if ( file_exists( $file ) ) {
			require_once( $file );
			return true;
		}
	}
	
	return false;
});

require_once 'settings.php';