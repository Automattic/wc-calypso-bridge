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
	 * Class instance.
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Unhooks Storefront footer credit
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
			<?php echo esc_html( apply_filters( 'storefront_copyright_text', '&copy; ' . get_bloginfo( 'name' ) . ' ' . date( 'Y' ) ) ); ?>
			<?php if ( apply_filters( 'storefront_credit_link', true ) ) { ?>
			<br />
				<?php
				if ( apply_filters( 'storefront_privacy_policy_link', true ) && function_exists( 'the_privacy_policy_link' ) ) {
					the_privacy_policy_link( '', '<span role="separator" aria-hidden="true"></span>' );
				}
				?>
				<?php echo '<a href="https://wordpress.com/?ref=footer_website" target="_blank" title="' . esc_attr__( 'WordPress.com - The Best eCommerce Platform for WordPress', 'wc-calypso-bridge' ) . '" rel="author">' . esc_html__( 'Built with Storefront &amp; WordPress.com', 'wc-calypso-bridge' ) . '</a>.'; ?>
			<?php } ?>
		</div><!-- .site-info -->
		<?php
	}
}

WC_Calypso_Bridge_Frontend::get_instance();
