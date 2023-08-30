<?php
/**
 * Tracks modifications for the ecommerce plan.
 *
 * Adjust tracks settings for business, ecomm, in calypsoified and wp-admin views.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.1.6
 * @version 2.2.12
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\WCAdminHelper;

/**
 * WC Calypso Bridge Tracks
 */
class WC_Calypso_Bridge_Tracks {

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Bridge_Tracks instance
	 */
	protected static $instance = false;

	/**
	 * Plugin host attribute for tracks.
	 *
	 * @var string
	 */
	public static $tracks_host_value = '';

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

		/**
		 * Init tracking configuration.
		 */
		add_action( 'init', array( $this, 'init' ) );

		if ( wc_calypso_bridge_has_ecommerce_features() ) {

			// Increase the frequency of the WC Tracker for the first three months.
			if ( ! WCAdminHelper::is_wc_admin_active_for( 3 * MONTH_IN_SECONDS ) ) {

				// Define constant so other plugins can check if this is set and adapt accordingly.
				wc_maybe_define_constant( 'WC_CALYPSO_BRIDGE_TRACKER_FREQUENCY', 'daily' );

				/**
				 * Increase WC Tracker's frequency from weekly to daily.
				 *
				 * @since   2.0.11
				 * @return int
				 */
				add_filter( 'woocommerce_tracker_last_send_interval', static function () {
					return strtotime( '-1 day' );
				}, PHP_INT_MAX );

			}
		}
	}

	/**
	 * Initialize.
	 */
	public function init() {
		$this->set_tracks_host_value();

		// Set track host and source.
		add_filter( 'admin_footer', array( $this, 'add_tracks_js_filter' ) );
		// Monkey patch for tracks id mismatch.
		// Runs at priority 23 which is before wcTracks is initialized.
		add_filter( 'admin_footer', array( $this, 'add_tracks_id_mismatch_monkey_patch' ), 23 );

		add_filter( 'woocommerce_tracks_event_properties', array( $this, 'add_tracks_php_filter' ), 10, 2 );
		add_filter( 'jetpack_woocommerce_analytics_event_props', array( $this, 'filter_jetpack_woocommerce_analytics_event_props' ) );
		add_filter( 'woocommerce_admin_survey_query', array( $this, 'set_survey_source' ) );

		// Hide WooCommerce.com advanced settings page.
		add_filter( 'woocommerce_get_sections_advanced', array( $this, 'hide_woocommerce_com_settings' ), 10, 1 );

		// Always opt-in to Tracks, WPCOM user tracks preferences take priority.
		add_filter( 'woocommerce_apply_tracking', '__return_true' );
		add_filter( 'woocommerce_apply_user_tracking', '__return_true' );
		add_filter( 'woocommerce_tracker_data', array( $this, 'add_host_to_wctracker_param' ) );
	}

	/**
	 * Add `host` key to woocommerce_tracker_data filter data
	 *
	 * @param array $data WC Tracker data from WC_Tracker class.
	 * @return array
	 */
	public function add_host_to_wctracker_param( $data ) {
		$data['host'] = self::$tracks_host_value;
		return $data;
	}

	/**
	 * Set's the value for the tracks host property.
	 */
	public function set_tracks_host_value() {
		// Default value assumes business plan, inside wp-admin.
		$host_value = 'bizplan-wp-admin';

		// Update host value according to plan. Ordering could be important
		// since some plans may have overlapping features.
		if ( wc_calypso_bridge_is_ecommerce_trial_plan() ) {
			$host_value = 'ecommplan-freetrial';
		} elseif ( wc_calypso_bridge_is_woo_express_performance_plan() ) {
			$host_value = 'woo-express-performance';
		} elseif ( wc_calypso_bridge_is_woo_express_essential_plan() ) {
			$host_value = 'woo-express-essentials';
		} elseif ( wc_calypso_bridge_is_wpcom_ecommerce_plan() ) {
			$host_value = 'ecommplan';
		} elseif ( wc_calypso_bridge_has_ecommerce_features() ) {
			$host_value = 'ecommplan';
		}

		self::$tracks_host_value = $host_value;
	}

	/**
	 * Add filter to js-based tracks events. This will add the host prop on all admin page tracks.
	 */
	public function add_tracks_js_filter() {
		?>
		<!-- WooCommerce JS Tracks Filter -->
		<script type="text/javascript">
			woocommerceTracksFilterProperties = function( properties, eventName ) {
				// let's add a host prop for all events.
				properties.host = '<?php echo esc_attr( self::$tracks_host_value ); ?>';
				return properties;
			}

			if ( window.wp && window.wp.hooks && window.wp.hooks.addFilter ) {
				window.wp.hooks.addFilter( "woocommerce_tracks_client_event_properties", "woocommerce", woocommerceTracksFilterProperties );
			}
		</script>
		<?php
	}

	/**
	 * Add a script to apply tracks ID mismatch in WooCommerce < 7.8.0.
	 * Issue: https://github.com/woocommerce/woocommerce/issues/38093
	 */
	public function add_tracks_id_mismatch_monkey_patch() {
		if (
			defined( 'WC_VERSION' ) &&
			version_compare( WC_VERSION, '7.8.0', '<' ) &&
			class_exists( '\WC_Tracks_Client' )
		) {
			$user            = wp_get_current_user();
			$tracks_identity = \WC_Tracks_Client::get_identity( $user->ID );
			if ( 'anon' !== $tracks_identity['_ut'] ) {
				?>
				<!-- WooCommerce Tracks ID mismatch fix -->
				<script type="text/javascript">
					window._tkq = window._tkq || [];
					window._tkq.push( [ 'identifyUser', '<?php echo esc_js( $tracks_identity['_ui'] ); ?>' ] );
				</script>
				<?php
			}
		}
	}

	/**
	 * Add filter to PHP-based tracks events.
	 *
	 * @param array  $properties Current event properties array.
	 * @param string $event_name Name of the event.
	 */
	public function add_tracks_php_filter( $properties, $event_name ) {
		$properties['host'] = self::$tracks_host_value;
		return $properties;
	}

	/**
	 * Filter JS-based Jetpack WooCommerce Analytics events.
	 *
	 * @param array  $properties Current event properties array.
	 *
	 * @since 2.2.12
	 */
	public function filter_jetpack_woocommerce_analytics_event_props( $properties ) {
		$properties['host'] = self::$tracks_host_value;

		return $properties;
	}

	/**
	 * Hide the display of the WooCommerce.com settings.
	 *
	 * @param array $settings Current settings array.
	 */
	public function hide_woocommerce_com_settings( $settings ) {
		unset( $settings['woocommerce_com'] );
		return $settings;
	}

	/**
	 * Set the survey source for survey URLs in WooCommerce Admin.
	 *
	 * @param array $query Query of arguments appended to URL.
	 * @return array
	 */
	public function set_survey_source( $query ) {
		$query['source'] = self::$tracks_host_value;
		return $query;
	}
}

WC_Calypso_Bridge_Tracks::get_instance();
