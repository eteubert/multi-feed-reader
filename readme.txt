=== Multi Feed Reader ===
Contributors: eteubert
Donate link: https://flattr.com/thing/474620/WordPress-Plugin-Multi-Feed-Reader
Tags: feed, rss, archive, shortcode, custom, template, html, customizable
Requires at least: 3.0
Tested up to: 3.3
Stable tag: trunk

Reads multiple feeds. Output can be customized via templates. Is displayed via Shortcodes.

== Description ==

= Quick Start =

Create a template "myfeeds" in `Settings > Multi Feed Reader`.
Add your Feeds.
Create a page and paste in one of these shortocdes:

	[multi-feed-reader template="myfeeds"]
	[multi-feed-reader template="myfeeds" limit="10"]
	
Enjoy :)

= Placeholders =

You can specify a custom template to display the archive elements.
Go to `Settings > Multi Feed Reader` for plugin preferences.
Use HTML and any of the following template placeholders.

- `%TITLE%` - Episode title (&lt;title&gt;).
- `%SUBTITLE%` - Episode subtitle (&lt;itunes:subtitle&gt;).
- `%CONTENT%` - Episode content (&lt;content:encoded&gt;).
- `%DURATION%` - Episode duration (&lt;itunes:duration&gt;).
- `%SUMMARY%` - Episode summary (&lt;itunes:summary&gt;).
- `%LINK%` - Episode link (&lt;link&gt;).
- `%GUID%` - Episode Globally Unique Identifier (&lt;guid&gt;)
- `%DESCRIPTION%` - Episode description (&lt;itunes:description&gt;).
- `%ENCLOSURE%` - Url of first episode enclosure (&lt;enclosure&gt; url attribute).
- `%THUMBNAIL%` - Thumbnail tag in original size (&lt;itunes:image&gt; or first found image in &lt;content:encoded&gt;).
- `%THUMBNAIL|...x...%` - Same as above but with certain dimensions. Example: `%THUMBNAIL|75x75%`.
- `%DATE%` - Episode publish date (&lt;pubDate&gt;) in WordPress default format. 
- `%DATE|...%` - Same as above but in a custom format. Example: `%DATE|Y/m/d%`.

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

= Can I help to add a feature? =

That would be awesome!

Visit https://github.com/eteubert/multi-feed-reader, fork the project, add your feature and create a Pull Request. I'll be happy to review and add your changes.

== Screenshots ==

1. The Admin Interface
2. Example Archive

== Changelog ==

= 1.0 =
* Release

== Upgrade Notice ==

* nothing to watch out for, yet :)