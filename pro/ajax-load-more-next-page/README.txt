=== Ajax Load More: Next Page ===

Contributors: dcooney
Author: Darren Cooney
Author URI: https://connekthq.com/
Plugin URI: https://connekthq.com/ajax-load-more/add-ons/next-page/
Requires at least: 4.0
Tested up to: 6.5
Stable tag: trunk
Homepage: https://connekthq.com/ajax-load-more/
Version: 1.8.0

== Copyright ==
Copyright 2024 Connekt Media

This software is NOT to be distributed, but can be INCLUDED in WP themes: Premium or Contracted.
This software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

== Description ==

The Next Page add-on will provide functionality for infinite scrolling and lazy loading paginated posts and pages.
Load and display multipage WordPress content on demand using the <!–-nextpage–-> Quicktag and Ajax Load More.

The Next Page add-on for Ajax Load More works by using <!-‒nextpage‒-> Quicktags to split the post_content of the current page (or post) into separate pages – as users load paginated content, the browser address bar updates to the page currently in view and pageviews can be sent to Google Analytics for tracking.

== Installation ==

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `ajax-load-more-next-page.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `ajax-load-more-next-page.zip`.
2. Extract the `ajax-load-more-next-page` directory to your computer.
3. Upload the `ajax-load-more-next-page` directory to the `/wp-content/plugins/` directory.
4. Ensure Ajax Load More is installed prior to activating the plugin.
5. Activate the plugin in the Plugin dashboard.

== Changelog ==

= 1.8.0 - May 10, 2024 =
* NEW: Added official support for using Next Page add-on with Single Posts add-on.
* NEW: Added global `alm_nextpage_post_id` PHP variable for use in filters/shortcodes etc. This will allow the current post ID to be accessed within the Ajax request.
* UPDATE: Code clean up.


= 1.7.1 - January 16, 2024 =
* FIX: Fixed issue with the Auto implementation method attempting to run in the_excerpt() requests. This issue resulted from a core WP bug with excerpt display in Ajax requests.


= 1.7.0 - September 27, 2023 =
* NEW: Added ability to load full post content and split into pages for URL updates without infinite scroll. Use `nextpage_type="fullpage"` shortcode parameter to implement full article pagination URLs.
* NEW: Added `alm_nextpage_retain_querystring` hook to prevent querystring params from being added on pages being loaded via Ajax request. e.g. `add_filter( 'alm_nextpage_retain_querystring', '__return_false' );`
* FIX: Fixed issue with browser fwd/back (popstate) events and first page not moving user to first page.
* FIX: Fixed issue with possible not retaining correct querystring params as pages are loaded.
* Fixed issue with paged URLs when using auto page break functionality not working in WP 6.1. Solution is to move to ?pg=%num% URL format when using auto page break.
* UPDATE: Added compatibility support for PHP 8.2.
* UPDATE: Removed Analytics shortcode parameter as Google Analytics 4 now handles pageviews automatically.
* UPDATE: Major code refactoring, cleanup and organization for the long-term health of the add-on.


= 1.6.4 - June 11, 2023 =
* UPDATE: Updated to add compatibility with Cache Add-on 2.0 and Ajax Load More 6.0.
* FIX: Fixed issue with paged URLs not loading the correct page.


= 1.6.3 - January 6, 2023 =
* FIX: Added DOM loaded event that double checks browser URL vs HTML stored URL for scrolling purposes and to prevent errors.
* UPDATE: Various code, build updates and overall code cleanup.

= 1.6.2 - June 24, 2022 =
* NEW: Added new functionality for Nextpage autoload based on taxonomy terms. This allows conditionally inject a shortcode for certain terms only.
e.g. `[ajax_load_more nextpage="true" taxonomy="actors" taxonomy_terms="will-smith, chris-rock"]

= 1.6.1 - March 7, 2022 =
* Update: Added update to exclude some unnessasary post types from the automatic installation.
* Fix: Adding `page` post type to the automatic installation settings.

= 1.6.0 - January 9, 2022 =
* NEW - Added support for automatic implementation of the Next Page add-on. Simply select the desired post types then enter the shortcode on the Settings > Next Page screen inside Ajax Load More and you are good to go 🎉
* NEW - Added new `alm_nextpage_the_content` hook that provides a method to run a custom content filter on individual pages in each Ajax request.
```
function my_nextpage_content( $content, $page ) {
	$content = "<h3>Page: ${page}</h3>" . $content;
	return $content;
}
add_filter( 'alm_nextpage_the_content', 'my_nextpage_content', 10, 2 );
```

= 1.5.0.1 - February 16, 2021 =
* HOTFIX - Fix for potential issues with trailing commas causing fatal errors on servers PHP 7.2.x and lower.


