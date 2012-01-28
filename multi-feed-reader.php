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
	$split  = explode( '\\', $class_name );
	// remove "MultiFeedReader" namespace
	$plugin = array_shift( $split ); 
	
	// only load classes prefixed with <Plugin> namespace
	if ( $plugin != "MultiFeedReader" )
		return false;
	
	// class name without namespace
	$class_name = array_pop( $split );
	// camel case to snake case
	$class_name = preg_replace('/([a-z])([A-Z])/', '$1_$2', $class_name );

	// the rest of the namespace, if any
	$namespaces = $split;

	// library directory
	$lib = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR;
	
	// register all possible paths for the class
	$possibilities = array();
	if ( count( $namespaces ) >= 1 ) {
		$possibilities[] = strtolower( $lib . implode( DIRECTORY_SEPARATOR, $namespaces ) . DIRECTORY_SEPARATOR . $class_name . '.php' );
	} else {
		$possibilities[] = strtolower( $lib . $class_name . '.php' );
	}
	
	file_put_contents('/tmp/php.log', print_r($possibilities, true), FILE_APPEND | LOCK_EX);
	
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
require_once 'settings.php';

require_once 'plugin.php';