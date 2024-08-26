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
		add_action( 'update_option_wpcom_public_coming_soon', array( $this, 'sync_coming_soon_wpcom_to_lys' ), 10, 3 );
		$this->hook_update_option_woocommerce_coming_soon();
	}

	/**
	 * Hook on woocommerce_coming_soon option update for ease of use.
	 */
	public function hook_update_option_woocommerce_coming_soon() {
		add_action( 'update_option_woocommerce_coming_soon' , array( $this, 'sync_coming_soon_lys_to_wpcom' ), 10, 3 );
	}

	/**
	 * Unhook on woocommerce_coming_soon option update for ease of use.
	 */
	public function unhook_update_option_woocommerce_coming_soon() {
		remove_action( 'update_option_woocommerce_coming_soon' , array( $this, 'sync_coming_soon_lys_to_wpcom' ), 10, 3 );
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
	 * @return void
	 */
	public function sync_coming_soon_wpcom_to_lys( $old_value, $new_value ) {
		if ( ! class_exists( '\Automattic\WooCommerce\Admin\Features\Features' ) || ! \Automattic\WooCommerce\Admin\Features\Features::is_enabled( 'launch-your-store' ) ) {
			return;
		}

		$woocommerce_coming_soon = get_option( 'woocommerce_coming_soon', false );
		$is_coming_soon_wpcom = 1 === (int) $new_value;

		// Value is already set, we don't need to update option.
		if ( ( 'yes' ===  $woocommerce_coming_soon && $is_coming_soon_wpcom ) ||
			( 'no' === $woocommerce_coming_soon && ! $is_coming_soon_wpcom ) ) {
			return;
		}

		// Unhook listener to prevent a loop of updating option between WPCOM <-> LYS.
		$this->unhook_update_option_woocommerce_coming_soon();

		if ( $is_coming_soon_wpcom ) {
			update_option( 'woocommerce_coming_soon', 'yes' );
		} else {
			update_option( 'woocommerce_coming_soon', 'no' );
		}

		$this->hook_update_option_woocommerce_coming_soon();
	}

	/**
	 * Sync the coming soon option from to woocommerce_coming_soon wpcom.
	 * Does not include a check of existing wpcom option values since
	 * there could be multiple options that are affected.
	 *
	 * @param int $old_value
	 * @param int $new_value
	 * @return void
	 */
	public function sync_coming_soon_lys_to_wpcom( $old_value, $new_value ) {
		$is_atomic_launched = 'launched' === get_option( 'launch-status' );
		$response = false;

		if ( 'no' === $new_value ) {
			if ( ! $is_atomic_launched ) {
				$response = WC_Calypso_Bridge_Atomic_Launch_API::launch_site();
			} else {
				$response = WC_Calypso_Bridge_Atomic_Launch_API::update_coming_soon( false );
			}
		} elseif ( $is_atomic_launched && 'yes' === $new_value ) {
			$response = WC_Calypso_Bridge_Atomic_Launch_API::update_coming_soon( true );
		}

		if ( $response && 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$body  = wp_remote_retrieve_body( $response );
			$error = json_decode( $body, true );

			if ( isset( $error['message'] ) ) {
				$this->add_admin_notice( $error[ 'message' ], 'error' );
			} else {
				$this->add_admin_notice( __( 'There was a problem trying to update site visibility.', 'wc-calypso-bridge' ), 'error' );
			}
		}
	}

	public function add_admin_notice( $message, $notice_type = 'info' ) {
		add_action( 'admin_notices', function () use ( $message, $notice_type ) {
			$screen    = get_current_screen();
			$screen_id = $screen ? $screen->id : '';

			if ( 'woocommerce_page_wc-settings' !== $screen_id ) {
				return;
			}

			if ( ! isset( $_GET['tab'] ) || 'site-visibility' !== $_GET['tab'] ) {
				return;
			}

			?>
			<div class="notice notice-<?php echo $notice_type ?>">
				<p><?php echo $message; ?></p>
			</div>
			<?php
		} );
	}
}

WC_Calypso_Bridge_Coming_Soon::get_instance();
