<?php
/**
 * The admin settings page logic.
 * 
 * Handles the settings pages accessible via the admin interface. The default
 * location should be "Settings > Name of the Plugin".
 */
namespace MultiFeedReader\Settings;
use MultiFeedReader\Models\FeedCollection as FeedCollection;
use MultiFeedReader\Models\Feed as Feed;

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

function process_forms() {
	// DELETE action
	if ( isset( $_POST[ 'delete' ] ) ) {
		$current = FeedCollection::current();
		if ( $current ) {
			$current->delete();
			// TODO delete sub feeds
		}
	// UPDATE action
	} elseif ( isset( $_POST[ 'feedcollection' ] ) ) {
		$current = FeedCollection::current();
		foreach ( FeedCollection::property_names() as $property ) {
			if ( isset( $_POST[ 'feedcollection' ][ $property ] ) ) {
				$current->$property = $_POST[ 'feedcollection' ][ $property ];
			}
		}
		$current->save();
		
		if ( isset( $_POST[ 'feedcollection' ][ 'feeds' ] ) ) {
			// update feeds
			foreach ( $_POST[ 'feedcollection' ][ 'feeds' ] as $feed_id => $feed_url ) {
				if ( ! is_numeric( $feed_id ) ) {
					continue;
				}
				$feed = Feed::find_by_id( $feed_id );
				if ( empty( $feed_url ) ) {
					$feed->delete();
				} else if ( $feed->url != $feed_url ) {
					$feed->url = $feed_url;
					$feed->save();
				}
			}
			
			// create feeds
			if ( isset( $_POST[ 'feedcollection' ][ 'feeds' ][ 'new' ] ) ) {
				foreach ( $_POST[ 'feedcollection' ][ 'feeds' ][ 'new' ] as $feed_url ) {
					$feed = new Feed();
					$feed->feed_collection_id = $current->id;
					$feed->url = $feed_url;
					$feed->save();
				}
			}
		}
        $current->delete_cache();
		
	// CREATE action
	} elseif ( isset( $_POST[ 'mfr_new_feedcollection_name' ] ) ) {
		$name = $_POST[ 'mfr_new_feedcollection_name' ];
		$existing = FeedCollection::find_one_by_name( $name );

		if ( ! $existing ) {
			$fc = new FeedCollection();
			$fc->name = $name;
			$fc->before_template = DEFAULT_BEFORE_TEMPLATE;
			$fc->body_template = DEFAULT_BODY_TEMPLATE;
			$fc->after_template = DEFAULT_AFTER_TEMPLATE;
			$fc->save();
			
			wp_redirect(
				admin_url(
					'options-general.php?page=' . $_REQUEST[ 'page' ]
					. '&choose_template_id=' . $fc->id
				)
			);
			exit;
		} else {
			wp_redirect(
				admin_url(
					'options-general.php?page=' . $_REQUEST[ 'page' ]
					. '&tab=add'
					. '&message=fc_exists'
				)
			);
			exit;
		}
	}
}
add_action( 'admin_init', 'MultiFeedReader\Settings\process_forms' );

