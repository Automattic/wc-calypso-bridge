<?php
/**
 * Filters for the ecommerce plan.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.1.6
 * @version 1.0.0
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
	}

	/**
	 * Initialization function that runs on the `plugins_loaded` action.
	 *
	 * @return void
	 */
	public function on_plugins_loaded() {
		add_filter( 'jetpack_sync_options_whitelist', array( $this, 'add_woocommerce_task_list_options_to_jetpack_sync' ) );
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
	public function add_woocommerce_task_list_options_to_jetpack_sync( $allowed_options ) {
		if ( ! is_array( $allowed_options ) ) {
			return $allowed_options;
		}

		$woocommerce_task_list_options = array(
			'woocommerce_task_list_complete',
			'woocommerce_task_list_completed_lists',
			'woocommerce_task_list_dismissed_tasks',
			'woocommerce_task_list_hidden_lists',
			'woocommerce_task_list_keep_completed',
			'woocommerce_task_list_tracked_completed_tasks',
		);

		return array_merge( $allowed_options, $woocommerce_task_list_options );
	}
}

WC_Calypso_Bridge_Filters::get_instance();
