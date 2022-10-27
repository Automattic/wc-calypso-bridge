<?php
/**
 * Modifies bundled plugins
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 1.9.8
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge Plugins
 */
class WC_Calypso_Bridge_Plugins {

	/**
	 * Managed plugins specific to the ecommerce plan.
	 *
	 * @since 1.9.8
	 */
	const WPCOM_ECOMMERCE_PLUGINS = array(
		'woocommerce/woocommerce.php',
		'facebook-for-woocommerce/facebook-for-woocommerce.php',
		'woocommerce-services/woocommerce-services.php',
		'woocommerce-product-addons/woocommerce-product-addons.php',
		'woocommerce-product-bundles/woocommerce-product-bundles.php',
		'woocommerce-gift-cards/woocommerce-gift-cards.php',
		'woocommerce-min-max-quantities/woocommerce-min-max-quantities.php',
		'woocommerce-google-analytics-integration/woocommerce-google-analytics-integration.php',
		'google-listings-and-ads/google-listings-and-ads.php',
		'taxjar-simplified-taxes-for-woocommerce/taxjar-woocommerce.php',
		'woocommerce-payments/woocommerce-payments.php',
		'woocommerce-shipping-australia-post/woocommerce-shipping-australia-post.php',
		'woocommerce-shipping-canada-post/woocommerce-shipping-canada-post.php',
		'woocommerce-shipping-royalmail/woocommerce-shipping-royalmail.php',
		'woocommerce-shipping-ups/woocommerce-shipping-ups.php',
		'woocommerce-shipping-usps/woocommerce-shipping-usps.php',
	);

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Bridge_Plugins instance
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
	 * Constructor
	 */
	private function __construct() {
		add_filter( 'plugin_action_links', array( $this, 'remove_woocommerce_deactivation_link' ), 10, 2 );
		add_filter( 'plugin_action_links', array( $this, 'remove_ecommerce_managed_plugin_delete_link' ), PHP_INT_MAX, 2 );
		add_action( 'update_option_active_plugins', array( $this, 'prevent_woocommerce_deactivation' ), 10, 2 );
		add_action( 'current_screen', array( $this, 'prevent_woocommerce_deactivation_route' ), 10, 2 );
		add_action( 'admin_notices', array( $this, 'prevent_woocommerce_deactivation_notice' ), 10, 2 );
		add_filter( 'woocommerce_admin_onboarding_industries', array( $this, 'maybe_create_wc_pages' ), 10, 2 );
		add_filter( 'manage_product_posts_columns', array( $this, 'remove_jetpack_stats_column' ), 100 );
		add_filter( 'default_hidden_columns', array( $this, 'hide_product_columns' ), 100, 2 );
	}

	/**
	 * Prevents the WooCommerce plugin from being deactivated by plugins
	 * or code other than traditional deactivation routes
	 *
	 * We don't use register_deactivation_hook here since it fires too early and
	 * the options would still remove the plugin.
	 *
	 * @param array $old_value List of old values.
	 * @param array $value     List of new values.
	 */
	public function prevent_woocommerce_deactivation( $old_value, $value ) {
		if ( ! in_array( 'woocommerce/woocommerce.php', $value, true ) ) {
			activate_plugin( 'woocommerce/woocommerce.php' );
		}
	}

	/**
	 * Prevents the WooCommerce plugin from being deactivated by direct URL
	 */
	public function prevent_woocommerce_deactivation_route() {
		$screen = get_current_screen();
		if ( 'plugins' === $screen->base
			&& isset( $_GET['action'] ) // WPCS: CSRF ok.
			&& isset( $_GET['plugin'] ) // WPCS: CSRF ok.
			&& 'deactivate' === $_GET['action'] // WPCS: CSRF ok.
			&& 'woocommerce/woocommerce.php' === $_GET['plugin'] // WPCS: CSRF ok.
		) {
			wp_safe_redirect( admin_url( 'plugins.php?prevent_wc_deactivation=1' ) );
			exit;
		}
	}

