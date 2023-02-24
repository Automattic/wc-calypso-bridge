<?php
/**
 * Contains the logic for override Marketing screen
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   2.0.4
 * @version 2.0.4
 */

class WC_Calypso_Bridge_Free_Trial_Marketing_Changes {
	/**
	 * The single instance of the class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Get class instance.
	 *
	 * @return object Instance.
	 */
	final public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		if ( ! wc_calypso_bridge_is_ecommerce_trial_plan() ) {
			return;
		}

		add_action( 'admin_menu', [ $this, 'remove_marketing_submenus' ], 99 );
	}

	public function remove_marketing_submenus() {
	 	global $menu, $submenu;

		// remove submenus under the Marketing menu.
		if ( isset( $submenu[ 'woocommerce-marketing' ] ) ) {
			unset( $submenu[ 'woocommerce-marketing' ] );
		}

		// then add path for the Marketing menu.
		// previsouly, it was set on the overview submenu.
		foreach ( $menu as &$menuItem ) {
			if ( isset( $menuItem[5] ) && $menuItem[5] === 'toplevel_page_woocommerce-marketing' ) {
				$menuItem[2] = 'admin.php?page=wc-admin&path=/marketing';
				break;
			}
		}
	}
}

WC_Calypso_Bridge_Free_Trial_Marketing_Changes::get_instance();
