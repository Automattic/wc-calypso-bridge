<?php
/**
 * Load the bridge if enabled
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge
 */
class WC_Calypso_Bridge {

	/**
	 * Ecommerce Plan release timestamps.
	 */
	const RELEASE_DATE_DEFAULT_CHECKOUT_BLOCKS = 1667898000; // Tuesday, November 8, 2022 9:00:00 AM GMT
	const RELEASE_DATE_PRE_CONFIGURE_JETPACK   = 1667898000; // Tuesday, November 8, 2022 9:00:00 AM GMT
	const RELEASE_DATE_ECOMMERCE_NAVIGATION    = 1673463773; // Wednesday, January 11, 2023 19:00:00 PM GMT

	/**
	 * Paths to assets act oddly in production.
	 */
	const MU_PLUGIN_ASSET_PATH = '/wp-content/mu-plugins/wpcomsh/vendor/automattic/wc-calypso-bridge/';

	/**
	 * Plugin asset path.
	 *
	 * @var string
	 */
	private $plugin_asset_path = null;

	/**
	 * Class Instance.
	 *
	 * @var WC_Calypso_Bridge instance
	 */
	protected static $instance = null;

	/**
	 * Class instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			// If this is a traditionally installed plugin, set plugin_url for the proper asset path.
			if ( file_exists( WP_PLUGIN_DIR . '/wc-calypso-bridge/wc-calypso-bridge.php' ) ) {

				if ( WP_PLUGIN_DIR . '/wc-calypso-bridge/' == plugin_dir_path( __FILE__ ) ) {
					$this->$plugin_asset_path = plugin_dir_url( __FILE__ );
				}
			}

			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->includes();

		add_action( 'plugins_loaded', array( $this, 'load_transalation' ) );

		if ( ! is_admin() && ! defined( 'DOING_CRON' ) ) {
			return;
		}

		// Only in Ecommerce.
		if ( ! wc_calypso_bridge_is_ecommerce_plan() ) {
			return;
		}

		add_action( 'init', array( $this, 'load_ecommerce_plan_ui' ), 2 );
	}

	/**
	 * Include files and controllers.
	 */
	public function includes() {
		require_once WC_CALYSPO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-helper-functions.php';
		require_once WC_CALYSPO_BRIDGE_PLUGIN_PATH . '/class-wc-calypso-bridge-shared.php';
		require_once WC_CALYSPO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-setup.php';
		require_once WC_CALYSPO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-jetpack.php';
		require_once WC_CALYSPO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-setup-tasks.php';
		require_once WC_CALYSPO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-filters.php';
		require_once WC_CALYSPO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-tracks.php';
		require_once WC_CALYSPO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-events.php';
		require_once WC_CALYSPO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-crowdsignal-redirect.php';
		require_once WC_CALYSPO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-themes-setup.php';
		require_once WC_CALYSPO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-woocommerce-admin-features.php';
		require_once WC_CALYSPO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-hide-alerts.php';
		require_once WC_CALYSPO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-plugins.php';
		require_once WC_CALYSPO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-addons.php';
	}

	/**
	 * Load ecommerce plan UI changes.
	 */
	public function load_ecommerce_plan_ui() {

		/**
		 * Load Ecommerce styles.
		 */
		add_action( 'admin_enqueue_scripts', array( $this, 'add_ecommerce_plan_styles' ) );

		/**
		 * Disable block editor for post types.
		 *
		 * @param bool    $value
		 * @param string  $post_type
		 * @return bool
		 */
		add_filter( 'use_block_editor_for_post_type', static function( $value, $post_type ) {

			$wc_post_types = array(
				'shop_coupon',
				'shop_order',
				'product',
				'bookable_resource',
				'wc_booking',
				'event_ticket',
				'wc_membership_plan',
				'wc_user_membership',
				'wc_voucher',
				'wc_pickup_location',
				'shop_subscription',
				'wc_product_tab',
				'wishlist',
				'wc_zapier_feed',
			);

			if ( in_array( $post_type, $wc_post_types ) ) {
				return false;
			}

			return $value;
		}, 10, 2 );

		/**
		 * Decalypsoify ecommerce plans in case the user meta has already been previously set. Remove calypsoify styles to prevent styling conflicts.
		 *
		 * @param null   $null Always null.
		 * @param int    $object_id Object ID.
		 * @param string $meta_key Meta key.
		 * @return null|bool
		 */
		add_filter( 'get_user_metadata', function( $null, $object_id, $meta_key ) {
			if ( 'calypsoify' === $meta_key ) {
				return false;
			}

			return $null;
		}, 10, 3 );

		/**
		 * Remove admin footer text.
		 */
		add_filter( 'woocommerce_display_admin_footer_text', '__return_false' );
	}

	/**
	 * Add styles for ecommerce plan.
	 */
	public function add_ecommerce_plan_styles() {
		wp_enqueue_style( 'wp-calypso-bridge-ecommerce', $this->get_asset_path() . 'assets/css/ecommerce.css', array(), WC_CALYPSO_BRIDGE_CURRENT_VERSION );

		if ( (bool) apply_filters( 'ecommerce_new_woo_atomic_navigation_enabled', true ) ) {
			wp_enqueue_style( 'wp-calypso-bridge-ecommerce-navigation', $this->get_asset_path() . 'assets/css/ecommerce-navigation.css', array(), WC_CALYPSO_BRIDGE_CURRENT_VERSION );
		}
	}

	/**
	 * Loads language files for the plugin.
	 *
	 * @since 2.0.0
	 */
	public function load_transalation() {
		$plugin_path = WC_CALYSPO_BRIDGE_PLUGIN_PATH . '/languages';
		$locale      = apply_filters( 'plugin_locale', determine_locale(), 'wc-calypso-bridge' );
		$mofile      = $plugin_path . '/wc-calypso-bridge-' . $locale . '.mo';

		load_textdomain( 'wc-calypso-bridge', $mofile );
	}

	/*---------------------------------------------------*/
	/*  Utils.                                           */
	/*---------------------------------------------------*/

	/**
	 * Get plugin asset path.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_asset_path() {
		return $this->plugin_asset_path ? $this->plugin_asset_path : self::MU_PLUGIN_ASSET_PATH;
	}

	/**
	 * Record event using JetPack if enabled
	 *
	 * @param string $event_name Name of the event.
	 * @param array  $event_params Custom event params to capture.
	 */
	public function record_event( $event_name, $event_params ) {
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

	/**
	 * Log using 'WC_Logger' class.
	 *
	 * @since 1.9.5
	 *
	 * @param string $message Message to log.
	 * @param string $level   Type of log.
	 * @param string $context Source context.
	 *
	 * @return void
	 */
	public function log_message( $message, $level = 'debug', $context = 'dotcom-ecommerce' ) {

		if ( ! function_exists( 'wc_get_logger' ) ) {
			return;
		}

		$logger = wc_get_logger();
		$logger->log( $level, $message, array( 'source' => $context ) );
	}
}

/**
 * Returns the main instance of WC_Calypso_Bridge to prevent the need to use globals.
 *
 * @return  WC_Calypso_Bridge
 */
function WC_Calypso_Bridge_Instance() {
	return WC_Calypso_Bridge::instance();
}

WC_Calypso_Bridge_Instance();
