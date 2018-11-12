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

## Test Suite

This repository does have a test suite, which depends upon `wc-api-dev`, and `woocommerce` both being present witin the same `wp-content/plugins` directory. Much like the test suite in `wc-api-dev` it borrows heavily from the base `woocommerce` API test suite to enable quick testing via all of the core helper methods.

Ideally all API functionality will eventually be contained within `wc-api-dev` ( and subsequently core ), but at least now we can have unit tests around various _quick fixes_ implemented here.

### Running the Test Suite

From a test install of WordPress with `wc-api-dev` and `woocommerce` present, run `phpunit` from the `store-on-wpcom` directory to run legacy Store on WP.com hotfix/API tests.

Run `phpunit` in the root plugin directory to run the new test suites.

Code coverage reports can be ran with `phpunit --coverage-html /tmp/coverage`.