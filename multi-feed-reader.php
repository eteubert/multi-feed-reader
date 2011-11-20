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

const TEXTDOMAIN = 'multi-feed-reader';
const DEFAULT_TEMPLATE = 'default';

/**
 * Translate text.
 * 
 * Shorthand method to translate text in the scope of the plugin.
 * 
 * Example:
 *   echo \MultiFeedReader\t( 'Hello World' );
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