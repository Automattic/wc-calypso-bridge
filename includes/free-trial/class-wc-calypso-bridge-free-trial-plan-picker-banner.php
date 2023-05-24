<?php

/**
 * Class WC_Calypso_Bridge_Free_Trial_Plan_Picker_Banner.
 *
 * @since   2.0.5
 * @version 2.0.16
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

		/**
		 * Disable the generic site launch banner for eCommerce trials.
		 */
		add_filter( 'wpcomsh_private_site_show_logged_in_banner', '__return_false' );

		add_action('init', function() {
			if ( current_user_can( 'manage_woocommerce' ) ) {
				add_action( 'wp_head', array( $this, 'add_plan_picker_banner' ), -2000 );
				add_filter( 'body_class', function ( $classes ) {
					if ( !in_array('admin-bar', $classes )) {
						$classes[] = 'hide-free-trial-plan-picker';
					}
					return $classes;
				});
				if ( ! class_exists( 'WC_Site_Tracking' ) ) {
					include_once WC_ABSPATH . 'includes/tracks/class-wc-tracks.php';
					include_once WC_ABSPATH . 'includes/tracks/class-wc-tracks-event.php';
					include_once WC_ABSPATH . 'includes/tracks/class-wc-tracks-client.php';
					include_once WC_ABSPATH . 'includes/tracks/class-wc-tracks-footer-pixel.php';
					include_once WC_ABSPATH . 'includes/tracks/class-wc-site-tracking.php';
				}

				add_action( 'wp_footer', array( $this, 'append_tracking_script' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'add_styles_and_scripts' ) );
			}
		});
	}

	public function append_tracking_script() {
		WC_Site_Tracking::add_tracking_function();
	}

	/**
	 * @return void
	 */
	public function add_plan_picker_banner() {
		$status      = new \Automattic\Jetpack\Status();
		$site_suffix = $status->get_site_suffix();
		$link = sprintf( "https://wordpress.com/plans/%s", $site_suffix );

		$text = sprintf( __("
			At the moment you are the only one who can see your store. To make your store available to everyone, please&nbsp;<a href='%s' id='banner_button'>upgrade to a paid plan</a>.
		", 'wc-calypso-bridge' ), $link );
		echo "<div id='free-trial-plan-picker-banner'>$text</div>";
	}

	public function add_styles_and_scripts() {
		wp_enqueue_style( 'wp-calypso-bridge-ecommerce-free-trial-plan-picker-banner', WC_Calypso_Bridge_Instance()->get_asset_path() . 'assets/css/free-trial-plan-picker-banner.css', array(), WC_CALYPSO_BRIDGE_CURRENT_VERSION );
		wp_enqueue_script( 'wp-calypso-bridge-ecommerce-free-trial-plan-picker-banner', WC_Calypso_Bridge_Instance()->get_asset_path() . 'assets/scripts/frontend-banner-tracks.js', array(), WC_CALYPSO_BRIDGE_CURRENT_VERSION );
		wp_enqueue_script( 'woo-tracks', 'https://stats.wp.com/w.js', array( 'wp-hooks' ), gmdate( 'YW' ), false );
	}
}

WC_Calypso_Bridge_Free_Trial_Plan_Picker_Banner::get_instance();
