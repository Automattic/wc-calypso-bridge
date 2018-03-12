# WooCommerce Calypso Bridge

This repository houses various fixes and extensions for wp-admin to enhance the experience for users of the WordPress.com Store.

## Test Suite

This repository does have a test suite, which depends upon `wc-api-dev`, and `woocommerce` both being present witin the same `wp-content/plugins` directory. Much like the test suite in `wc-api-dev` it borrows heavily from the base `woocommerce` API test suite to enable quick testing via all of the core helper methods.

Ideally all API functionality will eventually be contained within `wc-api-dev` ( and subsequently core ), but at least now we can have unit tests around various _quick fixes_ implemented here.

### Running the Test Suite

From a test install of WordPress with `wc-api-dev` and `woocommerce` present, run `phpunit` from the root of this plugin directory.

 Code coverage reports can be ran with `phpunit --coverage-html /tmp/coverage`.