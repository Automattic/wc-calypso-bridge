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

		add_filter( 'pre_option_wc_admin_show_legacy_coupon_menu', array( $this, 'filter_show_legacy_coupon_menu' ), PHP_INT_MAX );
		add_action( 'admin_init', array( $this, 'delete_coupon_moved_notes' ), PHP_INT_MAX );
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

	/**
	 * Remove the legacy `WooCommerce > Coupons` menu.
	 *
	 * @param mixed $value Value to be filtered.
	 *
	 * @return int 1 to show the legacy menu, 0 to hide it. Booleans do not work.
	 * @see  Automattic\WooCommerce\Internal\Admin\CouponsMovedTrait::display_legacy_menu()
	 * @todo Write a compatibility branch in CouponsMovedTrait to hide the legacy menu in new installations of WooCommerce.
	 */
	public function filter_show_legacy_coupon_menu( $value ) {
		return 0;
	}

	/**
	 * Delete all existing `Coupon Page Moved` notes from the DB.
	 *
	 * @return void
	 * @todo Create a one-time operation controller, to delete all `wc-admin-coupon-page-moved` notes from the database.
	 */
	public function delete_coupon_moved_notes() {

		if ( class_exists( 'Automattic\WooCommerce\Admin\Notes\Notes' ) ) {
			$notes_class = 'Automattic\WooCommerce\Admin\Notes\Notes';
		} elseif ( class_exists( 'Automattic\WooCommerce\Admin\Notes\WC_Admin_Notes' ) ) {
			$notes_class = 'Automattic\WooCommerce\Admin\Notes\WC_Admin_Notes';
		} else {
			return;
		}

		$notes_class::delete_notes_with_name( 'wc-admin-coupon-page-moved' );
	}
}

WC_Calypso_Bridge_WooCommerce_Admin::factory()->init();
