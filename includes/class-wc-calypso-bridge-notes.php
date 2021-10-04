<?php
/**
 * Notes.
 *
 * @package WC_Calypso_Bridge/Classes
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
		$this->init();
	}

	/**
	 * Include notes and initialize note hooks.
	 */
	public function init() {
		if ( wc_calypso_bridge_is_ecommerce_plan() ) {
			include_once dirname( __FILE__ ) . '/notes/class-wc-calypso-bridge-navigation-learn-more-note.php';
			new WC_Calypso_Bridge_Navigation_Learn_More_Note();
		}

		include_once dirname( __FILE__ ) . '/notes/class-wc-calypso-bridge-payments-remind-me-later-note.php';
		new WC_Calypso_Bridge_Payments_Remind_Me_Later_Note();
	}

	/**
	 * Add qualifying notes.
	 */
	public function add_notes() {
		if ( wc_calypso_bridge_is_ecommerce_plan() ) {
			WC_Calypso_Bridge_Navigation_Learn_More_Note::possibly_add_note();
		}

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
