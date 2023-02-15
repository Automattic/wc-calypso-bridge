<?php
/**
 * Free Trial related.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   2.0.0
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

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

		if ( ! $this->is_free_trial() ) {
			return;
		}

		add_action( 'init', array( $this, 'frontend' ), PHP_INT_MAX );
		add_action( 'admin_init', array( $this, 'backend' ), PHP_INT_MAX );

		// Disable Cash on Delivery.
		add_filter( 'pre_option_woocommerce_cod_settings', '__return_false', PHP_INT_MAX );
		add_filter( 'option_woocommerce_cod_settings', function ( $value ) {

			// Bail out early if the current user is allowed to create orders on free trial.
			if ( current_user_can( 'manage_woocommerce' ) ) {
				return $value;
			}

			$value['enabled'] = 'no';

			return $value;
		}, PHP_INT_MAX );

		// Disable Direct Bank Transfer.
		add_filter( 'pre_option_woocommerce_bacs_settings', '__return_false', PHP_INT_MAX );
		add_filter( 'option_woocommerce_bacs_settings', function ( $value ) {

			// Bail out early if the current user is allowed to create orders on free trial.
			if ( current_user_can( 'manage_woocommerce' ) ) {
				return $value;
			}

			$value['enabled'] = 'no';

			return $value;
		}, PHP_INT_MAX );

		// Disable Check Payments.
		add_filter( 'pre_option_woocommerce_cheque_settings', '__return_false', PHP_INT_MAX );
		add_filter( 'option_woocommerce_cheque_settings', function ( $value ) {

			// Bail out early if the current user is allowed to create orders on free trial.
			if ( current_user_can( 'manage_woocommerce' ) ) {
				return $value;
			}

			$value['enabled'] = 'no';

			return $value;
		}, PHP_INT_MAX );

		// Disable Stripe Express buttons.
		add_filter( 'pre_option_woocommerce_stripe_settings', '__return_false', PHP_INT_MAX );
		add_filter( 'option_woocommerce_stripe_settings', function ( $value ) {

			// Bail out early if the current user is allowed to create orders on free trial.
			if ( current_user_can( 'manage_woocommerce' ) ) {
				return $value;
			}

			$value['enabled']                          = 'no'; // Completely disable Stripe.
			$value['payment_request']                  = 'no'; // Apple Pay / Google Pay
			$value['payment_request_button_locations'] = array(); // Apple Pay / Google Pay

			return $value;
		}, PHP_INT_MAX );

		// Additional filters for Stripe - To be on the safe side.
		add_filter( 'wc_stripe_hide_payment_request_on_product_page', function ( $value, $post ) {

			// Bail out early if the current user is allowed to create orders on free trial.
			if ( current_user_can( 'manage_woocommerce' ) ) {
				return $value;
			}

			return false;
		}, PHP_INT_MAX, 2 );

		add_filter( 'wc_stripe_show_payment_request_on_cart', function ( $value ) {

			// Bail out early if the current user is allowed to create orders on free trial.
			if ( current_user_can( 'manage_woocommerce' ) ) {
				return $value;
			}

			return false;
		}, PHP_INT_MAX );

		add_filter( 'wc_stripe_show_payment_request_on_checkout', function ( $value, $post ) {

			// Bail out early if the current user is allowed to create orders on free trial.
			if ( current_user_can( 'manage_woocommerce' ) ) {
				return $value;
			}

			return false;
		}, PHP_INT_MAX, 2 );

		// Disable WooCommerce Payment Express checkouts.
		add_filter( 'pre_option_woocommerce_woocommerce_payments_settings', '__return_false', PHP_INT_MAX );
		add_filter( 'option_woocommerce_woocommerce_payments_settings', function ( $value ) {

			// Bail out early if the current user is allowed to create orders on free trial.
			if ( current_user_can( 'manage_woocommerce' ) ) {
				return $value;
			}

			$value['enabled']                            = 'no'; // Completely disable WC Payments.
			$value['payment_request']                    = 'no'; // Apple Pay / Google Pay.
			$value['payment_request_button_locations']   = array(); // Apple Pay / Google Pay.
			$value['platform_checkout']                  = 'no'; // WooPay.
			$value['platform_checkout_button_locations'] = array(); // WooPay.
			$value['upe_enabled_payment_method_ids']     = array(); // Link.

			return $value;
		}, PHP_INT_MAX );

		/****** PAYPAL Express Checkout / Smart Buttons ******/
		add_filter( 'pre_option_woocommerce-ppcp-settings', '__return_false', PHP_INT_MAX );
		add_filter( 'option_woocommerce-ppcp-settings', function ( $value ) {

			// Bail out early if the current user is allowed to create orders on free trial.
			if ( current_user_can( 'manage_woocommerce' ) ) {
				return $value;
			}

			$value['enabled'] = false; // Completely disable PayPal.

			// For PayPal, some of the following settings are needed to completely disable the gateway and smart buttons.
			$value['smart_button_locations'] = array();

			$value['button_product_enabled']   = false;
			$value['button_cart_enabled']      = false;
			$value['button_mini-cart_enabled'] = false;

			$value['pay_later_button_enabled']      = false;
			$value['pay_later_button_locations']    = array();
			$value['pay_later_messaging_enabled']   = false;
			$value['pay_later_messaging_locations'] = array();

			$value['products_dcc_enabled'] = false;
			$value['products_pui_enabled'] = false;

			$value['allow_card_button_gateway'] = false;

			return $value;
		}, PHP_INT_MAX );

		// Disable PayPal's OXXO gateway for non-admin users.
		add_filter( 'pre_option_woocommerce_ppcp-oxxo-gateway_settings', '__return_false', PHP_INT_MAX );
		add_filter( 'option_woocommerce_ppcp-oxxo-gateway_settings', function ( $value ) {

			// Bail out early if the current user is allowed to create orders on free trial.
			if ( current_user_can( 'manage_woocommerce' ) ) {
				return $value;
			}

			$value['enabled'] = 'no';

			return $value;
		}, PHP_INT_MAX );

		// Disable PayPal's Pay Upon Invoice gateway for non-admin users.
		add_filter( 'pre_option_woocommerce_ppcp-pay-upon-invoice-gateway_settings', '__return_false', PHP_INT_MAX );
		add_filter( 'option_woocommerce_ppcp-pay-upon-invoice-gateway_settings', function ( $value ) {

			// Bail out early if the current user is allowed to create orders on free trial.
			if ( current_user_can( 'manage_woocommerce' ) ) {
				return $value;
			}

			$value['enabled'] = 'no';

			return $value;
		}, PHP_INT_MAX );

	}

	/**
	 * Frontend free trial hooks.
	 */
	public function frontend() {

		// Bail out early if the current user is allowed to create orders on free trial.
		if ( current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		// Unset all payment gateways.
		add_filter( 'woocommerce_available_payment_gateways', function ( $gateways ) {
			return array();
		}, PHP_INT_MAX );

		// Change the "No available payment methods" message.
		add_filter( 'woocommerce_no_available_payment_methods_message', function ( $message ) {

			$message = __( 'This store is not ready to accept orders. Checkout functionality is currently enabled for preview purposes only.', 'wc-calypso-bridge' );

			return $message;

		}, PHP_INT_MAX );

		// Doesn't work, as it's filtered with JS. TODO!!!
		add_filter( 'gettext', function ( $translated_text, $text, $domain ) {

			if ( $domain === 'woocommerce' ) {
				switch ( $text ) {
					case 'There are no payment methods available. This may be an error on our side. Please contact us if you need any help placing your order.' :
						$translated_text = __( 'There are no payment methods available as this store is in trial mode.', 'wc-calypso-bridge' );
						break;
				}
			}

			return $translated_text;

		}, PHP_INT_MAX, 3 );

		// Prevent orders on shortcode checkout.
		add_action( 'woocommerce_before_checkout_process', function () {
			throw new Exception( __( 'Your order could not be placed. Checkout functionality is currently enabled for preview purposes only.', 'wc-calypso-bridge' ) );
		}, PHP_INT_MAX );

		// Prevent orders on shortcode checkout - PayPal removes the checkout button and replaces it.
		add_action( 'woocommerce_after_checkout_validation', function ( $data, $errors ) {
			$errors->add(
				409,
				__( 'Your order could not be placed. Checkout functionality is currently enabled for preview purposes only.', 'wc-calypso-bridge' )
			);
		}, PHP_INT_MAX, 2 );

		// Prevent orders on block checkout.
		add_action( 'woocommerce_store_api_checkout_order_processed', function () {
			throw new Automattic\WooCommerce\StoreApi\Exceptions\RouteException(
				409,
				__( 'Your order could not be placed. Checkout functionality is currently enabled for preview purposes only.', 'wc-calypso-bridge' )
			);
		}, PHP_INT_MAX );

		// Display an info message on the checkout page.
		add_filter( 'the_content', function ( $content ) {
			if ( ! is_checkout() ) {
				return $content;
			}

			$message = esc_html__( 'This store is not ready to accept orders. Checkout functionality is currently enabled for preview purposes only.', 'wc-calypso-bridge' );
			$markup  = '<div class="woocommerce"><div class="woocommerce-notices-wrapper"><ul class="woocommerce-info role="alert"><li> ' . $message . '</li></ul></div></div>';

			return $markup . $content;
		}, PHP_INT_MAX );

	}

	/**
	 * Backend free trial hooks.
	 */
	public function backend() {

		// Display notice in Admin > Payments settings.
		add_action( 'admin_notices', function () {

			$screen    = get_current_screen();
			$screen_id = $screen ? $screen->id : '';

			if ( 'woocommerce_page_wc-settings' !== $screen_id ) {
				return;
			}

			if ( ! isset( $_GET['tab'] ) || 'checkout' !== $_GET['tab'] ) {
				return;
			}

			$site_slug = ( new \Automattic\Jetpack\Status() )->get_site_suffix();
			$plan_url  = 'https://wordpress.com/plans/' . $site_slug;
			$message   = sprintf( __( 'Only Administrators and Store Managers can place orders during the free trial. If you are ready to start accepting payments from customers, <a href="%s">pick a plan</a>.', 'wc-calypso-bridge' ), $plan_url );
			?>
			<div class="notice notice-info">
				<p><?php echo $message; ?></p>
			</div>
			<?php
		}, PHP_INT_MAX );

	}

	/**
	 * @return boolean Whether the site is in free trial mode.
	 * @todo Properly check if free trial in enabled.
	 */
	public function is_free_trial() {
		return true;
	}

}

WC_Calypso_Bridge_Free_Trial::get_instance();
