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

		add_filter( 'default_option_woocommerce_onboarding_profile', array( $this, 'set_business_extensions_empty' ), 10, 1 );
		add_filter( 'option_woocommerce_onboarding_profile', array( $this, 'set_business_extensions_empty' ), 10, 1 );
		add_filter( 'woocommerce_admin_onboarding_themes', array( $this, 'remove_non_installed_themes' ), 10, 1 );
		add_filter( 'woocommerce_admin_onboarding_industries', array( $this, 'remove_not_allowed_industries' ), 10, 1 );
		add_filter( 'wp_redirect', array( $this, 'prevent_mailchimp_redirect' ), 10, 2 );
		add_filter( 'woocommerce_admin_onboarding_product_types', array( $this, 'remove_paid_extension_upsells' ), 10, 2 );
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
			$location = admin_url( 'admin.php?page=wc-setup&calypsoify=1' );
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
	 * Site Profiler OBW: Remove CBD industry from industries list
	 *
	 * @param  array $industries Array of industries.
	 * @return array
	 */
	public function remove_not_allowed_industries( $industries ) {
		if ( isset( $industries['cbd-other-hemp-derived-products'] ) ) {
			unset( $industries['cbd-other-hemp-derived-products'] );
		} else {
			$industries = array_filter( $industries, array( $this, 'filter_industries' ) );
		}
		return $industries;
	}

	/**
	 * Site Profiler OBW: Filter method for industries to remove `CBD and other hemp-derived products` option.
	 *
	 * @param  array $industry Array of industries.
	 * @return boolean
	 */
	public function filter_industries( $industry ) {
		return 'cbd-other-hemp-derived-products' !== $industry['slug'];
	}
}

$wc_calypso_bridge_setup = WC_Calypso_Bridge_Setup::get_instance();
