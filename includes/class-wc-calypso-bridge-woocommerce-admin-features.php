<?php
/**
 * Control wc-admin features in the eCommerce Plan.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.3.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC EComm Bridge
 */
class WC_Calypso_Bridge_WooCommerce_Admin_Features {

	/**
	 * Class Instance.
	 *
	 * @var WC_Calypso_Bridge_WooCommerce_Admin_Features instance
	 */
	protected static $instance = null;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'initialize' ), 2 );
	}

	/**
	 * Add hooks and filters if WooCommerce is active.
	 */
	public function initialize() {
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		add_filter( 'woocommerce_admin_features', array( $this, 'filter_wc_admin_enabled_features' ) );
		add_filter( 'woocommerce_get_sections_advanced', array( __CLASS__, 'add_features_section' ), 20 );
		add_filter( 'woocommerce_get_settings_advanced', array( __CLASS__, 'add_features_settings' ), 20, 2 );
	}

	/**
	 * Set feature flags for WooCommerce Admin front end at run time.
	 *
	 * @param array $features Array of wc-calypso-bridge features that are enabled by default for the current env.
	 * @return array
	 */
	public function filter_wc_admin_enabled_features( $features ) {
		if ( ! in_array( 'remote-inbox-notifications', $features, true ) ) {
			$features[] = 'remote-inbox-notifications';
		}

		if ( ! in_array( 'navigation', $features, true ) && 'yes' === get_option( 'woocommerce_navigation_enabled', 'yes' ) ) {
			$features[] = 'navigation';
		}

		return $features;
	}

	/**
	 * Adds the Features section to the advanced tab of WooCommerce Settings
	 *
	 * @todo This should be removed once the WC version included with the ecommerce plan contains the bundled version of WCA 1.8.3.
	 *
	 * @param array $sections Sections.
	 * @return array
	 */
	public static function add_features_section( $sections ) {
		if ( ! isset( $sections['features'] ) ) {
			$sections['features'] = __( 'Features', 'wc-calypso-bridge' );
		}

		return $sections;
	}


	/**
	 * Adds the Features settings if it doesn't exist.
	 *
	 * @todo This should be removed once the WC version included with the ecommerce plan contains the bundled version of WCA 1.8.3.
	 *
	 * @param array  $settings Settings.
	 * @param string $current_section Current section slug.
	 * @return array
	 */
	public static function add_features_settings( $settings, $current_section ) {
		if ( 'features' !== $current_section ) {
			return $settings;
		}

		// Bail if the features section has alread been added.
		foreach ( $settings as $setting ) {
			if ( 'features_options' === $setting['id'] ) {
				return $settings;
			}
		}

		return apply_filters(
			'woocommerce_settings_features',
			array(
				array(
					'title' => __( 'Features', 'wc-calypso-bridge' ),
					'type'  => 'title',
					'desc'  => __( 'Start using new features that are being progressively rolled out to improve the store management experience.', 'wc-calypso-bridge' ),
					'id'    => 'features_options',
				),
				array(
					'title' => __( 'Navigation', 'wc-calypso-bridge' ),
					'desc'  => __( 'Adds the new WooCommerce navigation experience to the dashboard', 'wc-calypso-bridge' ),
					'id'    => 'woocommerce_navigation_enabled',
					'type'  => 'checkbox',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'features_options',
				),
			)
		);
	}

	/**
	 * Get class instance
	 */
	public static function get_instance() {
		if ( ! static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}
}

WC_Calypso_Bridge_WooCommerce_Admin_Features::get_instance();
