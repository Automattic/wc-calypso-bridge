<?php
/**
 * Hide the extensions marketplace
 *
 * @since 1.8.1
 * @package WC_Calypso_Bridge
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'woocommerce_show_addons_page', '__return_false' );
