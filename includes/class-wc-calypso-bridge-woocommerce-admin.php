<?php
/**
 * Contains customizations for WooCommerce Admin
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 1.9.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge WooCommerce Admin
 */
class WC_Calypso_Bridge_WooCommerce_Admin {
	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Bridge_WooCommerce_Admin instance
	 */
	protected static $instance = false;

	/**
	 * Get class instance.
	 */
	public static function factory() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize the class.
	 */
	public function init() {
		add_filter( 'wc_admin_get_feature_config', array( $this, 'maybe_remove_devdocs_menu_item' ) );

		/**
		 * Remove the legacy `WooCommerce > Coupons` menu.
		 *
		 * @since   1.9.4
		 *
		 * @param mixed $value Value to be filtered.
		 *
		 * @return int 1 to show the legacy menu, 0 to hide it. Booleans do not work.
		 * @see     Automattic\WooCommerce\Internal\Admin\CouponsMovedTrait::display_legacy_menu()
		 * @todo    Write a compatibility branch in CouponsMovedTrait to hide the legacy menu in new installations of WooCommerce.
		 * @todo    Remove this filter when the compatibility branch is merged.
		 */
		add_filter( 'pre_option_wc_admin_show_legacy_coupon_menu', function ( $value ) {
			return 0;
		}, PHP_INT_MAX );

	}

	/**
	 * Remove the Dev Docs menu item unless allowed by the `wc_calypso_bridge_development_mode` filter.
	 *
	 * @param array $features WooCommerce Admin enabled features list.
	 */
	public function maybe_remove_devdocs_menu_item( $features ) {
		if ( ! apply_filters( 'wc_calypso_bridge_development_mode', false ) ) {
			unset( $features['devdocs'] );
		}

		return $features;
	}
}

WC_Calypso_Bridge_WooCommerce_Admin::factory()->init();
