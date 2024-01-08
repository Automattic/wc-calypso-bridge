<?php
/**
 * Removes various admin alerts that should not be there.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 2.3.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge Hide Alerts
 */
class WC_Calypso_Bridge_Hide_Alerts {

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Bridge_Hide_Alerts instance
	 */
	protected static $instance = false;

	/**
	 * Get class instance
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {

		// Only in Ecommerce.
		if ( ! wc_calypso_bridge_has_ecommerce_features() ) {
			return;
		}

		// Includes.
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/notes/data/class-wc-calypso-bridge-admin-note-data-store.php';

		// Hooks. Init is called on priority 1 to ensure it runs before other init hooks.
		add_action( 'init', array( $this, 'init' ), 1 );
	}

	/**
	 * Initialize.
	 */
	public function init() {

		// Override Admin Notes datastore to implement performant allow/suppress-listing.
		add_filter( 'woocommerce_data_stores', array( $this, 'filter_notes_data_store' ), 100 );

		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_head', array( $this, 'hide_alerts_on_non_settings_pages' ) );
		add_filter( 'woocommerce_helper_suppress_connect_notice', '__return_true' );
		add_filter( 'woocommerce_show_admin_notice', '__return_false' );
		add_filter( 'woocommerce_allow_marketplace_suggestions', '__return_false' );

		add_action( 'admin_head', array( $this, 'suppress_admin_notices' ) );
		add_action( 'load-index.php', array( $this, 'maybe_remove_somewherewarm_maintenance_notices' ) );
		add_action( 'load-plugins.php', array( $this, 'maybe_remove_somewherewarm_maintenance_notices' ) );
	}

	/**
	 * Overrides Notes data store.
	 *
	 * @since 2.2.20
	 *
	 * @param array $data_stores List of data stores.
	 * @return array
	 */
	public static function filter_notes_data_store( $data_stores ) {
		$data_stores['admin-note'] = 'WC_Calypso_Bridge_Admin_Note_Data_Store';

		return $data_stores;
	}

	/**
	 * Prevents some alerts like the Apple Pay alert and Akismet from being shown on pages besides settings pages / core wp-admin pages.
	 */
	public function hide_alerts_on_non_settings_pages() {
		if ( empty( $_GET['page'] ) || 'wc-settings' !== $_GET['page'] ) {
			WC_Calypso_Bridge_Helper_Functions::remove_class_action( 'admin_notices', 'WC_Stripe_Apple_Pay_Registration', 'admin_notices', 10 );
			remove_action( 'admin_notices', array( 'Akismet_Admin', 'display_notice' ) );
		}
	}

