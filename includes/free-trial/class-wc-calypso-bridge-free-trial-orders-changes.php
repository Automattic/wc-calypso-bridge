<?php
/**
 * Contains the logic for override WooCommerce > orders screen
 *
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
		add_action('admin_print_footer_scripts', array($this, 'override_empty_state_cta_button_class') );
	}

	public function override_empty_state_cta_button_class() {
		$screen = get_current_screen();
	
		if ( $screen->id === 'edit-shop_order' ) {
				?>
				<script>
					jQuery('.woocommerce-BlankState-cta.button')
						.addClass('button-secondary')
						.removeClass('button-primary');
				</script>
				<?php
		}
	}
}

WC_Calypso_Bridge_Free_Trial_Orders_Changes::get_instance();