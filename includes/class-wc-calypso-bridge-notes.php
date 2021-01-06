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
		include_once dirname( __FILE__ ) . '/notes/class-wc-calypso-bridge-navigation-learn-more-note.php';

		$navigation_learn_more_note = new WC_Calypso_Bridge_Navigation_Learn_More_Note();
	}

	/**
	 * Add qualifying notes.
	 */
	public function add_notes() {
		WC_Calypso_Bridge_Navigation_Learn_More_Note::possibly_add_note();
	}
}

WC_Calypso_Bridge_Notes::get_instance();
