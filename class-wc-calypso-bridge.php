<?php
/**
 * Load the bridge if enabled
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 2.0.8
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
	private static $plugin_asset_path = null;

	/**
	 * Class Instance.
	 *
	 * @var WC_Calypso_Bridge instance
	 */
	protected static $instance = null;

	/**
	 * Class instance.
	 */
	public static function get_instance() {
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
	 * Ensure WooCommerce is installed and up to date.
	 */
	private function is_woocommerce_valid() {
		return (
			function_exists( 'WC' ) &&
			property_exists( WC(), 'version' ) &&
			version_compare( WC()->version, WC_MIN_VERSION ) >= 0
		);
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'initialize' ), 0 );
		add_action( 'plugins_loaded', array( $this, 'load_translation' ) );
	}

	public function initialize() {
		if ( ! $this->is_woocommerce_valid() ) {
			return;
		}

		$this->includes();

		if ( ! is_admin() ) {
			return;
		}

		if ( wc_calypso_bridge_has_ecommerce_features() ) {
			add_action( 'init', array( $this, 'load_ecommerce_plan_ui' ), 2 );
		}
	}

	/**
	 * Include files and controllers.
	 */
	public function includes() {

		/**
		 * Hint:
		 * These files/controllers get included in all plans.
		 *
		 * Each controller will handle the plans and features in its own logic and constructor.
		 */
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-helper-functions.php';
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/class-wc-calypso-bridge-shared.php';
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-setup.php';
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-jetpack.php';
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-setup-tasks.php';
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-filters.php';
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-tracks.php';
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-events.php';
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-crowdsignal-redirect.php';
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-themes-setup.php';
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-woocommerce-admin-features.php';
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-hide-alerts.php';
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-plugins.php';
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-addons.php';
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-free-trial-payment-restrictions.php';
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/free-trial/class-wc-calypso-bridge-free-trial-expired-plan-redirects.php';
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/free-trial/class-wc-calypso-bridge-free-trial-hide-tasklist-tasks.php';
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-free-trial-payment-task.php';
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/free-trial/class-wc-calypso-bridge-free-trial-orders-changes.php';
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/free-trial/class-wc-calypso-bridge-free-trial-wc-payments.php';
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/free-trial/class-wc-calypso-bridge-free-trial-plan-picker-banner.php';
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/free-trial/class-wc-calypso-bridge-free-trial-orders-notice.php';
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-free-trial-store-details-task.php';
	}

	/**
	 * Load ecommerce plan UI changes.
	 */
	public function load_ecommerce_plan_ui() {

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
	 * Loads language files for the plugin.
	 *
	 * @since 2.0.0
	 */
	public function load_translation() {
		$plugin_path = WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/languages';
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
		return self::$plugin_asset_path ? self::$plugin_asset_path : self::MU_PLUGIN_ASSET_PATH;
	}

	/**
	 * Defensive helper function to return the current site slug in a way that will work
	 * when Automattic\Jetpack\Status hasn't been loaded by the Jetpack plugin.
	 *
	 * @since 2.0.8
	 *
	 * @return string
	 */
	public function get_site_slug() {
		// The Jetpack class should be auto-loaded if Jetpack has been loaded,
		// but we've seen fatals from cases where the class wasn't defined.
		// So let's make double-sure it exists before calling it.
		if ( class_exists( '\Automattic\Jetpack\Status' ) ) {
			$jetpack_status = new \Automattic\Jetpack\Status();

			return $jetpack_status->get_site_suffix();
		}

		// If the Jetpack Status class doesn't exist, fall back on site_url()
		// with any trailing '/' characters removed.
		$site_url = untrailingslashit( site_url( '/', 'https' ) );

		// Remove the leading 'https://' and replace any remaining `/` characters with
		return str_replace( '/', '::', substr( $site_url, 8 ) );
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
	return WC_Calypso_Bridge::get_instance();
}

WC_Calypso_Bridge_Instance();
