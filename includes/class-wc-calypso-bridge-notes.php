<?php
/**
 * Notes.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Calypso_Bridge_Notes Class.
 */
class WC_Calypso_Bridge_Notes {
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

		// Only in Ecommerce.
		if ( ! wc_calypso_bridge_has_ecommerce_features() ) {
			return;
		}

		$this->init();
	}

	/**
	 * Include notes and initialize note hooks.
	 */
	public function init() {
		include_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/notes/class-wc-calypso-bridge-payments-remind-me-later-note.php';
		new WC_Calypso_Bridge_Payments_Remind_Me_Later_Note();
	}

	/**
	 * Add qualifying notes.
	 */
	public function add_notes() {
		WC_Calypso_Bridge_Payments_Remind_Me_Later_Note::possibly_add_note();
	}

	/**
	 * Delete qualifying notes.
	 */
	public function delete_notes() {
		WC_Calypso_Bridge_Payments_Remind_Me_Later_Note::possibly_clear_note();
	}
}

WC_Calypso_Bridge_Notes::get_instance();
