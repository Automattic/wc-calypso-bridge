<?php
/**
 * Disables publicize on API-created Products
 *
 * @since 0.2.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'publicize_save_meta', 'wc_calypso_bridge_maybe_disable_publicize', 10, 4 );

/**
 * Possibly Update post meta to disable publicize on Products
 *
 * @since 0.2.3
 *
 * @param bool $submit_post Should the post be publicized.
 * @param int $post->ID Post ID.
 * @param string $service_name Service name.
 * @param array $connection Array of connection details.
 */
function wc_calypso_bridge_maybe_disable_publicize( $submit_post, $post_id, $service_name, $connection ) {
    $trigger_strings = array( '/wp-json/wc/v', '/?rest_route=%2Fwc%2Fv' );
    $is_rest_api_request = false;

    $request_uri = isset( $_SERVER[ 'REQUEST_URI' ] ) ? sanitize_text_field( wp_unslash( $_SERVER[ 'REQUEST_URI' ] ) ) : '';
    // Only run this logic on REST API requests
    foreach( $trigger_strings as $trigger_string ) {
        if ( false !== strpos( $request_uri, $trigger_string ) ) {
            $is_rest_api_request = true;
            break;
        }
    }
    
    $post_type = get_post_type( $post_id );

    // If not a product, or not a REST API request, return.
    if ( 'product' != $post_type || ! $is_rest_api_request ) {
        return;
    }

    // Since this is a product, and we are in an API request, disable publicize.
    if ( ! empty( $connection->unique_id ) ) {
        $unique_id = $connection->unique_id;
    } else if ( ! empty( $connection['connection_data']['token_id'] ) ) {
        $unique_id = $connection['connection_data']['token_id'];
    }

    update_post_meta( $post_id, '_wpas_skip_' . $unique_id, 1 );
}
