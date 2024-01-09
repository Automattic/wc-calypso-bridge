<?php
/**
 * Filters for the ecommerce plan.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.1.6
 * @version 2.3.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge Filters
 */
class WC_Calypso_Bridge_Filters {

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Filters instance
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

		// Only in Ecommerce.
		if ( ! wc_calypso_bridge_has_ecommerce_features() ) {
			return;
		}

		add_action( 'init', array( $this, 'init' ) );

		// Jetpack Sync is initialised from the 'plugins_loaded' action, so we need to do so as well.
		// Ref: https://github.com/Automattic/jetpack/blob/db92236462824dc73e4cf4602388fc0ded99e984/projects/packages/sync/src/class-main.php#L24-L25
		add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ) );
	}

	/**
	 * Initialize.
	 */
	public function init() {
		add_filter( 'admin_footer', array( $this, 'add_documentation_js_filter' ) );

		/**
		 * Disable email based notifications.
		 */
		add_filter( 'pre_option_woocommerce_merchant_email_notifications', static function() {
			return 'no';
		} );

		/**
		 * Filter recommended themes
		 */
		add_filter( '__experimental_woocommerce_rest_get_recommended_themes', array( $this, 'woocommerce_filter_get_recommended_themes'), 10, 3);
	}

	/**
	 * Initialization function that runs on the `plugins_loaded` action.
	 *
	 * @return void
	 */
	public function on_plugins_loaded() {
		add_filter( 'jetpack_sync_options_whitelist', array( $this, 'add_woocommerce_options_to_jetpack_sync' ) );
	}

	/**
	 * Add filter to js-based help documentation. This will modify the "Get Support" target link in the help documentation.
	 */
	public function add_documentation_js_filter() {
		?>
		<!-- WooCommerce JS Help documentation filter -->
		<script type="text/javascript">
			filterCalypsoDocumentation = function( helpDocumentationList ) {
				if ( helpDocumentationList ) {
					helpDocumentationList.map( ( item ) => {
						if ( item.title === 'Get Support' ) {
							item.link = 'https://wordpress.com/help';
						}
						return item;
					} )
				}
				return helpDocumentationList;
			}

			if ( window.wp && window.wp.hooks && window.wp.hooks.addFilter ) {
				window.wp.hooks.addFilter( "woocommerce_admin_setup_task_help_items", "woocommerce", filterCalypsoDocumentation );
			}
		</script>
		<?php
	}

	/**
	 * Function to hook into the `jetpack_sync_options_whitelist` filter
	 * and adds options related to the WooCommerce task list to the list of
	 * options Jetpack will synchronize to WordPress.com.
	 *
	 * @param array $allowed_options
	 * @return array
	 */
	public function add_woocommerce_options_to_jetpack_sync( $allowed_options ) {
		if ( ! is_array( $allowed_options ) ) {
			return $allowed_options;
		}

		$woocommerce_options = array(
			'woocommerce_task_list_complete',
			'woocommerce_task_list_completed_lists',
			'woocommerce_task_list_dismissed_tasks',
			'woocommerce_task_list_hidden_lists',
			'woocommerce_task_list_keep_completed',
			'woocommerce_task_list_tracked_completed_tasks',
			'woocommerce_admin_customize_store_completed_theme_id',
		);

		return array_merge( $allowed_options, $woocommerce_options );
	}

	/**
	 * Function to filter the theme recommendations for sites on WPCOM
	 *
	 * @since 2.2.18
	 *
	 * @param array $result
	 * @param string $industry
	 * @param string $currency
	 * @return array
	 */
	public function woocommerce_filter_get_recommended_themes( $result, $industry, $currency ) {
		$site_slug = WC_Calypso_Bridge_Instance()->get_site_slug();
		$current_theme_slug = get_stylesheet();

		$result['themes'] = array(
			array(
				'name'           => 'Tsubaki',
				'price'          => 'Free',
				'color_palettes' => array(),
				'total_palettes' => 0,
				'slug'           => 'tsubaki',
				'is_active'      => 'tsubaki' === $current_theme_slug,
				'thumbnail_url'  => 'https://i0.wp.com/s2.wp.com/wp-content/themes/premium/tsubaki/screenshot.png',
				'link_url'       => 'https://wordpress.com/theme/tsubaki/' . $site_slug . '?from=customize-store',
			),
			array(
				'name'           => 'Tazza',
				'price'          => 'Free',
				'color_palettes' => array(),
				'total_palettes' => 0,
				'slug'           => 'tazza',
				'is_active'      => 'tazza' === $current_theme_slug,
				'thumbnail_url'  => 'https://i0.wp.com/s2.wp.com/wp-content/themes/premium/tazza/screenshot.png',
				'link_url'       => 'https://wordpress.com/theme/tazza/' . $site_slug . '?from=customize-store',
			),
			array(
				'name'           => 'Amulet',
				'price'          => 'Free',
				'color_palettes' => array(
					array(
						'title'     => 'Default',
						'primary'   => '#FEFBF3',
						'secondary' => '#7F7E7A',
					),
					array(
						'title'     => 'Brown Sugar',
						'primary'   => '#EFEBE0',
						'secondary' => '#AC6239',
					),
					array(
						'title'     => 'Midnight',
						'primary'   => '#161514',
						'secondary' => '#AFADA7',
					),
					array(
						'title'     => 'Olive',
						'primary'   => '#FEFBF3',
						'secondary' => '#7F7E7A',
					),
				),
				'total_palettes' => 5,
				'slug'           => 'amulet',
				'is_active'      => 'amulet' === $current_theme_slug,
				'thumbnail_url'  => 'https://i0.wp.com/s2.wp.com/wp-content/themes/premium/amulet/screenshot.png',
				'link_url'       => 'https://wordpress.com/theme/amulet/' . $site_slug . '?from=customize-store',
			),
			array(
				'name'           => 'Zaino',
				'price'          => 'Free',
				'color_palettes' => array(
					array(
						'title'     => 'Default',
						'primary'   => '#202124',
						'secondary' => '#E3CBC0',
					),
					array(
						'title'     => 'Aubergine',
						'primary'   => '#1B1031',
						'secondary' => '#E1746D',
					),
					array(
						'title'     => 'Block out',
						'primary'   => '#FF5252',
						'secondary' => '#252525',
					),
					array(
						'title'     => 'Canary',
						'primary'   => '#FDFF85',
						'secondary' => '#353535',
					),
				),
				'total_palettes' => 11,
				'slug'           => 'zaino',
				'is_active'      => 'zaino' === $current_theme_slug,
				'thumbnail_url'  => 'https://i0.wp.com/s2.wp.com/wp-content/themes/premium/zaino/screenshot.png',
				'link_url'       => 'https://wordpress.com/theme/zaino/' . $site_slug . '?from=customize-store',
			),
		);

		$result['_links']['browse_all']['href'] = 'https://wordpress.com/themes/' . $site_slug;

		return $result;
	}

}

WC_Calypso_Bridge_Filters::get_instance();
