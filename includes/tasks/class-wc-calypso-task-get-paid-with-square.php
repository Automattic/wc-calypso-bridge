<?php

namespace Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks;

use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Task;

/**
 * WCBridgeSetupWooCommerceSquare Task
 *
 * @since   2.3.5
 * @version 2.3.5
 */
class WCBridgeGetPaidWithSquare extends Task {
	/**
	 * ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return 'get-paid-with-square';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Get paid with Square', 'wc-calypso-bridge' );
	}

	/**
	 * Content.
	 *
	 * @return string
	 */
	public function get_content() {
		return __(
			"Set up Square payments to accept credit card payments in your store. You'll need a Square account to get started",
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
		$access_token = get_option( 'wc_square_access_tokens', array() );
		return ! empty( $access_token );
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
		return __( 'Get paid with Square', 'wc-calypso-bridge' );
	}
}
