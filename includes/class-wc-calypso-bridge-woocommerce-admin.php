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
		add_filter( 'pre_option_woocommerce_task_list_hidden', array( $this, 'disable_new_task_list' ) );
		add_filter( 'pre_option_woocommerce_onboarding_opt_in', array( $this, 'disable_onboarding_opt_in' ) );
		add_filter( 'pre_option_woocommerce_setup_ab_wc_admin_onboarding', array( $this, 'disable_onboarding_a_b_test' ) );
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
	 * Force the woocommerce_task_list_hidden option to always be yes so the new checklist is never shown
	 */
	public function disable_new_task_list() {
		return 'yes';
	}

	/**
	 * Force the woocommerce_onboarding_opt_in option to always be no so the new checklist is never shown
	 */
	public function disable_onboarding_opt_in() {
		return 'no';
	}

	/**
	 * Always disable the new onboarding opt-in.
	 */
	public function disable_onboarding_a_b_test() {
		return 'a';
	}
}

WC_Calypso_Bridge_WooCommerce_Admin::factory()->init();
