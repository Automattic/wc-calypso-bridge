<?php

/**
 * WC Calypso Bridge Partner Square
 *
 * This file includes customizations for the sites that were created throguh /start/square on woo.com.
 * woocommerce_onboarding_profile.partner must ge 'square'
 */
class WC_Calypso_Bridge_Partner_Square {

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Bridge_Partner_Square instance
	 */
	protected static $instance = false;

	/**
	 * Get class instance
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
	public function __construct() {
		$onboarding_profile = get_option( 'woocommerce_onboarding_profile', array() );
		if ( ! isset( $onboarding_profile['partner'] ) ) {
			return;
		}

		if ( $onboarding_profile['partner'] !== 'square' ) {
			return;
		}

		$this->remove_woo_payments_from_payments_suggestions_feed();
		$this->remove_woo_payments_from_core_profiler_plugin_suggestions();
	}

	private function remove_woo_payments_from_payments_suggestions_feed() {
		add_filter( 'woocommerce_admin_payment_gateway_suggestion_specs', function( $specs ) {
			if ( isset( $specs['woocommerce_payments'] ) ) {
				unset( $specs['woocommerce_payments'] );
			}

			if ( isset( $specs['woocommerce_payments:with-in-person-payments'] ) ) {
				unset( $specs['woocommerce_payments:with-in-person-payments'] );
			}

			return $specs;
		});
	}

	private function remove_woo_payments_from_core_profiler_plugin_suggestions() {
		add_filter( 'rest_request_after_callbacks', function( $response, $handler, $request ) {
			if ( $request->get_route() === '/wc-admin/onboarding/free-extensions' ) {
				$data = $response->get_data();
				foreach ( $data as &$list ) {
					if ( $list['key'] === 'obw/core-profiler' ) {
						foreach ( $list['plugins'] as $index => $plugin ) {
							if ( $plugin->key === 'woocommerce-payments' ) {
								unset( $list['plugins'][$index] );
								break;
							}
						}
						break;
					}
				}
				$response->set_data( $data );
			}
			return $response;
		}, 10, 3);
	}
}

WC_Calypso_Bridge_Partner_Square::get_instance();
