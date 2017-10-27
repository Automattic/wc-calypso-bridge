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
		'_billing_email',
		'_billing_first_name',
		'_billing_last_name',
		'_sku',
	);
	return array_merge( $list, $additional_meta );
}

add_filter( 'jetpack_sync_post_meta_whitelist', 'wc_calypso_bridge_add_post_meta_whitelist', 10 );
