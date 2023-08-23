<?php
defined( 'ABSPATH' ) || exit;

/**
 * WC_Calypso_Bridge_HelloWorld_WC_React_Page Class.
 */
class WC_Calypso_Bridge_HelloWorld_WC_React_Page {

	/**
	 * Class instance.
	 */
	protected static $instance = false;

	/**
	 * Get class instance.
	 */
	final public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	protected function __construct() {
		add_action( 'admin_menu', array($this, 'add_menu_page'));
	}

	/**
	 * Initialize hooks.
	 */
	public function add_menu_page() {
		wc_admin_register_page(
			array(
				'id'        => 'woocommerce-wccom-helloworld',
				'title'     => __('Hello World', 'wc-calyso-bridge'),
				'nav_args'  => array(
					'title'  => __( 'Hello World', 'wc-calyso-bridge' ),
				),
				'path'=> '/hello-world',
				'position'  => 65,
				'icon'=>'dashicons-admin-site',
				'capability' => 'manage_options',
			)
		);
	}
}

WC_Calypso_Bridge_HelloWorld_WC_React_Page::get_instance();
