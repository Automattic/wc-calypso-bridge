<?php
/**
 * Handle cron events.
 *
 * @package WC_Calypso_Bridge/Classes
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
		add_action( 'plugins_loaded', array( $this, 'on_plugin_loaded' ), 0 );
		add_action( 'plugins_loaded', array( $this, 'init' ) );
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
	 * Initialize daily events.
	 */
	public function init() {
		add_action( 'wc_calypso_bridge_daily', array( $this, 'do_wc_calypso_bridge_daily' ) );
	}

	/**
	 * Registers the daily cron event.
	 */
	public function on_plugin_loaded() {
		if ( ! wp_next_scheduled( 'wc_calypso_bridge_daily' ) ) {
			wp_schedule_event( time(), 'daily', 'wc_calypso_bridge_daily' );
		}
	}

	/**
	 * Daily events to run.
	 */
	public function do_wc_calypso_bridge_daily() {
		require_once dirname( __FILE__ ) . '/class-wc-calypso-bridge-notes.php';
		WC_Calypso_Bridge_Notes::get_instance()->add_notes();
		WC_Calypso_Bridge_Notes::get_instance()->delete_notes();
	}
}

WC_Calypso_Bridge_Events::get_instance();
