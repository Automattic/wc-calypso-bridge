<?php

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Calypso_Bridge_Admin_Settings
 *
 * @since   x.x.x
 * @version x.x.x
 *
 * Handle Admin Settings.
 */
class WC_Calypso_Bridge_Admin_Settings {
	/**
	 * The single instance of the class.
	 *
	 * @var WC_Calypso_Bridge_Admin_Settings
	 */
	protected static $instance = null;

	/**
	 * Get class instance.
	 *
	 * @return object Instance.
	 */
	final public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'replace_settings_tab' ), PHP_INT_MAX );
	}

	/**
	 * Replace the settings tab.
	 *
	 * @param array $tabs The tabs.
	 * @return array The tabs.
	 */
	public function replace_settings_tab( $tabs ) {
		global $current_tab;

		if ( wc_calypso_bridge_is_ecommerce_trial_plan() ) {
			// Remove Site Visibility setting for the free trial plan.
			unset( $tabs['site-visibility'] );

			if ( isset( $current_tab ) && $current_tab === 'site-visibility' ) {
				wp_safe_redirect( admin_url( 'admin.php?page=wc-settings' ) );
				exit;
			}
		}

		return $tabs;
	}
}

WC_Calypso_Bridge_Admin_Settings::get_instance();
