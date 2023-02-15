<?php
/**
 * Tracks modifications for the ecommerce plan.
 *
 * Adjust tracks settings for business, ecomm, in calypsoified and wp-admin views.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.1.6
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

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
		 * Always make the tracks setting be yes. Users can opt via WordPress.com privacy settings.
		 */
		add_filter( 'pre_option_woocommerce_allow_tracking', static function() {
			return 'yes';
		} );

		if ( ! wc_calypso_bridge_has_ecommerce_features() ) {
			return;
		}

		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Initialize.
	 */
	public function init() {
		$this->set_tracks_host_value();

		// Set track host and source.
		add_filter( 'admin_footer', array( $this, 'add_tracks_js_filter' ) );
		add_filter( 'woocommerce_tracks_event_properties', array( $this, 'add_tracks_php_filter' ), 10, 2 );
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

		// If an ecomm plan site, update host value.
		if ( wc_calypso_bridge_has_ecommerce_features() ) {
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
	 * Add filter to PHP-based tracks events.
	 *
	 * @param array  $properties Current event properties array.
	 * @param string $event_name Nmae of the event.
	 */
	public function add_tracks_php_filter( $properties, $event_name ) {
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
