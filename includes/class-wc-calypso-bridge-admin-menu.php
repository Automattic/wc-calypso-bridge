<?php

/**
 * Class Ecommerce_Atomic_Admin_Menu.
 *
 * @since x.x.x
 * This class comes up in the wp-admin of a WoA site.
 */
class Ecommerce_Atomic_Admin_Menu extends \Automattic\Jetpack\Dashboard_Customizations\Atomic_Admin_Menu {

	public function __construct() {
		parent::__construct();

		add_action( 'admin_menu', array( $this, 'hide_woocommerce_menu_items' ), 99999 );
		add_action( 'admin_menu', function() {

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
		}, 9 );

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
	 * Create the desired menu output.
	 */
	public function reregister_menu_items() {
		parent::reregister_menu_items();

	}

	/**
	 * Hide default WC menu items.
	 */
	public function hide_woocommerce_menu_items() {
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

	/**
	 * Backwards compatible screen IDs mapper for the new Ecommerce menu structure.
	 *
	 * @param  $screen_id (Optional)  The screen ID
	 * @return array|string|false    Returns an array with all the replacements if no argument passed, or the string replacement screen ID.
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

		if ( $screen_id ) {
			return array_key_exists( $screen_id, $screen_map ) ? $screen_map[ $screen_id ] : '';
		} else {
			return $screen_map;
		}
	}
}
