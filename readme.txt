=== Ads EZ ===
Contributors: manojtd
Donate link: http://buy.thulasidas.com/ads-ez
Tags: ads, advertising, income, ad server, openx, revive, adserver
Requires at least: 4.0
Tested up to: 4.6
Stable tag: 3.10
License: GPL2 or later

Ads EZ is personal ad server with numerous features. It centralizes your banner ads in one location, and provides a modern interface to manage them.

== Description ==

*Ads EZ* is a clever program to serve your ads to multiple web sites. It can work as standalone web application or as a WordPress plugin. Using *Ads EZ*, you can centralize your banner ads in one location, and use simple shortcodes to place them on your blog pages, or generate invocation codes to place them on *any* other web sites or blogs. In the Pro version, you can even serve HTML ads with the same shortcode and invocation code mechanism.

> <strong>Sunset Edition</strong><br>
> This update of the plugin is the last publicly released version. We do not expect to make any serious changes or add new features to it from now on.
>
> We may not always be able to provide prompt support for this plugin on the WordPress.org forums. If you would like to have professional support or extra/custom features, consider buying the [Pro version](http://buy.thulasidas.com/ads-ez-pro "Get Ads EZ Pro for $15.95").

Do you have multiple websites which you monetize using advertising? If so, this personal ad server may be the right tool for you. *Ads EZ* makes it easy for you to set up an optimized ad server that can service your websites. Aiming at simplicity, Ads EZ does away with all the complicated zone and expiry settings. All you have to do is to collect your banners in a folder, upload them and specify their targets using a simple and intuitive interface.

> <strong>Live Demo</strong>
>
> Ads EZ admin interface is feature-rich, user-friendly and functional. Please visit this fully operational [live demo site](http://demo.thulasidas.com/ads-ez "Play with Ads EZ Admin Interface") to see what it can do for you.

 **Now available in your language using Google Translate.**

= Features =

* *Fully Automated Setup*: *Ads EZ* gets you started within two minutes, rather than hours and days.
* *Modern Admin Interface*: *Ads EZ* sports a modern and beautiful admin interface based on the twitter bootstrap framework. Fully responsive, with editable tables to set up and modify your ads.
* *Admin Interface Tour*: A slick tour will take you around the admin page and familiarize you with its features.
* *Generous Help for Admin*: Whenever you need help, the information and hint is only a click away in *Ads EZ*. (In fact, it is only a mouseover away.)
* *No Coding Required*: *Ads EZ* is written for creative people who have some ads in the form of banners or ad codes. So it doesn't call for any deep computing knowledge. The most you will have to do is perhaps to cut and paste some ad code.
* *Batch Processing*: *Ads EZ* makes it a cinch to get your existing banners into your database through an intuitive batch processing.
* *Memory Caching*: *Ads EZ* uses memory caching where available so that the database access is minimized. Caching can tremendously improve performance of busy sites.
* *Robust Security*: Unbreakable authentication (using hash and salt), impervious to SQL injection etc.
* *Data Validation*: Client-side and server-side data validation to minimize data errors.

*Ads EZ* is available as a freely distributed [lite version](http://buy.thulasidas.com/lite/ads-ez-lite.zip "Get Ads EZ Lite") and a [Pro version](http://buy.thulasidas.com/ads-ez-pro "Get Ads EZ Pro for $15.95"), which adds a ton of extra features.

= Pro Features =

If the following features are important to you, consider buying the *Pro* version.

* *Cache Visualization and Management*: You can see detailed cache statics in the *Pro* version, and options to set the cache expiry, and to manually clear it, if needed.
* *CDN Support*: Put your static files, such as your banners, on a Content Delivery Network (CDN) for improved performance.
* *In-Browser Banner Uploads*: The *Pro* version lets you upload multiple banners (through drag and drop) and enter their meta data. In the lite version, you would manually upload the banners to your server and run a batch to do it.
* *Statistics and Charts*: The *Pro* version can optionally collect statistics about your ad serving and present you with detailed performance reports and charts, including geolocation.
* *Category Management*: In the *Pro* version, you can have as many ad categories as you want. The lite version comes with three categories, and adding more would require direct database manipulation.
* *HTML Ads*: The *Pro* version allows you to store and serve HTML and JavaScript ads such as AdSense and other providers. The lite version is limited to banner ads.
* *OpenX Replacement*: Ads EZ is designed to be drop-in replacement for OpenX. In the *Pro*, you can generate the `.htaccess` directives that will make your Ads EZ app act like your OpenX server to the world.

== Upgrade Notice ==

Compatibility with WP4.6. Many accumulated fixes and changes. Releasing the sunset version.

== Screenshots ==

1. Dashboard, showing the beautifully designed admin interface.
2. Tour and Help, to quickly get started with Ads EZ.
3. Admin interface elements.
4. Listing your banners.
5. Generating invocation codes.
6. Configuration optons, showing help.
7. Editing categories.
8. Admin page tour sample screen, showing a dark theme.
9. Ad serving statistics and charts in the Pro version.
10. Advanced tools and Cache management in the Pro version.
11. OpenX/Revive replacement tools in the Pro version.
12. Drag-and-drop banners upload in the Pro version.

== Installation ==

The easiest way to install this plugin is to use the WordPress Admin interface. Go to your admin dashboard, find the "Plugins" menu, and click on "Add New". Find this plugin and click on "Install Now" and follow the WordPress instructions.

If you want to download it and manually install, you can again use the WordPress dashboard interface. First download the plugin zip file to your local computer. Then go to your admin dashboard, find the "Plugins" menu, and click on "Add New". After clicking on the "Add New" menu item as above, click on "Upload" (below the title "Install Plugins" near the top). Browse for your downloaded zip file, upload it and activate the plugin.

Further updates (and Pro upgrades and module installations) to the plugin can be done easily from the plugin admin page itself, by clicking on the Updates button on the top right corner.

= Standalone Mode =

1. Upload the contents of the archive `ads-ez` to your server.
2. Browse to the admin location of your uploaded the package (`http://yourserver/ads-ez/admin`, for example) using your web browser.
3. Enter the DB details and set up and Admin account in a couple of minutes and you are done with the installation!

Note that in the second step, your web server will try to create a configuration file where you uploaded the `ads-ez` package. If it cannot do that because of permission issues, you will have to create an empty file `dbCfg.php` and make it writeable. Don't worry, the setup will prompt you for it with detailed instructions.

= To get started with Ad Serving =

1. Upload your banners to the banners folder on your server and hit the Batch Process menu item to provide banner data.
2. Get the invocation codes and place them on your websites to start serving ads.
3. Take a tour to learn the program features.

== Frequently Asked Questions ==

= Why another Ad Server? =

I looked around for a light-weight ad serving solution for my own requirements, and could not find any. So I wrote one myself, as an exercise to learn bootstrap and other fancy modern web technology. I think you will like the result.

= Why would I want to replace my OpenX server? =

Well, I did, which is why I wrote this package. OpenX is a large application, and consequently quite demanding both in terms of the effort needed to maintain it, and on system resources. It may not be appropriate for small scale, personal ad serving, which needs to be simple and quick. If you have only a couple of hunded banners and don't plan on charging for your ads, *Ads EZ* may be the right solution for you. Having said that, I will develop a paid advertising module for *Ads EZ* if there is enough demand for it.

= Why do I get error message saying something about direct access to plugin files? =

This plugin admin interface is designed with a loosely coupled architecture, which means it interacts with the WordPress core only for certain essential services (login check, plugin activation status, database access etc). Loosely coupled systems tend to be more robust and flexible than tightly integrated ones because they make fewer assumptions about each other. My plugin admin pages are fairly independent, and do not pollute the global scope or leak the style directives or JavaScript functions. In order to achieve this, they are loaded in iFrames within the WordPress admin interface.

Your web server needs direct access to the plugin files to load anything in an iFrame. Some aggressive security settings block this kind of access, usually through an `.htaccess` file in your `wp-content` or `plugins` folders, which is why this plugin gives a corresponding error message if it detects inability to access the files (checked through a `file_get_contents` call on a plugin file URL). But some systems implement further blocks specifically on `file_get_contents` or on iFrames with specific styles (using `mod_securty` rules, for instance), which is why the plugin provides a means to override this auto-detection and force the admin page.

= Is the direct access to plugin files a security hole? =

Note that it is only your own webserver that needs direct access to the PHP files. The reason for preventing such access is that a hacker might be able to upload a malicious PHP (or other executable script) to your web host, which your webserver will run if asked to. Such a concern is valid only on systems where you explicitly permit unchecked file uploads. For instance, if anyone can upload any file to your media folder, and your media folder is not protected against direct access and script execution, you have given the potential hacker an attack vector.

In this plugin, its media/banner upload folder has a multiple layers protection:
1. Only users logged in as admin can ever see the upload interface.
2. The upload script accepts only media file types.
3. The backend AJAX handler also checks for safe file types.
4. The media storage locations are protected against script execution.

So allowing your webserver to serve the plugin admin files in an iFrame is completely safe, in my judgement.

== Change Log ==

= History =

* V3.10: Compatibility with WP4.6. Many accumulated fixes and changes. Releasing the sunset version. [Oct 12, 2016]
* V3.00: Compatibility with WP4.5. [Apr 12, 2016]
* V2.99: Improvements in the Google Translator interface. Compatibility with multisite installation of subdomain type. [Feb 27, 2016]
* V2.98: Minor interface and documentation changes. [Feb 25, 2016]
* V2.97: Changes in the DB interface to handle the case where native drivers are not installed. Restricting Google Translate not to translate user-editable strings. Optimizing screenshots. [Feb 7, 2016]
* V2.95: Adding diagnostic information on the update page. [Jan 20, 2015]
* V2.94: Changes to make the plugin compatible with multisite installations. Cookie-based long login option for standalone mode. Using APCu, if available. Other minor fixes. [Jan 14, 2016]
* V2.93: Minor fixes in standalone mode. [Jan 1, 2016]
* V2.92: Adding a dev-friendly include to introduce local settings, if any. Minor fixes in table rendering. [Dec 30, 2015]
* V2.91: Refactoring changes in admin footer rendering. [Dec 15, 2015]
* V2.90: Compatibility with WordPress 4.4. [Dec 5, 2015]
* V2.84: Making the admin menu dynamic (optionally) in standalone mode. Improving DB layer error handling. [Nov 29, 2015]
* V2.83: Warning about PHP V5.4 requirement on the admin page. [Nov 8, 2015]
* V2.82: Enforcing PHP V5.4 requirement at activation time. [Oct 26, 2015]
* V2.80: Admin pages in your language using Google translation. [Oct 22, 2015]
* V2.73: Updating screenshots. [Oct 20, 2015]
* V2.72: Improving the speed of admin page loading. [Sep 30, 2015]
* V2.71: Killing the option to allow update checks. [Sep 26, 2015]
* V2.70: Changes to make the plugin work on nginx and Microsoft servers. [Sep 20, 2015]
* V2.64: Minor bug fix. [Sep 17, 2015]
* V2.63: Ensuring usability on touch-screen devices. Removing an unused class and file. [Sep 17, 2015]
* V2.62: Reinstating the option to force the admin page loading, moving to a less colorful default theme. [Sep 14, 2015]
* V2.61: Fixing some refactoring errors related to the plugin end points. [Sep 13, 2015]
* V2.60: Adding better error handling on invalid requests, adding a link to the plugin admin page on WP plugins page, adding the ability to rerun the installer. [Sep 12, 2015]
* V2.51: Fixing a bug in the shortcode implementation. [Sep 8, 2015]
* V2.50: Removing WP core file loading and refactoring header and menus. [Sep 4, 2015]
* V2.43: More refactoring and cleanup changes. [Aug 31, 2015]
* V2.42: Code clean up and fixing some W3 validation errors. [Aug 23, 2015]
* V2.41: Fixing a minor error. [Aug 21, 2015]
* V2.40: Major refactoring of options handling, common functions etc. Compatibility with WP4.3. [Aug 10, 2015]
* V2.31: Refactoring setup modules. [Aug 5, 2015]
* V2.30: Implementing redirect to previous page upon login. Preparing for major refactoring. [Jul 25, 2015]
* V2.26: Tightening login security in standalone mode and hardening banners upload. [Jul 2, 2015]
* V2.25: Minor changes in documentation. [Jun 25, 2015]
* V2.24: Bug fix in the stats module. [Jun 15, 2015]
* V2.23: Bug fixes. [Jun 5, 2015]
* V2.22: Minor changes in the admin page. [Jun 4, 2015]
* V2.21: Admin page compatibility checks and improvements. [May 12, 2015]
* V2.20: Compatibility with WordPress 4.2. [April 25, 2015]
* V2.15: Misc. bug fixes. [April 23, 2-15]
* V2.14: Adding strip_slashes to html ads. [April 22, 2015]
* V2.13: Improvements in the admin dashboard. [April 15, 2015]
* V2.12: Improvements in the admin dashboard. [April 14, 2015]
* V2.11: Launching a demo site. [April 11, 2015]
* V2.10: More changes in the compatibility check. [April 7, 2015]
* V2.09: Fixing a style that may have caused the admin page not to appear on some blogs. [April 4, 2015]
* V2.08: More compatibility checks. [April 2, 2015]
* V2.07: Corrections to upload folder protection and HTML ads editing. [Mar 31, 2015]
* V2.06: Fixing a bug in banners batch processing, improving the charting module for more accurate display, and other minor changes. [Mar 27, 2015]
* V2.05: Adding error handling for updates. [Mar 24, 2015]
* V2.04: Compatibility check on the plugin admin page. [Mar 20, 2015]
* V2.03: Improvements in the login check functions. [Mar 9, 2015]
* V2.02: Code cleanup. [Mar 7, 2015]
* V2.01: Fixing some bugs specific to the plugin version. [Mar 6, 2015]
* V2.00: Adding WordPress plugin version. [Jan 2, 2015]
* V1.80: Major improvements to statistics and charting in the Pro version. [Dec 7, 2014]
* V1.71: Fixing the tour to handle accordion menus. [Nov 14, 2014]
* V1.70: Accordion menu implementation for a cleaner interface. [Nov 13, 2014]
* V1.61: Misc bug fixes. [Nov 11, 2014]
* V1.60: Sortable and paginated tables using dataTables. [Nov 11, 2014]
* V1.51: More misc cleanup and validation checks. [Nov 10, 2014]
* V1.50: Numerous misc changes. Ads EZ Pro is now feature-complete. [Nov 8, 2014]
* V1.40: Implemented stats, geolocation and charts. [Nov 6, 2014]
* V1.30: Implemented OpenX/Revive integration in the Pro version. [Nov 4, 2014]
* V1.20: Implemented new category creation in the Pro version. [Nov 1, 2014]
* V1.10: Implemented batch upload of banners and HTML Ads in the Pro version. [Oct 31, 2014]
* V1.00: Initial Public Release. [Oct 29, 2014]
