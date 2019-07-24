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
		add_action( 'init', array( $this, 'check_calypsoify_param' ), 1 );
		add_action( 'init', array( $this, 'check_setup_param' ) );
		add_action( 'init', array( $this, 'possibly_load_calypsoify' ), 2 );
		add_action( 'plugins_loaded', array( $this, 'disable_powerpack_features' ), 2 );
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
	 * Check for calypsoify param in URL
	 *
	 * We use our own check since Jetpack's does not load fast enough and
	 * only hooks on admin_init which won't be run by wc-setup
	 */
	public function check_calypsoify_param() {
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

		// TODO Add composer.json to GridIcons, and pull this in via wpcomsh instead.
		if ( ! function_exists( 'get_gridicon' ) ) {
			include_once dirname( __FILE__ ) . '/includes/gridicons.php';
		}

		// We always want the Calypso branded OBW to run on eCommerce plan sites.
		include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-admin-setup-checklist.php';
		include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-setup.php';

		if ( $this->dependencies_satisfied() ) {
			include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-helper-functions.php';
			include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-hide-alerts.php';
			include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-themes-setup.php';
			include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-page-controller.php';
			include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-menus.php';
			include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-plugins.php';
			include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-addons.php';
			include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-addons-screen.php';
			include_once dirname( __FILE__ ) . '/includes/gutenberg.php';

			// Shared with store-on-wpcom.
			include_once dirname( __FILE__ ) . '/store-on-wpcom/inc/wc-calypso-bridge-mailchimp-no-redirect.php';

			$connect_files = glob( dirname( __FILE__ ) . '/includes/connect/*.php' );
			foreach ( $connect_files as $connect_file ) {
				include_once $connect_file;
			}

			add_action( 'current_screen', array( $this, 'load_ui_elements' ) );
		}
	}

	/**
	 * Check if dependencies are met to load Calypsoify
	 *
	 * @return bool
	 */
	public function dependencies_satisfied() {
		if ( 1 !== (int) get_user_meta( get_current_user_id(), 'calypsoify', true ) ) {
			return false;
		}
		if (
			! class_exists( 'woocommerce' ) ||
			version_compare(
				get_option( 'woocommerce_db_version' ),
				WC_MIN_VERSION,
				'<'
			)
		) {
			return false;
		}
		if ( ! class_exists( 'Jetpack' ) || ! class_exists( 'Jetpack_Calypsoify' ) ) {
			return false;
		}
		if (
			! Jetpack::is_active()
			&& ! Jetpack::is_development_mode()
			&& ! Jetpack::is_onboarding()
			&& (
				! is_multisite()
				|| ! get_site_option( 'jetpack_protect_active' )
			)
		) {
			return false;
		}
		return true;
	}

	/**
	 * Updates required UI elements for calypso bridge pages only.
	 */
	public function load_ui_elements() {
		if ( is_wc_calypso_bridge_page() || ( isset( $_GET['page'] ) && 'wc-setup' === $_GET['page'] ) ) {
			add_action( 'admin_print_styles', array( $this, 'enqueue_calypsoify_scripts' ), 11 );

			include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-breadcrumbs.php';
			include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-pagination.php';
			include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-taxonomies.php';
			include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-action-header.php';
			include_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-tables.php';

			add_action( 'admin_init', array( $this, 'remove_woocommerce_core_footer_text' ) );
			add_filter( 'admin_footer_text', array( $this, 'update_woocommerce_footer' ) );
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

		$icons = array(
			'checkmark'   => get_gridicon( 'gridicons-checkmark' ),
			'chevronDown' => get_gridicon( 'gridicons-chevron-down' ),
			'cross'       => get_gridicon( 'gridicons-cross' ),
			'info'        => get_gridicon( 'gridicons-info' ),
			'notice'      => get_gridicon( 'gridicons-notice' ),
			'search'      => get_gridicon( 'gridicons-search' ),
		);
		wp_localize_script(
			'wc-calypso-bridge-calypsoify',
			'icons',
			$icons
		);

		$translations = array(
			'openSearch'      => __( 'Open Search', 'wc-calypso-bridge' ),
			'closeSearch'     => __( 'Close Search', 'wc-calypso-bridge' ),
			'cancel'          => __( 'Cancel', 'wc-calypso-bridge' ),
			'taxonomySuccess' => __( '"{name}" was successfully added.', 'wc-calypso-bridge' ),
		);
		wp_localize_script( 'wc-calypso-bridge-calypsoify', 'translations', $translations );

	}

	/**
	 * Remove WooCommerce footer text
	 */
	public function remove_woocommerce_core_footer_text() {
		add_filter( 'woocommerce_display_admin_footer_text', '__return_false' );
	}

	/**
	 * Adds in our own updates WooCommece branding.
	 */
	public function update_woocommerce_footer() {
		$svg = '<svg className="woocommerce-logo" height="32" width="120" viewBox="0 0 723 146" version="1.1">
			<g id="Page-1" stroke="none" strokeWidth="1" fill="none" fillRule="evenodd">
				<g id="woocommerce_logo" transform="translate(-1.000000, 0.000000)">
					<path
						d="M23.7,0.2 L222.8,0.2 C235.4,0.2 245.6,10.4 245.6,23 L245.6,99 C245.6,111.6 235.4,121.8 222.8,121.8 L151.4,121.8 L161.2,145.8 L118.1,121.8 L23.8,121.8 C11.2,121.8 1,111.6 1,99 L1,23 C0.9,10.5 11.1,0.2 23.7,0.2 Z"
						id="Shape"
						fill="#527994"
					/>
					<path
						d="M13.2,20.9 C14.6,19 16.7,18 19.5,17.8 C24.6,17.4 27.5,19.8 28.2,25 C31.3,45.9 34.7,63.6 38.3,78.1 L60.2,36.4 C62.2,32.6 64.7,30.6 67.7,30.4 C72.1,30.1 74.8,32.9 75.9,38.8 C78.4,52.1 81.6,63.4 85.4,73 C88,47.6 92.4,29.3 98.6,18 C100.1,15.2 102.3,13.8 105.2,13.6 C107.5,13.4 109.6,14.1 111.5,15.6 C113.4,17.1 114.4,19 114.6,21.3 C114.7,23.1 114.4,24.6 113.6,26.1 C109.7,33.3 106.5,45.4 103.9,62.2 C101.4,78.5 100.5,91.2 101.1,100.3 C101.3,102.8 100.9,105 99.9,106.9 C98.7,109.1 96.9,110.3 94.6,110.5 C92,110.7 89.3,109.5 86.7,106.8 C77.4,97.3 70,83.1 64.6,64.2 C58.1,77 53.3,86.6 50.2,93 C44.3,104.3 39.3,110.1 35.1,110.4 C32.4,110.6 30.1,108.3 28.1,103.5 C23,90.4 17.5,65.1 11.6,27.6 C11.3,25 11.8,22.7 13.2,20.9 Z"
						id="Shape"
						fill="#FFFFFF"
						fillRule="nonzero"
					/>
					<path
						d="M228.2,36.6 C224.6,30.3 219.3,26.5 212.2,25 C210.3,24.6 208.5,24.4 206.8,24.4 C197.2,24.4 189.4,29.4 183.3,39.4 C178.1,47.9 175.5,57.3 175.5,67.6 C175.5,75.3 177.1,81.9 180.3,87.4 C183.9,93.7 189.2,97.5 196.3,99 C198.2,99.4 200,99.6 201.7,99.6 C211.4,99.6 219.2,94.6 225.2,84.6 C230.4,76 233,66.6 233,56.3 C233,48.5 231.4,42 228.2,36.6 Z M215.6,64.3 C214.2,70.9 211.7,75.8 208,79.1 C205.1,81.7 202.4,82.8 199.9,82.3 C197.5,81.8 195.5,79.7 194,75.8 C192.8,72.7 192.2,69.6 192.2,66.7 C192.2,64.2 192.4,61.7 192.9,59.4 C193.8,55.3 195.5,51.3 198.2,47.5 C201.5,42.6 205,40.6 208.6,41.3 C211,41.8 213,43.9 214.5,47.8 C215.7,50.9 216.3,54 216.3,56.9 C216.3,59.5 216,62 215.6,64.3 Z"
						id="Shape"
						fill="#FFFFFF"
						fillRule="nonzero"
					/>
					<path
						d="M165.5,36.6 C161.9,30.3 156.5,26.5 149.5,25 C147.6,24.6 145.8,24.4 144.1,24.4 C134.5,24.4 126.7,29.4 120.6,39.4 C115.4,47.9 112.8,57.3 112.8,67.6 C112.8,75.3 114.4,81.9 117.6,87.4 C121.2,93.7 126.5,97.5 133.6,99 C135.5,99.4 137.3,99.6 139,99.6 C148.7,99.6 156.5,94.6 162.5,84.6 C167.7,76 170.3,66.6 170.3,56.3 C170.3,48.5 168.7,42 165.5,36.6 Z M152.9,64.3 C151.5,70.9 149,75.8 145.3,79.1 C142.4,81.7 139.7,82.8 137.2,82.3 C134.8,81.8 132.8,79.7 131.3,75.8 C130.1,72.7 129.5,69.6 129.5,66.7 C129.5,64.2 129.7,61.7 130.2,59.4 C131.1,55.3 132.8,51.3 135.5,47.5 C138.8,42.6 142.3,40.6 145.9,41.3 C148.3,41.8 150.3,43.9 151.8,47.8 C153,50.9 153.6,54 153.6,56.9 C153.6,59.5 153.4,62 152.9,64.3 Z"
						id="Shape"
						fill="#FFFFFF"
						fillRule="nonzero"
					/>
					<path
						d="M270.9,35.7 C264.2,42.3 260.9,50.7 260.9,60.9 C260.9,71.8 264.2,80.7 270.8,87.4 C277.4,94.1 286,97.5 296.7,97.5 C299.8,97.5 303.3,97 307.1,95.9 L307.1,79.7 C303.6,80.7 300.6,81.2 298,81.2 C292.7,81.2 288.5,79.4 285.3,75.9 C282.1,72.3 280.5,67.5 280.5,61.4 C280.5,55.7 282.1,51 285.2,47.4 C288.4,43.7 292.3,41.9 297.1,41.9 C300.2,41.9 303.5,42.4 307.1,43.4 L307.1,27.2 C303.8,26.3 300.1,25.9 296.2,25.9 C286,25.8 277.6,29.1 270.9,35.7 Z M340.3,25.8 C331.1,25.8 323.9,28.9 318.7,35 C313.5,41.1 311,49.7 311,60.7 C311,72.6 313.6,81.7 318.7,88 C323.8,94.3 331.3,97.5 341.1,97.5 C350.6,97.5 357.9,94.3 363,88 C368.1,81.7 370.7,72.8 370.7,61.4 C370.7,50 368.1,41.2 362.9,35 C357.6,28.9 350.1,25.8 340.3,25.8 Z M348.2,77.8 C346.4,80.6 343.7,82 340.3,82 C337.1,82 334.7,80.6 333,77.8 C331.3,75 330.5,69.4 330.5,60.9 C330.5,47.8 333.8,41.3 340.5,41.3 C347.5,41.3 351.1,47.9 351.1,61.2 C351,69.4 350,75 348.2,77.8 Z M420.1,27.7 L416.5,43 C415.6,46.9 414.7,50.9 413.9,55 L411.9,65.6 C410,55 407.4,42.4 404.1,27.7 L380.9,27.7 L372.2,95.8 L389.6,95.8 L394.3,48.9 L406.2,95.8 L418.6,95.8 L430,49 L434.9,95.8 L453.1,95.8 L443.9,27.7 L420.1,27.7 Z M503.4,27.7 L499.8,43 C498.9,46.9 498,50.9 497.2,55 L495.2,65.6 C493.3,55 490.7,42.4 487.4,27.7 L464.2,27.7 L455.5,95.8 L472.9,95.8 L477.6,48.9 L489.5,95.8 L501.9,95.8 L513.2,49 L518.1,95.8 L536.3,95.8 L527.1,27.7 L503.4,27.7 Z M560,68.9 L576.3,68.9 L576.3,54.8 L560,54.8 L560,42.3 L578.8,42.3 L578.8,27.8 L541.6,27.8 L541.6,95.9 L578.9,95.9 L578.9,81.4 L560,81.4 L560,68.9 Z M630.7,58.1 C632.6,55 633.6,51.8 633.6,48.5 C633.6,42.1 631.1,37 626.1,33.3 C621.1,29.6 614.2,27.7 605.6,27.7 L584.2,27.7 L584.2,95.8 L602.6,95.8 L602.6,64.8 L602.9,64.8 L617.8,95.8 L637.2,95.8 L622.5,65.1 C626,63.5 628.8,61.2 630.7,58.1 Z M602.5,57 L602.5,40.8 C606.9,40.9 610,41.6 611.9,43 C613.8,44.4 614.7,46.6 614.7,49.8 C614.7,54.5 610.6,56.9 602.5,57 Z M644.4,35.7 C637.7,42.3 634.4,50.7 634.4,60.9 C634.4,71.8 637.7,80.7 644.3,87.4 C650.9,94.1 659.5,97.5 670.2,97.5 C673.3,97.5 676.8,97 680.6,95.9 L680.6,79.7 C677.1,80.7 674.1,81.2 671.5,81.2 C666.2,81.2 662,79.4 658.8,75.9 C655.6,72.3 654,67.5 654,61.4 C654,55.7 655.6,51 658.7,47.4 C661.9,43.7 665.8,41.9 670.6,41.9 C673.7,41.9 677,42.4 680.6,43.4 L680.6,27.2 C677.3,26.3 673.6,25.9 669.7,25.9 C659.6,25.8 651.1,29.1 644.4,35.7 Z M704.1,81.2 L704.1,68.8 L720.4,68.8 L720.4,54.7 L704.1,54.7 L704.1,42.2 L723,42.2 L723,27.7 L685.8,27.7 L685.8,95.8 L723.1,95.8 L723.1,81.3 L704.1,81.3 L704.1,81.2 Z"
						id="Shape"
						fill="#527994"
						fillRule="nonzero"
					/>
				</g>
			</g>
		</svg>';

		$help_text = sprintf(
			// translators: "Need help? Read the {link to support document}eCommerce plan support document{/link to support document} or {link to help}get in touch with support{/link to help}.".
			__( 'Need help? Read the <a href="%1$s" class="wc-support-link" data-source="footer" target="_blank">eCommerce plan support document</a> or <a href="%2$s" class="wc-support-link" data-source="footer" target="_blank">get in touch with support</a>.', 'wc-calypso-bridge' ),
			'https://en.support.wordpress.com/ecommerce-plan/',
			'https://wordpress.com/help/contact'
		);

		// translators: "Powered by <WooCommerce Logo SVG>".
		echo '<div class="woocommerce-colophon"><span>' . sprintf( __( 'Powered by %s', 'wc-calypso-bridge' ), $svg ) . '</span></div>'; // WPCS: XSS ok.
		echo '<p class="woocommerce-help-text">' . $help_text . '</p>'; // WPCS: XSS ok.
	}

	/**
	 * Activates Calypsoify if the setup page is visited directly and it's not previously active.
	 */
	public function check_setup_param() {
		if ( current_user_can( 'manage_woocommerce' )
			&& isset( $_GET['page'] ) // WPCS: CSRF ok.
			&& 'wc-setup-checklist' === $_GET['page'] // WPCS: CSRF ok.
		) {
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
