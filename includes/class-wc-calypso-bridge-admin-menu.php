<?php

/**
 * Class Ecommerce_Atomic_Admin_Menu.
 *
 * This class comes up in the wp-admin of a WoA site.
 */
class Ecommerce_Atomic_Admin_Menu extends \Automattic\Jetpack\Dashboard_Customizations\Atomic_Admin_Menu {

	public function __construct() {
		parent::__construct();

		add_action( 'admin_menu', array( $this, 'update' ), 99999 );
		add_action( 'admin_menu', function() {

			wc_admin_register_page(
				array(
					'id'         => 'woocommerce-home',
					'title'      => __( 'Home', 'woocommerce' ),
					'parent'     => '',
					'path'       => \Automattic\WooCommerce\Internal\Admin\Homescreen::MENU_SLUG,
				)
			);
		}, 9 );
	}

	/**
	 * Create the desired menu output.
	 */
	public function reregister_menu_items() {
		parent::reregister_menu_items();

	}

	public function update() {
		global $submenu;

		$home = array_shift($submenu['woocommerce']);
		$this->hide_submenu_element( 'woocommerce-home', 'woocommerce', $home );
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
		// ...
	}
}
