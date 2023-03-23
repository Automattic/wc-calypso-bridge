<?php
/**
 * Adds the functionality needed to bridge the WooCommerce onboarding wizard.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 2.0.11
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
	 * @see   $this->modify_one_time_operations() to unset operations that should not run.
	 *
	 * @var array
	 */
	protected $one_time_operations = array(
		'delete_coupon_moved_notes'  => 'delete_coupon_moved_notes_callback',
		'woocommerce_create_pages'   => 'woocommerce_create_pages_callback',
		'set_jetpack_defaults'       => 'set_jetpack_defaults_callback',
		'set_wc_tracker_twice_daily' => 'set_wc_tracker_twice_daily_callback',
		'set_wc_tracker_default'     => 'set_wc_tracker_default_callback',
	);

	/**
	 * Option prefix.
	 *
	 * @since 1.9.9
	 * @var string
	 */
	protected $option_prefix = 'wc_calypso_bridge_one_time_operation_';

	/**
	 * Constructor.
	 */
	private function __construct() {

		/**
		 * Handle one-time operations.
		 */
		$this->modify_one_time_operations();
		$this->setup_one_time_operations();

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

		if ( wc_calypso_bridge_has_ecommerce_features() ) {

			add_filter( 'wp_redirect', array( $this, 'prevent_redirects_on_activation' ), 10, 2 );

			/**
			 * Enable WooCommerce Homescreen.
			 *
			 * @return string
			 */
			add_filter( 'pre_option_woocommerce_homescreen_enabled', static function() {
				return 'yes';
			} );

			/**
			 * Remove the Write button from the global bar in Ecommerce plan.
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
		}
	}

	/**
	 * Modify one time operations based on current plan.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function modify_one_time_operations() {

		if ( ! wc_calypso_bridge_has_ecommerce_features() ) {
			unset( $this->one_time_operations[ 'set_jetpack_defaults' ] );
			unset( $this->one_time_operations[ 'woocommerce_create_pages' ] );
			unset( $this->one_time_operations[ 'set_wc_tracker_twice_daily_callback' ] );
			unset( $this->one_time_operations[ 'set_wc_tracker_default_callback' ] );
		}
	}

	/**
	 * Set the one time operations and execute their callbacks.
	 *
	 * @since 1.9.4
	 * @return void
	 */
	public function setup_one_time_operations() {

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		foreach ( $this->one_time_operations as $operation => $callback ) {

			// Don't run the operation if the callback is not callable.
			if ( ! method_exists( $this, $callback ) ) {
				continue;
			}

			$status = get_option( $this->option_prefix . $operation );
			if ( ! $status ) {
				// Option doesn't exist, flag the operation as initialized.
				update_option( $this->option_prefix . $operation, 'init', 'no' );
			}

			if ( 'completed' === $status ) {
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

		add_action( 'woocommerce_init', function () {

			if ( ! class_exists( 'Automattic\WooCommerce\Admin\Notes\Notes' ) ) {
				return;
			}

			$operation = 'delete_coupon_moved_notes';

			// Delete all existing `Coupon Page Moved` notes from the DB.
			$note = Automattic\WooCommerce\Admin\Notes\Notes::get_note_by_name( 'wc-admin-coupon-page-moved' );
			if ( false === $note ) {
				update_option( $this->option_prefix . $operation, 'completed', 'no' );

				return;
			}

			Automattic\WooCommerce\Admin\Notes\Notes::delete_notes_with_name( 'wc-admin-coupon-page-moved' );
			update_option( $this->option_prefix . $operation, 'completed', 'no' );

		}, PHP_INT_MAX );
	}

	/**
	 * Create WooCommerce related pages for the Ecommerce Plan.
	 *
	 * @since 1.9.8
	 * @return void
	 */
	public function woocommerce_create_pages_callback() {

		add_action( 'woocommerce_init', function () {

			$operation = 'woocommerce_create_pages';

			// Set the operation as completed if the store is active for more than 5 minutes.
			if ( WCAdminHelper::is_wc_admin_active_for( 300 ) ) {
				update_option( $this->option_prefix . $operation, 'completed', 'no' );

				return;
			}

			global $wpdb;

			$wpdb->query( 'START TRANSACTION' );

			// Prepare to lock the row, when it gets updated.
			$status = $wpdb->get_var(
				$wpdb->prepare( "
				SELECT option_value
				FROM `{$wpdb->options}`
				WHERE option_name = '%s'
				LIMIT 1
				FOR UPDATE
				",
					$this->option_prefix . $operation
				)
			);

			// Lock the row, by immediately executing an UPDATE query.
			$wpdb->query(
				$wpdb->prepare( "
					UPDATE `{$wpdb->options}`
					SET option_value = '%s'
					WHERE option_name = '%s'",
					'started',
					$this->option_prefix . $operation
				)
			);

			if ( 'completed' === $status ) {
				$wpdb->query( 'ROLLBACK' );

				return;
			}

			try {
				/*
				 * Force delete all WooCommerce pages. Some themes create them, and we end up with duplicates.
				 *
				 * `My Account` page, has slug `my-account`.
				 * @see WC_Install::create_pages()
				 */
				foreach ( [ 'shop', 'cart', 'my-account', 'checkout', 'refund_returns' ] as $page ) {
					$page = get_page_by_path( $page, ARRAY_A );
					if ( is_array( $page ) && isset( $page['ID'] ) ) {
						wp_delete_post( $page['ID'], true );
					}
				}

				/*
				 * Reset the woocommerce_*_page_id options.
				 * This is needed as woocommerce_*_page_id options have incorrect values on a fresh installation
				 * for an ecommerce plan. WC_Install:create_pages() might not create all the
				 * required pages without resetting these options first.
				 *
				 * `My Account` page id setting, is created with key `myaccount`.
				 * @see WC_Install::create_pages()
				 */
				foreach ( [ 'shop', 'cart', 'myaccount', 'checkout', 'refund_returns' ] as $page ) {
					delete_option( "woocommerce_{$page}_page_id" );
				}

				// Delete the following note, so it can be recreated with the correct refund page ID.
				if ( class_exists( 'Automattic\WooCommerce\Admin\Notes\Notes' ) ) {
					Automattic\WooCommerce\Admin\Notes\Notes::delete_notes_with_name( 'wc-refund-returns-page' );
				}

				WC_Install::create_pages();

				// Get navigation menu page and set up the menu.
				$menu_page_slugs = array(
					'primary',
					'header-navigation',
					'navigation',
				);

				foreach ( $menu_page_slugs as $menu_page_slug ) {
					$menu_page_post = get_page_by_path( $menu_page_slug, OBJECT, 'wp_navigation' );

					if ( ! is_a( $menu_page_post, 'WP_Post' ) ) {
						continue;
					}

					$menu_pages = array(
						'shop'       => get_post( get_option( 'woocommerce_shop_page_id' ) ),
						'blog'       => get_page_by_path( 'blog' ),
						'my-account' => get_post( get_option( 'woocommerce_myaccount_page_id' ) ),
						'contact-us' => get_page_by_path( 'contact-us' ),
					);

					$menu_content = '<!-- wp:navigation-link {"label":"' . __( 'Home', 'woocommerce' ) . '","url":"/","kind":"custom","isTopLevelLink":true} /-->';

					foreach ( $menu_pages as $key => $page ) {
						if ( ! is_a( $page, 'WP_Post' ) ) {
							continue;
						}

						$title = $page->post_title;
						if ( 'contact-us' === $key && 'Contact us' === $title ) {
							$title = __( 'Contact', 'wc-calypso-bridge' );
						} elseif ( 'my-account' === $key && 'My account' === $title ) {
							$title = __( 'My Account', 'wc-calypso-bridge' );
						}

						$menu_content .= '<!-- wp:navigation-link {"label":"' . esc_attr( wp_strip_all_tags( $title ) ) . '","type":"page","id":' . $page->ID . ',"url":"' . get_permalink( $page->ID ) . '","kind":"post-type","isTopLevelLink":true} /-->';
					}

					wp_update_post( array(
						'ID'           => $menu_page_post->ID,
						'post_content' => $menu_content,
					) );

				}

				$wpdb->query(
					$wpdb->prepare( "
					UPDATE `{$wpdb->options}`
					SET option_value = '%s'
					WHERE option_name = '%s'",
						'completed',
						$this->option_prefix . $operation
					)
				);

				// Update and Release row.
				$wpdb->query( 'COMMIT' );
				wp_cache_delete( $this->option_prefix . $operation, 'options' );

				return;
			} catch ( Exception $e ) {
				// Release row.
				$wpdb->query( 'ROLLBACK' );
				error_log( 'Exception: ' . $e->getMessage() );

				return;
			}

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
				include_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/notes/class-wc-calypso-bridge-cart-checkout-blocks-default-inbox-note.php';
				new WC_Calypso_Bridge_Cart_Checkout_Blocks_Default_Inbox_Note();
				WC_Calypso_Bridge_Cart_Checkout_Blocks_Default_Inbox_Note::possibly_add_note();
			}

			return $pages;

		}, PHP_INT_MAX );

	}

	/**
	 * Defines the Jetpack modules active in the Ecommerce Plan by default.
	 *
	 * @since 1.9.8
	 * @return void
	 */
	public function set_jetpack_defaults_callback() {

		add_action( 'woocommerce_init', function () {

			$active_modules = array(
				'manage',
				'masterbar',
				'json-api',
				'sharedaddy',
				'google-fonts',
				'sso',
				'notes',
				'protect',
				'latex',
				'carousel',
				'comment-likes',
				'comments',
				'contact-form',
				'widgets',
				'likes',
				'shortcodes',
				'markdown',
				'search',
				'subscriptions',
				'tiled-gallery',
				'videopress',
				'shortlinks',
				'woocommerce-analytics',
				'monitor',
				'seo-tools',
				'custom-css',
				'publicize',
				'verification-tools',
				'sitemaps',
			);

			$sharing_options = array(
				'global' => array(
					'button_style'  => 'icon',
					'sharing_label' => '',
					'open_links'    => 'same',
					'show'          => array( 'post' ),
					'custom'        => array(),
				),
			);

			// Set defaults only if the store is brand new (been active for less than 5 minutes).
			if ( ! WCAdminHelper::is_wc_admin_active_for( 300 ) ) {
				update_option( 'jetpack_active_modules', $active_modules );
				update_option( 'sharing-options', $sharing_options );
			}
			$operation = 'set_jetpack_defaults';
			update_option( $this->option_prefix . $operation, 'completed', 'no' );
		}, PHP_INT_MAX );
	}

	/**
	 * Set wc tracker recurrence to twice daily.
	 * This job will run once if the store has been active for less than 3 months.
	 *
	 * @since 2.0.11
	 */
	public function set_wc_tracker_twice_daily_callback() {

		add_action( 'plugins_loaded', function () {

			if ( ! WCAdminHelper::is_wc_admin_active_for( 3 * MONTH_IN_SECONDS ) ) {
				wp_clear_scheduled_hook( 'woocommerce_tracker_send_event' );
				wp_schedule_event( time() + 10, 'twicedaily', 'woocommerce_tracker_send_event' );
			}

			$operation = 'set_wc_tracker_twice_daily';
			update_option( $this->option_prefix . $operation, 'completed', 'no' );
		}, PHP_INT_MAX );
	}

	/**
	 * Set wc tracker recurrence to its original value.
	 * This job will run once after the store has been active for 3 months.
	 *
	 * @since 2.0.11
	 */
	public function set_wc_tracker_default_callback() {

		add_action( 'plugins_loaded', function () {

			if ( WCAdminHelper::is_wc_admin_active_for( 3 * MONTH_IN_SECONDS ) ) {
				wp_clear_scheduled_hook( 'woocommerce_tracker_send_event' );
				wp_schedule_event( time() + 10, apply_filters( 'woocommerce_tracker_event_recurrence', 'daily' ), 'woocommerce_tracker_send_event' );

				$operation = 'set_wc_tracker_default';
				update_option( $this->option_prefix . $operation, 'completed', 'no' );
			}

		}, PHP_INT_MAX );
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
}

WC_Calypso_Bridge_Setup::get_instance();
