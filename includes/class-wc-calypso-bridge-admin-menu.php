<?php

/**
 * Class Ecommerce_Atomic_Admin_Menu.
 *
 * @since x.x.x
 *
 * The admin menu controller for Ecommerce WoA sites.
 */
class Ecommerce_Atomic_Admin_Menu extends \Automattic\Jetpack\Dashboard_Customizations\Atomic_Admin_Menu {

	public function __construct() {
		parent::__construct();

		add_action( 'admin_menu', array( $this, 'hide_woocommerce_menu_items' ), 99999 );
		add_action( 'admin_menu', function() {

			if ( ! class_exists( '\Automattic\WooCommerce\Internal\Admin\Homescreen' ) ) {
				return;
			}

			/**
			 * Add the home page behind the scenes to avoid 404 errors.
			 *
			 * This change will change the screen id from `woocommerce_page_wc-admin` to `admin_page_wc-admin`.
			 *
			 * @see self::get_screen_id_replacement()
			 */
			wc_admin_register_page(
				array(
					'id'         => 'woocommerce-home',
					'title'      => __( 'Home', 'woocommerce' ),
					'parent'     => '',
					'path'       => \Automattic\WooCommerce\Internal\Admin\Homescreen::MENU_SLUG,
				)
			);
		} );

		/**
		 * Fix/Replace broken screen IDs for WooCommerce core pages.
		 */
		add_action( 'current_screen', function() {

			$current_screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
			if ( ! is_a( $current_screen, 'WP_Screen' ) ) {
				return;
			}

			$replacement = $this->get_screen_id_replacement( $current_screen->id );
			if ( false !== $replacement ) {
				$current_screen->id = $replacement;
				$current_screen->base = $replacement;
			}

		}, -99999 );
	}

	/**
	 * Hide default WC menu items.
	 */
	public function hide_woocommerce_menu_items() {
		global $submenu;

		// Remove the WooCommerce > Home menu item.
		array_shift( $submenu['woocommerce'] );
	}

	/**
	 * Adds My Home menu.
	 */
	public function add_my_home_menu() {
		$this->update_menu( 'index.php', '/admin.php?page=wc-admin', __( 'My Home', 'jetpack' ), 'edit_posts', 'dashicons-admin-home' );
	}

	/**
	 * Remove Stats menu.
	 */
	public function add_stats_menu() {
		if ( Jetpack::is_module_active( 'stats' ) ) {
			parent::add_stats_menu();
		}
	}

	/**
	 * Adds Inbox menu.
	 */
	public function add_inbox_menu() {
		add_menu_page( __( 'Emails', 'jetpack' ), __( 'Emails', 'jetpack' ), 'manage_options', 'https://wordpress.com/inbox/' . $this->domain, null, 'dashicons-email', '4.64424' );
	}

	/**
	 * Remove the Jetpack menu
	 */
	public function add_jetpack_menu() {

		global $submenu;

		parent::add_jetpack_menu();

		$submenu[ 'jetpack' ][ 0 ][ 2 ] = admin_url( 'admin.php?page=jetpack#/settings' );

		// Remove Jetpack Search menu item. Already exposed in the Jetpack Dashboard.
		$this->hide_submenu_page( 'jetpack', 'jetpack-search' );

		// Move Akismet under Settings
		$this->hide_submenu_page( 'jetpack', 'akismet-key-config' );
		add_submenu_page( 'options-general.php', __( 'Anti-Spam', 'wc-calypso-bridge' ), __( 'Anti-Spam' , 'wc-calypso-bridge' ), 'manage_options', 'akismet-key-config', array( 'Akismet_Admin', 'display_page' ), 12 );

		// Add Backup and Activity under Tools.

		// $this->hide_submenu_page( 'jetpack', 'https://wordpress.com/activity-log/' . $this->domain );
		// add_submenu_page( 'tools.php', esc_attr__( 'Activity', 'jetpack' ), __( 'Activity', 'jetpack' ), 'manage_options', 'https://wordpress.com/activity-log/' . $this->domain, null, 0 );
		// $this->hide_submenu_page( 'jetpack', 'https://wordpress.com/backup/' . $this->domain );
		// add_submenu_page( 'tools.php', esc_attr__( 'Backup', 'jetpack' ), __( 'Backup', 'jetpack' ), 'manage_options', 'https://wordpress.com/backup/' . $this->domain, null, 1 );


		// Move Jetpack Status screen from Settings to Tools.
		remove_submenu_page( 'options-general.php', 'https://wordpress.com/settings/jetpack/' . $this->domain );
		add_submenu_page( 'tools.php', esc_attr__( 'Jetpack Status', 'wc-calypso-bridge' ), __( 'Jetpack Status', 'wc-calypso-bridge' ), 'manage_options', 'https://wordpress.com/settings/jetpack/' . $this->domain, null, 100 );

		// Remove Marketing and Earn from Tools.
		$this->hide_submenu_page( 'tools.php', 'https://wordpress.com/marketing/tools/' . $this->domain );
		$this->hide_submenu_page( 'tools.php', 'https://wordpress.com/earn/' . $this->domain );
	}

	/**
	 * Backwards compatible screen IDs mapper for the new Ecommerce menu structure.
	 *
	 * Changing menu item positions, by definition, changes the internal WP_Screen ID of each page.
	 * This method is responsible for replacing newly introduced screen ids with legacy ones to be compatible with 3PD codebases.
	 * (e.g., A 3PD code injects some custom CSS into the `woocommerce_admin_wc-admin` screen)
	 *
	 * @param  $screen_id (Optional)  The screen ID
	 * @return array|string|false     Returns an array with all the replacements if no argument passed, or the string replacement screen ID.
	 */
	private function get_screen_id_replacement( $screen_id = null ) {

		/**
		 * This array maps new screen IDs with legacy ones.
		 *
		 * The format is: `new_screen_id` => `legacy_screen_id`
		 */
		$screen_map = array(
			'admin_page_wc-admin' => 'woocommerce_page_wc-admin',
		);

		if ( is_null( $screen_id ) ) {
			return $screen_map;
		}

		return array_key_exists( $screen_id, $screen_map ) ? $screen_map[ $screen_id ] : false;
	}
}
