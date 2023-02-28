<?php

/**
 * Class WC_Calypso_Bridge_Free_Trial_WC_Payments.
 *
 * @since   2.0.2
 * @version 2.0.4
 *
 * Includes JS on the WC Payments page to customize the look.
 */
class WC_Calypso_Bridge_Free_Trial_WC_Payments  {
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
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct(){
		if ( ! wc_calypso_bridge_is_ecommerce_trial_plan() ) {
			return;
		}

		if ( ! class_exists( 'Automattic\WooCommerce\Admin\PageController' ) ) {
			return;
		}

		add_action('current_screen', function() {
			if ( \Automattic\WooCommerce\Admin\PageController::get_instance()->is_admin_or_embed_page() ) {
				add_action('admin_enqueue_scripts', function() {
					wp_enqueue_script( 'wp-calypso-bridge-free-trial-wc-payments', WC_Calypso_Bridge_Instance()->get_asset_path() . 'assets/scripts/free-trial-wc-payments.js', array(), WC_CALYPSO_BRIDGE_CURRENT_VERSION, true );
				});
			}
		});

	}
}

WC_Calypso_Bridge_Free_Trial_WC_Payments::get_instance();
