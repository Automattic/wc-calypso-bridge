<?php
/**
 * WC Calypso Bridge
 */
class WC_Calypso_Bridge {

	/**
	 * Current version of the plugin.
	 */
	const CURRENT_VERSION = '0.1.0';

	/**
	 * Minimum woocommerce version needed to run this plugin.
	 */
	const WC_MIN_VERSION = '3.0.0';

	/**
	 * Paths to assets act oddly in production
	 */
	const MU_PLUGIN_ASSET_PATH = '/wp-content/mu-plugins/wpcomsh/vendor/automattic/wc-calypso-bridge/';
	public static $plugin_asset_path = null;

	/**
	 * Class Instance.
	 */
	protected static $instance = null;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'woocommerce_init', array( $this, 'init' ) );
	}

	/**
	 * Loads API includes and registers routes.
	 */
	function init() {
		if ( $this->is_woocommerce_valid() ) {
			$this->includes();
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
				WC_Calypso_Bridge::WC_MIN_VERSION,
				'>='
			)
		);
	}

	/**
	 * Includes.
	 */
	public function includes() {
		// Hotfixes
		include_once( dirname( __FILE__ ) . '/hotfixes/wc-calypso-bridge-allowed-redirect-hosts.php' );
		include_once( dirname( __FILE__ ) . '/hotfixes/wc-calypso-bridge-cheque-defaults.php' );
		include_once( dirname( __FILE__ ) . '/hotfixes/wc-calypso-bridge-email-order-url.php' );
		include_once( dirname( __FILE__ ) . '/hotfixes/wc-calypso-bridge-email-site-title.php' );
		include_once( dirname( __FILE__ ) . '/hotfixes/wc-calypso-bridge-enable-auto-update-db.php' );
		include_once( dirname( __FILE__ ) . '/hotfixes/wc-calypso-bridge-jetpack-hotfixes.php' );
		include_once( dirname( __FILE__ ) . '/hotfixes/wc-calypso-bridge-mailchimp-no-redirect.php' );
		include_once( dirname( __FILE__ ) . '/hotfixes/wc-calypso-bridge-masterbar-menu.php' );
		include_once( dirname( __FILE__ ) . '/hotfixes/wc-calypso-bridge-paypal-defaults.php' );

		// Other classes.
		include_once( dirname( __FILE__ ) . '/inc/class-customizer-guided-tour.php' );
	}

	/**
	 * Class instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			// If this is a traditionally installed plugin, set plugin_url for the proper asset path.
			if ( file_exists( WP_PLUGIN_DIR . '/wc-calypso-bridge/wc-calypso-bridge.php' ) ) {
				if ( WP_PLUGIN_DIR . '/wc-calypso-bridge/' == plugin_dir_path( __FILE__ ) ) {
					self::$plugin_asset_path = plugin_dir_url( __FILE__ );
				}
			}

			self::$instance = new self();
		}

		return self::$instance;
	}
}

WC_Calypso_Bridge::instance();