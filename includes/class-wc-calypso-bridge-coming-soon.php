<?php

use Automattic\WooCommerce\Admin\Features\Features;

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Calypso_Bridge_Coming_Soon
 *
 * @since   x.x.x
 * @version x.x.x
 *
 * Handle Coming Soon mode.
 */
class  WC_Calypso_Bridge_Coming_Soon {
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
		add_filter( 'a8c_show_coming_soon_page', array( $this, 'should_show_a8c_coming_soon_page' ), 99999, 1 );
	}

	public function should_show_a8c_coming_soon_page( $should_show ) {
		if ( ! Features::is_enabled( 'launch-your-store' ) ) {
			// Bail out early if the Launch Your Store feature is not enabled.
			return $should_show;
		}

		return false;
	}


}

WC_Calypso_Bridge_Coming_Soon::get_instance();
