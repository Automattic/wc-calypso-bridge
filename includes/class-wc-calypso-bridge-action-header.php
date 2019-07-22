<?php
/**
 * Removes the back links and adds the action_header on settings pages.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge Action Header
 */
class WC_Calypso_Bridge_Action_Header {

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Bridge_Action_Header instance
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
		add_action( 'wp_after_admin_bar_render', array( $this, 'render_action_header' ) );
		add_action( 'in_admin_header', array( $this, 'render_sidebar_header' ) );
		add_action( 'wp_loaded', array( $this, 'remove_calypso_sidebar_header' ) );
	}

	/**
	 * Render action header
	 */
	public function render_action_header() {
		?>
		<div class="action-header" id="action-header">
			<?php $this->back_button(); ?>
			<div class="action-header__content">
				<?php $this->site_icon(); ?>
				<div class="action-header__details">
					<?php $this->site_title(); ?>
					<?php $this->breadcrumbs(); ?>
				</div>
			</div>
			<div class="action-header__actions"></div>
		</div>
		<?php
	}

	/**
	 * Render sidebar header
	 */
	public function render_sidebar_header() {
		?>
		<div class="action-header action-header-sidebar">
			<?php $this->back_button(); ?>
			<div class="action-header__content">
				<?php $this->site_icon(); ?>
				<div class="action-header__details">
					<?php $this->site_title(); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Back button
	 */
	public function back_button() {
		$svg_atts = array(
			'svg'   => array(
				'class'           => true,
				'aria-hidden'     => true,
				'aria-labelledby' => true,
				'role'            => true,
				'xmlns'           => true,
				'width'           => true,
				'height'          => true,
				'viewbox'         => true,
			),
			'g'     => array( 'fill' => true ),
			'title' => array( 'title' => true ),
			'path'  => array(
				'd'    => true,
				'fill' => true,
			),
		);
		?>
		<a class="action-header__ground-control-back" aria-label="<?php esc_html_e( 'Close Store', 'wc-calypso-bridge' ); ?>" href="<?php echo esc_url( 'https://wordpress.com/stats/day/' . Jetpack::build_raw_urls( home_url() ) ); ?>">
			<?php echo wp_kses( get_gridicon( 'gridicons-chevron-left' ), $svg_atts ); ?>
		</a>
		<?php
	}

	/**
	 * Site icon
	 */
	public function site_icon() {
		$site_icon = get_site_icon_url();
		$site_url  = get_site_url();
		$site_name = get_bloginfo( 'name' );
		?>
		<a href="<?php echo esc_url( $site_url ); ?>" aria-label="<?php echo esc_html( $site_name ); ?>">
			<div class="site-icon <?php echo $site_icon ? '' : 'is-blank'; ?>">
				<?php if ( $site_icon ) { ?>
					<img src="<?php echo esc_url( $site_icon ); ?>" alt="" />
				<?php } else { ?>
					<svg class="gridicon gridicons-globe" height="25" width="25" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 18l2-2 1-1v-2h-2v-1l-1-1H9v3l2 2v1.93c-3.94-.494-7-3.858-7-7.93l1 1h2v-2h2l3-3V6h-2L9 5v-.41C9.927 4.21 10.94 4 12 4s2.073.212 3 .59V6l-1 1v2l1 1 3.13-3.13c.752.897 1.304 1.964 1.606 3.13H18l-2 2v2l1 1h2l.286.286C18.03 18.06 15.24 20 12 20z"></path></g></svg>
				<?php } ?>
			</div>
		</a>
		<?php
	}

	/**
	 * Site title
	 */
	public function site_title() {
		?>
		<p class="action-header__site-title">
			<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>
		</p>
		<?php
	}

	/**
	 * Get array of breadcrumbs
	 *
	 * @return array
	 */
	public function get_crumbs() {
		global $submenu, $menu, $pagenow, $wp;
		$crumbs      = array( array( 'name' => get_admin_page_title() ) );
		$page_parent = get_admin_page_parent();

		$parent = false;
		foreach ( $menu as $top_level_menu_item ) {
			$parent_name = ! empty( $top_level_menu_item[3] ) ? $top_level_menu_item[3] : $top_level_menu_item[0];
			if ( $top_level_menu_item[2] === $page_parent && $crumbs[0]['name'] !== $parent_name ) {
				$parent = $top_level_menu_item;
				break;
			}
		}

		if ( $parent ) {
			array_unshift(
				$crumbs,
				array(
					'name' => $parent_name,
					'url'  => $this->get_parent_url( $parent[2] ),
				)
			);
		}
		return $crumbs;
	}

	/**
	 * Get parent page URL from slug
	 *
	 * @param string $parent_menu_slug Parent page slug.
	 */
	public function get_parent_url( $parent_menu_slug ) {
		global $submenu;

		if ( ! empty( $submenu[ $parent_menu_slug ] ) ) {
			$submenu_items   = $submenu[ $parent_menu_slug ];
			$first_menu_item = array_shift( $submenu_items );
			$menu_hook       = get_plugin_page_hook( $first_menu_item[2], $parent_menu_slug );

			if ( ! empty( $menu_hook ) ) {
				return 'admin.php?page=' . $first_menu_item[2];
			} else {
				return $first_menu_item[2];
			}
		}
		return null;
	}

	/**
	 * Breadcrumbs
	 */
	public function breadcrumbs() {
		$crumbs = $this->get_crumbs();
		?>
		<div class="action-header__breadcrumbs">
			<?php foreach ( $crumbs as $crumb ) { ?>
				<span>
					<?php if ( isset( $crumb['url'] ) ) { ?>
						<a href="<?php echo esc_url( admin_url( $crumb['url'] ) ); ?>">
					<?php } ?>
						<?php echo esc_html( wp_strip_all_tags( $crumb['name'] ) ); ?>
					<?php if ( isset( $crumb['url'] ) ) { ?>
						</a>
					<?php } ?>
				</span>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Remove calypso sidebar header
	 */
	public function remove_calypso_sidebar_header() {
		$jetpack_calypsoify = Jetpack_Calypsoify::getInstance();
		remove_action( 'in_admin_header', array( $jetpack_calypsoify, 'insert_sidebar_html' ) );
	}

}
$wc_calypso_bridge_action_header = WC_Calypso_Bridge_Action_Header::get_instance();
