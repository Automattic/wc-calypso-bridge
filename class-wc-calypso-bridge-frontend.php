<?php
/**
 * Frontend functions for Calypso Bridge
 * 
 * Mostly related to the Customizer, Storefront, and its extensions
 *
 * @package WC_Calypso_Bridge_Frontend/Classes
 * @since   1.0.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge Frontend
 */
class WC_Calypso_Bridge_Frontend {
	/**
	 * Paths to assets act oddly in production
	 */
	const MU_PLUGIN_ASSET_PATH = '/wp-content/mu-plugins/wpcomsh/vendor/automattic/wc-calypso-bridge/';

	/**
	 * Plugin asset path
	 *
	 * @var string
	 */
	public static $plugin_asset_path = null;

	/**
	 * Class Instance.
	 *
	 * @var WC_Calypso_Bridge_Frontend instance
	 */
	protected static $instance = null;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'remove_storefront_default_footer_credit' ), 10 );	
    }
    
    /**
	 * Unhooks Storefront footer credit
	 *
	 */
	public function remove_storefront_default_footer_credit() {
		remove_action( 'storefront_footer', 'storefront_credit', 20 );
		add_action( 'storefront_footer', array( $this, 'wpcom_ecommerce_plan_storefront_credit' ), 20 );
	}

	/**
	 * Display the new WordPress.com like theme credit
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function wpcom_ecommerce_plan_storefront_credit() {
		?>
		<div class="site-info">
			<?php echo esc_html( apply_filters( 'storefront_copyright_text', $content = '&copy; ' . get_bloginfo( 'name' ) . ' ' . date( 'Y' ) ) ); ?>
			<?php if ( apply_filters( 'storefront_credit_link', true ) ) { ?>
			<br />
				<?php
				if ( apply_filters( 'storefront_privacy_policy_link', true ) && function_exists( 'the_privacy_policy_link' ) ) {
					the_privacy_policy_link( '', '<span role="separator" aria-hidden="true"></span>' );
				}
				?>
				<?php echo '<a href="https://wordpress.com/?ref=footer_website" target="_blank" title="' . esc_attr__( 'WordPress.com - The Best eCommerce Platform for WordPress', 'storefront' ) . '" rel="author">' . esc_html__( 'Built with Storefront &amp; WordPress.com', 'storefront' ) . '</a>.'; ?>
			<?php } ?>
		</div><!-- .site-info -->
		<?php
	}

	/**
	 * Loads required functionality, classes, and API endpoints.
	 */
	private function includes() {
		
	}

	/**
	 * Class instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			// If this is a traditionally installed plugin, set plugin_url for the proper asset path.
			if ( file_exists( WP_PLUGIN_DIR . '/wc-calypso-bridge/wc-calypso-bridge.php' ) ) {
				if ( WP_PLUGIN_DIR . '/wc-calypso-bridge/' == plugin_dir_path( __FILE__ ) ) {
					self::$plugin_asset_path = plugin_dir_url( __FILE__ );
				}
			}

			self::$instance = new self();
		}

		return self::$instance;
	}



}

WC_Calypso_Bridge_Frontend::instance();