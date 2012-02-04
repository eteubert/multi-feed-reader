<?php
/*
Plugin Name: Multi Feed Reader
Plugin URI: https://github.com/eteubert/multi-feed-reader
Description: Reads multiple feeds. Output can be customized via templates. Is displayed via Shortcodes.
Version: 1.1.1
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

require_once 'bootstrap/bootstrap.php';

require_once 'constants.php';
require_once 'lib/general.php';
require_once 'lib/parser.php';
require_once 'settings.php';

require_once 'plugin.php';