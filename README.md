# WooCommerce Calypso Bridge

This repository houses various fixes and extensions for wp-admin to enhance the experience for users of the WordPress.com Store.

## Getting Started

To get started with development run `composer install` from the this repo's root directory. This will:

- Install all vendor dependencies
- Create pre-commit hooks to catch lint and WPCS errors

To check WPCS and lint errors via CLI, run the following from the root directory.
`./vendor/bin/phpcs [filename]`
To automatically fix errors and beautify files, run the following from the root directory.
`./vendor/bin/phpcbf [filename]`

To turn on development mode for this plugin, the following filter can be added:

`add_filter( 'wc_calypso_bridge_development_mode', '__return_true' );`

For working with front-end components, run the following commands:

```text
npm install
npm start
```

See [wp-scripts](https://github.com/WordPress/gutenberg/tree/master/packages/scripts) for more usage information.

### Activating the Ecommerce Plan Layout

To use the ecommerce plan changes in the dashboard and test various functionality in this plugin, there are a number of conditions that must be met.

#### Plugin Dependencies

- WooCommerce >= 3.0.0
- Jetpack

Note that the plugin expects to find these plugins in their original folders, so renaming these folders may prevent the plugin from running.

#### Jetpack Connection && Option Values

You will need either a connected Jetpack site or use the following filter to force Jetpack into development mode:

`add_filter( 'jetpack_offline_mode', '__return_true' );`

To make bridge work, the site must have the eCommerce plan.

Note that this checklist can't work simultaneously with the new WooCommerce Admin onboarding experience. To use the checklist in this plugin, make sure that you opt out of the new onboarding experience:

```
update_option( 'woocommerce_setup_ab_wc_admin_onboarding', 'a' );
update_option( 'wc_onboarding_opt_in', 'no' );
```

If you would like to skip all of the above, [just download this gist](https://gist.github.com/psealock/531205e2c3d37be1d8ac4d3ef4f346bc) as a plugin and activate it :).

### Plugin Integrations

The ecommerce plan comes bundled with a number of plugins that this plugin integrates with if activated. To fully test this plugin's functionality, the following plugins can be installed.

- Payments
  - [WooCommerce Stripe Payment Gateway](https://wordpress.org/plugins/woocommerce-gateway-stripe/)
  - [WooCommerce PayPal Checkout Payment Gateway](https://wordpress.org/plugins/woocommerce-gateway-paypal-express-checkout/)
  - [WooCommerce Square](https://wordpress.org/plugins/woocommerce-square/)
  - [Klarna Payments for WooCommerce](https://wordpress.org/plugins/klarna-payments-for-woocommerce/)
  - [Klarna Checkout for WooCommerce](https://wordpress.org/plugins/klarna-checkout-for-woocommerce/)
  - [WooCommerce eWAY Gateway](https://wordpress.org/plugins/woocommerce-gateway-eway/)
  - [WooCommerce PayFast Gateway](https://wordpress.org/plugins/woocommerce-payfast-gateway/)
- Taxes:
  - [TaxJar -- Sales Tax Automation for WooCommerce](https://wordpress.org/plugins/taxjar-simplified-taxes-for-woocommerce/)
- Shipping:
  - [WooCommerce Services](https://wordpress.org/plugins/woocommerce-services/)
- Marketing:
  - [MailChimp for WooCommerce](https://wordpress.org/plugins/mailchimp-for-woocommerce/)
  - [Facebook  for WooCommerce](https://woocommerce.com/products/facebook/)
  - [Creative Mail](https://wordpress.org/plugins/creative-mail-by-constant-contact/)
  - [Crowdsignal Forms](https://wordpress.org/plugins/crowdsignal-forms/)
- Store Management:

  - [TaxJar -- Sales Tax Automation for WooCommerce](https://wordpress.org/plugins/taxjar-simplified-taxes-for-woocommerce/)

- Theme:
  - [Storefront](https://woocommerce.com/storefront/)

#### Paid extensions

- Shipping (everywhere):
  - [UPS Shipping Method](https://woocommerce.com/products/ups-shipping-method/)
- Shipping (based on geo):
  - [USPS Shipping Method](https://woocommerce.com/products/usps-shipping-method/)
  - [Canada Post shipping](https://woocommerce.com/products/canada-post-shipping-method/)
  - [Royal Mail](https://woocommerce.com/products/royal-mail/)
  - [Australia Post Shipping Method](https://woocommerce.com/products/australia-post-shipping-method/)
- Product Page Features:
  - [Product Add-Ons](https://woocommerce.com/products/product-add-ons/)
- Storefront premium options
  - [Galleria](https://woocommerce.com/products/galleria/)
  - [Homestore](https://woocommerce.com/products/homestore/)
  - [Bookshop](https://woocommerce.com/products/bookshop/)
  - [Storefront Powerpack design options](https://woocommerce.com/products/storefront-powerpack/)
  - [Blog Customizer](https://woocommerce.com/products/storefront-blog-customiser/)
  - [Parallax Hero](https://woocommerce.com/products/storefront-parallax-hero/)
  - [Product Hero](https://woocommerce.com/products/storefront-product-hero/)
  - [Reviews](https://woocommerce.com/products/storefront-reviews/)

## Test Suite

This repository does have a test suite, which depends upon `wc-api-dev`, and `woocommerce` both being present witin the same `wp-content/plugins` directory. Much like the test suite in `wc-api-dev` it borrows heavily from the base `woocommerce` API test suite to enable quick testing via all of the core helper methods.

Ideally all API functionality will eventually be contained within `wc-api-dev` ( and subsequently core ), but at least now we can have unit tests around various _quick fixes_ implemented here.

### Running the Test Suite

From a test install of WordPress with `wc-api-dev` and `woocommerce` present, run `phpunit` from the `store-on-wpcom` directory to run legacy Store on WP.com hotfix/API tests.

Run `phpunit` in the root plugin directory to run the new test suites.

Code coverage reports can be ran with `phpunit --coverage-html /tmp/coverage`.

## Deployment

1. Create a [version bump pull request](https://github.com/Automattic/wc-calypso-bridge/pull/613/files) on `wc-calypso-bridge` that increments the [version](https://github.com/Automattic/wc-calypso-bridge/blob/master/composer.json#L3) in `composer.json`, and in [two spots](https://github.com/Automattic/wc-calypso-bridge/blob/master/wc-calypso-bridge.php#L33) in `wc-calypso-bridge.php`.
2. [Add a section](https://github.com/Automattic/wc-calypso-bridge/blob/master/readme.txt#L23-L26) to the `readme.txt` also with a changelog of what is in the release.
3. Mark the pull request as ready for review, and **merge** once a 👍 review is given.
4. Make sure you've closed your `start` script if you have it running.
5. *Back in your local copy of wc-calypso-bridge*, perform the following steps:
	1. `git checkout master`
	2. `git pull origin master`
	3. `npm i`
	4. `npm run build`
	5. `rm -rf node_modules` so these don't get packaged up in the release

6. Now you are ready to tag the release by replacing x.x.x with the version number being created:
	1. `git tag x.x.x`
	2. `git checkout x.x.x`
	3. `git add ./build --force`
	4. `git commit -m 'Adding build directory to release' --no-verify`
	5. `git tag -f x.x.x`
	6. `git push origin x.x.x`
7. 🎊 Congrats, you have released a new version of `wc-calypso-bridge`.
