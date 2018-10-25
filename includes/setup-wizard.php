<?php
/**
 * Setup Wizard Class
 *
 * Extends the original WC setup wizard so we can add in new templates to the view.
 */

if ( ! defined( 'ABSPATH' ) || ! class_exists( 'WC_Admin_Setup_Wizard' ) ) {
	exit;
}

/**
 * WC_Admin_Setup_Wizard class.
 */
class WC_Calypso_Bridge_Admin_Setup_Wizard extends WC_Admin_Setup_Wizard {

	/**
	 * Current step
	 *
		* @var string
	 */
	private $step = '';

	/**
	 * Steps for the setup wizard
	 *
	 * @var array
	 */
	private $steps = array();

    /**
	 * Hook in tabs.
	 */
	public function __construct() {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			add_action( 'admin_menu', array( $this, 'admin_menus' ) );
			add_action( 'admin_init', array( $this, 'setup_wizard' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}

    /**
	 * Setup Wizard Header.
	 */
	public function setup_wizard_header() {
		set_current_screen();
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width" />
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title><?php esc_html_e( 'WooCommerce &rsaquo; Setup Wizard', 'woocommerce' ); ?></title>
			<?php do_action( 'admin_head' ); ?>
			<?php do_action( 'admin_enqueue_scripts' ); ?>
			<?php wp_print_scripts( 'wc-setup' ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_print_scripts' ); ?>
		</head>
		<body class="wc-setup wp-core-ui">
			<?php wp_admin_bar_render(); ?>
		<?php
	}
	
	public function setup_wizard_steps() {
		$step = $this->steps[ $this->step ];
		?>
		<div class="wc-step-heading">
			<h1><?php echo $step['name']; ?></h1>
			<?php if ( isset( $step[ 'subheading' ] ) ) {  ?>
				<h2><?php echo $step['subheading']; ?></h2>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Show the setup wizard.
	 */
	public function setup_wizard() {
		if ( empty( $_GET['page'] ) || 'wc-setup' !== $_GET['page'] ) { // WPCS: CSRF ok, input var ok.
			return;
		}
		$default_steps = array(
			'store_setup'    => array(
				'name'       => __( 'Let\'s start setting up.', 'wc-calypso-bridge' ),
				'subheading' => __( 'First we need to determine some basic information about your store.', 'wc-calypso-bridge' ),
				'view'       => array( $this, 'wc_setup_store_setup' ),
				'handler'    => array( $this, 'wc_setup_store_setup_save' ),
			),
			'payment'        => array(
				'name'       => __( 'Select payment methods.', 'wc-calypso-bridge' ),
				'subheading' => __( 'Additional payment methods can be setup later.', 'wc-calypso-bridge' ),
				'view'       => array( $this, 'wc_setup_payment' ),
				'handler'    => array( $this, 'wc_setup_payment_save' ),
			),
		);

		$this->steps = apply_filters( 'wc_calypso_bridge_setup_wizard_steps', $default_steps );
		$this->step  = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) ); // WPCS: CSRF ok, input var ok.

		// @codingStandardsIgnoreStart
		if ( ! empty( $_POST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
			call_user_func( $this->steps[ $this->step ]['handler'], $this );
		}
		// @codingStandardsIgnoreEnd

		ob_start();
		$this->setup_wizard_header();
		$this->setup_wizard_steps();
		$this->setup_wizard_content();
		$this->setup_wizard_footer();
		exit;
	}

	/**
	 * Get the URL for the next step's screen.
	 *
	 * @param string $step  slug (default: current step).
	 * @return string       URL for next step if a next step exists.
	 *                      Admin URL if it's the last step.
	 *                      Empty string on failure.
	 * @since 3.0.0
	 */
	public function get_next_step_link( $step = '' ) {
		if ( ! $step ) {
			$step = $this->step;
		}
		$keys = array_keys( $this->steps );
		if ( end( $keys ) === $step ) {
			return admin_url();
		}
		$step_index = array_search( $step, $keys, true );
		if ( false === $step_index ) {
			return '';
		}
		return add_query_arg( 'step', $keys[ $step_index + 1 ], remove_query_arg( 'activate_error' ) );
	}

	/**
	 * Output the content for the current step.
	 */
	public function setup_wizard_content() {
		echo '<div class="wc-setup-content">';
		if ( ! empty( $this->steps[ $this->step ]['view'] ) ) {
			call_user_func( $this->steps[ $this->step ]['view'], $this );
		}
		echo '</div>';
	}
    
}

new WC_Calypso_Bridge_Admin_Setup_Wizard();
