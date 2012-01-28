<?php
namespace MultiFeedReader;

/**
 * @todo idea: namespace always reflects directory structure
 * not sure if this is a good thing but it would make autoloading truly awesome
 */
// autoload all files in /lib
spl_autoload_register( function ( $class_name ) { 
	// get class name without namespace
	$splitted_class = explode( '\\', $class_name );
	$class_name     = strtolower( array_pop( $splitted_class ) );
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

require_once 'lib/general.php';
require_once 'lib/parser.php';
require_once 'models/base.php';
require_once 'models/feed_collection.php';
require_once 'models/feed.php';
require_once 'settings.php';