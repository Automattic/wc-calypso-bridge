<?php
/**
 * Adds the functionality needed to bridge the WooCommerce onboarding wizard.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 1.0.0
 */

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
	 * Constructor.
	 */
	private function __construct() {
		if ( ! class_exists( 'Jetpack_Calypsoify', false ) ) {
			return;
		}

		add_filter( 'admin_body_class', array( $this, 'add_calypsoify_class' ) );

		// If setup has yet to complete, make sure MailChimp doesn't redirect the flow.
		$has_finshed_setup = (bool) WC_Calypso_Bridge_Admin_Setup_Checklist::is_checklist_done();
		if ( ! $has_finshed_setup ) {
			add_filter( 'wp_redirect', array( $this, 'prevent_mailchimp_redirect' ), 10, 2 );
		}

		if ( isset( $_GET['page'] ) && 'wc-setup' === $_GET['page'] ) {
			add_filter( 'woocommerce_setup_wizard_steps', array( $this, 'remove_unused_steps' ) );
			add_filter( 'woocommerce_enable_setup_wizard', '__return_false' );
			add_action( 'wp_loaded', array( $this, 'setup_wizard' ), 20 );

			$jetpack_calypsoify = Jetpack_Calypsoify::getInstance();
			$wc_calypso_bridge  = WC_Calypso_Bridge::instance();

			add_action( 'admin_enqueue_scripts', array( $jetpack_calypsoify, 'enqueue' ), 20 );
			add_action( 'admin_print_styles', array( $wc_calypso_bridge, 'enqueue_calypsoify_scripts' ), 11 );
		}
	}

	/**
	 * Show the setup wizard.
	 */
	public function setup_wizard() {
		// Always tell Calypsoify to run during the setup wizard.
		update_user_meta( get_current_user_id(), 'calypsoify', 1 );
		include_once dirname( __FILE__ ) . '/class-wc-calypso-bridge-admin-setup-wizard.php';
	}

	/**
	 * Remove unused steps from the wizard
	 *
	 * @param array $default_steps Default steps used by WC wizard.
	 * @return array
	 */
	public function remove_unused_steps( $default_steps ) {
		$whitelist = array( 'store_setup', 'payment' );
		$steps     = array_intersect_key( $default_steps, array_flip( $whitelist ) );
		return $steps;
	}

	/**
	 * Add the calypsoify classes to the body tag.
	 *
	 * @param string $classes Space separated string of body classes.
	 * @return string
	 */
	public static function add_calypsoify_class( $classes ) {
		include_once dirname( __FILE__ ) . '/class-wc-calypso-bridge-page-controller.php';

		if ( function_exists( 'is_wc_calypso_bridge_page' ) && is_wc_calypso_bridge_page() ) {
			$classes .= ' calypsoify-active';
		}

		return $classes;
	}

	/**
	 * Prevent MailChimp redirect on initial setup.
	 *
	 * @param string $location Redirect location.
	 * @param string $status Status code.
	 * @return string
	 */
	public function prevent_mailchimp_redirect( $location, $status ) {
		if ( 'admin.php?page=mailchimp-woocommerce' === $location ) {
			// Delete the redirect option so we don't end up here anymore.
			delete_option( 'mailchimp_woocommerce_plugin_do_activation_redirect' );
			$location = admin_url( 'admin.php?page=wc-setup-checklist&calypsoify=1' );
		}

		return $location;
	}

}

$wc_calypso_bridge_setup = WC_Calypso_Bridge_Setup::get_instance();
