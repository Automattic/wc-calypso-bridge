<?php

namespace Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks;

use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Task;

/**
 * Launch Site Task
 *
 * @since   1.9.12
 * @version 2.0.8
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
			'It\'s time to celebrate! Ready to launch your store?',
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
		return ! $this->is_complete() ? null : sprintf( "https://wordpress.com/settings/general/%s#site-privacy-settings", WC_Calypso_Bridge_Instance()->get_site_slug() );
	}

	/**
	 * Task completion.
	 *
	 * @return bool
	 */
	public function is_complete() {
		$launch_status = get_option( 'launch-status' );

		// The site is launched when the launch status is 'launched' or missing.
		$launched_values = array(
			'launched',
			'',
			false,
		);
		return in_array( $launch_status, $launched_values, true );
	}

	/**
	 * The task should not be displayed if the site is on the eCommerce trial.
	 *
	 * @return bool
	 */
	public function can_view() {
		return ! wc_calypso_bridge_is_ecommerce_trial_plan();
	}
}
