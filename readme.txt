=== WooCommerce Calypso Bridge ===
Contributors: automattic, woothemes
Tags: woocommerce
Requires at least: 4.6
Tested up to: 4.9
Stable tag: 2.1.0
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

= 2.1.0 =
* Update the hook we use to include additional task list options in Jetpack Sync #1084.
* Update to node 16 and update other dependencies #1088.
* Update unit tests after dependency updates #1094.

= 2.0.18 =
* Update _Add a domain_ task to check for domain purchases in addition to the site URL #1083.

= 2.0.17 =
* Update default progress title to "Welcome to your Woo Express store" #1071.
* Fix store name not rendering special characters #1070.
* Patch wc.data for Gutenberg 15.5+ and wc < 7.7.0 #1086.

= 2.0.16 =
* Add tracking on various free trial CTAs #1074

= 2.0.15 =
* Mark Store_Details task as complete for free trial #1061
* Fix site launch checks #1073.
* Hide site launch banner for eCommerce trials #1062.

= 2.0.14 =
* Make the free trial banner responsive #1066

= 2.0.13 =
* Remove the onboarding purchase task #1060.
* Add WooCommerce task list options to Jetpack Sync #1009.

= 2.0.12 =
* Redirect admin pages to the Calypso upgrade page for free trials #1055.

= 2.0.11 =
* Fix css conflict for snackbar #1041
* Add avalara plugin to Tax task #1032
* Remove default store notice #1053
* Remove homepage step from Personalize task for Tsubaki theme #1054.
* Increase WC Tracker frequency to run on a daily basis for the first 3 months #1050.

= 2.0.10 =
* Create navigation menus with new slugs #1039.

= 2.0.9 =
* Fix an issue with the WC Tracker #1034.

= 2.0.8 =
* Introduce site slug helper function #1025.
* Free trial: Avoid adding the Plugins menu for eCommerce trials #1027.

= 2.0.7 =
* Free trial: Update notice messages and other copies #1022.
* Add free trial plan picker banner #917
* Add wcadmin_free_trial_upgrade_now track for task_list and marketing sources #1023.
* Add wcadmin_free_trial_learn_more track #1024.

= 2.0.6 =
* Fix fatal error when trying to remove GC hidden menu items from Calypso menu #1018.

= 2.0.5 =
* Free trial: Use site title for domain suggestions #991.
* Prevent deletion of managed plugins (Avatax) #1012.
* Create navigation menu items #999.
* Remove PRL, BIS, and GC hidden menu items from Calypso menu #994.
* Add free trial host value for tracks #995.
* Use the theme color for completed task strikethrough #1006.
* Add free trial notice on WooCommerce Orders page #936.
* Change illustration in tasklist completed component #1007.
* Add task header for "Add a domain" task #1013.
* Add task header for "Launch your store" task #1015.

= 2.0.4 =
* Free Trial: Hide Tools > Marketing, Tools > Earn - Move Feedback under Jetpack #979.
* Free Trial: Introduce payment restrictions #930.
* Free trial: Replace Marketing page #984.
* Free trial: Introduce Extensions landing page - Hide Extensions > Manage #990.
* Update webpack config to import scss variables and mixins as a global import #988.
* Fixed wcpay customisation script error #989
* Customize homescreen title and progress header #987.


= 2.0.3 =
* Override the wc.experimental.useSlot hook #986.
* Bring Add a domain task back for free trial #985.
* Hides the Launch task for WooExpress sites #937.
* Remove absolute path prefix from My Home and Customer menu URLs #974.
* Fix woocommerce payments task #980.
* Fix incorrect SVG size #978.
* WC Payments customizations #977.
* Replace tax task to remove Avalara #975.
* Replace product task with custom completion logic #963.
* Customize payment tasklist header #956.
* Hide partial tasklist and tasks #951.
* Override orders empty state screen CTA button class #948.
* Add disabled tasks accordion component #940.
* Add task completion task #939.
* Add homescreen banner #933.
* Add payment task #919.

= 2.0.2 =
* Prevent deletion of managed plugins (AutomateWoo, FedEx Shipping) #969.
* Prevent a double footer issue when using Storefront in Ecommerce plan #970.

= 2.0.1 =
* Make plugin_asset_path a static prop #959.
* Fix conflict between woocommerce navigation and nav unification #952.

= 2.0.0 =
* Refactor and introduce plan detection controller #926.

= 1.9.18 =
* Arrange menu order for the menu items of Mailpoet and AutomateWoo #921.
* Remove Mailpoet Free and AutomateWoo from the managed plugins #921.

