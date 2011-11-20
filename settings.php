<?php
/**
 * The admin settings page logic.
 * 
 * Handles the settings pages accessible via the admin interface. The default
 * location should be "Settings > Name of the Plugin".
 */
namespace MultiFeedReader\Settings;

const HANDLE = 'multi_feed_reader_handle';

/**
 * @todo the whole template can probably be abstracted away
 * 
 * something like
 *   $settings_page = new TwoColumnSettingsPage()
 *   $tabs = new \MultiFeedReader\Lib\Tabs;
 *   // configure tabs ...
 *   $settings_page->add_tabs( $tabs );
 * 
 *   - display of content naming-convention based
 *   - needs a flexible soution for sidebar, though; first step might be to
 *     redefine sidebar for each tab separately
 *   - bonus abstraction: intelligently display page based on whether there
 *     are tabs or not
 *   - next bonus abstraction: Also implement SingleColumnSettingsPage() and
 *     have some kind of interface to plug different page classes
 */
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