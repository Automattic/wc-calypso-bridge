=== WooCommerce Calypso Bridge ===
Contributors: automattic, woothemes
Tags: woocommerce
Requires at least: 4.6
Tested up to: 4.9
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A feature plugin to provide ux enhancments for users of Store on WordPress.com.

== Description ==

This repository houses various fixes and extensions for wp-admin to enhance the experience for users of the WordPress.com Store.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress

== Changelog ==

= 1.0.2 =

* Various style fixes for Calypsoify + WooCommerce.
* Setup notices are now hidden for our bundled eCommerce plugins.
* Storefront Customizer + Powerpack defaults

= 1.0.1 =
* Calypsoify + WooCommerce: UI and CSS updates to make WooCommerce match Calypso.

= 1.0.0 =
* Renames `product/reviews` to `product/calypso-reviews` now that there is a core endpoint with the same name. There are differences between the two, so it needs to stick around until Calypso has been updated.
* Splits code into Store on WP.com handling, and new Atomic eCommerce functionality
* Adds Calypsoify compaibility for atomic stores

= 0.2.3 =
* Disable publicize on products created via the wc rest api

= 0.2.2 =
* `data/counts` endpoint added (moved from wc-api-dev)
* `products/reviews` endpoint added (moved from wc-api-dev)
* Sync currency info and update comment sync filter

= 0.2.1 =
* Fixed customizer tour
* Remove name and email from synced data list

= 0.2.0 =
* Removed WooCommerce Analytics logic which is now in Jetpack.
* Added deactivate hook to MailChimp plugin to truncate job tables

= 0.1.9 =
* Added the `woocommerce_email_footer_text` setting to batch email settings endpoint.

= 0.1.8 =
* Ensure WooCommerce Analytics does not get loaded when Jetpack v5.9 is shipped/installed

= 0.1.7 =
* Determine and sync to Jetpack what client created a product

= 0.1.6 =
* Bug fix for referrers in WooCommerce Analytics
* Removing email template over-rides

= 0.1.5 =
* Add WooCommerce Analytics Module
* This initial roll out will make its way to all jetpack sites
* Corresponding Jetpack pull request [here](https://github.com/Automattic/jetpack/pull/8296)

= 0.1.4 =
* Removed Redundant Currencies Controller
* Added endpoint to trigger sending order invoices
* Updated order email template to be current with WooCommerce Core
* Fixed bug in MailChimp API Controller

= 0.1.3 =
* Currencies Controller for returning WooCommerce Currency Data
* Email Groups controller for batch request of Email Settings

= 0.1.2 =
* Test Suite added
* BACS accounts added to payment-gateways API responses
* Rating added to Jetpack Sync comment meta whitelist

= 0.1.1 =
* Adding back the forgotten email templates

= 0.1.0 =
* Initial release
