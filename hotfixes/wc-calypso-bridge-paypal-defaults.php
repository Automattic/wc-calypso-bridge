<?php
/**
 * Prevents the paypal payment method from being auto enabled if settings haven't been configured.
 *
 * @since 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wc_calypso_bridge_paypal_defaults( $settings ) {
	$settings['enabled']['default'] = 'no';
	return $settings;
}

if ( ! function_exists( 'wc_api_dev_paypal_defaults' ) ) {
	add_filter( 'woocommerce_settings_api_form_fields_paypal', 'wc_calypso_bridge_paypal_defaults' );
}
