<?php
/**
 * Removes the back links and adds the taxonomies on settings pages.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge Taxonomies
 */
class WC_Calypso_Bridge_Taxonomies {

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Bridge_Taxonomies instance
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
	 * Constructor
	 */
	private function __construct() {
		add_action( 'add_tag_form_pre', array( $this, 'add_action_button' ) );
		add_action( 'wp_loaded', array( $this, 'remove_taxonomy_form_description' ) );
	}

	/**
	 * Add new button to toggle taxonomy form
	 */
	public function add_action_button() {
		?>
		<button class="page-title-action button button-primary taxonomy-form-toggle">
			<?php esc_html_e( 'Add New', 'wc-calypso-bridge' ); ?>
		</button>
		<button class="button button-secondary taxonomy-form-toggle taxonomy-form-cancel-button">
			<?php esc_html_e( 'Cancel', 'wc-calypso-bridge' ); ?>
		</button>
		<?php
	}

	/**
	 * Remove taxonomy form description
	 *
	 * Description includes items that have been removed and are irrelevant
	 * to WC Calypso Bridge.
	 */
	public function remove_taxonomy_form_description() {
		// @TODO: Uncomment the following line if https://github.com/woocommerce/woocommerce/pull/21884 is merged into WC core.
		// remove_action( 'product_cat_pre_add_form', array( WC_Admin_Taxonomies::get_instance(), 'product_cat_description' ), 10 );
	}
}
$wc_calypso_bridge_taxonomies = WC_Calypso_Bridge_Taxonomies::get_instance();
