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

		// If setup has been completed, do nothing.
		if ( true === (bool) get_option( 'atomic-ecommerce-setup-checklist-complete', false ) ) {
			// Redirect to orders if setup is complete.
			if ( isset( $_GET['page'] ) && 'wc-setup-checklist' === $_GET['page'] ) {
				wp_redirect( admin_url( 'edit.php?post_type=shop_order' ) );
				exit;
			}
			return;
		}

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		// priority is 20 to run after https://github.com/woocommerce/woocommerce/blob/a55ae325306fc2179149ba9b97e66f32f84fdd9c/includes/admin/class-wc-admin-menus.php#L165.
		add_action( 'admin_head', array( $this, 'admin_menu_structure' ), 20 );

		if ( isset( $_GET['page'] ) && 'wc-setup-checklist' === $_GET['page'] ) {
			add_action( 'admin_head', array( $this, 'remove_notices' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'load_checklist_styles' ) );
		}
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
		wp_enqueue_style( 'wc-calypso-bridge-setup-checklist', $asset_path . 'assets/css/setup-checklist.css', array(), WC_CALYPSO_BRIDGE_CURRENT_VERSION, 'all' );
	}

	/**
	 * Adds a new page for the setup checklist.
	 */
	public function admin_menu() {
		add_submenu_page(
			'woocommerce',
			__( 'Setup', 'wc-calypso-bridge' ),
			__( 'Setup', 'wc-calypso-bridge' ),
			'manage_woocommerce',
			'wc-setup-checklist',
			array( $this, 'checklist' )
		);
	}

	/**
	 * Puts the 'Setup' menu item at the very top of the WooCommerce link.
	 * We have to do some shuffling, because WooCommerce does some overwriting with the 'Orders' link.
	 */
	public function admin_menu_structure() {
		global $submenu;

		// User does not have capabilites to see the submenu.
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$setup_key = null;
		foreach ( $submenu['woocommerce'] as $submenu_key => $submenu_item ) {
			if ( 'wc-setup-checklist' === $submenu_item[2] ) {
				$setup_key = $submenu_key;
				break;
			}
		}

		if ( ! $setup_key ) {
			return;
		}

		$menu = $submenu['woocommerce'][ $setup_key ];

		// Move menu item to top of array.
		unset( $submenu['woocommerce'][ $setup_key ] );
		array_unshift( $submenu['woocommerce'], $menu );
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
		// TODO Double check against setup tasks in the spreadsheet. Needs Canada Post here.
		// Variables used in the conditions below.
		$count_posts    = wp_count_posts( 'product' );
		$total_products = $count_posts->publish;
		$ups_settings   = get_option( 'woocommerce_ups_settings' );
		$square_merchant_access_token = get_option( 'woocommerce_square_merchant_access_token' );

		$all_tasks = array(
			array(
				'title' => __( 'Add a product', 'wc-calypso-bridge' ),
				'completed_title' => __( 'Add another product', 'wc-calypso-bridge' ),
				'description' => __( 'Start by adding your first product to your store.', 'wc-calypso-bridge' ),
				'estimate' => '2',
				'link' => 'post-new.php?post_type=product',
				'condition' => $total_products > 0,
			),

			array(
				'title' => __( 'View and customize', 'wc-calypso-bridge' ),
				'completed_title' => __( 'Open customizer', 'wc-calypso-bridge' ),
				'description' => __( 'You have access to a few themes with your plan. See the options, chose the right one for you and customize your store.', 'wc-calypso-bridge' ),
				'estimate' => '2',
				'link' => 'customize.php?return=%2Fwp-admin%2Fadmin.php%3Fpage%3Dwc-setup-checklist',
				'condition' => false, // TODO Condition logic here. Based on click?
			),

			array(
				'title' => __( 'Review shipping', 'wc-calypso-bridge' ),
				'completed_title' => __( 'View Settings', 'wc-calypso-bridge' ),
				'description' => __( "We've set up a few shipping options based on your store location. Check them out to see if they're right for you.", 'wc-calypso-bridge' ),
				'estimate' => '2',
				'link' => 'admin.php?page=wc-settings&tab=shipping',
				'condition' => false, // TODO Condition logic here. Based on click?
			),

			array(
				'title' => __( 'Add live rates with UPS', 'wc-calypso-bridge' ),
				'completed_title' => __( 'View Settings', 'wc-calypso-bridge' ),
				'description' => __( "Showing shipping rates directly from UPS during checkout ensures you're charging customers the right amount for shipping.", 'wc-calypso-bridge' ),
				'estimate' => '2',
				'link' => 'admin.php?page=wc-settings&tab=shipping&section=ups',
				'condition' => ! empty( $ups_settings['user_id'] ) &&
							   ! empty( $ups_settings['password'] ) &&
							   ! empty( $ups_settings['access_key'] ) &&
							   ! empty( $ups_settings['shipper_number'] ),
				'extension' => 'woocommerce-shipping-ups/woocommerce-shipping-ups.php',
			),

			array(
				'title' => __( 'Setup payments with Square', 'wc-calypso-bridge' ),
				'completed_title' => __( 'View Settings', 'wc-calypso-bridge' ),
				'description' => __( 'Connect your Square account to accept credit and debit card, to track sales and sync inventory.', 'wc-calypso-bridge' ),
				'estimate' => '2',
				'link' => 'admin.php?page=wc-settings&tab=integration&section=squareconnect',
				'learn_more' => 'https://woocommerce.com/products/square/',
				'condition' => ! empty( $square_merchant_access_token ),
				'extension' => 'woocommerce-square/woocommerce-square.php',
			),

			array(
				'title' => __( 'Setup payments with PayPal', 'wc-calypso-bridge' ),
				'completed_title' => __( 'View Settings', 'wc-calypso-bridge' ),
				'description' => __( 'Connect your PayPal account to let customers to conveniently checkout directly with PayPal.', 'wc-calypso-bridge' ),
				'estimate' => '2',
				'link' => 'admin.php?page=wc-settings&tab=checkout&section=ppec_paypal',
				'learn_more' => 'https://woocommerce.com/products/woocommerce-gateway-paypal-checkout/',
				'condition' => false, // TODO Condition logic here.
				'extension' => 'woocommerce-gateway-paypal-express-checkout/woocommerce-gateway-paypal-express-checkout.php',
			),

			array(
				'title' => __( 'Setup payments with Stripe', 'wc-calypso-bridge' ),
				'completed_title' => __( 'View Settings', 'wc-calypso-bridge' ),
				'description' => __( 'Connect your Stripe account to accept credit and debit card payments.', 'wc-calypso-bridge' ),
				'estimate' => '2',
				'link' => 'admin.php?page=wc-settings&tab=checkout&section=ppec_paypal',
				'learn_more' => 'https://woocommerce.com/products/stripe/',
				'condition' => false, // TODO Condition logic here.
				'extension' => 'woocommerce-gateway-stripe/woocommerce-gateway-stripe.php',
			),

			array(
				'title' => __( 'Setup payments with Klarna', 'wc-calypso-bridge' ),
				'completed_title' => __( 'View Settings', 'wc-calypso-bridge' ),
				'description' => __( 'Connect your Klarna account to take payments with pay now, pay later and slice it.', 'wc-calypso-bridge' ),
				'estimate' => '2',
				'link' => 'admin.php?page=wc-settings&tab=checkout&section=klarna_payments',
				'learn_more' => 'https://woocommerce.com/products/klarna-payments/',
				'condition' => false, // TODO Condition logic here.
				'extension' => 'klarna-payments-for-woocommerce/klarna-payments-for-woocommerce.php',
			),

			array(
				'title' => __( 'Setup checkout with Klarna', 'wc-calypso-bridge' ),
				'completed_title' => __( 'View Settings', 'wc-calypso-bridge' ),
				'description' => __( 'Setup to provide a full checkout experience with pay now, pay later and slice it.', 'wc-calypso-bridge' ),
				'estimate' => '2',
				'link' => 'admin.php?page=wc-settings&tab=checkout&section=kco',
				'learn_more' => 'https://woocommerce.com/products/klarna-checkout/',
				'condition' => false, // TODO Condition logic here.
				'extension' => 'klarna-checkout-for-woocommerce/klarna-checkout-for-woocommerce.php',
			),

			array(
				'title' => __( 'Setup payments with eWAY', 'wc-calypso-bridge' ),
				'completed_title' => __( 'View Settings', 'wc-calypso-bridge' ),
				'description' => __( 'Connect your eWay account to take credit card payments directly on your store.', 'wc-calypso-bridge' ),
				'estimate' => '2',
				'link' => 'admin.php?page=wc-settings&tab=checkout&section=eway',
				'condition' => false, // TODO Condition logic here.
				'extension' => 'woocommerce-gateway-eway/woocommerce-gateway-eway.php',
			),

			array(
				'title' => __( 'Setup payments with PayFast', 'wc-calypso-bridge' ),
				'completed_title' => __( 'View Settings', 'wc-calypso-bridge' ),
				'description' => __( 'Connect your PayFast account to accept payments by credit card and Electronic Fund Transfer.', 'wc-calypso-bridge' ),
				'estimate' => '2',
				'link' => 'admin.php?page=wc-settings&tab=checkout&section=payfast',
				'condition' => false, // TODO Condition logic here.
				'extension' => 'woocommerce-payfast-gateway/gateway-payfast.php',
			),

			array(
				'title' => __( 'Enable automatic tax rates with Taxjar', 'wc-calypso-bridge' ),
				'completed_title' => __( 'View Settings', 'wc-calypso-bridge' ),
				'description' => __( 'Automatically collect sales tax at checkout by connecting with TaxJar.', 'wc-calypso-bridge' ),
				'estimate' => '2',
				'link' => 'admin.php?page=wc-settings&tab=integration&section=taxjar-integration',
				'condition' => false, // TODO Condition logic here.
				'extension' => 'taxjar-simplified-taxes-for-woocommerce/taxjar-woocommerce.php',
			),

			array(
				'title' => __( 'Integrate with Facebook', 'wc-calypso-bridge' ),
				'completed_title' => __( 'View Settings', 'wc-calypso-bridge' ),
				'description' => __( 'Integrating Facebook with your store and drive sales.', 'wc-calypso-bridge' ),
				'estimate' => '20',
				'link' => 'admin.php?page=wc-settings&tab=integration&section=facebookcommerce',
				'learn_more' => 'https://www.facebook.com/business/help/900699293402826',
				'condition' => false, // TODO Condition logic here.
				'extension' => 'facebook-for-woocommerce/facebook-for-woocommerce.php',
			),

			array(
				'title' => __( 'Integrate with Mailchimp', 'wc-calypso-bridge' ),
				'completed_title' => __( 'View Settings', 'wc-calypso-bridge' ),
				'description' => __( 'Connect your store to bring the power of email marketing to your business.', 'wc-calypso-bridge' ),
				'estimate' => '20',
				'link' => 'options-general.php?page=mailchimp-woocommerce',
				'learn_more' => 'https://wordpress.org/plugins/mailchimp-for-woocommerce/',
				'condition' => false, // TODO Condition logic here.
				'extension' => 'mailchimp-for-woocommerce/mailchimp-woocommerce.php',
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
		$data = $this->get_task_data();
		$percentage = floor( ( $data['completed'] / $data['total'] ) * 100 );
		?>
			<div class="checklist">
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
				</div>
				<div class="checklist__tasks">
					<?php
					foreach ( $data['tasks'] as $task ) {
						$this->render_task( $task );
					}
					?>
				</div>
			</div>
		<?php
	}

	/**
	 * Renders a specific task.
	 *
	 * @param array $task Array of task information.
	 */
	public function render_task( $task ) {
		$task_url = admin_url( $task['link'] );
		?>
		<div class="checklist-card checklist__task has-actionlink is-compact
		<?php
		if ( true === $task['condition'] ) {
			echo ' is-completed';
		}
		?>
		">
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
				$title = $task['completed_title'];
			} else {
				$action_link_secondary_class = 'button-primary';
				$title = $task['title'];
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
		<?php if ( true === $task['condition'] ) { ?>
			<div class="checklist__task-icon">
				<svg class="gridicon gridicons-checkmark" height="18" width="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g><path d="M9 19.414l-6.707-6.707 1.414-1.414L9 16.586 20.293 5.293l1.414 1.414"></path></g></svg>
			</div>
		<?php } ?>
		</div>
		<?php
	}
}

$wc_calypso_bridge_admin_setup_checklist = WC_Calypso_Bridge_Admin_Setup_Checklist::get_instance();
