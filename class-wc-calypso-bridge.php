<?php
/**
 * Load Calypsoify and bridge if enabled
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

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
		add_action( 'init', array( $this, 'possibly_load_calypsoify' ), 1 );
		add_action( 'init', array( $this, 'check_setup_param' ) );
	}

	/**
	 * Load calypsoify plugins if query param / user setting is set
	 */
	public function possibly_load_calypsoify() {
		if ( 1 === (int) get_user_meta( get_current_user_id(), 'calypsoify', true ) ) {
			$this->includes();
			// Hook on `admin_print_styles`, after some WC CSS is hooked, so we can override a few '!important' styles.
			add_action( 'admin_print_styles', array( $this, 'enqueue_calypsoify_styles' ), 11 );
		}
	}

	/**
	 * Loads required functionality, classes, and API endpoints.
	 */
	private function includes() {
		include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-page-controller.php';
		include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-menus.php';
		include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-setup.php';
		include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-admin-setup-checklist.php';
		include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-breadcrumbs.php';
		include_once dirname( __FILE__ ) . '/includes/gutenberg.php';

		$connect_files = glob( dirname( __FILE__ ) . '/includes/connect/*.php' );
		foreach ( $connect_files as $connect_file ) {
			include_once $connect_file;
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
	 * Add calypsoify styles
	 */
	public function enqueue_calypsoify_styles() {
		$asset_path = self::$plugin_asset_path ? self::$plugin_asset_path : self::MU_PLUGIN_ASSET_PATH;
		wp_enqueue_style( 'wc-calypso-bridge-calypsoify', $asset_path . 'assets/css/calypsoify.css', array(), WC_CALYPSO_BRIDGE_CURRENT_VERSION, 'all' );
		wp_enqueue_script( 'wc-calypso-bridge-calypsoify', $asset_path . 'assets/js/calypsoify.js', array( 'jquery' ), WC_CALYPSO_BRIDGE_CURRENT_VERSION, true );
		add_filter( 'woocommerce_display_admin_footer_text', '__return_false' );
	}

	/**
	 * Activates Calypsoify if the setup page is visited directly and it's not previously active.
	 */
	public function check_setup_param() {
		if ( isset( $_GET['page'] ) && 'wc-setup-checklist' === $_GET['page'] ) {
			if ( 1 !== (int) get_user_meta( get_current_user_id(), 'calypsoify', true ) ) {
				update_user_meta( get_current_user_id(), 'calypsoify', 1 );
				wp_safe_redirect( admin_url( 'admin.php?page=wc-setup-checklist' ) );
				exit;
			}
		}
	}

}

WC_Calypso_Bridge::instance();
