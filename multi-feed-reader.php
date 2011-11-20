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

namespace MultiFeedReader;

const DEFAULT_TEMPLATE = 'default';

require_once 'bootstrap.php';

/**
 * Translate text.
 * 
 * Shorthand method to translate text in the scope of the plugin.
 * 
 * Example:
 *   echo \MultiFeedReader\t( 'Hello World' );
 * 
 * @todo move somewhere else but keep namespace
 * 
 * @param	string $text
 * @return string
 */
function t( $text ) {
	return __( $text, TEXTDOMAIN );
}

function initialize() {
	add_shortcode( 'multi-feed-reader', 'MultiFeedReader\shortcode' );
	add_action( 'admin_menu', 'MultiFeedReader\add_menu_entry' );
}
add_action( 'plugins_loaded', 'MultiFeedReader\initialize' );

function shortcode( $attributes ) {
	extract(
		shortcode_atts(
			array(
				'template' => DEFAULT_TEMPLATE
			),
			$attributes
		)
	);
	
	echo $template;
}

function add_menu_entry() {
	add_submenu_page( 'options-general.php', PLUGIN_NAME, PLUGIN_NAME, 'manage_options', \MultiFeedReader\Settings\HANDLE, 'MultiFeedReader\Settings\initialize' );
}