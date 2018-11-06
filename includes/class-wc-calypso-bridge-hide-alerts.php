<?php
/**
 * Removes various admin alerts that should not be there
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge Hide Alerts
 */
class WC_Calypso_Bridge_Hide_Alerts {

    /**
     * Class instance.
     *
     * @var WC_Calypso_Bridge_Hide_Alerts instance
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
     * Constructor
     */
    private function __construct() {
        add_action( 'admin_init', array( $this, 'hide_woo_obw_alert' ) );
    }
    
    /**
     * Prevents the OBW admin alert from Woo Core from being shown
     */
    function hide_woo_obw_alert() {
        if ( class_exists( 'WC_Admin_Notices' ) ) {
            WC_Admin_Notices::remove_notice( 'install' );
        }
    }

}
$wc_calypso_bridge_hide_alerts = WC_Calypso_Bridge_Hide_Alerts::get_instance();
