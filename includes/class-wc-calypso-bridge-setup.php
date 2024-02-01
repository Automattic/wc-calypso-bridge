<?php
/**
 * Adds the functionality needed to bridge the WooCommerce onboarding wizard.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 2.3.5
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
		'add_free_trial_welcome_note'             => 'add_free_trial_welcome_note_callback',
		'delete_coupon_moved_notes'               => 'delete_coupon_moved_notes_callback',
		'woocommerce_create_pages'                => 'woocommerce_create_pages_callback',
		'set_jetpack_defaults'                    => 'set_jetpack_defaults_callback',
		'set_wc_tracker_twice_daily'              => 'set_wc_tracker_twice_daily_callback',
		'set_wc_tracker_default'                  => 'set_wc_tracker_default_callback',
		'set_wc_subscriptions_siteurl'            => 'set_wc_subscriptions_siteurl_callback',
		'set_wc_subscriptions_siteurl_add_domain' => 'set_wc_subscriptions_siteurl_add_domain_callback',
		'set_wc_measurement_units'                => 'set_wc_measurement_units_callback',
		'woocommerce_set_default_options'         => 'woocommerce_set_default_options_callback',
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
			add_filter( 'pre_option_woocommerce_homescreen_enabled', static function () {
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
			unset( $this->one_time_operations['set_jetpack_defaults'] );
			unset( $this->one_time_operations['woocommerce_create_pages'] );
			unset( $this->one_time_operations['set_wc_tracker_twice_daily'] );
			unset( $this->one_time_operations['set_wc_tracker_default'] );
			unset( $this->one_time_operations['set_wc_subscriptions_siteurl'] );
			unset( $this->one_time_operations['set_wc_subscriptions_siteurl_add_domain'] );
			unset( $this->one_time_operations['set_wc_measurement_units'] );
		}

		if ( ! wc_calypso_bridge_is_ecommerce_trial_plan() ) {
			unset( $this->one_time_operations['add_free_trial_welcome_note'] );
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
	 * Add a welcome note for Woo Express Free Trial users.
	 *
	 * @since 2.2.20
	 * @return void
	 */
	public function add_free_trial_welcome_note_callback() {

		add_action( 'woocommerce_init', function () {

			if ( ! class_exists( 'Automattic\WooCommerce\Admin\Notes\Notes' ) ) {
				return;
			}

			$operation = 'add_free_trial_welcome_note';

			// Set the operation as completed if the store is active for more than 60 minutes.
			if ( WCAdminHelper::is_wc_admin_active_for( 60 * MINUTE_IN_SECONDS ) ) {
				update_option( $this->option_prefix . $operation, 'completed', 'no' );

				return;
			}

			include_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/notes/class-wc-calypso-bridge-free-trial-welcome.php';
			WC_Calypso_Bridge_Free_Trial_Welcome_Note::possibly_add_note();

			update_option( $this->option_prefix . $operation, 'completed', 'no' );

		}, PHP_INT_MAX );

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

			$this->write_to_log( $operation, 'INITIALIZED' );

			// Set the operation as completed if the store is active for more than 60 minutes.
			if ( WCAdminHelper::is_wc_admin_active_for( 60 * MINUTE_IN_SECONDS ) ) {
				update_option( $this->option_prefix . $operation, 'completed', 'no' );
				$this->write_to_log( $operation, 'completed (60 minutes)' );

				return;
			}

			global $wpdb;

			$wpdb->query( 'START TRANSACTION' );
			$this->write_to_log( $operation, 'START TRANSACTION' );

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
				$this->write_to_log( $operation, 'ROLLBACK - already completed' );

				return;
			}

			try {

				/*
				 * Delete all WooCommerce pages, by translated slug, and start fresh.
				 *
				 * @see WC_Install::create_pages()
				 */
				$this->write_to_log( $operation, 'DELETING WOOCOMMERCE PAGES ' );

				$woocommerce_pages = array(
					'shop'           => _x( 'shop', 'Page slug', 'woocommerce' ),
					'cart'           => _x( 'cart', 'Page slug', 'woocommerce' ),
					'checkout'       => _x( 'checkout', 'Page slug', 'woocommerce' ),
					'myaccount'      => _x( 'my-account', 'Page slug', 'woocommerce' ),
					'refund_returns' => _x( 'refund_returns', 'Page slug', 'woocommerce' ),
				);
				foreach ( $woocommerce_pages as $key => $page_slug ) {
					$slugs = array( $page_slug, $page_slug . '-2' );
					foreach ( $slugs as $slug ) {
						$this->maybe_delete_page_by_slug( $slug, $operation );
					}
				}

				/*
				 * Reset the woocommerce_*_page_id options.
				 * This is needed as woocommerce_*_page_id options have incorrect values on a fresh installation
				 * for an ecommerce plan. WC_Install:create_pages() might not create all the
				 * required pages without resetting these options first.
				 *
				 * @see WC_Install::create_pages()
				 */
				$this->write_to_log( $operation, 'DELETING WOOCOMMERCE PAGE OPTIONS ' );

				foreach ( $woocommerce_pages as $key => $page_slug ) {
					$value  = get_option( "woocommerce_{$key}_page_id" );
					$result = delete_option( "woocommerce_{$key}_page_id" );
					if ( $result ) {
						$this->write_to_log( $operation, 'deleted option woocommerce_' . $key . '_page_id : ' . $value );
					} else {
						$this->write_to_log( $operation, 'failed to delete option woocommerce_' . $key . '_page_id : ' . $value );
					}

					$result_cache = wp_cache_delete( "woocommerce_{$key}_page_id", 'options' );
					if ( $result_cache ) {
						$this->write_to_log( $operation, 'deleted cache for option woocommerce_' . $key . '_page_id : ' . $value );
					} else {
						$this->write_to_log( $operation, 'failed to delete cache for option woocommerce_' . $key . '_page_id : ' . $value );
					}
				}

				/*
				 * Deleting the hardcoded pages created by Headstart.
				 *
				 * @see https://github.com/Automattic/theme-tsubaki/blob/trunk/inc/headstart/en.json
				 */
				$this->write_to_log( $operation, 'DELETING HEADSTART PAGES ' );

				$headstart_slugs = array( 'shop', 'cart', 'checkout', 'my-account', 'refund_returns' );
				foreach ( $headstart_slugs as $page_slug ) {
					$slugs = array( $page_slug, $page_slug . '-2' );
					foreach ( $slugs as $slug ) {
						$this->maybe_delete_page_by_slug( $slug, $operation );
					}
				}

				$this->write_to_log( $operation, 'GETTING WOOCOMMERCE PAGE OPTIONS AFTER DELETION' );
				foreach ( $woocommerce_pages as $key => $page_slug ) {
					$value = get_option( "woocommerce_{$key}_page_id" );
					$this->write_to_log( $operation, 'getting option woocommerce_' . $key . '_page_id : ' . $value );
				}

				// Delete the following note, so it can be recreated with the correct refund page ID.
				if ( class_exists( 'Automattic\WooCommerce\Admin\Notes\Notes' ) ) {
					Automattic\WooCommerce\Admin\Notes\Notes::delete_notes_with_name( 'wc-refund-returns-page' );
				}

				$this->write_to_log( $operation, 'CREATING PAGES ' );
				WC_Install::create_pages();
				$this->write_to_log( $operation, 'finished WC_Install::create_pages' );

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
						} elseif ( 'blog' === $key ) {
							$title = __( 'News' ); // Leaving it to WordPress to translate this, as News exists in the translation files.

							// Update the page title and slug to news.
							wp_update_post( array(
								'ID'         => $page->ID,
								'post_title' => $title,
								'post_name'  => 'news',
							) );

						}

						$menu_content .= '<!-- wp:navigation-link {"label":"' . esc_attr( wp_strip_all_tags( $title ) ) . '","type":"page","id":' . $page->ID . ',"url":"' . get_permalink( $page->ID ) . '","kind":"post-type","isTopLevelLink":true} /-->';
					}

					wp_update_post( array(
						'ID'           => $menu_page_post->ID,
						'post_content' => $menu_content,
					) );

				}
				$this->write_to_log( $operation, 'created menu items' );

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
				$this->write_to_log( $operation, 'COMMIT and cache deleted' );

				return;
			} catch ( Exception $e ) {
				// Release row.
				$wpdb->query( 'ROLLBACK' );
				$this->write_to_log( $operation, 'ROLLBACK with Exception: ' . $e->getMessage() );

				return;
			}

		}, PHP_INT_MAX );

		// Gets triggered from the above WC_Install::create_pages call.
		add_filter( 'woocommerce_create_pages', function ( $pages ) {

			$operation = 'woocommerce_create_pages';
			$log_pages = array();
			foreach ( $pages as $key => $details ) {
				$log_pages[] = $key;
			}
			$this->write_to_log( $operation, 'woocommerce_create_pages filter - pages:' . implode( ', ', $log_pages ) );

			return $pages;

		}, PHP_INT_MAX );

		// Force WooCommerce to recreate pages.
		add_filter( 'woocommerce_create_page_id', function ( $valid_page_found, $slug, $page_content ) {
			$operation = 'woocommerce_create_pages';
			$this->write_to_log( $operation, 'woocommerce_create_page_id force create slug: ' . $slug );

			return false;
		}, PHP_INT_MAX, 3 );


		// Log which pages have been created.
		add_action( 'woocommerce_page_created', function ( $page_id, $page_data ) {
			$operation = 'woocommerce_create_pages';
			$slug      = isset( $page_data['post_name'] ) ? $page_data['post_name'] : '';
			$this->write_to_log( $operation, 'woocommerce_page_created action - id: ' . $page_id . ', slug: ' . $slug );
		}, PHP_INT_MAX, 2 );

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
			if ( ! WCAdminHelper::is_wc_admin_active_for( 5 * MINUTE_IN_SECONDS ) ) {
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
	 * Force WooCommerce subscriptions to save the site URL to avoid move/duplicated site messages on new sites.
	 *
	 * @since 2.1.8
	 * @return void
	 */
	public function set_wc_subscriptions_siteurl_callback() {

		add_action( 'plugins_loaded', function () {

			$operation = 'set_wc_subscriptions_siteurl';

			if ( ! class_exists( 'WCS_Staging' )
			     || ! method_exists( 'WCS_Staging', 'set_duplicate_site_url_lock' ) ) {
				return;
			}

			// wc_subscriptions_siteurl is not created when a site is moved to the atomic platform.
			// Update it only if it doesn't exist, so we cover new and existing sites.
			$exists = get_option( 'wc_subscriptions_siteurl', false );
			if ( empty( $exists ) ) {
				WCS_Staging::set_duplicate_site_url_lock();
			}
			update_option( $this->option_prefix . $operation, 'completed', 'no' );

		}, PHP_INT_MAX );

	}

	/**
	 * Force WooCommerce subscriptions to save the site URL to avoid move/duplicated site messages on domain purchase.
	 * This job runs "forever" until a domain is purchased and then it gets marked as complete.
	 *
	 * @since 2.1.8
	 * @return void
	 */
	public function set_wc_subscriptions_siteurl_add_domain_callback() {

		add_action( 'plugins_loaded', function () {

			$operation = 'set_wc_subscriptions_siteurl_add_domain';

			if ( ! class_exists( 'WCS_Staging' )
			     || ! method_exists( 'WCS_Staging', 'set_duplicate_site_url_lock' ) ) {
				return;
			}

			$wc_subscriptions_siteurl = get_option( 'wc_subscriptions_siteurl', false );
			if ( empty( $wc_subscriptions_siteurl ) ) {
				return;
			}

			$site_url = untrailingslashit( home_url( '', 'https' ) );

			// If a domain is purchased, site_url will not end with .wpcomstaging.com.
			if ( ! str_ends_with( $site_url, '.wpcomstaging.com' ) ) {
				// See WCS_Staging::get_duplicate_site_lock_key
				$wc_subscriptions_siteurl = str_replace( '_[wc_subscriptions_siteurl]_', '', $wc_subscriptions_siteurl );

				// If wc_subscriptions_siteurl ends in .wpcomstaging.com, it means set_duplicate_site_url_lock has already run,
				// has set a wpcomstaging domain and we can safely call set_duplicate_site_url_lock to set the new domain as url_lock.
				if ( str_ends_with( $wc_subscriptions_siteurl, '.wpcomstaging.com' ) ) {
					WCS_Staging::set_duplicate_site_url_lock();
					update_option( $this->option_prefix . $operation, 'completed', 'no' );
				}
			}

		}, PHP_INT_MAX );

	}

	/**
	 * Preconfigure product measurement units.
	 *
	 * @since 2.2.18
	 */
	public function set_wc_measurement_units_callback() {

		add_action( 'plugins_loaded', function () {

			$operation = 'set_wc_measurement_units';

			// Set the operation as completed if the store is active for more than 60 minutes.
			if ( WCAdminHelper::is_wc_admin_active_for( 60 * MINUTE_IN_SECONDS ) ) {
				update_option( $this->option_prefix . $operation, 'completed', 'no' );
				$this->write_to_log( $operation, 'completed (60 minutes)' );

				return;
			}

			// Bail out early if WooCommerce is not active.
			if (
				! function_exists( 'WC' ) ||
				! method_exists( WC(), 'plugin_path' )
			) {
				update_option( $this->option_prefix . $operation, 'completed', 'no' );
				$this->write_to_log( $operation, 'plugin_path does not exist' );

				return;
			};

			list( $country ) = explode( ':', get_option( 'woocommerce_default_country' ) );
			$locale_info = (array) include WC()->plugin_path() . '/i18n/locale-info.php';

			if (
				! isset( $locale_info[ $country ]['weight_unit'] ) ||
				! isset( $locale_info[ $country ]['dimension_unit'] )
			) {
				update_option( $this->option_prefix . $operation, 'completed', 'no' );
				$this->write_to_log( $operation, 'locale_info does not exist for country ' . $country );

				return;
			}

			// Dimension unit for US/UK is foot; WooCommerce does not use foot, so we need to convert it to inches.
			if ( 'foot' === $locale_info[ $country ]['dimension_unit'] ) {
				$locale_info[ $country ]['dimension_unit'] = 'in';
			}

			update_option( 'woocommerce_weight_unit', $locale_info[ $country ]['weight_unit'] );
			update_option( 'woocommerce_dimension_unit', $locale_info[ $country ]['dimension_unit'] );

			update_option( $this->option_prefix . $operation, 'completed', 'no' );
			$this->write_to_log( $operation, 'done for country ' . $country );
		}, PHP_INT_MAX );

	}

	/**
	 * Update default WooCommerce options
	 *
	 * @since 2.3.5
	 */
	public function woocommerce_set_default_options_callback() {

		add_action( 'plugins_loaded', function () {

			$operation = 'woocommerce_set_default_options';

			// Set the operation as completed if the store is active for more than 60 minutes.
			if ( WCAdminHelper::is_wc_admin_active_for( 60 * MINUTE_IN_SECONDS ) ) {
				update_option( $this->option_prefix . $operation, 'completed', 'no' );
				$this->write_to_log( $operation, 'completed (60 minutes)' );

				return;
			}

			// Delete woocommerce_demo_store, to avoid displaying the demo store notice.
			delete_option( 'woocommerce_demo_store' );

			update_option( $this->option_prefix . $operation, 'completed', 'no' );
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

	/**
	 * error_log wrapper
	 *
	 * @since 2.2.8
	 *
	 * @param string|array $message   Message.
	 *
	 * @param string       $operation Operation.
	 * @return void
	 */
	private function write_to_log( $operation, $message ) {
		error_log( 'WooExpress: Operation: ' . $operation . ': (' . microtime( true ) . ') ' . print_r( $message, 1 ) );
	}

	/**
	 * Maybe delete page by slug.
	 * If the page is older than 60 minutes, it will be ignored.
	 *
	 * @since 2.2.15
	 *
	 * @param string $operation Operation.
	 *
	 * @param string $slug      Slug.
	 * @return void
	 */
	private function maybe_delete_page_by_slug( $slug, $operation ) {

		// Sanitize slug, as it might contain invalid characters when translated and get_page_by_path will fail.
		$slug = sanitize_title( $slug );

		$page = get_page_by_path( $slug, ARRAY_A );
		if ( ! is_array( $page ) || ! isset( $page['ID'] ) ) {
			return;
		}

		$page_gmt_ts = get_post_time( 'U', true, $page['ID'] );
		// draft pages don't have a post_date_gmt, so we need to calculate it.
		if ( false === $page_gmt_ts ) {
			$page_gmt_ts = get_gmt_from_date( $page['post_date'], 'U' );
		}
		$current_time_gmt_ts = current_time( 'U', true );
		$diff_ts             = $current_time_gmt_ts - $page_gmt_ts;

		if ( $diff_ts > 60 * MINUTE_IN_SECONDS ) {
			$this->write_to_log( $operation, 'ignored page deletion ' . $slug . ' diff: ' . $diff_ts / 60 . ' minutes (older than 60 minutes) ' );

			return;
		}

		$result = wp_delete_post( $page['ID'], true );
		clean_post_cache( $page['ID'] );
		if ( $result ) {
			$this->write_to_log( $operation, 'deleted page ' . $slug );
		} else {
			$this->write_to_log( $operation, 'failed to delete page ' . $slug );
		}

	}
}

WC_Calypso_Bridge_Setup::get_instance();
