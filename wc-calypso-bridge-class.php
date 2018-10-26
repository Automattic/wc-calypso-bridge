<?php
/**
 * WC Calypso Bridge
 */
class WC_Calypso_Bridge {
	/**
	 * Paths to assets act oddly in production
	 */
	const MU_PLUGIN_ASSET_PATH = '/wp-content/mu-plugins/wpcomsh/vendor/automattic/wc-calypso-bridge/';

	/**
	 * Plugin asset path
	 *
	 * @var string
	 */
	public static $plugin_asset_path = null;

	/**
	 * Class Instance.
	 *
	 * @var WC_Calypso_Bridge instance
	 */
	protected static $instance = null;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->includes();

		// Hook on `admin_print_styles`, after some WC CSS is hooked, so we can override a few '!important' styles.
		add_action( 'admin_print_styles', array( $this, 'possibly_add_calypsoify_styles' ), 11 );
	}

	/**
	 * Loads required functionality, classes, and API endpoints.
	 */
	private function includes() {
		include_once( dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-page-controller.php' );
		include_once( dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-menus.php' );
		include_once( dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-setup.php' );
		include_once( dirname( __FILE__ ) . '/includes/gutenberg.php' );
		$connect_files = glob( dirname( __FILE__ ) . '/includes/connect/*.php' );
		foreach ( $connect_files as $connect_file ) {
			include_once( $connect_file );
		}
	}

	/**
	 * Class instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			// If this is a traditionally installed plugin, set plugin_url for the proper asset path.
			if ( file_exists( WP_PLUGIN_DIR . '/wc-calypso-bridge/wc-calypso-bridge.php' ) ) {
				if ( WP_PLUGIN_DIR . '/wc-calypso-bridge/' == plugin_dir_path( __FILE__ ) ) {
					self::$plugin_asset_path = plugin_dir_url( __FILE__ );
				}
			}

			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Add calypsoify styles if calypsoify is enabled
	 */
	public function possibly_add_calypsoify_styles() {
		if ( 1 == (int) get_user_meta( get_current_user_id(), 'calypsoify', true ) ) {
			$asset_path = WC_Calypso_Bridge::$plugin_asset_path ? WC_Calypso_Bridge::$plugin_asset_path : WC_Calypso_Bridge::MU_PLUGIN_ASSET_PATH;
			wp_enqueue_style( 'wc-calypso-bridge-calypsoify', $asset_path . 'assets/css/calypsoify.css', array(), WC_CALYPSO_BRIDGE_CURRENT_VERSION, 'all' );
			add_filter( 'woocommerce_display_admin_footer_text', '__return_false' );
		}
	}
}

WC_Calypso_Bridge::instance();
