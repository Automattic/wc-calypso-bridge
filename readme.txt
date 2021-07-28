=== WooCommerce Calypso Bridge ===
Contributors: automattic, woothemes
Tags: woocommerce
Requires at least: 4.6
Tested up to: 4.9
Stable tag: 1.7.10
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A feature plugin to provide UX enhancements for users of Store on WordPress.com.

== Description ==

This repository houses various fixes and extensions for wp-admin to enhance the experience for users of the WordPress.com Store.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress

== Changelog ==

= 1.7.10 =

* Fix missing WCPay icon on ecom plan users #723

= 1.7.9 =

* Show WCPay promotion in eligible WooCommerce stores

= 1.7.8 =

* Fix overflow issue with new CardBody component #710

= 1.7.7 =

* Remove Canada Post notice suppression and header fix #682

= 1.7.6 =

* Apply fix only for desktop view #682

= 1.7.5 =

* Fix nav unification causing header overflow #680

= 1.7.4 =

* Fix WC pages bug on ecommerce plan #671

= 1.7.3 =

* Removed WC Page creation logic #672

= 1.7.2 =
* Fix WC pages bug on ecommerce plan #666

= 1.7.1 =
* Check and create WC pages if they are missing on a new installation #662
* Prevent Creative Mail and Crowdsignal Forms redirects when activating plugins #663

= 1.7.0 =
* Set back button in new navigation to be WordPress.com Dashboard #634
* Set survey source with wpcom plan #638
* Disable email based inbox notifications #641
* Add Get Support link to WordPress.com support page #650
* Remove filtered link in store manager order confirmation email #651

= 1.6.0 =

* Enable Navigation

= 1.5.1 =
* Atomic: Change how we check the plan data

= 1.5.0 =
* Onboarding: Hide CBD option
* Calypsoify: Use wc-admin header instead of calypsoify header
* Onboarding: Hide monthly/annual price toggle
* Onboarding: Update redirect logic to work with Woo 4.6

= 1.4.0 =
* Enable Home Screen for all users
* Prevent Crowdsignal Forms from redirecting

= 1.3.0 =
* Enable Remote Inbox Notifications

= 1.2.0 =
* Correct the menu path for analytics so that it shows in the main menu

= 1.1.9 =
* Remove redirects to wc-setup-checklist

= 1.1.8 =
* Make host prop conditional in tracks
* Remove Legacy OBW
* Enable Woo Core setup checklist

= 1.1.7 =
* Add connect logic for WooCommerce Payments
* Site Profiler: Remove non installed themes
* Site Profiler: Remove business extensions
* Enable new Site Profile Wizard
* Tweak address 2 toggle margins
* Add Travis configuration
* Opt ecom sites into tracks by default
* Fix debug warnings in admin checklist setup

= 1.1.6 =
* Add support for global `host` Tracks prop
* Register TaxJar settings page
* Register new Marketing Tab for Woo 4.1

= 1.1.5 =
* Update Manage Site link to redirect to WordPress.com MySites root.
* Force use of legacy obw to ensure woo pages are setup.

= 1.1.4 =
* Disable new setup checklist to prep for WooCommerce 4.0
* Added styling to hide WooCommerce Admin nav when in calypsoify view

= 1.1.3 =
* Improved compatibility with the new onboarding experience being tested in WooCommerce Core.
* Fixed search box display on the tax settings page.
* Fix the product importer layout and styles.

= 1.1.2 =
* Fix for OBW bug in Woo 3.8

= 1.1.1 =
* Bail early if Woo is not active or present

= 1.1.0 =
* decrease padding on table per page select in Calypso
* Add support for wc-admin layout
* Fix wc-admin onboarding layout and input issues
* Update menu item colors to match Calypso
* Override default WP navspan class styles
* Add calypsoify class to body tag
* Fix up card form styling in plugins
* use jQuery to copy WC Admin breadcrumbs to Calypso
* Add default payment gateway task
* Mark checklist complete if orders exist
* Setup: Ensure MailChimp does not redirect
* Check Jetpack dependencies before modifying the WooCommerce setup wizard
* Check for product count instead of click in checklist
* update readme with current dev environment set up instructions
* add WooCommerce Admin top level pages to bridge

= 1.0.17 =
* Update styles for WooCommerce setup.

= 1.0.16 =
* Ensure that redirect to Mailchimp for WooCommerce settings is canceled when visiting WooCommerce setup wizard (wc-setup).

= 1.0.14 =
* Fix for Core Woo REST controllers only being loaded on rest requests in 3.6 beta 1

= 1.0.11 =
* eCommerce Plan: Fix duplicated "manage your subscriptions" banner.

= 1.0.10 =
* eCommerce Plan: Fix blank state button size.
* eCommerce Plan: Forces the customizer setup checklist link to load the Starter Content.
* eCommerce Plan: Fix typo in method name.

= 1.0.9 =
* Fix for fatal errors that can occur when deactivating MailChimp on Store on WP.com stores.

= 1.0.8 =
* Add support links (with tracking) to the interface
* Redirect checklist to setup if the setup wizard hasn't ran yet
* Add testing instructions to README
* Fix some IE11 styles

= 1.0.7 =
* Storefront customizer setup nonce fix
* Minor mobile CSS fixes

= 1.0.6 =
* Further Calypsoify style fixes
* Fix load order for Powerpack code
* Some IE11 fixes
* Hides extensions from search results when already installed

= 1.0.5 =
* Even more Calypsoify style fixes
* Prevent WooCommerce deactivation on eCommerce plan sites
* Various notice fixing/removal/moving
* Flush rewrite rules during setup

= 1.0.4 =
* More Calypsoify style fixes
* Fix for setup checklist errors
* Fix for MailChimp activation

= 1.0.3 =
* Various style fixes for Calypsoify + WooCommerce.
* Text/string fixes.
* Fix for setup/calyspoify loading issue.

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
