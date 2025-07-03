<?php
/**
 * Jetpack customizations.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.9.8
 * @version 2.3.2
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

		// Only in Ecommerce.
		if ( ! wc_calypso_bridge_has_ecommerce_features() ) {
			return;
		}

		$this->init();
	}

	/**
	 * Initialize hooks.
	 */
	public function init() {
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

		/**
		 * Removes the "Notify me of new posts by email" checkbox from the product review form.
		 *
		 * Hint: Remove when https://github.com/Automattic/jetpack/issues/34859 is fixed.
		 *
		 * @since 2.3.2
		 *
		 * @param  string $html The checkbox HTML value.
		 * @return string
		 */
		add_filter( 'jetpack_comment_subscription_form', function( $html ) {
			global $product;
			if ( is_single() && ! is_null( $product ) && is_a( $product, 'WC_Product' ) ) {
				return '';
			}

			return $html;
		} );

	}
}

WC_Calypso_Bridge_Jetpack::get_instance();
