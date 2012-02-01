<?php
namespace MultiFeedReader;

register_activation_hook(   PLUGIN_FILE, __NAMESPACE__ . '\activate' );
register_deactivation_hook( PLUGIN_FILE, __NAMESPACE__ . '\deactivate' );
register_uninstall_hook(    PLUGIN_FILE, __NAMESPACE__ . '\uninstall' );

function initialize() {	
	add_shortcode( SHORTCODE, 'MultiFeedReader\shortcode' );
	add_action( 'admin_menu', 'MultiFeedReader\add_menu_entry' );
}
add_action( 'plugins_loaded', 'MultiFeedReader\initialize' );


function activate() {
	Models\Feed::build();
	Models\FeedCollection::build();
}

function deactivate() {

}

function uninstall() {
	Models\Feed::destroy();
	Models\FeedCollection::destroy();
}

function add_menu_entry() {
	add_submenu_page( 'options-general.php', PLUGIN_NAME, PLUGIN_NAME, 'manage_options', \MultiFeedReader\Settings\HANDLE, 'MultiFeedReader\Settings\initialize' );
}

function shortcode( $attributes ) {
	extract(
		shortcode_atts(
			array(
				'template' => DEFAULT_TEMPLATE,
				'limit'    => DEFAULT_LIMIT,
				'nocache'  => false
			),
			$attributes
		)
	);
	
	if ( $nocache === false ) {
		$cache_key = get_cache_key( $template . $limit );
	    if ( false === ( $out = get_transient( $cache_key ) ) ) {
	        $out = generate_html_by_template( $template, $limit );
	        set_transient( $cache_key, $out, 60 * 5 ); // 5 minutes
	    }
	} else {
		$out = generate_html_by_template( $template, $limit );
	}

	echo $out;
}

function get_cache_key( $template ) {
    return 'multi_feed_result_for_' . substr( sha1( $template ), 0, 6 );
}

function generate_html_by_template( $template, $limit ) {
    $collection = Models\FeedCollection::find_one_by_name( $template );
	$feeds      = $collection->feeds();

	$feed_items = array();
	foreach ( $feeds as $feed ) {
		$parsed = $feed->parse();
		$feed_items = array_merge( $feed_items, $parsed[ 'items' ] );
	}

	// order by publication date
	usort( $feed_items, function ( $a, $b ) {
	    if ( $a[ 'pubDateTime' ] == $b[ 'pubDateTime' ] ) {
	        return 0;
	    }
	    return ( $a[ 'pubDateTime' ] > $b[ 'pubDateTime' ] ) ? -1 : 1;
	} );
	
	if ( $limit > 0 ) {
		$feed_items = array_slice( $feed_items, 0, $limit );
	}
	
	$out = $collection->before_template;
	foreach ( $feed_items as $item ) {
		$out .= Parser\parse( $collection->body_template, $item );
	}
	$out .= $collection->after_template;
	
	return $out;
}
