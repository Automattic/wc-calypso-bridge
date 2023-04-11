<?php
/**
 * Control wc-admin features in the eCommerce Plan.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\PageController;

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
	 * Get class instance
	 */
	public static function get_instance() {
		if ( ! static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Only in Ecommerce.
		if ( ! wc_calypso_bridge_has_ecommerce_features() ) {
			return;
		}

		add_action( 'plugins_loaded', array( $this, 'initialize' ), 2 );
	}

	/**
	 * Initialize.
	 */
	public function initialize() {

		add_filter( 'wc_admin_get_feature_config', array( $this, 'maybe_remove_devdocs_menu_item' ) );
		add_filter( 'woocommerce_admin_features', array( $this, 'filter_wc_admin_enabled_features' ) );
		add_filter( 'woocommerce_admin_get_feature_config', array( $this, 'filter_woocommerce_admin_features' ), PHP_INT_MAX );

		/**
		 * Hide WC Admin's activity panel in all pages except Home.
		 *
		 * @param  mixed  $value
		 * @return array
		 */
		add_filter( 'admin_body_class', array( $this, 'filter_woocommerce_body_classes' ) );
		add_action( 'admin_init', array( $this, 'add_custom_activity_panels_styles' ) );
		add_action( 'admin_footer', array( $this, 'filter_woocommerce_body_classes_js' ) );

		/**
		 * Skip the OBW.
		 *
		 * This callback will ensure that the `woocommerce_onboarding_profile` option value will result to skipped state, always.
		 *
		 * @since 1.9.4
		 *
		 * @param  mixed  $value
		 * @return array
		 */
		add_filter( 'option_woocommerce_onboarding_profile', static function ( $option_value ) {
			$value = $option_value ?? array();
			$value['skipped'] = true;
			return $value;
		}, 100 );

		/**
		 * Disable WooCommerce Navigation.
		 *
		 * @since   1.9.4
		 *
		 * @param mixed $pre Fixed to false.
		 * @return string no.
		 */
		add_filter( 'pre_option_woocommerce_navigation_enabled', static function ( $pre ) {
			return 'no';
		}, PHP_INT_MAX );
	}

	/**
	 * Remove the Dev Docs menu item unless allowed by the `wc_calypso_bridge_development_mode` filter.
	 *
	 * @param array $features WooCommerce Admin enabled features list.
	 */
	public function maybe_remove_devdocs_menu_item( $features ) {
		if ( ! apply_filters( 'wc_calypso_bridge_development_mode', false ) ) {
			unset( $features['devdocs'] );
		}

		return $features;
	}

	/**
	 * Set feature flags for WooCommerce Admin front end at run time.
	 *
	 * @param array $features Array of wc-calypso-bridge features that are enabled by default for the current env.
	 *
	 * @return array
	 */
	public function filter_wc_admin_enabled_features( $features ) {
		if ( ! in_array( 'remote-inbox-notifications', $features, true ) ) {
			$features[] = 'remote-inbox-notifications';
		}

		return $features;
	}

	/**
	 * Enable/disable features for WooCommerce Admin .
	 *
	 * @param array $features Array containing all wc-calypso-bridge features (enabled and disabled).
	 *
	 * @return array
	 */
	public function filter_woocommerce_admin_features( $features ) {

		// Disable and revert the navigation experiment.
		if ( array_key_exists( 'navigation', $features ) ) {
			$features['navigation'] = false;
		}

		return $features;
	}

	/**
	 * Add is-woocommerce-home body class.
	 *
	 * @since   1.9.5
	 *
	 * @param string $classes Body classes.
	 * @return string
	 */
	public function filter_woocommerce_body_classes( $classes ) {

		$page = PageController::get_instance()->get_current_page();

		if ( $page && 'woocommerce-home' === $page['id'] ) {
			$classes .= ' is-woocommerce-home';
		}

		return $classes;
	}

	/**
	 * Add is-woocommerce-home body class when url changes between wc-admin pages.
	 *
	 * @since   1.9.9
	 *
	 * @return void
	 */
	public function filter_woocommerce_body_classes_js() {
		?>
		<script>
			( function() {

				// Bail out early.
				if ( ! document.body.classList.contains( 'woocommerce_page_wc-admin' ) ) {
					return;
				}

				let url = location.href;
				document.body.addEventListener( 'click', ( event ) => {

					requestAnimationFrame( () => {
						// URL has changed - let the magic happen.
						if ( url !== location.href ) {
							url          = location.href;
							const params = ( new URL( location.href ) ).searchParams;

							if (
								'wc-admin' === params.get( 'page' )
								&& null === params.get( 'path' )
							) {
								document.body.classList.add( 'is-woocommerce-home' );
							} else {
								document.body.classList.remove( 'is-woocommerce-home' );
							}
						}
					} );

				}, true );
			} )();
		</script>
	<?php }

	/**
	 * Add custom CSS to hide activity panels in all WooCommerce pages other than Home.
	 * Note that this is not possible via the 'woocommerce_admin_features' filter, as we don't have access to the screen id at that point.
	 *
	 * @since   1.9.5
	 *
	 * @return void
	 */
	public function add_custom_activity_panels_styles() {

		wp_register_style( 'activity-panels-hide', false );
		wp_enqueue_style( 'activity-panels-hide' );

		$css = 'body:not(.is-woocommerce-home) #wpbody { margin-top: 0 !important; } body:not(.is-woocommerce-home) .woocommerce-layout__header { display:none; } body.is-woocommerce-home #screen-meta-links { display: none; } body.is-woocommerce-home .woocommerce-layout__header-heading, body.is-woocommerce-home .woocommerce-task-progress-header__title, .woocommerce-layout__inbox-title span { font-size: 20px; font-weight: 400; } body.is-woocommerce-home .woocommerce-layout__inbox-panel-header { padding: 0; } .woocommerce-layout__inbox-subtitle { margin-top: 5px; } .woocommerce-layout__inbox-subtitle span { color: #757575; }';
		wp_add_inline_style( 'activity-panels-hide', $css );
	}
}

WC_Calypso_Bridge_WooCommerce_Admin_Features::get_instance();
