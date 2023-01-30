<?php
/**
 * Free Trial related.
 *
 * @package WC_Calypso_Bridge/Classes
 * @version 1.9.19
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Features\Features;

/**
 * WC_Calypso_Bridge_Free_Trial Class.
 */
class WC_Calypso_Bridge_Free_Trial {

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
	 * Free trial hooks.
	 */
	public function init() {

		// Bail out early if the site is not in free trial mode.
		if ( ! $this->is_free_trial() ) {
			return;
		}

		// Bail out early if the current user is allowed to create orders on free trial.
		if ( current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		// Prevent orders on shortcode checkout.
		add_action( 'woocommerce_before_checkout_process', function () {
			throw new Exception( __( 'TODO: Only Admins and Shop Managers can place test orders. Contact an Admin to get permission.', 'wc-calypso-bridge' ) );
		}, PHP_INT_MAX );

		// Prevent orders on shortcode checkout - PayPal removes the checkout button and replaces it.
		add_action( 'woocommerce_after_checkout_validation', function ( $data, $errors ) {
			$errors->add(
				405,
				__( 'TODO: Only Admins and Shop Managers can place test orders. Contact an Admin to get permission.', 'wc-calypso-bridge' )
			);
		}, PHP_INT_MAX, 2 );

		// Prevent orders on block checkout.
		add_action( 'woocommerce_store_api_checkout_order_processed', function () {
			throw new Automattic\WooCommerce\StoreApi\Exceptions\RouteException(
				405,
				__( 'TODO: Only Admins and Shop Managers can place test orders. Contact an Admin to get permission.', 'wc-calypso-bridge' )
			);
		}, PHP_INT_MAX );

		// Display notice on the checkout page
		add_filter( 'the_content', function ( $content ) {
			if ( ! is_checkout() ) {
				return $content;
			}

			$message = __( 'TODO: Only Admins and Shop Managers can place test orders. Contact an Admin to get permission.', 'wc-calypso-bridge' );
			$markup  = '<div class="woocommerce"><div class="woocommerce-notices-wrapper"><ul class="woocommerce-error" role="alert"><li> ' . $message . '</li></ul></div></div>';

			return $markup . $content;
		}, PHP_INT_MAX );

		// Display notice in Admin > Payments settings.
		add_action( 'admin_notices', function () {

			$screen    = get_current_screen();
			$screen_id = $screen ? $screen->id : '';

			if ( 'woocommerce_page_wc-settings' !== $screen_id ) {
				return;
			}

			if ( isset( $_GET['tab'] ) && 'checkout' === $_GET['tab'] ) {
				$screen_id .= '_checkout';
			}

			$show_on_screens = array(
				'woocommerce_page_wc-settings_checkout',
			);

			if ( ! in_array( $screen_id, $show_on_screens, true ) ) {
				return;
			}

			$blog_id   = get_current_blog_id();
			$site_url  = get_home_url( $blog_id );
			$site_slug = wp_parse_url( $site_url, PHP_URL_HOST );
			$plan_url  = 'https://wordpress.com/plans/' . $site_slug;
			$message   = sprintf( __( 'TODO: During trial, only Admins and Shop Managers can place orders. To process real transactions, <a href="%s">pick a plan</a>.', 'wc-calypso-bridge' ), $plan_url );
			?>
			<div class="notice notice-warning">
				<p><?php echo $message; ?></p>
			</div>
			<?php
		}, PHP_INT_MAX );

		/****** PAYPAL ******/

		add_filter( 'pre_option_woocommerce-ppcp-settings', '__return_false', PHP_INT_MAX );
		// There is no need for a `default_option` filter, as the default value is an empty array.
		add_filter( 'option_woocommerce-ppcp-settings', function ( $value ) {
			$value['button_product_enabled']      = false;
			$value['button_cart_enabled']         = false;
			$value['button_mini-cart_enabled']    = false;
			$value['pay_later_button_enabled']    = false;
			$value['pay_later_messaging_enabled'] = false;

			return $value;
		}, PHP_INT_MAX );

		// Alternatively, we can completely disable PayPal for non-admin users on free trial.
		add_filter( 'pre_option_woocommerce_ppcp-gateway_settings', '__return_false', PHP_INT_MAX );
		add_filter( 'option_woocommerce_ppcp-gateway_settings', function ( $value ) {
			$value['enabled'] = 'no';

			return $value;
		}, PHP_INT_MAX );

		add_filter( 'pre_option_woocommerce_ppcp-oxxo-gateway_settings', '__return_false', PHP_INT_MAX );
		add_filter( 'option_woocommerce_ppcp-oxxo-gateway_settings', function ( $value ) {
			$value['enabled'] = 'no';

			return $value;
		}, PHP_INT_MAX );

		add_filter( 'pre_option_woocommerce_ppcp-pay-upon-invoice-gateway_settings', '__return_false', PHP_INT_MAX );
		add_filter( 'option_woocommerce_ppcp-pay-upon-invoice-gateway_settings', function ( $value ) {
			$value['enabled'] = 'no';

			return $value;
		}, PHP_INT_MAX );

		add_filter( 'pre_option_woocommerce_ppcp-pay-upon-invoice-gateway_settings', '__return_false', PHP_INT_MAX );
		add_filter( 'option_woocommerce_ppcp-pay-upon-invoice-gateway_settings', function ( $value ) {
			$value['enabled'] = 'no';

			return $value;
		}, PHP_INT_MAX );

	}

	/**
	 * @return boolean Whether the site is in free trial mode.
	 */
	public function is_free_trial() {
		return true;
	}

}

WC_Calypso_Bridge_Free_Trial::get_instance();
