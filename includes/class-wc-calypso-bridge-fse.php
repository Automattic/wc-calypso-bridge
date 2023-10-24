<?php
/**
 * Site Editing tweaks and improvements.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   2.2.15
 * @version 2.2.15
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge FSE class
 */
class WC_Calypso_Bridge_FSE {

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Bridge_FSE instance
	 */
	protected static $instance = false;

	/**
	 * Get class instance.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {

		// Only in Ecommerce.
		if ( ! wc_calypso_bridge_has_ecommerce_features() ) {
			return;
		}

		if ( is_admin() ) {

			// Change "Blog Home" template name in the Site Editor to something more suitable for Woo Express.
			add_filter(
				'default_template_types',
				function( $arg ) {

					if ( isset( $arg[ 'home' ][ 'title' ] ) && _x( 'Blog Home', 'Template name' ) === $arg[ 'home' ][ 'title' ] ) {
						$arg[ 'home' ][ 'title' ] = __( 'Home' );
					}

					return $arg;
				}
			);
		}
	}
}

WC_Calypso_Bridge_FSE::get_instance();
