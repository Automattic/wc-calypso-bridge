<?php

namespace Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks;

use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Task;

/**
 * Add domain Task
 *
 * @since   1.9.12.
 * @version 1.9.12.
 */
class AddDomain extends Task {

	/**
	 * ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return 'add_domain';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		if ( true === $this->get_parent_option( 'use_completed_title' ) ) {
			if ( $this->is_complete() ) {
				return __( 'You added your domain', 'woocommerce' );
			}

			return __( 'Add a domain', 'woocommerce' );
		}

		return __( 'Domain', 'woocommerce' );
	}

	/**
	 * Content.
	 *
	 * @return string
	 */
	public function get_content() {
		return __(
			'Add your domain name and make your store unique.',
			'woocommerce'
		);
	}

	/**
	 * Time.
	 *
	 * @return string
	 */
	public function get_time() {
		return __( '2 minutes', 'woocommerce' );
	}

	/**
	 * Task visibility.
	 *
	 * @return bool
	 */
	public function can_view() {
		return ! wc_calypso_bridge_is_ecommerce_trial_plan();
	}

	/**
	 * Action URL.
	 *
	 * @return string
	 */
	public function get_action_url() {
		$status      = new \Automattic\Jetpack\Status();
		$site_suffix = $status->get_site_suffix();

		return sprintf( "https://wordpress.com/domains/add/%s", $site_suffix );
	}

	/**
	 * Action Label.
	 *
	 * @return string
	 */
	public function get_action_label() {
		return __( "Add a domain", 'woocommerce' );
	}

	/**
	 * Task completion.
	 *
	 * @return bool
	 */
	public function is_complete() {
		// Determine if a custom domain is used by ensuring that the default atomic url wpcomstating is not part of the `siteurl` option.
		return false === strpos( get_option( 'siteurl' ), 'wpcomstaging' );
	}
}
