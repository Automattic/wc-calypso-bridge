<?php

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

        return add_filter( 'wpcom_better_footer_credit_link', array( $this, 'get_footer_credits' ) );
    }

    /**
     * Get footer credit as HTML.
     *
     * @see <wpcomsh/block-theme-footer-credits/class-wpcom-block-theme-footer-credits.php>
     *
     * @return string
     */
    public function get_footer_credits(): string {
        $utm_string = '?utm_source=referral&utm_medium=footer-credit&utm_campaign=woo-express-footer-credit';
        return sprintf( __( 'Powered by <a href="https://woocommerce.com/express/%1$s" rel="nofollow">Woo</a>' ), $utm_string );
    }

};
WC_Calypso_Bridge_Footer_Credits::get_instance();
