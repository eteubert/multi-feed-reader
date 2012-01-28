<?php
namespace MultiFeedReader;

/**
 * Conventions
 * 
 * 	Plugin Name:		This Is My Plugin
 * 	Plugin Namespace:	ThisIsMyPlugin
 * 	Plugin File:		this-is-my-plugin.php
 * 	Plugin Textdomain:	this-is-my-plugin
 */
define( 'PLUGIN_FILE_NAME', strtolower( preg_replace( '/([a-z])([A-Z])/', '$1-$2', __NAMESPACE__ ) ) . '.php' );
define( 'PLUGIN_FILE', plugin_dir_path( __FILE__ ) . PLUGIN_FILE_NAME );

/**
 * Get a value of the plugin header
 */
function get_plugin_header( $value ) {
	static $plugin_data; // only load file once
	
	if ( ! function_exists( 'get_plugin_data' ) ) {
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	}
	
	$plugin_data  = get_plugin_data( PLUGIN_FILE );
	$plugin_value = $plugin_data[ $value ];
	
	return $plugin_value;
}

define( 'PLUGIN_NAME', get_plugin_header( 'Name' ) );
define( 'TEXTDOMAIN', strtolower( str_replace( ' ', '-', PLUGIN_NAME ) ) );

const DEFAULT_TEMPLATE = 'default';

namespace MultiFeedReader\Settings;

const HANDLE = 'multi_feed_reader_handle';
const DEFAULT_BEFORE_TEMPLATE = '<table>
<thead>
<th style="width:74px"></th>
<th>Titel</th>
<th>Dauer</th>
</thead>
<tbody>';
const DEFAULT_BODY_TEMPLATE = '<tr class="podcast_archive_element">
	<td class="thumbnail">%THUMBNAIL|64x64%</td>
	<td class="title" style="vertical-align:top">
		<a href="%LINK%"><strong>%TITLE%</strong></a><br/><em>%SUBTITLE%</em>
	</td>
	<td class="duration" style="vertical-align:top">
		%DURATION%
	</td>
</tr>';
const DEFAULT_AFTER_TEMPLATE = '</tbody>
</table>';