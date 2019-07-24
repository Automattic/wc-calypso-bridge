<?php
/**
 * Contains customizations for WooCommerce Admin
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.17
 * @version 1.0.0
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
	 * Get class instance
	 */
	public static function factory() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initialize the class
	 */
	public function init() {
		add_filter( 'wc_admin_get_feature_config', array( $this, 'maybe_remove_devdocs_menu_item' ) );
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
