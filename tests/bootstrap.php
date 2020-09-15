<?php
/**
 * WooCommerce Calypso Bridge Unit Tests Bootstrap
 */
class WC_Calypso_Bridge_Unit_Tests_Bootstrap {

	/** @var WC_Calypso_Bridge_Unit_Tests_Bootstrap instance */
	protected static $instance = null;

	/** @var string directory where wordpress-tests-lib is installed */
	public $wp_tests_dir;

	/** @var string directory where WooCommerce tests are located */
	public $wc_tests_dir;

	/** @var string WooCommerce plugin directory */
	public $wc_dir;

	/** @var string testing directory */
	public $tests_dir;

	/** @var string plugin directory */
	public $plugin_dir;

	/**
	 * Setup the unit testing environment.
	 */
	public function __construct() {
		ini_set( 'display_errors', 'on' );
		error_reporting( E_ALL );

		// Ensure server variable is set for WP email functions.
		if ( ! isset( $_SERVER['SERVER_NAME'] ) ) {
			$_SERVER['SERVER_NAME'] = 'localhost';
		}

		$this->tests_dir      = dirname( __FILE__ );
		$this->plugin_dir     = dirname( $this->tests_dir );
		$this->wc_dir         = dirname( $this->plugin_dir ) . '/woocommerce';
		$this->wc_api_dev_dir = dirname( $this->plugin_dir ) . '/wc-api-dev';
		$this->wc_tests_dir   = dirname( $this->plugin_dir ) . '/woocommerce/tests';
		if ( ! is_dir( $this->wc_tests_dir . '/framework' ) ) {
			$this->wc_tests_dir .= '/legacy';
		}
		$this->wp_tests_dir = getenv( 'WP_TESTS_DIR' ) ? getenv( 'WP_TESTS_DIR' ) : '/tmp/wordpress-tests-lib';

		// load test function so tests_add_filter() is available
		require_once $this->wp_tests_dir . '/includes/functions.php';

		// load WC
		tests_add_filter( 'muplugins_loaded', array( $this, 'load_wc' ) );

		// load WC Calypso Bridge
		tests_add_filter( 'muplugins_loaded', array( $this, 'load_wc_calypso_bridge' ) );

		// install WC
		tests_add_filter( 'setup_theme', array( $this, 'install_wc' ) );

		// load the WP testing environment
		require_once $this->wp_tests_dir . '/includes/bootstrap.php';

		// load WC testing framework
		$this->includes();

		// load extra filters
		require_once $this->plugin_dir . '/includes/class-wc-calypso-bridge-filters.php';
	}

	/**
	 * Load WooCommerce.
	 */
	public function load_wc() {
		require_once $this->wc_dir . '/woocommerce.php';
	}

	/**
	 * Load WC Calypso Bridge.
	 */
	public function load_wc_calypso_bridge() {
		require_once $this->plugin_dir . '/class-wc-calypso-bridge.php';
		WC_Calypso_Bridge::instance()->initialize();
	}

	/**
	 * Install WooCommerce after the test environment and WC have been loaded.
	 */
	public function install_wc() {

		// Clean existing install first.
		define( 'WP_UNINSTALL_PLUGIN', true );
		define( 'WC_REMOVE_ALL_DATA', true );
		include $this->wc_dir . '/uninstall.php';

		WC_Install::install();

		// Reload capabilities after install, see https://core.trac.wordpress.org/ticket/28374
		if ( version_compare( $GLOBALS['wp_version'], '4.7', '<' ) ) {
			$GLOBALS['wp_roles']->reinit();
		} else {
			// phpcs:disable
			$GLOBALS['wp_roles'] = null;
			// phpcs:enable
			wp_roles();
		}

		echo 'Installing WooCommerce...' . PHP_EOL;
	}

	/**
	 * Load WC-specific test cases and factories.
	 * If needed, these can also be shipped with the dev plugin and a local copy can be called.
	 */
	public function includes() {
		// framework
		require_once $this->wc_tests_dir . '/framework/class-wc-unit-test-factory.php';
		require_once $this->wc_tests_dir . '/framework/class-wc-mock-session-handler.php';
		require_once $this->wc_tests_dir . '/framework/class-wc-mock-wc-data.php';
		require_once $this->wc_tests_dir . '/framework/class-wc-mock-wc-object-query.php';
		require_once $this->wc_tests_dir . '/framework/class-wc-payment-token-stub.php';
		require_once $this->wc_tests_dir . '/framework/vendor/class-wp-test-spy-rest-server.php';

		// test cases
		require_once $this->wc_tests_dir . '/includes/wp-http-testcase.php';
		require_once $this->wc_tests_dir . '/framework/class-wc-unit-test-case.php';
		require_once $this->wc_tests_dir . '/framework/class-wc-api-unit-test-case.php';
		require_once $this->wc_tests_dir . '/framework/class-wc-rest-unit-test-case.php';

		// Helpers
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-product.php';
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-coupon.php';
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-fee.php';
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-shipping.php';
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-customer.php';
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-order.php';
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-shipping-zones.php';
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-payment-token.php';
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-settings.php';
	}

	/**
	 * Gets the single class instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

WC_Calypso_Bridge_Unit_Tests_Bootstrap::instance();
