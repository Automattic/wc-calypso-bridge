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
		add_filter( 'default_option_woocommerce_onboarding_profile', array( $this, 'set_business_extensions_empty' ) );
		add_filter( 'option_woocommerce_onboarding_profile', array( $this, 'set_business_extensions_empty' ) );
		add_filter( 'default_option_woocommerce_navigation_enabled', array( $this, 'enable_navigation_by_default' ) );
		add_filter( 'woocommerce_admin_onboarding_themes', array( $this, 'remove_non_installed_themes' ) );
		add_filter( 'wp_redirect', array( $this, 'prevent_redirects_on_activation' ), 10, 2 );
		add_filter( 'woocommerce_admin_onboarding_product_types', array( $this, 'remove_paid_extension_upsells' ), 10, 2 );
		add_filter( 'pre_option_woocommerce_homescreen_enabled', array( $this, 'always_enable_homescreen' ) );
	}

	/**
	 * Opt all sites into using WooCommerce Home Screen.
	 */
	public function always_enable_homescreen() {
		return 'yes';
	}

	/**
	 * Prevent redirects on activation when WooCommerce is being setup. Some plugins
	 * do this when they are activated.
	 *
	 * @param string $location Redirect location.
	 * @param string $status Status code.
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
	 * Site Profiler OBW: Remove Paid Extensions
	 *
	 * @param  array $product_types Array of product types.
	 * @return array
	 */
	public function remove_paid_extension_upsells( $product_types ) {
		// Product Types are fetched from https://woocommerce.com/wp-json/wccom-extensions/1.0/search?category=product-type .
		$filtered_product_types = array_filter( $product_types, array( $this, 'filter_product_types' ) );
		return $filtered_product_types;
	}

	/**
	 * Site Profiler OBW: Filter method for product_types to remove items with product.
	 *
	 * @param  array $product_type Array of product type data.
	 * @return boolean
	 */
	public function filter_product_types( $product_type ) {
		return ! isset( $product_type['product'] );
	}

	/**
	 * Store Profiler: Set business_extenstions to empty array.
	 *
	 * @param array $option Array of properties for OBW Profile.
	 * @return array
	 */
	public function set_business_extensions_empty( $option ) {
		// Ensuring the option is an array by default.
		// By having an empty array of 'business_extensions' all options are toggled off by default in the OBW.
		if ( ! is_array( $option ) ) {
			$option = array(
				'business_extensions' => array(),
			);
		} else {
			$option['business_extensions'] = array();
		}

		return $option;
	}

	/**
	 * Enable the navigation feature by default.
	 *
	 * @return string
	 */
	public function enable_navigation_by_default() {
		return 'yes';
	}

	/**
	 * Remove non-installed ( paid ) themes from the Onboarding data source.
	 *
	 * @param array $themes Array of themes comprised of locally installed themes + marketplace themes.
	 * @return array
	 */
	public function remove_non_installed_themes( $themes ) {
		$local_themes = array_filter( $themes, array( $this, 'is_theme_installed' ) );
		return $local_themes;
	}

	/**
	 * Conditional method to determine if a theme is installed locally.
	 *
	 * @param array $theme Theme attributes.
	 * @return boolean
	 */
	public function is_theme_installed( $theme ) {
		return isset( $theme['is_installed'] ) && $theme['is_installed'];
	}

	/**
	 * Registers the WooCommerce Payments welcome page.
	 */
	public function register_payments_welcome_page() {
		wc_admin_register_page(
			array(
				'id'       => 'wc-calypso-bridge-payments-welcome-page',
				'title'    => __( 'Payments', 'wc-calypso-bridge' ),
				'path'     => '/payments-welcome',
			)
		);
	}
}

$wc_calypso_bridge_setup = WC_Calypso_Bridge_Setup::get_instance();
