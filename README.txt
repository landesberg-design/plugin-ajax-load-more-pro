 === Ajax Load More: Pro ===

Contributors: dcooney
Author: Darren Cooney
Author; URI: https://connekthq.com/
Plugin URI: https://connekthq.com/ajax-load-more/pro/
Requires at least: 4.0
Tested up to: 6.2
Stable tag: trunk
Homepage: https://connekthq.com/ajax-load-more/
Version: 1.2.18

== Copyright ==
Copyright 2023 Darren Cooney, Connekt Media

This software is NOT to be distributed, but can be INCLUDED in WP themes: Premium or Contracted.
This software is distributed in the hope that it will be useful, but WITHOUT ANY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

== Description ==

= Access to all premium add-ons in a single installation =

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

= 1.2.18 - June 11, 2023 =

**Cache - 2.0.0 **
* NOTICE: Cache 2.0 is not compatible with Ajax Load More 5.x or lower.
* NOTICE: After this update the current ALM cache will be cleared and a new cache will be created when requested.
* NEW: Cache 2.0 is now compatible with all Ajax Load More add-ons and extensions.
* NEW: Cache 2.0 introduces a new cache directory structure, file naming convention.
- NEW: Cached pages now use [MD5 hash](https://en.wikipedia.org/wiki/MD5) as the file names. This adds more stability and removes complexity from fetching the cached files.
* NEW: Added new cache_id template variables `%post_id%` & %post_slug% that allows for dynamically injecting the post id or slug into the cache_id. `[ajax_load_more cache="true" cache_id="my-cache-id-%post_id%"]`
* NEW: Added new REST API endpoints for creating and fetching cached files from the server.
* NEW: Added support for Elementor and WooCommerce add-ons.
* FIX: Fixed various issues with Filters add-on compatibility.
* FIX: Fixed issue with load more button not shutting down on the last page of results.
* UPDATE: Code cleanup and optimization.


**Comments - 1.2.1 **
* UPDATE: Updated to add compatibility with Cache Add-on 2.0 and Ajax Load More 6.0.
* UPDATE: Code cleanup.

**Elementor - 1.1.4 **
* NEW: Added Elementor widget setting for `button_done_label` shortcode parameter.
* UPDATE: Updated to add compatibility with Cache Add-on 2.0 and Ajax Load More 6.0.
* UPDATE - Code cleanup.
* UPDATE - Elementor compatibility version bump and testing.
* FIX - Fix for PHP warning about undefined `cache` index.

**Filters - 2.0.2.2 **
* UPDATE: Various security fixes and data escaping.
* FIX: Suppressed php 8.1+ warnings about `FILTER_SANITIZE_STRING` being deprecated.
* Fix: Fixed issue with decimal values in range slider being displayed in URL when not required.

**Next Page - 1.6.4 **
* UPDATE: Updated to add compatibility with Cache Add-on 2.0 and Ajax Load More 6.0.
* FIX: Fixed issue with paged URLs not loading the correct page.

**Single Posts - 1.5.5 **
* UPDATE: Updated to add compatibility with Cache Add-on 2.0 and Ajax Load More 6.0.
* UPDATE: Code cleanup and organization.

**WooCommerce - 1.2.2 **
* UPDATE: WooCommerce version bump.
* UPDATE: Code cleanup and optimization.


= 1.2.17 - March 9, 2023 =
**Filters - 2.0.2.1 **
* HOTFIX: Sanitizing filters target parameter with `sanitize_key` to coincide with core ALM `5.6.0.4` release.


= 1.2.16 - February 25, 2023 =
**Filters - 2.0.2 **
* FIX: Fixed issue with parsing filters & facets on archive templates/pages.
* FIX: Fixed querystring params not being passed to query on taxonomy archive pages.
* FIX: Fixed dynamic filter values not working on archive pages.
* FIX: Fixed issue with PHP generator output and sort field.
* NEW: Added `alm_filters_range_slider_steps` hook to adjust the default input steps when using the Range Slider.
* NEW: Added `alm_filters_textfield_submit_label` to filter textfield submit button labels.
* NEW: Added `alm_filters_textfield_placeholder` to filter the textfield input placeholder.
* NEW: Added `alm_filters_css_classes` to allow for filtering of container classnames.

**WooCommerce - 1.2.1 **
* Update: Added support for disabling Images Loaded functionality via Customizer setting.


= 1.2.15 - February 16, 2023 =
**Filters - 2.0.1 **
* FIX: Fixed PHP warning that could be displayed in debug log about undefined `facet` array key.
* FIX: Fixed issue with unwanted `]`character being rendered in some instances of select drop menus.
* FIX: Fixed issue with select displaying result count even if not checked in Filter admin.


= 1.2.14 - February 14, 2023 =

UPGRADE NOTICE:
This filters update requires updating core Ajax Load More plugin to 5.6.0

**Filters - 2.0.0**
* NEW: Added Facet Filtering.
* NEW: Added duplicate filters functionality that allows for easy duplication of filters.
* NEW: Added ability to sort filter dashboard columns by column headers.
* NEW: Added filter preview functionality.
* NEW: Added support for `include_children` parameter when running a taxonomy query.
* NEW: Added support for passing filter ID to `alm_filters( ID, ALM_ID)` PHP method.
* NEW: Added ability to safely delete filters from WP backend when using the `alm_filters()` PHP method for initiating a filter.
* FIX: Fixed issues with Selected Filters display and item counter.
* FIX: Added checker function to confirm taxonomy exists before attempting to render a tax filter which will prevent frontend PHP warnings.
* FIX: Fixed issue with almFiltersActive callback function not working correctly.
* FIX: Fixed issue with Reset button not hiding in the correct instances.
* FIX: Fixed issue with category__and and tag__and checkboxes not remaining selected on page reload.
* FIX: Fixed bug with `default_values` not being maintained on tax and meta queries in some instances.
* UPDATE: Various admin UI/UX updates.
* UPDATE: Cleaned up Filter builder JavaScript to make it easier for future updates.
* UPDATE: Improved taxonomy and meta query handling on deep linked queries.

**Elementor - 1.1.3**
* FIX - Updated widget function to fix issues with function deprecation notices.

** Layouts - 2.0.1**
* UPDATE - Updated the exported CSS of default layout to allow for image sizes of any dimensions.
* UPDATE - Added support for Paging addon and new layouts.
* UPDATE - Added uniform spacing below listing and load more button.
* FIX - Added missing Gallery layout to layout listing.
* FIX - Fixed issue with gap spacing and load more elements.
* FIX - Various spacing and alignment tweaks using minmax for columns.

** Paging - 1.5.6.2**
* FIX: Fixed potential CSS issue with double loading icon when using Paging and Filters add-ons together.

= Custom Repeaters - 2.5.10=
* FIX: Added fix and warning message if Repeater Template is missing from the filesystem. This fix prevents a fatal error on the Repeater Template admin listing page and also allows for saving of the template at runtime.


= 1.2.13 - January 10, 2023 =

**Cache - 1.7.6**
* FIX: Added fix for PHP deprecation warning that could be displayed on some PHP versions.

**Filters - 1.13.0.4**
* HOTFIX: Adding fix for missing constant name that was causing a fatal error.


= 1.2.12 - January 6, 2023 =

**Cache - 1.7.5**
* NEW: Added the ability to clear an individual cache by ID using the `alm_clear_cache` action.
* UPDATE: Code cleanup and organization.

**Layouts - 2.0**
* UPGRADE NOTICE: Layouts has been completely rebuilt and now uses CSS Grid for layout and additional shortcode parameters for configuration.
* UPDATE: Replaced flexbox layout with CSS Grid.
* UPDATE: Added new Layout shortcode parameters to style the ALM container. `[ajax_load_more layout="true" layouts_cols="2"]`
* [See Docs](https://connekthq.com/plugins/ajax-load-more/add-ons/layouts/) for more information.

**Nextpage - 1.6.3**
* FIX: Added DOM loaded event that double checks browser URL vs HTML stored URL for scrolling purposes and to prevent errors.
* UPDATE: Various code, build updates and overall code cleanup.

**Single Posts - 1.5.4**
* NEW: Added new `almSinglePostsLoaded` JavaScript callback discpatched after the plugin has completed the initial setup.
* UPDATE: Added new admin prompt when activating plugin without core Ajax Load More installed.
* UPDATE: Code cleanup and organization.

**Custom Repeaters - 2.5.9**
* UPDATE: Code cleanup.
* UPDATE: Fixing data sanitization and function organization.
* UPDATE: Improved update routine for when plugin is updated.


= 1.2.11 - June 24, 2022 =

**Filters - 1.13.0.3**
* UPDATE: Improved accessibility of admin filter builder.
* UPDATE: Added localstorage variable for expanding/collapsing admin filters. Filters in the admin will now retain the selected state (expanded/collapse)).

**NextPage - 1.6.2**
* NEW: Added new functionality for Nextpage autoload based on taxonomy terms. This allows conditionally inject a shortcode for certain terms only.
e.g. `[ajax_load_more nextpage="true" taxonomy="actors" taxonomy_terms="will-smith, chris-rock"]


= 1.2.10 - March 7, 2022 =

**Cache - 1.7.4**
* NEW: Added the ability to clear an individual cache by ID using the `alm_clear_cache` action.
* UPDATE: Code cleanup and organization.

**Filters - 1.13.0.2**
* FIX: Added fix for filters not starting when loading with Ajax.

**Next Page - 1.6.1**
* Update: Added update to exclude some unnessasary post types from the automatic installation.
* Fix: Adding `page` post type to the automatic installation settings.

**Paging - 1.5.6.1**
* Update: Improved initial loading animations.
* Update: Code cleanup and re-organization.

**SEO - 1.9.4**
* NEW: Adding new setting (`seo_offset`) that allows for offsetting the SEO pagination to start at page/2. This allows for users to run a query before ALM and still use SEO as they normally would.
* FIX: Fixed issue with SEO and Preloaded element not getting the 'alm-preloaded' classname..
* UPDATE: Code Cleanup.


= 1.2.9 - January 11, 2022 =
**Filters - 1.13.0.1**
* HOTFIX - Fixed issue with multiple filters using same filter key (e.g. category, tag) not working.
* HOTFIX - Fixed issue with filters becoming unresponsive when using an integer target ID.
* UPDATE - Various admin UX/UI updates.
* UPDATE - Updated axios HTTP library to latest version.


= 1.2.8 - January 10, 2022 =
**Filters - 1.13.0**
* UPGRADE NOTICE - Users updating to Filters v1.13.0 must update core Ajax Load More to the version 5.5.1 or greater.
* UPGRADE NOTICE - Filters v1.13.0 changes the way Default Values are handled in Ajax Load More. When setting a Default Value the Filters add-on now sets the value in the core Ajax Load More shortcode automatically.

* NEW - Added support for adding multiple instances of Filters on one page. When multiple instances are present some core functionality like paging URLs and fwd/back button support is disabled.
* NEW - Added two new hooks for use with Checkbox and Radio filters that allow for injecting items before or after a dynamically generated term list.
	- `alm_filters_{id}_{key}_before` and `alm_filters_{id}_{key}_after`
* UPDATE - Updated the functionality of the Default Value setting. In Filters 1.13.0+ setting a Default Value will now automatically set the parameter it in the core `ajax_load_more shortcode`. Users will want to update there shortcodes if they were previously using the Default Value setting.
* UPDATE - Exposed range sliders to the global window scope (`window.alm_range_{filter_id}`) that allows for developers to hook into the UISlider component and update the functionality if required.
* FIX - Fixed issue with Section Toggle functionality losing its status when saving and revisiting the filters admin.
* FIX - Fixed issue with Star Rating filter returning HTML when using the `alm-selected-filters` HTML element.

**Paging - 1.5.6**
* NEW - Added new `almPagingComplete` callback that is dispatched once the Paging DOM elements have completed any transiations following an Ajax request.

**Next Page - 1.6.0**
* NEW - Added support for automatic implementation of the Next Page add-on. Simply select the desired post types then enter the shortcode on the Settings > Next Page screen inside Ajax Load More and you are good to go 🎉
* NEW - Added new `alm_nextpage_the_content` hook that provides a method to run a custom content filter on individual pages in each Ajax request.

**Custom Repeaters - 1.5.8**
* HOTFIX - Fixed potential issue with missing PHP Class in plugin updater.


= 1.2.7 - July 8, 2021 =

**Filters - 1.12.2**
* NEW - Added new hook that provides support adjusting the term parameters of the Filters `get_terms` query. This will allow for setting a `parent` or `child_of` taxonomy option to return the children of a specific term.* FIX - Fixed issue with W3C HTML validator errors. [View Docs](https://connekthq.com/plugins/ajax-load-more/docs/add-ons/filters/#term-query)
* FIX - Fixed issue caused by `alm_filters_public_taxonomies` filter working incorrectly to show non-public taxonomies.
* FIX - Fixed an error with Radio buttons where the selected element was unable to be unchecked in some instances.
* UPDATE - Adding activation warning if core Ajax Load More is not installed when attempting to install add-on.
* UPDATE - Code cleanup and optimization.

**WooCommerce - 1.2.0**
* UPGRADE NOTICE - Users are required to update core Ajax Load More to v5.5 or greater when updating this add-on.
* NEW - Added support for loading products in a reverse load more using a Load Previous button for users who land on a paged URL. e.g. website.com/shop/page/4
* FIX - Fixed issue with Light Grey loading style not working.

**Elementor - 1.1.2**
* NEW - Added support for lazy loading images.
* UPDATE - Adding activation warning if core Ajax Load More is not installed when attempting to install add-on.
* UPDATE - Updated copy throughout Ajax Load More Elementor widget.
* UPDATE - Adding Elementor tested up to versioning for better update support.
* FIX - Fixed issue with network activation of Elementor Pro causing add-on to not activate.

**Custom Repeaters - 2.5.7**
* FIX - Fixed issue with empty spaces at the start of the Repeater Templates.
* UPDATE - Code cleanup and organization.

**Theme Repeaters - 1.1.3.3**
* UPDATE - Adding activation warning if core Ajax Load More is not installed when attempting to install add-on.


= 1.2.6 - May 6, 2021 =

**Filters - 1.12.2**
* NEW - Added new callback for the Range Slider field type that allows for modification of the start and end value display label. [View Docs](https://connekthq.com/plugins/ajax-load-more/docs/add-ons/filters/#almFiltersFormatRangeValues)
* NEW - Made it easier for uses to find the ID of each filter group by including the dynamic ID under the 'What's This' helper in each filter. e.g. alm_filters_actors_category
* FIX - Fixed issue with Select 'Default Select Option' not displaying in taxonomy queries.
* FIX - Fixed issues with duplicate IDs on input and select field types when using multiple Custom Field filters.
* UPDATE - Updated [alm_filters_{id}_{key}](https://connekthq.com/plugins/ajax-load-more/docs/add-ons/filters/#alm_filters_id_key) filter to accept the current Custom Values as a $values array. This allows users to use Custom Values and the Filter Hook to build the filter options.
* UPDATE - Improved admin UI/UX of adding/removing filter blocks.


= 1.2.5 - April 20, 2021 =

**Add-on Updates**

**Filters - 1.12.0**
UPGRADE NOTICE:
The following two updates affect the Select field type only. You may need to adjust your filters after updating.

* NEW - Added new `Default Select Option` field to replace the `Default Label` field for when using the Select field type.
	Note: This field will be displayed as the first option in the select dropdown list.
* NEW - Added HTML `Label` field for Select and Multi-Select field types. This update was added for accessibility and best practices purposes.
	Note: The `Label` field was previously used as the `Default Label` for each filter.

OTHER CHANGES:
* NEW - Added support for multiple Ajax Load More filter shortcodes on the same page or page template.
* NEW - Added aria-label attributes on Selected Filters button for accessibility.
* NEW - Added dynamic classnames to radio and checkbox <li/> attributes for easier target CSS styling.
* UPDATE - Updated JavaScript to allow for moving of filter elements on page load. This is useful when a developer wants to move a specific filter group (`.alm-filter--group`) to another location on the page.
* UPDATE - Removed use `parse_str` for querystring parsing for a custom solution to allow spaces in Custom Field values. Previously, spaces would be converted to `+` for querying.
* FIX - Fixed issue with hash links causing issues when using back/fwd buttons after a filter and URL changes.
* FIX - Fixed issue with multi-select input field and [Selected Filters](https://connekthq.com/plugins/ajax-load-more/add-ons/filters/selected-filters/) functionality not working to display the active filters.
* FIX - Fixed issue with spaces in custom fields and search queries causing problems with selected filters and initial query.

**WooCommerce - 1.1.1**
* NEW - Added new Customizer setting and filter (`alm_woocommerce_permalink_structure`) that provides a method to change the URL permalink structure.
This is helpful for when a site or plugin (Divi, Elementor) uses a paging permalink structure outside of the default WooCommerce `/page/3/` structure.
	add_filter('alm_woocommerce_permalink_structure', function() {
		return '?product-page={page}';
	});

**Paging - 1.5.5**
* FIX - Fixed issue with the loading of the paging navigation for users who have disabled the Paging CSS. On page load, all page number would be shown when CSS was disabled.

**Cache - 1.7.3.1**
* UDATES - Various admin styling updates to match the updated look and feel of core Ajax Load More v5.4.5.


= 1.2.4 - March 31, 2021 =

**Add-on Updates**

**Single Posts - 1.5.3**
* FIX - Fixed potential ordering issue with using a custom query on sites with a large amount of posts.
* UPDATE - Added console warning if Ajax Load More is unable to locate target post element specified in shortcode.
* UPDATE - Added functionality that allows Ajax Load More to fetch elements outside the Single Posts target element and pull them in for display in each load more action.

**Users - 1.1.2**
* FIX - Fixed issue with `include` and `exclude` users by ID not working. This is because the IDs were not being passed in as an array.


= 1.2.3.2 - February 16, 2021 =

* HOTFIX - Fix for potential issues with trailing commas causing fatal errors on servers PHP 7.2.x and lower.

**Add-on Updates**
This hotfix applies to the following add-ons:

- Call to Actions - 1.0.4.1
- Next Page - 1.5.0.1
- Theme Repeaters - 1.1.3.2
- WooCommerce - 1.1.0.1


= 1.2.3.1 - February 14, 2021 =

**Add-on Updates**

**Theme Repeaters - 1.5.0**
* HOTFIX - Fix for potential fatal error due to invalid amount of arguments being passed to Theme Repeaters filter.
* UPDATE - Adding defaults to all filters.


= 1.2.3 - February 11, 2021 =

**Add-on Updates**

**Next Page - 1.5.0**

* UPGRADE NOTICE - You must update core ALM when updating Next Page add-on.
* NEW - Added new Page Title Template option that allow for updating the browser title when each load more action. e.g. `Page 3 of 15 | My Post Title | Site Title`.
* FIX - Fixed issue with fwd/back buttons and interaction between Ajax loaded pages.
* FIX - Fixed issues with nesting Next Page inside Single Posts add-on. They now work together seemlessly :)
* UPDATE - PHP and JS code cleanup.
* UPDATE - Various updates to support PHP 8.0+.

**Single Posts - 1.5.2**

* NEW - Added post preview functionality - [View Example](https://connekthq.com/accessibility-and-ajax-load-more/?showads=showpreview).
* FIX - Fixed URL issues with nesting Next Page add-on inside Single Posts add-on.
* UPDATE - PHP and JS code cleanup.
* UPDATE - Various updates to support PHP 8.0+.

**WooCommerce - 1.1.0**

* NEW - Added new customizer options for setting the WooCommerce container and product classes. Previously, these values needed to be entered using custom [filter hooks](https://connekthq.com/plugins/ajax-load-more/docs/add-ons/woocommerce/#filter-hooks).
* NEW - Adding console warnings for when ALM cannot find WooCommerce containers.
* UPDATE - Various updates to support PHP 8.0+.

**Theme Repeaters - 1.1.3**

* UPGRADE NOTICE - You must update core ALM when updating Theme Repeaters.
* Update - Update integration with core ALM to include new functionality for [passing variables](https://connekthq.com/plugins/ajax-load-more/docs/code-samples/passing-variables/) method.
* UPDATE - Various updates to support PHP 8.0+.

**Call to Actions - 1.0.4**

* UPGRADE NOTICE - You must update core ALM when updating Call to Actions.
* Update - Updated CTA and Theme Repeaters integration to include new functionality for [passing variables](https://connekthq.com/plugins/ajax-load-more/docs/code-samples/passing-variables/) method.
* UPDATE - Code clean and refactoring.


= 1.2.2 - January 3, 2021 =

* UPDATE - Code Cleanup

**Add-on Updates**

Cache - 1.7.3

* FIX - Fixed issue with [Auto Cache Generation](https://connekthq.com/plugins/ajax-load-more/docs/add-ons/cache/#auto-generate) and included version of JQuery of WP 5.6.
* UPDATE - Code cleanup and organization.

Filters - 1.11.0

* NEW - Added `Show Count` setting to display a total count beside each filter item.
* NEW - Added setting to add a `Reset Filters` button. Under the `Options` for each Filter is a `Reset Filters` option to enable the button on the filter frontend.
* UPDATE - Added support for selecting by `meta_value` and `meta_value_num` while using the Sorting filter with Custom Fields.
* FIX - Fixed issue with <select/> field types and the new Hierarchical terms listing not displaying child terms.
* FIX - Fixed issue with excluded Authors appearing is author list.
* FIX - Fixed issue with HTML markup in nested radio/checkbox lists.
* FIX - Fixed potential issue with recursion when a taxonomy does not exist and a filter is attempted to be run.
* FIX - Added fix for restoring the default values of a checkbox and sort field types.

Single Posts 1.5.1

* Fix - Fixed issue with custom query returning all posts if the query was empty.
* FIX - Fixed issue with new custom query feature failing to pass the correct data to the shortcode.

= 1.2.1 - November 25, 2020 =

** UPGRADE NOTICE **
Elementor Add-on users should be made aware that this update requires Ajax Load More version 5.5.1. This is a breaking change and will require Elementor widget updates.
See the Elementor section below for more details.

**Add-on Updates**

Filters - 1.10.2

* NEW - Added filter setting to set toggle blocks collapsed on initial page load.
* NEW - Added new `alm_filters_public_taxonomies` filter to allow for filtering the taxonomy query options in the filter builder. e.g. `add_filter( 'alm_filters_public_taxonomies', '__return_false' );eventheimpliedwarrantyofMERCHANTABILITYorFITNESSFORAPARTICULARPURPOSE.DescriptionUnlockadditionalrepeatersandkeepyoursitelookingandfeelingfreshAjaxLoadMore’sUnlimitedRepeatersadd-onwillunlocktheabilitytocreateaninfinitenumberrepeatertemplates.httpeventheimpliedwarrantyofMERCHANTABILITYorFITNESSFORAPARTICULARPURPOSE.DescriptionUnlockadditionalrepeatersandkeepyoursitelookingandfeelingfreshAjaxLoadMore’sUnlimitedRepeatersadd-onwillunlocktheabilitytocreateaninfinitenumberrepeatertemplates.http`
* UPDATE - Added support for multi-level taxonomy terms listings in nested `<ul/>`. Previously only two levels was supported aesthetically.
* FIX - Fixed issue with hash links (`href="#{target}"`) causing a popstate which would trigger a filter change event.
* FIX - Fixed issue in [Selected Filters](https://connekthq.com/plugins/ajax-load-more/add-ons/filters/selected-filters/) where searching with a `+` would cause the string to split into multiple results.
* FIX - Fixed issue in admin where Author Role would not be selected in the Filter builder.

Elementor - 1.1
We have updated the Elementor add-on settings for how Ajax Load More determines the next page of content during infinite scroll.
Ajax Load More no longer uses the `Numbers` pagination type, please follow the steps below to update your widget after update.

1. Visit your page in Elementor.
2. Locate the Posts Widget.
3. Update the `Pagination` type in the Posts Widget from `Numbers` to `Numbers + Previous/Next` or just `Previous/Next`.
4. View the [Guide](https://connekthq.com/plugins/ajax-load-more/add-ons/elementor/#configuration) if you require addtional information.

* NEW - Added integration for WooCommerce Product Widget.
* NEW - Added integration for Ajax Load More Cache.

= 1.2.0 - November 11, 2020 =

**Add-on Updates**

Elementor

* NEW - Added new [Elementor Add-on](https://connekthq.com/plugins/ajax-load-more/add-ons/elementor/). This add-on will need to be enabled in Ajax Load More > Pro in your WordPress admin.

Single Posts

* NEW - Added support for custom queries using core taxonomy, category and tag query parameters in Ajax Load More 🎉

Cache

* FIX - Fixed issue with PHP error on Cache admin page when the cache directory does not exist.

= 1.1.7 - October 1, 2020 =

**Add-on Updates**

Filters

* NEW - Added new Filter toggle option to allow users to expand/collapse induvidual filter groups. This is turned off by default and must be set to true in each filter group.
* NEW - Added new `.hidden` CSS class to quickly allow users to hide filters via custom `CSS Class` input in filter admin. Under CSS Classes of each filter you can add a class of `hidden` to hide the entire filter.
* FIX - Fixed issue in Safari with Filters and Paging add-ons where a back button press would remove ALM content.
* FIX - Fixed issue with console error related to star field type on `popstate`.
* FIX - Fixed issue where star rating field would not reset when removing a star filter.
* UPDATE - Added Category and Tags to Taxonomy filters. Ccategory and tags can now be filtered via Taxonomy query if required.

= 1.1.6 - September 10, 2020 =

**UPGRADE NOTICE**
Users upgrading to WooCommerce add-on version 1.0.2 must also update core Ajax Load More to `5.3.8`. Failure to update will result is broken functionality.

**Add-on Updates**

Filters

* HOTFIX - Fixed issue with PHP warning messages being displayed in WP 5.5+ warning about REST API issues when `WP_DEBUG` is `true`.

Paging

* UPDATE - Updated `First`/`Last` navigation buttons to only display when required. Previously, First and Last may show even though users can access the first and last pages via button navigation.

WooCommerce

* NEW - Added support for [WOOF Filters](https://wordpress.org/plugins/woocommerce-products-filter/) and [Advanced AJAX Product Filters](https://wordpress.org/plugins/woocommerce-ajax-filters/) via Ajax Load More `reset` method.
* UPDATE - Updated the method ALM sets global WooCommerce configuration data. Previously, data was set in a window scoped JS variable. I've since moved this logic into JSON styled data atributes to allow for dynamic re-rendering of ALM during a filter.
* UPDATE - Adding WooCommerce `WC requires` and `WC tested up to` meta parameters.
* UPDATE - Updated the `alm_woocommerce_pagination_class` filter to remove the `.` before the classname.
* UPDATE - Updated WooCommerce JavaScript to improve performance.

= 1.1.5 - July 13, 2020 =

**Add-on Updates**

Filters

* NEW - Added new Star Rating field for allowing users to query by start rating custom fields.
* NEW - Added new optional description field for filter block.
* UPDATE - Added new PHP Output option. Filters can now can be added via PHP Array and not a shortcode. Click the `Generate PHP` button in the Shortcode Output sidebar.
* UPDATE - Admin UI/UX updates for a better experince building filters.

= 1.1.4 - June 29, 2020 =

**Add-on Updates**

Cache

* FIX - Fixed issue with 'Delete cache when new posts are published.` setting causing issues for Editor, Author roles saving and creating posts.

Filters

* NEW - Added `Reset` button to Range Slider field type. Users can now reset the Range Slider to the default values after filtering.
* UPDATE - Added support for Masonry `transition` and updating the paging URLs when using Filters add-on.
* UPDATE - Improved the stability of the scroll to post functionality when loading a paged URL.
* FIX - Fixed IE11 issue with keyboard navigation of radio buttons.
* FIX - Fixed IE11 issue where Range Slider was not triggering a change event.

Next Page

* FIX - Fixed issue with querystrings in paging URLs when using Next Page + Cache add-ons.

= 1.1.3 - June 12, 2020 =

**Add-on Updates**

Cache

* NEW - Added support for caching results with the [Filters add-on](https://connekthq.com/plugins/ajax-load-more/add-ons/filters/)
* NEW - Added support for nested cache directories for when caching [Filters](https://connekthq.com/plugins/ajax-load-more/add-ons/filters/) or [WooCommerce](https://connekthq.com/plugins/ajax-load-more/add-ons/woocommerce/) add-on results.
* UPDATE - Updated warning before deleting a cache directory. Users are now instructed as to which specific directory is up for deletion.
* FIX - Fixed PHP warning message about undefined index 0 on ALM Cache admin page.

Filters

* UPDATE - Added support for spacerbar keydown event to trigger events when using radio or checkbox field types.
* UPDATE - Added support for arrow keys to traverse the radio groups and act more like native radio buttons.
* FIX - Fixed issue on setting page not displaying filter preview
* FIX - Pushed fix for issue where hitting a paged URL would not send the user to the top of the current page.

= 1.1.2 - May 29, 2020 =

**Add-on Updates**

WooCommerce

* NEW - Added new cache API for caching Single Posts using the new implementation method. This new API will allow for more robust caching options in the future.

= 1.1.1 - May 1, 2020 =

**Add-on Updates**

Cache

* NEW - Added new cache API for caching Single Posts using the new implementation method. This new API will allow for more robust caching options in the future.

Next Page

* FIX - Fixed issue with push/popstate when using Nextpage and Paging add-ons.
* FIX - Fixed issue with Google Analytics integration sending a second pageview on page load.
* UPDATE - Set default `nextpage_scroll` shortcode parameter to false for better UX and accessibility.
* UPDATE - Disabled `Scroll to Page` functionality if Paging add-on is active. Use Paging add-on to scroll user to the top of the page.

Paging

* NEW - Added new Scroll to Top functionality for after a paging click event.
* FIX - Resolved issue with loading transition not displaying properly in some instances.
* FIX - Added CSS fix for loading issue when Paging CSS is not loaded inline.

Search Engine Optimization

* UPDATE - Updated default setting for `Scroll to Page` to false for better UX and accessibility.

= 1.1.0 - April 22, 2020 =

* NEW - Added new [WooCommerce Add-on](https://connekthq.com/plugins/ajax-load-more/add-ons/woocommerce/). This add-on will need to be enabled in Ajax Load More > Pro in your WordPress admin.

**Add-on Updates**

Layouts

* NEW - Added new 4 column grid for each layout.

Next Page

* FIX - Fixed issue with [Results Text](https://connekthq.com/plugins/ajax-load-more/docs/results-text/) not working when using ALM `id` parameter.

Single Posts

* FIX - Fixed issue with anchor links taking user to the top of the page.
* FIX - Fixed issue with encoded characters in post titles.
* UPDATE - Added support for Yoast page titles.

= 1.0.26 - March 18, 2020 =

**Add-on Updates**

FILTERS

## UPGRADE NOTICE

This version of Filters updates the markup of Radio and Checkbox filed types.
For accessibility purposes, the `<a href="#"/>` has been replaced with clickable `<div/>` elements.

Users who are using custom CSS to style these elements may need to update their markup for these elements.

* UPDATE - Improved Radio and Checkbox accessibility by adding aria role, checked and labelledby attributes.
* FIX - Fixed issue with filter loading animation not working as intended.

Layouts

* NEW - Added new Blog Card #3 layout.
* UPDATE - Updated various template styles.
* UPDATE - Only load .min CSS file when `WP_DEBUG` is `true`.
* UPDATE - Convert to webpack for quicker build iterations.

Single Posts

* NEW - Updated implementation method.
* NEW - Single Posts now longer requires Repeater Template when using the new [implementation method](https://connekthq.com/plugins/ajax-load-more/add-ons/single-posts/#implementation).
* NEW - Added new `single_post_target` shortcode parameters as part of the implementation update.

= 1.0.25 - February 24, 2020 =
**Add-on Updates**

Filters

* NEW - Added Range Slider field type using [noUiSlider](https://refreshless.com/nouislider/). This field type is considered to be in beta but is fully functional for querying by custom field ranges using the `BETWEEN` compare operator.
* NEW - Added new `alm_filters_{$id}_{key}_label` filter to allow for filtering of the default label in select and textfield field types.
* UPDATE - Updated time of filter transitions. In some cases users were reporting issues of double clicks causing no results to be returned.

Next Page

* NEW - Added `alm_nextpage_paged` filter hook that allows users to stop the loading of previous pages when hitting a paged URL.
* FIX - Fixed issue with paged URLs if an incorrect value was entered in `nextpage-scroll` shortcode parameter.

= 1.0.24 - January 25, 2020 =
**Add-on Updates**

Theme Repeaters

* UPDATE - Updated HTML layout of Theme Repeater selection in shortcode builder.

= 1.0.23 - December 17, 2019 =
**Add-on Updates**

Filters

* FIX - Fixed issue in `almfilters.start()` function not initiating properly when loaded via Ajax.
* NEW - Adding separate `filters.min` JS file for easier debugging.

Next Page

* FIX - Fixed issue with custom ACF Blocks for Gutenberg not displaying in Ajax requests.

= 1.0.22 - December 6, 2019 =
**Add-on Updates**

Filters

* FIX - Fixed issue with `filters_scrolltop` parameter not being maintained on scroll.
* UPDATE - Filters admin UI updates and tweaks.

Custom Repeaters

* NEW - Added `CTRL+S` and `CMD+S` support for saving Repeater Templates in the Ajax Load More admin :)
* UPDATE - Admin UI updates and tweaks for template display.

= 1.0.21 - November 18, 2019 =
**Add-on Updates**

Filters

* NEW - Added new feature to add a `Toggle All` option to the Checkbox field type. Users can select/unselect all options with a single click.
* NEW - Added support for custom taxonomy and tag queries on front pages, home pages and archive templates. Previously, if a user shared a filters URL they would redircted to the archive URL.
* FIX - Fixed issue with tag\_\_and and tag filtering causing duplicates in some instances.
* FIX - Fixed issue with filters not starting when initiated via Ajax.
* FIX - Remove JS error that could occur when using [Selected Filters](;;filtertoallowforfilteringofthedefaultlabelinselectandtextfieldfieldtypes.UPDATEUpdatedtimeoffiltertransitions.Insomecasesuserswerereportingissuesofdoubleclickscausingnoresultstobereturned.NextPageNEWAdded`alm_nextpage_paged`filterhookthatallowsuserstostoptheloadingofpreviouspageswhenhittingapagedURL.FIXFixedissuewithpagedURLsifanincorrectvaluewasenteredin`nextpage-scroll`shortcodeparameter.1.0.24January25,2020**Add-onUpdates**ThemeRepeatersUPDATEUpdatedHTMLlayoutofThemeRepeaterselectioninshortcodebuilder.1.0.23December17,2019**Add-onUpdates**FiltersFIXFixedissuein`almfilters.startfunctionnotinitiatingproperlywhenloadedviaAjax.NEWAddingseparate`filters.min`JSfileforeasierdebugging.NextPageFIXFixedissuewithcustomACFBlocksforGutenbergnotdisplayinginAjaxrequests.1.0.22December6,2019**Add-onUpdates**FiltersFIXFixedissuewith`filters_scrolltop`parameternotbeingmaintainedonscroll.UPDATEFiltersadminUIupdatesandtweaks.CustomRepeatersNEWAdded`CTRL+S`and`CMD+S`supportforsavingRepeaterTemplatesintheAjaxLoadMoreadminfiltertoallowforfilteringofthedefaultlabelinselectandtextfieldfieldtypes.UPDATEUpdatedtimeoffiltertransitions.Insomecasesuserswerereportingissuesofdoubleclickscausingnoresultstobereturned.NextPageNEWAdded`alm_nextpage_paged`filterhookthatallowsuserstostoptheloadingofpreviouspageswhenhittingapagedURL.FIXFixedissuewithpagedURLsifanincorrectvaluewasenteredin`nextpage-scroll`shortcodeparameter.1.0.24January25,2020**Add-onUpdates**ThemeRepeatersUPDATEUpdatedHTMLlayoutofThemeRepeaterselectioninshortcodebuilder.1.0.23December17,2019**Add-onUpdates**FiltersFIXFixedissuein`almfilters.startfunctionnotinitiatingproperlywhenloadedviaAjax.NEWAddingseparate`filters.min`JSfileforeasierdebugging.NextPageFIXFixedissuewithcustomACFBlocksforGutenbergnotdisplayinginAjaxrequests.1.0.22December6,2019**Add-onUpdates**FiltersFIXFixedissuewith`filters_scrolltop`parameternotbeingmaintainedonscroll.UPDATEFiltersadminUIupdatesandtweaks.CustomRepeatersNEWAdded`CTRL+S`and`CMD+S`supportforsavingRepeaterTemplatesintheAjaxLoadMoreadminhttps://connekthq.com/plugins/ajax-load-more/add-ons/filters/selected-filters/) and the value does not exist.

Comments

* FIX - Fixed issue with using Paging, Preloaded and Comments together would cause pagination to be off by a page.
* UPDATE - Removed legacy REST API route

= 1.0.20 - October 22, 2019 =
**Add-on Updates**

Filters

* NEW - Added date picker field type using [FlatpickrJS](;;https://flatpickr.js.org/) This field type is considered to be in beta but is fully functional for querying by custom field dates.
* NEW - Added new `almfilters.start()` public function to init filters.
* NEW - Added ability to add custom classnames to each filter block.

= 1.0.19 - October 1, 2019 =
**Add-on Updates**

Filters

* NEW - Added new [filter hook](;;https://connekthq.com/plugins/ajax-load-more/docs/add-ons/filters/#filter-hooks) to allow for custom ordering of Author, Category, Tag and Custom Taxonomy term listings. `alm_filters_taxonomy_test_actor_args`
* NEW - Added new `almfilters.resetFilter(key)` [public function](;;https://connekthq.com/plugins/ajax-load-more/docs/add-ons/filters/#public-functions) that allows for resetting a specific filterback to it's default state.
* UPDATE - Improved the HTML layout of child terms when using category, tags or taxonomy filters. Child terms are now displayed in their own `<ul/>` wrapper nested inside the parent term.
* FIX - Fixed issue with filtering a `meta_value` with a value of `0`. It was returning false instead of a numeric value.
* FIX - Fixed JS error that may appear when using back/fwd buttons to modify a filter. This only affected the Multi-Select field type.
* FIX - Fixed issue with Filters and Paging adding paged URLs(?pg=3) even if set to `false` in the shortcode.

Paging

* FIX - Fixed issue with pagination not showing in some instances when used with Filters Addon.
* FIX - Fixed issue with Filters and Paging adding paged URLs(?pg=3) even if set to `false` in the shortcode.

Single Posts

* NEW - Added option to display a reading progress bar. `single_post_progress_bar` is the shortcode parameter - please view the Shortcode Builder for implementation help.
* UPDATE - Updated default `Scroll to Post` behaviour setting to false.

= 1.0.18 - August 16, 2019 =
**Add-on Updates**

Paging

* FIX - Fixed issue where posts would not appear if `posts_per_page` value was greater than the amount of posts returned.

= 1.0.17 - August 15, 2019 =
**Add-on Updates**

Filters

* NEW - Added new `almFiltersActive` callback function. This callback function contains an object of the currently active filters.
* NEW - Added new `filters_url` parameter that can disable URL rewrites preventing the browser querystring from being updated when filters are modified. e.g. `filters_url="false"`
* NEW - Added `<noscript/>` support for Filters Addon.
* UPDATE - Updated Preselected filter value functionality to remove URL parameters when all Preselected options are selected.

= 1.0.16 - August 6, 2019 =
**Add-on Updates**

SEO

* FIX - Fixed issue with `alm_seo_leading_slash` filter not working in some situations. [Docs](https://connekthq.com/plugins/ajax-load-more/docs/add-ons/search-engine-optimization/#alm_seo_leading_slash)

Paging

* NEW - Added Last and First page buttons.
* NEW - Added controls (shortcode parameters) to change the Next and Prev button label.
* UPDATE - Added links URLs to Next, Prev, Last and First buttons.
* UPDATE - Smoother transitions between pages.
* FIX - Fixed issue where addon would throw an error when filtering with zero results.

Users

* Added new filter `alm_users_query_args_$id` filter to modify the core `WP_User_Query` used by Ajax Load More. [Docs](https://connekthq.com/plugins/ajax-load-more/docs/add-ons/users/).

= 1.0.15 - June 26, 2019 =
**Add-on Updates**

Filters
= 1.7.3 = June 26, 2019 =

* FIX - Fixed issue where `title` HTML markup was being rendered even if empty.
* FIX - Fixed an issue with UTM (querystring) parameters being treated as meta query parameters.
* FIX - Fixed issue with fatal error after filter deletion.
* UPDATE - Improved debug information.

= 1.0.14 - May 29, 2019 =

This updates relate to fixes applied in Ajax Load More `5.1.1`

**Add-on Updates**

Single Posts

* FIX - Added fix for browser popstate issue in core Ajax Load More.

Next Page

* FIX - Added fix for browser popstate issue in core Ajax Load More.

SEO

* FIX - Added fix for browser popstate issue in core Ajax Load More.

= 1.0.13 - May 13, 2019 =
**Add-on Updates**

Filters

* NEW - Added Sort (`sort`) filter. This new filter, combines order & orderby parameters into a single filter. Perfect for WooCommerce users who want to sort products `Lowest to Highest` or `Highest to Lowest`.
* NEW - Added Multi select field type (`<select multiple/>`) support.
* NEW - Added instructional text inside the admin to help users better understand the functionality of each Filter Key.

= 1.0.12 - May 7, 2019 =
Please make sure to update to Ajax Load More 5.1.0 if you run this ALM Pro upgrade. There are breaking changes that require the latest plugin.

**Add-on Updates**

Comments

* NEW - Comments add-on now uses the REST API for Ajax queries. This change can be reverted in ALM Settings.
* NEW - Added custom JavaScript fix for reply links in Comment query causing page refresh.
* FIX - Fixed issue with `offset` parameter not working correctly.

Nextpage

* NEW - Next Page add-on now uses the REST API for Ajax queries. This change can be reverted in ALM Settings.
* NEW - Added pagination in a `<noscript/>` tag for SEO and users without JS enabled.

Single Posts

* NEW - Single Posts add-on now uses the REST API for Ajax queries. This change can be reverted in ALM Settings.
* FIX - Added fix to suppress PHP warning messages about WP_Query parameters.

Users

* NEW - Users add-on now uses the REST API for Ajax queries. This change can be reverted in ALM Settings.
* NEW - Added support for Paging + Users addon.
* FIX - Fixed fatal error that can occur if core Ajax Load More is deactivated and the add-on remains active.

Filters

* NEW - Added support for nested (hierarchal) display for taxonomy, custom fields, tags and categories.
* NEW - Adding `AND` operator for Taxonomy queries.
* NEW - Added new `alm_filters_{id}_{key}_title` filter which will allow users to customize & localize filter group titles. FTI - Better localization support for string is coming soon.
* NEW - Adding support for pre-selected values and checkbox field type.
* FIX - Fixed issue where the textfield field type was not able to set Meta Query or Tax Query data.
* FIX - Fixed issue with `almFiltersClear` function not clearing <select/> values.
* FIX - Fixed issue with paging URLs when using Filters + Paging add-ons.

Layouts

* FIX - Fixed fatal error that can occur if core Ajax Load More is deactivated and the add-on remains active.

Call to Actions

* FIX - Fixed fatal error that can occur if core Ajax Load More is deactivated and the add-on remains active.

Custom Repeaters

* FIX - Fixed fatal error that can occur if core Ajax Load More is deactivated and the add-on remains active.

Theme Repeaters

* FIX - Fixed fatal error that can occur if core Ajax Load More is deactivated and the add-on remains active.

Preloaded

* FIX - Fixed fatal error that can occur if core Ajax Load More is deactivated and the add-on remains active.

= 1.0.11 - May 2, 2019 =

**Add-on Updates**

Paging

* FIX - Fixed issue with zero paging results failing to complete Ajax request or trigger callback functions.
* FIX - Fixed issue with undefined variable in paging URLs when using SEO add-on.

= 1.0.10 - March 20, 2019 =

**Add-on Updates**
Paging

* FIX - Fixed another issue with ALM failing to load if only one page of results was returned.

= 1.0.9 - March 19, 2019 =

**Add-on Updates**

Paging

* FIX - Fixed issue with ALM failing to load if only page of results was returned.

= 1.0.8 - March 8, 2019 =

## UPGRADE NOTICE

When updating to Pro 1.0.8 you must also update core (Ajax Load More)[https://wordpress.org/plugins/ajax-load-more/] to version 5.0.

**Add-on Updates**

Filters

* NEW - Adding paging URL parameters to allow for deep linking to paded results. `website.com/blog/?pg=3`. Paging URLs can be turned off in the shortcode `filters_paging="false"`.
* NEW - Added new `filters_scroll` shortcode parameter to allow for scrolling user to top of the listing after a filter action. By default this is set to false.
* FIX - Fixed bug where [almFiltersChange](https://connekthq.com/plugins/ajax-load-more/docs/add-ons/filters/#almFiltersChange) callback function was not triggering.
* FIX - Added polyfills for supporting IE10 and 11.
* FIX - Added fix for popstate error of `Cannot read property 'indexOf' of undefined` function when setting current filters.
* UPDATE - Update `almFilter` function in favour of new `ajaxloadmore.filters` function.
* UPDATE - Removed shorthand array `[]` sysntax for users on PHP version < 5.4.

Single Posts

* NEW - Adding translation file.
* NEW - Added update to allow for offsetting first post. Users can now set `offset="1"` in the `[ajax_load_more]` shortcode and render a custom single template before Ajax Load More.
* NEW - Added new `getOffset` function to more reliably get the correct permalink URL while scrolling.
* UPDATE - Update Google Analytics Tracking script.
* UPDATE - Removed Scroll Speed function and it has been deprecated.
* UPDATE - Updated scroll to element function to use core ALM almScroll function.
* UPDATE - Setting `$wp_query->in_the_loop` and `$wp_query->is_feed` to true for allowing various 3rd plugins to run hooks ion Ajax content.
* FIX - Fixed a bug where the browser URL would not update if a user clicked the back or forward browser buttons.

SEO

* NEW - SEO URLS now hold the original querystring values as new pages are loaded.
* NEW - Plugin completely re-written in vanilla JS.
* FIX - Fixed IE11 error with `append()` function.
* UPDATE - Removed jQuery dependancy.
* UPDATE - Removed Scroll Speed setting as it is no longer used.

Next Page

* NEW - Added new `alm_nextpage_split_{id}` filter hook that provides the ability to generate dynamic content breaks. This will allow users to automatically split content into pages at specific HTML tags and not rely on `<!--nextpage--> short tags. See (Next Page docs)[https://connekthq.com/plugins/ajax-load-more/docs/add-ons/next-page/#filter-hooks] for more info.
* NEW - Plugin re-written in vanilla JS without jQuery dependency.
* NEW - Querystring values are now maintained through all page loads.
* UPDATE - Removed Scroll Speed shortcode parameter. Scroll speed is now a global ALM variable.

Paging

* NEW - Plugin completely re-written in vanilla JS.
* UPDATE - Updated callback function to be window scoped.
* NEW - Adding translation file.

Comments

* FIX - Fixed issue with preloaded comments not working because of an incorrectly `comments_post_id`.

= 1.0.7 - February 4, 2019 =

**Add-on Updates**

Filters

* Updated to 1.6.4
* FIX - Fixed issue with `alm_filters_{id}_{key}_default` & `alm_filters_{id}_{key}_selected` filters not triggering correctly with Taxonomy and Meta Query

= 1.0.6 - January 19, 2019 =

**Add-on Updates**

Previous Post

* Updated to 1.3.1
* FIX - Fix for `$` functions causing JavaScript errors for some users.

= 1.0.5 - January 15, 2019 =

**Add-on Updates**

Previous Post

* Updated to 1.3.0
* UPGRADE NOTICE - If you run this update you must also update ALM core to 4.2.0.
* NEW - Added support for load order single posts by Previous, Next, Latest and ID.
* UPDATE - Renamed add-on to `Single Post` from `Previous Post`.
* UPDATE - Adding strip_tags function to page title

= 1.0.4 - December 28, 2018 =

**Add-on Updates**

Filters

* Updated to 1.6.3
* FIX - Fixed issue with custom taxonomy term values not being selected on page load.
* FIX - Fixed with saving of filter data in WordPress admin. On some servers the data being passed was being rejected by the REST API as the data was not being sent as JSON.

Cache

* Updated to 1.6.0
* NEW - Added functionality to auto-generate ALM Cache - [view docs](https://connekthq.com/plugins/ajax-load-more/docs/add-ons/cache/#auto-generate).
* NEW - Added new `do_action('alm_clear_cache')` function to clear the entire cache. This allows developers to run custom cache clear scripts.
* NEW - Added uninstaller script to remove `alm-cache` directory on plugin deletion.
* UPDATE - Removing `mkdir()` functions in favor of core WP function `wp_mkdir_p()` which helps with permission issues on directory creation.

= 1.0.3 - December 13, 2018 =

**Add-on Updates**

Filters

* Updated to 1.6.2

= 1.0.2 - December 6, 2018 =

* FIX - Fixed PHP warning messages that would appear if core Ajax Load More was uninstalled. Pro references functions in Ajax Load More so I have wrapped those function call in `function_exists` methods.

**Add-on Updates**

Filters

* Updated to 1.6.1

Next Page

* Updated to 1.2.0

Paging

* Updated to 1.3.6

SEO

* Updated to 1.8.1

= 1.0.1 - November 10, 2018 =

* FIX - Fixed issue with 500 error if core Ajax Load More was deactivated when Pro was activated.
* NEW - Adding readme and changelog.

**Add-on Updates**

Custom Repeaters

* Updated to 2.5.2
* Fixed issue with activation script not running function to DB create tables for Custom Repeaters.

= 1.0 - November 3, 2018 =

* Initial Release
