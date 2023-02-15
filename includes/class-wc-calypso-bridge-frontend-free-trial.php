<?php

/**
 * Class WC_Calypso_Bridge_Frontend_Free_Trial.
 *
 * @since   x.x.x
 * @version x.x.x
 *
 * Handles Free Trial frontend.
 */
class WC_Calypso_Bridge_Frontend_Free_Trial  {
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
			if (current_user_can('manage_woocommerce')) {
				add_action('wp_footer', array($this, 'add_plan_picker_banner'), 10);
			}
		});

		add_action('wp_enqueue_scripts', array($this, 'add_styles'));
	}

	/**
	 * @return void
	 */
	public function add_plan_picker_banner()
	{
		$link = 'https://wordpress.com/';
		$text = sprintf( __("
			At the moment you are the only one who can see your store. To let everyone see your store, you simply need to&nbsp;<a href='%s'>pick a plan</a>.
		", 'wc-calypso-bridge' ), $link );
		echo "<div id='free-trial-plan-ppicker_banner'>$text</div>";
	}

	public function add_styles()
	{
		$asset_path = WC_Calypso_Bridge::$plugin_asset_path ? WC_Calypso_Bridge::$plugin_asset_path : WC_Calypso_Bridge::MU_PLUGIN_ASSET_PATH;
		wp_enqueue_style( 'wp-calypso-bridge-ecommerce', $asset_path . 'assets/css/free-trial.css', array(), WC_CALYPSO_BRIDGE_CURRENT_VERSION );
	}
}

WC_Calypso_Bridge_Frontend_Free_Trial::get_instance();
