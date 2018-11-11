<?php
/**
 * Adds the functionality needed to streamline the themes experience for Storefront and suppressing WC admin notices
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge Themes Setup
 */
class WC_Calypso_Bridge_Themes_Setup {

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Bridge_Themes_Setup instance
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
        $this->includes();

        // Suppress WC Admin Notices
		add_action( 'admin_head', array( $this, 'suppress_admin_notices' ) );
        add_filter( 'woocommerce_helper_suppress_connect_notice', '__return_true' );
        // Set default theme values
        add_action( 'init', array( $this, 'set_theme_default_values' ) ); 
        
	}

    /**
	 * Loads required functionality, classes, and API endpoints.
	 */
	private function includes() {
		include_once( dirname( __FILE__ ) . '/helper-functions.php' );
    }
    
	/**
	 * Suppresses admin notifications in wp-admin
	 *
	 * @return void
	 */
	public function suppress_admin_notices() {
		/**
		 * List of extension specific and themes class level functions to suppress
		 * 'CLASS_NAME' => array( 'FUNCTION_PRIORITY' => 'FUNCTION_NAME' )
		 */ 
		$extension_admin_notices_to_suppress = array(	'WC_Shipping_Australia_Post_Init' 	=> array( '10' => 'environment_check' ),
														'WC_Facebookcommerce_Integration' 	=> array( '10' => 'checks' ),	
														'WC_USPS' 							=> array( '10' => 'environment_check' ),
														'SP_Admin' 							=> array( '10' => 'activation_notice' ),
														'Woocommerce_Square' 				=> array( '10' => 'is_connected_to_square' ),
														'WC_Taxjar' 						=> array( '10' => 'maybe_display_admin_notices' ),
														'WC_Klarna_Payments' 				=> array( '10' => 'order_management_check' ),
														'Klarna_Checkout_For_WooCommerce' 	=> array( '10' => 'order_management_check' ),
														'WC_Gateway_PayFast'				=> array( '10' => 'admin_notices' ),
														'WC_Connect_Nux'					=> array( '9' => 'show_banner_before_connection' ),
														'Storefront_NUX_Admin' 				=> array( '99' => 'admin_notices' )
												);
		foreach ( $extension_admin_notices_to_suppress as $class_name => $function_to_suppress ) {
			WC_Calypso_Bridge_Helper_Functions::remove_class_action( 'admin_notices', $class_name, current( $function_to_suppress ), key( $function_to_suppress ) );
		}
		// List of extensions that do not use class level functions for admin notices.
		$other_admin_notices = array( 'woocommerce_gateway_paypal_express_upgrade_notice', 'woocommerce_gateway_klarna_welcome_notice' );
		foreach ( $other_admin_notices as $function_to_suppress ) {
			remove_action( 'admin_notices', $function_to_suppress );
		}
        // Suppress: Looking for the store notice setting? It can now be found in the Customizer.
        $user_id = get_current_user_id();
        $user_meta_key = 'dismissed_store_notice_setting_moved_notice';
        $current_user_meta_value = get_user_meta( $user_id, $user_meta_key, true);
        if ( ! $current_user_meta_value ) {
            $updated_user_meta_value = update_user_meta( $user_id, $user_meta_key, true );    
        }
		// Suppress: Product Add Ons Activation Notice
		$deleted = delete_option( 'wpa_activation_notice' );
		// Suppress all other WC Admin Notices not specified above
		WC_Admin_Notices::remove_all_notices();
    }
    
    public function check_if_defaults_already_setup() {
        $wpcom_ec_plan_theme_defaults = get_option( 'wpcom_ec_plan_theme_defaults', false );
        return $wpcom_ec_plan_theme_defaults;
    }

    public function set_theme_default_values() {
        // Should either be on theme activation or set to have been saved
        if ( ! $this->check_if_defaults_already_setup() ) {
            set_theme_mod( 'sp_product_layout', 'full-width' ); // enables Full width single product page.
            set_theme_mod( 'sp_shop_layout', 'full-width' ); // enables Full width shop archive pages.
            set_theme_mod( 'sph_hero_enable', 'enable' ); // enables Parallax Hero on homepage.
            set_theme_mod( 'sph_hero_heading_text', esc_attr__( 'Welcome Test', 'storefront' ) ); // Heading Text for Parallax Hero.
            set_theme_mod( 'sph_hero_text', sprintf( esc_attr__( 'This is your homepage which is what most visitors will see when they first visit your shop.%sYou can change this text by editing the "Parallax Hero" Section via the "Powerpack" settings in the Customizer on the left hand side of your screen.', 'storefront' ), PHP_EOL . PHP_EOL ) ); // Content for Parallax Hero.
            if ( 0 < wc_get_page_id( 'shop' ) ) {
                set_theme_mod( 'sph_hero_button_url', get_permalink( wc_get_page_id( 'shop' ) ) ); // Set button url to the shop page instead of home if the shop page exists.
            }
            set_theme_mod( 'sp_homepage_content', false ); // Removes default Welcome banner from starter content.
            set_theme_mod( 'sp_homepage_top_rated', false ); // Removes Top Rated Products area from starter content.
            set_theme_mod( 'sp_homepage_on_sale', false ); // Removes On Sale Products area from starter content.
            set_theme_mod( 'sp_homepage_best_sellers', false ); // Removes Best Sellers Products area from starter content.
            update_option( 'woocommerce_demo_store', 'yes' ); // enables demo store notice.

            // Save option that says the setup has been run already.
            update_option( 'wpcom_ec_plan_theme_defaults', true );
        }
    }    

}

$wc_calypso_bridge_themes_setup = WC_Calypso_Bridge_Themes_Setup::get_instance();
