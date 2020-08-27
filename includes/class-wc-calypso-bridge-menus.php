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
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'current_screen', array( $this, 'setup_menu_hooks' ) );
		add_action( 'admin_menu', array( $this, 'change_woocommerce_menu_item_name' ), 100 );
		add_action( 'admin_menu', array( $this, 'remove_create_new_menu_items' ), 100 );

		add_action( 'admin_menu', array( $this, 'add_support_link' ) );
	}

	/**
	 * Manage site URL for Calypso
	 * 
	 * @return String URL to manage site on Calypso.
	 */
	public function manage_site_link(){
		$strip_http = '/.*?:\/\//i';
		$site_slug  = preg_replace( $strip_http, '', get_home_url() );
		$site_slug  = str_replace( '/', '::', $site_slug );

		$manage_site_url = 'https://wordpress.com/sites/' . $site_slug;
		return $manage_site_url;
	}

	/**
	 * Redirects the user back to Calypso.
	 */
	public function manage_site() {
		$redirect_url = $this->manage_site_link();

		wp_redirect( $redirect_url );
		exit;
	}

	/**
	 * Adds a support link to the sidebar.
	 */
	public function add_support_link() {
		add_menu_page(
			__( 'Support', 'wc-calypso-bridge' ),
			__( 'Support', 'wc-calypso-bridge' ),
			'manage_woocommerce',
			'wc-support',
			array( $this, 'support_link' ),
			'dashicons-editor-help',
			1000
		);
	}

	/**
	 * Redirects the user to support.
	 */
	public function support_link() {
		WC_Calypso_Bridge::record_event(
			'jetpack_atomic_wc_support_link_click',
			array(
				'source' => 'sidebar',
				'href'   => 'https://wordpress.com/help/contact',
			)
		);
		wp_redirect( 'https://wordpress.com/help/contact' );
		exit;
	}

	// TODO If any extensions add new pages to wp-admin's settings section, we will want to copy those over,
	// just like Calypsoify does in `add_plugin_menus`.

	/**
	 * Hooks into WordPress to overtake the menu system on WooCommerce pages.
	 */
	public function setup_menu_hooks() {
		if ( is_wc_calypso_bridge_page() ) {
			remove_action( 'in_admin_header', array( Jetpack_Calypsoify::getInstance(), 'insert_sidebar_html' ) );
			add_action( 'in_admin_header', array( $this, 'insert_sidebar_html' ) );
			add_action( 'admin_head', array( $this, 'woocommerce_menu_handler' ) );
		} else {
			add_action( 'admin_head', array( $this, 'calypsoify_menu_handler' ) );
		}
	}

	public function insert_sidebar_html() {
		$heading  =  __( 'Store', 'wc-calypso-bridge' );
		$home_url = $this->manage_site_link();
		?>
		<a href="<?php echo esc_url( $home_url ); ?>" id="calypso-sidebar-header">
			<svg class="gridicon gridicons-chevron-left" height="24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g><path d="M14 20l-8-8 8-8 1.414 1.414L8.828 12l6.586 6.586"></path></g></svg>

			<ul>
				<li id="calypso-sitename"><?php bloginfo( 'name' ); ?></li>
				<li id="calypso-plugins"><?php echo esc_html( $heading ); ?></li>
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

	/**
	 * Change the default WooCommerce menu item name to "Store"
	 */
	public function change_woocommerce_menu_item_name() {
		global $menu;
		foreach ( $menu as $key => $item ) {
			if ( 'woocommerce' === $item[2] ) {
				// @codingStandardsIgnoreStart
				$GLOBALS['menu'][ $key ][0] = esc_attr__( 'Store', 'wc-calypso-bridge' );
				// @codingStandardsIgnoreEnd
			}
		}
	}

	/**
	 * Remove all create new pages for custom post types
	 */
	public function remove_create_new_menu_items() {
		$post_types = (array) get_post_types(
			array(
				'show_ui'      => true,
				'_builtin'     => false,
				'show_in_menu' => true,
			)
		);

		foreach ( $post_types as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );
			$singular_name    = strtolower( $post_type_object->labels->singular_name );
			remove_submenu_page( 'edit.php?post_type=' . $post_type, 'post-new.php?post_type=' . $singular_name );
			remove_submenu_page( 'edit.php?post_type=' . $post_type, 'create_' . $singular_name );
		}
	}
}

$wc_calypso_bridge_menus = WC_Calypso_Bridge_Menus::get_instance();
