<?php
/**
 * Adds a new WC setup page with a checklist of steps for setting up your store.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Calypso_Bridge_Admin_Setup_Checklist class.
 */
class WC_Calypso_Bridge_Admin_Setup_Checklist {

	/**
	 * Instance variable
	 *
	 * @var WC_Calypso_Bridge_Admin_Setup_Checklist instance
	 */
	protected static $instance = false;

	/**
	 * Provide only a single instance of this class.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Hooks into WordPress to add our new setup checklist.
	 */
	private function __construct() {
		$this->handle_redirects();

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		// priority is 20 to run after https://github.com/woocommerce/woocommerce/blob/a55ae325306fc2179149ba9b97e66f32f84fdd9c/includes/admin/class-wc-admin-menus.php#L165.
		add_action( 'admin_head', array( $this, 'menu_order_count' ) );
		add_action( 'admin_notices', array( $this, 'return_notice' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_checklist_styles' ) );
		add_action( 'wp_ajax_set_woocommerce_setup_active_task', array( $this, 'set_active_task_ajax' ) );
		add_action( 'wp_ajax_clear_woocommerce_setup_active_task', array( $this, 'clear_active_task_ajax' ) );
		add_action( 'admin_post_storefront_ecommerce_plan_starter_content', array( $this, 'redirect_customizer' ) );

		if ( isset( $_GET['page'] ) && 'wc-setup-checklist' === $_GET['page'] ) {
			add_action( 'admin_init', array( $this, 'clear_active_task' ) );
			add_action( 'admin_head', array( $this, 'remove_notices' ) );

			if ( isset( $_GET['finished'] ) ) {
				add_action( 'admin_init', array( $this, 'mark_finished_and_redirect' ) );
			}
		}

		if ( isset( $_GET['wc-setup-step'] ) ) {
			add_action( 'admin_init', array( $this, 'track_step_click' ) );
		}
	}

	/**
	 * Handles a few different cases depending on how far along a user is.
	 * If they have finished the checklist and clicked "I'm done", they get sent to the orders page.
	 * If they haven't even gone through the wizard yet, they are sent there.
	 */
	private function handle_redirects() {
		if ( empty( $_GET['page'] ) || 'wc-setup-checklist' !== $_GET['page'] ) {
			return;
		}

		// If setup has been completed, redirect to orders page.
		// TODO Redirect to wc-admin once we launch it on eCommerce plans.
		if ( $this->is_checklist_done() ) {
			wp_safe_redirect( admin_url( 'edit.php?post_type=shop_order' ) );
			exit;
		}

		// If the setup wizard was never complete, redirect to the setup wizard.
		$notices = WC_Admin_Notices::get_notices();
		if ( in_array( 'install', $notices ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=wc-setup' ) );
			exit;
		}
	}

	/**
	 * Returns if the checklist is "done" via the user clicking the "I'm done" button.
	 *
	 * @return bool True if the done button has been clicked.
	 */
	private function is_checklist_done() {
		return (bool) get_option( 'atomic-ecommerce-setup-checklist-complete', false );
	}

	/**
	 * Handles redirect to the customizer for starter content.
	 */
	public function redirect_customizer() {
		check_admin_referer( 'storefront_ecommerce_plan_starter_content' );

		if ( current_user_can( 'manage_options' ) ) {

			// Dismiss notice.
			update_option( 'storefront_nux_dismissed', true );
		}

			$args = array( 'sf_starter_content' => '1' );

			$tasks = array();

		if ( ! empty( $_REQUEST['homepage'] ) && 'on' === $_REQUEST['homepage'] ) {
			if ( current_user_can( 'edit_pages' ) && 'page' === get_option( 'show_on_front' ) ) {
				WC_Calypso_Bridge_Helper_Functions::_assign_page_template( get_option( 'page_on_front' ), 'template-homepage.php' );
			} else {
				$tasks[] = 'homepage';
			}
		}

		if ( ! empty( $_REQUEST['products'] ) && 'on' === $_REQUEST['products'] ) {
			$tasks[] = 'products';
		}

		if ( ! empty( $tasks ) ) {
			$args['sf_tasks'] = implode( ',', $tasks );

			if ( current_user_can( 'manage_options' ) ) {

				// Make sure the fresh_site flag is set to true.
				update_option( 'fresh_site', true );

				if ( current_user_can( 'edit_pages' ) && true === (bool) get_option( 'storefront_nux_fresh_site' ) ) {
					WC_Calypso_Bridge_Helper_Functions::_set_woocommerce_pages_full_width();
				}
			}
		}

			// Redirect to the Checklist screen when exiting the Customizer.
			$args['return'] = urlencode( admin_url( 'admin.php?page=wc-setup-checklist&wc-setup-step=customize' ) );

			wp_safe_redirect( add_query_arg( $args, admin_url( 'customize.php' ) ) );

			die();
	}

	/**
	 * Mark's the setup screen as complete (and redirects the user to the orders page)
	 */
	public function mark_finished_and_redirect() {
		update_option( 'atomic-ecommerce-setup-checklist-complete', true );
		wp_redirect( admin_url( 'edit.php?post_type=shop_order' ) );
		exit;
	}

	/**
	 * Remove all admin notices
	 */
	public function remove_notices() {
		remove_all_actions( 'admin_notices' );
	}

	/**
	 * Loads checklist CSS
	 */
	public function load_checklist_styles() {
		$asset_path = WC_Calypso_Bridge::$plugin_asset_path ? WC_Calypso_Bridge::$plugin_asset_path : WC_Calypso_Bridge::MU_PLUGIN_ASSET_PATH;
		wp_enqueue_style( 'wc-calypso-bridge-setup-checklist-style', $asset_path . 'assets/css/setup-checklist.css', array(), WC_CALYPSO_BRIDGE_CURRENT_VERSION, 'all' );
		wp_enqueue_script( 'wc-calypso-bridge-setup-checklist', $asset_path . 'assets/js/setup-checklist.js', array( 'jquery' ), WC_CALYPSO_BRIDGE_CURRENT_VERSION, true );

		wp_localize_script(
			'wc-calypso-bridge-setup-checklist',
			'i18nstrings',
			array(
				'hide' => esc_html__( 'Hide completed', 'wc-calypso-bridge' ),
				'show' => esc_html__( 'Show completed', 'wc-calypso-bridge' ),
			)
		);
		wp_localize_script(
			'wc-calypso-bridge-setup-checklist',
			'wccb',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'woocommerce_setup_active_task' ),
			)
		);
	}

	/**
	 * Adds a new page for the setup checklist.
	 */
	public function admin_menu() {
		if ( $this->is_checklist_done() ) {
			return;
		}

		add_menu_page(
			__( 'Setup', 'wc-calypso-bridge' ),
			__( 'Setup', 'wc-calypso-bridge' ),
			'manage_woocommerce',
			'wc-setup-checklist',
			array( $this, 'checklist' ),
			'dashicons-admin-tools',
			0
		);
	}

	/**
	 * Sets the active step
	 */
	public function set_active_task_ajax() {
		check_ajax_referer( 'woocommerce_setup_active_task', 'nonce' );
		if ( isset( $_GET['taskId'] ) ) {
			update_option( 'woocommerce_setup_active_task', sanitize_text_field( wp_unslash( $_GET['taskId'] ) ) ); // WPCS: CSRF ok.
		}
	}

	/**
	 * Removes the active task
	 */
	public function clear_active_task() {
		delete_option( 'woocommerce_setup_active_task' );
	}

	/**
	 * Removes the active task via AJAX
	 */
	public function clear_active_task_ajax() {
		check_ajax_referer( 'woocommerce_setup_active_task', 'nonce' );
		$this->clear_active_task();
	}

	/**
	 * Shows a return notice if an active task is set and condition is met
	 */
	public function return_notice() {
		$task_id = get_option( 'woocommerce_setup_active_task', false );
		if ( $task_id ) {
			$data = $this->get_task_data();
			foreach ( $data['tasks'] as $task ) {
				if ( $task_id === $task['id'] ) {
					$active_task = $task;
				}
			}
			if ( $active_task && $active_task['condition'] ) {
				?>
				<div class="notice notice-success is-dismissible" id="woocommerce-setup-return-notice">
					<p>
						<?php
						echo sprintf(
							wp_kses( 'Ready to continue with the setup checklist?  <a href="%s">Back to setup</a>.', 'wc-calypso-bridge' ),
							esc_url( admin_url( 'admin.php?page=wc-setup-checklist' ) )
						);
						?>
					</p>
				</div>
				<?php
			}
		}
	}

	/**
	 * Tracks a step has completed after visting a specific setup link.
	 */
	public function track_step_click() {
		if ( ! isset( $_GET['wc-setup-step'] ) ) { // WPCS: CSRF ok.
			return;
		}

		$whitelist = array( 'customize', 'shipping', 'product', 'stripe', 'paypal' );
		$step      = $_GET['wc-setup-step']; // WPCS: CSRF ok, sanitization ok.
		if ( ! in_array( $step, $whitelist, true ) ) {
			return;
		}

		$click_settings          = get_option( 'woocommerce_setup_checklist_clicks', array() );
		$click_settings[ $step ] = true;

		update_option( 'woocommerce_setup_checklist_clicks', $click_settings );
	}

	/**
	 * Adds a count of uncompleted tasks to the navigation sidebar.
	 */
	public function menu_order_count() {
		global $menu;
		if ( $this->is_checklist_done() ) {
			return;
		}

		foreach ( $menu as $key => $menu_item ) {
			if ( 'wc-setup-checklist' === $menu_item[2] ) {
				$data        = $this->get_task_data();
				$setup_count = $data['uncompleted'];

				if ( current_user_can( 'manage_woocommerce' ) && $setup_count ) {
						$menu[ $key ][0] .= ' <span class="update-plugins count-' . esc_attr( $setup_count ) . '"><span class="setup-count">' . esc_html( number_format_i18n( $setup_count ) ) . '</span></span>'; // WPCS: override ok.
						break;
				}
			}
		}
	}

	/**
	 * Returns an array of relevent setup tasks and meta information (completed tasks, uncompleted, etc).
	 *
	 * New tasks can be added to the array below. They use the following format:
	 * title - Task title (e.g. 'Add a product')
	 * completed_title - Used for the action link once a task is completed (e.g. 'View settings')
	 * description - A description of the task
	 * estimate - An estimate in minutes how long the task will take
	 * link - Destination for the action button
	 * learn_more - If present, a learn more link will be appended to the description.
	 * extension - Plugin slug (can be taken from  wp-admin's plugin page). If present, this task will only show when the extension is active.
	 * condition - A conditional to determine if the task is completed or not.
	 */
	private function get_task_data() {
		$ups_settings                     = get_option( 'woocommerce_ups_settings' );
		$square_merchant_access_token     = get_option( 'woocommerce_square_merchant_access_token' );
		$paypal_settings                  = get_option( 'woocommerce_ppec_paypal_settings' );
		$stripe_settings                  = get_option( 'woocommerce_stripe_settings' );
		$klarna_payments_settings         = get_option( 'woocommerce_klarna_payments_settings' );
		$kco_settings                     = get_option( 'woocommerce_kco_settings' );
		$eway_settings                    = get_option( 'woocommerce_eway_settings' );
		$payfast_settings                 = get_option( 'woocommerce_payfast_settings' );
		$taxjar_settings                  = get_option( 'woocommerce_taxjar-integration_settings' );
		$facebook_settings                = get_option( 'woocommerce_facebookcommerce_settings' );
		$mailchimp_settings               = get_option( 'mailchimp-woocommerce' );
		$click_settings                   = get_option( 'woocommerce_setup_checklist_clicks' );
		$wc_canada_post_merchant_username = get_option( 'wc_canada_post_merchant_username' );
		$wc_canada_post_merchant_password = get_option( 'wc_canada_post_merchant_password' );
		$has_starter_content_imported     = get_option( 'wpcom_ec_plan_starter_content_imported', false );

		if ( ! $has_starter_content_imported ) {
			$sf_nonce               = wp_create_nonce( 'storefront_ecommerce_plan_starter_content' );
			$customize_extra_params = esc_attr( 'sf_starter_content=1&homepage=on&products=on&action=storefront_ecommerce_plan_starter_content&_wpnonce=' . $sf_nonce . '&wc-setup-step=customize' );
			$customizer_link        = 'admin-post.php?' . $customize_extra_params;
		} else {
			$customizer_link = 'customize.php?return=%2Fwp-admin%2Fadmin.php%3Fpage%3Dwc-setup-checklist&wc-setup-step=customize';
		}

		$all_tasks = array(
			array(
				'id'              => 'add-product',
				'title'           => __( 'Add a product', 'wc-calypso-bridge' ),
				'completed_title' => __( 'Add another product', 'wc-calypso-bridge' ),
				'description'     => __( 'Start by adding your first product to your store.', 'wc-calypso-bridge' ),
				'estimate'        => '2',
				'link'            => 'post-new.php?post_type=product&wc-setup-step=product',
				'condition'       => isset( $click_settings['product'] ) && true === (bool) $click_settings['product'],
			),

			array(
				'id'              => 'customize',
				'title'           => __( 'View and customize', 'wc-calypso-bridge' ),
				'completed_title' => __( 'Open customizer', 'wc-calypso-bridge' ),
				'description'     => __( 'You have access to a few themes with your plan. See the options, chose the right one for you and customize your store.', 'wc-calypso-bridge' ),
				'estimate'        => '2',
				'link'            => $customizer_link,
				'condition'       => isset( $click_settings['customize'] ) && true === (bool) $click_settings['customize'],
			),

			array(
				'id'              => 'shipping',
				'title'           => __( 'Review shipping', 'wc-calypso-bridge' ),
				'completed_title' => __( 'View Settings', 'wc-calypso-bridge' ),
				'description'     => __( "We've set up a few shipping options based on your store location. Check them out to see if they're right for you.", 'wc-calypso-bridge' ),
				'estimate'        => '2',
				'link'            => 'admin.php?page=wc-settings&tab=shipping&wc-setup-step=shipping',
				'condition'       => isset( $click_settings['shipping'] ) && true === (bool) $click_settings['shipping'],
			),

			array(
				'id'              => 'ups',
				'title'           => __( 'Add live rates with UPS', 'wc-calypso-bridge' ),
				'completed_title' => __( 'View Settings', 'wc-calypso-bridge' ),
				'description'     => __( "Showing shipping rates directly from UPS during checkout ensures you're charging customers the right amount for shipping.", 'wc-calypso-bridge' ),
				'estimate'        => '2',
				'link'            => 'admin.php?page=wc-settings&tab=shipping&section=ups',
				'condition'       => ! empty( $ups_settings['user_id'] ) &&
							   ! empty( $ups_settings['password'] ) &&
							   ! empty( $ups_settings['access_key'] ) &&
							   ! empty( $ups_settings['shipper_number'] ),
				'extension'       => 'woocommerce-shipping-ups/woocommerce-shipping-ups.php',
			),

			array(
				'id'              => 'canada-post',
				'title'           => __( 'Add live rates with Canada Post', 'wc-calypso-bridge' ),
				'completed_title' => __( 'View Settings', 'wc-calypso-bridge' ),
				'description'     => __( 'Get shipping rates for domestic and international parcels.', 'wc-calypso-bridge' ),
				'estimate'        => '2',
				'link'            => 'https://woocommerce.com/wc-api/canada_post_registration?return_url=' . WC()->api_request_url( 'canada_post_return' ),
				'condition'       => ! empty( $wc_canada_post_merchant_username ) &&
							   ! empty( $wc_canada_post_merchant_password ),
				'extension'       => 'woocommerce-shipping-canada-post/woocommerce-shipping-canada-post.php',
			),

			array(
				'id'              => 'square',
				'title'           => __( 'Setup payments with Square', 'wc-calypso-bridge' ),
				'completed_title' => __( 'View Settings', 'wc-calypso-bridge' ),
				'description'     => __( 'Connect your Square account to accept credit and debit card, to track sales and sync inventory.', 'wc-calypso-bridge' ),
				'estimate'        => '2',
				'link'            => 'admin.php?page=wc-settings&tab=integration&section=squareconnect',
				'learn_more'      => 'https://woocommerce.com/products/square/',
				'condition'       => ! empty( $square_merchant_access_token ),
				'extension'       => 'woocommerce-square/woocommerce-square.php',
			),

			array(
				'id'              => 'stripe',
				'title'           => __( 'Setup payments with Stripe', 'wc-calypso-bridge' ),
				'completed_title' => __( 'View Settings', 'wc-calypso-bridge' ),
				'description'     => __( 'Connect your Stripe account to accept credit and debit card payments.', 'wc-calypso-bridge' ),
				'estimate'        => '2',
				'link'            => 'admin.php?page=wc-settings&tab=checkout&section=stripe&return=%2Fwp-admin%2Fadmin.php%3Fpage%3Dwc-setup-checklist&wc-setup-step=stripe',
				'learn_more'      => 'https://woocommerce.com/products/stripe/',
				'condition'       => isset( $click_settings['stripe'] ) &&
								true === (bool) $click_settings['stripe'] &&
								! empty( $stripe_settings['publishable_key'] ) &&
								! empty( $stripe_settings['secret_key'] ) &&
								'yes' === $stripe_settings['enabled'],
				'extension'       => 'woocommerce-gateway-stripe/woocommerce-gateway-stripe.php',
			),

			array(
				'id'              => 'paypal',
				'title'           => __( 'Setup payments with PayPal', 'wc-calypso-bridge' ),
				'completed_title' => __( 'View Settings', 'wc-calypso-bridge' ),
				'description'     => __( 'Connect your PayPal account to let customers to conveniently checkout directly with PayPal.', 'wc-calypso-bridge' ),
				'estimate'        => '2',
				'link'            => 'admin.php?page=wc-settings&tab=checkout&section=ppec_paypal&&return=%2Fwp-admin%2Fadmin.php%3Fpage%3Dwc-setup-checklist&wc-setup-step=paypal',
				'learn_more'      => 'https://woocommerce.com/products/woocommerce-gateway-paypal-checkout/',
				'condition'       => isset( $click_settings['paypal'] ) &&
								true === (bool) $click_settings['paypal'] &&
								! empty( $paypal_settings['api_username'] ) &&
								! empty( $paypal_settings['api_password'] ) &&
								( ! empty( $paypal_settings['api_signature'] ) || ! empty( $paypal_settings['api_certificate'] ) ) &&
								'yes' === $paypal_settings['enabled'],
				'extension'       => 'woocommerce-gateway-paypal-express-checkout/woocommerce-gateway-paypal-express-checkout.php',
			),

			array(
				'id'              => 'klarna-payments',
				'title'           => __( 'Setup payments with Klarna', 'wc-calypso-bridge' ),
				'completed_title' => __( 'View Settings', 'wc-calypso-bridge' ),
				'description'     => __( 'Connect your Klarna account to take payments with pay now, pay later and slice it.', 'wc-calypso-bridge' ),
				'estimate'        => '2',
				'link'            => 'admin.php?page=wc-settings&tab=checkout&section=klarna_payments',
				'learn_more'      => 'https://woocommerce.com/products/klarna-payments/',
				'condition'       => 'yes' === $klarna_payments_settings['enabled'],
				'extension'       => 'klarna-payments-for-woocommerce/klarna-payments-for-woocommerce.php',
			),

			array(
				'id'              => 'klarna-checkout',
				'title'           => __( 'Setup checkout with Klarna', 'wc-calypso-bridge' ),
				'completed_title' => __( 'View Settings', 'wc-calypso-bridge' ),
				'description'     => __( 'Setup to provide a full checkout experience with pay now, pay later and slice it.', 'wc-calypso-bridge' ),
				'estimate'        => '2',
				'link'            => 'admin.php?page=wc-settings&tab=checkout&section=kco',
				'learn_more'      => 'https://woocommerce.com/products/klarna-checkout/',
				'condition'       => 'yes' === $kco_settings['enabled'],
				'extension'       => 'klarna-checkout-for-woocommerce/klarna-checkout-for-woocommerce.php',
			),

			array(
				'id'              => 'eway',
				'title'           => __( 'Setup payments with eWAY', 'wc-calypso-bridge' ),
				'completed_title' => __( 'View Settings', 'wc-calypso-bridge' ),
				'description'     => __( 'Connect your eWay account to take credit card payments directly on your store.', 'wc-calypso-bridge' ),
				'estimate'        => '2',
				'link'            => 'admin.php?page=wc-settings&tab=checkout&section=eway',
				'condition'       => ! empty( $eway_settings['customer_api'] ) &&
							   ! empty( $eway_settings['customer_password'] ) &&
							   'yes' === $eway_settings['enabled'],
				'extension'       => 'woocommerce-gateway-eway/woocommerce-gateway-eway.php',
			),

			array(
				'id'              => 'payfast',
				'title'           => __( 'Setup payments with PayFast', 'wc-calypso-bridge' ),
				'completed_title' => __( 'View Settings', 'wc-calypso-bridge' ),
				'description'     => __( 'Connect your PayFast account to accept payments by credit card and Electronic Fund Transfer.', 'wc-calypso-bridge' ),
				'estimate'        => '2',
				'link'            => 'admin.php?page=wc-settings&tab=checkout&section=payfast',
				'condition'       => ! empty( $payfast_settings['merchant_id'] ) &&
							   ! empty( $payfast_settings['merchant_key'] ) &&
							   ! empty( $payfast_settings['pass_phrase'] ) &&
							   'yes' === $payfast_settings['enabled'],
				'extension'       => 'woocommerce-payfast-gateway/gateway-payfast.php',
			),

			array(
				'id'              => 'taxjar',
				'title'           => __( 'Enable automatic tax rates with TaxJar', 'wc-calypso-bridge' ),
				'completed_title' => __( 'View Settings', 'wc-calypso-bridge' ),
				'description'     => __( 'Automatically collect sales tax at checkout by connecting with TaxJar.', 'wc-calypso-bridge' ),
				'estimate'        => '2',
				'link'            => 'admin.php?page=wc-settings&tab=integration&section=taxjar-integration',
				'condition'       => ! empty( $taxjar_settings['api_token'] ),
				'extension'       => 'taxjar-simplified-taxes-for-woocommerce/taxjar-woocommerce.php',
			),

			array(
				'id'              => 'facebook',
				'title'           => __( 'Integrate with Facebook', 'wc-calypso-bridge' ),
				'completed_title' => __( 'View Settings', 'wc-calypso-bridge' ),
				'description'     => __( 'Integrating Facebook with your store and drive sales.', 'wc-calypso-bridge' ),
				'estimate'        => '5',
				'link'            => 'admin.php?page=wc-settings&tab=integration&section=facebookcommerce',
				'learn_more'      => 'https://www.facebook.com/business/help/900699293402826',
				'condition'       => ! empty( $facebook_settings['fb_api_key'] ),
				'extension'       => 'facebook-for-woocommerce/facebook-for-woocommerce.php',
			),

			array(
				'id'              => 'mailchimp',
				'title'           => __( 'Integrate with Mailchimp', 'wc-calypso-bridge' ),
				'completed_title' => __( 'View Settings', 'wc-calypso-bridge' ),
				'description'     => __( 'Connect your store to bring the power of email marketing to your business.', 'wc-calypso-bridge' ),
				'estimate'        => '5',
				'link'            => 'options-general.php?page=mailchimp-woocommerce',
				'learn_more'      => 'https://wordpress.org/plugins/mailchimp-for-woocommerce/',
				'condition'       => ! empty( $mailchimp_settings['mailchimp_api_key'] ),
				'extension'       => 'mailchimp-for-woocommerce/mailchimp-woocommerce.php',
			),
		);

		$completed = 0;
		$tasks     = array();
		foreach ( $all_tasks as $task ) {
			// Remove tasks for extensions that are not active.
			if ( isset( $task['extension'] ) ) {
				if ( ! is_plugin_active( $task['extension'] ) ) {
					continue;
				}
			}

			$tasks[] = $task;
			if ( true === $task['condition'] ) {
				$completed++;
			}
		}
		$total = count( $tasks );

		return( array(
			'tasks'       => $tasks,
			'completed'   => $completed,
			'uncompleted' => $total - $completed,
			'total'       => $total,
		) );
	}

	/**
	 * Renders the checklist display.
	 */
	public function checklist() {
		$data       = $this->get_task_data();
		$percentage = floor( ( $data['completed'] / $data['total'] ) * 100 );

		$asset_path = WC_Calypso_Bridge::$plugin_asset_path ? WC_Calypso_Bridge::$plugin_asset_path : WC_Calypso_Bridge::MU_PLUGIN_ASSET_PATH;
		?>
			<div class="setup-header">
				<img src="<?php echo esc_attr( $asset_path . 'assets/images/woocommerce-setup.svg' ); ?>" width="160" alt="" />
				<div>
					<h2><?php esc_html_e( 'Get ready to start selling.', 'wc-calypso-bridge' ); ?></h2>
					<p><?php esc_html_e( "Here are the things you'll need to do to get started.", 'wc-calypso-bridge' ); ?></p>
				</div>
			</div>
			<div id="checklist" class="checklist is-expanded">
				<div class="checklist-card checklist__header is-compact">
					<div class="checklist__header-main">
						<div class="checklist__header-progress">
							<h2 class="checklist__header-progress-text"><?php esc_html_e( 'Your setup list', 'wc-calypso-bridge' ); ?></h2>
							<span class="checklist__header-progress-number"><?php echo esc_html( $data['completed'] ); ?>/<?php echo esc_html( $data['total'] ); ?></span>
						</div>
						<div class="progress-bar is-compact">
							<div class="progress-bar__progress" style="width: <?php echo intval( $percentage ); ?>%;"></div>
						</div>
					</div>
					<div class="checklist__header-secondary">
						<label for="checklist__header-action" class="checklist__header-summary checklist__toggle checklist__header-complete-label">Hide completed</label>
						<button id="checklist__header-action" class="checklist__header-action checklist__toggle">
							<span class="screen-reader-text checklist__header-complete-label">Hide completed</span>
							<?php echo get_gridicon( 'gridicons-chevron-down' ); // WPCS: XSS ok. ?>
						</button>
					</div>
				</div>
				<div class="checklist__tasks">
					<?php
					foreach ( $data['tasks'] as $task ) {
						$this->render_task( $task );
					}
					?>
				</div>
			</div>
			<div class="setup-footer">
				<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=wc-setup-checklist&finished=1' ) ); ?>"><?php esc_html_e( "I'm done setting up", 'wc-calypso-bridge' ); ?></a>
			</div>
		<?php
	}

	/**
	 * Renders a specific task.
	 *
	 * @param array $task Array of task information.
	 */
	public function render_task( $task ) {
		$task_url = $task['link'];
		if ( substr( $task_url, 0, 4 ) !== 'http' ) {
			$task_url = admin_url( $task_url );
		}
		?>
		<div class="checklist-card checklist__task has-actionlink is-compact <?php echo $task['condition'] ? 'is-completed' : ''; ?>" data-id="<?php echo esc_html( $task['id'] ); ?>" data-title="<?php echo esc_html( $task['title'] ); ?>">
			<div class="checklist__task-primary">
				<h3 class="checklist__task-title">
					<?php
						echo '<a href="' . esc_url( $task_url ) . '" class="' . ( true === $task['condition'] ? 'task-is-completed' : '' ) . '">' . esc_html( $task['title'] ) . '</a>';
					?>
				</h3>
				<p class="checklist__task-description">
					<?php echo esc_html( $task['description'] ); ?>
					<?php
					if ( ! empty( $task['learn_more'] ) ) {
						echo '<a href="' . esc_html( $task['learn_more'] ) . '" target="_blank"> ' . esc_html__( 'Learn more.', 'wc-calypso-bridge' ) . '</a>';
					}
					?>
				</p>
				<small class="checklist__task-duration">
					<?php
					/* translators: %s: Estimated amount of minutes for the task. */
					printf( esc_html__( 'Estimated time: %d minutes', 'wc-calypso-bridge' ), intval( $task['estimate'] ) );
					?>
				</small>
			</div>
			<div class="checklist__task-secondary">
				<?php
				if ( true === $task['condition'] ) {
					$action_link_secondary_class = 'checklist__task-action';
					$title                       = $task['completed_title'];
				} else {
					$action_link_secondary_class = 'button-primary';
					$title                       = __( 'Do it', 'wc-calypso-bridge' );
				}
				echo '<a href="' . esc_url( $task_url ) . '" class=" ' . esc_html( $action_link_secondary_class ) . '">' . esc_html( $title ) . '</a>';
				?>
				<small class="checklist__task-duration">
					<?php
					/* translators: %s: Estimated amount of minutes for the task. */
					printf( esc_html__( 'Estimated time: %d minutes', 'wc-calypso-bridge' ), intval( $task['estimate'] ) );
					?>
				</small>
			</div>
			<div class="checklist__task-icon">
				<?php echo get_gridicon( 'gridicons-checkmark' ); // WPCS: XSS ok. ?>
			</div>
		</div>
		<?php
	}
}

$wc_calypso_bridge_admin_setup_checklist = WC_Calypso_Bridge_Admin_Setup_Checklist::get_instance();
