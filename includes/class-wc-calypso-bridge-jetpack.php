<?php
/**
 * Jetpack customizations.
 *
 * @package WC_Calypso_Bridge/Jetpack
 * @since   1.9.8
 * @version 1.9.13
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
				require_once dirname( __FILE__ ) . '/class-wc-calypso-bridge-ecommerce-admin-menu.php';
				return Ecommerce_Atomic_Admin_Menu::class;
			}

			return $menu_controller_class;
		} );

		/**
		 * Limits Jetpack Modules to those relevant to Ecommerce Plan users.
		 *
		 * @since 1.9.8
		 *
		 * @param  array $mods Available Jetpack modules for activation.
		 * @return array
		 */
		add_filter( 'jetpack_get_available_modules', function ( $mods ) {

			// Removing Google Analytics module as we've activated WooCommerce Google Analytics Integration for all new sites.
			if ( WC_Calypso_Bridge_Helper_Functions::is_wc_admin_installed_gte( WC_Calypso_Bridge::RELEASE_DATE_PRE_CONFIGURE_JETPACK ) ) {
				$ga_options = get_option( 'jetpack_wga' );
				$ga_enabled = isset( $ga_options['code'] ) && ! empty( $ga_options['code'] );

				// Do not remove the module in case Jetpack GA was already enabled before the transfer to atomic.
				if ( ! $ga_enabled ) {
					$mods = array_diff_key( $mods, array_flip( array( 'google-analytics' ) ) );
				}
			}

			return $mods;
		} );

	}
}

WC_Calypso_Bridge_Jetpack::get_instance();
