<?php

/**
 * Class Ecommerce_Atomic_Admin_Menu.
 *
 * @since   1.9.8
 * @version 1.9.11
 *
 * The admin menu controller for Ecommerce WoA sites.
 */
class Ecommerce_Atomic_Admin_Menu extends \Automattic\Jetpack\Dashboard_Customizations\Atomic_Admin_Menu {

	/**
	 * Introduce 'Settings > Anti-Spam' and remove 'Settings > Jetpack' from Settings.
	 */
	public function add_options_menu() {
		parent::add_options_menu();
		// Introduce 'Settings > Anti-Spam'.
		add_submenu_page( 'options-general.php', __( 'Anti-Spam', 'wc-calypso-bridge' ), __( 'Anti-Spam', 'wc-calypso-bridge' ), 'manage_options', 'akismet-key-config', array( 'Akismet_Admin', 'display_page' ), 12 );
		// Remove 'Settings > Jetpack' from Settings.
		remove_submenu_page( 'options-general.php', 'https://wordpress.com/settings/jetpack/' . $this->domain );
	}

	/**
	 * Update the Jetpack menu.
	 */
	public function add_jetpack_menu() {

		global $submenu;

		parent::add_jetpack_menu();

		// Remove Jetpack Search menu item. Already exposed in the Jetpack Dashboard.
		$this->hide_submenu_page( 'jetpack', 'jetpack-search' );

		// Move Akismet under Settings
		$this->hide_submenu_page( 'jetpack', 'akismet-key-config' );

		// Move Jetpack status screen from 'Settings > Jetpack' to 'Tools > Jetpack Status'.
		add_submenu_page( 'tools.php', esc_attr__( 'Jetpack Status', 'wc-calypso-bridge' ), __( 'Jetpack Status', 'wc-calypso-bridge' ), 'manage_options', 'https://wordpress.com/settings/jetpack/' . $this->domain, null, 100 );
	}
}
