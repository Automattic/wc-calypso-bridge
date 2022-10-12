<?php
/**
 * Modifies bundled plugins
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 1.9.6
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge Plugins
 */
class WC_Calypso_Bridge_Plugins {

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
		add_action( 'update_option_active_plugins', array( $this, 'prevent_woocommerce_deactivation' ), 10, 2 );
		add_action( 'current_screen', array( $this, 'prevent_woocommerce_deactivation_route' ), 10, 2 );
		add_action( 'admin_notices', array( $this, 'prevent_woocommerce_deactivation_notice' ), 10, 2 );
		add_action( 'woocommerce_installed', array( $this, 'maybe_create_wc_pages' ), 100 );
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
	 * Check WooCommerce pages (shop, cart, my-account, checkout) and create them if the following conditions are met.
	 *
	 * 1. This is the first time running this method.
	 * 2. Shop, cart, my-account, and checkout pages do not exist.
	 *
	 * @return void
	 */
	public function maybe_create_wc_pages() {
		global $wpdb;

		$option_name = 'wc_pages_created_by_wc_calypso_bridge';

		// Abort if we have attempted to create the pages already.
		if ( 'yes' === get_option( $option_name, 'no' ) ) {
			return;
		}

		$post_count = (int) $wpdb->get_var( "select count(*) from $wpdb->posts where post_name in ('shop', 'cart', 'my-account', 'checkout')" );

		// Abort if we don't find all the pages.
		if ( 4 === $post_count ) {
			return;
		}

		// Reset the woocommerce_*_page_id options.
		// This is needed as woocommerce_*_page_id options have incorrect values on a fresh installation
		// for an ecomm plan. WC_Install:create_pages() might not create all the
		// required pages without resetting these options first.
		foreach ( [ 'shop', 'cart', 'myaccount', 'checkout' ] as $page ) {
			delete_option( "woocommerce_{$page}_page_id" );
		}

		do_action( 'wc_calypso_bridge_maybe_create_pages' );
		WC_Install::create_pages();
		update_option( $option_name, 'yes' );
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
