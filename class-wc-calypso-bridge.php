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
		add_action( 'init', array( $this, 'check_calyposify_param' ), 1 );
		add_action( 'init', array( $this, 'check_setup_param' ) );
		add_action( 'init', array( $this, 'possibly_load_calypsoify' ), 2 );
	}

	/**
	 * Check for calypsoify param in URL
	 *
	 * We use our own check since Jetpack's does not load fast enough and
	 * only hooks on admin_init which won't be run by wc-setup
	 */
	public function check_calyposify_param() {
		if ( isset( $_GET['calypsoify'] ) ) { // WPCS: CSRF ok.
			if ( 1 === (int) $_GET['calypsoify'] ) { // WPCS: CSRF ok.
				update_user_meta( get_current_user_id(), 'calypsoify', 1 );
			} else {
				update_user_meta( get_current_user_id(), 'calypsoify', 0 );
			}

			if ( isset( $_SERVER['REQUEST_URI'] ) ) {
				$page = remove_query_arg( 'calypsoify', wp_basename( $_SERVER['REQUEST_URI'] ) ); // WPCS: Sanitization ok.
				wp_safe_redirect( admin_url( $page ) );
				exit;
			}
		}
	}

	/**
	 * Load calypsoify plugins if query param / user setting is set
	 */
	public function possibly_load_calypsoify() {
		add_action( 'admin_init', array( $this, 'track_calypsoify_toggle' ) );
		if ( 1 === (int) get_user_meta( get_current_user_id(), 'calypsoify', true ) ) {
			$this->includes();
			// Hook on `admin_print_styles`, after some WC CSS is hooked, so we can override a few '!important' styles.
			add_action( 'admin_print_styles', array( $this, 'enqueue_calypsoify_scripts' ), 11 );
			add_action( 'admin_init', array( $this, 'remove_woocommerce_footer_text' ) );
		}
	}

	/**
	 * Loads required functionality, classes, and API endpoints.
	 */
	private function includes() {
		include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-page-controller.php';
		include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-menus.php';
		include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-setup.php';
		include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-themes-setup.php';
		include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-admin-setup-checklist.php';
		include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-breadcrumbs.php';
		include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-hide-alerts.php';
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
	public function enqueue_calypsoify_scripts() {
		$asset_path = self::$plugin_asset_path ? self::$plugin_asset_path : self::MU_PLUGIN_ASSET_PATH;
		wp_enqueue_style( 'wc-calypso-bridge-calypsoify', $asset_path . 'assets/css/calypsoify.css', array(), WC_CALYPSO_BRIDGE_CURRENT_VERSION, 'all' );
		wp_enqueue_script( 'wc-calypso-bridge-calypsoify', $asset_path . 'assets/js/calypsoify.js', array( 'jquery' ), WC_CALYPSO_BRIDGE_CURRENT_VERSION, true );
	}

	/**
	 * Remove WooCommerce footer text
	 */
	public function remove_woocommerce_footer_text() {
		add_filter( 'woocommerce_display_admin_footer_text', '__return_false' );
	}

	/**
	 * Activates Calypsoify if the setup page is visited directly and it's not previously active.
	 */
	public function check_setup_param() {
		if ( isset( $_GET['page'] ) && 'wc-setup-checklist' === $_GET['page'] ) { // WPCS: CSRF ok.
			if ( 1 !== (int) get_user_meta( get_current_user_id(), 'calypsoify', true ) ) {
				update_user_meta( get_current_user_id(), 'calypsoify', 1 );
				wp_safe_redirect( admin_url( 'admin.php?page=wc-setup-checklist' ) );
				exit;
			}
		}
	}

	/**
	 * Track Calypsoify events when turned on or off
	 */
	public function track_calypsoify_toggle() {
		if ( isset( $_GET['calypsoify'] ) ) { // WPCS: CSRF ok.
			$calypsoify_status = (int) get_user_meta( $current_user->ID, 'calypsoify', true );
			if ( 1 === $calypsoify_status && 0 === (int) $_GET['calypsoify'] // WPCS: CSRF ok.
				|| 0 === $calypsoify_status && 1 === (int) $_GET['calypsoify'] // WPCS: CSRF ok.
			) {
				$this->record_event(
					'atomic_wc_calypsoify_toggle',
					array( 'status' => intval( $_GET['calypsoify'] ) ? 'on' : 'off' ) // WPCS: CSRF ok.
				);
			}
		}
	}

	/**
	 * Record event using JetPack if enabled
	 *
	 * @param string $event_name Name of the event.
	 * @param array  $event_params Custom event params to capture.
	 */
	public static function record_event( $event_name, $event_params ) {
		if ( function_exists( 'jetpack_tracks_record_event' ) ) {
			$current_user         = wp_get_current_user();
			$default_event_params = array( 'blog_id' => Jetpack_Options::get_option( 'id' ) );
			$event_params         = array_merge( $default_event_params, $event_params );
			jetpack_tracks_record_event(
				$current_user,
				$event_name,
				$event_params
			);
		}
	}

}
if ( is_admin() ) {
	WC_Calypso_Bridge::instance();
}
