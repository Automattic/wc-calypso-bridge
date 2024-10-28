=== WooCommerce Calypso Bridge ===
Contributors: automattic, woothemes
Tags: woocommerce
Requires at least: 4.6
Tested up to: 4.9
Stable tag: 2.5.2
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

= Unreleased =

= 2.8.1 =
* Add optional check and fix button deprecated parameters #1527
* Allow WooCommerce Services to co-exist with WooCommerce Shipping to provide tax functionality only
* Fix coming soon unintentionally expose the rest of the site #1529

= 2.8.0 =
* Move "composer/installers" package to require-dev. #1513

= 2.7.1 =
* Remove LYS badge even when feature flag is disabled #1523

= 2.7.0 =
* Re-enable Site visibility settings tab for free trial plans #1512
* Remove launch-your-store feature flag override #1521
* Remove LYS badge from WPCOM sites #1519
* Remove setting demo store option on ecommerce sites by default #1518

= 2.6.0 =
* Hide WPCOM's coming soon page when the launch-your-store feature flag is enabled #1500
* Exclude LYS coming soon page for WPCOM share link #1501
* Sync WPCOM coming soon status to LYS #1502
* Add sync coming soon status from LYS to WPCOM #1503
* Refactor LYS to use unidirectional data flow #1506
* Disable launch your store on trial sites #1507
* Add conditional check to replace launch-site task with LYS task #1509
* Fix logic for disabling lys task on trial sites #1511

= 2.5.5 =
* Add a new class to customize for Stripe from Partner Aware Onboarding #1492

= 2.5.4 =
Add a new class to customize for PayPal from Partner Aware Onboarding #1491

= 2.5.3 =
* Entrepreneur Trial: Fix Entrepreneur trial plan Welcome note

= 2.5.2 =
* eCommerce Signup Flow: Add the "Welcome" note back #1484
* Fix homepage crashing with WooCommerce 9.0.0 #1483
* Moved logic to ensure launch-your-store feature is disabled in all plans #1485
* Implement is_complete for the square task #1486

= 2.5.1 =
* Fix broken image on Woo launchpad header #1481
* Ensure i18n loader is preserved in the production build #1480

= 2.5.0 =
* i18n: Load script translations
* Admin Menu: Fix order of Jetpack submenu pages

= 2.4.2 =
* Redirect to Woo My Home for Enterpreneur sites.

= 2.4.1 =
* eCommerce Signup Flow: Update eCommerce plan Welcome note

= 2.4.0 =
* Make Entrepreneur and Entrepreneur trial plans navigate to Calypso's home when "My Home" sidebar menu item is selected

= 2.3.15 =
* Open AI Woo store builder to all sites on WPCOM Entrepreneur plan

= 2.3.14 =
* Deactivate WooCommerce Services if either Woo Shipping or Woo Tax is active on an ecommerce-related plan (on WPCOM and Woo Express, including trial plans) #1458

= 2.3.13 =
* Fix the duplicate My Home menu in Ecommerce Admin Menu

= 2.3.12 =
* Update DataSourcePoller import since after refactor in core #1450
* Force launch-your-store feature flag to false #1450

= 2.3.11 =
* Fix the Woo Express navigation is missing when the wpcom_is_nav_redesign_enabled is enabled #1449

= 2.3.10 =
* Force square_cash_app_pay and square_credit_card order on the payment settings page -- follow up issue #1447

= 2.3.9 =
* Force square_cash_app_pay and square_credit_card order on the payment settings page #1445

= 2.3.8 =
* Update Square task copy changes #1443

= 2.3.7 =
* Improve Partner Aware Onboarding customiations #1441

= 2.3.6 =
* This PR removes unintended space before the php tag, which was adding a space to the JSON endpoint and resulting in invalid JSON #1439

= 2.3.5 =
* Add a new class to customize for Square from Partner Aware Onboarding #1426
* Remove the Customizer from the admin menu and the admin bar, if a block theme is used #1433
* Delete woocommerce_demo_store option to hide the demo store notice #1433

= 2.3.4 =
* Deactivate TikTok for WooCommerce if both TikTok for WooCommerce and Business are active #1430
* Change tracks calypso_wooexpress_one_dollar_offer to use calypso analytics #1434