/**
 * @todo the whole template can probably be abstracted away
 * 
 * something like
 *   $settings_page = new TwoColumnSettingsPage()
 *   $tabs = new \MultiFeedReader\Tabs;
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
	$tabs = new \MultiFeedReader\Tabs;
	$tabs->set_tab( 'edit', __( 'Edit Feedcollection', 'multi-feed-reader' ) );
	$tabs->set_tab( 'add', __( 'Add Feedcollection', 'multi-feed-reader' ) );
	$tabs->set_default( 'edit' );
	
	if ( ! FeedCollection::has_entries() ) {
		$tabs->enforce_tab( 'add' );
	}
	?>
	<div class="wrap">

		<div id="icon-options-general" class="icon32"></div>
		<?php $tabs->display() ?>
		
		<?php if ( ! empty( $_REQUEST[ 'message' ] ) ): ?>
			<div id="message" class="updated">
				<p>
					<?php
					switch ( $_REQUEST[ 'message' ] ) {
						case 'fc_exists':
							_e( 'Feedcollection already exists. Please choose another name.' );
							break;
					}
					?>
				</p>
			</div>
		<?php endif; ?>
		

		<div class="metabox-holder has-right-sidebar">

			<div class="inner-sidebar">

				<?php display_creator_metabox(); ?>
                <?php display_help_metabox( $tabs ); ?>
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
	postbox( __( 'Creator', 'multi-feed-reader' ), function () {
		?>
		<p>
			<?php echo __( 'Hey, I\'m Eric. I created this plugin.<br/> If you like it, consider to flattr me a beer.', 'multi-feed-reader' ) ?>
		</p>
		<script type="text/javascript">
		/* <![CDATA[ */
		    (function() {
		        var s = document.createElement('script'), t = document.getElementsByTagName('script')[0];
		        s.type = 'text/javascript';
		        s.async = true;
		        s.src = 'http://api.flattr.com/js/0.6/load.js?mode=auto';
		        t.parentNode.insertBefore(s, t);
		    })();
		/* ]]> */</script>
		<a class="FlattrButton" style="display:none;" rev="flattr;button:compact;" href="http://wordpress.org/extend/plugins/multi-feed-reader/"></a>
		<noscript><a href="http://flattr.com/thing/474620/WordPress-Plugin-Multi-Feed-Reader" target="_blank">
		<img src="http://api.flattr.com/button/flattr-badge-large.png" alt="Flattr this" title="Flattr this" border="0" /></a></noscript>
		<p>
			<?php echo wp_sprintf( __( 'Get in touch: Visit my <a href="%1s">Homepage</a>, follow me on <a href="%2s">Twitter</a> or look at my projects on <a href="%3s">GitHub</a>.', 'multi-feed-reader' ), 'http://www.ericteubert.de', 'http://www.twitter.com/ericteubert', 'https://github.com/eteubert', 'multi-feed-reader' ) ?>
		</p>
		<?php
	});
}

