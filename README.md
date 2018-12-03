# WooCommerce Calypso Bridge

This repository houses various fixes and extensions for wp-admin to enhance the experience for users of the WordPress.com Store.

## Getting Started

To get started with development run `composer install` from the this repo's root directory.  This will:
* Install all vendor dependencies
* Create pre-commit hooks to catch lint and WPCS errors

To check WPCS and lint errors via CLI, run the following from the root directory.
`./vendor/bin/phpcs [filename]`
To automatically fix errors and beautify files, run the following from the root directory.
`./vendor/bin/phpcbf [filename]`

### Activating Calypsoify

To Calypsoify the dashboard and test various functionality in this plugin, there are a number of conditions that must be met.

#### Plugin Dependencies
* WooCommerce >= 3.0.0
* Jetpack

Note that the plugin expects to find these plugins in their original folders, so renaming these folders may prevent the plugin from running.

#### Jetpack Connection
You will need either a connected Jetpack site or set your Jetpack development environment constants.  To enable Jetpack's dev mode, add this to your `wp-config.php` file:

`define( 'JETPACK_DEV_DEBUG', true );`

However, if your site is still not connected via Jetpack, you will not be able to fully test the Masterbar.  If you're working locally, you can Ngrok your local site to connect via Jetpack.  Also note that the `JETPACK_DEV_DEBUG` constant above will prevent making a new Jetpack connection.

#### Option Values

To make the bridge work, the site must have the ecommerce plan option set under the `at_options` option.  You can add this to your site by temporarily adding this to the plugin file:

`update_option( 'at_options', array( 'plan_slug' => 'ecommerce' ) );`

Clicking the "I'm Done Setting Up" button on the Setup Checklist page will mark the option `atomic-ecommerce-setup-checklist-complete` as true.  If you need to access this page again, you can update this in your database or temporarily add the following to your plugin file:

`update_option( 'atomic-ecommerce-setup-checklist-complete', false );`

#### Calypsoify Param
Adding the Calypsoify param `calypsoify=1` to the URL will Calypsoify any WooCommerce or WC Calypso Bridge route once the above dependencies have been met.

`/wp-admin/edit.php?post_type=shop_order&calypsoify=1`

If you manually visit a route that is not able to be Calypsoified (i.e, visiting `wp-admin/*` directly via URL) you will be bumped out of Calypsoify mode and need to add the param to the URL once again to reactivate it.


## Test Suite

This repository does have a test suite, which depends upon `wc-api-dev`, and `woocommerce` both being present witin the same `wp-content/plugins` directory. Much like the test suite in `wc-api-dev` it borrows heavily from the base `woocommerce` API test suite to enable quick testing via all of the core helper methods.

Ideally all API functionality will eventually be contained within `wc-api-dev` ( and subsequently core ), but at least now we can have unit tests around various _quick fixes_ implemented here.

### Running the Test Suite

From a test install of WordPress with `wc-api-dev` and `woocommerce` present, run `phpunit` from the `store-on-wpcom` directory to run legacy Store on WP.com hotfix/API tests.

Run `phpunit` in the root plugin directory to run the new test suites.

Code coverage reports can be ran with `phpunit --coverage-html /tmp/coverage`.