<?php

namespace Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks;

use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks\Appearance;
use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks\HeadstartProducts;

/**
 * WCBridgeAppearance Task
 *
 * @since   2.0.11
 * @version 2.0.11
 */
class WCBridgeAppearance extends Appearance {
	/**
	 * Addtional data.
	 *
	 * @return array
	 */
	public function get_additional_data() {
		return array(
			'has_homepage' => self::has_homepage(),
			'has_products' => HeadstartProducts::has_products(),
			'stylesheet'   => get_option( 'stylesheet' ),
			'theme_mods'   => get_theme_mods(),
		);
	}
	/**
	 * Check if the site has a homepage set up.
	 */
	public static function has_homepage() {
		// Temporary hotfix, we should implement a better solution in Core.
		$themes_no_need_homepage = array(
			'Tsubaki',
		);
		if ( in_array( wp_get_theme()->get( 'Name' ), $themes_no_need_homepage, true ) ) {
			return true;
		}

		return Appearance::has_homepage();
	}
}
