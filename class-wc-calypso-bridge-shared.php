<?php
/**
 * Controller for shared content.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Loader;

/**
 * WC Calypso Bridge
 */
class WC_Calypso_Bridge_Shared {

	/**
	 * Class Instance.
	 *
	 * @var WC_Calypso_Bridge_Shared instance
	 */
	protected static $instance = null;

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Both ecommerce and business.
		if ( ! is_admin() && ! defined( 'DOING_CRON' ) ) {
			return;
		}

		add_action( 'plugins_loaded', array( $this, 'init' ), 2 );
		add_action( 'current_screen', array( $this, 'load_ui_elements' ) );
	}

	/**
	 * Class instance.
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize only if WC is present.
	 */
	public function init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'add_extension_register_script' ) );
	}

	/**
	 * Registers scripts.
	 */
	public function add_extension_register_script() {

		$is_woo_page = class_exists( 'Automattic\WooCommerce\Admin\PageController' )
			&& \Automattic\WooCommerce\Admin\PageController::is_admin_or_embed_page()
			? true
			: false;

		$script_path       = '/build/index.js';
		$script_asset_path = dirname( __FILE__ ) . '/build/index.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require $script_asset_path
			: array(
				'dependencies' => array(),
				'version'      => filemtime( $script_path ),
			);
		$script_url        = plugins_url( $script_path, __FILE__ );

		wp_register_script(
			'wc-calypso-bridge',
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		$style_path     = 'build/style-index.css';
		$style_path_url = plugins_url( $style_path, __FILE__ );
		$res            = wp_register_style(
			'wc-calypso-bridge',
			$style_path_url,
			array(),
			filemtime( dirname( __FILE__ ) . '/build/style-index.css' )
		);

		$status       = new \Automattic\Jetpack\Status();
		$site_suffix  = $status->get_site_suffix();

		$params       = array(
			'isEcommercePlan'              => (bool) wc_calypso_bridge_is_ecommerce_plan(),
			'isWooNavigationEnabled'       => (bool) apply_filters( 'ecommerce_new_woo_atomic_navigation_enabled', true ),
			'isWooPage'                    => $is_woo_page,
			'homeUrl'                      => esc_url( get_home_url() ),
			'siteSlug'                     => $site_suffix,
			'adminHomeUrl'                 => esc_url( admin_url( 'admin.php?page=wc-admin' ) ),
			'assetPath'                    => esc_url( WC_Calypso_Bridge_Instance()->get_asset_path() ),
			'wcpayConnectUrl'              => 'admin.php?page=wc-admin&path=%2Fpayments%2Fconnect&wcpay-connect=1&_wpnonce=' . wp_create_nonce( 'wcpay-connect' ),
			'hasViewedPayments'            => get_option( 'wc_calypso_bridge_payments_view_welcome_timestamp', false ) !== false,
		);

		if ( wc_calypso_bridge_is_ecommerce_plan() ) {
			$params['showEcommerceNavigationModal'] = ! WC_Calypso_Bridge_Helper_Functions::is_wc_admin_installed_gte( WC_Calypso_Bridge::RELEASE_DATE_ECOMMERCE_NAVIGATION );
		}

		wp_add_inline_script(
			'wc-calypso-bridge',
			'window.wcCalypsoBridge = ' . wp_json_encode( $params ),
			'before'
		);

		wp_enqueue_script( 'wc-calypso-bridge' );
		wp_enqueue_style( 'wc-calypso-bridge' );
	}

	/**
	 * Updates required UI elements for calypso bridge pages only.
	 */
	public function load_ui_elements() {

		// Nav unification fixes.
		if ( function_exists( 'wpcomsh_activate_nav_unification' ) && wpcomsh_activate_nav_unification( false ) && ! Loader::is_feature_enabled( 'navigation' ) ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'add_nav_unification_styles' ) );
		}
	}

	/**
	 * Add styles for nav unification fixes.
	 */
	public function add_nav_unification_styles() {
		wp_enqueue_style( 'wp-calypso-bridge-nav-unification', WC_Calypso_Bridge_Instance()->get_asset_path() . 'store-on-wpcom/assets/css/admin/nav-unification.css', array(), WC_CALYPSO_BRIDGE_CURRENT_VERSION );
	}
}

WC_Calypso_Bridge_Shared::instance();
