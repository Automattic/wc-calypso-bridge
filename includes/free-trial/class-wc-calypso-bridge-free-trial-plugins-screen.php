<?php
/**
 * WC Calypso Bridge Free Trial Plugins Screen - Landing page.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   2.2.8
 * @version 2.2.8
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Calypso_Bridge_Free_Trial_Plugins_Screen Class.
 */
class WC_Calypso_Bridge_Free_Trial_Plugins_Screen {

	/**
	 * Class instance.
	 */
	protected static $instance = false;

	/**
	 * Get class instance.
	 */
	final public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	protected function __construct() {
		// Only for free trials.
		if ( ! wc_calypso_bridge_is_ecommerce_trial_plan() ) {
			return;
		}

		$this->init();
	}

	/**
	 * Initialize hooks.
	 */
	protected function init() {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'admin_menu', array( $this, 'remove_marketplace_menu' ), 100000 );
	}

	/**
	 * Initialize hooks.
	 */
	public function add_menu_page() {
		wc_admin_register_page(
			array(
				'id'        => 'woocommerce-wccom-plugins',
				'title'     => __( 'Plugins', 'wc-calypso-bridge' ),
				'nav_args'  => array(
					'title'  => __( 'Plugins', 'wc-calypso-bridge' ),
				),
				'path'=> '/plugins-upgrade',
				'position'  => 65,
				'icon'=>'dashicons-admin-plugins',
				'capability' => 'manage_options',
			)
		);
	}

	/**
	 * Remove the default Marketplace admin menu. Ecommerce trials have their own landing page for it.
	 *
	 * @return void
	 */
	public function remove_marketplace_menu() {
		$plugins_slug = 'https://wordpress.com/plugins/' . WC_Calypso_Bridge_Instance()->get_site_slug();
		remove_menu_page( $plugins_slug );

		/**
		 * Remove the Marketplace submenu when the site is using the classic interface.
		 */
		remove_menu_page( 'plugins.php' );
	}
}

WC_Calypso_Bridge_Free_Trial_Plugins_Screen::get_instance();
