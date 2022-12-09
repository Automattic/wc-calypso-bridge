<?php
/**
 * Jetpack customizations.
 *
 * @package WC_Calypso_Bridge/Jetpack
 * @since   1.9.8
 * @version 1.9.11
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Calypso_Bridge_Jetpack Class.
 */
class WC_Calypso_Bridge_Jetpack {
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
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize hooks.
	 */
	public function init() {

		/**
		 * Inject the Ecommerce admin menu controller into Jetpack.
		 *
		 * @since 1.9.8
		 *
		 * @param  string $menu_controller_class The name of the menu controller class.
		 * @return string
		 */
		add_filter( 'jetpack_admin_menu_class', function ( $menu_controller_class ) {
			if ( class_exists( '\Automattic\Jetpack\Dashboard_Customizations\Atomic_Admin_Menu' ) ) {
				require_once dirname( __FILE__ ) . '/class-wc-calypso-bridge-admin-menu.php';

				return Ecommerce_Atomic_Admin_Menu::class;
			}

			return $menu_controller_class;
		} );

	}
}

WC_Calypso_Bridge_Jetpack::get_instance();
