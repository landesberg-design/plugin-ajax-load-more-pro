=== Ajax Load More: Elementor ===

Contributors: dcooney, connekthq;Contributors
Author: Connekt Media
Author; URI: https://connekthq.com/
Plugin URI: https://connekthq.com/plugins/ajax-load-more/add-ons/woocommerce/
Requires at least: 4.0
Tested up to: 6.3
Stable tag: trunk
Homepage: https://connekthq.com/
Version: 1.1.5

Infinite scroll Elementor widget content with Ajax Load More.

== Copyright ==
Copyright 2023 Connekt Media

This software is NOT to be distributed, but can be INCLUDED in WP themes: Premium or Contracted.
This software is distributed in the hope that it will be useful, but WITHOUT ANY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

== Description ==

The Elementor add-on provides functionality required for integrating with the Elementor Posts Widget.

http://connekthq.com/plugins/ajax-load-more/add-ons/elementor/

== Installation ==

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `ajax-load-more-elementor.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `ajax-load-more-elementor.zip`.
2. Extract the `ajax-load-more-elementor` directory to your computer.
3. Upload the `ajax-load-more-elementor` directory to the `/wp-content/plugins/` directory.
4. Ensure Ajax Load More is installed prior to activating the plugin.
5. Activate the plugin in the WP plugin dashboard.

== Changelog ==

= 1.1.5 - September 27, 2023 =
* UPDATE: Updated Elementor JavaScript to support new Google Analytics 4 implementation.


= 1.1.4 - June 11, 2023 =
* NEW: Added Elementor widget setting for `button_done_label` shortcode parameter.
* UPDATE: Updated to add compatibility with Cache Add-on 2.0 and Ajax Load More 6.0.
* UPDATE - Code cleanup.
* UPDATE - Elementor compatibility version bump and testing.
* FIX - Fix for PHP warning about undefined `cache` index.


= 1.1.3 - February 14, 2023 =
* FIX - Updated widget function to fix issues with function deprecation notices.


= 1.1.2 - July 8, 2021 =
* NEW - Added support for lazy loading images.
* UPDATE - Adding activation warning if core Ajax Load More is not installed when attempting to install add-on.
* UPDATE - Updated copy throughout Ajax Load More Elementor widget.
* UPDATE - Adding Elementor tested up to versioning for better update support.
* FIX - Fixed issue with network activation of Elementor Pro causing add-on to not activate.


= 1.1 - November 25, 2020 =

** UPGRADE NOTICE **
This update requires Ajax Load More version 5.5.1. This is a breaking change and will require widget updates.

We have updated the Elementor add-on settings for how Ajax Load More determines the next page of content during infinite scroll.
Ajax Load More no longer uses the `Numbers` pagination type, please follow the steps below to update your widget after update.

1. Visit your page in Elementor.
2. Locate the Posts Widget.
3. Update the `Pagination` type in the Posts Widget from `Numbers` to `Numbers + Previous/Next` or just `Previous/Next`.
4. View the [Guide](https://connekthq.com/plugins/ajax-load-more/add-ons/elementor/#configuration) if you require addtional information.

** OTHER UPDATES **

* NEW - Added integration for WooCommerce Product Widget.
* NEW - Added integration for Ajax Load More Cache.

= 1.0 - November 11, 2020 =

* Initial Release.eventheimpliedwarrantyofMERCHANTABILITYorFITNESSFORAPARTICULARPURPOSE.DescriptionTheElementoradd-onprovidesfunctionalityrequiredforintegratingwiththeElementorPostsWidget.httpeventheimpliedwarrantyofMERCHANTABILITYorFITNESSFORAPARTICULARPURPOSE.DescriptionTheElementoradd-onprovidesfunctionalityrequiredforintegratingwiththeElementorPostsWidget.http
