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
	}

	/**
	 * Add qualifying notes.
	 */
	public function add_notes() {
	}

	/**
	 * Delete qualifying notes.
	 */
	public function delete_notes() {
	}
}

WC_Calypso_Bridge_Notes::get_instance();
