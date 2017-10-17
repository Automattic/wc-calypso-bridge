<?php
/**
 * Runs DB updates automatically without wp-admin notices.
 *
 * @see https://github.com/woocommerce/woocommerce/issues/16703
 * @see https://github.com/woocommerce/woocommerce/pull/16711
 * @since 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'woocommerce_enable_auto_update_db', '__return_true' );
