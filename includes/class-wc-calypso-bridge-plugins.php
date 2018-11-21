<?php
/**
 * Modifies bundled plugins
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge Plugins
 */
class WC_Calypso_Bridge_Plugins {

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Bridge_Plugins instance
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
		add_action( 'admin_init', array( $this, 'remove_mailchimp_redirect' ), 5 );
	}

	/**
	 * Remove Mailchimp redirect
	 */
	public function remove_mailchimp_redirect() {
		delete_option( 'mailchimp_woocommerce_plugin_do_activation_redirect' );
	}


}
$wc_calypso_bridge_plugins = WC_Calypso_Bridge_Plugins::get_instance();
