=== Multi Feed Reader ===
Contributors: eteubert
Donate link: https://flattr.com/thing/474620/WordPress-Plugin-Multi-Feed-Reader
Tags: feed, rss, archive, shortcode, custom, template, html, customizable
Requires at least: 3.0
Tested up to: 3.7.5
Stable tag: trunk

Reads multiple feeds. Output can be customized via templates. Is displayed via Shortcodes.

== Description ==

This plugin was created with the iTunes Podcast Feed in mind. However, you can feed it any RSS feed you like. If you would like to display some tags which are not supported right now, please feel free to contact me.

= Quick Start =

Create a template "myfeeds" in `Settings > Multi Feed Reader`.
Add your Feeds.
Create a page and paste in one of these shortocdes:

	[multi-feed-reader template="myfeeds"]
	[multi-feed-reader template="myfeeds" limit="10"]
	[multi-feed-reader template="myfeeds" cachetime="300"]
	
Enjoy :)

= Parameters =

- `template`: (required) name of the template
- `limit`: (optional) maximum number of items per feed. default: 15
- `cachetime`: (optional) time in seconds to cache results. default: 300
- `nocache`: (optional) set to "1" to deactivate cache. not recommended, will make your multifeed-page very slow. default: 0

= Force Cache Refresh =

To clear the cache, call the site with parameter `?nocache=1`. So if your site is `example.com/archives`, open `example.com/archives?nocache=1` in your browser. You should then see the refreshed page immediately.

= Placeholders =

You can specify a custom template to display the archive elements.
Go to `Settings > Multi Feed Reader` for plugin preferences.
Use HTML and any of the following template placeholders.

- `%TITLE%` - Episode title (&lt;title&gt;).
- `%SUBTITLE%` - Episode subtitle (&lt;itunes:subtitle&gt;).
- `%CONTENT%` - Episode content (&lt;content:encoded&gt;).
- `%CONTENT|...%` - Same as above but truncated to the given amount of words.
- `%DURATION%` - Episode duration (&lt;itunes:duration&gt;).
- `%SUMMARY%` - Episode summary (&lt;itunes:summary&gt;).
- `%LINK%` - Episode link (&lt;link&gt;).
- `%GUID%` - Episode Globally Unique Identifier (&lt;guid&gt;)
- `%DESCRIPTION%` - Episode description (&lt;itunes:description&gt; or &lt;description&gt;).
- `%DESCRIPTION|...%` - Same as above but truncated to the given amount of words.
- `%ENCLOSURE%` - Url of first episode enclosure (&lt;enclosure&gt; url attribute).
- `%THUMBNAIL%` - Thumbnail tag in original size (&lt;itunes:image&gt;).
- `%THUMBNAIL|...x...%` - Same as above but with certain dimensions. Example: `%THUMBNAIL|75x75%`.
- `%DATE%` - Episode publish date (&lt;pubDate&gt;) in WordPress default format. 
- `%DATE|...%` - Same as above but in a custom format. Example: `%DATE|Y/m/d%`.

Access data from app store feeds:

- %APPNAME% - App name.
- %APPPRICE% - App price.
- %APPIMAGE% - App Icon as HTML image.
- %APPARTIST% - App artist / publisher.
- %APPRELEASE% - App release date in WordPress format.

Use these placeholders to access global feed data:

- `%FEEDTITLE%` - Feed title (&lt;title&gt;).
- `%FEEDSUBTITLE%` - Feed subtitle (&lt;itunes:subtitle&gt;).
- `%FEEDSUMMARY%` - Feed summary (&lt;itunes:summary&gt;).
- `%FEEDLINK%` - Feed link (&lt;link&gt;).
- `%FEEDLANGUAGE%` - Feed language (&lt;language&gt;).
- `%FEEDTHUMBNAIL%` - Feed image (&lt;itunes:image&gt;).
- `%FEEDTHUMBNAIL|...x...%` - Same as above but with certain dimensions. Example: `%FEEDTHUMBNAIL|75x75%`.

== Installation ==

1. Upload the `multi-feed-reader` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to `Settings > Multi Feed Reader` and create a template
1. Place `[multi-feed-reader template="mytemplate"]` in your post or page

== Frequently Asked Questions ==

= W00t, it says I need PHP 5.3?! =

PHP 5.3 is available since June 2009.
It introduced some long overdue features to the language and I refuse to support legacy junk.
Please ask your hoster to update, kindly.

= How many feeds can I add to a collection? =

That depends on two aspects: Feed response time and feed size. Each feed you add increases the time needed to display the final page. The results are cached, so most of the time the page will render in a snap. If you'd like to see how long the page rendering takes when the cache is expired, have a look at the logfile (/wp-content/multi-feed-reader/reader.log). Look for the last line containing "template generated". It contains the durations.

Rule of thumb: On average, one feed requires one second. 5 Feeds are fine. 10 might work. 20 is probably too much. Your mileage may vary :)

= Can I help to add a feature? =

That would be awesome!

Visit https://github.com/eteubert/multi-feed-reader, fork the project, add your feature and create a Pull Request. I'll be happy to review and add your changes.

== Screenshots ==

1. The Admin Interface
2. Example Archive

== Changelog ==

= 2.2.4 =

* security: fix SQL injection (thanks to JPCERT)

= 2.2.3 =

* fix error (use of deprecated function `mysql_insert_id()`)

= 2.2.2 =
* apparently having a settings.php in your plugin breaks the network pages. Yay WordPress. *slow clap*

= 2.2.1 =
* fix issue for windows based servers

= 2.2.0 =
* new placeholder: `%CONTENT|...%`
* add documentation for all parameters
* cache duration is configurable via `cachetime` parameter
* minor fixes / enhancements

= 2.1.3 =
* class loading fix

= 2.1.2 =
* compatibility fix

= 2.1.1 =
* if there are class attributes the description html, they will now correctly be displayed

= 2.1.0 =
* add support for some iTunes App Store tags (name, price, icon, releasedate). Please see readme for usage.
* bugfix concerning description truncating
* internal translation API changes

= 2.0.0 =
* finally supports a wide variety of feeds
* logs feed fetching info to a log file

= 1.1.3 =
* add `DESCRIPTION|...` placeholder
* enhanced description parsing

= 1.1.2 =
* enhance feed parser robustness

= 1.1.1 =
* Bugfix: Shortcode prints template in the correct place now

= 1.1 =
* `%THUMBNAIL%`: Use &lt;itunes:image&gt; if available. Otherwise, look for the first suitable &lt;img&gt; in &lt;content:encoded&gt;
* add support for global feed data: `%FEEDTITLE%`, `%FEEDSUBTITLE%`, `%FEEDSUMMARY%`, `%FEEDLINK%`, `%FEEDLANGUAGE%`, `%FEEDTHUMBNAIL%`, `%FEEDTHUMBNAIL|...x...%`

= 1.0 =
* Release

== Upgrade Notice ==

* nothing to watch out for, yet :)
