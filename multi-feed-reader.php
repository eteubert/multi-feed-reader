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

function add_menu_entry() {
	add_submenu_page( 'options-general.php', 'Multi Feed Reader', 'Multi Feed Reader', 'manage_options', \MultiFeedReader\Settings\HANDLE, 'MultiFeedReader\Settings\initialize' );
}

namespace MultiFeedReader\Settings;

const HANDLE = 'multi_feed_reader_handle';

/**
 * @todo move in a bootstrapping file
 */
// autoload all files in /lib
spl_autoload_register( function ( $class_name ) { 
	// get class name without namespace
	$class_name = strtolower( array_pop( explode( '\\', $class_name ) ) );
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

function initialize() {
	$tabs = new \MultiFeedReader\Lib\Tabs;
	$tabs->set_tab( 'edit', \MultiFeedReader\t( 'Edit Templates' ) );
	$tabs->set_tab( 'add', \MultiFeedReader\t( 'Add Templates' ) );
	$tabs->set_default( 'edit' );
	?>
	<div class="wrap">

		<div id="icon-options-general" class="icon32"></div>
		<?php $tabs->display() ?>

		<div class="metabox-holder has-right-sidebar">

			<div class="inner-sidebar">

				<?php display_creator_metabox(); ?>

				<!-- ... more boxes ... -->

			</div> <!-- .inner-sidebar -->

			<div id="post-body">
				<div id="post-body-content">
					<?php
					switch ( $tabs->get_current_tab() ) {
						case 'edit':
							display_edit_page();
							break;
						case 'add':
							display_add_page();
							break;
						default:
							die( 'Whoops! The tab "' . $tabs->get_current_tab() . '" does not exist.' );
							break;
					}
					?>
				</div> <!-- #post-body-content -->
			</div> <!-- #post-body -->

		</div> <!-- .metabox-holder -->

	</div> <!-- .wrap -->
	<?php
}

/**
 * @todo this should be a template/partial
 */
function display_creator_metabox() {
	?>
	<div class="postbox">
		<h3><span><?php echo \MultiFeedReader\t( 'Creator' ) ?></span></h3>
		<div class="inside">
			<p>
				<?php echo \MultiFeedReader\t( 'Hey, I\'m Eric. I created this plugin.<br/> If you like it, consider to flattr me a beer.' ) ?>
			</p>
			<?php
			/**
			 * @todo add flattr button
			 */
			?>
			<p>
				<?php echo wp_sprintf( \MultiFeedReader\t( 'Get in touch: Visit my <a href="%1s">Homepage</a>, follow me on <a href="%2s">Twitter</a> or look at my projects on <a href="%3s">GitHub</a>.' ), 'http://www.FarBeyondProgramming.com/', 'http://www.twitter.com/ericteubert', 'https://github.com/eteubert' ) ?>
			</p>
		</div>
	</div>
	<?php
}

/**
 * @todo determine directory / namespace structure for settings pages
 * 
 * \MFR\Settings\Pages\AddTemplate
 * \MFR\Settings\Pages\EditTemplate
 * manual labour to include all the files. or ... autoload.
 * 
 */

function display_edit_page() {
	?>
	<div class="postbox">
		<h3><span><?php echo \MultiFeedReader\t( 'Edit Template' ) ?></span></h3>
		<div class="inside">
			<p>Hi, I'm the edit metabox!</p>
		</div> <!-- .inside -->
	</div>	
	<?php
}

function display_add_page() {
	?>
	<div class="postbox">
		<h3><span><?php echo \MultiFeedReader\t( 'Add Template' ) ?></span></h3>
		<div class="inside">
			<p>Hi, I'm the add metabox!</p>
		</div> <!-- .inside -->
	</div>	
	<?php
}