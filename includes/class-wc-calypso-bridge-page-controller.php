<?php
/**
 * Manages all of the admin pages that make up WooCommerce + WooCommerce Extensions
 * This includes registering support  and menu handlig.
 * Generally, the class is not used directly. The following helper functions can be used instead:
 *
 * Functions: wc_calypso_bridge_connect_page, is_wc_calypso_bridge_page, wc_calypso_bridge_get_current_screen_id().
 *
 * @package WC_Calypso_Bridge
 * @since 1.0.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns the current screen ID.
 * This is different from WP's get_current_screen, in that it attaches an action,
 * so certain pages like 'add new' pages can have different screen ids & handling.
 * It also catches some more unique dynamic pages like taxonomy/attribute management.
 *
 * Format: {$current_screen->action}-{$current_screen->id}-$tab,
 * {$current_screen->action}-{$current_screen->id} if no tab is present,
 * or just {$current_screen->id} if no action or tab is present.
 *
 * @return string Current screen ID.
 */
function wc_calypso_bridge_get_current_screen_id() {
	$current_screen = get_current_screen();

	if ( ! $current_screen ) {
		return false;
	}
	$current_screen_id = $current_screen->action ? $current_screen->action . '-' . $current_screen->id : $current_screen->id;
	if ( ! empty( $_GET['taxonomy'] ) && ! empty( $_GET['post_type'] ) && 'product' === $_GET['post_type'] ) { // WPCS: CSRF ok.
		$current_screen_id = 'product_page_product_attributes';
	}
	// Default tabs.
	$pages_with_tabs = apply_filters(
		'wc_calypso_bridge_pages_with_tabs',
		array(
			'wc-reports'  => 'orders',
			'wc-settings' => 'general',
			'wc-status'   => 'status',
		)
	);
	if ( ! empty( $_GET['page'] ) ) {
		if ( in_array( $_GET['page'], array_keys( $pages_with_tabs ), true ) ) {
			if ( ! empty( $_GET['tab'] ) ) {
				$tab = $_GET['tab']; // WPCS: input var ok, sanitization ok, CSRF ok.
			} else {
				$tab = $pages_with_tabs[ $_GET['page'] ]; // WPCS: input var ok, sanitization ok, CSRF ok.
			}
			$current_screen_id = $current_screen_id . '-' . $tab;
		}
	}

	$allowed_importers = array( 'woocommerce_coupon_csv', 'woocommerce_customer_csv', 'woocommerce_order_csv' );

	if ( ! empty( $_GET['import'] ) && in_array( $_GET['import'], $allowed_importers, true ) ) {
		return 'importer_' . $_GET['import']; // WPCS: input var ok, sanitization ok, CSRF ok.
	}

	return $current_screen_id;
}

/**
 * Connects a wp-admin page to a Calypso WooCommerce page.
 * The page will no longer be shown in the Calypsoified plugins section and will instead
 * show up under Store/WooCommerce
 *
 * They will also get Calypsoified styles.
 *
 * @param array $options {
 *   Array describing the page.
 *
 *   @type string      menu         wp-admin menu id/path.
 *   @type string      submenu      wp-admin submenu id/path.
 *   @type string      screen_id    WooCommerce screen ID (`wc_calypso_bridge_get_current_screen_id()`). Used for correctly identifying which pages are WooCommerce pages.
 * }
 */
function wc_calypso_bridge_connect_page( $options ) {
	$defaults = array(
		'menu' => '',
	);
	$options  = wp_parse_args( $options, $defaults );

	$wc_admin_page_controller = WC_Calypso_Bridge_Page_Controller::get_instance();

	if ( ! empty( $options['menu'] ) ) {
		$wc_admin_page_controller->register_menu( $options );
	}

	$wc_admin_page_controller->register_page( $options );
}

/**
 * Returns if we are on a WooCommerce related admin page.
 *
 * @return bool True if this is a WooCommerce admin page. False otherwise.
 */
function is_wc_calypso_bridge_page() {
	$controller         = WC_Calypso_Bridge_Page_Controller::get_instance();
	$pages              = $controller->get_registered_pages();
	$screen_id          = wc_calypso_bridge_get_current_screen_id();
	$is_registered_page = false;
	foreach ( $pages as $page ) {
		if ( $screen_id === $page['screen_id'] ) {
			$is_registered_page = true;
			break;
		}
	}
	return $is_registered_page;
}

/**
 * Returns an array of wp-admin menu slugs that are registered as woocommerce menu items.
 *
 * @return bool True if this is a WooCommerce admin page. False otherwise.
 */
function wc_calypso_bridge_menu_slugs() {
	$controller       = WC_Calypso_Bridge_Page_Controller::get_instance();
	$registered_menus = $controller->get_registered_menus();

	$wc_menus = array();
	foreach ( $registered_menus as $registered_menu ) {
		if ( ! empty( $registered_menu['menu'] ) ) {
			$wc_menus[] = $registered_menu['menu'];
		}
	}

	return $wc_menus;
}

/**
 * WC_Calypso_Bridge_Page_Controller.
 */
class WC_Calypso_Bridge_Page_Controller {

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Bridge_Page_Controller instance
	 */
	protected static $instance = false;

	/**
	 * Menu items.
	 *
	 * @var array
	 */
	private $menus = array();

	/**
	 * Registered pages
	 * Contains information (breadcrumbs, menu info) about JS powered pages and classic WooCommerce pages.
	 *
	 * @var array
	 */
	private $pages = array();

	/**
	 * We want a single instance of this class so we can accurately track registered menus and pages.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Registers a menu item.
	 *
	 * @param array $options Array describing the menu. See wc_calypso_bridge_connect_page.
	 */
	public function register_menu( $options ) {
		global $menu, $submenu;
		$this->menus[] = $options;
	}
	/**
	 * Registers a page.
	 *
	 * @param array $options Array describing the page. See wc_calypso_bridge_connect_page.
	 */
	public function register_page( $options ) {
		$this->pages[] = $options;
	}
	/**
	 * Returns an array of registered WooCommerce pages.
	 *
	 * @return array Array of registered pages.
	 */
	public function get_registered_pages() {
		return $this->pages;
	}
	/**
	 * Returns an array of registered WooCommerce menus.
	 *
	 * @return array Array of registered menus.
	 */
	public function get_registered_menus() {
		return $this->menus;
	}
}

$wc_calypso_bridge_page_controller = WC_Calypso_Bridge_Page_Controller::get_instance();
