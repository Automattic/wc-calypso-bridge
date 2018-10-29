<?php
/**
 * Setup Checklist
 *
 * Adds a new WC setup page with a checklist of steps for setting up your store.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Calypso_Bridge_Admin_Setup_Checklist class.
 */
class WC_Calypso_Bridge_Admin_Setup_Checklist {

	protected static $instance = false;

	/**
	 * Provide only a single instance of this class.
	 */
	public static function getInstance() {
		if ( !self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Hooks into WordPress to add our new setup checklist.
	 */
	private function __construct() {

		// If setup has been completed, do nothing.
		if ( true === (bool) get_option( 'atomic-ecommerce-setup-checklist-complete', false ) ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		// priority is 20 to run after https://github.com/woocommerce/woocommerce/blob/a55ae325306fc2179149ba9b97e66f32f84fdd9c/includes/admin/class-wc-admin-menus.php#L165.
		add_action( 'admin_head', array( $this, 'admin_menu_structure' ), 20 );

		// run early and late
		if ( isset( $_GET['page'] ) && 'wc-setup-checklist' === $_GET['page'] ) {
			add_action( 'admin_notices', array( $this, 'hide_all_notices_on_setup_page' ), 0 );
			add_action( 'admin_notices', array( $this, 'after_hide_notices_on_setup_page'), PHP_INT_MAX );
		}
	}

	/**
	 * Adds a new page for the setup checklist.
	 */
	public function admin_menu() {
		if ( 1 != (int) get_user_meta( get_current_user_id(), 'calypsoify', true ) ) {
			return;
		}

		add_submenu_page(
			'woocommerce',
			__( 'Setup', 'wc-calypso-bridge' ),
			__( 'Setup', 'wc-calypso-bridge' ),
			'manage_woocommerce',
			'wc-setup-checklist',
			array( $this, 'checklist' )
		);
	}

	/**
	 * Puts the 'Setup' menu item at the very top of the WooCommerce link.
	 * We have to do some shuffling, because WooCommerce does some overwriting with the 'Orders' link.
	 */
	public function admin_menu_structure() {
		global $submenu;

		// User does not have capabilites to see the submenu.
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		if ( 1 != (int) get_user_meta( get_current_user_id(), 'calypsoify', true ) ) {
			return;
		}
		
		$setup_key = null;
		foreach ( $submenu['woocommerce'] as $submenu_key => $submenu_item ) {
			if ( 'wc-setup-checklist' === $submenu_item[2] ) {
				$setup_key = $submenu_key;
				break;
			}
		}

		if ( ! $setup_key ) {
			return;
		}
	
		$menu = $submenu['woocommerce'][ $setup_key ];

		// Move menu item to top of array.
		unset( $submenu['woocommerce'][ $setup_key ] );
		array_unshift( $submenu['woocommerce'], $menu );
	}

	/**
	 * Runs before admin notices action and hides them all.
	 */
	function hide_all_notices_on_setup_page() {
		echo '<div class="woocommerce__notice-list-hide">';
		echo '<div class="wp-header-end"></div>'; // https://github.com/WordPress/WordPress/blob/f6a37e7d39e2534d05b9e542045174498edfe536/wp-admin/js/common.js#L737.
	}

	/**
	 * Runs after admin notices and closes hidden div.
	 */
	function after_hide_notices_on_setup_page() {
		echo '</div>';
	}



	public function checklist() {

	}

}

$WC_Calypso_Bridge_Admin_Setup_Checklist = WC_Calypso_Bridge_Admin_Setup_Checklist::getInstance();
