<?php
/**
 * Removes the back links and adds the breadcrumbs on settings pages.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge Breadcrumbs
 */
class WC_Calypso_Bridge_Breadcrumbs {

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Bridge_Breadcrumbs instance
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
		add_action( 'woocommerce_settings_checkout', array( $this, 'add_payment_gateway_breadcrumbs' ) );
		add_action( 'woocommerce_settings_email', array( $this, 'add_email_breadcrumbs' ) );
	}

	/**
	 * Render breadcrumbs
	 *
	 * @param string $parent_page_title Title of parent page.
	 * @param string $parent_page_url URL of parent page.
	 * @param string $current_page_title Title of current (child) page.
	 */
	public function render_breadcrumbs( $parent_page_title, $parent_page_url, $current_page_title ) {
		?>
		<h2 class="wc-calypso-bridge-breadcrumbs">
			<a href="<?php echo esc_url( $parent_page_url ); ?>"><?php echo esc_html( $parent_page_title ); ?></a>
			&gt;
			<?php echo esc_html( $current_page_title ); ?>
		</h2>
		<?php
	}

	/**
	 * Add payment gateway breadcrumbs
	 */
	public function add_payment_gateway_breadcrumbs() {
		$payment_gateways = WC()->payment_gateways->payment_gateways();
		if ( isset( $_GET['section'] ) && ! empty( $payment_gateways ) && isset( $payment_gateways[ $_GET['section'] ] ) ) {
			$gateway = $payment_gateways[ $_GET['section'] ]; // WPCS: CSRF ok, sanitization ok.
			$this->render_breadcrumbs(
				__( 'Payment methods', 'wc-calypso-bridge' ),
				admin_url( '/admin.php?page=wc-settings&tab=checkout' ),
				$gateway->get_method_title()
			);
		}
	}

	/**
	 * Add email breadcrumbs
	 */
	public function add_email_breadcrumbs() {
		$emails = wc()->mailer()->emails;
		$emails = array_change_key_case( $emails );
		if ( isset( $_GET['section'] ) && ! empty( $emails ) && isset( $emails[ $_GET['section'] ] ) ) {
			$email = $emails[ $_GET['section'] ]; // WPCS: CSRF ok, sanitization ok.
			$this->render_breadcrumbs(
				__( 'Email notifications', 'wc-calypso-bridge' ),
				admin_url( '/admin.php?page=wc-settings&tab=email' ),
				$email->get_title()
			);
		}
	}
}
$wc_calypso_bridge_breadcrumbs = WC_Calypso_Bridge_Breadcrumbs::get_instance();
