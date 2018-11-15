<?php
/**
 * Adds a wrapper to tables to allow scrolling long tables
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge Tables
 */
class WC_Calypso_Bridge_Tables {

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Bridge_Tables instance
	 */
	protected static $instance = false;

	/**
	 * Get class instance
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		add_action( 'manage_posts_extra_tablenav', array( $this, 'wrap_before_table' ), PHP_INT_MAX );
		add_action( 'manage_posts_extra_tablenav', array( $this, 'wrap_after_table' ), 0 );
	}

	/**
	 * Open the wrapper and create a fake div that's closed by the previous tablenav top
	 *
	 * @param string $which Top or bottom.
	 */
	public function wrap_before_table( $which ) {
		if ( 'top' === $which ) {
			echo '</div><div class="wp-list-table-wrapper"><div class="wp-list-table-wrapper__inner"><div class="wp-list-table-wrapper__fake-inner">';
		}
	}

	/**
	 * Close the wrapper and reopen the bottom tablenav
	 *
	 * @param string $which Top or bottom.
	 */
	public function wrap_after_table( $which ) {
		if ( 'bottom' === $which ) {
			echo '</div></div></div><div class="tablenav bottom secondary">';
		}
	}

}
$wc_calypso_bridge_table = WC_Calypso_Bridge_Tables::get_instance();
