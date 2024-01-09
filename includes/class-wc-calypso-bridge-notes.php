<?php
/**
 * Notes.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 2.3.2
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
		include_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/notes/class-wc-calypso-bridge-choose-domain.php';
		new WC_Calypso_Bridge_Choose_Domain_Note();
	}

	/**
	 * Add qualifying notes.
	 */
	public function add_notes() {
		WC_Calypso_Bridge_Choose_Domain_Note::possibly_add_note();
	}

	/**
	 * Update notes.
	 */
	public function update_notes() {
		WC_Calypso_Bridge_Choose_Domain_Note::update_note();
	}

	/**
	 * Delete qualifying notes.
	 */
	public function delete_notes() {
		WC_Calypso_Bridge_Choose_Domain_Note::delete_if_not_applicable();
	}
}

WC_Calypso_Bridge_Notes::get_instance();