	/**
	 * Suppresses admin notifications in wp-admin.
	 *
	 * @return void
	 */
	public function suppress_admin_notices() {
		/**
		 * List of extension specific and themes class level functions to suppress
		 * 'CLASS_NAME' => array( 'FUNCTION_PRIORITY' => 'FUNCTION_NAME' ).
		 */
		$extension_admin_notices_to_suppress = array(
			'WC_Shipping_Australia_Post_Init' => array( '10' => 'environment_check' ),
			'WC_Facebookcommerce_Integration' => array( '10' => 'checks' ),
			'WC_USPS'                         => array( '10' => 'environment_check' ),
			'SP_Admin'                        => array( '10' => 'activation_notice' ),
			'Woocommerce_Square'              => array( '10' => 'is_connected_to_square' ),
			'WC_Taxjar'                       => array( '10' => 'maybe_display_admin_notices' ),
			'WC_Klarna_Payments'              => array( '10' => 'order_management_check' ),
			'Klarna_Checkout_For_WooCommerce' => array( '10' => 'order_management_check' ),
			'WC_Gateway_PayFast'              => array( '10' => 'admin_notices' ),
			'WC_Connect_Nux'                  => array( '9' => 'show_banner_before_connection' ),
			'Storefront_NUX_Admin'            => array( '99' => 'admin_notices' ),
			'WC_Gateway_PPEC_Plugin'          => array( '10' => 'show_bootstrap_warning' ),
			'WC_RoyalMail'                    => array( '10' => 'environment_check' ),
			'Storefront_Blog_Customiser'      => array( '10' => 'customizer_notice' ),
			'Storefront_Parallax_Hero'        => array( '10' => 'customizer_notice' ),
			'Storefront_Product_Hero'         => array( '10' => 'sprh_customizer_notice' ),
			'Storefront_Reviews'              => array( '10' => 'customizer_notice' ),
		);

		foreach ( $extension_admin_notices_to_suppress as $class_name => $function_to_suppress ) {
			WC_Calypso_Bridge_Helper_Functions::remove_class_action( 'admin_notices', $class_name, current( $function_to_suppress ), key( $function_to_suppress ) );
		}

		// Square Specific - refactor after launch to be included in the above loop.
		WC_Calypso_Bridge_Helper_Functions::remove_class_action( 'admin_notices', 'Woocommerce_Square', 'check_environment', 10 );
		WC_Calypso_Bridge_Helper_Functions::remove_class_action( 'admin_notices', 'Woocommerce_Square', 'is_connected_to_square', 10 );

		WC_Calypso_Bridge_Helper_Functions::remove_class_action( 'in_admin_header', 'WC_Klarna_Banners', 'klarna_banner', 10 );
		WC_Calypso_Bridge_Helper_Functions::remove_class_action( 'in_admin_header', 'WC_Klarna_Banners_KP', 'klarna_banner', 10 );

		// List of extensions that do not use class level functions for admin notices.
		$other_admin_notices = array(
			'woocommerce_gateway_paypal_express_upgrade_notice',
			'woocommerce_gateway_klarna_welcome_notice',
		);
		foreach ( $other_admin_notices as $function_to_suppress ) {
			remove_action( 'admin_notices', $function_to_suppress );
		}

		// Suppress: Looking for the store notice setting? It can now be found in the Customizer.
		$user_id                 = get_current_user_id();
		$user_meta_key           = 'dismissed_store_notice_setting_moved_notice';
		$current_user_meta_value = get_user_meta( $user_id, $user_meta_key, true );
		if ( ! $current_user_meta_value ) {
			update_user_meta( $user_id, $user_meta_key, true );
		}

		// Suppress: Product Add Ons Activation Notice.
		delete_option( 'wpa_activation_notice' );

		/**
		 * Suppress: Facebook for WooCommerce welcome notices.
		 * There is no hook to remove them, so the safest choice is to dismiss them per user
		 * if they haven't been dismissed already.
		 *
		 * @since 1.9.5
		 */
		if (
			function_exists( 'facebook_for_woocommerce' )
			&& method_exists( facebook_for_woocommerce(), 'get_admin_notice_handler' )
			&& method_exists( facebook_for_woocommerce()->get_admin_notice_handler(), 'is_notice_dismissed' )
			&& method_exists( facebook_for_woocommerce()->get_admin_notice_handler(), 'dismiss_notice' )
		) {
			$fb_admin_notice_handler = facebook_for_woocommerce()->get_admin_notice_handler();
			$fb_admin_notices        = array(
				'facebook_for_woocommerce_get_started',
				'settings_moved_to_marketing',
			);

			foreach ( $fb_admin_notices as $message_id ) {
				if ( ! $fb_admin_notice_handler->is_notice_dismissed( $message_id ) ) {
					$fb_admin_notice_handler->dismiss_notice( $message_id );
				}
			}
		}

		// Suppress all other WC Admin Notices not specified above.
		WC_Admin_Notices::remove_notice( 'wootenberg' );
		WC_Admin_Notices::remove_all_notices();
	}

	/**
	 * Disable activation notices, specific for SomewhereWarm plugins as they share the same logic.
	 * Filters out the `welcome` notice from the list of notices to be displayed.
	 *
	 * It's specifically hooked on `load-index.php` and `load-plugins.php`
	 * as all plugins display notices only on these pages.
	 *
	 * @since 1.9.4
	 * @return void
	 */
	public function maybe_remove_somewherewarm_maintenance_notices() {

		$classes = array(
			'WC_GC_Admin_Notices', // Gift Cards.
			'WC_PB_Admin_Notices', // Product Bundles.
			'WC_BIS_Admin_Notices', // Back In Stock.
			'WC_PRL_Admin_Notices', // Product Recommendations.
		);

		foreach ( $classes as $class ) {

			if ( class_exists( $class ) && $class::is_maintenance_notice_visible( 'welcome' ) ) {
				$class::$maintenance_notices = array_filter( $class::$maintenance_notices, static function ( $element ) {
					return 'welcome' !== $element;
				} );
			}

		}

	}

}

WC_Calypso_Bridge_Hide_Alerts::get_instance();
