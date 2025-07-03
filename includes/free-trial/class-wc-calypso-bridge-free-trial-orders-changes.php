<?php
/**
 * Contains the logic for override WooCommerce > orders screen
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   2.0.1
 * @version 2.2.26
 */

class WC_Calypso_Bridge_Free_Trial_Orders_Changes {
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
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		if ( ! wc_calypso_bridge_is_ecommerce_trial_plan() ) {
			return;
		}

		add_action('admin_print_footer_scripts', array($this, 'override_empty_state_cta_button_class') );
	}

	/**
	 * Change the class of the button on the empty state of the orders screen.
	 *
	 */
	public function override_empty_state_cta_button_class() {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( in_array( $screen->id, array( 'edit-shop_order', 'woocommerce_page_wc-orders' ), true ) ) {
			?>
			<script>
				const woocommerceBlankStateButton = document.querySelector('.woocommerce-BlankState-cta.button');
				woocommerceBlankStateButton.classList.add('button-secondary');
				woocommerceBlankStateButton.classList.remove('button-primary');
			</script>
			<?php
		}
	}
}

WC_Calypso_Bridge_Free_Trial_Orders_Changes::get_instance();
