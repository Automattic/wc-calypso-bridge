<?php

/**
 * Class Ecommerce_Atomic_Admin_Menu.
 *
 * @since   1.9.8
 * @version 1.9.8
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

		if ( ! $this->is_api_request ) {
			add_filter( 'submenu_file', array( $this, 'modify_woocommerce_menu_highlighting' ), 99999 );
		}

		// Move Orders.
		//
		// TODO: menu_order_count at class-wc-admin-menu.php -- woocommerce_include_processing_order_count_in_menu
		// TODO: What about the COT menu?
		add_filter( 'woocommerce_register_post_type_shop_order', function( $args ) {
			$args[ 'show_in_menu' ] = true;
			$args[ 'menu_icon' ]    = 'dashicons-cart';
			return $args;
		} );
	}

	/**
	 * Groups WooCommerce items.
	 */
	public static function menu_order( $menu_order ) {

		// Initialize our custom order array.
		$woocommerce_menu_order = array();

		// Get the index of our managed pages.
		$orders    = array_search( 'edit.php?post_type=shop_order', $menu_order, true );
		$customers = array_search( 'admin.php?page=wc-admin&path=/customers', $menu_order, true );
		$products  = array_search( 'edit.php?post_type=product', $menu_order, true );
		$analytics = array_search( 'wc-admin&path=/analytics/overview', $menu_order, true );
		$marketing = array_search( 'woocommerce-marketing', $menu_order, true );

		// Loop through menu order and do some rearranging.
		foreach ( $menu_order as $index => $item ) {

			$extensions                = array_search( 'woocommerce', $menu_order, true );
			$woocommerce_separator     = array_search( 'separator-woocommerce', $menu_order, true );
			$woocommerce_separator_top = array_search( 'wc-calypso-bridge-separator-top', $menu_order, true );

			// Move the WC group above the "Posts" menu item.
			if ( 'edit.php' === $item ) {
				$woocommerce_menu_order[] = 'wc-calypso-bridge-separator-top'; // Separator WC top.
				$woocommerce_menu_order[] = 'edit.php?post_type=shop_order'; // Orders.
				$woocommerce_menu_order[] = 'edit.php?post_type=product'; // Products.
				$woocommerce_menu_order[] = 'admin.php?page=wc-admin&path=/customers'; // Customers.
				$woocommerce_menu_order[] = 'wc-admin&path=/analytics/overview'; // Analytics.
				$woocommerce_menu_order[] = 'woocommerce-marketing'; // Marketing.
				$woocommerce_menu_order[] = 'woocommerce'; // Extensions.
				$woocommerce_menu_order[] = 'separator-woocommerce'; // Separator WC.
				$woocommerce_menu_order[] = $item; // Posts.
				unset( $menu_order[ $orders ] );
				unset( $menu_order[ $customers ] );
				unset( $menu_order[ $products ] );
				unset( $menu_order[ $analytics ] );
				unset( $menu_order[ $marketing ] );
				unset( $menu_order[ $extensions ] );
				unset( $menu_order[ $woocommerce_separator ] );
				unset( $menu_order[ $woocommerce_separator_top ] );
			} elseif ( ! in_array( $item, array( 'wc-calypso-bridge-separator-top', 'separator-woocommerce', 'woocommerce', 'woocommerce-marketing', 'wc-admin&path=/analytics/overview', 'edit.php?post_type=product', 'edit.php?post_type=shop_order', 'admin.php?page=wc-admin&path=/customers'), true ) ) {
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
		$this->update_menu( 'index.php', '/admin.php?page=wc-admin', __( 'My Home', 'jetpack' ), 'edit_posts', 'dashicons-admin-home' );
	}

	/**
	 * Fixes the menu highligting based on the changes of the self::add_woocommerce_menu.
	 *
	 * @param  string  $submenu_file
	 * @return string
	 */
	public function modify_woocommerce_menu_highlighting( $submenu_file ) {
		global $parent_file, $submenu_file, $plugin_page, $current_screen;

		// Move WooCommerce > Settings under under Settings > WooCommerce.
		$screen_id = is_a( $current_screen, 'WP_Screen' ) ? $current_screen->id : '';
		if ( in_array( $screen_id, array( 'woocommerce_page_wc-settings' ), true ) ) {
			// We change the global $plugin_page due to the get_admin_page_parent() that replaces parent_file with this.
			$plugin_page  = 'options-general.php';
			$submenu_file = 'admin.php?page=wc-settings';
		}

		// Move WooCommerce > Status under under Tools > WooCommerce Status.
		if ( in_array( $screen_id, array( 'woocommerce_page_wc-status' ), true ) ) {
			// We change the global $plugin_page due to the get_admin_page_parent() that replaces parent_file with this.
			$plugin_page  = 'tools.php';
			$submenu_file = 'admin.php?page=wc-status';
		}

		if ( in_array( $screen_id, array( 'woocommerce_page_wc-admin' ), true ) ) {
			// We change the global $plugin_page due to the get_admin_page_parent() that replaces parent_file with this.
			// $parent_file  = 'admin.php?page=wc-admin';
			$plugin_page  = 'admin.php?page=wc-admin';
			$submenu_file = 'admin.php?page=wc-admin';
		}

		return $submenu_file;
	}

	/**
	 * Handle WooCommerce menu.
	 */
	public function add_woocommerce_menu() {

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
		add_submenu_page( 'woocommerce', __( 'Orders','wc-calypso-bridge' ), __( 'Orders','wc-calypso-bridge' ), 'manage_woocommerce', 'edit.php?post_type=shop_order', '', 1 );
		$this->hide_submenu_page( 'woocommerce', 'edit.php?post_type=shop_order' );

		// Move WooCommerce > Settings under Settings > WooCommerce.
		$this->hide_submenu_page( 'woocommerce', 'wc-settings' );
		add_submenu_page( 'options-general.php', __( 'WooCommerce Settings','wc-calypso-bridge' ), __( 'WooCommerce','wc-calypso-bridge' ), 'manage_woocommerce', 'admin.php?page=wc-settings', '', 10 );

		// Move WooCommerce > Status under Tools > WooCommerce Status.
		$this->hide_submenu_page( 'woocommerce', 'wc-status' );
		add_submenu_page( 'tools.php', __( 'WooCommerce Status','wc-calypso-bridge' ), __( 'WooCommerce Status','wc-calypso-bridge' ), 'manage_woocommerce', 'admin.php?page=wc-status', '', 10 );

		// Hide legacy reports.
		$this->hide_submenu_page( 'woocommerce', 'wc-reports' );

		// Move Customers to root menu.
		$this->hide_submenu_page( 'woocommerce', 'wc-admin&path=/customers' );
		add_menu_page( __( 'Customers', 'woocommerce' ), __( 'Customers', 'woocommerce' ), 'manage_woocommerce', '/admin.php?page=wc-admin&path=/customers', null, 'dashicons-money', 100 );

		// Update WooCommerce to Extensions
		$this->update_menu( 'woocommerce', null, 'Extensions', null, null, null );

		global $submenu, $menu;

		// Move WooCommerce > Extensions under Extensions > Discover.
		foreach ( $submenu['woocommerce'] as $key => $data ) {
			if ( 'wc-addons' !== $data[2] ) {
				continue;
			}
			$submenu['woocommerce'][$key][0] = __( 'Discover', 'wc-calypso-bridge' );
		}

		// Add Orders count.
		if ( apply_filters( 'woocommerce_include_processing_order_count_in_menu', true ) && current_user_can( 'edit_others_shop_orders' ) ) {
			$order_count = (int) apply_filters( 'woocommerce_menu_order_count', wc_processing_order_count() );

			if ( $order_count ) {
				foreach ( $menu as $i => $menu_item ) {
					if ( 'edit.php?post_type=shop_order' === $menu_item[2] ) {
						$menu[$i][0] .= ' <span class="awaiting-mod update-plugins count-' . esc_attr( $order_count ) . '"><span class="processing-count">' . number_format_i18n( $order_count ) . '</span></span>'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						break;
					}
				}
			}
		}

		if ( empty( $submenu['woocommerce'] ) || ! is_array( $submenu['woocommerce'] ) ) {
			// return;
		}

		// Make sure that the managed and hidden are the last on the submenu list. THis is make the parent "WooCommerce" item to point to an extension instead of a hidden page.
		uasort( $submenu['woocommerce'], function( $a, $b ) {

			// Helper weights.
			$A = 1;
			$B = 1;
			if ( in_array( $a[2], self::WPCOM_ECOMMERCE_MANAGED_PAGES ) ) {
				if ( 'wc-addons' === $a[2] ) {
					$A = 0;
				} else {
					$A = 2;
				}
			}

			if ( in_array( $b[2], self::WPCOM_ECOMMERCE_MANAGED_PAGES ) ) {
				if ( 'wc-addons' === $b[2] ) {
					$B = 0;
				} else {
					$B = 2;
				}
			}

			if ( $A == $B ) {
				return 0;
			}

			return ( $A < $B ) ? -1 : 1;
		} );
	}

	/**
	 * Remove 'Earn' from Tools. Merchants have an idea already about how they want to make money :)
	 */
	public function add_tools_menu() {
		parent::add_tools_menu();

		// Remove Earn from Tools.
		$this->hide_submenu_page( 'tools.php', 'https://wordpress.com/earn/' . $this->domain );
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
	}
}
