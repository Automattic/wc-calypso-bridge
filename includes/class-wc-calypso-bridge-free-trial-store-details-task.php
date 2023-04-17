<?php


/**
 * Class WC_Calypso_Bridge_Free_Trial_Store_Details_Task.
 *
 * @since   2.0.15
 * @version 2.0.15
 *
 */
class WC_Calypso_Bridge_Free_Trial_Store_Details_Task
{
	/**
	 * The single instance of the class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Get class instance.
	 *
	 * @return object Instance.
	 */
	final public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		if ( ! wc_calypso_bridge_is_ecommerce_trial_plan() ) {
			return;
		}

		add_filter( 'woocommerce_admin_experimental_onboarding_tasklists', [ $this, 'replace_store_details_task' ] );
	}

	public function replace_store_details_task( $lists ) {
		if ( isset( $lists['setup'] ) ) {
			foreach ($lists['setup']->tasks as $index => $task) {
				if ( $task->get_id() === 'store_details' ) {
					require_once __DIR__ . '/tasks/class-wc-calypso-task-free-trial-store-details.php';
					$lists['setup']->tasks[$index] = new \Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks\TrialStoreDetails( $lists['setup'] );
				}
			}
		}
		return $lists;
	}
}

WC_Calypso_Bridge_Free_Trial_Store_Details_Task::get_instance();
