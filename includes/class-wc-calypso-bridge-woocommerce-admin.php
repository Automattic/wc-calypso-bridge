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
		add_action( 'admin_init', array( $this, 'redirect_store_details_onboarding' ) );
	}

	/**
	 * Handle the store location's onboarding redirect when user provided a full address.
	 *
	 * @since 1.9.4
	 * @return void
	 */
	public function redirect_store_details_onboarding() {

		// Only run on save.
		if ( empty( $_POST ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return;
		}

		if ( ! isset( $_GET['tutorial'], $_GET['page'] ) || 'true' !== $_GET['tutorial'] || 'wc-settings' !== $_GET['page'] ) {
			return;
		}

		$should_redirect_home = false;

		$store_address  = get_option( 'woocommerce_store_address' );
		$store_city     = get_option( 'woocommerce_store_city' );
		$store_postcode = get_option( 'woocommerce_store_postcode' );

		if ( ! empty( $store_address ) && ! empty( $store_city ) && ! empty( $store_postcode ) ) {
			$should_redirect = true;
		}

		if ( $should_redirect ) {
			wp_safe_redirect( admin_url( 'admin.php?page=wc-admin' ) );
		}
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
