<?php

namespace Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks;

use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Task;

/**
 * WCBridgeSetupWooCommercePayPal Task
 *
 * @since   x.x.x
 * @version x.x.x
 */
class WCBridgeGetPaidWithPayPal extends Task {
	/**
	 * ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return 'get-paid-with-paypal';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Get paid with PayPal', 'wc-calypso-bridge' );
	}

	/**
	 * Content.
	 *
	 * @return string
	 */
	public function get_content() {
		return __(
			"Set up PayPal payments to accept credit card payments in your store. You'll need a PayPal account to get started",
			'wc-calypso-bridge'
		);
	}

		/**
	 * Check if the task is complete.
	 *
	 * When Square is connected, it sets an access token in the options table.
	 * Count the access token and consider the task complete if it is not empty.
	 *
	 */
	public function is_complete() {

		$pp = \WooCommerce\PayPalCommerce\PPCP::container();
		$state = $pp->get('onboarding.state');
		$is_onboarded = ( $state->current_state() >= \WooCommerce\PayPalCommerce\Onboarding\State::STATE_ONBOARDED );

		return $is_onboarded;
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
		return __( 'Get paid with PayPal', 'wc-calypso-bridge' );
	}
}
