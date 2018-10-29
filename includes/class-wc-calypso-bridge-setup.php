<?php
/**
 * Adds the functionality needed to bridge the WooCommerce onboarding wizard.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge Setup
 */
class WC_Calypso_Bridge_Setup {

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Bridge_Setup instance
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
	 * Constructor.
	 */
	private function __construct() {
		if ( isset( $_GET['page'] ) && 'wc-setup' === $_GET['page'] ) {
			add_filter( 'woocommerce_setup_wizard_steps', array( $this, 'remove_unused_steps' ) );
			add_filter( 'woocommerce_enable_setup_wizard', '__return_false' );
			add_action( 'wp_loaded', array( $this, 'setup_wizard' ), 20 );
		}
	}

	/**
	 * Show the setup wizard.
	 */
	public function setup_wizard() {
		include_once dirname( __FILE__ ) . '/class-wc-calypso-bridge-admin-setup-wizard.php';
	}

	/**
	 * Remove unused steps from the wizard
	 *
	 * @param array $default_steps Default steps used by WC wizard.
	 * @return array
	 */
	public function remove_unused_steps( $default_steps ) {
		$whitelist = array( 'store_setup', 'payment' );
		$steps     = array_intersect_key( $default_steps, array_flip( $whitelist ) );
		return $steps;
	}

}

$wc_calypso_bridge_setup = WC_Calypso_Bridge_Setup::get_instance();
