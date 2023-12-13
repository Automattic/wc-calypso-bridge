<?php
/**
 * Adds Customize Store task related functionalities
 *
 * @package WC_Calypso_Bridge/Classes
 * @since  1.0.0
 * @version x.x.x
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge Customize Store
 */
class WC_Calypso_Bridge_Customize_Store {

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Bridge_Customize_Store instance
	 */
	protected static $instance = false;

	/**
	 * Get class instance
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_filter( 'woocommerce_admin_features', array( 'WC_Calypso_Bridge_Customize_Store', 'possibly_enable_cys' ) );

		add_action( 'plugins_loaded', function() {
			if ( self::is_enabled() ) {
				add_action( 'load-site-editor.php', array( $this, 'mark_customize_store_task_as_completed_on_site_editor' ) );
			}
		});

		// wpcom.editor.js conflicts with CYS scripts due to double registration of the private-apis
		// dequeue it on CYS pages.
		add_action( 'admin_print_scripts', function() {
			if ( isset( $_GET['path'] ) && str_contains( wp_unslash( $_GET['path'] ), '/customize-store/' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				wp_dequeue_script( 'wpcom-block-editor-wpcom-editor-script' );
			}
		}, 9999);

		add_action( 'wp_head', array( $this, 'possibly_remove_wpcom_ui_elements' ) );

		add_action( 'wp_head', array( $this, 'possibly_add_track_homepage_view' ) );
	}

	/**
	 * Mark Customize Store task as completed on Site Editor by checking $_GET['from'] value.
	 * The value is set from WP-Calypso.
	 *
	 * @since 2.2.14
	 *
	 * @return void
	 */
	public function mark_customize_store_task_as_completed_on_site_editor() {
		if ( isset( $_GET['from'] ) && $_GET['from'] === 'theme-info' ) {
			update_option( 'woocommerce_admin_customize_store_completed', 'yes' );
		}
	}

	/**
	 * Runs script and add styles to remove WPCOM elements such as admin bar, proxy banner, gift banner, store notice
	 * and hide scrollbar when users are viewing with ?cys-hide-admin-bar=true.
	 *
	 * @since 2.2.24
	 *
	 * @return void
	 */
	public function possibly_remove_wpcom_ui_elements() {
		if ( isset( $_GET['cys-hide-admin-bar'] ) ) {
			echo '
			<style type="text/css">
				#wpadminbar,
				#wpcom-gifting-banner,
				#wpcom-launch-banner-wrapper,
				#atomic-proxy-bar { display: none !important; }
				.woocommerce-store-notice { display: none !important; }
				html { margin-top: 0 !important; }
				body { overflow: hidden; }
			</style>';
			echo '
			<script type="text/javascript">
			( function() {
				document.addEventListener( "DOMContentLoaded", function() {
					document.documentElement.style.setProperty( "margin-top", "0px", "important" );
				} );
			} )();
			</script>';
		}
	}

	/**
	 * Add track homepage view event when admin user is viewing homepage.
	 */
	public function possibly_add_track_homepage_view() {
		if ( self::is_admin() ) {
			if ( is_front_page() || is_home() ) {
				// This is tracked via backend.
				WC_Tracks::record_event( 'store_homepage_view' );
			}
		}
	}

	/**
	 * Possibly enable Customize Store feature when feature flag is not already enabled
	 * and experiment is treatment.
	 * Criteria:
	 *  1. Admin user
	 *  2. Install date >= 2023-12-21 00:00:00 UTC
	 */
	public static function possibly_enable_cys( $features ) {
		// When the feature is already enabled, return early since it's likely to be internal testing.
		if ( isset( $features['customize-store'] ) ) {
			return $features;
		}

		$timestamp = get_option( 'woocommerce_admin_install_timestamp', false );

		// Eligiblity checks.
		if ( self::is_admin() && $timestamp && $timestamp >= 1703116800 ) {
			if ( class_exists( '\WooCommerce\Admin\Experimental_Abtest' ) && \WooCommerce\Admin\Experimental_Abtest::in_treatment( 'woocommerce_wooexpress_cys_launch_v1', true ) ) {
				if ( ! in_array( 'customize-store', $features, true ) ) {
					$features[] = 'customize-store';
				}
			}
		}

		return $features;
	}

	/**
	 * Check if Customize Store feature is enabled.
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		return class_exists( '\Automattic\WooCommerce\Admin\Features\Features' ) && \Automattic\WooCommerce\Admin\Features\Features::is_enabled( 'customize-store' );
	}

	/**
	 * Check if current user is admin.
	 *
	 * @return bool
	 */
	public static function is_admin() {
		return wc_current_user_has_role( 'administrator' );
	}
}

WC_Calypso_Bridge_Customize_Store::get_instance();
