<?php
/**
 * Adds a new WC setup page with a checklist of steps for setting up your store.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Calypso_Bridge_Admin_Setup_Checklist class.
 */
class WC_Calypso_Bridge_Admin_Setup_Checklist {

	/**
	 * Instance variable
	 *
	 * @var WC_Calypso_Bridge_Admin_Setup_Checklist instance
	 */
	protected static $instance = false;

	/**
	 * Provide only a single instance of this class.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
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
		add_action( 'admin_head', array( $this, 'remove_notices' ) );
		// priority is 20 to run after https://github.com/woocommerce/woocommerce/blob/a55ae325306fc2179149ba9b97e66f32f84fdd9c/includes/admin/class-wc-admin-menus.php#L165.
		add_action( 'admin_head', array( $this, 'admin_menu_structure' ), 20 );
	}

	/**
	 * Remove all admin notices
	 */
	public function remove_notices() {
		if ( isset( $_GET['page'] ) && 'wc-setup-checklist' === $_GET['page'] ) {
			remove_all_actions( 'admin_notices' );
		}
	}

	/**
	 * Adds a new page for the setup checklist.
	 */
	public function admin_menu() {
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
	 * Render the checklist
	 */
	public function checklist() {

	}

}

$wc_calypso_bridge_admin_setup_checklist = WC_Calypso_Bridge_Admin_Setup_Checklist::get_instance();
