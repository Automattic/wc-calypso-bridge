<?php
/**
 * Contains the logic for removing installed extensions from the search results.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.6
 * @version 2.0.8
 */

/**
 * WC Calypso Bridge Addons Screen
 */
class WC_Calypso_Bridge_Addons_Screen extends WC_Admin_Addons {
	/**
	 * Call API to get extensions.
	 *
	 * @param string $category Extension category.
	 * @param string $term Extension term.
	 * @param string $country Extension country.
	 *
	 * @return array of extensions
	 */
	public static function get_extension_data( $category, $term, $country ) {
		$parameters     = self::build_parameter_string( $category, $term, $country );
		$raw_extensions = wp_remote_get(
			'https://woocommerce.com/wp-json/wccom-extensions/1.0/search' . $parameters
		);
		if ( ! is_wp_error( $raw_extensions ) ) {
			$addons = json_decode( wp_remote_retrieve_body( $raw_extensions ) )->products;
		}

		return $addons;
	}

	/**
	 * Receives a plugin slug as plugin/plugin.php, and strips after the forward slash.
	 *
	 * @param  string $plugin_slug full plugin slug.
	 * @return string plugin directory name, with no attached file name.
	 */
	public static function prepare_plugin_directory_slug( $plugin_slug ) {
		$slug_pieces           = explode( '/', $plugin_slug );
		$plugin_directory_name = $slug_pieces[0];
		return $plugin_directory_name;
	}

	/**
	 * Handles output of the addons page in admin.
	 * See WooCommerce core for the original method within WC_Admin_Addons.
	 */
	public static function output() {

		if ( wc_calypso_bridge_is_ecommerce_trial_plan() ) {

			$upgrade_url = sprintf( 'https://wordpress.com/plans/%s', WC_Calypso_Bridge_Instance()->get_site_slug() );

			/**
			 * Addon page view.
			 *
			 * @uses $upgrade_url
			 */
			include_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/templates/html-admin-page-addons-landing-page.php';
			return;
		}

		if ( isset( $_GET['section'] ) && 'helper' === $_GET['section'] ) { // WPCS: CSRF ok.
			do_action( 'woocommerce_helper_output' );
			return;
		}

		if ( isset( $_GET['install-addon'] ) && 'woocommerce-services' === $_GET['install-addon'] ) { // WPCS: CSRF ok.
			self::install_woocommerce_services_addon();
		}

		$sections        = self::get_sections();
		$theme           = wp_get_theme();
		$current_section = isset( $_GET['section'] ) ? sanitize_text_field( wp_unslash( $_GET['section'] ) ) : '_featured'; // WPCS: CSRF ok.
		$addons          = array();

		if ( '_featured' !== $current_section ) {
			$category = isset( $_GET['section'] ) ? sanitize_text_field( wp_unslash( $_GET['section'] ) ) : null; // WPCS: CSRF ok.
			$term     = isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : null; // WPCS: CSRF ok.
			$country  = WC()->countries->get_base_country();
			$addons   = self::get_extension_data( $category, $term, $country );

			$plugins      = get_plugins();
			$plugin_slugs = array_map( 'self::prepare_plugin_directory_slug', array_keys( $plugins ) );

			foreach ( $addons as $key => $single_addon ) {
				if ( isset( $single_addon->slug ) && in_array( $single_addon->slug, $plugin_slugs, true ) ) {
					unset( $addons[ $key ] );
				}
			}
		}

		/**
		 * Addon page view.
		 *
		 * @uses $addons
		 * @uses $sections
		 * @uses $theme
		 * @uses $current_section
		 */
		include_once WC_ABSPATH . 'includes/admin/views/html-admin-page-addons.php';
	}
}
