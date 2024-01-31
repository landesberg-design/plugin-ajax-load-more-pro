=== Ajax Load More: Layouts ===

Contributors: dcooney
Author: Darren Cooney
Author URI: https://connekthq.com
Plugin URI: https://connekthq.com/ajax-load-more/add-ons/layouts/
Requires at least: 5.0
Tested up to: 6.4
Stable tag: trunk
Homepage: https://connekthq.com
Version: 2.1.0

== Copyright ==
Copyright 2024 Connekt Media

This software is NOT to be distributed, but can be INCLUDED in WP themes: Premium or Contracted.
This software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

== Description ==

= Predefined layouts for your repeater templates =

The Layouts add-on provides a collection of unique, well designed and fully responsive templates.

https://connekthq.com/plugins/ajax-load-more/add-ons/layouts/

== Installation ==

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `ajax-load-more-layouts.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `ajax-load-more-layouts.zip`
2. Extract the `ajax-load-more-layouts.zip` to your computer
3. Upload the `ajax-load-more-layouts` directory to the `/wp-content/plugins/` directory
4. Ensure Ajax Load More is installed prior to activating the repeater plugin
5. Activate the plugin in the Plugin dashboard


== Changelog ==

= 2.1.0 - January 16, 2024 =
* UPGRADE NOTICE: This update is affected by the core Ajax Load More 7.0 release. Updating this plugin will require updating Ajax Load More to 7.0.
* UPDATE: Updated layouts CSS and HTML to remove all references to `alm-reveal` divs.
* UPDATE: Code cleanup and oragnization.


= 2.0.2 - July 27, 2023=
* UPDATE: Updated layout listing design and display.
* UPDATE: Updated various elements to match new core Ajax Load More 6.1 admin styling.


= 2.0.1 - February 14, 2023 =
* UPDATE - Updated the exported CSS of default layout to allow for image sizes of any dimensions.
* UPDATE - Added support for Paging addon and new layouts.
* UPDATE - Added uniform spacing below listing and load more button.
* FIX - Added missing Gallery layout to layout listing.
* FIX - Fixed issue with gap spacing and load more elements.
* FIX - Various spacing and alignment tweaks using minmax for columns.


= 2.0 - January 5, 2023 =
UPGRADE NOTICE: Layouts has been completely rebuilt and now uses CSS Grid for layout and additional shortcode parameters for configuration.

* UPDATE: Replaced flexbox layout with CSS Grid.
* UPDATE: Added new Layout shortcode parameters to style the ALM container. `[ajax_load_more layout="true" layouts_cols="2"]`
* [See Docs](https://connekthq.com/plugins/ajax-load-more/add-ons/layouts/) for more information.

= 1.3.1 - April 22, 2020 =
* NEW - Added new 4 column grid for each layout.


= 1.3.0 - March 18, 2020 =
* NEW - Added new Blog Card #3 layout.
* UPDATE - Updated various template styles.
* UPDATE - Only load .min CSS file when `WP_DEBUG` is `true`.
* UPDATE - Convert to webpack for quicker build iterations.


= 1.2.2 - May 6, 2019 =
* FIX - Fixed issue if core Ajax Load More is deactivated the add-on will throw a fatal error because of undefined methods.


= 1.2.1 - February 8, 2018 =
* UPDATE - Updating Layouts CSS to be loaded inline if selected.
* UPDATE - Removing deprecated vendor files.


= 1.2 - May 29, 2017 =
- *NEW - Adding new layout, [Blog Card #2](https://connekthq.com/plugins/ajax-load-more/add-ons/layouts/#layout-blog-card-2)


= 1.1 - May 16, 2017 =
- * NEW - Adding new Blog Card layout.
- * UPDATE - Cleaning up CSS and markup of some layouts.
- * UPDATE - Updating plugin updater script.
- * FIX - Adding a fix for issues while saving template with the Gallery layout.


= 1.0.2 =
* FIX - Updated class issue with Card Flip, 3 column layout.


= 1.0.1 =
* FIX - Update for Undefined Index warning if WP_DEBUG is set to true.


= 1.0 =
* Initial plugin release
