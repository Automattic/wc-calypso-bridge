<?php
/**
 * Modifies bundled plugins
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 2.2.20
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
		'tiktok-for-woocommerce/tiktok-for-woocommerce.php',
		'woocommerce-pinterest/pinterest-for-woocommerce.php',
		'woocommerce-brands/woocommerce-brands.php',
		'woocommerce-back-in-stock-notifications/woocommerce-back-in-stock-notifications.php',
		'woocommerce-eu-vat-number/woocommerce-eu-vat-number.php',
		'woocommerce-shipment-tracking/woocommerce-shipment-tracking.php',
		'crowdsignal-forms/crowdsignal-forms.php',
		'polldaddy/polldaddy.php',
		'woocommerce-product-recommendations/woocommerce-product-recommendations.php',
		'automatewoo/automatewoo.php',
		'woocommerce-shipping-fedex/woocommerce-shipping-fedex.php',
		'woocommerce-avatax/woocommerce-avatax.php',
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
	 * Constructor.
	 */
	private function __construct() {

		// Only in Ecommerce.
		if ( ! wc_calypso_bridge_has_ecommerce_features() ) {
			return;
		}

		add_action( 'init', array( $this, 'init' ), 2 );
	}

	/**
	 * Initialize.
	 */
	public function init() {

		add_filter( 'plugin_action_links', array( $this, 'remove_woocommerce_deactivation_link' ), 10, 2 );
		add_filter( 'plugin_action_links', array( $this, 'remove_ecommerce_managed_plugin_delete_link' ), PHP_INT_MAX, 2 );
		add_action( 'update_option_active_plugins', array( $this, 'prevent_woocommerce_deactivation' ), 10, 2 );
		add_action( 'current_screen', array( $this, 'prevent_woocommerce_deactivation_route' ), 10, 2 );
		add_action( 'admin_notices', array( $this, 'prevent_woocommerce_deactivation_notice' ), 10, 2 );
		add_filter( 'manage_product_posts_columns', array( $this, 'remove_jetpack_stats_column' ), 100 );
		add_filter( 'default_hidden_columns', array( $this, 'hide_product_columns' ), 100, 2 );
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
				<p><?php esc_html_e( 'WooCommerce cannot be deactivated on this plan.', 'wc-calypso-bridge' ); ?></p>
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
	 * @param array  $actions     Plugin actions.
	 * @param string $plugin_file Plugin file.
	 */
	public function remove_ecommerce_managed_plugin_delete_link( $actions, $plugin_file ) {

		if ( in_array( $plugin_file, self::WPCOM_ECOMMERCE_PLUGINS, true ) ) {
			unset( $actions['delete'] );
		}

		return $actions;
	}

	/**
	 * Removes the Stats column.
	 *
	 * @since  1.9.5
	 *
	 * @param array $cols Array of product columns.
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
			return array_merge( $hidden, array( 'likes', 'date', 'taxonomy-product_brand' ) );
		}

		return $hidden;
	}

}

WC_Calypso_Bridge_Plugins::get_instance();
