<?php
/**
 * WC Calypso Bridge Free Trial Plugins Screen - Landing page.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   x.x.x
 * @version x.x.x
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

		add_action( 'admin_menu', array($this, 'add_menu_page'));
		add_filter( 'admin_body_class', function( $classes ) {
				$screen = get_current_screen();
				if ( $screen && 'toplevel_page_plugins-upgrade' === $screen->id ) {
					$classes .= 'woocommerce_page_wc-bridge-landing-page woocommerce_page_wc-plugins-landing-page';
				}

				return $classes;
			} );
	}

	/**
	 * Initialize hooks.
	 */
	public function add_menu_page() {
		add_menu_page( __( 'Plugins', 'wc-calypso-bridge' ), __( 'Plugins', 'wc-calypso-bridge' ), 'manage_options', 'plugins-upgrade', array( $this, 'output' ), 'dashicons-admin-plugins', 65 );
	}

	public function output() {

		$upgrade_url = sprintf( 'https://wordpress.com/plans/%s', WC_Calypso_Bridge_Instance()->get_site_slug() );

		/**
		 * Addon page view.
		 *
		 * @uses $upgrade_url
		 */
		include_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/templates/html-admin-page-plugins-landing-page.php';
	}
}

WC_Calypso_Bridge_Free_Trial_Plugins_Screen::get_instance();
