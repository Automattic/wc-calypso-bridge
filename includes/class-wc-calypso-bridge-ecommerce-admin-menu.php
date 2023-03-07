<?php

/**
 * Class Ecommerce_Atomic_Admin_Menu.
 *
 * @since   1.9.8
 * @version 2.0.8
 *
 * The admin menu controller for Ecommerce WoA sites.
 */
class Ecommerce_Atomic_Admin_Menu extends \Automattic\Jetpack\Dashboard_Customizations\Atomic_Admin_Menu {

	const WPCOM_ECOMMERCE_MANAGED_PAGES = array(
		'wc-admin',
		'wc-admin&path=/customers',
		'edit.php?post_type=shop_order',
		'wc-reports',
		'wc-settings',
		'wc-status',
		'wc-addons',
	);

	/**
	 * Override constructor and add custom actions.
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'admin_menu', array( $this, 'add_woocommerce_menu' ), 99999 );
		add_filter( 'menu_order', array( $this, 'menu_order' ), 100 );

		// Handle menu for ecommerce free trial.
		if ( wc_calypso_bridge_is_ecommerce_trial_plan() ) {
			$this->handle_free_trial_menu();
		}

		if ( ! $this->is_api_request ) {
			add_filter( 'submenu_file', array( $this, 'modify_woocommerce_menu_highlighting' ), 99999 );
		}

		// Move Orders.
		// TODO: What about the COT menu?
		add_filter( 'woocommerce_register_post_type_shop_order', function( $args ) {
			$args[ 'labels' ][ 'add_new' ] = __( 'Add New', 'woocommerce' );
			$args[ 'show_in_menu' ]        = true;
			$args[ 'menu_icon' ]           = 'dashicons-cart';
			return $args;
		} );

		/**
		 * Fix stale admin menu items for GC, BIS, and PRL.
		 *
		 * @since   2.0.5
		 * @version 2.0.6
		 */
		add_action( 'admin_menu', function() {

			// GC.
			if ( class_exists( 'WC_GC_Admin_Menus' ) ) {
				$this->hide_submenu_page( WC_GC_Admin_Menus::$parent_file, 'gc_activity' );
			}

			// BIS.
			$this->hide_submenu_page( 'woocommerce', 'bis_notifications' );
			$this->hide_submenu_page( 'woocommerce', 'bis_activity' );

			// PRL.
			$this->hide_submenu_page( 'woocommerce', 'prl_locations' );

		}, 9999 );
	}

	/**
	 * Modify admin menu for the Ecommerce Free Trial plan.
	 */
	protected function handle_free_trial_menu() {

		add_action( 'admin_menu', function() {

			// Hide Extensions > Manage.
			$this->hide_submenu_page( 'woocommerce', 'admin.php?page=wc-addons&section=helper' );

			// Move Feedback under Jetpack > Feedback.
			$this->hide_submenu_page( 'feedback', 'edit.php?post_type=feedback' );
			remove_menu_page( 'feedback' );
			add_submenu_page( 'jetpack', __( 'Feedback', 'wc-calypso-bridge' ), __( 'Feedback', 'wc-calypso-bridge' ), 'manage_woocommerce', 'edit.php?post_type=feedback', '', 10 );


			// Hide Tools > Marketing and Tools > Earn submenus.
			$site_suffix  = WC_Calypso_Bridge_Instance()->get_site_slug();
			$this->hide_submenu_page( 'tools.php', sprintf( 'https://wordpress.com/marketing/tools/%s', $site_suffix ) );
			$this->hide_submenu_page( 'tools.php', sprintf( 'https://wordpress.com/earn/%s', $site_suffix ) );

		}, 99999 );
	}

	/**
	 * Override the base implementation of add_plugins_menu() to avoid
	 * adding the Plugins menu for eCommerce trials.
	 *
	 * @since   2.0.8
	 * @version 2.0.8
	 *
	 * @return void
	 */
	public function add_plugins_menu() {
		if ( wc_calypso_bridge_is_ecommerce_trial_plan() ) {
			return;
		}

		return parent::add_plugins_menu();
	}

	/**
	 * Groups WooCommerce items.
	 */
	public static function menu_order( $menu_order ) {
		// Initialize our custom order array.
		$woocommerce_menu_order   = array();

		// Get the index of our managed pages for integrations.
		$payments_connect_exists  = in_array( 'wc-admin&path=/payments/connect', $menu_order );
		$payments_overview_exists = in_array( 'wc-admin&path=/payments/overview', $menu_order );
		$automatewoo_exists       = in_array( 'automatewoo', $menu_order );
		$mailpoet_exists          = in_array( 'mailpoet-newsletters', $menu_order );

		// Loop through menu order and do some rearranging.
		foreach ( $menu_order as $index => $item ) {

			// Move the WC group above the "Posts" menu item.
			if ( 'edit.php' === $item ) {

				$woocommerce_menu_order[] = 'wc-calypso-bridge-separator-top'; // Separator WC top.
				$woocommerce_menu_order[] = 'edit.php?post_type=shop_order'; // Orders.
				$woocommerce_menu_order[] = 'edit.php?post_type=product'; // Products.
				$woocommerce_menu_order[] = 'admin.php?page=wc-admin&path=/customers'; // Customers.
				if ( false !== $payments_connect_exists ) {
					$woocommerce_menu_order[] = 'wc-admin&path=/payments/connect'; // Payments.
				} elseif ( false !== $payments_overview_exists ) {
					$woocommerce_menu_order[] = 'wc-admin&path=/payments/overview'; // Payments.
				}

				$woocommerce_menu_order[] = 'wc-admin&path=/analytics/overview'; // Analytics.
				$woocommerce_menu_order[] = 'woocommerce-marketing'; // Marketing.
				if ( false !== $automatewoo_exists ) {
					$woocommerce_menu_order[] = 'automatewoo'; // AutomateWoo.
				}
				$woocommerce_menu_order[] = 'woocommerce'; // Extensions.
				$woocommerce_menu_order[] = 'separator-woocommerce'; // Separator WC.
				$woocommerce_menu_order[] = $item; // Posts.

			// Move "Mailpoet" below the "Jetpack" menu item.
			} elseif ( false !== $mailpoet_exists && 'jetpack' === $item ) {
				$woocommerce_menu_order[] = $item; // Jetpack.
				$woocommerce_menu_order[] = 'mailpoet-newsletters'; // Mailpoet.

			} elseif ( ! in_array( $item, array(
				'mailpoet-newsletters',
				'automatewoo',
				'wc-admin&path=/payments/connect',
				'wc-admin&path=/payments/overview',
				'wc-calypso-bridge-separator-top',
				'separator-woocommerce',
				'woocommerce',
				'woocommerce-marketing',
				'wc-admin&path=/analytics/overview',
				'edit.php?post_type=product',
				'edit.php?post_type=shop_order',
				'admin.php?page=wc-admin&path=/customers'
			), true ) ) {
				$woocommerce_menu_order[] = $item;
			}
		}

		// Return order.
		return $woocommerce_menu_order;
	}

	/**
	 * Adds My Home menu.
	 */
	public function add_my_home_menu() {
		$this->update_menu( 'index.php', 'admin.php?page=wc-admin', __( 'My Home', 'jetpack' ), 'edit_posts', 'dashicons-admin-home' );
	}

	/**
	 * Fixes the menu highlighting based on the changes of the self::add_woocommerce_menu.
	 *
	 * @param  string  $submenu_file
	 * @return string
	 */
	public function modify_woocommerce_menu_highlighting( $submenu_file ) {
		global $parent_file, $submenu_file, $plugin_page, $current_screen, $pagenow;
		// We change the global $plugin_page due to the get_admin_page_parent() that replaces parent_file with this.

		// Move WooCommerce > Settings to Settings > WooCommerce.
		$screen_id = is_a( $current_screen, 'WP_Screen' ) ? $current_screen->id : '';
		if ( in_array( $screen_id, array( 'woocommerce_page_wc-settings' ), true ) ) {
			$plugin_page  = 'options-general.php';
			$submenu_file = 'admin.php?page=wc-settings';
		}

		// Move WooCommerce > Status to Tools > WooCommerce Status.
		if ( in_array( $screen_id, array( 'woocommerce_page_wc-status' ), true ) ) {
			$plugin_page  = 'tools.php';
			$submenu_file = 'admin.php?page=wc-status';
		}

		// Fix WC Home highlight.
		if ( in_array( $screen_id, array( 'woocommerce_page_wc-admin' ), true ) ) {
			$plugin_page  = 'admin.php?page=wc-admin';
			$submenu_file = 'admin.php?page=wc-admin';
		}

		// Move WooCommerce > Extensions (My subscriptions tab) to Extensions > Manage.
		if ( in_array( $screen_id, array( 'woocommerce_page_wc-addons' ), true ) && isset( $_GET[ 'section' ] ) && 'helper' === $_GET[ 'section' ] ) {
			$plugin_page  = 'woocommerce';
			$submenu_file = 'admin.php?page=wc-addons&section=helper';
		}

		// Move WooCommerce > Reports to Anaytics > Legacy Reports.
		if ( in_array( $screen_id, array( 'woocommerce_page_wc-reports' ), true ) ) {
			$plugin_page  = 'wc-admin&path=/analytics/overview';
			$submenu_file = 'admin.php?page=wc-reports';
		}

		// Move Feedback to Jetpack > Feedback (Free trial).
		if ( wc_calypso_bridge_is_ecommerce_trial_plan() && in_array( $screen_id, array( 'edit-feedback' ) ) ) {
			$plugin_page = 'jetpack';
			$parent_file = 'jetpack';
			$submenu_file = 'edit.php?post_type=feedback';
			// Force the `get_admin_page_parent` core function to avoid handling this as a CPT.
			$pagenow = null;
			// Fix the typenow global, after the menu print.
			add_action('adminmenu', static function() {
				global $typenow;
				$typenow = 'edit.php';
			} );
		}

		return $submenu_file;
	}

	/**
	 * Handle WooCommerce menu.
	 */
	public function add_woocommerce_menu() {
		global $submenu, $menu;

		// Seperator1 gets removed on Atomic_Admin_Menu class.
		// We add one more here to be used on top of the WC group.
		$separator_top = array(
			'',                   // Menu title (ignored).
			'manage_woocommerce', // Required capability.
			'wc-calypso-bridge-separator-top',  // URL or file (ignored, but must be unique).
			'',                   // Page title (ignored).
			'wp-menu-separator',  // CSS class. Identifies this item as a separator.
		);

		$this->set_menu_item( $separator_top, null );

		// Hide WooCommerce > Home.
		$this->hide_submenu_page( 'woocommerce', 'wc-admin' );

		// Restore the Orders submenu page for backwards compatibility.
		add_submenu_page( 'woocommerce', __( 'Orders', 'wc-calypso-bridge' ), __( 'Orders', 'wc-calypso-bridge' ), 'manage_woocommerce', 'edit.php?post_type=shop_order', '', 1 );
		$this->hide_submenu_page( 'woocommerce', 'edit.php?post_type=shop_order' );

		// Move WooCommerce > Settings under Settings > WooCommerce.
		$this->hide_submenu_page( 'woocommerce', 'wc-settings' );
		add_submenu_page( 'options-general.php', __( 'WooCommerce Settings', 'wc-calypso-bridge' ), __( 'WooCommerce', 'wc-calypso-bridge' ), 'manage_woocommerce', 'admin.php?page=wc-settings', '', 10 );

		// Move WooCommerce > Status under Tools > WooCommerce Status.
		$this->hide_submenu_page( 'woocommerce', 'wc-status' );
		add_submenu_page( 'tools.php', __( 'WooCommerce Status', 'wc-calypso-bridge' ), __( 'WooCommerce Status', 'wc-calypso-bridge' ), 'manage_woocommerce', 'admin.php?page=wc-status', '', 10 );

		// Move legacy reports.
		$this->hide_submenu_page( 'woocommerce', 'wc-reports' );
		// Force-add the "Report (Legacy)" submenu item.
		if ( ! empty( $submenu['wc-admin&path=/analytics/overview'] ) && is_array( $submenu['wc-admin&path=/analytics/overview'] ) ) {
			$submenu['wc-admin&path=/analytics/overview'][] = array(
				__( 'Legacy Reports', 'wc-calypso-bridge' ),
				'manage_woocommerce',
				'admin.php?page=wc-reports',
				'Legacy Reports'
			);
		}

		if ( class_exists( '\Automattic\WooCommerce\Admin\Features\Features' ) && \Automattic\WooCommerce\Admin\Features\Features::is_enabled( 'analytics' ) ) {
			// Move Customers to root menu.
			$this->hide_submenu_page( 'woocommerce', 'wc-admin&path=/customers' );
			add_menu_page( __( 'Customers', 'woocommerce' ), __( 'Customers', 'woocommerce' ), 'manage_woocommerce', 'admin.php?page=wc-admin&path=/customers', null, 'dashicons-money', 100 );
		}

		// Update WooCommerce to Extensions
		$this->update_menu( 'woocommerce', null, __( 'Extensions', 'woocommerce' ), null, null, null );

		// Add Extensions > Manage submenu.
		add_submenu_page( 'woocommerce', __( 'WooCommerce Subscriptions', 'wc-calypso-bridge' ), __( 'Manage', 'wc-calypso-bridge' ), 'manage_woocommerce', 'admin.php?page=wc-addons&section=helper', '', 10 );

		// Move WooCommerce > Extensions under Extensions > Discover.
		foreach ( $submenu['woocommerce'] as $key => $data ) {
			if ( 'wc-addons' !== $data[2] ) {
				continue;
			}
			$submenu['woocommerce'][ $key ][0] = __( 'Discover', 'wc-calypso-bridge' );
		}

		// Add Orders count.
		if ( apply_filters( 'woocommerce_include_processing_order_count_in_menu', true ) && current_user_can( 'edit_others_shop_orders' ) ) {
			$order_count = (int) apply_filters( 'woocommerce_menu_order_count', wc_processing_order_count() );

			if ( $order_count ) {
				foreach ( $menu as $i => $menu_item ) {
					if ( 'edit.php?post_type=shop_order' === $menu_item[2] ) {
						$menu[ $i ][0] .= ' <span class="awaiting-mod update-plugins count-' . esc_attr( $order_count ) . '"><span class="processing-count">' . number_format_i18n( $order_count ) . '</span></span>'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						break;
					}
				}
			}
		}

		// Re-order submenus.
		$this->reorder_woocommerce_menu();
		$this->reorder_settings_menu();
		$this->reorder_analytics_menu();
	}

	/**
	 * Re-Order WooCommerce submenu.
	 */
	private function reorder_woocommerce_menu() {
		global $submenu;

		if ( ! empty( $submenu['woocommerce'] ) && is_array( $submenu['woocommerce'] ) ) {

			// Make sure that the managed and hidden are the last on the submenu list. This is make the parent "WooCommerce" item to point to an extension instead of a hidden page.
			uasort( $submenu['woocommerce'], function ( $a, $b ) {

				// Helper weights.
				$A = 1;
				$B = 1;
				if ( in_array( $a[2], self::WPCOM_ECOMMERCE_MANAGED_PAGES ) ) {
					if ( 'wc-addons' === $a[2] ) {
						$A = 0;
					} else {
						$A = 3;
					}
				}

				if ( 'admin.php?page=wc-addons&section=helper' === $a[2] ) {
					$A = 2;
				}

				if ( in_array( $b[2], self::WPCOM_ECOMMERCE_MANAGED_PAGES ) ) {
					if ( 'wc-addons' === $b[2] ) {
						$B = 0;
					} else {
						$B = 3;
					}
				}

				if ( 'admin.php?page=wc-addons&section=helper' === $b[2] ) {
					$B = 2;
				}

				if ( $A == $B ) {
					return 0;
				}

				return ( $A < $B ) ? -1 : 1;
			} );
		}
	}

	/**
	 * Re-Order Settings submenu.
	 */
	private function reorder_settings_menu() {
		global $submenu;

		// Order options-general.php -- ensure WooCommerce is right after "General".
		if ( ! empty( $submenu['options-general.php'] ) && is_array( $submenu['options-general.php'] ) ) {

			uasort( $submenu['options-general.php'], function ( $a, $b ) {

				// Helper weights.
				$A = 2;
				$B = 2;

				if ( false !== strpos( $a[2], 'https://wordpress.com/settings/general/' ) ) {
					$A = 0;
				} elseif ( 'admin.php?page=wc-settings' === $a[2] ) {
					$A = 1;
				}

				if ( false !== strpos( $b[2], 'https://wordpress.com/settings/general/' ) ) {
					$B = 0;
				} elseif ( 'admin.php?page=wc-settings' === $b[2] ) {
					$B = 1;
				}

				if ( $A == $B ) {
					return 0;
				}

				return ( $A < $B ) ? -1 : 1;
			} );
		}
	}

	/**
	 * Re-Order Analytics submenu.
	 */
	private function reorder_analytics_menu() {
		global $submenu;

		// Order wc-admin&path=/analytics/overview -- ensure settings are always last and "Legacy Reports" is the one above it.
		if ( ! empty( $submenu['wc-admin&path=/analytics/overview'] ) && is_array( $submenu['wc-admin&path=/analytics/overview'] ) ) {

			uasort( $submenu['wc-admin&path=/analytics/overview'], function ( $a, $b ) {

				// Helper weights.
				$A = 1;
				$B = 1;

				if ( 'admin.php?page=wc-reports' === $a[2] ) {
					$A = 2;
				} elseif ( 'wc-admin&path=/analytics/settings' === $a[2] ) {
					$A = 3;
				}

				if ( 'admin.php?page=wc-reports' === $b[2] ) {
					$B = 2;
				} elseif ( 'wc-admin&path=/analytics/settings' === $b[2] ) {
					$B = 3;
				}

				if ( $A == $B ) {
					return 0;
				}

				return ( $A < $B ) ? -1 : 1;
			} );
		}
	}

	/**
	 * Introduce 'Settings > Anti-Spam' and remove 'Settings > Jetpack' from Settings.
	 */
	public function add_options_menu() {
		parent::add_options_menu();

		// Introduce 'Settings > Anti-Spam'.
		add_submenu_page( 'options-general.php', __( 'Anti-Spam', 'wc-calypso-bridge' ), __( 'Anti-Spam', 'wc-calypso-bridge' ), 'manage_options', 'akismet-key-config', array( 'Akismet_Admin', 'display_page' ), 12 );
		// Remove 'Settings > Jetpack' from Settings.
		remove_submenu_page( 'options-general.php', 'https://wordpress.com/settings/jetpack/' . $this->domain );
	}

	/**
	 * Update the Jetpack menu.
	 */
	public function add_jetpack_menu() {

		global $submenu;

		parent::add_jetpack_menu();

		// Remove Jetpack Search menu item. Already exposed in the Jetpack Dashboard.
		$this->hide_submenu_page( 'jetpack', 'jetpack-search' );

		// Move Akismet under Settings
		$this->hide_submenu_page( 'jetpack', 'akismet-key-config' );

		// Move Jetpack status screen from 'Settings > Jetpack' to 'Tools > Jetpack Status'.
		add_submenu_page( 'tools.php', esc_attr__( 'Jetpack Status', 'wc-calypso-bridge' ), __( 'Jetpack Status', 'wc-calypso-bridge' ), 'manage_options', 'https://wordpress.com/settings/jetpack/' . $this->domain, null, 100 );

		add_submenu_page( 'jetpack', esc_attr__( 'Jetpack Stats', 'wc-calypso-bridge' ), __( 'Stats', 'wc-calypso-bridge' ), 'manage_options', 'https://wordpress.com/stats/day/' . $this->domain, null, 100 );

		// Order Jetpack submenu to have Dashboard first followed by Stats.
		if ( ! empty( $submenu['jetpack'] ) && is_array( $submenu['jetpack'] ) ) {

			uasort( $submenu['jetpack'], function ( $a, $b ) {

				// Helper weights.
				$A = 2;
				$B = 2;

				if ( false !== strpos( $a[2], 'stats/day' ) ) {
					$A = 1;
				} elseif ( 'jetpack#/dashboard' === $a[2] ) {
					$A = 0;
				}

				if ( false !== strpos( $b[2], 'stats/day' ) ) {
					$B = 1;
				} elseif ( 'jetpack#/dashboard' === $b[2] ) {
					$B = 0;
				}

				if ( $A == $B ) {
					return 0;
				}

				return ( $A < $B ) ? -1 : 1;
			} );
		}
	}

	/**
	 * Remove Stats menu.
	 */
	public function add_stats_menu() {
	}
}
