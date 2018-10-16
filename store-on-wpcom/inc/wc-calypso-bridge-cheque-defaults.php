<?php
/**
 * Prevents the cheque/check payment method from being auto enabled if settings haven't been configured.
 * It also sets the description/instructions defaults.
 *
 * @since 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wc_calypso_bridge_cheque_defaults( $settings ) {
	$settings['enabled']['default'] = 'no';
	$settings['description']['default'] = __( 'Pay for this order by check.', 'wc-calypso-bridge' );
	$settings['instructions']['default'] = __( 'Make your check payable to...', 'wc-calypso-bridge' );
	return $settings;
}

if ( ! function_exists( 'wc_api_dev_cheque_defaults' ) ) {
	add_filter( 'woocommerce_settings_api_form_fields_cheque', 'wc_calypso_bridge_cheque_defaults' );
}
