<?php
/**
 * Modifies bundled plugins
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 1.9.5
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
		add_filter( 'woocommerce_create_pages', array( $this, 'add_blocks_to_cart_checkout_pages' ), 100, 2 );
		add_action( 'load-index.php', array( $this, 'maybe_remove_somewherewarm_maintenance_notices' ) );
		add_action( 'load-plugins.php', array( $this, 'maybe_remove_somewherewarm_maintenance_notices' ) );
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
	 * When WooCommerce creates the pages that it relies on,
	 * filter the pages to add the blocks to the cart and checkout pages.
	 *
	 * @since 1.9.5
	 *
	 * @param array $pages Array of pages to be created.
	 * @return array
	 */
	public function add_blocks_to_cart_checkout_pages( $pages ) {

		if ( isset( $pages['cart']['content'] ) ) {
			$pages['cart']['content'] = '<!-- wp:woocommerce/cart --><div class="wp-block-woocommerce-cart is-loading"><!-- wp:woocommerce/filled-cart-block --><div class="wp-block-woocommerce-filled-cart-block"><!-- wp:woocommerce/cart-items-block --><div class="wp-block-woocommerce-cart-items-block"><!-- wp:woocommerce/cart-line-items-block --><div class="wp-block-woocommerce-cart-line-items-block"></div><!-- /wp:woocommerce/cart-line-items-block --></div><!-- /wp:woocommerce/cart-items-block --><!-- wp:woocommerce/cart-totals-block --><div class="wp-block-woocommerce-cart-totals-block"><!-- wp:woocommerce/cart-order-summary-block --><div class="wp-block-woocommerce-cart-order-summary-block"></div><!-- /wp:woocommerce/cart-order-summary-block --><!-- wp:woocommerce/cart-express-payment-block --><div class="wp-block-woocommerce-cart-express-payment-block"></div><!-- /wp:woocommerce/cart-express-payment-block --><!-- wp:woocommerce/proceed-to-checkout-block --><div class="wp-block-woocommerce-proceed-to-checkout-block"></div><!-- /wp:woocommerce/proceed-to-checkout-block --><!-- wp:woocommerce/cart-accepted-payment-methods-block --><div class="wp-block-woocommerce-cart-accepted-payment-methods-block"></div><!-- /wp:woocommerce/cart-accepted-payment-methods-block --></div><!-- /wp:woocommerce/cart-totals-block --></div><!-- /wp:woocommerce/filled-cart-block --><!-- wp:woocommerce/empty-cart-block --><div class="wp-block-woocommerce-empty-cart-block"><!-- wp:image {"align":"center","sizeSlug":"small"} --><div class="wp-block-image"><figure class="aligncenter size-small"><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzgiIGhlaWdodD0iMzgiIHZpZXdCb3g9IjAgMCAzOCAzOCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTE5IDBDOC41MDQwMyAwIDAgOC41MDQwMyAwIDE5QzAgMjkuNDk2IDguNTA0MDMgMzggMTkgMzhDMjkuNDk2IDM4IDM4IDI5LjQ5NiAzOCAxOUMzOCA4LjUwNDAzIDI5LjQ5NiAwIDE5IDBaTTI1LjEyOSAxMi44NzFDMjYuNDg1MSAxMi44NzEgMjcuNTgwNiAxMy45NjY1IDI3LjU4MDYgMTUuMzIyNkMyNy41ODA2IDE2LjY3ODYgMjYuNDg1MSAxNy43NzQyIDI1LjEyOSAxNy43NzQyQzIzLjc3MyAxNy43NzQyIDIyLjY3NzQgMTYuNjc4NiAyMi42Nzc0IDE1LjMyMjZDMjIuNjc3NCAxMy45NjY1IDIzLjc3MyAxMi44NzEgMjUuMTI5IDEyLjg3MVpNMTEuNjQ1MiAzMS4yNTgxQzkuNjE0OTIgMzEuMjU4MSA3Ljk2Nzc0IDI5LjY0OTIgNy45Njc3NCAyNy42NTczQzcuOTY3NzQgMjYuMTI1IDEwLjE1MTIgMjMuMDI5OCAxMS4xNTQ4IDIxLjY5NjhDMTEuNCAyMS4zNjczIDExLjg5MDMgMjEuMzY3MyAxMi4xMzU1IDIxLjY5NjhDMTMuMTM5MSAyMy4wMjk4IDE1LjMyMjYgMjYuMTI1IDE1LjMyMjYgMjcuNjU3M0MxNS4zMjI2IDI5LjY0OTIgMTMuNjc1NCAzMS4yNTgxIDExLjY0NTIgMzEuMjU4MVpNMTIuODcxIDE3Ljc3NDJDMTEuNTE0OSAxNy43NzQyIDEwLjQxOTQgMTYuNjc4NiAxMC40MTk0IDE1LjMyMjZDMTAuNDE5NCAxMy45NjY1IDExLjUxNDkgMTIuODcxIDEyLjg3MSAxMi44NzFDMTQuMjI3IDEyLjg3MSAxNS4zMjI2IDEzLjk2NjUgMTUuMzIyNiAxNS4zMjI2QzE1LjMyMjYgMTYuNjc4NiAxNC4yMjcgMTcuNzc0MiAxMi44NzEgMTcuNzc0MlpNMjUuOTEwNSAyOS41ODc5QzI0LjE5NDQgMjcuNTM0NyAyMS42NzM4IDI2LjM1NDggMTkgMjYuMzU0OEMxNy4zNzU4IDI2LjM1NDggMTcuMzc1OCAyMy45MDMyIDE5IDIzLjkwMzJDMjIuNDAxNiAyMy45MDMyIDI1LjYxMTcgMjUuNDA0OCAyNy43ODc1IDI4LjAyNUMyOC44NDQ4IDI5LjI4MTUgMjYuOTI5NCAzMC44MjE0IDI1LjkxMDUgMjkuNTg3OVoiIGZpbGw9ImJsYWNrIi8+Cjwvc3ZnPgo=" alt=""/></figure></div><!-- /wp:image --><!-- wp:heading {"textAlign":"center","className":"wc-block-cart__empty-cart__title"} --><h2 class="has-text-align-center wc-block-cart__empty-cart__title">Your cart is currently empty!</h2><!-- /wp:heading --><!-- wp:paragraph {"align":"center"} --><p class="has-text-align-center"><a href="http://localhost:8889/shop/">Browse store</a>.</p><!-- /wp:paragraph --><!-- wp:separator {"className":"is-style-dots"} --><hr class="wp-block-separator is-style-dots"/><!-- /wp:separator --><!-- wp:heading {"textAlign":"center"} --><h2 class="has-text-align-center">New in store</h2><!-- /wp:heading --><!-- wp:woocommerce/product-new {"rows":1} /--></div><!-- /wp:woocommerce/empty-cart-block --></div><!-- /wp:woocommerce/cart -->';
		}

		if ( isset( $pages['checkout']['content'] ) ) {
			$pages['checkout']['content'] = '<!-- wp:woocommerce/checkout --><div class="wp-block-woocommerce-checkout wc-block-checkout is-loading"><!-- wp:woocommerce/checkout-fields-block --><div class="wp-block-woocommerce-checkout-fields-block"><!-- wp:woocommerce/checkout-express-payment-block --><div class="wp-block-woocommerce-checkout-express-payment-block"></div><!-- /wp:woocommerce/checkout-express-payment-block --><!-- wp:woocommerce/checkout-contact-information-block --><div class="wp-block-woocommerce-checkout-contact-information-block"></div><!-- /wp:woocommerce/checkout-contact-information-block --><!-- wp:woocommerce/checkout-shipping-address-block --><div class="wp-block-woocommerce-checkout-shipping-address-block"></div><!-- /wp:woocommerce/checkout-shipping-address-block --><!-- wp:woocommerce/checkout-billing-address-block --><div class="wp-block-woocommerce-checkout-billing-address-block"></div><!-- /wp:woocommerce/checkout-billing-address-block --><!-- wp:woocommerce/checkout-shipping-methods-block --><div class="wp-block-woocommerce-checkout-shipping-methods-block"></div><!-- /wp:woocommerce/checkout-shipping-methods-block --><!-- wp:woocommerce/checkout-payment-block --><div class="wp-block-woocommerce-checkout-payment-block"></div><!-- /wp:woocommerce/checkout-payment-block --><!-- wp:woocommerce/checkout-order-note-block --><div class="wp-block-woocommerce-checkout-order-note-block"></div><!-- /wp:woocommerce/checkout-order-note-block --><!-- wp:woocommerce/checkout-terms-block --><div class="wp-block-woocommerce-checkout-terms-block"></div><!-- /wp:woocommerce/checkout-terms-block --><!-- wp:woocommerce/checkout-actions-block --><div class="wp-block-woocommerce-checkout-actions-block"></div><!-- /wp:woocommerce/checkout-actions-block --></div><!-- /wp:woocommerce/checkout-fields-block --><!-- wp:woocommerce/checkout-totals-block --><div class="wp-block-woocommerce-checkout-totals-block"><!-- wp:woocommerce/checkout-order-summary-block --><div class="wp-block-woocommerce-checkout-order-summary-block"></div><!-- /wp:woocommerce/checkout-order-summary-block --></div><!-- /wp:woocommerce/checkout-totals-block --></div><!-- /wp:woocommerce/checkout -->';
		}

		return $pages;
	}

	/**
	 * Disable activation notices, specific for SomewhereWarm plugins as they share the same logic.
	 * Filters out the `welcome` notice from the list of notices to be displayed.
	 *
	 * It's specifically hooked on `load-index.php` and `load-plugins.php`
	 * as both PB and GC display notices only on these pages.
	 *
	 * @since 1.9.14
	 * @return void
	 */
	public function maybe_remove_somewherewarm_maintenance_notices() {
		// Gift Cards.
		if ( class_exists( 'WC_GC_Admin_Notices' ) && WC_GC_Admin_Notices::is_maintenance_notice_visible( 'welcome' ) ) {
			WC_GC_Admin_Notices::$maintenance_notices = array_filter( WC_GC_Admin_Notices::$maintenance_notices, static function ( $element ) {
				return 'welcome' !== $element;
			} );
		}

		// Product Bundles.
		if ( class_exists( 'WC_PB_Admin_Notices' ) && WC_PB_Admin_Notices::is_maintenance_notice_visible( 'welcome' ) ) {
			WC_PB_Admin_Notices::$maintenance_notices = array_filter( WC_PB_Admin_Notices::$maintenance_notices, static function ( $element ) {
				return 'welcome' !== $element;
			} );
		}
	}

}

$wc_calypso_bridge_plugins = WC_Calypso_Bridge_Plugins::get_instance();
