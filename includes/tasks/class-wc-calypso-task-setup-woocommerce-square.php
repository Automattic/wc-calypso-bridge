<?php

namespace Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks;

use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Task;

/**
 * WCBridgeSetupWooCommerceSquare Task
 *
 * @since   x.x.x
 * @version x.x.x
 */
class WCBridgeSetupWooCommerceSquare extends Task {
	/**
	 * ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return 'setup-woocommerce-square';
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
