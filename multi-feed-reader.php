<?php
/*
Plugin Name: Multi Feed Reader
Plugin URI: 
Description: Reads multiple feeds. Output can be customized via templates. Is displayed via Shortcodes.
Version: 1.0
Author: Eric Teubert
Author URI: ericteubert@googlemail.com
License: MIT
*/

$correct_php_version = version_compare( phpversion(), "5.3", ">=" );

if ( ! $correct_php_version ) {
	echo "Multi Feed Reader requires <strong>PHP 5.3</strong> or higher.<br>";
	echo "You are running PHP " . phpversion();
	exit;
}

/**
 * @todo idea: namespace always reflects directory structure
 * not sure if this is a good thing but it would make autoloading truly awesome
 */
// autoload all files in /lib
function mfr_autoloader( $class_name ) { 
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
}
spl_autoload_register( 'mfr_autoloader' );

require_once 'constants.php';
require_once 'lib/general.php';
require_once 'lib/parser.php';
require_once 'models/base.php';
require_once 'models/feed_collection.php';
require_once 'models/feed.php';
require_once 'settings.php';

require_once 'plugin.php';