function display_help_metabox( $tabs ) {
	if ( $tabs->get_current_tab() == 'edit' && $c = FeedCollection::current() ) {
		$value = '[' . \MultiFeedReader\SHORTCODE . ' template=&quot;' . $c->name . '&quot;';
		postbox( __( 'Usage', 'multi-feed-reader' ), function () use ( $value ) {
			?>
			<p>
				<?php
				echo __( 'Use this shortcode in any post or page:', 'multi-feed-reader' );
				?>
				<input type="text" class="large-text" value="<?php echo $value . ']'; ?>" />
			</p>
			<p>
				<?php
				echo __( 'You can limit the amount of posts displayed:', 'multi-feed-reader' );
				?>
				<input type="text" class="large-text" value="<?php echo $value . ' limit=&quot;5&quot;]'; ?>" />				
			</p>
			<?php
		});
	}
	
    postbox( __( 'Placeholders', 'multi-feed-reader' ), function () {
		?>
        <style type="text/css" media="screen">
    		.inline-pre pre {
    			display: inline !important;
    		}
    	</style>
    	<div class="inline-pre">
			<strong><?php echo __( 'Feed item data', 'multi-feed-reader' ); ?></strong>
			<p>
           		<pre>%TITLE%</pre><br/><?php echo __( 'Episode title (&lt;title&gt;).', 'multi-feed-reader' ); ?><br/><br/>
           		<pre>%SUBTITLE%</pre><br/><?php echo __( 'Episode subtitle (&lt;itunes:subtitle&gt;).', 'multi-feed-reader' ); ?><br/><br/>
           		<pre>%CONTENT%</pre><br/><?php echo __( 'Episode content (&lt;content:encoded&gt;).', 'multi-feed-reader' ); ?><br/><br/>
           		<pre>%CONTENT|...%</pre><br/><?php echo __( 'Same as above but truncated to the given amount of words.', 'multi-feed-reader' ); ?><br/><br/>
           		<pre>%DURATION%</pre><br/><?php echo __( 'Episode duration (&lt;itunes:duration&gt;).', 'multi-feed-reader' ); ?><br/><br/>
           		<pre>%SUMMARY%</pre><br/><?php echo __( 'Episode summary (&lt;itunes:summary&gt;).', 'multi-feed-reader' ); ?><br/><br/>
           		<pre>%LINK%</pre><br/><?php echo __( 'Episode link (&lt;link&gt;).', 'multi-feed-reader' ); ?><br/><br/>
           		<pre>%GUID%</pre><br/><?php echo __( 'Episode Globally Unique Identifier (&lt;guid&gt;)', 'multi-feed-reader' ); ?><br/><br/>
           		<pre>%DESCRIPTION%</pre><br/><?php echo __( 'Episode description (&lt;itunes:description&gt;).', 'multi-feed-reader' ); ?><br/><br/>
           		<pre>%DESCRIPTION|...%</pre><br/><?php echo __( 'Same as above but truncated to the given amount of words.', 'multi-feed-reader' ); ?><br/><br/>
           		<pre>%ENCLOSURE%</pre><br/><?php echo __( 'Url of first episode enclosure (&lt;enclosure&gt; url attribute).', 'multi-feed-reader' ); ?><br/><br/>
            	<pre>%THUMBNAIL%</pre><br/><?php echo __( 'Thumbnail tag in original size (&lt;itunes:image&gt;).', 'multi-feed-reader' ); ?><br/><br/>
            	<pre>%THUMBNAIL|...x...%</pre><br/><?php echo __( 'Same as above but with certain dimensions. Example: <pre>%THUMBNAIL|75x75%</pre>.', 'multi-feed-reader' ); ?><br/><br/>
            	<pre>%DATE%</pre><br/><?php echo __( 'Episode publish date (&lt;pubDate&gt;) in WordPress default format. ', 'multi-feed-reader' ); ?><br/><br/>
            	<pre>%DATE|...%</pre><br/><?php echo __( 'Same as above but in a custom format. Example: <pre>%DATE|Y/m/d%</pre>.', 'multi-feed-reader' ); ?><br/><br/>
			</p>
			<strong><?php echo __( 'App Store data', 'multi-feed-reader' ); ?></strong>
			<p>
				<pre>%APPNAME%</pre><br/><?php echo __( 'App name.', 'multi-feed-reader' ); ?><br/><br/>
				<pre>%APPPRICE%</pre><br/><?php echo __( 'App price.', 'multi-feed-reader' ); ?><br/><br/>
				<pre>%APPIMAGE%</pre><br/><?php echo __( 'App Icon as HTML image.', 'multi-feed-reader' ); ?><br/><br/>
				<pre>%APPARTIST%</pre><br/><?php echo __( 'App artist / publisher.', 'multi-feed-reader' ); ?><br/><br/>
				<pre>%APPRELEASE%</pre><br/><?php echo __( 'App release date in WordPress format.', 'multi-feed-reader' ); ?><br/><br/>
			</p>
			<strong><?php echo __( 'Global feed data', 'multi-feed-reader' ); ?></strong>
			<p>
				<pre>%FEEDTITLE%</pre><br/><?php echo __( 'Feed title (&lt;title&gt;).', 'multi-feed-reader' ); ?><br/><br/>
				<pre>%FEEDSUBTITLE%</pre><br/><?php echo __( 'Feed subtitle (&lt;itunes:subtitle&gt;).', 'multi-feed-reader' ); ?><br/><br/>
				<pre>%FEEDSUMMARY%</pre><br/><?php echo __( 'Feed summary (&lt;itunes:summary&gt;).', 'multi-feed-reader' ); ?><br/><br/>
				<pre>%FEEDLINK%</pre><br/><?php echo __( 'Feed link (&lt;link&gt;).', 'multi-feed-reader' ); ?><br/><br/>
				<pre>%FEEDLANGUAGE%</pre><br/><?php echo __( 'Feed language (&lt;language&gt;).', 'multi-feed-reader' ); ?><br/><br/>
				<pre>%FEEDTHUMBNAIL%</pre><br/><?php echo __( 'Feed image (&lt;itunes:image&gt;).', 'multi-feed-reader' ); ?><br/><br/>
				<pre>%FEEDTHUMBNAIL|...x...%</pre><br/><?php echo __( 'Same as above but with certain dimensions. Example: <pre>%FEEDTHUMBNAIL|75x75%</pre>.', 'multi-feed-reader' ); ?><br/><br/>
				
			</p>
    	</div>
		<?php
	});
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
	if ( FeedCollection::count() > 1 ) {
		postbox( __( 'Choose Template', 'multi-feed-reader' ), function () {
			$all = FeedCollection::all();
			?>
			<form action="<?php echo admin_url( 'options-general.php' ) ?>" method="get">
				<input type="hidden" name="page" value="<?php echo HANDLE ?>">

				<script type="text/javascript" charset="utf-8">
					jQuery( document ).ready( function() {
						// hide button only if js is enabled
						jQuery( '#choose_template_button' ).hide();
						// if js is enabled, auto-submit form on change
						jQuery( '#choose_template_id' ).change( function() {
							this.form.submit();
						} );
					});
				</script>

				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<?php echo __( 'Template to Edit', 'multi-feed-reader' ) ?>
							</th>
							<td>
								<select name="choose_template_id" id="choose_template_id" style="width:99%">
									<?php $selected_choose_template_id = isset( $_REQUEST[ 'choose_template_id' ] ) ? $_REQUEST[ 'choose_template_id' ] : 0; ?>
									<?php foreach ( $all as $c ): ?>
										<?php $selected = ( $selected_choose_template_id == $c->id ) ? 'selected="selected"' : ''; ?>
										<option value="<?php echo $c->id ?>" <?php echo $selected ?>><?php echo $c->name ?></option>
									<?php endforeach ?>
								</select>
							</td>
						</tr>
					</tbody>
				</table>

				<p class="submit" id="choose_template_button">
					<input type="submit" class="button-primary" value="<?php echo __( 'Choose Template', 'multi-feed-reader' ) ?>" />
				</p>

				<br class="clear" />

			</form>
			<?php
		});
	}
	
	postbox( wp_sprintf( __( 'Settings for "%1s" Collection', 'multi-feed-reader' ), FeedCollection::current()->name ), function () {
		$current = FeedCollection::current();
		$feeds = $current->feeds();
		?>
		<script type="text/javascript" charset="utf-8">
		jQuery( document ).ready( function( $ ) {
			$("#feed_form .add_feed").click(function(e) {
				e.preventDefault();
				
				var input_html = '<input type="text" name="feedcollection[feeds][new][]" value="" class="large-text" />';
				$(input_html).insertAfter("#feed_form input:last");
				
				return false;
			});
		});
		</script>
		
		<form action="<?php echo admin_url( 'options-general.php?page=' . HANDLE ) ?>" method="post">
			<input type="hidden" name="choose_template_id" value="<?php echo $current->id ?>">
			
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row">
							<h4><?php echo __( 'Feeds', 'multi-feed-reader' ) ?></h4>
						</th>
						<td scope="row" id="feed_form">
							<?php if ( $feeds ): ?>
								<?php foreach ( $feeds as $feed ): ?>
									<input type="text" name="feedcollection[feeds][<?php echo $feed->id ?>]" value="<?php echo $feed->url; ?>" class="large-text" />
								<?php endforeach; ?>
							<?php else: ?>
								<input type="text" name="feedcollection[feeds][new][]" value="" class="large-text" />
							<?php endif; ?>
							<a href="#" class="add_feed">Add Feed</a>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" colspan="2">
							<h4><?php echo __( 'Template Options', 'multi-feed-reader' ) ?></h4>
						</th>
					</tr>
					<tr valign="top">
						<th scope="row">
							<?php echo __( 'Template Name', 'multi-feed-reader' ) ?>
						</th>
						<td>
							<input type="text" name="feedcollection[name]" value="<?php echo $current->name ?>" class="large-text">
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<?php echo __( 'Before Template', 'multi-feed-reader' ) ?>
						</th>
						<td>
							<textarea name="feedcollection[before_template]" rows="10" class="large-text"><?php echo $current->before_template ?></textarea>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<?php echo __( 'Body Template', 'multi-feed-reader' ) ?>
						</th>
						<td>
							<textarea name="feedcollection[body_template]" rows="10" class="large-text"><?php echo $current->body_template ?></textarea>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<?php echo __( 'After Template', 'multi-feed-reader' ) ?>
						</th>
						<td>
							<textarea name="feedcollection[after_template]" rows="10" class="large-text"><?php echo $current->after_template ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
			
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" style="float:right" />
				<input type="submit" class="button-secondary" style="color:#BC0B0B; margin-right:20px; float: right" name="delete" value="<?php echo __( 'delete permanently', 'multi-feed-reader' ) ?>">
			</p>
			
			<br class="clear" />
		</form>
		<?php
	});

}

function display_add_page() {
	postbox( __( 'Add Feedcollection', 'multi-feed-reader' ), function () {
		?>
		<form action="" method="post">

			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<?php echo __( 'New Feedcollection Name', 'multi-feed-reader' ); ?>
						</th>
						<td>
							<input type="text" name="mfr_new_feedcollection_name" value="" id="mfr_new_feedcollection_name" class="large-text">
							<p>
								<small><?php echo __( 'This name will be used in the shortcode to identify the feedcollection.<br/>Example: If you name the collection "rockstar", then you can use it with the shortcode <em>[multi-feed-reader template="rockstar"]</em>', 'multi-feed-reader' ); ?></small>
							</p>
						</td>
					</tr>
				</tbody>
			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php echo __( 'Add New Feedcollection', 'multi-feed-reader' ); ?>" />
			</p>
			
			<br class="clear" />
			
		</form>
		<?php
	} );
}