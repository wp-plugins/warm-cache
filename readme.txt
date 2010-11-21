=== Warm Cache ===
Contributors: ramon fincken
Tags: cache, warm, keep, xml, sitemap, load, speed, quick, tag, w3tc, optimize, page cache
Requires at least: 2.3
Tested up to: 3.0.1
Stable tag: 1.1

Crawls your website-pages based on google XML sitemap (google-sitemap-generator). If you have a caching plugin this wil keep your cache warm. Speeds up your site.

== Description ==

Crawls your website-pages based on google XML sitemap (google-sitemap-generator). If you have a caching plugin this wil keep your cache warm. 
Speeds up your site.<br>
All urls in your sitemap will be visited by the plugin to keep the cache up to date.<br>
Will show average page load times and pages visited.<br>

Needs google XML sitemap to read the generated XML file.<br>
Needs a cronjob (wget or curl) to call the plugin.<br>
* Coding by <a href="http://www.mijnpress.nl" title="MijnPress.nl WordPress ontwikkelaars">MijnPress.nl</a><br>
* Crawl script idea by <a href="http://blogs.tech-recipes.com/johnny/2006/09/17/handling-the-digg-effect-with-wordpress-caching/">http://blogs.tech-recipes.com/johnny/2006/09/17/handling-the-digg-effect-with-wordpress-caching/</a>

== Installation ==

1. Upload directory `mass_delete_tags` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Visit Plugins menu to mass delete your tags.

== Frequently Asked Questions ==

How to run a cronjob?
> Ask your webhost how to set up a get call using wget or curl

I have set up the cronjob but the stats table on the plugin page remains empty.
> If you have object caching such as W3 total cache, the statistics cannot be read.<br>
A help topic about this is placed <a href="http://wordpress.org/support/topic/plugin-w3-total-cache-strange-transient-problem?replies=1">on the support forums</a>.
Note that the script is crawling your XML file (check your webhosts access log), but you cannot see the statistics.



== Changelog ==

= 1.1 =
First release

== Screenshots ==

1. Details
<a href="http://s.wordpress.org/extend/plugins/warm-cache/screenshot-1.png">Fullscreen Screenshot 1</a><br>
2. Overview
<a href="http://s.wordpress.org/extend/plugins/warm-cache/screenshot-2.png">Fullscreen Screenshot 2</a><br>
