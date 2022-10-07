<?php
/**
 * Jetpack customizations.
 *
 * @package WC_Calypso_Bridge/Jetpack
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
		 * @since x.x.x
		 *
		 * @param  string  $menu_controller_class  The name of the menu controller class.
		 * @return string
		 */
		add_filter( 'jetpack_admin_menu_class', function( $menu_controller_class ) {
			if ( class_exists( '\Automattic\Jetpack\Dashboard_Customizations\Atomic_Admin_Menu' ) ) {
				require_once dirname( __FILE__ ) . '/class-wc-calypso-bridge-admin-menu.php';
				return Ecommerce_Atomic_Admin_Menu::class;
			}

			return $menu_controller_class;
		} );

		/**
		 * Cleans up the Jetpack Dashboard -- Ecommerce Plan users only need to see the Jetpack settings.
		 *
		 * @since x.x.x
		 *
		 * @return void
		 */
		add_action( 'admin_print_styles', function() {

			$css = '.jp-masthead__nav { display: none !important; }';
			wp_add_inline_style( 'jetpack-admin', $css );
		} );

		/**
		 * Limits Jetpack Modules to those relevant to Ecommerce Plan users.
		 *
		 * @since x.x.x
		 *
		 * @param  array  $mods  Available Jetpack modules for activation.
		 * @return array
		 */
		add_filter( 'jetpack_get_available_modules', function( $mods ) {
			if ( WC_Calypso_Bridge_Helper_Functions::is_wc_admin_installed_gte( WC_Calypso_Bridge::S2_2022_RELEASE_DATE ) ) {
				$mods = array_diff_key( $mods, array_flip( array( 'gravatar-hovercards', 'custom-content-types', 'wordads', 'google-analytics', 'widget-visibility', 'post-list', 'infinite-scroll', 'copy-post', 'lazy-images', 'post-by-email', 'related-posts', 'action-bar' ) ) );
			}
			return $mods;
		} );
	}
}

WC_Calypso_Bridge_Jetpack::get_instance();
