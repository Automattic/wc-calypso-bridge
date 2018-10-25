<?php
/**
 * WC Calypso Bridge
 */
class WC_Calypso_Bridge {
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
		$this->includes();

		// Hook on `admin_print_styles`, after some WC CSS is hooked, so we can override a few '!important' styles.
		add_action( 'admin_print_styles', array( $this, 'possibly_add_calypsoify_styles' ), 11 );

		// Suppress WC Admin Notices
		add_action( 'admin_head', array( $this, 'suppress_admin_notices' ) );
		add_filter( 'woocommerce_helper_suppress_connect_notice', '__return_true' );
	}

	/**
	 * Loads required functionality, classes, and API endpoints.
	 */
	private function includes() {
		include_once( dirname( __FILE__ ) . '/includes/helper-functions.php' );
		include_once( dirname( __FILE__ ) . '/includes/page-controller.php' );
		include_once( dirname( __FILE__ ) . '/includes/menus.php' );
		include_once( dirname( __FILE__ ) . '/includes/setup.php' );

		$connect_files = glob( dirname( __FILE__ ) . '/includes/connect/*.php' );
		foreach ( $connect_files as $connect_file ) {
			include_once( $connect_file );
		}
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

	/**
	 * Add calypsoify styles if calypsoify is enabled
	 */
	public function possibly_add_calypsoify_styles() {
		if ( 1 == (int) get_user_meta( get_current_user_id(), 'calypsoify', true ) ) {
			$asset_path = WC_Calypso_Bridge::$plugin_asset_path ? WC_Calypso_Bridge::$plugin_asset_path : WC_Calypso_Bridge::MU_PLUGIN_ASSET_PATH;
			wp_enqueue_style( 'wc-calypso-bridge-calypsoify', $asset_path . 'assets/css/calypsoify.css', array(), WC_CALYPSO_BRIDGE_CURRENT_VERSION, 'all' );
			add_filter( 'woocommerce_display_admin_footer_text', '__return_false' );
		}
	}

	/**
	 * Suppresses admin notifications in wp-admin
	 *
	 * @return void
	 */
	public function suppress_admin_notices() {
		/**
		 * List of extension specific and themes class level functions to suppress
		 * 'CLASS_NAME' => array( 'FUNCTION_PRIORITY' => 'FUNCTION_NAME' )
		 */ 
		$extension_admin_notices_to_suppress = array(	'WC_Shipping_Australia_Post_Init' 	=> array( '10' => 'environment_check' ),
														'WC_Facebookcommerce_Integration' 	=> array( '10' => 'checks' ),	
														'WC_USPS' 							=> array( '10' => 'environment_check' ),
														'SP_Admin' 							=> array( '10' => 'activation_notice' ),
														'Woocommerce_Square' 				=> array( '10' => 'is_connected_to_square' ),
														'WC_Taxjar' 						=> array( '10' => 'maybe_display_admin_notices' ),
														'WC_Klarna_Payments' 				=> array( '10' => 'order_management_check' ),
														'Klarna_Checkout_For_WooCommerce' 	=> array( '10' => 'order_management_check' ),
														'WC_Gateway_PayFast'				=> array( '10' => 'admin_notices' ),
														'WC_Connect_Nux'					=> array( '9' => 'show_banner_before_connection' ),
														'Storefront_NUX_Admin' 				=> array( '99' => 'admin_notices' )
												);
		foreach ( $extension_admin_notices_to_suppress as $class_name => $function_to_suppress ) {
			WC_Calypso_Bridge_Helper_Functions::remove_class_action( 'admin_notices', $class_name, current( $function_to_suppress ), key( $function_to_suppress ) );
		}
		// List of extensions that do not use class level functions for admin notices.
		$other_admin_notices = array( 'woocommerce_gateway_paypal_express_upgrade_notice', 'woocommerce_gateway_klarna_welcome_notice' );
		foreach ( $other_admin_notices as $function_to_suppress ) {
			remove_action( 'admin_notices', $function_to_suppress );
		}
		// Suppress: Looking for the store notice setting? It can now be found in the Customizer.
		$updated = update_user_meta( get_current_user_id(), 'dismissed_store_notice_setting_moved_notice', true );
		// Suppress: Product Add Ons Activation Notice
		$deleted = delete_option( 'wpa_activation_notice' );
		// Suppress all other WC Admin Notices not specified above
		WC_Admin_Notices::remove_all_notices();
	}
	
}

WC_Calypso_Bridge::instance();
