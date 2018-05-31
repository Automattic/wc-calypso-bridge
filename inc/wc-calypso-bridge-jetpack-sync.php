<?php
/**
 * Adds additional WooCommerce options to the sync whitelist.
 * See Jetpack's `class.jetpack-sync-module-woocommerce.php`.
 * Some fields are already synced back. This adds a few additional ones for the
 * Store on .com experience.
 *
 * @since 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wc_calypso_bridge_add_post_meta_whitelist( $list ) {
	$additional_meta = array(
		'_created_via',
	);
	return array_merge( $list, $additional_meta );
}

add_filter( 'jetpack_sync_post_meta_whitelist', 'wc_calypso_bridge_add_post_meta_whitelist' );

function wc_calypso_bridge_add_options_whitelist( $list ) {
	$additional_options = array(
		'woocommerce_currency_pos',
		'woocommerce_price_thousand_sep',
		'woocommerce_price_decimal_sep',
		'woocommerce_price_num_decimals',
	);
	return array_merge( $list, $additional_options );
}

add_filter( 'jetpack_sync_options_whitelist', 'wc_calypso_bridge_add_options_whitelist' );

function wc_calypso_bridge_add_comment_meta_whitelist( $list ) {
	$additional_meta = array(
		'rating',
	);
	return array_merge( $list, $additional_meta );
}

add_filter( 'jetpack_sync_comment_meta_whitelist', 'wc_calypso_bridge_add_comment_meta_whitelist' );
