<?php
/**
 * Handles the dotCom tasks in the Setup List.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   2.0.0
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

use Automattic\Jetpack\Connection\Client;

/**
 * WC Calypso Bridge Setup Tasks Controller
 */
class WC_Calypso_Bridge_Setup_Tasks {

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Bridge_Setup_Tasks instance
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

		// All plans.
		add_action( 'load-woocommerce_page_wc-settings', array( $this, 'redirect_store_details_onboarding' ) );
		add_action( 'wp_ajax_launch_store', array( $this, 'handle_ajax_launch_endpoint' ) );
		add_action( 'init', array( $this, 'add_tasks' ) );
		add_filter( 'woocommerce_admin_experimental_onboarding_tasklists', [ $this, 'replace_tasks' ] );
	}

	/**
	 * Handle the AJAX endpoint for launching the Store.
	 */
	public function handle_ajax_launch_endpoint() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( new WP_Error( 'unauthorized', __( 'You don\'t have permissions to launch this site', 'wc-calypso-bridge' ) ), 400 );
			return;
		}

		if ( 'launched' === get_option( 'launch-status' ) ) {
			wp_send_json_error( new WP_Error( 'already-launched', __( 'This site has already been launched', 'wc-calypso-bridge' ) ), 400 );
			return;
		}

		$blog_id  = \Jetpack_Options::get_option( 'id' );
		$response = Client::wpcom_json_api_request_as_user(
			sprintf( '/sites/%d/launch', $blog_id ),
			'2',
			[ 'method' => 'POST' ],
			json_encode( [
				'site' => $blog_id
			] ),
			'wpcom'
		);

		// Handle error.
		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$body  = wp_remote_retrieve_body( $response );
			$error = json_decode( $body, true );
			wp_send_json_error( new WP_Error( $error[ 'code' ], $error[ 'message' ] ), 400 );
		}

		$body   = wp_remote_retrieve_body( $response );
		$status = json_decode( $body );
		wp_send_json( $status );
	}

	/**
	 * Add Setup Tasks.
	 */
	public function add_tasks() {

		if ( ! class_exists( '\Automattic\WooCommerce\Admin\Features\OnboardingTasks\TaskLists' ) ) {
			return;
		}

		/**
		 * `ecommerce_custom_setup_tasks_enabled` filter.
		 *
		 * This filter is used to remove the "add a domain" and "launch your store" tasks from ecommerce plans.
		 *
		 * @since 1.9.12
		 *
		 * @param  bool $status_enabled
		 * @return bool
		 */
		if ( ! (bool) apply_filters( 'ecommerce_custom_setup_tasks_enabled', true ) ) {
			return;
		}

		$tl = \Automattic\WooCommerce\Admin\Features\OnboardingTasks\TaskLists::instance();
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/tasks/class-wc-calypso-task-add-domain.php';
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/tasks/class-wc-calypso-task-launch-site.php';

		$list = $tl::get_lists_by_ids( array( 'setup' ) );
		$list = array_pop( $list );

		$add_domain_task  = new \Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks\AddDomain( $list );
		$launch_site_task = new \Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks\LaunchSite( $list );
		$tl::add_task( 'setup', $add_domain_task );
		$tl::add_task( 'setup', $launch_site_task );
	}

	/**
	 * Replace setup tasks.
	 */
	public function replace_tasks( $lists ) {
		if ( isset( $lists['setup'] ) ) {
			foreach ($lists['setup']->tasks as $index => $task) {
				switch ( $task->get_id() ) {
					case 'products':
						require_once __DIR__ . '/tasks/class-wc-calypso-task-headstart-products.php';
						$lists['setup']->tasks[$index] = new \Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks\HeadstartProducts( $lists['setup'] );
						break;
					case 'appearance':
						require_once __DIR__ . '/tasks/class-wc-calypso-task-appearance.php';
						$lists['setup']->tasks[$index] = new \Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks\WCBridgeAppearance( $lists['setup'] );
						break;
					case 'purchase':
						// Remove the purchase task
						unset( $lists['setup']->tasks[$index] );
						break;
				}
			}
		}
		return $lists;
	}

	/**
	 * Handle the store location's onboarding redirect when user provided a full address.
	 *
	 * @since 1.9.4
	 * @return void
	 */
	public function redirect_store_details_onboarding() {

		// Only run on save.
		if ( empty( $_POST ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return;
		}

		if ( ! isset( $_GET['tutorial'] ) || 'true' !== $_GET['tutorial'] ) {
			return;
		}

		$store_address  = get_option( 'woocommerce_store_address' );
		$store_city     = get_option( 'woocommerce_store_city' );
		$store_postcode = get_option( 'woocommerce_store_postcode' );

		if ( ! empty( $store_address ) && ! empty( $store_city ) && ! empty( $store_postcode ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=wc-admin' ) );
		}
	}
}

WC_Calypso_Bridge_Setup_Tasks::get_instance();
