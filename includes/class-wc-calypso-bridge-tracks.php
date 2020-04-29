<?php
/**
 * Tracks modifications for the ecommerce plan.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.1.6
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge Tracks
 */
class WC_Calypso_Bridge_Tracks {

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Tracks instance
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
		add_filter( 'admin_footer', array( $this, 'add_tracks_js_filter' ) );
		add_filter( 'woocommerce_tracks_event_properties', array( $this, 'add_tracks_php_filter' ), 10, 2 );
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
				properties.host = 'ecommplan';
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
	 */
	public function add_tracks_php_filter( $properties, $event_name ) {
		$properties['host'] = 'ecommplan';
		return $properties;
	}
}

$wc_calypso_bridge_setup = WC_Calypso_Bridge_Tracks::get_instance();
