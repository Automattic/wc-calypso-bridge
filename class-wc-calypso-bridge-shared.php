<?php
/**
 * Load the bridge if enabled
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Loader;

/**
 * WC Calypso Bridge
 */
class WC_Calypso_Bridge_Shared {
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
	 * @var WC_Calypso_Bridge_Shared instance
	 */
	protected static $instance = null;

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! is_admin() && ! defined( 'DOING_CRON' ) ) {
			return;
		}

		add_action( 'plugins_loaded', array( $this, 'initialize' ), 2 );
		add_action( 'current_screen', array( $this, 'load_ui_elements' ) );
	}

	/**
	 * Initialize only if WC is present.
	 */
	public function initialize() {
		// if woo is not active, then bail.
		if ( ! function_exists( 'WC' ) ) {
			return;
		}
		add_action( 'admin_enqueue_scripts', array( $this, 'add_extension_register_script') );
	}

	function add_extension_register_script() {
		$is_woo_page = class_exists( 'Automattic\WooCommerce\Admin\Loader' )
			&& \Automattic\WooCommerce\Admin\Loader::is_admin_or_embed_page()
			? true
			: false;

		$script_path       = '/build/index.js';
		$script_asset_path = dirname( __FILE__ ) . '/build/index.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require( $script_asset_path )
			: array( 'dependencies' => array(), 'version' => filemtime( $script_path ) );
		$script_url = plugins_url( $script_path, __FILE__ );

		wp_register_script(
			'wc-calypso-bridge',
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		wp_add_inline_script( 'wc-calypso-bridge', 'window.wcCalypsoBridge = ' . wp_json_encode(
			array(
				'isWooPage' => $is_woo_page,
				'homeUrl'   => get_home_url(),
				'wcpayConnectUrl' => 'admin.php?page=wc-admin&path=%2Fpayments%2Fconnect&wcpay-connect=1&_wpnonce=' . wp_create_nonce( 'wcpay-connect' )
			)
		), 'before' );

		wp_enqueue_script( 'wc-calypso-bridge' );
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
	 * Updates required UI elements for calypso bridge pages only.
	 */
	public function load_ui_elements() {
    include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-page-controller.php';

    // @todo This should rely on the navigation screens instead.
    $connect_files = glob( dirname( __FILE__ ) . '/includes/connect/*.php' );
    foreach ( $connect_files as $connect_file ) {
      include_once $connect_file;
    }

		if ( is_wc_calypso_bridge_page() ) {
		// Nav unification fixes.
		if ( function_exists( 'wpcomsh_activate_nav_unification' )
			&& wpcomsh_activate_nav_unification( false )
			&& ! Loader::is_feature_enabled( 'navigation' ) ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'add_nav_unification_styles' ) );
			}
		}
	}

	/**
	 * Add styles for nav unification fixes.
	 */
	public function add_nav_unification_styles() {
		$asset_path = self::$plugin_asset_path ? self::$plugin_asset_path : self::MU_PLUGIN_ASSET_PATH;
		wp_enqueue_style( 'wp-calypso-bridge-nav-unification', $asset_path . 'store-on-wpcom/assets/css/admin/nav-unification.css', array(), WC_CALYPSO_BRIDGE_CURRENT_VERSION );
	}
}
WC_Calypso_Bridge_Shared::instance();