= 1.5.0 - February 11, 2021 =
* UPGRADE NOTICE - You must update core ALM when updating Next Page add-on.
* NEW - Added new Page Title Template option that allow for updating the browser title when each load more action. e.g. `Page 3 of 15 | My Post Title | Site Title`.
* FIX - Fixed issue with fwd/back buttons and interaction between Ajax loaded pages.
* FIX - Fixed issues with nesting Next Page inside Single Posts add-on. They now work together seemlessly :)
* UPDATE - PHP and JS code cleanup.
* UPDATE - Various updates to support PHP 8.0+.


= 1.4.6 - June 29, 2020 =
* FIX - Fixed issue with querystrings in paging URLs when using Next Page + Cache add-ons.


= 1.4.5 - May 1, 2020 =
* FIX - Fixed issue with push/popstate when using Nextpage and Paging add-ons.
* FIX - Fixed issue with Google Analytics integration sending a second pageview on page load.
* UPDATE - Set default `nextpage_scroll` shortcode parameter to false for better UX and accessibility.
* UPDATE - Disabled `Scroll to Page` functionality if Paging add-on is active. Use Paging add-on to scroll user to the top of the page.


= 1.4.4 - April 22, 2020 =
* FIX - Fixed issue with [Results Text](https://connekthq.com/plugins/ajax-load-more/docs/results-text/) not working when using ALM `id` parameter.

= 1.4.3 - March 2, 2020 =
* NEW - Added `alm_nextpage_paged` filter hook that allows users to stop the loading of previous pages when hitting a paged URL.
```
// website.com/about/4/
add_filter('alm_nextpage_paged', function(){
	return false;
});
```
* FIX - Fixed issue with paged URLs if an incorrect value was entered in `nextpage-scroll` shortcode parameter.


= 1.4.2 - December 17, 2019 =
FIX - Fixed issue with custom ACF Blocks for Gutenberg not displaying in Ajax requests.


= 1.4.1 - May 29, 2019 =
This update relates to fixes applied to Ajax Load More `5.1.1`
* FIX - Added fix for browser popstate issue in core Ajax Load More.
* UPDATE - Removed REST API endpoint.


= 1.4.0 - May 6, 2019 =
* UPGRADE NOTICE - This update requires Ajax Load More 5.1+.
* NEW - Next Page add-on now uses the REST API for Ajax queries. This change can be reverted in ALM Settings.
* NEW - Added pagination in a `<noscript/>` tag for SEO and users without JS enabled.


= 1.3.0 - March 8, 2019 =

## UPGRADE NOTICE
When updating to Next Page 1.3.0 you must also update core (Ajax Load More)[https://wordpress.org/plugins/ajax-load-more/] to version 5.0.

#### What's New
* NEW - Added new `alm_nextpage_split_{id}` filter hook that provides the ability to generate dynamic content breaks. This will allow users to automatically split content into pages at specific HTML tags and not rely on `<!--nextpage--> short tags. See (Next Page docs)[https://connekthq.com/plugins/ajax-load-more/docs/add-ons/next-page/#filter-hooks] for more info.
* NEW - Plugin re-written in vanilla JS without jQuery dependency.
* NEW - Querystring values are now maintained through all page loads.
* UPDATE - Improved scroll to element functionality and URL updates.
* UPDATE - Removed Scroll Speed shortcode parameter. Scroll speed is now a global ALM variable.


= 1.2.0 - December 6, 2018 =
* UPDATE - Adding support for URL querystrings on initial page load. 1st page URLs now retain their initial querystring parameters if present.
* UPDATE - Adding support for nested Ajax Load More instances - The Next page add-on can now be used as a nested Ajax Load More instance. When used as a nested instance paging URLs are disabled.

= 1.1.1 - May 30, 2018 =
* NEW - Added new filters for injecting custom HTML/JS into each page. Please refer to our [docs](https://connekthq.com/plugins/ajax-load-more/docs/add-ons/next-page/) for implementation support.
 - `alm_nextpage_before` adds content before the page.
 - `alm_nextpage_after` adds content after the page.

= 1.1 - March 8, 2018 =
* NEW - Added new filters for customizing URLS. Please refer to our [docs](https://connekthq.com/plugins/ajax-load-more/docs/add-ons/next-page/) for implementation support.
 - `alm_nextpage_leading_slash` adds a leading slash (/) before the page number.
 - `alm_nextpage_remove_trailing_slash` removes the trailing slash (/) at the end of the URL.

= 1.0.3 - January 22, 2018 =
* NEW - Added support for the new gtag Analytics script.

= 1.0.2 - May 4, 2017 =
* NEW - Added support for Cache add-on.
* UPDATE - Updated plugin updater class.

= 1.0.1 - April 26, 2017 =
* New - Added support for cache add-on. Users can now cache Next page content.
* New - Added support Visual Composer shortcodes.
* Update - Added update for plugin updater class.

= 1.0.1 - February 16, 2017 =
* FIX - Bug fix for oEmbeds and shortcodes not triggering after next page load.

= 1.0 - February 14, 2017 =
* Initial Release.
