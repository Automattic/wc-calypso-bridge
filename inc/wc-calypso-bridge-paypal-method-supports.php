<?php
/**
 * Properly set if PayPal is configured to support refunds
 * TODO: Remove this when https://github.com/woocommerce/woocommerce/pull/19380/files lands in Woo Core
 * @since 0.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


function wc_calypso_bridge_adjust_paypal_method_supports( $response, $gateway, $request ) {
	if ( is_wp_error( $response ) ) {
		return $response;
	}

	if ( 'paypal' === $gateway->id ) {
        $api_username = $gateway->get_option( 'api_username' );
        $api_password = $gateway->get_option( 'api_password' );
        $api_signature = $gateway->get_option( 'api_signature' );

        // If api username, password or signature is not set, i.e. empty string, we can't support woo-based refunds
        if ( empty( $api_username ) || empty( $api_password ) || empty( $api_signature ) ) {
            $supported_methods = array_diff( $gateway->supports, [ 'refunds' ] );
            $response->data[ 'method_supports' ] = $supported_methods;
        }
	}
	return $response;
}

add_filter( 'woocommerce_rest_prepare_payment_gateway', 'wc_calypso_bridge_adjust_paypal_method_supports', 10, 3 );
