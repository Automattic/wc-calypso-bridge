<?php
/**
 * Load the bridge if enabled
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Loader;
use Automattic\WooCommerce\Admin\WCAdminHelper;
use Automattic\WooCommerce\Admin\Features\OnboardingTasks;
use Automattic\Jetpack\Connection\Client;

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
	 * Plugin asset path
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
	 * Constructor.
	 */
	public function __construct() {
		$this->includes();

		/**
		 * Handle the store launching AJAX endpoint.
		 *
		 * @since   1.9.12
		 */
		add_action( 'wp_ajax_launch_store', function() {

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( new WP_Error( 'unauthorized', __( 'You don\'t have permissions to launch this site', 'wc-calypso-bridge' ) ), 400 );
				return;
			}

			if ( 'launched' === get_option( 'launch-status' ) ) {
				wp_send_json_error( new WP_Error( 'already-launched', __( 'This site has already been launched', 'wc-calypso-bridge' ) ), 400 );
				return;
			}

			$blog_id  = \Jetpack_Options::get_option( 'id' );
			$response = Client::wpcom_json_api_request_as_user(
				sprintf( '/sites/%d/launch', $blog_id ),
				'2',
				[ 'method' => 'POST' ],
				json_encode( [
					'site' => $blog_id
				] ),
				'wpcom'
			);

			// Handle error.
			if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
				$body  = wp_remote_retrieve_body( $response );
				$error = json_decode( $body, true );
				wp_send_json_error( new WP_Error( $error[ 'code' ], $error[ 'message' ] ), 400 );
			}

			$body   = wp_remote_retrieve_body( $response );
			$status = json_decode( $body );
			wp_send_json( $status );
		} );

		/**
		 * Enable DB auto updates.
		 *
		 * @since   1.9.13
		 *
		 * @return  bool
		 */
		add_filter( 'woocommerce_enable_auto_update_db', '__return_true' );

		/**
		 * Remove the legacy `WooCommerce > Coupons` menu.
		 *
		 * @since   1.9.4
		 *
		 * @param mixed $pre Fixed to false.
		 * @return int 1 to show the legacy menu, 0 to hide it. Booleans do not work.
		 * @see     Automattic\WooCommerce\Internal\Admin\CouponsMovedTrait::display_legacy_menu()
		 * @todo    Write a compatibility branch in CouponsMovedTrait to hide the legacy menu in new installations of WooCommerce.
		 * @todo    Remove this filter when the compatibility branch is merged.
		 */
		add_filter( 'pre_option_wc_admin_show_legacy_coupon_menu', static function ( $pre ) {
			return 0;
		}, PHP_INT_MAX );

		/**
		 * Disable WooCommerce Navigation.
		 *
		 * @since   1.9.4
		 *
		 * @param mixed $pre Fixed to false.
		 * @return string no.
		 * @todo    Refactor and move it.
		 */
		add_filter( 'pre_option_woocommerce_navigation_enabled', static function ( $pre ) {
			return 'no';
		}, PHP_INT_MAX );

		/**
		 * Remove the Write button from the global bar.
		 *
		 * @since   1.9.8
		 *
		 * @return void
		 */
		add_action( 'wp_before_admin_bar_render', static function () {
			global $wp_admin_bar;

			if ( ! is_object( $wp_admin_bar ) ) {
				return;
			}

			$wp_admin_bar->remove_menu( 'ab-new-post' );
		}, PHP_INT_MAX );
    
		// Include these classes as early as possible.
		include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-helper-functions.php';
		include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-jetpack.php';
		include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-setup.php';

		/**
		 * Introduce the "Add a domain" and "Launch your store" setup tasks.
		 *
		 * @since   1.9.12
		 */
		add_action( 'init', function() {

			if ( ! class_exists( '\Automattic\WooCommerce\Admin\Features\OnboardingTasks\TaskLists' ) ) {
				return;
			}

			/**
			 * `ecommerce_custom_setup_tasks_enabled` filter.
			 *
			 * This filter is used to remove the "add a domain" and "launch your store" tasks from ecommerce plans.
			 *
			 * @since 1.9.12
			 *
			 * @param  bool $status_enabled
			 * @return bool
			 */
			if ( ! (bool) apply_filters( 'ecommerce_custom_setup_tasks_enabled', true ) ) {
				return;
			}

			$tl = \Automattic\WooCommerce\Admin\Features\OnboardingTasks\TaskLists::instance();
			require_once __DIR__ . '/includes/tasks/class-wc-calypso-task-add-domain.php';
			require_once __DIR__ . '/includes/tasks/class-wc-calypso-task-launch-site.php';

			$list = $tl::get_lists_by_ids( array( 'setup' ) );
			$list = array_pop( $list );

			$add_domain_task  = new \Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks\AddDomain( $list );
			$launch_site_task = new \Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks\LaunchSite( $list );
			$tl::add_task( 'setup', $add_domain_task );
			$tl::add_task( 'setup', $launch_site_task );

		}, PHP_INT_MAX );

		if ( ! is_admin() && ! defined( 'DOING_CRON' ) ) {
			return;
		}

		add_action( 'plugins_loaded', array( $this, 'initialize' ), 2 );
		add_action( 'plugins_loaded', array( $this, 'disable_powerpack_features' ), 2 );
	}

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
	 * Initialize only if WC is present.
	 */
	public function initialize() {
		add_action( 'init', array( $this, 'load_ecommerce_plan_ui' ), 2 );
		add_action( 'plugins_loaded', array( $this, 'load_transalation' ) );
	}

	/**
	 * Include files and controllers.
	 */
	public function includes() {
		require_once WC_CALYSPO_BRIDGE_PLUGIN_PATH . '/class-wc-calypso-bridge-shared.php';
		require_once WC_CALYSPO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-filters.php';
		require_once WC_CALYSPO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-tracks.php';
		require_once WC_CALYSPO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-events.php';
		require_once WC_CALYSPO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-crowdsignal-redirect.php';
		require_once WC_CALYSPO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-themes-setup.php';
		require_once WC_CALYSPO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-woocommerce-admin-features.php';
		include_once WC_CALYSPO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-hide-alerts.php';
	}

	/**
	 * Disables Specific Features within the Powerpack extension for Storefront.
	 */
	public function disable_powerpack_features() {
		if ( ! class_exists( 'Storefront_Powerpack' ) ) {
			return;
		}

		/**
		 * List of Powerpack features able to disable
		 *
		 * 'storefront_powerpack_helpers_enabled'
		 * 'storefront_powerpack_admin_enabled'
		 * 'storefront_powerpack_frontend_enabled'
		 * 'storefront_powerpack_customizer_enabled'
		 * 'storefront_powerpack_header_enabled'
		 * 'storefront_powerpack_footer_enabled'
		 * 'storefront_powerpack_designer_enabled'
		 * 'storefront_powerpack_layout_enabled'
		 * 'storefront_powerpack_integrations_enabled'
		 * 'storefront_powerpack_mega_menus_enabled'
		 * 'storefront_powerpack_parallax_hero_enabled'
		 * 'storefront_powerpack_checkout_enabled'
		 * 'storefront_powerpack_homepage_enabled'
		 * 'storefront_powerpack_messages_enabled'
		 * 'storefront_powerpack_product_details_enabled'
		 * 'storefront_powerpack_shop_enabled'
		 * 'storefront_powerpack_pricing_tables_enabled'
		 * 'storefront_powerpack_reviews_enabled'
		 * 'storefront_powerpack_product_hero_enabled'
		 * 'storefront_powerpack_blog_customizer_enabled'
		 */
		$disabled_powerpack_features = array(
			'storefront_powerpack_designer_enabled',
			'storefront_powerpack_mega_menus_enabled',
			'storefront_powerpack_pricing_tables_enabled',
		);

		foreach ( $disabled_powerpack_features as $feature_filter_name ) {
			add_filter( $feature_filter_name, '__return_false' );
		}
	}

	/**
	 * Load ecommere plan specific UI changes.
	 */
	public function load_ecommerce_plan_ui() {
		// We always want the Calypso branded OBW to run on eCommerce plan sites.
		include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-page-controller.php';
		include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-plugins.php';
		include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-addons.php';
		include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-addons-screen.php';
		include_once dirname( __FILE__ ) . '/includes/gutenberg.php';

		// Shared with store-on-wpcom.
		include_once dirname( __FILE__ ) . '/store-on-wpcom/inc/wc-calypso-bridge-mailchimp-no-redirect.php';

		// @todo This should rely on the navigation screens instead.
		$connect_files = glob( dirname( __FILE__ ) . '/includes/connect/*.php' );
		foreach ( $connect_files as $connect_file ) {
			include_once $connect_file;
		}

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

		add_action( 'current_screen', array( $this, 'load_ui_elements' ) );
	}

	/**
	 * Updates required UI elements for calypso bridge pages only.
	 */
	public function load_ui_elements() {

		if ( is_wc_calypso_bridge_page() ) {
			add_action( 'admin_init', array( $this, 'remove_woocommerce_core_footer_text' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'add_ecommerce_plan_styles' ) );
		}
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
	 * Remove WooCommerce footer text
	 */
	public function remove_woocommerce_core_footer_text() {
		add_filter( 'woocommerce_display_admin_footer_text', '__return_false' );
	}

	/**
	 * Loads language files for the plugin.
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
		return $this->$plugin_asset_path ? $this->$plugin_asset_path : self::MU_PLUGIN_ASSET_PATH;
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
