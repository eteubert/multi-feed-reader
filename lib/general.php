<?php
/**
 * General helper methods.
 * 
 * @todo find a better place for general helpers
 * I feel like there is a better place to put them. /lib feels right, but
 * the namespace should be MultiFeedReader\Lib. Though, for brevity I want
 * to keep the namespace as short as possible.
 */
namespace MultiFeedReader;

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