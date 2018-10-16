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

function wc_calypso_bridge_validate_bacs_accounts( $accounts ) {
	$valid_keys = array(
		'account_name',
		'account_number',
		'bank_name',
		'bic',
		'iban',
		'sort_code',
	);

	foreach ( $accounts as $account ) {
		if ( ! is_array( $account ) ) {
			return false;
		}

		$account_keys = array_keys( $account );
		sort( $account_keys );
		if ( $account_keys !== $valid_keys ) {
			return false;
		}
	}
	return true;
}

function wc_calypso_bridge_format_bacs_accounts( $accounts ) {
	$formatted_accounts = array();

	foreach ( $accounts as $account ) {
		$formatted_accounts[] = array(
			'account_name'    => (string) $account[ 'account_name' ],
			'account_number'  => (string) $account[ 'account_number' ],
			'bank_name'       => (string) $account[ 'bank_name' ],
			'bic'             => (string) $account[ 'bic' ],
			'iban'            => (string) $account[ 'iban' ],
			'sort_code'       => (string) $account[ 'sort_code' ],
		);
	}
	return $formatted_accounts;
}

function wc_calypso_bridge_update_bacs_accounts( $response, $handler, $request ) {
	if ( is_wp_error( $response ) ) {
		return $response;
	}

	if (
		isset( $handler[ 'callback' ] ) &&
		is_callable( $handler[ 'callback' ], false, $callable_name )
	) {
		switch ( $callable_name ) {
			case 'WC_REST_Dev_Payment_Gateways_Controller::update_item':
			case 'WC_REST_Payment_Gateways_Controller::update_item':
				$controller = new WC_REST_Payment_Gateways_Controller;
				$gateway = $controller->get_gateway( $request );

				if (
					$gateway &&
					'bacs' === $gateway->id &&
					isset( $request[ 'settings' ] ) &&
					isset( $request[ 'settings' ][ 'accounts' ] )
				) {
					$new_accounts = (array) $request[ 'settings' ][ 'accounts' ];

					if ( ! wc_calypso_bridge_validate_bacs_accounts( $new_accounts ) ) {
						return new WP_Error( 'rest_setting_value_invalid', __( 'An invalid setting value was passed.', 'woocommerce' ), array( 'status' => 400 ) );
					}
					$formatted_accounts = wc_calypso_bridge_format_bacs_accounts( $new_accounts );
					update_option( 'woocommerce_bacs_accounts', $formatted_accounts );
				}
				break;
		}
	}

	return $response;
}

add_filter( 'rest_request_before_callbacks', 'wc_calypso_bridge_update_bacs_accounts', 10, 3 );
