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
                                                        'Storefront_NUX_Admin' 				=> array( '99' => 'admin_notices' ),
                                                        'WC_Gateway_PPEC_Plugin'            => array( '10' => 'show_bootstrap_warning' ),
                                                        'WC_RoyalMail'                      => array( '10' => 'environment_check' )
                                                );
		foreach ( $extension_admin_notices_to_suppress as $class_name => $function_to_suppress ) {
			WC_Calypso_Bridge_Helper_Functions::remove_class_action( 'admin_notices', $class_name, current( $function_to_suppress ), key( $function_to_suppress ) );
        }
        // Canada Post Specific - refactor after launch to be included in the above loop
        WC_Calypso_Bridge_Helper_Functions::remove_class_action( 'admin_notices', 'WC_Shipping_Canada_Post_Init', 'connect_canada_post', 10 );
        WC_Calypso_Bridge_Helper_Functions::remove_class_action( 'admin_notices', 'WC_Shipping_Canada_Post_Init', 'environment_check', 10 );
        // Square Specific - refactor after launch to be included in the above loop
        WC_Calypso_Bridge_Helper_Functions::remove_class_action( 'admin_notices', 'Woocommerce_Square', 'check_environment', 10 );
        WC_Calypso_Bridge_Helper_Functions::remove_class_action( 'admin_notices', 'Woocommerce_Square', 'is_connected_to_square', 10 );        
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
        WC_Admin_Notices::remove_notice( 'wootenberg' );
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
            set_theme_mod( 'sph_hero_heading_text', esc_attr__( 'Welcome', 'wc-calypso-bridge' ) ); // Heading Text for Parallax Hero.
            set_theme_mod( 'sph_hero_text', sprintf( esc_attr__( 'This is your homepage which is what most visitors will see when they first visit your shop.%sYou can change this text by editing the "Parallax Hero" Section via the "Powerpack" settings in the Customizer on the left hand side of your screen.', 'wc-calypso-bridge' ), PHP_EOL . PHP_EOL ) ); // Content for Parallax Hero.
            if ( 0 < wc_get_page_id( 'shop' ) ) {
                set_theme_mod( 'sph_hero_button_url', get_permalink( wc_get_page_id( 'shop' ) ) ); // Set button url to the shop page instead of home if the shop page exists.
            }
            set_theme_mod( 'sp_homepage_content', false ); // Removes default Welcome banner from starter content.
            set_theme_mod( 'sp_homepage_top_rated', false ); // Removes Top Rated Products area from starter content.
            set_theme_mod( 'sp_homepage_on_sale', false ); // Removes On Sale Products area from starter content.
            set_theme_mod( 'sp_homepage_best_sellers', false ); // Removes Best Sellers Products area from starter content.
            update_option( 'woocommerce_demo_store', 'yes' ); // enables demo store notice.

            // Create default homepage
            $page_slug = esc_attr__( 'Welcome', 'wc-calypso-bridge' );
            $page_options =  'woocommerce_welcome_page_id';
            $page_title = $page_slug;
            $page_content = sprintf( esc_attr__( 'This is your homepage which is what most visitors will see when they first visit your shop.%sYou can change this text by editing the "Parallax Hero" Section via the "Powerpack" settings in the Customizer on the left hand side of your screen.', 'wc-calypso-bridge' ), PHP_EOL . PHP_EOL );
            $post_parent = 0;
            $welcome_page_id = wc_create_page( esc_sql( $page_slug ), $page_options, $page_title, $page_content, $post_parent );
            // Set page as Static Front Page with homepage template and attach default image.
            if ( 0 < $welcome_page_id ) {
                $this->set_default_page_template( $welcome_page_id );
                $this->attach_storefront_image( $welcome_page_id );
                update_option( 'page_on_front', $welcome_page_id );
                update_option( 'show_on_front', 'page' );
            }

            // Save option that says the setup has been run already.
            update_option( 'wpcom_ec_plan_theme_defaults', true );
        }
    }
    
    /**
     * Sets page template for specified page id
     *
     * @param integer $page_id
     * @param string $page_template
     * @return void
     */
    private function set_default_page_template( $page_id = 0, $page_template = 'template-homepage.php' ) {
        if ( 0 < $page_id ) {
            update_post_meta( $page_id, '_wp_page_template', $page_template );
        }
    }
    /**
     * Attaches Storefront images to default content
     *
     * Original Attribution: https://gist.github.com/hissy/7352933
     * 
     * @param integer $parent_post_id
     * @param string $filename
     * @return void
     */
    private function attach_storefront_image( $parent_post_id = 0, $file = 'assets/images/customizer/starter-content/hero.jpg' ) {
        
        if ( 0 < $parent_post_id ) {
            // Get the path to the upload directory.
            $file = trailingslashit( get_theme_root_uri() ) . trailingslashit( 'storefront' ) . $file;
            $filename = basename($file);
            
            $upload_file = wp_upload_bits($filename, null, file_get_contents($file));
            if (!$upload_file['error']) {
                $wp_filetype = wp_check_filetype($filename, null );
                $attachment = array(
                    'post_mime_type' => $wp_filetype['type'],
                    'post_parent' => $parent_post_id,
                    'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );
                $attachment_id = wp_insert_attachment( $attachment, $upload_file['file'], $parent_post_id );
                if (!is_wp_error($attachment_id)) {
                    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
                    $attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );
                    wp_update_attachment_metadata( $attachment_id,  $attachment_data );
                    set_post_thumbnail( $parent_post_id, $attachment_id );
                }
            }
        }

    }

}

$wc_calypso_bridge_themes_setup = WC_Calypso_Bridge_Themes_Setup::get_instance();
