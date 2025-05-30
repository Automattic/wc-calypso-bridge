<?php


/**
 * Class WC_Calypso_Bridge_Free_Trial_Payment_Task.
 *
 * @since   1.9.16
 * @version 1.9.16
 *
 */
class WC_Calypso_Bridge_Free_Trial_Payment_Task
{
	/**
	 * The single instance of the class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * List of valid payment partners
	 *
	 * @var array
	 */
	const VALID_PARTNERS = ['square', 'paypal', 'stripe'];

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
		$onboarding_profile = get_option( 'woocommerce_onboarding_profile', array() );
		if ( ! isset( $onboarding_profile['partner'] ) ) {
			return;
		}

		if ( ! in_array( $onboarding_profile['partner'], self::VALID_PARTNERS ) ) {
			return;
		}

		add_filter( 'woocommerce_admin_experimental_onboarding_tasklists', [ $this, 'replace_payment_task' ] );
	}

	public function replace_payment_task( $lists ) {
		if ( isset( $lists['setup'] ) ) {
			foreach ($lists['setup']->tasks as $index => $task) {
				if ( $task->get_id() === 'payments' ) {
					require_once __DIR__ . '/tasks/class-wc-calypso-task-free-trial-payments.php';
					$lists['setup']->tasks[$index] = new \Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks\TrialPayments( $lists['setup'] );
				}
			}
		}
		return $lists;
	}
}

WC_Calypso_Bridge_Free_Trial_Payment_Task::get_instance();
