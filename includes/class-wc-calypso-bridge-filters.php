<?php
/**
 * Filters for the ecommerce plan.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.1.6
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
use Automattic\WooCommerce\Admin\Notes\Notes;

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

		// Hides all WooCommerce Database update notices.
		add_filter( 'woocommerce_rest_notes_object_query', array( $this, 'remove_update_note_type_from_args' ), 10, 2 );
		// Run after Automattic\WooCommerce\Admin\Loader.
		add_filter( 'woocommerce_components_settings', array( $this, 'component_settings' ), 30 );
		add_filter( 'woocommerce_shared_settings', array( $this, 'component_settings' ), 30 );
	}

	/**
	 * Adjust alert count to the component settings, to prevent loading state from being shown on db updates.
	 *
	 * @param array $settings Component settings.
	 */
	public function component_settings( $settings ) {
		$settings['alertCount'] = Notes::get_notes_count( array( 'error' ), array( 'unactioned' ) );
		return $settings;
	}

	/**
	 * Remove the "update" note type from REST API requests to surpress db notices from being displayed.
	 *
	 * @param array  $args REST request args
	 * @param object $request WP_REST_Request
	 */
	public function remove_update_note_type_from_args( $args, $request ) {
		// We only want to filter on the get notes route, and on requests that have a type arg.
		if ( '/wc-analytics/admin/notes' !== $request->get_route() || ! isset( $args[ 'type' ] ) ) {
			return $args;
		}

		// If the type `update` is being requested, remove it from args.
		$update_type_index = array_search( Automattic\WooCommerce\Admin\Notes\Note::E_WC_ADMIN_NOTE_UPDATE, $args[ 'type' ] );
		if ( $update_type_index ) {
			unset( $args[ 'type' ][ $update_type_index ] );
		}
		return $args;
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
