<?php

	use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks\Shipping as ShippingTask;

	/**
	 * Set free shipping in the same country as the store default
	 * Flag rate in all other countries when any of the following conditions are ture
	 *
	 * - The store sells physical products, has JP and WCS installed and connected, and is located in the US.
	 * - The store sells physical products, and is not located in US/Canada/Australia/UK (irrelevant if JP is installed or not).
	 * - The store sells physical products and is located in US, but JP and WCS are not installed.
	 *
	 * @param array $settings shared admin settings.
	 * @return array
	 */

	 function wc_calypso_bridge_maybe_set_default_shipping_options_on_home( $settings ) {
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
		if ( $already_created === 'yes' ) {
			return $settings;
		}

		$zone_count = count( \WC_Data_Store::load( 'shipping-zone' )->get_zones() );
		if ( $zone_count ) {
			update_option( 'woocommerce_admin_created_default_shipping_zones', 'yes' );
			update_option( 'woocommerce_admin_reviewed_default_shipping_zones', 'yes' );
			return $settings;
		}

		$user_skipped_obw           = $settings['onboarding']['profile']['skipped'] ?? false;
		$store_address              = $settings['preloadSettings']['general']['woocommerce_store_address'] ?? '';
		$product_types              = $settings['onboarding']['profile']['product_types'] ?? array();
		$user_has_set_store_country = $settings['onboarding']['profile']['is_store_country_set'] ?? false;


		// Do not proceed if user has not filled out their country in the onboarding profiler.
		if ( ! $user_has_set_store_country ) {
			return $settings;
		}


		// If user skipped the obw or has not completed the store_details
		// then we assume the user is going to sell physical products.
		if ( $user_skipped_obw || '' === $store_address ) {
			$product_types[] = 'physical';
		}

		if ( false === in_array( 'physical', $product_types, true ) ) {
			return $settings;
		}

		$country_code = wc_format_country_state_string( $settings['preloadSettings']['general']['woocommerce_default_country'] )['country'];
		$country_name = WC()->countries->get_countries()[ $country_code ] ?? null;

		$zone = new \WC_Shipping_Zone();
		$zone->set_zone_name( $country_name );
		$zone->add_location( $country_code, 'country' );
		$zone->add_shipping_method( 'free_shipping' );
		update_option( 'woocommerce_admin_created_default_shipping_zones', 'yes' );
		ShippingTask::delete_zone_count_transient();


		return $settings;
	}

	add_action( 'woocommerce_admin_shared_settings', 'wc_calypso_bridge_maybe_set_default_shipping_options_on_home', 99, 3 );
