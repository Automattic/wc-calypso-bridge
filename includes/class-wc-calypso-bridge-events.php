<?php
/**
 * Handle cron events.
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Calypso_Bridge_Events Class.
 */
class WC_Calypso_Bridge_Events {
	/**
	 * The single instance of the class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function __construct() {
		add_action( 'wc_calypso_bridge_daily', array( $this, 'do_wc_calypso_bridge_daily' ) );
    }

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
	 * Daily events to run.
	 */
	public function do_wc_calypso_bridge_daily() {
        include_once dirname( __FILE__ ) . '/notes/class-wc-calypso-bridge-navigation-learn-more-note.php';

        WC_Calypso_Bridge_Navigation_Learn_More_Note::possibly_add_note();
	}
}
