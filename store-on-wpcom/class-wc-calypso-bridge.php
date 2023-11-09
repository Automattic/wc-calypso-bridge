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
		include_once dirname( __FILE__ ) . '/inc/wc-calypso-bridge-add-bacs-accounts.php';
		include_once dirname( __FILE__ ) . '/inc/wc-calypso-bridge-allowed-redirect-hosts.php';
		include_once dirname( __FILE__ ) . '/inc/wc-calypso-bridge-cheque-defaults.php';
		include_once dirname( __FILE__ ) . '/inc/wc-calypso-bridge-disable-publicize.php';
		include_once dirname( __FILE__ ) . '/inc/wc-calypso-bridge-enable-auto-update-db.php';
		include_once dirname( __FILE__ ) . '/inc/wc-calypso-bridge-hide-alerts.php';
		include_once dirname( __FILE__ ) . '/inc/wc-calypso-bridge-jetpack-hotfixes.php';
		include_once dirname( __FILE__ ) . '/inc/wc-calypso-bridge-jetpack-sync.php';
		include_once dirname( __FILE__ ) . '/inc/wc-calypso-bridge-mailchimp-deactivate-hook.php';
		include_once dirname( __FILE__ ) . '/inc/wc-calypso-bridge-mailchimp-no-redirect.php';
		include_once dirname( __FILE__ ) . '/inc/wc-calypso-bridge-masterbar-menu.php';
		include_once dirname( __FILE__ ) . '/inc/wc-calypso-bridge-paypal-defaults.php';
		include_once dirname( __FILE__ ) . '/inc/wc-calypso-bridge-paypal-method-supports.php';
		include_once dirname( __FILE__ ) . '/inc/wc-calypso-bridge-products.php';
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
