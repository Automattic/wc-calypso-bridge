<?php

namespace Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks;

use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Task;

/**
 * WCBridgeSetupWooCommerceStripe Task
 *
 * @since   2.5.5
 * @version 2.5.5
 */
class WCBridgeGetPaidWithStripe extends Task {
	/**
	 * ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return 'get-paid-with-stripe';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Get paid with Stripe', 'wc-calypso-bridge' );
	}

	/**
	 * Content.
	 *
	 * @return string
	 */
	public function get_content() {
		return __(
			"Set up Stripe payments to accept credit card payments in your store. You'll need a Stripe account to get started",
			'wc-calypso-bridge'
		);
	}

	/**
	 * Check if the task is complete.
	 *
	 * When Stripe is connected, it sets an access token in the options table.
	 * Count the access token and consider the task complete if it is not empty.
	 *
	 */
	public function is_complete() {
		if ( ! class_exists( '\WC_Stripe' ) ) {
			return false;
		}

		return woocommerce_gateway_stripe()->connect->is_connected();
	}

	/**
	 * Time.
	 *
	 * @return string
	 */
	public function get_time() {
		return __( '2 minutes', 'wc-calypso-bridge' );
	}

	/**
	 * Action label.
	 *
	 * @return string
	 */
	public function get_action_label() {
		return __( 'Get paid with Stripe', 'wc-calypso-bridge' );
	}
}
