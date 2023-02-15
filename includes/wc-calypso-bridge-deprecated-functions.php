<?php

/**
 * Deprecated functions for backwards compatibility.
 */

/**
 * Returns the current screen ID.
 *
 * @return string Current screen ID.
 */
function wc_calypso_bridge_get_current_screen_id() {
	_deprecated_function( 'wc_calypso_bridge_get_current_screen_id', '2.0.0' );

	$current_screen = get_current_screen();

	if ( ! $current_screen ) {
		return '';
	}

	return $current_screen->id;
}

/**
 * Connects a wp-admin page to a Calypso WooCommerce page.
 *
 * @param array $options {
 *   Array describing the page.
 *
 *   @type string      menu         wp-admin menu id/path.
 *   @type string      submenu      wp-admin submenu id/path.
 *   @type string      screen_id    WooCommerce screen ID (`wc_calypso_bridge_get_current_screen_id()`). Used for correctly identifying which pages are WooCommerce pages.
 * }
 */
function wc_calypso_bridge_connect_page( $options ) {
	_deprecated_function( 'wc_calypso_bridge_connect_page', '2.0.0' );
	return;
}

/**
 * Returns if we are on a WooCommerce related admin page.
 *
 * @return bool True if this is a WooCommerce admin page. False otherwise.
 */
function is_wc_calypso_bridge_page() {
	_deprecated_function( 'is_wc_calypso_bridge_page', '2.0.0' );
	return false;
}

/**
 * Returns an array of wp-admin menu slugs that are registered as woocommerce menu items.
 *
 * @return array
 */
function wc_calypso_bridge_menu_slugs() {
	_deprecated_function( 'wc_calypso_bridge_menu_slugs', '2.0.0' );
	return array();
}
