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

To turn on development mode for this plugin and prevent the setup checklist from being completed, the following filter can be added:

`add_filter( 'wc_calypso_bridge_development_mode', '__return_true' );`

### Activating Calypsoify

To Calypsoify the dashboard and test various functionality in this plugin, there are a number of conditions that must be met.

#### Plugin Dependencies
* WooCommerce >= 3.0.0
* Jetpack

Note that the plugin expects to find these plugins in their original folders, so renaming these folders may prevent the plugin from running.

#### Jetpack Connection && Option Values
You will need either a connected Jetpack site or use the following filter to force Jetpack into development mode:

`add_filter( 'jetpack_development_mode', '__return_true' );`

Enable the Jetpack Calypso module:

```
add_filter( 'jetpack_tools_to_include', function( $tools ) {
    return array_merge( $tools, [ 'calypsoify/class.jetpack-calypsoify.php' ] );
} );
```

To make bridge work, the site must have the eCommerce plan.

Clicking the "I'm Done Setting Up" button on the Setup Checklist page will mark the option `atomic-ecommerce-setup-checklist-complete` as true.  If you need to access this page again, you can update this in your database or temporarily add the following to your plugin file:

`update_option( 'atomic-ecommerce-setup-checklist-complete', false );`

Note that this checklist can't work simultaneously with the new WooCommerce Admin onboarding experience.  To use the checklist in this plugin, make sure that you opt out of the new onboarding experience:

```
update_option( 'woocommerce_setup_ab_wc_admin_onboarding', 'a' );
update_option( 'wc_onboarding_opt_in', 'no' );
```

If you would like to skip all of the above, [just download this gist](https://gist.github.com/timmyc/72061e99f2e6893a94845ba93e6db6ca) as a plugin and activate it :).

#### Calypsoify Param
Adding the Calypsoify param `calypsoify=1` to the URL will Calypsoify any WooCommerce or WC Calypso Bridge route once the above dependencies have been met.

`/wp-admin/edit.php?post_type=shop_order&calypsoify=1`

If you manually visit a route that is not able to be Calypsoified (i.e, visiting `wp-admin/*` directly via URL) you will be bumped out of Calypsoify mode and need to add the param to the URL once again to reactivate it.


### Plugin Integrations

The ecommerce plan comes bundled with a number of plugins that this plugin integrates with if activated.  To fully test this plugin's functionality, the following plugins can be installed.

* Payments
    * [WooCommerce Stripe Payment Gateway](https://href.li/?https://wordpress.org/plugins/woocommerce-gateway-stripe/)
    * [WooCommerce PayPal Checkout Payment Gateway](https://href.li/?https://wordpress.org/plugins/woocommerce-gateway-paypal-express-checkout/)
    * [WooCommerce Square](https://href.li/?https://wordpress.org/plugins/woocommerce-square/)
    * [Klarna Payments for WooCommerce](https://href.li/?https://wordpress.org/plugins/klarna-payments-for-woocommerce/)
    * [Klarna Checkout for WooCommerce](https://href.li/?https://wordpress.org/plugins/klarna-checkout-for-woocommerce/)
    * [WooCommerce eWAY Gateway](https://href.li/?https://wordpress.org/plugins/woocommerce-gateway-eway/)
    * [WooCommerce PayFast Gateway](https://href.li/?https://wordpress.org/plugins/woocommerce-payfast-gateway/)
* Taxes:
    * [TaxJar -- Sales Tax Automation for WooCommerce](https://href.li/?https://wordpress.org/plugins/taxjar-simplified-taxes-for-woocommerce/)
* Shipping:
    * [WooCommerce Services](https://href.li/?https://wordpress.org/plugins/woocommerce-services/)
* Marketing:
    * [MailChimp for WooCommerce](https://href.li/?https://wordpress.org/plugins/mailchimp-for-woocommerce/)
    * [Facebook  for WooCommerce](https://href.li/?https://woocommerce.com/products/facebook/)
* Store Management:
    * [TaxJar -- Sales Tax Automation for WooCommerce](https://href.li/?https://wordpress.org/plugins/taxjar-simplified-taxes-for-woocommerce/)

* Theme:
    * [Storefront](https://href.li/?https://woocommerce.com/storefront/)

#### Paid extensions

* Shipping (everywhere):
    * [UPS Shipping Method](https://href.li/?https://woocommerce.com/products/ups-shipping-method/)
* Shipping (based on geo):
    * [USPS Shipping Method](https://href.li/?https://woocommerce.com/products/usps-shipping-method/)
    * [Canada Post shipping](https://href.li/?https://woocommerce.com/products/canada-post-shipping-method/)
    * [Royal Mail](https://href.li/?https://woocommerce.com/products/royal-mail/)
    * [Australia Post Shipping Method](https://href.li/?https://woocommerce.com/products/australia-post-shipping-method/)
* Product Page Features:
    * [Product Add-Ons](https://href.li/?https://woocommerce.com/products/product-add-ons/)
* Storefront premium options
    * [Galleria](https://href.li/?https://woocommerce.com/products/galleria/)
    * [Homestore](https://href.li/?https://woocommerce.com/products/homestore/)
    * [Bookshop](https://href.li/?https://woocommerce.com/products/bookshop/)
    * [Storefront Powerpack design options](https://href.li/?https://woocommerce.com/products/storefront-powerpack/)
    * [Blog Customizer](https://woocommerce.com/products/storefront-blog-customiser/)
    * [Parallax Hero](https://woocommerce.com/products/storefront-parallax-hero/)
    * [Product Hero](https://woocommerce.com/products/storefront-product-hero/)
    * [Reviews](https://woocommerce.com/products/storefront-reviews/)

## Test Suite

This repository does have a test suite, which depends upon `wc-api-dev`, and `woocommerce` both being present witin the same `wp-content/plugins` directory. Much like the test suite in `wc-api-dev` it borrows heavily from the base `woocommerce` API test suite to enable quick testing via all of the core helper methods.

Ideally all API functionality will eventually be contained within `wc-api-dev` ( and subsequently core ), but at least now we can have unit tests around various _quick fixes_ implemented here.

### Running the Test Suite

From a test install of WordPress with `wc-api-dev` and `woocommerce` present, run `phpunit` from the `store-on-wpcom` directory to run legacy Store on WP.com hotfix/API tests.

Run `phpunit` in the root plugin directory to run the new test suites.

Code coverage reports can be ran with `phpunit --coverage-html /tmp/coverage`.
