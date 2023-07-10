<?php
/**
 * AB Experiment handling for the reminder bar task list nudge.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   x.x.x
 * @version x.x.x
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Calypso_Bridge_Task_List_ReminderBar_Experiment Class.
 */
class WC_Calypso_Bridge_Task_List_ReminderBar_Experiment {

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
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Only in Ecommerce and Woo Express sites.
		if ( ! wc_calypso_bridge_has_ecommerce_features() ) {
			return;
		}

		if ( defined('REST_REQUEST') && REST_REQUEST ) {
			return;
		}

		if ( ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined('DOING_AJAX') && DOING_AJAX ) || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			return;
		}
 
		$this->init();
	}

	/**
	 * Init experiment.
	 */
	public function init() {

		add_filter( 'pre_option_woocommerce_task_list_reminder_bar_hidden', function( $pre_option ) {
			return self::is_experiment_treatment() ? $pre_option : 'yes';
		} );
	}

    /**
	 * Check if current session is experiment treatment.
	 *
	 * @return bool Returns true if the current session is treatment.
	 */
	protected static function is_experiment_treatment() {
		
		if ( ! class_exists( '\WooCommerce\Admin\Experimental_Abtest' ) ) {
			return false;
		}

		$anon_id        = isset( $_COOKIE['tk_ai'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['tk_ai'] ) ) : '';
		$allow_tracking = 'yes' === get_option( 'woocommerce_allow_tracking' );
		$abtest         = new \WooCommerce\Admin\Experimental_Abtest(
			$anon_id,
			'woocommerce',
			$allow_tracking
		);
		return $abtest->get_variation( 'woocommerce_woo_express_remindertopbar_woo_screens_nudge_202307_v1' ) === 'treatment';
	}
}

WC_Calypso_Bridge_Task_List_ReminderBar_Experiment::get_instance();
