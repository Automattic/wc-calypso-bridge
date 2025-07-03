<?php
/**
 * Prevents Crowdsignal Forms plugin from doing a redirect.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 2.1.9
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge Crowdsignal Redirect
 */
class WC_Calypso_Bridge_Crowdsignal_Redirect {

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Bridge_Crowdsignal_Redirect instance
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

		// Only Ecommerce.
		if ( ! wc_calypso_bridge_has_ecommerce_features() ) {
			return;
		}

		add_action( 'admin_init', array( $this, 'disable_crowdsignal_redirect' ), 9 );
	}

	/**
	 * Prevent redirection, by deleting the option earlier than Crowdsignal Forms runs their activate_redirect.
	 */
	public function disable_crowdsignal_redirect() {
		if ( get_option( 'crowdsignal_forms_do_activation_redirect', false ) ) {
			delete_option( 'crowdsignal_forms_do_activation_redirect' );
		}
	}
}

WC_Calypso_Bridge_Crowdsignal_Redirect::get_instance();
