<?php
/**
 * Smart shipping function.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   2.2.24
 * @version 2.2.24
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks\Shipping as ShippingTask;

/**
 * WC Calypso Bridge Smart shipping
 */
class WC_Calypso_Smart_Shipping {

	/**
	 * Class Instance.
	 *
	 * @var WC_Calypso_Smart_Shipping instance
	 */
	protected static $instance = null;

	/**
	 * Get class instance
	 */
	public static function get_instance() {
		if ( ! static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Only in Ecommerce or free trial.
		if ( ! ( wc_calypso_bridge_has_ecommerce_features() || wc_calypso_bridge_is_ecommerce_trial_plan() ) ) {
			return;
		}

		add_action( 'woocommerce_admin_shared_settings', array( $this, 'wc_calypso_bridge_maybe_set_default_shipping_options_on_home' ), 99, 3 );
	}

	/**
	 * Set free shipping in the same country as the store's current country.
	 * Flag rate in all other countries when any of the following conditions are true:
	 *
	 * - is_store_country_set is true in the onboarding profile.
	 * - No shipping zone is set.
	 * - Country is set.
	 * - No orders exists.
	 *
	 * @param array $settings shared admin settings.
	 * @return array
	 */
	public function wc_calypso_bridge_maybe_set_default_shipping_options_on_home( $settings ) {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return $settings;
		}

		$current_screen = get_current_screen();

		// Abort if it's not the homescreen.
		if ( ! isset( $current_screen->id ) || 'woocommerce_page_wc-admin' !== $current_screen->id ) {
			return $settings;
		}

		// Abort if we already created the shipping options.
		$already_created = get_option( 'woocommerce_admin_created_default_shipping_zones' );
		if ( 'yes' === $already_created ) {
			return $settings;
		}

		$zone_count = count( \WC_Data_Store::load( 'shipping-zone' )->get_zones() );
		if ( $zone_count ) {
			update_option( 'woocommerce_admin_created_default_shipping_zones', 'yes' );
			update_option( 'woocommerce_admin_reviewed_default_shipping_zones', 'yes' );
			return $settings;
		}

		$user_has_set_store_country = $settings['onboarding']['profile']['is_store_country_set'] ?? false;
		// Do not proceed if user has not filled out their country from NUX.
		if ( ! $user_has_set_store_country ) {
			return $settings;
		}

		$country_code = wc_format_country_state_string( $settings['preloadSettings']['general']['woocommerce_default_country'] )['country'];
		$country_name = WC()->countries->get_countries()[ $country_code ] ?? null;

		// Country is not defined.
		if ( ! $country_code || ! $country_name ) {
			return $settings;
		}

		$args         = array( 'limit'  => 1 );
		$orders_count = count( wc_get_orders( $args ) );

		// If there are orders, don't create the default shipping options.
		// This is to protect from mistakenly adding free shipping to an established store.
		if ( $orders_count > 0 ) {
			return $settings;
		}

		$zone = new \WC_Shipping_Zone();
		$zone->set_zone_name( $country_name );
		$zone->add_location( $country_code, 'country' );
		$zone->add_shipping_method( 'free_shipping' );
		update_option( 'woocommerce_admin_created_default_shipping_zones', 'yes' );
		ShippingTask::delete_zone_count_transient();

		return $settings;
	}

}

WC_Calypso_Smart_Shipping::get_instance();
