<?php
/**
 * Helpful functions that can be used across the plugin.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.2
 * @version 1.9.8
 */

use Automattic\WooCommerce\Admin\WCAdminHelper;

/**
 * WC_Calypso_Bridge_Helper_Functions.
 */
class WC_Calypso_Bridge_Helper_Functions {

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Bridge_Helper_Functions instance
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
	 * Constructor.
	 */
	private function __construct() {
		// Silence.
	}

	/**
	 * Checks to see if WC Admin was installed later than a reference time.
	 *
	 * @since 1.9.8
	 *
	 * @param  int $reference_timestamp Time to compare against.
	 * @return bool
	 */
	public static function is_wc_admin_installed_gte( $reference_timestamp ) {

		$install_timestamp = get_option( WCAdminHelper::WC_ADMIN_TIMESTAMP_OPTION );

		if ( ! is_numeric( $install_timestamp ) ) {
			$install_timestamp = time();
			update_option( WCAdminHelper::WC_ADMIN_TIMESTAMP_OPTION, $install_timestamp );
		}

		return $install_timestamp > $reference_timestamp;
	}

	/**
	 * Remove Class Filter Without Access to Class Object
	 *
	 * Original Attribution:
	 * Github Gist Code: https://gist.github.com/tripflex/c6518efc1753cf2392559866b4bd1a53
	 * WordPress Developer Handbook: https://developer.wordpress.org/reference/functions/remove_action/#comment-2256
	 *
	 * In order to use the core WordPress remove_filter() on a filter added with the callback
	 * to a class, you either have to have access to that class object, or it has to be a call
	 * to a static method.  This method allows you to remove filters with a callback to a class
	 * you don't have access to.
	 *
	 * Works with WordPress 1.2+ (4.7+ support added 9-19-2016)
	 * Updated 2-27-2017 to use internal WordPress removal for 4.7+ (to prevent PHP warnings output)
	 *
	 * @param string $tag         Filter to remove.
	 * @param string $class_name  Class name for the filter's callback.
	 * @param string $method_name Method name for the filter's callback.
	 * @param int    $priority    Priority of the filter (default 10).
	 *
	 * @return bool Whether the function is removed.
	 */
	public static function remove_class_filter( $tag, $class_name = '', $method_name = '', $priority = 10 ) {
		global $wp_filter;
		// Check that filter actually exists first.
		if ( ! isset( $wp_filter[ $tag ] ) ) {
			return false;
		}
		/**
		 * If filter config is an object, means we're using WordPress 4.7+ and the config is no longer
		 * a simple array, rather it is an object that implements the ArrayAccess interface.
		 *
		 * To be backwards compatible, we set $callbacks equal to the correct array as a reference (so $wp_filter is updated)
		 *
		 * @see https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/
		 */
		if ( is_object( $wp_filter[ $tag ] ) && isset( $wp_filter[ $tag ]->callbacks ) ) {
			// Create $fob object from filter tag, to use below.
			$fob       = $wp_filter[ $tag ];
			$callbacks = &$wp_filter[ $tag ]->callbacks;
		} else {
			$callbacks = &$wp_filter[ $tag ];
		}
		// Exit if there aren't any callbacks for specified priority.
		if ( ! isset( $callbacks[ $priority ] ) || empty( $callbacks[ $priority ] ) ) {
			return false;
		}
		// Loop through each filter for the specified priority, looking for our class & method.
		foreach ( (array) $callbacks[ $priority ] as $filter_id => $filter ) {
			// Filter should always be an array - array( $this, 'method' ), if not goto next.
			if ( ! isset( $filter['function'] ) || ! is_array( $filter['function'] ) ) {
				continue;
			}
			// If first value in array is not an object, it can't be a class.
			if ( ! is_object( $filter['function'][0] ) ) {
				continue;
			}
			// Method doesn't match the one we're looking for, goto next.
			if ( $filter['function'][1] !== $method_name ) {
				continue;
			}
			// Method matched, now let's check the Class.
			if ( get_class( $filter['function'][0] ) === $class_name ) {
				// WordPress 4.7+ use core remove_filter() since we found the class object.
				if ( isset( $fob ) ) {
					// Handles removing filter, reseting callback priority keys mid-iteration, etc.
					$fob->remove_filter( $tag, $filter['function'], $priority );
				} else {
					// Use legacy removal process (pre 4.7).
					unset( $callbacks[ $priority ][ $filter_id ] );
					// and if it was the only filter in that priority, unset that priority.
					if ( empty( $callbacks[ $priority ] ) ) {
						unset( $callbacks[ $priority ] );
					}
					// and if the only filter for that tag, set the tag to an empty array.
					if ( empty( $callbacks ) ) {
						$callbacks = array();
					}
					// Remove this filter from merged_filters, which specifies if filters have been sorted.
					unset( $GLOBALS['merged_filters'][ $tag ] );
				}
				return true;
			}
		}
		return false;
	}


	/**
	 * Remove Class Action Without Access to Class Object
	 *
	 * Original Attribution:
	 * Github Gist Code: https://gist.github.com/tripflex/c6518efc1753cf2392559866b4bd1a53
	 * WordPress Developer Handbook: https://developer.wordpress.org/reference/functions/remove_action/#comment-2256
	 *
	 * In order to use the core WordPress remove_action() on an action added with the callback
	 * to a class, you either have to have access to that class object, or it has to be a call
	 * to a static method.  This method allows you to remove actions with a callback to a class
	 * you don't have access to.
	 *
	 * Works with WordPress 1.2+ (4.7+ support added 9-19-2016)
	 *
	 * @param string $tag         Action to remove.
	 * @param string $class_name  Class name for the action's callback.
	 * @param string $method_name Method name for the action's callback.
	 * @param int    $priority    Priority of the action (default 10).
	 */
	public static function remove_class_action( $tag, $class_name = '', $method_name = '', $priority = 10 ) {
		self::remove_class_filter( $tag, $class_name, $method_name, $priority );
	}

	/**
	 * Given a page id assign a given page template to it.
	 * For Storefront Starter Content
	 *
	 * @param int    $page_id    Page ID.
	 * @param string $template    Template file name.
	 */
	public static function _assign_page_template( $page_id, $template ) {
		if ( empty( $page_id ) || empty( $template ) || '' === locate_template( $template ) ) {
			return false;
		}

		update_post_meta( $page_id, '_wp_page_template', $template );
	}

	/**
	 * Set WooCommerce pages to use the full width template.
	 * For Storefront Starter Content
	 */
	public static function _set_woocommerce_pages_full_width() {
		$wc_pages = self::get_woocommerce_pages();

		foreach ( $wc_pages as $option => $page_id ) {
			self::_assign_page_template( $page_id, 'template-fullwidth.php' );
		}
	}

	/**
	 * Get WooCommerce page ids.
	 * For Storefront Starter Content
	 */
	public static function get_woocommerce_pages() {
		$woocommerce_pages = array();

		$wc_pages_options = apply_filters(
			'storefront_page_option_names',
			array(
				'woocommerce_cart_page_id',
				'woocommerce_checkout_page_id',
				'woocommerce_myaccount_page_id',
				'woocommerce_shop_page_id',
				'woocommerce_terms_page_id',
			)
		);

		foreach ( $wc_pages_options as $option ) {
			$page_id = get_option( $option );

			if ( ! empty( $page_id ) ) {
				$page_id = intval( $page_id );

				if ( null !== get_post( $page_id ) ) {
					$woocommerce_pages[ $option ] = $page_id;
				}
			}
		}

		return $woocommerce_pages;
	}

}
