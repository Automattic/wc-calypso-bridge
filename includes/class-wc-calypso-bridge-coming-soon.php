<?php

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
		add_filter( 'woocommerce_coming_soon_exclude', array( $this, 'should_exclude_lys_coming_soon' ) );
		add_action( 'update_option_wpcom_public_coming_soon', array( $this, 'sync_coming_soon_option' ), 10, 3 );
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

	/**
	 * Exclude the coming soon page if the user is accessing the site via a valid share link.
	 *
	 * @param bool $exclude
	 * @return bool
	 */
	public function should_exclude_lys_coming_soon( $exclude ) {
		if ( ! function_exists( '\A8C\FSE\Coming_soon\get_share_code' ) ) {
			return $exclude;
		}

		$share_code = \A8C\FSE\Coming_soon\get_share_code();
		if ( \A8C\FSE\Coming_soon\is_accessed_by_valid_share_link( $share_code ) ) {
			return true;
		}

		return $exclude;
	}

	/**
	 * Sync the coming soon option from wpcom_public_coming_soon to woocommerce_coming_soon.
	 *
	 * @param int $old_value
	 * @param int $new_value
	 * @param string $option
	 * @return void
	 */
	public function sync_coming_soon_option( $old_value, $new_value, $option ) {
		if ( 1 ===  (int) $new_value ) {
			update_option( 'woocommerce_coming_soon', 'yes' );
		} else {
			update_option( 'woocommerce_coming_soon', 'no' );
		}
	}
}

WC_Calypso_Bridge_Coming_Soon::get_instance();
