<?php
/**
 * Removes the back links and adds the pagination on settings pages.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge Pagination
 */
class WC_Calypso_Bridge_Pagination {

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Bridge_Pagination instance
	 */
	protected static $instance = false;

	/**
	 * Current page
	 *
	 * @var int
	 */
	private $current_page = null;

	/**
	 * Max pages
	 *
	 * @var int
	 */
	private $max_pages = null;

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
		add_action( 'wp', array( $this, 'set_page_vars' ) );
		add_action( 'manage_posts_extra_tablenav', array( $this, 'render_pagination' ), PHP_INT_MAX - 1 );
	}


	/**
	 * Set pagination vars after wp is ready
	 */
	public function set_page_vars() {
		global $wp_query;
		$this->current_page = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
		$this->max_pages    = $wp_query->max_num_pages;
	}

	/**
	 * Render pagination
	 */
	public function render_pagination() {
		$page_links = paginate_links(
			array(
				'base'      => add_query_arg( 'paged', '%#%' ),
				'format'    => '',
				'prev_text' => $this->prev_link(),
				'next_text' => $this->next_link(),
				'prev_next' => true,
				'total'     => $this->max_pages,
				'current'   => $this->current_page,
			)
		);
		$page_links = $this->add_prev_next_disabled_links( $page_links );
		echo '<div class="tablenav-pages wc-calypso-brdige-pagination">' . $page_links . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}

	/**
	 * Get the previous page link
	 */
	public function prev_link() {
		// translators: Add in the Gridicons previous svg icon.
		return sprintf( __( '%s Previous', 'wc-calypso-bridge' ), '<svg class="gridicon gridicons-arrow-left" height="24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></g></svg>' );
	}

	/**
	 * Get the next page link
	 */
	public function next_link() {
		// translators: Add in the Gridicons next svg icon.
		return sprintf( __( 'Next %s', 'wc-calypso-bridge' ), '<svg class="gridicon gridicons-arrow-right" height="24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8-8-8z"/></g></svg>' );
	}

	/**
	 * Add next and previous links even if we're on the first or last page
	 *
	 * @param string $page_links Pagination html to append to.
	 */
	public function add_prev_next_disabled_links( $page_links ) {
		if ( $this->max_pages > 1 ) {
			if ( 1 === $this->current_page ) {
				$prev_link  = '<span class="tablenav-pages-navspan disabled">' . $this->prev_link() . '</span>';
				$page_links = $prev_link . $page_links;
			}
			if ( (int) $this->current_page === (int) $this->max_pages ) {
				$next_link  = '<span class="tablenav-pages-navspan disabled">' . $this->next_link() . '</span>';
				$page_links = $page_links . $next_link;
			}
		}
		return $page_links;
	}

}
$wc_calypso_bridge_pagination = WC_Calypso_Bridge_Pagination::get_instance();
