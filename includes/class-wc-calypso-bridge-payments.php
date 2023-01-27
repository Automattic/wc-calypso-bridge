<?php
/**
 * Notes.
 *
 * @package WC_Calypso_Bridge/Classes
 * @version 1.9.4
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Features\Features;

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
		add_action( 'woocommerce_init', array( $this, 'init' ), 20 );
	}

	/**
	 * Include notes and initialize note hooks.
	 */
	public function init() {
		if ( $this->is_woocommerce_valid() && ! Features::is_enabled( 'wc-pay-welcome-page' ) ) {
			add_action( 'rest_api_init', array( $this, 'register_routes' ), 20 );
			add_action( 'admin_menu', array( $this, 'register_payments_welcome_page' ) );
			add_action( 'current_screen', array( $this, 'enqueue_scripts_and_styles' ) );
		} else {
			// load wc-payments.css so that the font can be used in WCA on WPCOM.
			add_action( 'admin_enqueue_scripts', array( $this, 'add_wc_payments_style' ) );
		}
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function enqueue_scripts_and_styles() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_payments_welcome_page_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_wc_payments_style' ) );
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
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			return;
		}

		// WC Payment must not be active.
		if ( is_plugin_active( 'woocommerce-payments/woocommerce-payments.php' ) ) {
			return;
		}

		// Store country must be in defined array.
		$supported_countries = array( 'US', 'GB', 'AU', 'NZ', 'CA', 'IE', 'ES', 'FR', 'IT', 'DE' );
		if ( ! in_array( WC()->countries->get_base_country(), $supported_countries ) ) {
			return;
		}

		if ( 'yes' === get_option( 'wc_calypso_bridge_payments_dismissed', 'no' ) ) {
			return;
		}

		$menu_icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjxzdmcKICAgdmVyc2lvbj0iMS4xIgogICBpZD0ic3ZnNjciCiAgIHNvZGlwb2RpOmRvY25hbWU9IndjcGF5X21lbnVfaWNvbi5zdmciCiAgIHdpZHRoPSI4NTIiCiAgIGhlaWdodD0iNjg0IgogICBpbmtzY2FwZTp2ZXJzaW9uPSIxLjEgKGM0ZThmOWUsIDIwMjEtMDUtMjQpIgogICB4bWxuczppbmtzY2FwZT0iaHR0cDovL3d3dy5pbmtzY2FwZS5vcmcvbmFtZXNwYWNlcy9pbmtzY2FwZSIKICAgeG1sbnM6c29kaXBvZGk9Imh0dHA6Ly9zb2RpcG9kaS5zb3VyY2Vmb3JnZS5uZXQvRFREL3NvZGlwb2RpLTAuZHRkIgogICB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiAgIHhtbG5zOnN2Zz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgogIDxkZWZzCiAgICAgaWQ9ImRlZnM3MSIgLz4KICA8c29kaXBvZGk6bmFtZWR2aWV3CiAgICAgaWQ9Im5hbWVkdmlldzY5IgogICAgIHBhZ2Vjb2xvcj0iI2ZmZmZmZiIKICAgICBib3JkZXJjb2xvcj0iIzY2NjY2NiIKICAgICBib3JkZXJvcGFjaXR5PSIxLjAiCiAgICAgaW5rc2NhcGU6cGFnZXNoYWRvdz0iMiIKICAgICBpbmtzY2FwZTpwYWdlb3BhY2l0eT0iMC4wIgogICAgIGlua3NjYXBlOnBhZ2VjaGVja2VyYm9hcmQ9IjAiCiAgICAgc2hvd2dyaWQ9ImZhbHNlIgogICAgIGZpdC1tYXJnaW4tdG9wPSIwIgogICAgIGZpdC1tYXJnaW4tbGVmdD0iMCIKICAgICBmaXQtbWFyZ2luLXJpZ2h0PSIwIgogICAgIGZpdC1tYXJnaW4tYm90dG9tPSIwIgogICAgIGlua3NjYXBlOnpvb209IjI1NiIKICAgICBpbmtzY2FwZTpjeD0iLTg0Ljg1NzQyMiIKICAgICBpbmtzY2FwZTpjeT0iLTgzLjI5NDkyMiIKICAgICBpbmtzY2FwZTp3aW5kb3ctd2lkdGg9IjEzMTIiCiAgICAgaW5rc2NhcGU6d2luZG93LWhlaWdodD0iMTA4MSIKICAgICBpbmtzY2FwZTp3aW5kb3cteD0iMTE2IgogICAgIGlua3NjYXBlOndpbmRvdy15PSIyMDIiCiAgICAgaW5rc2NhcGU6d2luZG93LW1heGltaXplZD0iMCIKICAgICBpbmtzY2FwZTpjdXJyZW50LWxheWVyPSJzdmc2NyIgLz4KICA8cGF0aAogICAgIHRyYW5zZm9ybT0ic2NhbGUoLTEsIDEpIHRyYW5zbGF0ZSgtODUwLCAwKSIKICAgICBkPSJNIDc2OCw4NiBWIDU5OCBIIDg0IFYgODYgWiBtIDAsNTk4IGMgNDgsMCA4NCwtMzggODQsLTg2IFYgODYgQyA4NTIsMzggODE2LDAgNzY4LDAgSCA4NCBDIDM2LDAgMCwzOCAwLDg2IHYgNTEyIGMgMCw0OCAzNiw4NiA4NCw4NiB6IE0gMzg0LDEyOCB2IDQ0IGggLTg2IHYgODQgaCAxNzAgdiA0NCBIIDM0MCBjIC0yNCwwIC00MiwxOCAtNDIsNDIgdiAxMjggYyAwLDI0IDE4LDQyIDQyLDQyIGggNDQgdiA0NCBoIDg0IHYgLTQ0IGggODYgViA0MjggSCAzODQgdiAtNDQgaCAxMjggYyAyNCwwIDQyLC0xOCA0MiwtNDIgViAyMTQgYyAwLC0yNCAtMTgsLTQyIC00MiwtNDIgaCAtNDQgdiAtNDQgeiIKICAgICBmaWxsPSIjYTJhYWIyIgogICAgIGlkPSJwYXRoNjUiIC8+Cjwvc3ZnPgo=';

		$menu_data = array(
			'id'       => 'wc-calypso-bridge-payments-welcome-page',
			'title'    => __( 'Payments', 'wc-calypso-bridge' ),
			'path'     => '/payments-welcome',
			'position' => '55.7',
			'nav_args' => array(
				'title'        => __( 'WooCommerce Payments', 'wc-calypso-bridge' ),
				'is_category'  => false,
				'menuId'       => 'plugins',
				'is_top_level' => true,
			),
		);

		$is_calypso_menu_request = 0 === strpos( $_SERVER['REQUEST_URI'], '/?rest_route=%2Fwpcom%2Fv2%2Fadmin-menu' );

		if ( $is_calypso_menu_request ) {
			$menu_data['icon'] = $menu_icon;
		}

		wc_admin_register_page( $menu_data );

		// Add badge.
		foreach ( $menu as $index => $menu_item ) {
			if (
				'wc-admin&path=/payments-welcome' === $menu_item[2]
				|| 'admin.php?page=wc-admin&path=/payments-welcome' === $menu_item[2]
			) {
				$menu[ $index ][0] .= ' <span class="wcpay-menu-badge awaiting-mod count-1"><span class="plugin-count">1</span></span>';
			}
		}
	}

	/**
	 * Registers styles for WC Payments.
	 */
	public function add_wc_payments_style() {
		$asset_path = WC_Calypso_Bridge_Instance()->get_asset_path();
		wp_enqueue_style( 'wp-calypso-bridge-ecommerce-payments', $asset_path . 'assets/css/wc-payments.css', array(), WC_CALYPSO_BRIDGE_CURRENT_VERSION );
	}

	/**
	 * Registers styles & scripts for WC Payments welcome page.
	 */
	public function enqueue_payments_welcome_page_scripts() {
		$css_file_version = filemtime( dirname( __FILE__ ) . '/../build/style-index.css' );

		wp_register_style(
			'wcpay-welcome-page',
			plugins_url( '../build/style-index.css', __FILE__ ),
			array(), // Add any dependencies styles may have, such as wp-components.
			$css_file_version
		);

		wp_enqueue_style( 'wcpay-welcome-page' );
	}
}

WC_Calypso_Bridge_Payments::get_instance();
