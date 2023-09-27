<?php
/**
 * WC Calypso Bridge Footer Credits
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   2.2.11
 * @version 2.2.12
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge Footer Credits
 */
class WC_Calypso_Bridge_Footer_Credits {

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
	final public static function get_instance(): WC_Calypso_Bridge_Footer_Credits {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

    /**
	 * Constructor.
	 */
	public function __construct() {
		// Bail out early if the current site is not on Woo plan.
		if ( ! wc_calypso_bridge_is_woo_express_plan() ) {
			return;
		}

        add_filter( 'wpcom_better_footer_credit_link', array( $this, 'get_footer_credits' ), PHP_INT_MAX, 2 );
    }

	/**
	 * Override footer credit as HTML - Only if there is no option in the DB or if it's set to default.
	 *
	 * @param $credit
	 * @param $lang
	 *
	 * @return string
	 * @see <wpcomsh/block-theme-footer-credits/class-wpcom-block-theme-footer-credits.php>
	 *
	 */
	public function get_footer_credits( $credit, $lang ) {

		$credit_option = get_option( 'footercredit', false );
		if ( empty( $credit_option ) || 'default' === $credit_option ) {
			$utm_string = '?utm_source=referral&utm_medium=footer-credit&utm_campaign=woo-express-footer-credit';

			/* translators: %1$s is replaced with a link to WooCommerce.com */
			return sprintf( __( 'Powered by %1$s', 'wc-calypso-bridge' ), '<a href="https://woocommerce.com/express/' . $utm_string . '" rel="nofollow">Woo</a>' );
		}

		return $credit;

	}

};
WC_Calypso_Bridge_Footer_Credits::get_instance();
