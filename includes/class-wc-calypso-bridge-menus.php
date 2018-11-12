<?php
/**
 * Removes WooCommerce plugins/extensions from the main plugin management interface and puts them under a new 'Store' item.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Calypso_Bridge_Menus
 */
class WC_Calypso_Bridge_Menus {

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Bridge_Menus instance
	 */
	protected static $instance = false;

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
	 * Constructor
	 */
	private function __construct() {
		add_action( 'current_screen', array( $this, 'setup_menu_hooks' ) );
	}

	// TODO If any extensions add new pages to wp-admin's settings section, we will want to copy those over,
	// just like Calypsoify does in `add_plugin_menus`.

	/**
	 * Hooks into WordPress to overtake the menu system on WooCommerce pages.
	 */
	public function setup_menu_hooks() {
		// We want the menu handler hooks to run late, so that other plugins hooking in here can make changes first.
		$late_priority = 1000;
		if ( is_wc_calypso_bridge_page() ) {
			add_action( 'in_admin_header', array( $this, 'insert_sidebar_html' ) );
			remove_action( 'in_admin_header', array( Jetpack_Calypsoify::getInstance(), 'insert_sidebar_html' ) );

			add_action( 'admin_head', array( $this, 'woocommerce_menu_handler' ) );
		} else {
			add_action( 'admin_head', array( $this, 'calypsoify_menu_handler' ) );
		}
	}

	/**
	 * Creates a top level "WooCommerce" Calypso item, so that users can easily navigate back to the real Calypso.
	 *
	 * TODO: Final naming on this (Store vs eCommerce vs WooCommerce)
	 * TODO: id="calypso-woocommerce" instead, once we start loading some CSS.
	 */
	public function insert_sidebar_html() { ?>
		<a href="<?php echo esc_url( 'https://wordpress.com/stats/day/' . Jetpack::build_raw_urls( home_url() ) ); ?>" id="calypso-sidebar-header">
			<?php echo get_gridicon( 'gridicons-chevron-left' ); // WPCS: XSS ok. ?>
			<ul>
				<li id="calypso-sitename"><?php bloginfo( 'name' ); ?></li>
				<li id="calypso-plugins"><?php esc_html_e( 'WooCommerce', 'wc-calypso-bridge' ); ?></li>
			</ul>
		</a>
		<?php
	}

	/**
	 * Updates the menu handling on WooCommerce pages to only show WooCommerce navigation.
	 */
	public function woocommerce_menu_handler() {
		global $menu, $submenu;

		$wc_menus = wc_calypso_bridge_menu_slugs();

		foreach ( $menu as $menu_key => $menu_item ) {
			if ( ! in_array( $menu_item[2], $wc_menus, true ) ) {
				unset( $menu[ $menu_key ] );
			}
		}

	}

	/**
	 * Updates the menu handling on Calypsoified pages to only show plugin navigation.
	 */
	public function calypsoify_menu_handler() {
		global $menu, $submenu;

		$wc_menus = wc_calypso_bridge_menu_slugs();

		foreach ( $menu as $menu_key => $menu_item ) {
			if ( in_array( $menu_item[2], $wc_menus, true ) ) {
				unset( $menu[ $menu_key ] );
			}
		}
	}
}

$wc_calypso_bridge_menus = WC_Calypso_Bridge_Menus::get_instance();
