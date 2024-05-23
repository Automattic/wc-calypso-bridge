<?php
/**
 * Controller for assets and shared content.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 2.2.26
 */

defined( 'ABSPATH' ) || exit;

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
	 * Class instance.
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Both ecommerce and business.
		if ( ! is_admin() ) {
			return;
		}

		/**
		 * Add webpack assets.
		 */
		add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts' ) );

		/**
		 * Nav unification style fixes.
		 */
		if ( function_exists( 'wpcomsh_activate_nav_unification' ) && wpcomsh_activate_nav_unification() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'add_nav_unification_styles' ) );
		}

		/**
		 * Load Ecommerce styles.
		 */
		if ( wc_calypso_bridge_has_ecommerce_features() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'add_ecommerce_plan_styles' ) );
		}

		/**
		 * Load Ecommerce trial styles.
		 */
		if ( wc_calypso_bridge_is_ecommerce_trial_plan() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'add_ecommerce_trial_plan_styles' ) );
		}
	}

	/**
	 * Registers scripts.
	 */
	public function add_scripts() {

		$is_woo_page = class_exists( 'Automattic\WooCommerce\Admin\PageController' )
			&& \Automattic\WooCommerce\Admin\PageController::is_admin_or_embed_page()
			? true
			: false;

		$script_path       = '/build/index.js';
		$script_asset_path = WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/build/index.asset.php';
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

		// Rewrite script relative path when used as dependency to calculate the right filename.
		add_filter( 'load_script_textdomain_relative_path', function( $relative ) {
			if ( $relative === 'vendor/automattic/wc-calypso-bridge/build/index.js' ) {
				return 'build/index.js';
			}

			return $relative;
		});

		wp_set_script_translations( 'wc-calypso-bridge', 'wc-calypso-bridge', WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/languages' );

		$style_path     = 'build/style-index.css';
		$style_path_url = plugins_url( $style_path, __FILE__ );
		$res            = wp_register_style(
			'wc-calypso-bridge',
			$style_path_url,
			array(),
			filemtime( WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/build/style-index.css' )
		);

		$site_suffix = WC_Calypso_Bridge_Instance()->get_site_slug();
		$is_ecom_trial = (bool) wc_calypso_bridge_is_ecommerce_trial_plan();


		$params      = array(
			'isEcommercePlan'              => (bool) wc_calypso_bridge_has_ecommerce_features(),
			'isEcommercePlanTrial'         => $is_ecom_trial, // This is true for ecommerce trial only.
			'isWooExpress'                 => (bool) wc_calypso_bridge_is_woo_express_plan(),
			'isWooNavigationEnabled'       => (bool) apply_filters( 'ecommerce_new_woo_atomic_navigation_enabled', 'yes' === get_option( 'wooexpress_navigation_enabled', 'yes' ) ) && class_exists('\Jetpack') && \Jetpack::is_module_active( 'sso' ),
			'isWooPage'                    => $is_woo_page,
			'homeUrl'                      => esc_url( get_home_url() ),
			'siteSlug'                     => $site_suffix,
			'adminHomeUrl'                 => esc_url( admin_url( 'admin.php?page=wc-admin' ) ),
			'assetPath'                    => esc_url( WC_Calypso_Bridge_Instance()->get_asset_path() ),
			'wcpayConnectUrl'              => 'admin.php?page=wc-admin&path=%2Fpayments%2Fconnect&wcpay-connect=1&_wpnonce=' . wp_create_nonce( 'wcpay-connect' ),
			'hasViewedPayments'            => get_option( 'wc_calypso_bridge_payments_view_welcome_timestamp', false ) !== false,
			'version'                      => WC_CALYPSO_BRIDGE_CURRENT_VERSION,
			'i18n'                         => array(
				'baseUrl' => plugins_url( '/languages', __FILE__ ),
				'locale' => get_user_locale(),
			),
		);

		/**
		 * `wc_calypso_bridge_shared_params` filter.
		 *
		 * This filter is used to add additional shared params to the bridge.
		 *
		 * @since 2.3.5
		 *
		 * @param  array $params
		 * @return array
		 */
		$params = apply_filters( 'wc_calypso_bridge_shared_params', $params );

		if ( $is_ecom_trial ) {
			$woo_express_introductory_offers = WC_Calypso_Bridge_Woo_Express_Introductory_offers::get_offers_for_current_blog( function( $offer ) {
				return in_array( $offer['product_slug'], WC_Calypso_Bridge_Woo_Express_Introductory_offers::WOO_EXPRESS_PRODUCT_SLUGS );
			} );
			if ( count( $woo_express_introductory_offers ) ) {
				$params['wooExpressIntroductoryOffer'] = WC_Calypso_Bridge_Woo_Express_Introductory_offers::extract_offer_data_for_js( current( $woo_express_introductory_offers ) );
			}
		}


		if ( wc_calypso_bridge_has_ecommerce_features() ) {
			$params['showEcommerceNavigationModal'] = ! WC_Calypso_Bridge_Helper_Functions::is_wc_admin_installed_gte( WC_Calypso_Bridge::RELEASE_DATE_ECOMMERCE_NAVIGATION );
		}

		wp_add_inline_script(
			'wc-calypso-bridge',
			'window.wcCalypsoBridge = ' . wp_json_encode( $params ),
			'before'
		);

		wp_enqueue_script( 'wc-calypso-bridge' );
		wp_enqueue_style( 'wc-calypso-bridge' );

		// Inject the WC data store patch for WooCommerce < 7.7.0 with Gutenberg 15.5+
		// Issue: https://github.com/Automattic/wp-calypso/issues/76000
		$has_gutenberg             = is_plugin_active( 'gutenberg/gutenberg.php' );
		$gutenberg_version         = $has_gutenberg ? get_plugin_data( WP_PLUGIN_DIR . '/gutenberg/gutenberg.php' )['Version'] : false;

		if (
			defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '7.7.0', '<' ) &&
			$gutenberg_version && version_compare( $gutenberg_version, '15.5.0', '>=' )
		) {
			wp_enqueue_script(
				'wp-calypso-bridge-wc-data-patch',
				WC_Calypso_Bridge_Instance()->get_asset_path() . 'assets/scripts/wc-data-patch.js', array(), WC_CALYPSO_BRIDGE_CURRENT_VERSION,
				array( 'wc-store-data', 'wp-data', 'wp-element', 'wp-compose' )
			);
		}
	}

	/**
	 * Add styles for nav unification fixes.
	 */
	public function add_nav_unification_styles() {
		wp_enqueue_style( 'wp-calypso-bridge-nav-unification', WC_Calypso_Bridge_Instance()->get_asset_path() . '/assets/css/nav-unification.css', array(), WC_CALYPSO_BRIDGE_CURRENT_VERSION );
	}

	/**
	 * Add styles for ecommerce plan.
	 */
	public function add_ecommerce_plan_styles() {
		wp_enqueue_style( 'wp-calypso-bridge-ecommerce', WC_Calypso_Bridge_Instance()->get_asset_path() . 'assets/css/ecommerce.css', array(), WC_CALYPSO_BRIDGE_CURRENT_VERSION );

		if ( (bool) apply_filters( 'ecommerce_new_woo_atomic_navigation_enabled', 'yes' === get_option( 'wooexpress_navigation_enabled', 'yes' ) ) && class_exists('\Jetpack') && \Jetpack::is_module_active( 'sso' ) ) {
			wp_enqueue_style( 'wp-calypso-bridge-ecommerce-navigation', WC_Calypso_Bridge_Instance()->get_asset_path() . 'assets/css/ecommerce-navigation.css', array(), WC_CALYPSO_BRIDGE_CURRENT_VERSION );
		}
	}

	/**
	 * Add styles for ecommerce plan trial.
	 */
	public function add_ecommerce_trial_plan_styles() {
		wp_enqueue_style( 'wp-calypso-bridge-ecommerce-trial', WC_Calypso_Bridge_Instance()->get_asset_path() . 'assets/css/free-trial-admin.css', array(), WC_CALYPSO_BRIDGE_CURRENT_VERSION );
	}
}

WC_Calypso_Bridge_Shared::instance();
