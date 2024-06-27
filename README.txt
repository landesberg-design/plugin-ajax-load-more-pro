 === Ajax Load More: Pro ===

Contributors: dcooney
Author: Darren Cooney
Author; URI: https://connekthq.com/
Plugin URI: https://connekthq.com/ajax-load-more/pro/
Requires at least: 4.0
Tested up to: 6.5
Stable tag: trunk
Requires PHP: 7.0
Homepage: https://connekthq.com/ajax-load-more/
Donate: https://connekthq.com/donate
Version: 1.2.25

== Copyright ==
Copyright 2024 Darren Cooney, Connekt Media

This software is NOT to be distributed, but can be INCLUDED in WP themes: Premium or Contracted.
This software is distributed in the hope that it will be useful, but WITHOUT ANY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

== Description ==

**Access to all premium add-ons in a single installation**

The Pro bundle is installed as a single product with one license key and contains immediate access all Ajax Load More premium add-ons. Once installed, add-ons are able to be activated with a click from the Pro dashboard inside your WordPress admin.

http://connekthq.com/ajax-load-more/pro/

== Installation ==

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `ajax-load-more-pro.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `ajax-load-more-pro.zip`
2. Extract the `ajax-load-more-pro.zip` to your computer
3. Upload the `ajax-load-more-pro.zip` directory to the `/wp-content/plugins/` directory
4. Ensure Ajax Load More is installed prior to activating the repeater plugin
5. Activate the plugin in the Plugin dashboard

== Upgrade Notice ==

This Ajax Load More Pro update requires updating core Ajax Load More plugin to 5.6.0 for compatibility with the Filters add-on 2.0 update.

== Changelog ==

** 1.2.25 - May 30, 2024 **

Filters - 2.2.1

- NEW: Added new hook that allows for modifying the query args used when creating the facet index. [View Docs](https://connekthq.com/plugins/ajax-load-more/docs/add-ons/filters/facets/#alm_filters_facets_index_args)
- NEW: Added new `almfilters.getActiveFilters()` public JS function that returns an object of active filters.
- NEW: Added new `alm_filters_redirect_underscore_{filter_id}` hook that is used with the Redirect functionality to remove the underscores in URLs on archive pages.


** 1.2.24 - May 10, 2024 **

Next Page - 1.8.0

- NEW: Added official support for using Next Page add-on with Single Posts add-on.
- NEW: Added global `alm_nextpage_post_id` PHP variable for use in filters/shortcodes etc. This will allow the current post ID to be accessed within the Ajax request.
- UPDATE: Code clean up.

Single Posts - 1.7.0

- NEW: Added official support for using Next Page add-on with Single Posts add-on.
- FIX: Fixed issue with undefined PHP variable when using `Next` post ordering.
- UPDATE: Code clean up.

** 1.2.23 - March 21, 2024 **

Filters - 2.2.0

- NEW: Added Posts Per Page filter.
- NEW: Added support for Post Type facet filtering.
- NEW: Added Filters WordPress Block for Ajax Load More rendering filters directly to the Block Editor.
- NEW: When "Hide Inactive Filter Options" is enabled with facet filtering, the entire filter group (radio & checkboxes only) will now be hidden if no filter options are returned.
- NEW: Added support for deeplink URLs when using multiple Filter instances.
- NEW: Added new `redirect` parameter for redirecting users to a new page after a filter action. e.g. `[ ajax_load_more_filters redirect="https://website.com/results" ...]`
- UPDATE: Added console warning when filters is missing the core Ajax Load More instance.
- UPDATE: Added support for search with use of archive="true".
- FIX: Fixed issue with Facet checkbox/radios and the Show/Hide More buttons not always functioning correctly.
- FIX: Fixed issue with sortKey not resetting after clearing the sort value.
- FIX: Fixed issue with restoring the default value of a filter after a change event.
- UPDATE: Accessibility updates to filter checkbox/radios.
- UPDATE: Various UI/UX updates throughout plugin admin and frontend.

Cache - 2.0.3

- UPDATE: Added support for caching Filter add-on facets.


** 1.2.22 - February 8, 2024 **

Elementor - 1.2.0

- NEW: Added support for Loop Grid Elementor widget.
- FIX: Fixed issue with add-on potentially not disabling the Load More button when no content remains.


** 1.2.21 - January 16, 2024 **

UPGRADE NOTICE:

This Ajax Load More Pro update may introduce breaking changes for add-on functionality.
Major changes in this release includes the removal of the `transition_container` parameter which affects the rendered HTML of the plugin by removing the .alm-reveal div used to hold the Ajax loaded content.
After updating please make sure to update the core Ajax Load More plugin to 7.0.0 or greater.

Cache - 2.0.2

- FIX: Fixed issue with display of cache URL on the Cache admin page.

Layouts - 2.1.0

- UPDATE: Updated layouts CSS and HTML to remove all references to `alm-reveal` divs.
- UPDATE: Code cleanup and organization.

Filters - 2.1.2

- UPDATE: Remove all references and output of `alm-reveal` divs.
- UPDATE: Updated JS parameters to match updates in ALM 7.0.

Next Page - 1.7.1

- FIX: Fixed issue with the Auto implementation method attempting to run in the_excerpt() requests. This issue resulted from a core WP bug with excerpt display in Ajax requests.

Paging - 2.0.0

- UPDATE: Reduced Ajax requests on initial page load. Previously Paging add-on dispatched 2 requests and we have reduced this to a single request.
- UPDATE: Pagination container is now created on the server side and not with Javascript on page load. This reduces the CLS (Cumulative Layout Shift) of the functionality.
- UPDATE: Adding required changes for Ajax Load More 7.0 and removal of `alm-reveal` wrapper.
- UPDATE: Improved loading animation and timing of pagination display.
- UPDATE: Code cleanup, CSS updates and organization.
- FIX: Fixed issue where first page link in paging navigation could result in a JS error.

Preloaded - 1.4.0

- UPDATE: Remove all references and output of `alm-reveal` divs.
- UPDATE: Code cleanup and organization.

SEO - 1.9.6

- UPDATE: Updated JS parameters to match updates in ALM 7.0.
- UPDATE: Code cleanup and organization.
- FIX: Fixed issue with SEO Offset not working to update URL params.

Single Posts - 1.6.0

- UPDATE: Remove all references and output of `alm-reveal` divs.
- FIX: Fixed issue with Single Post preview functionality.

Custom Repeaters - 2.5.12

- FIX: Fixed issue with multiline tab support while editing Repeater Templates in the ALM Admin.


** 1.2.20 - September 27, 2023 **

Paging - 1.6.0

- FIX: Fixed issue with paging links not matching final URL in Filters add-on.
- UPDATE: Added support for new Next Page add-on URLs via querystring `pg` parameters.
- UPDATE: Updated paging resize functionality to use ResizeObserver.
- UPDATE: Code cleanup and organization.

 Next Page - 1.7.0

- NEW: Added ability to load full post content and split into pages for URL updates without infinite scroll. Use `nextpage_type="fullpage"` shortcode parameter to implement full article pagination URLs.
- NEW: Added `alm_nextpage_retain_querystring` hook to prevent querystring params from being added on pages being loaded via Ajax request. e.g. `add_filter( 'alm_nextpage_retain_querystring', '__return_false' );`
- FIX: Fixed issue with browser fwd/back (popstate) events and first page not moving user to first page.
- FIX: Fixed issue with possible not retaining correct querystring params as pages are loaded.
- FIX: Fixed issue with paged URLs when using auto page break functionality not working in WP 6.1. Solution is to move to ?pg=%num% URL format when using auto page break.
- UPDATE: Added compatibility support for PHP 8.2.
- UPDATE: Removed Analytics shortcode parameter as Google Analytics 4 now handles pageviews automatically.
- UPDATE: Major code refactoring, cleanup and organization for the long-term health of the add-on.

Filters - 2.2.1

- NEW: Added support for facets and Post Type filtering.
- UPDATE: Removed Analytics shortcode parameter as Google Analytics 4 (GA4) now handles pageviews automatically.
- UPDATE: Updated Filters JavaScript to support new Google Analytics 4.
- FIX: Fixed issue with aria-checked attribute being encoded incorrectly on the frontend.
- FIX: Fixed issue where taxonomy filter could return a fatal error if no terms exist on the taxonomy.
- FIX: Fixed issue with category and tag filters on frontpage or homepage not parsing the querystring parameters correctly.
- FIX: Stopped frontpage URLs from being encoded by the core WP `redirect_canonical` hook when using a static homepage. e.g. /?category=design+development was being encoded to /?category=design%20development
- UPDATE: Removed legacy IE support for IE10 and IE11.

Elementor - 1.1.5

- UPDATE: Updated to support new Google Analytics 4 implementation.

SEO - 1.9.5

- UPDATE: Updated to support new Google Analytics 4 implementation.

Single Posts - 1.5.6

- UPDATE: Updated to support new Google Analytics 4 implementation.
- UPDATE: Code cleanup and organization.

WooCommerce - 1.2.4

- UPDATE: Updated to support new Google Analytics 4 implementation.
- UPDATE: Updated compatibility to WooCommerce 8.1.0.


** 1.2.19 - July 27, 2023 **

- UPDATE: Updated Pro settings page to match Ajax Load More 6.1 admin layout.

Cache - 2.0.1
- UPDATE: Updated cache admin page to reflect new admin styling.
- UPDATE: Updated various cache dashboard styles.
- FIX: Fixed spelling issues with REST API test notice.
- FIX: Fixed URL error in admin display of cache listing.

Custom Repeaters - 2.5.11

- UPDATE: Admin UI updates to match core Ajax Load More 6.1 updates.
- UPDATE: Code cleanup and organization.

Filters - 2.1.0

- FIX: Fixed issue with `attachment` post type and facets not returning results due to `post_status` not being set to `inherit`.
- FIX: Updated the facet naming convention to allow facets to be reused with multiple instances of Ajax Load More and queries.
- FIX: Added fix for deep link custom field queries not working if a duplicate `meta_key` has been set for ordering.
- FIX: Allow for decimals in Range Slider input steps.
- FIX: Removed orphaned quote in the Select listing for taxonomy terms.
- FIX: Fixed issue with Default Values being incorrectly added to a query when using Radio field type.
- UPDATE: Adding support for new `sort_key` parameter in Ajax Load More 6.1 that adds better control for ordering results by custom field key.
- UPDATE: Updated admin pages to match new Ajax Load More 6.1 admin layout.
- UPDATE: Various code cleanup tasks and file structure organizations.

** Layouts - 2.0.2

- UPDATE: Updated layout listing design and display.
- UPDATE: Updated various elements to match new core Ajax Load More 6.1 admin styling.

WooCommerce - 1.2.3

- UPDATE: Updated the WooCommerce admin page to use the new Ajax Load More 6.1 admin layout.

** 1.2.18 - June 11, 2023 **

Cache - 2.0.0

- NOTICE: Cache 2.0 is not compatible with Ajax Load More 5.x or lower.
- NOTICE: After this update the current ALM cache will be cleared and a new cache will be created when requested.
- NEW: Cache 2.0 is now compatible with all Ajax Load More add-ons and extensions.
- NEW: Cache 2.0 introduces a new cache directory structure, file naming convention.
- NEW: Cached pages now use [MD5 hash](https://en.wikipedia.org/wiki/MD5) as the file names. This adds more stability and removes complexity from fetching the cached files.
- NEW: Added new cache_id template variables `%post_id%` & %post_slug% that allows for dynamically injecting the post id or slug into the cache_id. `[ajax_load_more cache="true" cache_id="my-cache-id-%post_id%"]`
- NEW: Added new REST API endpoints for creating and fetching cached files from the server.
- NEW: Added support for Elementor and WooCommerce add-ons.
- FIX: Fixed various issues with Filters add-on compatibility.
- FIX: Fixed issue with load more button not shutting down on the last page of results.
- UPDATE: Code cleanup and optimization.

Comments - 1.2.1

- UPDATE: Updated to add compatibility with Cache Add-on 2.0 and Ajax Load More 6.0.
- UPDATE: Code cleanup.


Elementor - 1.1.4

- NEW: Added Elementor widget setting for `button_done_label` shortcode parameter.
- UPDATE: Updated to add compatibility with Cache Add-on 2.0 and Ajax Load More 6.0.
- UPDATE - Code cleanup.
- UPDATE - Elementor compatibility version bump and testing.
- FIX - Fix for PHP warning about undefined `cache` index.

Filters - 2.0.2.2

- UPDATE: Various security fixes and data escaping.
- FIX: Suppressed php 8.1+ warnings about `FILTER_SANITIZE_STRING` being deprecated.
- FIX: Fixed issue with decimal values in range slider being displayed in URL when not required.

Next Page - 1.6.4

- UPDATE: Updated to add compatibility with Cache Add-on 2.0 and Ajax Load More 6.0.
- FIX: Fixed issue with paged URLs not loading the correct page.

Single Posts - 1.5.5

- UPDATE: Updated to add compatibility with Cache Add-on 2.0 and Ajax Load More 6.0.
- UPDATE: Code cleanup and organization.

WooCommerce - 1.2.2

- UPDATE: WooCommerce version bump.
- UPDATE: Code cleanup and optimization.


** 1.2.17 - March 9, 2023 **
Filters - 2.0.2.1

- HOTFIX: Sanitizing filters target parameter with `sanitize_key` to coincide with core ALM `5.6.0.4` release.


** 1.2.16 - February 25, 2023 **

Filters - 2.0.2

- FIX: Fixed issue with parsing filters & facets on archive templates/pages.
- FIX: Fixed querystring params not being passed to query on taxonomy archive pages.
- FIX: Fixed dynamic filter values not working on archive pages.
- FIX: Fixed issue with PHP generator output and sort field.
- NEW: Added `alm_filters_range_slider_steps` hook to adjust the default input steps when using the Range Slider.
- NEW: Added `alm_filters_textfield_submit_label` to filter textfield submit button labels.
- NEW: Added `alm_filters_textfield_placeholder` to filter the textfield input placeholder.
- NEW: Added `alm_filters_css_classes` to allow for filtering of container classnames.

WooCommerce - 1.2.1

- UPDATE: Added support for disabling Images Loaded functionality via Customizer setting.


** 1.2.15 - February 16, 2023 **

Filters - 2.0.1

- FIX: Fixed PHP warning that could be displayed in debug log about undefined `facet` array key.
- FIX: Fixed issue with unwanted `]`character being rendered in some instances of select drop menus.
- FIX: Fixed issue with select displaying result count even if not checked in Filter admin.


** 1.2.14 - February 14, 2023 **

UPGRADE NOTICE:
This filters update requires updating core Ajax Load More plugin to 5.6.0

Filters - 2.0.0

- NEW: Added Facet Filtering.
- NEW: Added duplicate filters functionality that allows for easy duplication of filters.
- NEW: Added ability to sort filter dashboard columns by column headers.
- NEW: Added filter preview functionality.
- NEW: Added support for `include_children` parameter when running a taxonomy query.
- NEW: Added support for passing filter ID to `alm_filters( ID, ALM_ID)` PHP method.
- NEW: Added ability to safely delete filters from WP backend when using the `alm_filters()` PHP method for initiating a filter.
- FIX: Fixed issues with Selected Filters display and item counter.
- FIX: Added checker function to confirm taxonomy exists before attempting to render a tax filter which will prevent frontend PHP warnings.
- FIX: Fixed issue with almFiltersActive callback function not working correctly.
- FIX: Fixed issue with Reset button not hiding in the correct instances.
- FIX: Fixed issue with category__and and tag__and checkboxes not remaining selected on page reload.
- FIX: Fixed bug with `default_values` not being maintained on tax and meta queries in some instances.
- UPDATE: Various admin UI/UX updates.
- UPDATE: Cleaned up Filter builder JavaScript to make it easier for future updates.
- UPDATE: Improved taxonomy and meta query handling on deep linked queries.

Elementor - 1.1.3

- FIX - Updated widget function to fix issues with function deprecation notices.

Layouts - 2.0.1

- UPDATE - Updated the exported CSS of default layout to allow for image sizes of any dimensions.
- UPDATE - Added support for Paging addon and new layouts.
- UPDATE - Added uniform spacing below listing and load more button.
- FIX - Added missing Gallery layout to layout listing.
- FIX - Fixed issue with gap spacing and load more elements.
- FIX - Various spacing and alignment tweaks using minmax for columns.

Paging - 1.5.6.2

- FIX: Fixed potential CSS issue with double loading icon when using Paging and Filters add-ons together.

Custom Repeaters - 2.5.10

- FIX: Added fix and warning message if Repeater Template is missing from the filesystem. This fix prevents a fatal error on the Repeater Template admin listing page and also allows for saving of the template at runtime.


** 1.2.13 - January 10, 2023 **

Cache - 1.7.6

- FIX: Added fix for PHP deprecation warning that could be displayed on some PHP versions.

Filters - 1.13.0.4

- HOTFIX: Adding fix for missing constant name that was causing a fatal error.


** 1.2.12 - January 6, 2023 **

Cache - 1.7.5

- NEW: Added the ability to clear an individual cache by ID using the `alm_clear_cache` action.
- UPDATE: Code cleanup and organization.

Layouts - 2.0

* UPGRADE NOTICE: Layouts has been completely rebuilt and now uses CSS Grid for layout and additional shortcode parameters for configuration.
- UPDATE: Replaced flexbox layout with CSS Grid.
- UPDATE: Added new Layout shortcode parameters to style the ALM container. `[ajax_load_more layout="true" layouts_cols="2"]`
* [See Docs](https://connekthq.com/plugins/ajax-load-more/add-ons/layouts/) for more information.

Nextpage - 1.6.3

- FIX: Added DOM loaded event that double checks browser URL vs HTML stored URL for scrolling purposes and to prevent errors.
- UPDATE: Various code, build updates and overall code cleanup.

Single Posts - 1.5.4

- NEW: Added new `almSinglePostsLoaded` JavaScript callback discpatched after the plugin has completed the initial setup.
- UPDATE: Added new admin prompt when activating plugin without core Ajax Load More installed.
- UPDATE: Code cleanup and organization.

Custom Repeaters - 2.5.9

- UPDATE: Code cleanup.
- UPDATE: Fixing data sanitization and function organization.
- UPDATE: Improved update routine for when plugin is updated.


** 1.2.11 - June 24, 2022 **

Filters - 1.13.0.3

- UPDATE: Improved accessibility of admin filter builder.
- UPDATE: Added localstorage variable for expanding/collapsing admin filters. Filters in the admin will now retain the selected state (expanded/collapse)).

NextPage - 1.6.2

- NEW: Added new functionality for Nextpage autoload based on taxonomy terms. This allows conditionally inject a shortcode for certain terms only.
e.g. `[ajax_load_more nextpage="true" taxonomy="actors" taxonomy_terms="will-smith, chris-rock"]


** 1.2.10 - March 7, 2022 **

Cache - 1.7.4

- NEW: Added the ability to clear an individual cache by ID using the `alm_clear_cache` action.
- UPDATE: Code cleanup and organization.

Filters - 1.13.0.2

- FIX: Added fix for filters not starting when loading with Ajax.

Next Page - 1.6.1

- UPDATE: Added update to exclude some unnessasary post types from the automatic installation.
- FIX: Adding `page` post type to the automatic installation settings.

Paging - 1.5.6.1

- UPDATE: Improved initial loading animations.
- UPDATE: Code cleanup and re-organization.

SEO - 1.9.4

- NEW: Adding new setting (`seo_offset`) that allows for offsetting the SEO pagination to start at page/2. This allows for users to run a query before ALM and still use SEO as they normally would.
- FIX: Fixed issue with SEO and Preloaded element not getting the 'alm-preloaded' classname..
- UPDATE: Code Cleanup.

... continued in changelog.txt


** 1.0 - November 3, 2018 **

- Initial Release
