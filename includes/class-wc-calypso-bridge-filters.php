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
		add_action( 'woocommerce_admin_onboarding_industries', array( $this, 'remove_not_allowed_industries' ), 10, 1 );
		add_filter( 'admin_footer', array( $this, 'add_documentation_js_filter' ) );

		// Turn off email notifications.
		add_filter( 'pre_option_woocommerce_merchant_email_notifications', array( $this, 'disable_email_notes' ) );
	}

	/**
	 * Remove `CBD and other hemp-derived products` option from industries list
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
	 * Filter method for industries to remove `CBD and other hemp-derived products` option.
	 *
	 * @param  array $industry Array of industries.
	 * @return boolean
	 */
	public function filter_industries( $industry ) {
		return 'cbd-other-hemp-derived-products' !== $industry['slug'];
	}

	/**
	 * Disable email based notifications.
	 */
	public function disable_email_notes() {
		return 'no';
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
}

$wc_calypso_bridge_filters = WC_Calypso_Bridge_Filters::get_instance();
