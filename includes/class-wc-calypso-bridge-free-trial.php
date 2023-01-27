<?php
/**
 * Free Trial related.
 *
 * @package WC_Calypso_Bridge/Classes
 * @version 1.9.19
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Features\Features;

/**
 * WC_Calypso_Bridge_Free_Trial Class.
 */
class WC_Calypso_Bridge_Free_Trial {

	/**
	 * The single instance of the class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Get class instance.
	 *
	 * @return object Instance.
	 */
	final public static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'woocommerce_init', array( $this, 'init' ), 20 );
	}

	/**
	 * Free trial hooks.
	 */
	public function init() {

		if ( $this->is_free_trial() ) {

			// Prevent orders on shortcode checkout.
			add_action( 'woocommerce_before_checkout_process', function () {

				// Bail out early if the current user is allowed to create orders on free trial.
				if ( current_user_can( 'manage_woocommerce' ) ) {
					return;
				}

				throw new Exception( __( 'Only Admins and Shop Managers can place test orders. Contact an Admin to get permission.', 'wc-calypso-bridge' ) );
			}, PHP_INT_MAX );


			// Prevent orders on shortcode checkout - PayPal removes the checkout button and replaces it.
			add_action( 'woocommerce_after_checkout_validation', function ( $data, $errors ) {

				// Bail out early if the current user is allowed to create orders on free trial.
				if ( current_user_can( 'manage_woocommerce' ) ) {
					return;
				}

				$errors->add( 405, __( 'Only Admins and Shop Managers can place test orders. Contact an Admin to get permission.', 'wc-calypso-bridge' ) );
			}, PHP_INT_MAX, 2 );


			// Prevent orders on block checkout.
			add_action( 'woocommerce_store_api_checkout_order_processed', function () {

				// Bail out early if the current user is allowed to create orders on free trial.
				if ( current_user_can( 'manage_woocommerce' ) ) {
					return;
				}

				throw new Automattic\WooCommerce\StoreApi\Exceptions\RouteException(
					405,
					__( 'Only Admins and Shop Managers can place test orders. Contact an Admin to get permission.', 'wc-calypso-bridge' )
				);
			}, PHP_INT_MAX );


			// Display notice on the checkout page
			add_filter( 'the_content', function ( $content ) {

				// Bail out early if the current user is allowed to create orders on free trial.
				if ( current_user_can( 'manage_woocommerce' ) ) {
					return $content;
				}

				if ( ! is_checkout() ) {
					return $content;
				}

				$message = __( 'Only Admins and Shop Managers can place test orders. Contact an Admin to get permission.', 'wc-calypso-bridge' );
				$markup  = '<div class="woocommerce"><div class="woocommerce-notices-wrapper"><ul class="woocommerce-error" role="alert"><li> ' . $message . '</li></ul></div></div>';

				return $markup . $content;
			}, PHP_INT_MAX );

			// Display notice in Admin > Payments settings.
			add_action( 'admin_notices', function () {

				$screen    = get_current_screen();
				$screen_id = $screen ? $screen->id : '';

				if ( 'woocommerce_page_wc-settings' !== $screen_id ) {
					return;
				}

				if ( isset( $_GET['tab'] ) && 'checkout' === $_GET['tab'] ) {
					$screen_id .= '_checkout';
				}

				$show_on_screens = array(
					'woocommerce_page_wc-settings_checkout',
				);

				if ( ! in_array( $screen_id, $show_on_screens, true ) ) {
					return;
				}

				$blog_id   = get_current_blog_id();
				$site_url  = get_home_url( $blog_id );
				$site_slug = wp_parse_url( $site_url, PHP_URL_HOST );
				$plan_url  = 'https://wordpress.com/plans/' . $site_slug;
				$message = sprintf( __( '🔔 During trial, only Admins and Shop Managers can place orders. To process real transactions, <a href="%s">pick a plan</a>.', 'wc-calypso-bridge' ), $pick_a_plan_url );
				?>
				<div class="notice notice-warning">
					<p><?php echo $notice; ?></p>
				</div>
				<?php
			}, PHP_INT_MAX );

		}
	}

	/**
	 * @return boolean Whether the site is in free trial mode.
	 */
	public function is_free_trial() {
		return true;
	}

}

WC_Calypso_Bridge_Free_Trial::get_instance();
