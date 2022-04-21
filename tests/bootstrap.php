<?php
/**
 * WooCommerce Calypso Bridge Unit Tests Bootstrap
 *
 * Loosely based on woocommerce/tests/legacy/bootstrap.php.
 */

use Automattic\WooCommerce\Proxies\LegacyProxy;
use Automattic\WooCommerce\Testing\Tools\CodeHacking\CodeHacker;
use Automattic\WooCommerce\Testing\Tools\CodeHacking\Hacks\StaticMockerHack;
use Automattic\WooCommerce\Testing\Tools\CodeHacking\Hacks\FunctionsMockerHack;
use Automattic\WooCommerce\Testing\Tools\CodeHacking\Hacks\BypassFinalsHack;
use Automattic\WooCommerce\Testing\Tools\DependencyManagement\MockableLegacyProxy;

/**
 * Class WC_Calypso_Bridge_Unit_Tests_Bootstrap
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

		$this->register_autoloader_for_testing_tools();

		$this->initialize_code_hacker();

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

		// re-initialize dependency injection, this needs to be the last operation after everything else is in place.
		$this->initialize_dependency_injection();
	}

	/**
	 * Load WooCommerce.
	 */
	public function load_wc() {
		define( 'WC_TAX_ROUNDING_MODE', 'auto' );
		define( 'WC_USE_TRANSACTIONS', false );
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
	 *
	 * wc-calypso-bridge: fix include uninstall dir.
	 */
	public function install_wc() {
		// Clean existing install first.
		define( 'WP_UNINSTALL_PLUGIN', true );
		define( 'WC_REMOVE_ALL_DATA', true );
		include $this->wc_dir . '/uninstall.php';

		// Initialize the WC API extensions.
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
		// framework.
		require_once $this->wc_tests_dir . '/framework/class-wc-unit-test-factory.php';
		require_once $this->wc_tests_dir . '/framework/class-wc-mock-session-handler.php';
		require_once $this->wc_tests_dir . '/framework/class-wc-mock-wc-data.php';
		require_once $this->wc_tests_dir . '/framework/class-wc-mock-wc-object-query.php';
		require_once $this->wc_tests_dir . '/framework/class-wc-mock-payment-gateway.php';
		require_once $this->wc_tests_dir . '/framework/class-wc-payment-token-stub.php';
		require_once $this->wc_tests_dir . '/framework/vendor/class-wp-test-spy-rest-server.php';

		// test cases.
		require_once $this->wc_tests_dir . '/includes/wp-http-testcase.php';
		require_once $this->wc_tests_dir . '/framework/class-wc-unit-test-case.php';
		require_once $this->wc_tests_dir . '/framework/class-wc-api-unit-test-case.php';
		require_once $this->wc_tests_dir . '/framework/class-wc-rest-unit-test-case.php';

		// Helpers.
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-product.php';
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-coupon.php';
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-fee.php';
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-shipping.php';
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-customer.php';
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-order.php';
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-shipping-zones.php';
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-payment-token.php';
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-settings.php';

		// Traits.
		require_once $this->wc_tests_dir . '/framework/traits/trait-wc-rest-api-complex-meta.php';
	}

	/**
	 * Register autoloader for the files in the 'tests/tools' directory, for the root namespace 'Automattic\WooCommerce\Testing\Tools'.
	 *
	 * wc-calypso-bridge: fixed base_dir.
	 */
	protected function register_autoloader_for_testing_tools() {
		$plugin_dir = $this->plugin_dir;
		return spl_autoload_register(
			function ( $class ) use ( $plugin_dir ) {
				$prefix   = 'Automattic\\WooCommerce\\Testing\\Tools\\';
				$base_dir = dirname( $plugin_dir ) . '/woocommerce/tests/Tools/';
				$len      = strlen( $prefix );
				if ( strncmp( $prefix, $class, $len ) !== 0 ) {
					// no, move to the next registered autoloader.
					return;
				}
				$relative_class = substr( $class, $len );
				$file           = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';
				if ( ! file_exists( $file ) ) {
					throw new \Exception( 'Autoloader for unit tests: file not found: ' . $file );
				}
				require $file;
			}
		);
	}

	/**
	 * Initialize the code hacker.
	 *
	 * @throws Exception Error when initializing one of the hacks.
	 *
	 * wc-calypso-bridge: fixed paths to point at $this->wc_tests_dir.
	 */
	private function initialize_code_hacker() {
		CodeHacker::initialize( array( $this->wc_tests_dir . '/../../includes/' ) );

		$replaceable_functions = include_once $this->wc_tests_dir . '/mockable-functions.php';
		if ( ! empty( $replaceable_functions ) ) {
			FunctionsMockerHack::initialize( $replaceable_functions );
			CodeHacker::add_hack( FunctionsMockerHack::get_hack_instance() );
		}

		$mockable_static_classes = include_once $this->wc_tests_dir . '/classes-with-mockable-static-methods.php';
		if ( ! empty( $mockable_static_classes ) ) {
			StaticMockerHack::initialize( $mockable_static_classes );
			CodeHacker::add_hack( StaticMockerHack::get_hack_instance() );
		}

		CodeHacker::add_hack( new BypassFinalsHack() );

		CodeHacker::enable();
	}

	/**
	 * Re-initialize the dependency injection engine.
	 *
	 * The dependency injection engine has been already initialized as part of the Woo initialization, but we need
	 * to replace the registered read-only container with a fully configurable one for testing.
	 * To this end we hack a bit and use reflection to grab the underlying container that the read-only one stores
	 * in a private property.
	 *
	 * Additionally, we replace the legacy/function proxies with mockable versions to easily replace anything
	 * in tests as appropriate.
	 *
	 * @throws \Exception The Container class doesn't have a 'container' property.
	 */
	private function initialize_dependency_injection() {
		try {
			$inner_container_property = new \ReflectionProperty( \Automattic\WooCommerce\Container::class, 'container' );
		} catch ( ReflectionException $ex ) {
			throw new \Exception( "Error when trying to get the private 'container' property from the " . \Automattic\WooCommerce\Container::class . ' class using reflection during unit testing bootstrap, has the property been removed or renamed?' );
		}

		$inner_container_property->setAccessible( true );
		$inner_container = $inner_container_property->getValue( wc_get_container() );

		$inner_container->replace( LegacyProxy::class, MockableLegacyProxy::class );
		$inner_container->reset_all_resolved();

		$GLOBALS['wc_container'] = $inner_container;
	}

	/**
	 * Get the single class instance.
	 *
	 * @return WC_Calypso_Bridge_Unit_Tests_Bootstrap
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

WC_Calypso_Bridge_Unit_Tests_Bootstrap::instance();
