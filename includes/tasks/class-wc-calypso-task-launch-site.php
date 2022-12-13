<?php

namespace Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks;

use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Task;

/**
 * Launch Site Task
 *
 * @since x.x.x.
 * @version x.x.x.
 */
class LaunchSite extends Task {

	/**
	 * ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return 'launch_site';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		if ( true === $this->get_parent_option( 'use_completed_title' ) ) {
			if ( $this->is_complete() ) {
				return __( 'You\'ve already launched your store', 'woocommerce' );
			}
			return __( 'Launch your store', 'woocommerce' );
		}
		return __( 'Launch your store', 'woocommerce' );
	}

	/**
	 * Content.
	 *
	 * @return string
	 */
	public function get_content() {
		return __(
			'It's time to celebrate! Ready to launch your store?',
			'woocommerce'
		);
	}

	/**
	 * Time.
	 *
	 * @return string
	 */
	public function get_time() {
		return __( '1 minute', 'woocommerce' );
	}

	/**
	 * Action Label.
	 *
	 * @return string
	 */
	public function get_action_label() {
		return __( "Launch your store", 'woocommerce' );
	}

	/**
	 * Action URL.
	 *
	 * @return string|null
	 */
	public function get_action_url() {
		$status       = new \Automattic\Jetpack\Status();
		$site_suffix  = $status->get_site_suffix();
		// return ! $this->is_complete() ? null : sprintf( "https://wordpress.com/settings/general/%s#site-privacy-settings", $site_suffix );
		return null;
	}

	/**
	 * Task completion.
	 *
	 * @return bool
	 */
	public function is_complete() {
		// return 'launched' === get_option( 'launch-status' );
		return true;
	}
}
