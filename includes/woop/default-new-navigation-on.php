<?php
/**
 * Hide the extensions marketplace
 *
 * @since 1.8.1
 * @package WC_Calypso_Bridge
 */

defined( 'ABSPATH' ) || exit;

function wc_calypso_bridge_enable_new_nav() {
	return 'yes';
}
add_filter( 'default_option_woocommerce_navigation_enabled', 'wc_calypso_bridge_enable_new_nav' );
