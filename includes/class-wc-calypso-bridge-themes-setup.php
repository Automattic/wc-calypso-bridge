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
		// Set default theme values.
		add_action( 'init', array( $this, 'set_theme_default_values' ) );
	}

	/**
	 * Test if defaults are set.
	 *
	 * @return boolean
	 */
	public function check_if_defaults_already_setup() {
		$wpcom_ec_plan_theme_defaults = get_option( 'wpcom_ec_plan_theme_defaults', false );
		return $wpcom_ec_plan_theme_defaults;
	}

	/**
	 * Sets default values for Storefront powered themes and Powerpack.
	 *
	 * @return void
	 */
	public function set_theme_default_values() {
		// Should either be on theme activation or set to have been saved.
		if ( ! $this->check_if_defaults_already_setup() ) {
			set_theme_mod( 'sp_product_layout', 'full-width' ); // enables Full width single product page.
			set_theme_mod( 'sp_shop_layout', 'full-width' ); // enables Full width shop archive pages.
			set_theme_mod( 'sph_hero_enable', 'enable' ); // enables Parallax Hero on homepage.
			set_theme_mod( 'sph_hero_heading_text', esc_attr__( 'Welcome', 'wc-calypso-bridge' ) ); // Heading Text for Parallax Hero.
			set_theme_mod( 'sph_hero_text', sprintf( '%1$s <br/><br/> %2$s', esc_attr__( 'This is your homepage which is what most visitors will see when they first visit your shop.', 'wc-calypso-bridge' ), esc_attr__( 'You can change this text by editing the "Parallax Hero" Section via the "Powerpack" settings in the Customizer on the left hand side of your screen.', 'wc-calypso-bridge' ) ) ); // Content for Parallax Hero.
			if ( 0 < wc_get_page_id( 'shop' ) ) {
				set_theme_mod( 'sph_hero_button_url', get_permalink( wc_get_page_id( 'shop' ) ) ); // Set button url to the shop page instead of home if the shop page exists.
			}
			set_theme_mod( 'sp_homepage_content', false ); // Removes default Welcome banner from starter content.
			set_theme_mod( 'sp_homepage_top_rated', false ); // Removes Top Rated Products area from starter content.
			set_theme_mod( 'sp_homepage_on_sale', false ); // Removes On Sale Products area from starter content.
			set_theme_mod( 'sp_homepage_best_sellers', false ); // Removes Best Sellers Products area from starter content.
			update_option( 'woocommerce_demo_store', 'yes' ); // enables demo store notice.
			// Force Fresh Site.
			update_option( 'fresh_site', true );
			// Save option that says the setup has been run already.
			update_option( 'wpcom_ec_plan_theme_defaults', true );
		}
	}

}

$wc_calypso_bridge_themes_setup = WC_Calypso_Bridge_Themes_Setup::get_instance();
