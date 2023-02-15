<?php
/**
 * Prevents Crowdsignal Forms plugin from doing a redirect.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 2.0.0
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

		add_action( 'admin_init', array( $this, 'add_crowdsignal_redirect_filter' ) );
	}

	/**
	 * Hook into add_option to disable the redirect.
	 */
	public function add_crowdsignal_redirect_filter() {
		add_action( 'add_option_crowdsignal_forms_do_activation_redirect', array( $this, 'disable_crowdsignal_redirect' ) );
	}

	/**
	 * When the option to redirect is added, delete the option.
	 */
	public function disable_crowdsignal_redirect() {
		delete_option( 'crowdsignal_forms_do_activation_redirect' );
	}
}

WC_Calypso_Bridge_Crowdsignal_Redirect::get_instance();
