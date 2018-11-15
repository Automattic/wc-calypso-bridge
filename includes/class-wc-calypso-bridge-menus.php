<?php
/**
 * Removes WooCommerce plugins/extensions from the main plugin management interface and puts them under a new 'Store' item.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Calypso_Bridge_Menus
 */
class WC_Calypso_Bridge_Menus {

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Bridge_Menus instance
	 */
	protected static $instance = false;

	/**
	 * We want a single instance of this class so we can accurately track registered menus and pages.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		add_action( 'current_screen', array( $this, 'setup_menu_hooks' ) );
		add_action( 'admin_menu', array( $this, 'remove_create_new_menu_items' ), 100 );
	}

	// TODO If any extensions add new pages to wp-admin's settings section, we will want to copy those over,
	// just like Calypsoify does in `add_plugin_menus`.

	/**
	 * Hooks into WordPress to overtake the menu system on WooCommerce pages.
	 */
	public function setup_menu_hooks() {
		if ( is_wc_calypso_bridge_page() ) {
			remove_action( 'in_admin_header', array( Jetpack_Calypsoify::getInstance(), 'insert_sidebar_html' ) );

			add_action( 'admin_head', array( $this, 'woocommerce_menu_handler' ) );
		} else {
			add_action( 'admin_head', array( $this, 'calypsoify_menu_handler' ) );
		}
	}

	/**
	 * Updates the menu handling on WooCommerce pages to only show WooCommerce navigation.
	 */
	public function woocommerce_menu_handler() {
		global $menu, $submenu;

		$wc_menus = wc_calypso_bridge_menu_slugs();

		foreach ( $menu as $menu_key => $menu_item ) {
			if ( ! in_array( $menu_item[2], $wc_menus, true ) ) {
				unset( $menu[ $menu_key ] );
			}
		}
	}

	/**
	 * Updates the menu handling on Calypsoified pages to only show plugin navigation.
	 */
	public function calypsoify_menu_handler() {
		global $menu, $submenu;

		$wc_menus = wc_calypso_bridge_menu_slugs();

		foreach ( $menu as $menu_key => $menu_item ) {
			if ( in_array( $menu_item[2], $wc_menus, true ) ) {
				unset( $menu[ $menu_key ] );
			}
		}
	}

	/**
	 * Remove all create new pages for custom post types
	 */
	public function remove_create_new_menu_items() {
		$post_types = (array) get_post_types(
			array(
				'show_ui'      => true,
				'_builtin'     => false,
				'show_in_menu' => true,
			)
		);

		foreach ( $post_types as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );
			$singular_name    = strtolower( $post_type_object->labels->singular_name );
			remove_submenu_page( 'edit.php?post_type=' . $post_type, 'post-new.php?post_type=' . $singular_name );
			remove_submenu_page( 'edit.php?post_type=' . $post_type, 'create_' . $singular_name );
		}
	}
}

$wc_calypso_bridge_menus = WC_Calypso_Bridge_Menus::get_instance();
