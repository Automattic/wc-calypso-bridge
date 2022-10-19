<?php

/**
 * Class Ecommerce_Atomic_Admin_Menu.
 *
 * @since x.x.x
 *
 * The admin menu controller for Ecommerce WoA sites.
 */
class Ecommerce_Atomic_Admin_Menu extends \Automattic\Jetpack\Dashboard_Customizations\Atomic_Admin_Menu {

	/**
	 * Remove Stats menu.
	 */
	public function add_stats_menu() {
		// Silence!
	}

	/**
	 * Remove the Jetpack menu
	 */
	public function add_jetpack_menu() {

		global $submenu;

		parent::add_jetpack_menu();

		// Remove Jetpack Search menu item. Already exposed in the Jetpack Dashboard.
		$this->hide_submenu_page( 'jetpack', 'jetpack-search' );

		// Move Akismet under Settings
		$this->hide_submenu_page( 'jetpack', 'akismet-key-config' );
		add_submenu_page( 'options-general.php', __( 'Anti-Spam', 'wc-calypso-bridge' ), __( 'Anti-Spam' , 'wc-calypso-bridge' ), 'manage_options', 'akismet-key-config', array( 'Akismet_Admin', 'display_page' ), 12 );

		// Move Stats in here.
		add_submenu_page( 'jetpack', esc_attr__( 'Stats', 'wc-calypso-bridge' ), __( 'Stats', 'wc-calypso-bridge' ), 'manage_options', 'https://wordpress.com/stats/day/' . $this->domain, null, 20 );

		// Move Jetpack Status screen from Settings to Tools.
		remove_submenu_page( 'options-general.php', 'https://wordpress.com/settings/jetpack/' . $this->domain );
		add_submenu_page( 'tools.php', esc_attr__( 'Jetpack Status', 'wc-calypso-bridge' ), __( 'Jetpack Status', 'wc-calypso-bridge' ), 'manage_options', 'https://wordpress.com/settings/jetpack/' . $this->domain, null, 100 );

		// Remove Earn from Tools.
		$this->hide_submenu_page( 'tools.php', 'https://wordpress.com/earn/' . $this->domain );
	}
}
