<?php
/**
 * Limit payment gateways.
 *
 * @since 1.8.1
 * @package WC_Calypso_Bridge
 */

defined( 'ABSPATH' ) || exit;

/**
 * Limit payment gateways when on a plan lower than business.
 *
 * @param mixed $payment_gateways An array of either strings or subclasses of WC_Payment_Gateway.
 * @return mixed Filtered array of either strings or WC_Payment_Gateway's depending on what was passed in.
 */
function wc_calypso_bridge_limit_payment_gateways( $payment_gateways ) {
	if ( ! class_exists( 'Atomic_Plan_Manager' ) ||
		! method_exists( 'Atomic_Plan_Manager', 'current_plan_slug' ) ||
		Atomic_Plan_Manager::current_plan_slug() === Atomic_Plan_Manager::BUSINESS_PLAN_SLUG ||
		Atomic_Plan_Manager::current_plan_slug() === Atomic_Plan_Manager::ECOMMERCE_PLAN_SLUG
	) {
		return $payment_gateways;
	}

	// Core WooCommerce gateways sourced from woocommerce/includes/class-wc-payment-gateways.php::init.
	$allowed_strings = array(
		'WC_Gateway_BACS',
		'WC_Gateway_Cheque',
		'WC_Gateway_COD',
	);

	// WCPay gateways sourced from woocommerce-payments/includes/class-wc-payments.php::init.
	$allowed_classes = array(
		'WCPay\Payment_Methods\CC_Payment_Gateway',
		'WCPay\Payment_Methods\UPE_Payment_Gateway',
		'WCPay\Payment_Methods\Giropay_Payment_Gateway',
		'WCPay\Payment_Methods\Sepa_Payment_Gateway',
		'WCPay\Payment_Methods\Sofort_Payment_Gateway',
	);

	$filtered = array();
	foreach ( $payment_gateways as $gateway ) {
		if (
			( is_string( $gateway ) && in_array( $gateway, $allowed_strings, true ) ) ||
			( is_object( $gateway ) && in_array( get_class( $gateway ), $allowed_classes, true ) )
		) {
			$filtered[] = $gateway;
		}
	}
	return $filtered;
}
add_filter( 'woocommerce_payment_gateways', 'wc_calypso_bridge_limit_payment_gateways', PHP_INT_MAX );
