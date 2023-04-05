<?php

/**
 * Class WC_Calypso_Bridge_Free_Trial_Plan_Picker_Banner.
 *
 * @since   2.0.5
 * @version 2.0.5
 *
 * Handles Free Trial Plan Picker Banner.
 */
class WC_Calypso_Bridge_Free_Trial_Plan_Picker_Banner {
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
	
	public function __construct() {
		if ( ! wc_calypso_bridge_is_ecommerce_trial_plan() ) {
			return;
		}

		add_action('init', function() {
			if ( current_user_can( 'manage_woocommerce' ) ) {
				add_action( 'wp_head', array( $this, 'add_plan_picker_banner' ), -2000 );
				add_filter( 'body_class', function ( $classes ) {
					if ( !in_array('admin-bar', $classes )) {
						$classes[] = 'hide-free-trial-plan-picker';
					}
					return $classes;
				});
				add_action( 'wp_enqueue_scripts', array( $this, 'add_styles' ) );
			}
		});

	}

	/**
	 * @return void
	 */
	public function add_plan_picker_banner() {
		$status      = new \Automattic\Jetpack\Status();
		$site_suffix = $status->get_site_suffix();
		$link = sprintf( "https://wordpress.com/plans/%s", $site_suffix );

		$text = sprintf( __("
			At the moment you are the only one who can see your store. To make your store available to everyone, please&nbsp;<a href='%s'>upgrade to a paid plan</a>.
		", 'wc-calypso-bridge' ), $link );
		echo "<div id='free-trial-plan-picker-banner'>$text</div>";
	}

	public function add_styles() {
		wp_enqueue_style( 'wp-calypso-bridge-ecommerce-free-trial-plan-picker-banner', WC_Calypso_Bridge_Instance()->get_asset_path() . 'assets/css/free-trial-plan-picker-banner.css', array(), WC_CALYPSO_BRIDGE_CURRENT_VERSION );
	}
}

WC_Calypso_Bridge_Free_Trial_Plan_Picker_Banner::get_instance();
