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
		add_action( 'admin_notices', array( $this, 'maybe_add_settings_notice' ), 10, 2 );
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

	public function maybe_add_settings_notice() {
		if ('woocommerce_page_wc-settings' === get_current_screen()->base && isset( $_GET['tab'] ) && $_GET['tab'] === 'checkout' ) {
			if ( 'yes' === get_option( 'wc_calypso_bridge_payments_dismissed', 'no' ) ) {
				$limited_time_offer = esc_html__( 'Limited time offer', 'wc-calypso-bridge' );
				$save_big = esc_html__( 'Save big with WooCommerce Payments', 'wc-calypso-bridge' );
				$banner_copy = esc_html__( 'No transaction fees for up to 3 months (or $25,000 in payments)', 'wc-calypso-bridge' );
				$learn_more = esc_html__('Learn more', 'wc-calypso-bridge');

				echo '<div class="notice notice-info"><p>';
				printf(
					'<strong>%s</strong>. %s - %s.',
					$limited_time_offer,
					$save_big,
					$banner_copy
				);
				echo '</p><p>';
				printf( '<a class="button" href="/wp-admin/admin.php?page=wc-admin&path=/payments-welcome&enable_menu=1">%s</a>', $learn_more );
				echo '</div>';
			}
		}
	}
}

WC_Calypso_Bridge_Payments::get_instance();
