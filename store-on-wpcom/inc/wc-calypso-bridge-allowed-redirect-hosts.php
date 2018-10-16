<?php
/**
 * Adds WordPress.com, and Calypso localhost to safe redirect whitelist
 *
 * @since 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wc_calypso_bridge_add_redirect_hosts( $content ) {
	$content[] = 'wordpress.com';
	$content[] = 'calypso.localhost';
	return $content;
}

if ( ! function_exists( 'wc_api_dev_add_redirect_hosts' ) ) {
	add_filter( 'allowed_redirect_hosts', 'wc_calypso_bridge_add_redirect_hosts' );
}
