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
class WC_Calypso_Bridge_Coming_Soon {
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

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'a8c_show_coming_soon_page', array( $this, 'should_show_a8c_coming_soon_page' ), PHP_INT_MAX, 1 );
	}

	/**
	 * Hide the a8c coming soon page if the Launch Your Store feature is enabled.
	 *
	 * @param bool $should_show
	 * @return bool
	 */
	public function should_show_a8c_coming_soon_page( $should_show ) {
		if (
			class_exists( '\Automattic\WooCommerce\Admin\Features\Features' ) && \Automattic\WooCommerce\Admin\Features\Features::is_enabled( 'launch-your-store' )
		) {
			return false;
		}

		return $should_show;
	}
}

WC_Calypso_Bridge_Coming_Soon::get_instance();
