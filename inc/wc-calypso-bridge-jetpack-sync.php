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
	);
	return array_merge( $list, $additional_meta );
}

add_filter( 'jetpack_sync_post_meta_whitelist', 'wc_calypso_bridge_add_post_meta_whitelist', 10 );

// There is no Jetpack sync filter for the comment meta whitelist like there is for postmeta
// We can still hook into the return value of the option where the whitelist is stored.
// @TODO We can update this when https://github.com/Automattic/jetpack/issues/8170 is resolved.
function wc_calypso_bridge_add_comment_meta_whitelist( $list ) {
	if ( false === $list ) {
		return false;
	}

	$additional_meta = array(
		'rating',
	);
	return array_merge( $list, $additional_meta );
}

add_filter( 'option_jetpack_sync_settings_comment_meta_whitelist', 'wc_calypso_bridge_add_comment_meta_whitelist', 10 );
