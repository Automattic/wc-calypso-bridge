<?php

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Calypso_Bridge_Coming_Soon
 *
 * @since   2.6.0
 * @version 2.8.1
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
		add_filter( 'pre_option_woocommerce_coming_soon', array( $this, 'override_option_woocommerce_coming_soon' ) );
		add_filter( 'pre_update_option_woocommerce_coming_soon', array( $this, 'override_update_woocommerce_coming_soon' ), 10, 2 );
		// Admin bar menu is not only shown in the admin area but also in the front end when the admin user is logged in.
		add_action( 'admin_bar_menu', array( $this, 'remove_site_visibility_badge' ), 32 );
		add_filter( 'rest_pre_dispatch', array( $this, 'handle_initial_coming_soon_endpoint' ), 10, 3 );

		if ( is_admin() ) {
			add_filter( 'plugins_loaded', array( $this, 'maybe_add_admin_notice' ) );
			add_filter( 'woocommerce_get_settings_site-visibility', array( $this, 'possibly_hide_site_visibility_form' ) );
		}
	}

	/**
	 * Hide the a8c coming soon page if the Launch Your Store feature is enabled.
	 *
	 * @param bool $should_show Whether to show the coming soon page.
	 * @return bool
	 */
	public function should_show_a8c_coming_soon_page( $should_show ) {
		if ( $this->is_feature_enabled() && ! wc_calypso_bridge_is_trial_plan() ) {
			return false;
		}

		return $should_show;
	}

	/**
	 * Exclude the coming soon page if the user is accessing the site via a valid share link.
	 *
	 * @param bool $exclude Whether to exclude the coming soon page.
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
	 * Override the coming soon option value.
	 *
	 * @param string $current_value The current option value.
	 * @return string
	 */
	public function override_option_woocommerce_coming_soon( $current_value ) {
		// Turn off coming soon mode for trial plan.
		if ( wc_calypso_bridge_is_trial_plan() ) {
			return 'no';
		}

		if ( ! $this->is_feature_enabled() || ! $this->is_private_site_available() ) {
			return $current_value;
		}
		// Either private or coming soon is considered as coming soon.
		return \Private_Site\site_is_private() || \Private_Site\site_is_public_coming_soon() ? 'yes' : 'no';
	}

	/**
	 * Sends API request to WPCOM to update coming soon state or launch the site
	 * when the option is updated. Still sets the underlying option value
	 * to accommodate exporting sites via manual means.
	 *
	 * @param string $new_value The new option value.
	 * @param string $old_value The old option value.
	 * @return string
	 */
	public function override_update_woocommerce_coming_soon( $new_value, $old_value ) {
		if ( ! $this->is_feature_enabled() || ! $this->is_private_site_available() ) {
			return $new_value;
		}

		$is_atomic_launched = 'unlaunched' !== \Private_Site\site_launch_status();
		$response           = false;

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
				$this->add_admin_notice( $error['message'], 'error' );
			} else {
				$this->add_admin_notice( __( 'There was a problem trying to update site visibility.', 'wc-calypso-bridge' ), 'error' );
			}

			// Don't update option value if there's an error.
			return $old_value;
		}

		return $new_value;
	}

	/**
	 * Conditionally add a notice for free trial sites or when site is unlaunched.
	 *
	 * @return void
	 */
	public function maybe_add_admin_notice() {
		if ( ! $this->is_feature_enabled() ) {
			return;
		}

		if ( wc_calypso_bridge_is_trial_plan() ) {
			$upgrade_url = sprintf( 'https://wordpress.com/plans/%s', WC_Calypso_Bridge_Instance()->get_site_slug() );
			$this->add_admin_notice( sprintf( __( 'You’re currently using a free trial! To get access to the full range of features, please <a href="%s">upgrade to a paid plan</a>.', 'wc-calypso-bridge' ), esc_url( $upgrade_url ) ), 'info' );
			return;
		}

		if ( ! $this->is_private_site_available() ) {
			return;
		}

		$launch_status = \Private_Site\site_launch_status();
		if ( 'unlaunched' === $launch_status ) {
			$task_url = '/wp-admin/admin.php?page=wc-admin&path=%2Flaunch-your-store';
			$this->add_admin_notice( sprintf( __( 'Looking to launch your site? Please visit <a href="%s">this link</a> to get started.', 'wc-calypso-bridge' ), esc_url( $task_url ) ), 'info' );
		}
	}

	/**
	 * Add admin notice to the Site Visibility settings page.
	 *
	 * @param string $message     The message to display.
	 * @param string $notice_type The notice type.
	 * @return void
	 */
	public function add_admin_notice( $message, $notice_type = 'info' ) {
		add_action(
			'admin_notices',
			function () use ( $message, $notice_type ) {
				$screen    = get_current_screen();
				$screen_id = $screen ? $screen->id : '';

				if ( 'woocommerce_page_wc-settings' !== $screen_id ) {
					return;
				}

				if ( ! isset( $_GET['tab'] ) || 'site-visibility' !== $_GET['tab'] ) {
					return;
				}

				?>
				<div class="notice notice-<?php echo esc_attr( $notice_type ); ?>">
					<p><?php echo wp_kses_post( $message ); ?></p>
				</div>
				<?php
			}
		);
	}

	/**
	 * Possibly remove the site visibility badge from the admin bar.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar The admin bar instance.
	 * @return void
	 */
	public function remove_site_visibility_badge( $wp_admin_bar ) {
		if ( $wp_admin_bar ) {
			$wp_admin_bar->remove_node( 'woocommerce-site-visibility-badge' );
		}
	}

	/**
	 * Possibly hide the site visibility form in the Site Visibility settings for free trial sites.
	 *
	 * @param array $settings The settings.
	 * @return array
	 */
	public function possibly_hide_site_visibility_form( $settings ) {
		if ( wc_calypso_bridge_is_trial_plan() ) {
			return array();
		}
		return $settings;
	}


	/**
	 * Handle the initial coming soon endpoint when coming soon mode is already enabled.
	 *
	 * This is to prevent the API request from being made when coming soon mode is already enabled
	 * so that store pages only option won't be enabled when WPCOM site is in coming soon mode.
	 *
	 * @param mixed $result The dispatch result.
	 * @param WP_REST_Server $server The REST server instance.
	 * @param WP_REST_Request $request The request object.
	 * @return mixed
	 */
	public function handle_initial_coming_soon_endpoint( $result, $server, $request ) {
		if ( '/wc-admin/launch-your-store/initialize-coming-soon' !== $request->get_route() ) {
			// Abort if the request is not for the coming soon endpoint. This should be faster than checking if the feature is enabled.
			return $result;
		}

		if ( ! $this->is_feature_enabled() ) {
			return $result;
		}

		if ( 'yes' === get_option( 'woocommerce_coming_soon' ) ) {
			return new WP_REST_Response( true, 200 );
		}

		// Site is live, set default option for store pages only to true and proceed with the request.
		add_option( 'woocommerce_store_pages_only', 'yes' );
		return $result;
	}

	/**
	 * Check if the Launch Your Store feature is enabled.
	 *
	 * @return bool
	 */
	private function is_feature_enabled() {
		return class_exists( '\Automattic\WooCommerce\Admin\Features\Features' ) && \Automattic\WooCommerce\Admin\Features\Features::is_enabled( 'launch-your-store' );
	}

	/**
	 * Check if Private_Site class and functions are available.
	 *
	 * @return bool
	 */
	private function is_private_site_available() {
		return function_exists( '\Private_Site\site_is_private' ) &&
			function_exists( '\Private_Site\site_is_public_coming_soon' ) &&
			function_exists( '\Private_Site\site_launch_status' );
	}
}

WC_Calypso_Bridge_Coming_Soon::get_instance();
