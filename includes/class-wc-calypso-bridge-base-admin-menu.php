<?php

/**
 * Base Admin Menu file.
 *
 * @since   2.3.11
 * @version 2.3.11
 *
 * The base admin menu controller for Ecommerce WoA sites.
 * Copied from https://github.com/Automattic/jetpack/blob/45c299dc82c265f627328899697cfab154d2fa04/projects/plugins/jetpack/modules/masterbar/admin-menu/class-base-admin-menu.php#L15
 */
abstract class WC_Calypso_Bridge_Base_Admin_Menu {
	/**
	 * Holds class instances.
	 *
	 * @var array
	 */
	protected static $instances;

	/**
	 * Whether the current request is a REST API request.
	 *
	 * @var bool
	 */
	protected $is_api_request = false;

	/**
	 * Domain of the current site.
	 *
	 * @var string
	 */
	protected $domain;

	/**
	 * The CSS classes used to hide the submenu items in navigation.
	 *
	 * @var string
	 */
	const HIDE_CSS_CLASS = 'hide-if-js';

	/**
	 * Base_Admin_Menu constructor.
	 */
	protected function __construct() {
		$this->is_api_request = defined( 'REST_REQUEST' ) && REST_REQUEST || isset( $_SERVER['REQUEST_URI'] ) && str_starts_with( filter_var( wp_unslash( $_SERVER['REQUEST_URI'] ) ), '/?rest_route=%2Fwpcom%2Fv2%2Fadmin-menu' );
		$this->domain         = WC_Calypso_Bridge_Instance()->get_site_slug();

		add_action( 'admin_menu', array( $this, 'reregister_menu_items' ), 99999 );
	}

	/**
	 * Returns class instance.
	 *
	 * @return Admin_Menu
	 */
	public static function get_instance() {
		$class = static::class;

		if ( empty( static::$instances[ $class ] ) ) {
			static::$instances[ $class ] = new $class();
		}

		return static::$instances[ $class ];
	}

	/**
	 * Updates the menu data of the given menu slug.
	 *
	 * @param string $slug Slug of the menu to update.
	 * @param string $url New menu URL.
	 * @param string $title New menu title.
	 * @param string $cap New menu capability.
	 * @param string $icon New menu icon.
	 * @param int    $position New menu position.
	 * @return bool Whether the menu has been updated.
	 */
	public function update_menu( $slug, $url = null, $title = null, $cap = null, $icon = null, $position = null ) {
		global $menu, $submenu;

		$menu_item     = null;
		$menu_position = null;

		foreach ( $menu as $i => $item ) {
			if ( $slug === $item[2] ) {
				$menu_item     = $item;
				$menu_position = $i;
				break;
			}
		}

		if ( ! $menu_item ) {
			return false;
		}

		if ( $title ) {
			$menu_item[0] = $title;
			$menu_item[3] = esc_attr( $title );
		}

		if ( $cap ) {
			$menu_item[1] = $cap;
		}

		// Change parent slug only if there are no submenus (the slug of the 1st submenu will be used if there are submenus).
		if ( $url ) {
			$this->hide_submenu_page( $slug, $slug );

			if ( ! isset( $submenu[ $slug ] ) || ! $this->has_visible_items( $submenu[ $slug ] ) ) {
				$menu_item[2] = $url;
			}
		}

		if ( $icon ) {
			$menu_item[4] = 'menu-top';
			$menu_item[6] = $icon;
		}

		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		unset( $menu[ $menu_position ] );
		if ( $position ) {
			$menu_position = $position;
		}
		$this->set_menu_item( $menu_item, $menu_position );

		// Only add submenu when there are other submenu items.
		if ( $url && isset( $submenu[ $slug ] ) && $this->has_visible_items( $submenu[ $slug ] ) ) {
			add_submenu_page( $slug, $menu_item[3], $menu_item[0], $menu_item[1], $url, null, 0 );
		}

		return true;
	}

