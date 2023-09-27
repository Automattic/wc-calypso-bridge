<?php
/**
 * Adds Customize Store task related functionalities
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 2.2.14
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge Customize Store
 */
class WC_Calypso_Bridge_Customize_Store {

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Bridge_Customize_Store instance
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
		add_action( 'plugins_loaded', function() {
			if ( class_exists( '\Automattic\WooCommerce\Admin\Features\Features' ) && \Automattic\WooCommerce\Admin\Features\Features::is_enabled( 'customize-store' ) ) {
				add_action( 'load-site-editor.php', array( $this, 'mark_customize_store_task_as_completed_on_site_editor' ) );
			}
		});
	}

	/**
	 * Mark Customize Store task as completed on Site Editor by checking $_GET['from'] value.
	 * The value is set from WP-Calypso.
	 *
	 * @since 2.2.14
	 *
	 * @return void
	 */
	public function mark_customize_store_task_as_completed_on_site_editor() {
		if ( isset( $_GET['from'] ) && $_GET['from'] === 'theme-browser' ) {
			update_option( 'woocommerce_admin_customize_store_completed', 'yes' );
		}
	}
}

WC_Calypso_Bridge_Customize_Store::get_instance();
