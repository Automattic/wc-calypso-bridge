<?php

/**
 * WC Calypso Bridge Partner Square
 *
 *	@since   2.3.5
 *	@version 2.3.10
 *
 * This file includes customizations for the sites that were created through /start/square on woo.com.
 * woocommerce_onboarding_profile.partner must get 'square'
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
		// Only for free trials.
		if ( ! wc_calypso_bridge_is_ecommerce_trial_plan() ) {
			return;
		}
		$onboarding_profile = get_option( 'woocommerce_onboarding_profile', array() );
		if ( ! isset( $onboarding_profile['partner'] ) ) {
			return;
		}

		if ( $onboarding_profile['partner'] !== 'square' ) {
			return;
		}

		$this->force_square_payment_methods_order();
		$this->add_square_setup_task();
		$this->add_square_connect_url_to_js();
		$this->remove_woo_payments_from_payments_suggestions_feed();
		$this->remove_payments_note();
	}

	/**
	 * Remove woo payments from the payments suggestions feed.
	 *
	 * @return void
	 */
	private function remove_woo_payments_from_payments_suggestions_feed() {
		add_filter( 'woocommerce_admin_payment_gateway_suggestion_specs', function( $specs ) {
			$keys = array(
				'woocommerce_payments',
				'woocommerce_payments:with-in-person-payments',
				'woocommerce_payments:without-in-person-payments',
			);
			foreach ( $keys as $key ) {
				if ( isset( $specs[ $key ] ) ) {
					unset( $specs[ $key ] );
				}
			}

			return $specs;
		});
	}

	private function has_square_plugin_class() {
		return class_exists( '\WooCommerce\Square\Plugin' );
	}

	/**
	 * Add Square setup task to the setup tasklist.
	 */
	private function add_square_setup_task() {
		add_filter( 'woocommerce_admin_experimental_onboarding_tasklists', function( $lists ) {
			if ( isset( $lists['setup'] ) ) {
				require_once __DIR__ . '/../../tasks/class-wc-calypso-task-get-paid-with-square.php';

				$removeTasks = [
					'Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks\TrialPayments',
					'Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks\WooCommercePayments'
				];

				$lists['setup']->tasks = array_filter( $lists['setup']->tasks,  function( $task ) use ($removeTasks) {
					if ( in_array( get_class( $task ), $removeTasks ) ) {
						return false;
					}

					return true;
				});

				// Place it at the third position.
				array_splice( $lists['setup']->tasks, 2, 0, array( new \Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks\WCBridgeGetPaidWithSquare( $lists['setup'] ) ) );
			}
			return $lists;
		} );
	}

	/**
	 * Gets the connection URL.
	 *
	 * Copied from WooCommerce Square plugin. This is used in case Square plugin class isn't available for some reason.
	 *
	 * @param bool $is_sandbox whether to point to production or sandbox
	 * @return string
	 */
	public function get_connect_url( $is_sandbox = false ) {
		if ( $is_sandbox ) {
			$raw_url = 'https://connect.woocommerce.com/login/squaresandbox';
		} else {
			$raw_url = 'https://connect.woocommerce.com/login/square';
		}

		/**
		 * Filters the connection URL.
		 *
		 * @since 2.0.0
		 *
		 * @param string $raw_url API URL
		 */
		$url = (string) apply_filters( 'wc_square_api_url', $raw_url );

		$action       = 'wc_square_connected';
		$redirect_url = wp_nonce_url( add_query_arg( 'action', $action, admin_url() ), $action );

		$args = array(
			'redirect' => urlencode( urlencode( $redirect_url ) ),
			'scopes'   => implode( ',', array(
				'MERCHANT_PROFILE_READ',
				'PAYMENTS_READ',
				'PAYMENTS_WRITE',
				'ORDERS_READ',
				'ORDERS_WRITE',
				'CUSTOMERS_READ',
				'CUSTOMERS_WRITE',
				'SETTLEMENTS_READ',
				'ITEMS_READ',
				'ITEMS_WRITE',
				'INVENTORY_READ',
				'INVENTORY_WRITE',
				'GIFTCARDS_READ',
				'GIFTCARDS_WRITE',
				'PAYMENTS_WRITE',
				'ORDERS_WRITE',
			) ),
		);

		return add_query_arg( $args, $url ); // nosemgrep:audit.php.wp.security.xss.query-arg -- This URL is escaped on output in get_connect_button_html().
	}

	/**
	 * Add Square connect URL to the JS.
	 *
	 * @return void
	 */
	private function add_square_connect_url_to_js() {
		add_filter( 'wc_calypso_bridge_shared_params', function( $params ) {
			if ( !$this->has_square_plugin_class() ){
				$params['square_connect_url'] = $this->get_connect_url();
				return $params;
			}

			try {
				$params['square_connect_url'] = \WooCommerce\Square\Plugin::instance()->get_connection_handler()->get_connect_url();
			} catch (\Throwable $e) {
				// Fallback to the settings page
				$params['square_connect_url'] = add_query_arg( array(
					'page' => 'wc-settings',
					'tab' => 'square',
				), admin_url( 'admin.php' ) );
			}

			return $params;
		});
	}

	/**
	 * Remove wc-admin-onboarding-payments-reminder note from the notes api endpoint.
	 *
	 * @return void
	 */
	private function remove_payments_note() {
		add_filter( 'rest_request_after_callbacks', function( $response, $handler, $request ) {
			if ( $request->get_route() === '/wc-analytics/admin/notes' ) {
				$data = $response->get_data();
				foreach( $data as $key=>$note ) {
					if ( isset( $note['name'] ) && $note['name'] === 'wc-admin-onboarding-payments-reminder' ) {
						unset( $data[$key] );
						$headers = $response->get_headers();
						if ( isset( $headers['X-WP-Total'] ) ) {
							$headers['X-WP-Total'] = (int) $headers['X-WP-Total'] - 1;
							$response->set_headers( $headers );
						}
						break;
					}
				}
				$response->set_data( array_values( $data ) );
			}
			return $response;
		}, 10, 3);
	}

	/**
	 * Force square_cash_app_pay and square_credit_card order
	 * IF user hans't customized the payment methods order yet.
	 *
	 * @return void
	 */
	private function force_square_payment_methods_order() {
		$order_option = get_option( 'woocommerce_gateway_order', false );
		if ( ! $order_option ) {
			update_option( 'woocommerce_gateway_order', array(
				'square_credit_card' => 0,
				'square_cash_app_pay' => 1,
			) );
		}
	}
}

WC_Calypso_Bridge_Partner_Square::get_instance();