= 1.9.17 =
* Roll Product Recommendations into the Ecommerce Plan #910.
* Prevent deletion of new round of managed plugins (Product Recommendations, AutomateWoo, MailPoet Free) #912.

= 1.9.16 =
* Hide Customers menu item when analytics are disabled #914.

= 1.9.15 =
* Revert the default value of the ecommerce_new_woo_atomic_navigation_enabled filter #908.

= 1.9.14 =
* Fix a fatal error about WC_Calypso_Bridge_Helper_Functions in Business plan #903.
* Limit JS filters to Ecommerce plans to avoid unwanted menu highlights in Business plan #904.

= 1.9.13 =
* Enable the WooCommerce menu and remove the feature flag #899.
* Delete WooCommerce pages before recreating them (new sites only) #900.
* Run DB updates automatically without wp-admin notices #899.
* Fix deprecated function Loader::is_admin_or_embed_page() #884.

= 1.9.12 =
* Introduce add-a-domain and launch-your-store tasks in the setup list #879.
* Flatten the WooCommerce menu under a feature gate #879.

= 1.9.11 =
* Prevent deletion of new round of managed extensions #876.
* Rollback Tools > Earn menu item #874.
* Do not remove Jetpack Google Analytics module if it was enabled prior moving to Atomic #874.

= 1.9.10 =
* Rollback limiting available Jetpack Modules #870.

= 1.9.9 =
* Introduce locking to one time operations #865.
* Fix fatal error when pre-configuring Jetpack for Ecommerce Plan users #862.
* Disable activation notices for Back In Stock Notifications #860.
* Improve limiting activity panels to Woo Home page #858.

= 1.9.8 =
* Pre-configure Jetpack for Ecommerce Plan users #844.
* Create WooCommerce pages with a one-time operation #823.
* Delete wc-refund-returns-page inbox note, so it can be recreated with the correct refund page ID #823.
* Set default to block-based cart/checkout #823.
* Display note about the new block-based cart/checkout if the site is active for more than 2 days #823.
* Display wc-refund-returns-page inbox note if the site is active for more than 5 days #823.
* Hide write button in global bar #827.
* Prevent deletion of managed Extensions #846.
* Suppress wc-payments-notes-set-up-refund-policy inbox note #849.

= 1.9.7 =
* Revert: Hook `maybe_create_wc_pages` on `woocommerce_installed` #842.

= 1.9.6 =
* Hook `maybe_create_wc_pages` on `woocommerce_installed` #839.

= 1.9.5 =
* Enable logger to check woocommerce installation hooks #833.
* Disable welcome notices for Facebook for WooCommerce #819.
* Suppress inbox messages #821.
* Limit activity panels to Woo Home page #826.
* Clean up product table columns #829.

= 1.9.4 =
* Disable activation notices for Gift Cards and Product Bundles #813.
* Skip the Onboarding Profiler in the Ecommerce Plan #811 #816.
* Delete coupon management has moved notes #810.
* Remove legacy coupon menu #810.
* One-time operations controller #810.
* Revert the navigation experiment #807.

= 1.9.3 =

* Add host param to WC Tracker params #800
* Use pre_option_hook for woocommerce_allow_tracking #802

= 1.9.2 =

- Fix a deploy build bug with unintended import

= 1.9.1 =

* Adjust the header width when the sidebar is collapsed #793

= 1.9.0 =

* Fix layout header overflow issue on wp.com #791

= 1.8.9 =

* Fixes a PHP fatal error while running wc_calypso_bridge_daily cron job when WC is deactivated #77 

= 1.8.8 =

* Use feature check to determine eCommerce plan. #783

= 1.8.7 =

* Fix WCPay in core texts and promo slug #779

= 1.8.6 =

* Apply the WC Pay menu icon in CSS for the plugin #777

= 1.8.5 =

* Don't render WC Pay menu if WCA has it already #773

= 1.8.4 =

* Use plugins_loaded to register callback to register cron event #764
* Remove calls to old navigation note #765

= 1.8.3 =

* Override Nav styles after GB 11.6.0 changes #761

= 1.8.2 =

* Launch WCPay experiment in new countries #760
* Set woocommerce_navigation_enabled to yes on eCom plans #749

= 1.8.1 =

* WCPay in core experiment: expand new countries #755
* Add payments remind me later note #756
* WCPay temporarily remove new countries #757

= 1.8.0 =

* Fix fatals when WooCommerce is installed but not activated #724

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
