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
 * Postbox helper function.
 * 
 * @param string $name
 * @param function $content
 */
function postbox( $name, $content ) {
	?>
	<div class="postbox">
		<h3><span><?php echo $name; ?></span></h3>
		<div class="inside">
			<?php call_user_func( $content ); ?>
		</div> <!-- .inside -->
	</div>
	<?php
}

/**
 * @todo the whole template can probably be abstracted away
 * @todo reduce set_tab() to only receive the name and auto-deduce id from name
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
	if ( isset( $_POST[ 'mfr_new_feedcollection_name' ] ) ) {
		$id = $_POST[ 'mfr_new_feedcollection_name' ];
		$collection = FeedCollection::create_by_id( $id );
		if ( ! $collection ) {
			?>
			<div class="error">
				<p>
					<?php echo wp_sprintf( \MultiFeedReader\t( 'Feedcollection "%1s" already exists.' ), $id ) ?>
				</p>
			</div>
			<?php
		}
	}
	
	$tabs = new \MultiFeedReader\Lib\Tabs;
	$tabs->set_tab( 'edit', \MultiFeedReader\t( 'Edit Feedcollection' ) );
	$tabs->set_tab( 'add', \MultiFeedReader\t( 'Add Feedcollection' ) );
	$tabs->set_default( 'edit' );
	
	if ( ! FeedCollection::has_entries() ) {
		$tabs->enforce_tab( 'add' );
	}
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
	postbox( \MultiFeedReader\t( 'Creator' ), function () {
		?>
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
		<?php
	});
}

class FeedCollection
{
	/**
	 * Dictionary of all FeedCollection objects
	 */
	static $feed_collections = NULL;
	
	/**
	 * Reference to the collection itself in self::$feed_collections
	 */
	private $self;
	
	/**
	 * The id of the FeedCollection identifying the collection
	 */
	private $id = NULL;
	
	/**
	 * A list of feed URLs
	 */
	private $feeds = array();
	
	private function __construct( $id ) {
		$this->id = $id;
	}
	
	/**
	 * Find single FeedCollection by id.
	 * 
	 * @return false if not found, else the FeedCollection.
	 */
	public static function find_by_id( $id ) {
		self::load_all();
		$collection = self::$feed_collections[ $id ];
		
		if ( $collection ) {
			return $collection;
		} else {
			return false;
		}
	}
	
	/**
	 * Create single FeedCollection by id.
	 * 
	 * @return false if exists already, else the FeedCollection.
	 */
	public static function create_by_id( $id ) {
		self::load_all();
		$collection = self::$feed_collections[ $id ];
		
		if ( $collection ) {
			return false; // exists already
		} else {
			self::$feed_collections[ $id ] = new FeedCollection( $id );
			self::save_all();
			return self::$feed_collections[ $id ];
		}
	}
	
	public function add_feed( $url ) {
		$this->self->feeds[] = $url;
		self::save_all();
	}
	
	public static function has_entries() {
		self::load_all();
		return is_array( self::$feed_collections ) && count( self::$feed_collections ) > 0;
	}
	
	public static function count() {
		self::load_all();
		return count( self::$feed_collections );
	}
	
	public static function get_ids() {
		return array_keys( self::$feed_collections );
	}
	
	/**
	 * Load all FeedCollections, but only if they have not been loaded yet.
	 */
	private static function load_all() {
		if ( self::$feed_collections !== NULL ) {
			return true;
		}

		self::$feed_collections = get_option( 'feed_collections' );
	}
	
	/**
	 * Save all data to the database
	 */
	private static function save_all() {
		update_option( 'feed_collections', self::$feed_collections );
	}

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
	if ( FeedCollection::count() === 1 ) {
		$ids = FeedCollection::get_ids();
		$id = $ids[0];
		$collection = FeedCollection::find_by_id( $id );
		
		postbox( $id, function () {
			$ids = FeedCollection::get_ids();
			$id = $ids[0];
			$collection = FeedCollection::find_by_id( $id );
			
			echo "<pre>";
			var_dump($collection);
			echo "</pre>";
		});
		
	} else {
		postbox( \MultiFeedReader\t( 'Not Yet Implemented' ), function () {
			?>
			<p>Sorry, I don't know how to handle multiple thingies :/</p>
			<?php
		});
	}

}

function display_add_page() {
	postbox( \MultiFeedReader\t( 'Add Feedcollection' ), function () {
		?>
		<form action="" method="post">

			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<?php echo \MultiFeedReader\t( 'New Feedcollection Name' ) ?>
						</th>
						<td>
							<input type="text" name="mfr_new_feedcollection_name" value="" id="mfr_new_feedcollection_name" class="large-text">
							<p>
								<small><?php echo \MultiFeedReader\t( 'This name will be used in the shortcode to identify the feedcollection.<br/>Example: If you name the collection "rockstar", then you can use it with the shortcode <em>[multi-feed-reader template="rockstar"]</em>' ) ?></small>
							</p>
						</td>
					</tr>
				</tbody>
			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php echo \MultiFeedReader\t( 'Add New Feedcollection' ) ?>" />
			</p>
			
			<br class="clear" />
			
		</form>
		<?php
	});
}