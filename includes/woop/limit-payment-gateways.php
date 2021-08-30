<?php

function wc_calypso_bridge_woop_limit_personal_payment_gateways( $payment_gateways ) {
	return array_filter(
		$payment_gateways,
		function ( $payment_gateway ) {
			$string_whitelist = [
				// 'WC_Gateway_BACS',
				// 'WC_Gateway_Cheque',
				'WC_Gateway_COD',
			// 'WC_Gateway_Paypal',
			];

			$class_whitelist = [
				'WC_Payment_Gateway_WCPay_Subscriptions_Compat',
			];

			if ( is_string( $payment_gateway ) ) {
				return in_array( $payment_gateway, $string_whitelist, true );
			}

			if ( is_object( $payment_gateway ) ) {
				return in_array( get_class( $payment_gateway ), $class_whitelist, true );
			}

			return false;
		}
	);
}

if ( wc_calypso_bridge_is_personal_plan() ) {
	add_filter( 'woocommerce_payment_gateways', 'wc_calypso_bridge_woop_limit_personal_payment_gateways' );
}
