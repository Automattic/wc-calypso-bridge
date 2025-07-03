<?php

namespace Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks;

use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks\StoreDetails;

/**
 * TrialStoreDetails Task
 * 
 * @since   2.0.15
 * @version 2.0.15
 */
class TrialStoreDetails extends StoreDetails {
	/**
	 * Task completion.
	 *
	 * @return bool
	 */
	public function is_complete() {
		return get_option( 'woocommerce_default_country', '' ) !== '';
	}	
}
