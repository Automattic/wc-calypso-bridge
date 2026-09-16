<?php
/**
 * WC Calypso Bridge File
 *
 * @package WC_Calypso_bridge
 */

/**
 * WC Calypso Bridge
 */
class WC_Calypso_Bridge_Deprecated {

	/**
	 * Paths to assets act oddly in production
	 */
	const MU_PLUGIN_ASSET_PATH       = '/wp-content/mu-plugins/wpcomsh/vendor/automattic/wc-calypso-bridge/store-on-wpcom/';
	/**
	 * Plugin asset path.
	 *
	 * @var string
	 */
	public static $plugin_asset_path = null;

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Bridge_Deprecated
	 */
	protected static $instance = null;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'woocommerce_init', array( $this, 'init' ), 20 );
	}

	/**
	 * Loads API includes and registers routes.
	 */
	public function init() {
		if ( $this->is_woocommerce_valid() ) {
			$this->includes();

			// Temporary: detect any caller still sending the retired BACS account details payload.
			add_filter( 'rest_request_before_callbacks', array( $this, 'log_legacy_bacs_accounts_payload' ), 10, 3 );

			// Ensure wc-api-dev has already registered routes.
			add_action( 'rest_api_init', array( $this, 'register_routes' ), 20 );
		}
	}

	/**
	 * Makes sure WooCommerce is installed and up to date.
	 */
	public function is_woocommerce_valid() {
		return (
			class_exists( 'woocommerce' ) &&
			version_compare(
				get_option( 'woocommerce_db_version' ),
				WC_MIN_VERSION,
				'>='
			)
		);
	}

	/**
	 * Includes.
	 */
	public function includes() {
		/** Patches includes */
		include_once dirname( __FILE__ ) . '/inc/wc-calypso-bridge-cheque-defaults.php';
		include_once dirname( __FILE__ ) . '/inc/wc-calypso-bridge-disable-publicize.php';
		include_once dirname( __FILE__ ) . '/inc/wc-calypso-bridge-enable-auto-update-db.php';
		include_once dirname( __FILE__ ) . '/inc/wc-calypso-bridge-jetpack-hotfixes.php';
		include_once dirname( __FILE__ ) . '/inc/wc-calypso-bridge-mailchimp-deactivate-hook.php';
		include_once dirname( __FILE__ ) . '/inc/wc-calypso-bridge-masterbar-menu.php';
		include_once dirname( __FILE__ ) . '/inc/wc-calypso-bridge-paypal-defaults.php';
		include_once dirname( __FILE__ ) . '/inc/wc-calypso-bridge-products.php';
	}

	/**
	 * Logs requests that still carry the retired BACS account details payload.
	 *
	 * This branch used to read and write Direct Bank Transfer account details
	 * through `settings.accounts` on /wc/v3/payment_gateways/bacs, for the
	 * Calypso Store dashboard retired in 2021. WooCommerce ignores that key, so
	 * a caller that still sends it now gets a 200 with nothing saved and no
	 * error, leaving no trace anywhere.
	 *
	 * One line is logged per such request, so that any remaining caller shows up
	 * in the platform PHP error logs and can be alerted on. Account values are
	 * never logged. Remove this method and its filter once the observation
	 * window has passed with no hits.
	 *
	 * The message ends with ` in <file> on line <n>` on purpose. The platform log
	 * index stores the message but does not index it, so a message alone cannot
	 * be queried or alerted on. It does index the file and line it parses out of
	 * that suffix, which is what lets an alert match this file's path. Keep the
	 * suffix last, and keep `__FILE__` and `__LINE__` rather than a literal.
	 *
	 * Runs on `rest_request_before_callbacks`, which fires before the route's
	 * permission callback, so unauthenticated attempts are recorded too. It only
	 * reads the request and returns the response untouched.
	 *
	 * @param WP_REST_Response|WP_HTTP_Response|WP_Error|mixed $response Current response.
	 * @param array                                            $handler  Route handler for the request.
	 * @param WP_REST_Request                                  $request  Request being dispatched.
	 * @return WP_REST_Response|WP_HTTP_Response|WP_Error|mixed The response, unchanged.
	 */
	public function log_legacy_bacs_accounts_payload( $response, $handler, $request ) {
		if ( ! $request instanceof WP_REST_Request ) {
			return $response;
		}

		if ( ! in_array( $request->get_method(), array( 'POST', 'PUT', 'PATCH' ), true ) ) {
			return $response;
		}

		$route  = $request->get_route();
		$suffix = '/payment_gateways/bacs';
		if ( $suffix !== substr( $route, -strlen( $suffix ) ) ) {
			return $response;
		}

		$settings = $request['settings'];
		if ( ! is_array( $settings ) || ! array_key_exists( 'accounts', $settings ) ) {
			return $response;
		}

		error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			sprintf(
				'wc-calypso-bridge: legacy BACS accounts payload received. route=%s method=%s authorized=%s accounts=%d in %s on line %d',
				$route,
				$request->get_method(),
				current_user_can( 'manage_woocommerce' ) ? 'yes' : 'no',
				is_array( $settings['accounts'] ) ? count( $settings['accounts'] ) : -1,
				__FILE__,
				__LINE__
			)
		);

		return $response;
	}

	/**
	 * Register REST API routes.
	 *
	 * New endpoints/controllers can be added here.
	 */
	public function register_routes() {
		/** API includes */
		include_once dirname( __FILE__ ) . '/api/class-wc-calypso-bridge-send-invoice-controller.php';
		include_once dirname( __FILE__ ) . '/api/class-wc-calypso-bridge-settings-email-groups-controller.php';
		include_once dirname( __FILE__ ) . '/api/class-wc-calypso-bridge-data-counts-controller.php';
		include_once dirname( __FILE__ ) . '/api/class-wc-calypso-bridge-product-reviews-controller.php';

		if ( class_exists( 'MailChimp_Woocommerce' ) ) {
			include_once dirname( __FILE__ ) . '/api/class-wc-calypso-bridge-mailchimp-settings-controller.php';
		}

		$controllers = array(
			'WC_Calypso_Bridge_Send_Invoice_Controller',
			'WC_Calypso_Bridge_Settings_Email_Groups_Controller',
			'WC_Calypso_Bridge_Data_Counts_Controller',
			'WC_Calypso_Bridge_Product_Reviews_Controller',
		);

		if ( class_exists( 'MailChimp_Woocommerce' ) ) {
				$controllers[] = 'WC_Calypso_Bridge_MailChimp_Settings_Controller';
		}

		foreach ( $controllers as $controller ) {
			$controller_instance = new $controller();
			$controller_instance->register_routes();
		}
	}

	/**
	 * Class instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			// If this is a traditionally installed plugin, set plugin_url for the proper asset path.
			if ( file_exists( WP_PLUGIN_DIR . '/wc-calypso-bridge/wc-calypso-bridge.php' ) ) {
				if ( WP_PLUGIN_DIR . '/wc-calypso-bridge/store-on-wpcom/' == plugin_dir_path( __FILE__ ) ) {
					self::$plugin_asset_path = plugin_dir_url( __FILE__ );
				}
			}

			self::$instance = new self();
		}

		return self::$instance;
	}
}

WC_Calypso_Bridge_Deprecated::instance();
