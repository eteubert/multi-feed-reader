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

require_once 'lib/settings.php';

function initialize() {
	$tabs = new Tabs;
	$tabs->set_tab( 'edit', \MultiFeedReader\t( 'Edit Templates' ) );
	$tabs->set_tab( 'add', \MultiFeedReader\t( 'Add Templates' ) );
	$tabs->set_default( 'edit' );
	?>
	<div class="wrap">

		<div id="icon-options-general" class="icon32"></div>
		<!-- <h2><?php echo \MultiFeedReader\t( 'Multi Feed Reader' ); ?></h2> -->
		<?php $tabs->display() ?>

		<div class="metabox-holder has-right-sidebar">

			<div class="inner-sidebar">

				<div class="postbox">
					<h3><span>Metabox 1</span></h3>
					<div class="inside">
						<p>Hi, I'm metabox 1!</p>
					</div>
				</div>

				<div class="postbox">
					<h3><span>Metabox 2</span></h3>
					<div class="inside">
						<p>Hi, I'm metabox 2!</p>
					</div>
				</div>

				<!-- ... more boxes ... -->

			</div> <!-- .inner-sidebar -->

			<div id="post-body">
				<div id="post-body-content">

					<div class="postbox">
						<h3><span>Metabox 3</span></h3>
						<div class="inside">
							<p>Hi, I'm metabox 3!</p>
						</div> <!-- .inside -->
					</div>

					<div class="postbox">
						<h3><span>Metabox 4</span></h3>
						<div class="inside">
							<p>Hi, I'm metabox 4!</p>
						</div> <!-- .inside -->
					</div>

				</div> <!-- #post-body-content -->
			</div> <!-- #post-body -->

		</div> <!-- .metabox-holder -->

	</div> <!-- .wrap -->
	<?php
}