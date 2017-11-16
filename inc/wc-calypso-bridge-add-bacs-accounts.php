<?php
/**
 * Adds BACS Accounts to /wc/v3/payment_gateways response when applicable
 *
 * @since 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


function wc_calypso_bridge_add_bacs_accounts( $response, $gateway, $request ) {
	if ( is_wp_error( $response ) ) {
		return $response;
	}

	if ( 'bacs' === $gateway->id ) {
		$response->data[ 'settings' ][ 'accounts' ] = array(
			'id'    => 'accounts',
			'value' => get_option( 'woocommerce_bacs_accounts', array() ),
		);
	}
	return $response;
}

add_filter( 'woocommerce_rest_prepare_payment_gateway', 'wc_calypso_bridge_add_bacs_accounts', 10, 3 );
