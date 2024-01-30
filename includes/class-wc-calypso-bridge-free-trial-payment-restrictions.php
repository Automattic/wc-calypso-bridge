<?php
/**
 * Free Trial related.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   2.0.4
 * @version 2.3.3
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Calypso_Bridge_Free_Trial Class.
 */
class WC_Calypso_Bridge_Free_Trial_Payment_Restrictions {

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

		// Bail out early if the current site is not on a free trial.
		if ( ! wc_calypso_bridge_is_ecommerce_trial_plan() ) {
			return;
		}

		add_action( 'init', array( $this, 'frontend' ), PHP_INT_MAX );
		add_action( 'admin_init', array( $this, 'backend' ), PHP_INT_MAX );

		// Disable Cash on Delivery.
		// There is no need to filter the default options. Double-checked in a pristine DB.
		add_filter( 'pre_option_woocommerce_cod_settings', '__return_false', PHP_INT_MAX );
		add_filter( 'option_woocommerce_cod_settings', function ( $value ) {

			// Bail out early if the current user is allowed to create orders on free trial.
			if ( ! is_array( $value ) || current_user_can( 'manage_woocommerce' ) ) {
				return $value;
			}

			$value['enabled'] = 'no';

			return $value;
		}, PHP_INT_MAX );

		// Disable Direct Bank Transfer.
		// There is no need to filter the default options. Double-checked in a pristine DB.
		add_filter( 'pre_option_woocommerce_bacs_settings', '__return_false', PHP_INT_MAX );
		add_filter( 'option_woocommerce_bacs_settings', function ( $value ) {

			// Bail out early if the current user is allowed to create orders on free trial.
			if ( ! is_array( $value ) || current_user_can( 'manage_woocommerce' ) ) {
				return $value;
			}

			$value['enabled'] = 'no';

			return $value;
		}, PHP_INT_MAX );

		// Disable Check Payments.
		// There is no need to filter the default options. Double-checked in a pristine DB.
		add_filter( 'pre_option_woocommerce_cheque_settings', '__return_false', PHP_INT_MAX );
		add_filter( 'option_woocommerce_cheque_settings', function ( $value ) {

			// Bail out early if the current user is allowed to create orders on free trial.
			if ( ! is_array( $value ) || current_user_can( 'manage_woocommerce' ) ) {
				return $value;
			}

			$value['enabled'] = 'no';

			return $value;
		}, PHP_INT_MAX );