	/**
	 * Prevents the WooCommerce plugin from being deactivated by direct URL
	 */
	public function prevent_woocommerce_deactivation_notice() {
		if ( isset( $_GET['prevent_wc_deactivation'] ) ) { // WPCS: CSRF ok, input var ok, sanitization ok.
			?>
			<div class="notice notice-error is-dismissible">
				<p><?php esc_html_e( 'WooCommerce can\'t be deactivated on the eCommerce plan.', 'wc-calypso-bridge' ); ?></p>
			</div>
			<?php
		}
	}

	/**
	 * Remove WooCommerce deactivation links from plugins page
	 *
	 * @param array  $actions     Plugin actions.
	 * @param string $plugin_file Plugin file.
	 */
	public function remove_woocommerce_deactivation_link( $actions, $plugin_file ) {
		if ( 'woocommerce/woocommerce.php' === $plugin_file ) {
			unset( $actions['deactivate'] );
		}

		return $actions;
	}

	/**
	 * Remove WooCommerce delete links from plugins page.
	 *
	 * @since 1.9.8
	 *
	 * @param string $plugin_file Plugin file.
	 * @param array  $actions     Plugin actions.
	 */
	public function remove_ecommerce_managed_plugin_delete_link( $actions, $plugin_file ) {

		if ( in_array( $plugin_file, self::WPCOM_ECOMMERCE_PLUGINS, true ) ) {
			unset( $actions['delete'] );
		}

		return $actions;
	}

	/**
	 * Check WooCommerce pages (shop, cart, my-account, checkout) and create them if the following conditions are met.
	 *
	 * 1. This is the first time running this method.
	 * 2. User has not finished Store details task.
	 * 3. Shop, cart, my-account, and checkout pages do not exist.
	 *
	 * @param array $industries Array of industries.
	 *
	 * @return array
	 */
	public function maybe_create_wc_pages( $industries ) {
		global $wpdb;

		$option_name = 'wc_pages_created_by_wc_calypso_bridge';

		// Abort if we have attempted to create the pages already.
		if ( 'yes' === get_option( $option_name, 'no' ) ) {
			return $industries;
		}

		// Abort if the user has completed store details task already.
		$completed_tasks = get_option( 'woocommerce_task_list_tracked_completed_tasks', [] );
		if ( in_array( 'store_details', $completed_tasks ) ) {
			return $industries;
		}

		$post_count = $wpdb->get_var( "select count(*) from $wpdb->posts where post_name in ('shop', 'cart', 'my-account', 'checkout')" );

		// Abort if we find any existing pages.
		if ( 0 !== (int) $post_count ) {
			return $industries;
		}

		// Reset the woocommerce_*_page_id options.
		// This is needed as woocommerce_*_page_id options have incorrect values on a fresh installation
		// for an ecom plan. WC_Install:create_pages() might not create all the
		// required pages without resetting these options first.
		foreach ( [ 'shop', 'cart', 'myaccount', 'checkout' ] as $page ) {
			delete_option( "woocommerce_{$page}_page_id" );
		}

		WC_Install::create_pages();
		update_option( $option_name, 'yes' );

		return $industries;
	}

	/**
	 * Removes the Stats column.
	 *
	 * @since  1.9.5
	 *
	 * @param  array $cols Array of product columns.
	 * @return array
	 */
	public function remove_jetpack_stats_column( $cols ) {
		return array_diff_key( $cols, array_flip( array( 'stats' ) ) );
	}

	/**
	 * Hides the Likes and Date product columns by default.
	 *
	 * @since   1.9.5
	 *
	 * @param array  $hidden Current hidden columns.
	 * @param object $screen Current screen.
	 * @return array
	 */
	public function hide_product_columns( $hidden, $screen ) {
		if ( isset( $screen->id ) && 'edit-product' === $screen->id ) {
			return array_merge( $hidden, array( 'likes', 'date' ) );
		}

		return $hidden;
	}

}

$wc_calypso_bridge_plugins = WC_Calypso_Bridge_Plugins::get_instance();