	/**
	 * Updates the submenus of the given menu slug.
	 *
	 * It hides the menu by adding the `hide-if-js` css class and duplicates the submenu with the new slug.
	 *
	 * @param string $slug Menu slug.
	 * @param array  $submenus_to_update Array of new submenu slugs.
	 */
	public function update_submenus( $slug, $submenus_to_update ) {
		global $submenu;

		if ( ! isset( $submenu[ $slug ] ) ) {
			return;
		}

		// This is needed for cases when the submenus to update have the same new slug.
		$submenus_to_update = array_filter(
			$submenus_to_update,
			static function ( $item, $old_slug ) {
				return $item !== $old_slug;
			},
			ARRAY_FILTER_USE_BOTH
		);

		/**
		 * Iterate over all submenu items and add the hide the submenus with CSS classes.
		 * This is done separately of the second foreach because the position of the submenu might change.
		 */
		foreach ( $submenu[ $slug ] as $index => $item ) {
			if ( ! array_key_exists( $item[2], $submenus_to_update ) ) {
				continue;
			}

			$this->hide_submenu_element( $index, $slug, $item );
		}

		$submenu_items = array_values( $submenu[ $slug ] );

		/**
		 * Iterate again over the submenu array. We need a copy of the array because add_submenu_page will add new elements
		 * to submenu array that might cause an infinite loop.
		 */
		foreach ( $submenu_items as $i => $submenu_item ) {
			if ( ! array_key_exists( $submenu_item[2], $submenus_to_update ) ) {
				continue;
			}

			add_submenu_page(
				$slug,
				isset( $submenu_item[3] ) ? $submenu_item[3] : '',
				isset( $submenu_item[0] ) ? $submenu_item[0] : '',
				isset( $submenu_item[1] ) ? $submenu_item[1] : 'read',
				$submenus_to_update[ $submenu_item[2] ],
				'',
				0 === $i ? 0 : $i + 1
			);
		}
	}

	/**
	 * Adds a menu separator.
	 *
	 * @param int    $position The position in the menu order this item should appear.
	 * @param string $cap Optional. The capability required for this menu to be displayed to the user.
	 *                         Default: 'read'.
	 */
	public function add_admin_menu_separator( $position = null, $cap = 'read' ) {
		$menu_item = array(
			'',                                  // Menu title (ignored).
			$cap,                                // Required capability.
			wp_unique_id( 'separator-custom-' ), // URL or file (ignored, but must be unique).
			'',                                  // Page title (ignored).
			'wp-menu-separator',                 // CSS class. Identifies this item as a separator.
		);

		$this->set_menu_item( $menu_item, $position );
	}

	/**
	 * Hide the submenu page based on slug and return the item that was hidden.
	 *
	 * Instead of actually removing the submenu item, a safer approach is to hide it and filter it in the API response.
	 * In this manner we'll avoid breaking third-party plugins depending on items that no longer exist.
	 *
	 * A false|array value is returned to be consistent with remove_submenu_page() function
	 *
	 * @param string $menu_slug The parent menu slug.
	 * @param string $submenu_slug The submenu slug that should be hidden.
	 * @return false|array
	 */
	public function hide_submenu_page( $menu_slug, $submenu_slug ) {
		global $submenu;

		if ( ! isset( $submenu[ $menu_slug ] ) ) {
			return false;
		}

		foreach ( $submenu[ $menu_slug ] as $i => $item ) {
			if ( $submenu_slug !== $item[2] ) {
				continue;
			}

			$this->hide_submenu_element( $i, $menu_slug, $item );

			return $item;
		}

		return false;
	}

	/**
	 * Apply the hide-if-js CSS class to a submenu item.
	 *
	 * @param int    $index The position of a submenu item in the submenu array.
	 * @param string $parent_slug The parent slug.
	 * @param array  $item The submenu item.
	 */
	public function hide_submenu_element( $index, $parent_slug, $item ) {
		global $submenu;

		$css_classes = empty( $item[4] ) ? self::HIDE_CSS_CLASS : $item[4] . ' ' . self::HIDE_CSS_CLASS;

		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$submenu [ $parent_slug ][ $index ][4] = $css_classes;
	}

	/**
	 * Check if the menu has submenu items visible
	 *
	 * @param array $submenu_items The submenu items.
	 * @return bool
	 */
	public function has_visible_items( $submenu_items ) {
		$visible_items = array_filter(
			$submenu_items,
			array( $this, 'is_item_visible' )
		);

		return array() !== $visible_items;
	}

