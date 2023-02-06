<?php

/**
 * Class WC_Calypso_Bridge_ECOM_Free_Trial_Frontend.
 *
 * @since   1.9.16
 * @version 1.9.16
 *
 * Handles Free Trial frontend.
 */
class WC_Calypso_Bridge_ECOM_Free_Trial_Frontend  {
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
		add_action('init', function() {
			if (current_user_can('manage_woocommerce')) {
				add_action('wp_enqueue_scripts', array($this, 'add_styles'));
				add_action('wp_footer', array($this, 'add_plan_picker_banner'), 10);
			}
		});
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

WC_Calypso_Bridge_ECOM_Free_Trial_Frontend::get_instance();