<?php
/**
 * Adds the functionality needed to bridge the WooCommerce onboarding wizard.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 1.9.8
 */

use Automattic\WooCommerce\Admin\WCAdminHelper;

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge Setup
 */
class WC_Calypso_Bridge_Setup {

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Bridge_Setup instance
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
	 * Array of operations - name => callback.
	 *
	 * @since 1.9.4
	 * @var array
	 */
	protected $one_time_operations = array(
		'delete_coupon_moved_notes' => 'delete_coupon_moved_notes_callback',
		'woocommerce_create_pages'  => 'woocommerce_create_pages_callback',
		'set_jetpack_defaults'      => 'set_jetpack_defaults',
	);

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->setup_one_time_operations();
		add_action( 'shutdown', array( $this, 'save_one_time_operations_status' ), PHP_INT_MAX );

		add_action( 'load-woocommerce_page_wc-settings', array( $this, 'redirect_store_details_onboarding' ) );
		add_filter( 'pre_option_woocommerce_onboarding_profile', array( $this, 'set_onboarding_status_to_skipped' ), 100 );
		add_filter( 'default_option_woocommerce_onboarding_profile', array( $this, 'set_business_extensions_empty' ) );
		add_filter( 'option_woocommerce_onboarding_profile', array( $this, 'set_business_extensions_empty' ) );
		add_filter( 'woocommerce_admin_onboarding_themes', array( $this, 'remove_non_installed_themes' ) );
		add_filter( 'wp_redirect', array( $this, 'prevent_redirects_on_activation' ), 10, 2 );
		add_filter( 'pre_option_woocommerce_homescreen_enabled', array( $this, 'always_enable_homescreen' ) );
	}

	/**
	 * Set the one time operations and execute their callbacks.
	 * If a callback is true (boolean), it means the operation
	 * has already been executed and will be skipped.
	 *
	 * @since 1.9.4
	 * @return void
	 */
	public function setup_one_time_operations() {

		$operations                = get_option( 'woocommerce_atomic_one_time_operations', $this->one_time_operations );
		$this->one_time_operations = array_merge( $this->one_time_operations, $operations );

		foreach ( $this->one_time_operations as $operation => $callback ) {

			// Don't run the operation if the callback has already been executed.
			if ( $this->is_one_time_operation_complete( $operation ) ) {
				continue;
			}

			// Don't run the operation if the callback is not callable and don't save it in the options.
			if ( ! method_exists( $this, $callback ) ) {
				unset( $this->one_time_operations[ $operation ] );
				continue;
			}

			$this->$callback();
		}

	}

	/**
	 * Delete all `wc-admin-coupon-page-moved` notes and sets the operation as completed.
	 *
	 * @since 1.9.4
	 * @return void
	 */
	public function delete_coupon_moved_notes_callback() {

		add_action( 'admin_init', function () {

			if ( ! class_exists( 'Automattic\WooCommerce\Admin\Notes\Notes' ) ) {
				return;
			}

			// Delete all existing `Coupon Page Moved` notes from the DB.
			$note = Automattic\WooCommerce\Admin\Notes\Notes::get_note_by_name( 'wc-admin-coupon-page-moved' );
			if ( false === $note ) {
				$this->set_one_time_operation_complete( 'delete_coupon_moved_notes' );

				return;
			}

			Automattic\WooCommerce\Admin\Notes\Notes::delete_notes_with_name( 'wc-admin-coupon-page-moved' );
			$this->set_one_time_operation_complete( 'delete_coupon_moved_notes' );

		}, PHP_INT_MAX );
	}

	/**
	 * Defines the Jetpack modules active in the Ecommerce Plan by default.
	 *
	 * @since 1.9.8
	 * @return void
	 */
	public function woocommerce_create_pages_callback() {

		add_action( 'woocommerce_init', function () {

			global $wpdb;
			$post_count = (int) $wpdb->get_var( "select count(*) from $wpdb->posts where post_name in ('shop', 'cart', 'my-account', 'checkout', 'refund_returns')" );

			// Abort if we find any existing pages.
			if ( 5 === $post_count ) {
				$this->set_one_time_operation_complete( 'woocommerce_create_pages' );

				return;
			}

			// Reset the woocommerce_*_page_id options.
			// This is needed as woocommerce_*_page_id options have incorrect values on a fresh installation
			// for an ecommerce plan. WC_Install:create_pages() might not create all the
			// required pages without resetting these options first.
			foreach ( [ 'shop', 'cart', 'myaccount', 'checkout', 'refund_returns' ] as $page ) {
				delete_option( "woocommerce_{$page}_page_id" );
			}

			// Delete the following note, so it can be recreated with the correct refund page ID.
			if ( class_exists( 'Automattic\WooCommerce\Admin\Notes\Notes' ) ) {
				Automattic\WooCommerce\Admin\Notes\Notes::delete_notes_with_name( 'wc-refund-returns-page' );
			}

			WC_Install::create_pages();
			$this->set_one_time_operation_complete( 'woocommerce_create_pages' );

		}, PHP_INT_MAX );

		// Gets triggered from the above WC_Install::create_pages call.
		add_filter( 'woocommerce_create_pages', function ( $pages ) {

			// Set the cart and checkout blocks as defaults.
			if (
				class_exists( 'Automattic\WooCommerce\Blocks\Package' )
				&& WC_Calypso_Bridge_Helper_Functions::is_wc_admin_installed_gte( WC_Calypso_Bridge::RELEASE_DATE_DEFAULT_CHECKOUT_BLOCKS )
				&& version_compare( \Automattic\WooCommerce\Blocks\Package::get_version(), '8.7.4' ) >= 0
			) {
				if ( isset( $pages['cart']['content'] ) ) {
					$pages['cart']['content'] = '<!-- wp:woocommerce/cart --><div class="wp-block-woocommerce-cart is-loading"><!-- wp:woocommerce/filled-cart-block --><div class="wp-block-woocommerce-filled-cart-block"><!-- wp:woocommerce/cart-items-block --><div class="wp-block-woocommerce-cart-items-block"><!-- wp:woocommerce/cart-line-items-block --><div class="wp-block-woocommerce-cart-line-items-block"></div><!-- /wp:woocommerce/cart-line-items-block --></div><!-- /wp:woocommerce/cart-items-block --><!-- wp:woocommerce/cart-totals-block --><div class="wp-block-woocommerce-cart-totals-block"><!-- wp:woocommerce/cart-order-summary-block --><div class="wp-block-woocommerce-cart-order-summary-block"></div><!-- /wp:woocommerce/cart-order-summary-block --><!-- wp:woocommerce/cart-express-payment-block --><div class="wp-block-woocommerce-cart-express-payment-block"></div><!-- /wp:woocommerce/cart-express-payment-block --><!-- wp:woocommerce/proceed-to-checkout-block --><div class="wp-block-woocommerce-proceed-to-checkout-block"></div><!-- /wp:woocommerce/proceed-to-checkout-block --><!-- wp:woocommerce/cart-accepted-payment-methods-block --><div class="wp-block-woocommerce-cart-accepted-payment-methods-block"></div><!-- /wp:woocommerce/cart-accepted-payment-methods-block --></div><!-- /wp:woocommerce/cart-totals-block --></div><!-- /wp:woocommerce/filled-cart-block --><!-- wp:woocommerce/empty-cart-block --><div class="wp-block-woocommerce-empty-cart-block"><!-- wp:image {"align":"center","sizeSlug":"small"} --><div class="wp-block-image"><figure class="aligncenter size-small"><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzgiIGhlaWdodD0iMzgiIHZpZXdCb3g9IjAgMCAzOCAzOCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTE5IDBDOC41MDQwMyAwIDAgOC41MDQwMyAwIDE5QzAgMjkuNDk2IDguNTA0MDMgMzggMTkgMzhDMjkuNDk2IDM4IDM4IDI5LjQ5NiAzOCAxOUMzOCA4LjUwNDAzIDI5LjQ5NiAwIDE5IDBaTTI1LjEyOSAxMi44NzFDMjYuNDg1MSAxMi44NzEgMjcuNTgwNiAxMy45NjY1IDI3LjU4MDYgMTUuMzIyNkMyNy41ODA2IDE2LjY3ODYgMjYuNDg1MSAxNy43NzQyIDI1LjEyOSAxNy43NzQyQzIzLjc3MyAxNy43NzQyIDIyLjY3NzQgMTYuNjc4NiAyMi42Nzc0IDE1LjMyMjZDMjIuNjc3NCAxMy45NjY1IDIzLjc3MyAxMi44NzEgMjUuMTI5IDEyLjg3MVpNMTEuNjQ1MiAzMS4yNTgxQzkuNjE0OTIgMzEuMjU4MSA3Ljk2Nzc0IDI5LjY0OTIgNy45Njc3NCAyNy42NTczQzcuOTY3NzQgMjYuMTI1IDEwLjE1MTIgMjMuMDI5OCAxMS4xNTQ4IDIxLjY5NjhDMTEuNCAyMS4zNjczIDExLjg5MDMgMjEuMzY3MyAxMi4xMzU1IDIxLjY5NjhDMTMuMTM5MSAyMy4wMjk4IDE1LjMyMjYgMjYuMTI1IDE1LjMyMjYgMjcuNjU3M0MxNS4zMjI2IDI5LjY0OTIgMTMuNjc1NCAzMS4yNTgxIDExLjY0NTIgMzEuMjU4MVpNMTIuODcxIDE3Ljc3NDJDMTEuNTE0OSAxNy43NzQyIDEwLjQxOTQgMTYuNjc4NiAxMC40MTk0IDE1LjMyMjZDMTAuNDE5NCAxMy45NjY1IDExLjUxNDkgMTIuODcxIDEyLjg3MSAxMi44NzFDMTQuMjI3IDEyLjg3MSAxNS4zMjI2IDEzLjk2NjUgMTUuMzIyNiAxNS4zMjI2QzE1LjMyMjYgMTYuNjc4NiAxNC4yMjcgMTcuNzc0MiAxMi44NzEgMTcuNzc0MlpNMjUuOTEwNSAyOS41ODc5QzI0LjE5NDQgMjcuNTM0NyAyMS42NzM4IDI2LjM1NDggMTkgMjYuMzU0OEMxNy4zNzU4IDI2LjM1NDggMTcuMzc1OCAyMy45MDMyIDE5IDIzLjkwMzJDMjIuNDAxNiAyMy45MDMyIDI1LjYxMTcgMjUuNDA0OCAyNy43ODc1IDI4LjAyNUMyOC44NDQ4IDI5LjI4MTUgMjYuOTI5NCAzMC44MjE0IDI1LjkxMDUgMjkuNTg3OVoiIGZpbGw9ImJsYWNrIi8+Cjwvc3ZnPgo=" alt=""/></figure></div><!-- /wp:image --><!-- wp:heading {"textAlign":"center","className":"wc-block-cart__empty-cart__title"} --><h2 class="has-text-align-center wc-block-cart__empty-cart__title">Your cart is currently empty!</h2><!-- /wp:heading --><!-- wp:paragraph {"align":"center"} --><p class="has-text-align-center"><a href="http://localhost:8889/shop/">Browse store</a>.</p><!-- /wp:paragraph --><!-- wp:separator {"className":"is-style-dots"} --><hr class="wp-block-separator is-style-dots"/><!-- /wp:separator --><!-- wp:heading {"textAlign":"center"} --><h2 class="has-text-align-center">New in store</h2><!-- /wp:heading --><!-- wp:woocommerce/product-new {"rows":1} /--></div><!-- /wp:woocommerce/empty-cart-block --></div><!-- /wp:woocommerce/cart -->';
				}

				if ( isset( $pages['checkout']['content'] ) ) {
					$pages['checkout']['content'] = '<!-- wp:woocommerce/checkout --><div class="wp-block-woocommerce-checkout wc-block-checkout is-loading"><!-- wp:woocommerce/checkout-fields-block --><div class="wp-block-woocommerce-checkout-fields-block"><!-- wp:woocommerce/checkout-express-payment-block --><div class="wp-block-woocommerce-checkout-express-payment-block"></div><!-- /wp:woocommerce/checkout-express-payment-block --><!-- wp:woocommerce/checkout-contact-information-block --><div class="wp-block-woocommerce-checkout-contact-information-block"></div><!-- /wp:woocommerce/checkout-contact-information-block --><!-- wp:woocommerce/checkout-shipping-address-block --><div class="wp-block-woocommerce-checkout-shipping-address-block"></div><!-- /wp:woocommerce/checkout-shipping-address-block --><!-- wp:woocommerce/checkout-billing-address-block --><div class="wp-block-woocommerce-checkout-billing-address-block"></div><!-- /wp:woocommerce/checkout-billing-address-block --><!-- wp:woocommerce/checkout-shipping-methods-block --><div class="wp-block-woocommerce-checkout-shipping-methods-block"></div><!-- /wp:woocommerce/checkout-shipping-methods-block --><!-- wp:woocommerce/checkout-payment-block --><div class="wp-block-woocommerce-checkout-payment-block"></div><!-- /wp:woocommerce/checkout-payment-block --><!-- wp:woocommerce/checkout-order-note-block --><div class="wp-block-woocommerce-checkout-order-note-block"></div><!-- /wp:woocommerce/checkout-order-note-block --><!-- wp:woocommerce/checkout-terms-block --><div class="wp-block-woocommerce-checkout-terms-block"></div><!-- /wp:woocommerce/checkout-terms-block --><!-- wp:woocommerce/checkout-actions-block --><div class="wp-block-woocommerce-checkout-actions-block"></div><!-- /wp:woocommerce/checkout-actions-block --></div><!-- /wp:woocommerce/checkout-fields-block --><!-- wp:woocommerce/checkout-totals-block --><div class="wp-block-woocommerce-checkout-totals-block"><!-- wp:woocommerce/checkout-order-summary-block --><div class="wp-block-woocommerce-checkout-order-summary-block"></div><!-- /wp:woocommerce/checkout-order-summary-block --></div><!-- /wp:woocommerce/checkout-totals-block --></div><!-- /wp:woocommerce/checkout -->';
				}

				// Inform the merchant that we've enabled the new checkout experience.
				include_once dirname( __FILE__ ) . '/notes/class-wc-calypso-bridge-cart-checkout-blocks-default-inbox-note.php';
				new WC_Calypso_Bridge_Cart_Checkout_Blocks_Default_Inbox_Note();
				WC_Calypso_Bridge_Cart_Checkout_Blocks_Default_Inbox_Note::possibly_add_note();

			}

			return $pages;

		}, PHP_INT_MAX );

	}

	/**
	 * Save the one-time operations' status .
	 *
	 * @since 1.9.4
	 * @return void
	 */
	public function save_one_time_operations_status() {
		update_option( 'woocommerce_atomic_one_time_operations', $this->one_time_operations );
	}

	/**
	 * Skip the OBW.
	 *
	 * This callback will ensure that the `woocommerce_onboarding_profile` option value will result to skipped state, always.
	 *
	 * @since 1.9.4
	 *
	 * @param  mixed  $value
	 * @return array
	 */
	public function set_onboarding_status_to_skipped( $option_value ) {
		return array( 'skipped' => true );
	}

	/**
	 * Opt all sites into using WooCommerce Home Screen.
	 */
	public function always_enable_homescreen() {
		return 'yes';
	}

	/**
	 * Prevent redirects on activation when WooCommerce is being setup. Some plugins
	 * do this when they are activated.
	 *
	 * @param string $location Redirect location.
	 * @param string $status   Status code.
	 *
	 * @return string
	 */
	public function prevent_redirects_on_activation( $location, $status ) {
		$location_prefix = '';
		if ( wp_parse_url( $location, PHP_URL_SCHEME ) !== null ) {
			// $location has a URL scheme, so it is probably a full URL;
			// we will need to match against a full URL
			$location_prefix = admin_url();
		}

		$redirect_options_by_location = array(
			$location_prefix . 'admin.php?page=mailchimp-woocommerce'   => 'mailchimp_woocommerce_plugin_do_activation_redirect',
			$location_prefix . 'admin.php?page=crowdsignal-forms-setup' => 'crowdsignal_forms_do_activation_redirect',
			$location_prefix . 'admin.php?page=creativemail'            => 'ce4wp_activation_redirect',
		);

		if ( isset( $redirect_options_by_location[ $location ] ) ) {
			$option_to_delete = $redirect_options_by_location[ $location ];
			if ( is_string( $option_to_delete ) ) {
				// Delete the redirect option so we don't end up here anymore.
				delete_option( $option_to_delete );
			}
			$location = admin_url( 'admin.php?page=wc-admin' );
		}

		return $location;
	}

	/**
	 * Handle the store location's onboarding redirect when user provided a full address.
	 *
	 * @since 1.9.4
	 * @return void
	 */
	public function redirect_store_details_onboarding() {

		// Only run on save.
		if ( empty( $_POST ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return;
		}

		if ( ! isset( $_GET['tutorial'] ) || 'true' !== $_GET['tutorial'] ) {
			return;
		}

		$store_address  = get_option( 'woocommerce_store_address' );
		$store_city     = get_option( 'woocommerce_store_city' );
		$store_postcode = get_option( 'woocommerce_store_postcode' );

		if ( ! empty( $store_address ) && ! empty( $store_city ) && ! empty( $store_postcode ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=wc-admin' ) );
		}
	}

	/**
	 * Store Profiler: Set business_extensions to empty array.
	 *
	 * @param array $option Array of properties for OBW Profile.
	 *
	 * @return array
	 */
	public function set_business_extensions_empty( $option ) {
		// Ensuring the option is an array by default.
		// By having an empty array of 'business_extensions' all options are toggled off by default in the OBW.
		if ( ! is_array( $option ) ) {
			$option = array(
				'business_extensions' => array(),
			);
		} else {
			$option['business_extensions'] = array();
		}

		return $option;
	}

	/**
	 * Remove non-installed ( paid ) themes from the Onboarding data source.
	 *
	 * @param array $themes Array of themes comprised of locally installed themes + marketplace themes.
	 *
	 * @return array
	 */
	public function remove_non_installed_themes( $themes ) {
		$local_themes = array_filter( $themes, array( $this, 'is_theme_installed' ) );

		return $local_themes;
	}

	/**
	 * Conditional method to determine if a theme is installed locally.
	 *
	 * @param array $theme Theme attributes.
	 *
	 * @return boolean
	 */
	public function is_theme_installed( $theme ) {
		return isset( $theme['is_installed'] ) && $theme['is_installed'];
	}

	/**
	 * Check if the operation has completed.
	 *
	 * @since 1.9.4
	 * @param string $operation One time operation name.
	 * @return boolean True if the operation has completed, false otherwise.
	 */
	protected function is_one_time_operation_complete( $operation ) {
		return ( isset( $this->one_time_operations[ $operation ] ) && true === $this->one_time_operations[ $operation ] );
	}

	/**
	 * Sets an operation as complete.
	 *
	 * @since 1.9.4
	 * @param string $operation One time operation name.
	 * @return void
	 */
	protected function set_one_time_operation_complete( $operation ) {
		if ( isset( $this->one_time_operations[ $operation ] ) ) {
			$this->one_time_operations[ $operation ] = true;
		}
	}
}

$wc_calypso_bridge_setup = WC_Calypso_Bridge_Setup::get_instance();
