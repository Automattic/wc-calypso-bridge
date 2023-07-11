<?php
/**
 * Adds additional meta to products for the Store on .com experience.
 * See also wc-calypso-bridge-jetpack-sync.php
 *
 * @since 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wc_calypso_bridge_products_get_context() {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
    	$referrer        = isset( $_SERVER[ 'HTTP_REFERER' ] ) ? sanitize_text_field( wp_unslash( $_SERVER[ 'HTTP_REFERER' ] ) ) : '';
		$known_referrers = array(
			'page=product_importer' => 'ajax-product-importer',
			'/post-new.php'         => 'ajax-post-new',
		);

		foreach ( $known_referrers as $known_referrer => $context ) {
			if ( false !== strpos( $referrer, $known_referrer ) ) {
				return $context;
			}
		}

		return 'ajax-unknown';
	}

    $request_uri = isset( $_SERVER[ 'REQUEST_URI' ] ) ? sanitize_text_field( wp_unslash( $_SERVER[ 'REQUEST_URI' ] ) ) : '';
	$known_fragments = array(
		'&_via_calypso'           => 'calypso',
		'/wp-json/wc/v2'          => 'rest-api-v2',
		'/?rest_route=%2Fwc%2Fv2' => 'rest-api-v2-rest-route',
		'/wp-json/wc/v3'          => 'rest-api-v3',
		'/?rest_route=%2Fwc%2Fv3' => 'rest-api-v3-rest-route',
		'/post-new.php'           => 'post-new',
	);

	foreach ( $known_fragments as $known_fragment => $context ) {
		if ( false !== strpos( $request_uri, $known_fragment ) ) {
			return $context;
		}
	}

	return 'unknown';
}

function wc_calypso_bridge_products_wp_insert_post( $post_ID, $post, $update ) {
	if ( $update ) {
		return;
	}

	$product_post_types = array( 'product', 'product_variation' );
	if ( ! in_array( $post->post_type, $product_post_types ) ) {
		return;
	}

	$created_via = wc_calypso_bridge_products_get_context();
	update_post_meta( $post_ID, '_created_via', $created_via );
}

add_action( 'wp_insert_post', 'wc_calypso_bridge_products_wp_insert_post', 10, 3 );