= 2.3.3 =
* Replace hardcoded HTML with the wc_print_notice helper in the free trial's checkout notice #1424
* Fire calypso_wooexpress_one_dollar_offer on Woo Home when there is $1 dollar offer #1398
* Bring back choose your theme for stores ineligible for CYS #1431

= 2.3.2 =
* Fix free trial banner missing border #1421
* Remove appearance task and handle unregister plugin JS #1399
* Update payment gateways info notice for free trial users in countries that do not support Woo Payments #1415
* Remove "Notify me of new posts by email" checkbox in reviews section #1420
* Hide Payments tab for countries that do not support Woo Payments #1403
* Page creation job - Sanitize slugs before deleting #1401
* Rename "Blog" frontend navigation item to "News" #1400
* Made the domain purchase note unactioned #1396
* Made the domain purchase note show up within 1 hour after store creation #1396
* Made the domain purchase unread for users who just upgraded to a paid plan #1396


= 2.3.1 =
* Render introductory offer banner on Woo Home #1397
* Force remove "Need help?" spotlight in tasklist #1417

= 2.3.0 =
* Add tracks for homepage views for CYS #1390
* Revert #1377 and fix init priority, to avoid the empty tab being added to the product data tabs #1395
* Hide Jetpack JITM in CYS screen #1393
* Enable customize-store feature flag #1357
* Hide free trial plan picker banner when viewing iframe #1404

= 2.2.26 =
* Fix missing free trial banner in orders page #1371
* Override task progress header and title for all Woo Express sites #1372
* Remove the empty Product Data > Get more options tab #1377
* With HPOS enabled, the empty state CTA button should be secondary if a primary button exists #1381
* Fix free trial banner upgrade now button class #1379
* Add woocommerce_admin_customize_store_completed_theme_id option to Jetpack sync #1384

= 2.2.25 =
* Mitigate object cache issue while creating Woo related pages #1368
* Remove code defaulting to cart/checkout blocks #1368

= 2.2.24 =
* Add smart shipping defaults to all countries for Woo Express sites #1276
* Hide "Choose your theme" task when customize-store feature flag is enabled #1356
* Fix Woo Express identifier for free trials Remote Inbox Notifications #1363

= 2.2.23 =
* Add parameter to go back for theme links in cys intro screen #1351

= 2.2.22 =
* Introduce is_woo_express_trial_plan function #1352

= 2.2.21 =
* Remove wpcom elements when viewing cys iframe from intro screen #1344
* Hide launch banner when viewing iframe from the intro screen #1348

= 2.2.20 =
* Introduced an Inbox allow-list for Free Trial plan users #1268
* Updated the existing messages/notes block-list based on the findings of our recent Inbox audit #1268
* Added a welcome message consistent with the welcome email sent as part of the lifecycle series #1268
* Added a domain purchase message/prompt #1268
* Removed the block-based Cart/Checkout Inbox note #1268

= 2.2.19 =
* Fix CYS conflict with wpcom.editor #1336

= 2.2.18 =
* Fix the "Orders" menu position when using HPOS #1330
* Preconfigure product measurement units #1309
* Add filter for recommended WPCOM themes #1324

= 2.2.17 =
* Enable the reactified WC Admin Marketplace under the "Extensions > Discover" menu item #1318

= 2.2.16 =
* Introduce additional cache purging while creating WooCommerce related pages #1311
* Ensure existing WooCommerce related pages are deleted #1311

= 2.2.15 =
* Revert all page deletion and delete only WooCommerce related pages #1304
* Purge cache before creating WooCommerce related pages #1304
* Change "Blog Home" template name to "Home" #1263

= 2.2.14 =
* Updated plugins landing page (free trial) #1300
* Removed some get_options logging during the page creation one time job #1296

= 2.2.13 =
* Display a server based WooPayments setup task header when an incentive is available #1294
* Introduce additional logging in the page creation one time job #1296
* Delete all created pages (except an allow-list) and then recreate WooCommerce ones, to avoid ending up with duplicates #1296 

= 2.2.12 =
* Improve handling footer credits for Woo Express plans #1286
* Introduce a blocklist for feature settings to be hidden (Analytics, Old Navigation) #1284
* Add logic to search explicitly for the right key in order to inject the Navigation setting under the "Features" section #1284
* Restore access to the editor and HPOS experimental options #1284

