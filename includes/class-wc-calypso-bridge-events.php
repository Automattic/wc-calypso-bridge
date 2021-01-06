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
		register_activation_hook( WC_CALYSPO_BRIDGE_PLUGIN_FILE, array( $this, 'on_plugin_activation' ) );
		register_deactivation_hook( WC_CALYSPO_BRIDGE_PLUGIN_FILE, array( $this, 'on_plugin_deactivation' ) );
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
	public function on_plugin_activation() {
		if ( ! wp_next_scheduled( 'wc_calypso_bridge_daily' ) ) {
			wp_schedule_event( time(), 'daily', 'wc_calypso_bridge_daily' );
		}
	}

	/**
	 * Clear scheduled cron events.
	 */
	public function on_plugin_deactivation() {
		wp_clear_scheduled_hook( 'wc_calypso_bridge_daily' );
	}

	/**
	 * Daily events to run.
	 */
	public function do_wc_calypso_bridge_daily() {
		if ( class_exists( 'WC_Calypso_Bridge_Notes' ) ) {
			WC_Calypso_Bridge_Notes::get_instance()->add_notes();
		}
	}
}

WC_Calypso_Bridge_Events::get_instance();