	/**
	 * Return the number of existing submenu items under the supplied parent slug.
	 *
	 * @param string $parent_slug The slug of the parent menu.
	 * @return int The number of submenu items under $parent_slug.
	 */
	public function get_submenu_item_count( $parent_slug ) {
		global $submenu;

		if ( empty( $parent_slug ) || empty( $submenu[ $parent_slug ] ) || ! is_array( $submenu[ $parent_slug ] ) ) {
			return 0;
		}

		return count( $submenu[ $parent_slug ] );
	}

	/**
	 * Adds the given menu item in the specified position.
	 *
	 * @param array $item The menu item to add.
	 * @param int   $position The position in the menu order this item should appear.
	 */
	public function set_menu_item( $item, $position = null ) {
		global $menu;

		// Handle position (avoids overwriting menu items already populated in the given position).
		// Inspired by https://core.trac.wordpress.org/browser/trunk/src/wp-admin/menu.php?rev=49837#L160.
		if ( null === $position ) {
			$menu[] = $item; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		} elseif ( isset( $menu[ "$position" ] ) ) {
			$position            = $position + substr( base_convert( md5( $item[2] . $item[0] ), 16, 10 ), -5 ) * 0.00001;
			$menu[ "$position" ] = $item; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		} else {
			$menu[ $position ] = $item; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		}
	}

	/**
	 * Hide menus that are unauthorized and don't have visible submenus and cases when the menu has the same slug
	 * as the first submenu item.
	 *
	 * This must be done at the end of menu and submenu manipulation in order to avoid performing this check each time
	 * the submenus are altered.
	 */
	public function hide_parent_of_hidden_submenus() {
		global $menu, $submenu;

		$this->sort_hidden_submenus();

		foreach ( $menu as $menu_index => $menu_item ) {
			$has_submenus = isset( $submenu[ $menu_item[2] ] );

			// Skip if the menu doesn't have submenus.
			if ( ! $has_submenus ) {
				continue;
			}

			// If the first submenu item is hidden then we should also hide the parent.
			// Since the submenus are ordered by self::HIDE_CSS_CLASS (hidden submenus should be at the end of the array),
			// we can say that if the first submenu is hidden then we should also hide the menu.
			$first_submenu_item       = array_values( $submenu[ $menu_item[2] ] )[0];
			$is_first_submenu_visible = $this->is_item_visible( $first_submenu_item );

			// if the user does not have access to the menu and the first submenu is hidden, then hide the menu.
			if ( ! current_user_can( $menu_item[1] ) && ! $is_first_submenu_visible ) {
				// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$menu[ $menu_index ][4] = self::HIDE_CSS_CLASS;
			}

			// if the menu has the same slug as the first submenu then hide the submenu.
			if ( $menu_item[2] === $first_submenu_item[2] && ! $is_first_submenu_visible ) {
				// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$menu[ $menu_index ][4] = self::HIDE_CSS_CLASS;
			}
		}
	}

	/**
	 * Sort the hidden submenus by moving them at the end of the array in order to avoid WP using them as default URLs.
	 *
	 * This operation has to be done at the end of submenu manipulation in order to guarantee that the hidden submenus
	 * are at the end of the array.
	 */
	public function sort_hidden_submenus() {
		global $submenu;

		foreach ( $submenu as $menu_slug => $submenu_items ) {
			foreach ( $submenu_items as $submenu_index => $submenu_item ) {
				if ( $this->is_item_visible( $submenu_item ) ) {
					continue;
				}

				unset( $submenu[ $menu_slug ][ $submenu_index ] );
				// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$submenu[ $menu_slug ][] = $submenu_item;
			}
		}
	}

	/**
	 * Check if the given item is visible or not in the admin menu.
	 *
	 * @param array $item A menu or submenu array.
	 */
	public function is_item_visible( $item ) {
		return ! isset( $item[4] ) || ! str_contains( $item[4], self::HIDE_CSS_CLASS );
	}

	/**
	 * Whether the current user has indicated they want to use the wp-admin interface for the given screen.
	 *
	 * @return bool
	 */
	public function use_wp_admin_interface() {
		return 'wp-admin' === get_option( 'wpcom_admin_interface' );
	}

	/**
	 * Create the desired menu output.
	 */
	abstract public function reregister_menu_items();
}