= 2.2.11 =
* Handling footer credits for Woo Express plans #1265
* Convert plugins page to a WC page #1278
* Fix plugins mobile screen UI #1281

= 2.2.10 =
* Update translation files on every release

= 2.2.9 =
* Use the correct text domain for i18n functions #1127
* Add a text domain linter #1272

= 2.2.8 =
* Fix Tax task for free trial #1247
* Fix tax task component UIs #1261
* Introduce the remaining tasks bubble nudge under My Home > Home admin menu item #1225
* Add logging in the page creation one time job #1258
* Increase old site identification to older than 1 hour for the page creation one time job #1258
* Properly unset one time jobs for non commerce sites #1258
* Add the Plugins menu item back to free trial sites #1249

= 2.2.7 =
* Add `wp-cli` as a developer dependency #1250

= 2.2.6 =
* Add 'host' parameter to WooCommerce analytics collected by Jetpack #1244

= 2.2.5 =
* Enable downloading updated translation files

= 2.2.4 =
* Hide Brands column in Products list table #1235
* Reverted !important fixed positioning on snackbar css as it's been changed to absolute on WooCommerce Core #1234
* Fixed free trial banner position #1237
* Remove double headings in the Marketing and Extensions pages on the free trial #1239
* Disable ecommerce menu and relevant JS fixes when SSO is disabled #925

= 2.2.2 =
* Reverted hidden activity bar in WC Admin pages #1217
* Disable ecommerce menu and relevant JS fixes when SSO is disabled #925
* Introduced the woocommerce_woo_express_remindertopbar_woo_screens_nudge_202307_v1 experiment #1219
* Hide storefront theme suggestion in addons page #1227

= 2.2.1 =
* Fixed fatal error caused by not checking if tasklist exists #1212

= 2.2.0 =
* Update Free Trial Upgrade message on Task List #1203
* Introduce option to disable Woo Express menu under "Settings > Advanced > Features" #1199
* Move "Settings > Advanced" tab to the end of the list #1199
* Hide advanced options under "Settings > Advanced > Features" for Woo Express stores #1199
* Replace appearance task with choosing theme #1202
* Ensure the woocommerce key exists in the global submenu at all times #1206
* Suppress the WooCommerce Help tab in all WooCommerce pages #1205
* Creates a dedicated section under "Settings > General > Onboarding", where users can restore the visibility of suppressed Task Lists #1205
* Remove extension's hidden admin menu items handling from the ecommerce menu controller #1207
* Fix array missing key warning in product task #1208

= 2.1.9 =
* Avoid Crowdsignal activation redirect #1192

= 2.1.8 =
* Suppress WooCommerce Subscriptions move/duplicated site messages #1172 

= 2.1.7 =
* Hide store_details task in free trial sites #1178
* Fixed pain plan typo #1179
* Tailor add products task copies according to NUX onboarding #1180

= 2.1.6 =
* Default to wide alignment when provisioning the Cart and Checkout pages. #1043
* Add WooExpress Upgrades > Plans track. #1156
* Add UTM tags on the "Extensions > Discover" page links in Free Trial. #1161
* Fix fatal errors in onboarding option filters #1162

= 2.1.5 =
* Make the readme update command run standalone

= 2.1.4 =
* i18n: Add a mechanism to update the files in the languages/ folder #1130

= 2.1.3 =
* Add helper functions to detect the new Essential and Performance Woo Express plans. #1128
* Remove useSlot monkey patch. #1117
* Optimize bridge file size by lazy-loading components #1124.
* Make OBW skip reliable for ecommerce and free trial plans #1125

= 2.1.2 =
* Fix JS lint errors #1105.
* Update Appearance task has_products logic #1107
* Update product task to detect modified products #1106.
* Fix Jetpack tracks ID mismatch #1118.
* Patch for product import issue #1119.
* New script to automate updating the plugin version #1116.
* New script to automate updating the changelog #1122.
* Refactoring release automation scripts #1126.

= 2.1.1 =
* Allow expired trial sites to access the Export tool #1104.
* New script to automate creating a new build #1109.
* New script to automate creating a release tag #1115.

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
