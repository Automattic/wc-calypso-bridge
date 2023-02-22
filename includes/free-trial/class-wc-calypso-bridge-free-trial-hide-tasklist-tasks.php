<?php
/**
 * Contains the logic for hiding tasklist and tasks
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   2.0.1
 * @version 2.0.1
 */

class WC_Calypso_Bridge_Free_Trial_Hide_TaskList_Tasks {
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

		add_filter( 'woocommerce_admin_experimental_onboarding_tasklists', [ $this, 'extend_tasklists' ] );
	}

	/**
	 * This filter is applied to the task list
	 *
	 * @param array $lists
	 * @return array $lists
	 */
	public function extend_tasklists( $lists ) {
		$lists = $this->hide_tasklists( $lists );
		$lists = $this->hide_tasks( $lists );

		return $lists;
	}

	/**
	 * Hides the tasklists that we don't want to show on the dashboard.
	 *
	 * @param array $lists The array of tasklists to be shown.
	 *
	 * @return array The array of tasklists to be shown.
	 */
	private function hide_tasklists( $lists ) {
			$tasklist_ids_to_be_hidden = Array(
				// Hide the next things to do tasklist
				'extended',
				'extended_two_column',
			);

			foreach ( $tasklist_ids_to_be_hidden as $tasklist_id ) {
				if ( isset( $lists[ $tasklist_id ] ) ) {
					unset( $lists[ $tasklist_id ] );
				}
			}

			return $lists;
	}

	/**
	 * Filters the tasks shown in the setup task list.
	 *
	 * @param array $lists The task lists.
	 * @return array $lists The task lists.
	 */
	private function hide_tasks( $lists ) {
		$tasklist_id = 'setup';
		$task_ids_to_be_hidden = Array(
			'marketing',
		);

		if ( isset( $lists[ $tasklist_id ] ) ) {
			$tasks = $lists[ $tasklist_id ]->tasks;

			$lists[ $tasklist_id ]->tasks = array_filter( $tasks,
				function( $task ) use ( $task_ids_to_be_hidden ) {
					return ! in_array( $task->get_id(), $task_ids_to_be_hidden, true );
			} );
		}
		return $lists;
	}
}

WC_Calypso_Bridge_Free_Trial_Hide_TaskList_Tasks::get_instance();
