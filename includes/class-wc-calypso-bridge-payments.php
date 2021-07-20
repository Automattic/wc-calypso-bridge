<?php
/**
 * Notes.
 *
 * @package WC_Calypso_Bridge/Classes
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Calypso_Bridge_Payments Class.
 */
class WC_Calypso_Bridge_Payments {

	/**
	 * The single instance of the class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Get class instance.
	 *
	 * @return object Instance.
	 */
	final public static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Include notes and initialize note hooks.
	 */
	public function init() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ), 20 );
		add_action( 'admin_menu', array( $this, 'register_payments_welcome_page' ) );
		add_action( 'current_screen', array( $this, 'enqueue_scripts_and_styles' ) );
	}

	public function enqueue_scripts_and_styles() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_payments_welcome_page_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_wc_payments_style' ) );
	}

	/**
	 * Register REST API routes.
	 *
	 * New endpoints/controllers can be added here.
	 */
	public function register_routes() {
		/** API includes */
		include_once dirname( __FILE__ ) . '/payments/class-wc-payments-controller.php';
		$controller = new WC_Payments_Controller();
		$controller->register_routes();
	}

	/**
	 * Registers the WooCommerce Payments welcome page.
	 */
	public function register_payments_welcome_page() {
		global $menu;

		// WooCommerce must be active.
		if ( !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			return;
		}

		// WC Payment must not be active.
		if ( is_plugin_active( 'woocommerce-payments/woocommerce-payments.php' ) ) {
			return;
		}

		// Store country must be the US.
		if ( 'US' !== WC()->countries->get_base_country() ) {
			return;
		}

		if ( 'yes' === get_option( 'wc_calypso_bridge_payments_dismissed', 'no' ) ) {
			return;
		}

		$menu_icon = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMDAwIDEwMDAiPgogIDxwYXRoIGZpbGw9IiNhMmFhYjIiIGQ9Ik04NTQgMTcwLjY2N3Y1MTJoLTY4NHYtNTEyaDY4NHpNODU0IDc2OC42NjdjNDggMCA4NC0zOCA4NC04NnYtNTEyYzAtNDgtMzYtODYtODQtODZoLTY4NGMtNDggMC04NCAzOC04NCA4NnY1MTJjMCA0OCAzNiA4NiA4NCA4Nmg2ODR6TTQ3MCAyMTIuNjY3djQ0aC04NnY4NGgxNzB2NDRoLTEyOGMtMjQgMC00MiAxOC00MiA0MnYxMjhjMCAyNCAxOCA0MiA0MiA0Mmg0NHY0NGg4NHYtNDRoODZ2LTg0aC0xNzB2LTQ0aDEyOGMyNCAwIDQyLTE4IDQyLTQydi0xMjhjMC0yNC0xOC00Mi00Mi00MmgtNDR2LTQ0aC04NHoiLz4KPC9zdmc+';

		wc_admin_register_page( array(
			'id'       => 'wc-calypso-bridge-payments-welcome-page',
			'title'    => __( 'Payments', 'wc-calypso-bridge' ),
			'path'     => '/payments-welcome',
			'icon' => $menu_icon,
			'position' => '55.7',
			'nav_args'   => [
				'title'        => __( 'WooCommerce Payments', 'wc-calypso-bridge' ),
				'is_category'  => false,
				'menuId'       => 'plugins',
				'is_top_level' => true,
			],
		) );

		// Registering a top level menu via wc_admin_register_page doesn't work when the new
		// nav is enabled. The new nav disabled everything, except the 'WooCommerce' menu.
		// We need to register this menu via add_menu_page so that it doesn't become a child of
		// WooCommerce menu.
		if ( 'yes' === get_option('woocommerce_navigation_enabled', 'no') ) {
			add_menu_page(
				__( 'Payments', 'wc-calypso-bridge' ),
				__( 'Payments', 'wc-calypso-bridge' ),
				'view_woocommerce_reports',
				'admin.php?page=wc-admin&path=/payments-welcome',
				null,
				$menu_icon,
				'55.7' // After WooCommerce & Product menu items.
			);
		}

		// Add badge
		foreach ( $menu as $index => $menu_item ) {
			if ( 'wc-admin&path=/payments-welcome' === $menu_item[2]
			     || 'admin.php?page=wc-admin&path=/payments-welcome' === $menu_item[2] ) {
				$menu[ $index ][0] .= ' <span class="wcpay-menu-badge awaiting-mod count-1"><span class="plugin-count">1</span></span>';
				break;
			}
		}
	}

	/**
	 * Registers styles for WC Payments.
	 */
	public function add_wc_payments_style() {
		$asset_path = WC_Calypso_Bridge_Shared::$plugin_asset_path ? WC_Calypso_Bridge_Shared::$plugin_asset_path : WC_Calypso_Bridge_Shared::MU_PLUGIN_ASSET_PATH;
		wp_enqueue_style( 'wp-calypso-bridge-ecommerce', $asset_path . 'assets/css/wc-payments.css', array(), WC_CALYPSO_BRIDGE_CURRENT_VERSION );
	}


	/**
	 * Registers styles & scripts for WC Payments welcome page.
	 */
	public function enqueue_payments_welcome_page_scripts() {
		$css_file_version = filemtime( dirname( __FILE__ ) . '/../build/style-index.css' );

		wp_register_style(
			'wcpay-welcome-page',
			plugins_url( '../build/style-index.css', __FILE__ ),
			// Add any dependencies styles may have, such as wp-components.
			$css_file_version
		);

		wp_enqueue_style( 'wcpay-welcome-page' );
	}
}

WC_Calypso_Bridge_Payments::get_instance();
