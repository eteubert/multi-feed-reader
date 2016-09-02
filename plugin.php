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
	maybe_create_content_directories();
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
				'template'  => DEFAULT_TEMPLATE,
				'limit'     => DEFAULT_LIMIT,
				'nocache'   => isset( $_GET[ 'nocache' ] ),
				'cachetime' => DEFAULT_CACHETIME
			),
			$attributes
		)
	);

	$cache_key = get_cache_key( $template . $limit );
	$out = get_transient( $cache_key );

	if ( $nocache || ! $out )
		$out = generate_html_by_template( $template, $limit );

	set_transient( $cache_key, $out, $cachetime );

	return $out;
}

function get_cache_key( $template ) {
    return 'multi_feed_result_for_' . substr( sha1( $template ), 0, 6 );
}

function generate_html_by_template( $template, $limit ) {
	$timer = new Timer();

	$timer->start( 'fetch' );
    $collection = Models\FeedCollection::find_one_by_name( $template );
	if ( ! $collection ) {
		wp_die( "Whoops! The template <strong>" . $template . "</strong> does not exist :/" );
	}
	$feeds = $collection->feeds();
	$timer->stop( 'fetch' );

	$timer->start( 'parse' );
	$feed_items = array();
	$feed_data  = array();
	foreach ( $feeds as $feed ) {
		$parsed = $feed->parse();
		$feed_data[ $feed->id ] = $parsed[ 'feed' ];
		$feed_items = array_merge( $feed_items, $parsed[ 'items' ] );
	}
	$timer->stop( 'parse' );

	$timer->start( 'sort' );
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
	$timer->stop( 'sort' );
	
	$timer->start( 'render' );
	$out = $collection->before_template;
	foreach ( $feed_items as $item ) {
		$out .= Parser\parse( $collection->body_template, $item, $feed_data[ $item[ 'feed_id' ] ] );
	}
	$out .= $collection->after_template;
	$timer->stop( 'render' );

	$out = do_shortcode($out);

	write_log(
		sprintf(
			'template generated. fetch: %ss, parse: %ss, sort: %ss, render: %ss',
			$timer->get( 'fetch', 'range_human' ),
			$timer->get( 'parse', 'range_human' ),
			$timer->get( 'sort', 'range_human' ),
			$timer->get( 'render', 'range_human' )
		)
	);
	
	return $out;
}

function get_content_directory() {
	return apply_filters( 'mfr_content_directory', WP_CONTENT_DIR . '/multi-feed-reader' );
}

function get_log_directory() {
	return apply_filters( 'mfr_log_directory', get_content_directory() );
}

function get_cache_directory() {
	return apply_filters( 'mfr_cache_directory', get_content_directory() . '/cache' );
}

/**
 * Log a message to the logfile.
 * 
 * @param  string $message
 * @param  string $type    Message category. Default: INFO
 * @return void
 */
function write_log( $message, $type = 'INFO' ) {
	maybe_create_content_directories( 'log' );
	$file = get_log_directory() . '/reader.log';
	$log = "[$type] " . date( 'Y-m-d G:i:s' ) . " | $message\n";
	file_put_contents( $file, $log, FILE_APPEND | LOCK_EX );
}

/**
 * Creates directories necessary for the plugin to work.
 *
 * @param string $filter Default: all. Create only one directory.
 */
function maybe_create_content_directories( $filter = 'all' ) {
	$directories = array(
		'log'   => get_log_directory(),
		'cache' => get_cache_directory()
	);

	$directories = apply_filters( 'mfr_content_directories', $directories );

	$check_dir = function ( $dir ) {
		if ( is_dir( $dir ) )
			return;

		if ( ! mkdir( $dir, 0755, true ) )
			wp_die( 'MultiFeedReader: Can\'t create directory "' . $dir . '" :(' );
	};

	if ( $filter === 'all' ) {
		foreach ( $directories as $dir )
			$check_dir( $dir );
	} else {
		$check_dir( $directories[ $filter ] );
	}

}