		// Disable Stripe Express buttons.
		// There is no need to filter the default options. Double-checked in a pristine DB.
		add_filter( 'pre_option_woocommerce_stripe_settings', '__return_false', PHP_INT_MAX );
		add_filter( 'option_woocommerce_stripe_settings', function ( $value ) {

			// Bail out early if the current user is allowed to create orders on free trial.
			if ( ! is_array( $value ) || current_user_can( 'manage_woocommerce' ) ) {
				return $value;
			}

			$value['enabled']                          = 'no';
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

			return true;
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
		// There is no need to filter the default options. Double-checked in a pristine DB.
		add_filter( 'pre_option_woocommerce_woocommerce_payments_settings', '__return_false', PHP_INT_MAX );
		add_filter( 'option_woocommerce_woocommerce_payments_settings', function ( $value ) {

			// Bail out early if the current user is allowed to create orders on free trial.
			if ( ! is_array( $value ) || current_user_can( 'manage_woocommerce' ) ) {
				return $value;
			}

			$value['enabled']                            = 'no';
			$value['payment_request']                    = 'no'; // Apple Pay / Google Pay.
			$value['payment_request_button_locations']   = array(); // Apple Pay / Google Pay.
			$value['platform_checkout']                  = 'no'; // WooPay.
			$value['platform_checkout_button_locations'] = array(); // WooPay.
			$value['upe_enabled_payment_method_ids']     = array(); // Link.

			return $value;
		}, PHP_INT_MAX );

		// PAYPAL Express Checkout / Smart Buttons.
		// There is no need to filter the default options. Double-checked in a pristine DB.
		add_filter( 'pre_option_woocommerce-ppcp-settings', '__return_false', PHP_INT_MAX );
		add_filter( 'option_woocommerce-ppcp-settings', function ( $value ) {

			// Bail out early if the current user is allowed to create orders on free trial.
			if ( ! is_array( $value ) || current_user_can( 'manage_woocommerce' ) ) {
				return $value;
			}

			$value['enabled'] = false;

			// Express Checkout / Smart Buttons
			$value['smart_button_locations']   = array();
			$value['button_product_enabled']   = false;
			$value['button_cart_enabled']      = false;
			$value['button_mini-cart_enabled'] = false;

			// Pay Later
			$value['pay_later_button_enabled']      = false;
			$value['pay_later_button_locations']    = array();
			$value['pay_later_messaging_enabled']   = false;
			$value['pay_later_messaging_locations'] = array();

			$value['products_pui_enabled'] = false; // Pay Upon Invoice.
			$value['products_dcc_enabled'] = false; // PayPal Card Processing.

			$value['allow_card_button_gateway'] = false; // Separate gateway button.

			return $value;
		}, PHP_INT_MAX );

		// Disable PayPal's OXXO gateway for non-admin users.
		// Couldn't test this. Given that PayPal will already be disabled from the rest of the filters, this is me being extra cautious.
		add_filter( 'pre_option_woocommerce_ppcp-oxxo-gateway_settings', '__return_false', PHP_INT_MAX );
		add_filter( 'option_woocommerce_ppcp-oxxo-gateway_settings', function ( $value ) {

			// Bail out early if the current user is allowed to create orders on free trial.
			if ( ! is_array( $value ) || current_user_can( 'manage_woocommerce' ) ) {
				return $value;
			}

			$value['enabled'] = 'no';

			return $value;
		}, PHP_INT_MAX );

		// Disable PayPal's Pay Upon Invoice gateway for non-admin users.
		// Couldn't test this. Given that PayPal will already be disabled from the rest of the filters, this is me being extra cautious.
		add_filter( 'pre_option_woocommerce_ppcp-pay-upon-invoice-gateway_settings', '__return_false', PHP_INT_MAX );
		add_filter( 'option_woocommerce_ppcp-pay-upon-invoice-gateway_settings', function ( $value ) {

			// Bail out early if the current user is allowed to create orders on free trial.
			if ( ! is_array( $value ) || current_user_can( 'manage_woocommerce' ) ) {
				return $value;
			}

			$value['enabled'] = 'no';

			return $value;
		}, PHP_INT_MAX );

		// Only allow specific gateways as suggestions in admin.
		add_filter( 'woocommerce_admin_payment_gateway_suggestion_specs', function ( $gateways ) {

			if ( ! is_array( $gateways ) ) {
				return $gateways;
			}

			$allowed = array(
				'woocommerce_payments',
				'woocommerce_payments:without-in-person-payments',
				'woocommerce_payments:with-in-person-payments',
				'stripe',
				'ppcp-gateway',
				'cod',
				'bacs',
				'cheque',
			);

			$allowed_gateways = array_filter( $gateways, function ( $key ) use ( $allowed ) {
				return in_array( $key, $allowed, true );
			}, ARRAY_FILTER_USE_KEY );

			return $allowed_gateways;

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

		add_filter( 'gettext', function ( $translated_text, $text, $domain ) {

			if ( $domain === 'woocommerce' ) {
				switch ( $text ) {
					// Since we're removing all payment gateways, we need to change the "No available payment methods" message.
					case 'No payment method provided.' :
						$translated_text = __( 'Your order could not be placed. Checkout functionality is currently enabled for preview purposes only.', 'wc-calypso-bridge' );
						break;
				}
			}

			return $translated_text;

		}, PHP_INT_MAX, 3 );

		// Change the "No available payment methods" message.
		add_action( 'wp_head', function () {
			?>
			<script type="text/javascript">
				function overrideNoPaymentMethodsMessage( translation, text, domain ) {
					if ( text === '<?php /* phpcs:ignore WordPress.WP.I18n.TextDomainMismatch */ esc_html_e( 'There are no payment methods available. This may be an error on our side. Please contact us if you need any help placing your order.', 'woocommerce' ); ?>' ) {
						return '<?php esc_html_e( 'This store is not ready to accept orders. Checkout functionality is currently enabled for preview purposes only.', 'wc-calypso-bridge' ); ?>';
					}

					return translation;
				}

				window.wp && window.wp.hooks && window.wp.hooks.addFilter(
					'i18n.gettext_woocommerce',
					'wc-calypso-bridge/override-no-payment-methods-message',
					overrideNoPaymentMethodsMessage
				);
			</script>
			<?php
		} );

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
			if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
				return $content;
			}

			$message = esc_html__( 'This store is not ready to accept orders. Checkout functionality is currently enabled for preview purposes only.', 'wc-calypso-bridge' );
			ob_start();
			wc_print_notice( $message, 'notice' );
			$notice = ob_get_clean();

			return $notice . $content;
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

			$plan_url  = sprintf( 'https://wordpress.com/plans/%s',  WC_Calypso_Bridge_Instance()->get_site_slug() );
			/* translators: %s is the plans URL */
			$message   = sprintf( __( 'Only Administrators and Store Managers can place orders during the free trial. If you are ready to accept payments from customers, <a href="%s">upgrade to a paid plan</a>.', 'wc-calypso-bridge' ), $plan_url );

			?>
			<div class="notice notice-info">
				<p><?php echo $message; ?></p>
			</div>
			<?php
		}, PHP_INT_MAX );

	}
}

WC_Calypso_Bridge_Free_Trial_Payment_Restrictions::get_instance();
