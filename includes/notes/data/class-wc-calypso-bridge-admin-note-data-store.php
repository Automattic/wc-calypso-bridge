<?php
/**
 * Overrides the default notes datastores to introduce suppress/allowlisting features.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   2.2.20
 * @version 2.3.12
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\WCAdminHelper;
use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\DataStore;

/**
 * WC Calypso Bridge Admin Notes Data Store
 */
class WC_Calypso_Bridge_Admin_Note_Data_Store extends DataStore {

	/**
	 * Runtime-cached allow list.
	 *
	 * @var array
	 */
	protected $allow_list;

	/**
	 * Indicates whether there's an allow-list.
	 *
	 * @return boolean
	 */
	protected function has_allow_list() {
		return wc_calypso_bridge_is_ecommerce_trial_plan();
	}

	/**
	 * Attempts to fetch specs from the first available DataSourcePoller class.
	 *
	 * @return array The specs data or null if not retrievable.
	 */
	public function fetch_specs() {
		$possible_paths = [
			'Automattic\WooCommerce\Admin\RemoteInboxNotifications\RemoteInboxNotificationsDataSourcePoller',
			'Automattic\WooCommerce\Admin\RemoteInboxNotifications\DataSourcePoller',
		];

		foreach ( $possible_paths as $class_path ) {
			if ( class_exists( $class_path ) ) {
				// Directly call the static method from the fully qualified class name.
				return $class_path::get_instance()->get_specs_from_data_sources();
			}
		}

		return array();
	}

	/**
	 * Returns a list of messages to allow.
	 *
	 * @return array|null
	 */
	protected function get_allow_list() {

		if ( ! $this->has_allow_list() ) {
			return null;
		}

		// If initialized, return it.
		if ( is_array( $this->allow_list ) ) {
			return $this->allow_list;
		}

		$allow_list = array(
			// Woo Core.
			'wc-admin-add-first-product-note',
			'wc-admin-mobile-app',
			'wc-admin-test-checkout',
			'wc-admin-payments-remind-me-later',
			'wc-admin-onboarding-payments-reminder',
			'wc-admin-orders-milestone',
			// Woo Express lifecycle messages.
			'wc-calypso-bridge-free-trial-welcome',
			'wc-calypso-bridge-choose-domain',
			// Extensions.
			'stripe-apple-pay-domain-verification-needed',
		);

		// Allow Remote Inbox Notifications targeting Woo Express sites to be saved.
		$data = $this->fetch_specs();
		foreach ( $data as $spec ) {
			if ( isset( $spec->rules ) && is_array( $spec->rules ) ) {
				foreach ( $spec->rules as $rule ) {
					if ( isset( $rule->type ) && 'is_woo_express' === $rule->type ) {
						$allow_list[] = $spec->slug;
					}
				}
			}
		}

		$this->allow_list = $allow_list;

		return $this->allow_list;
	}

	/**
	 * Indicates whether a note is allowed.
	 *
	 * @param  Note $note
	 * @return boolean
	 */
	protected function is_allow_listed( $note ) {
		if ( ! $this->has_allow_list() ) {
			return true;
		}

		return in_array( $note->get_name(), $this->get_allow_list(), true );
	}

	/**
	 * Indicates whether there's a suppress-list.
	 *
	 * @return boolean
	 */
	protected function has_suppress_list() {
		return wc_calypso_bridge_has_ecommerce_features();
	}

	/**
	 * Returns a list of messages to suppress.
	 *
	 * @return array|null
	 */
	protected function get_suppress_list() {

		if ( ! $this->has_suppress_list() ) {
			return null;
		}

		$suppress_list = array(
			// Woo Core.
			'wc-admin-adding-and-managing-products',
			'wc-admin-choosing-a-theme',
			'wc-admin-launch-checklist',
			'wc-admin-personalize-store',
			'wc-admin-customizing-product-catalog',
			'wc-admin-first-product',
			'wc-admin-store-notice-giving-feedback-2',
			'wc-admin-insight-first-product-and-payment',
			'wc-admin-insight-first-sale',
			'wc-admin-install-jp-and-wcs-plugins',
			'wc-admin-manage-store-activity-from-home-screen',
			'wc-admin-usage-tracking-opt-in',
			'wc-admin-remove-unsecured-report-files',
			'wc-admin-update-store-details',
			'wc-admin-welcome-to-woocommerce-for-store-users',
			'wc-admin-woocommerce-payments',
			'wc-payments-notes-set-up-refund-policy',
			'wc-admin-marketing-jetpack-backup',
			'wc-admin-migrate-from-shopify', // suppress for now, to be revisited.
			'wc-admin-magento-migration', // suppress for now, to be revisited.
			'wc-admin-woocommerce-subscriptions', // suppress for now, to be revisited.
			'wc-admin-online-clothing-store', // suppress for now, to be revisited.
			'wc-admin-selling-online-courses', // suppress for now, to be revisited.
			// Extensions.
			'woocommerce-services',
			'gla-invalid-php-version',
			'gla-64-bit',
			'gla-wc-admin',
			'gla-wc-requirement',
			'facebook-for-woocommerce-settings-moved-to-marketing',
			'wc-payments-notes-set-up-stripe-link',
			'wc-pb-bulk-discounts',
			'wc-prl-whats-new-1-4',
			'automatewoo-system-checks',
			'automatewoo-php-minimum-version-check',
			'automatewoo-wc-minimum-version-check',
			'automatewoo-welcome-notification',
			'automatewoo-subscriptions-addon-deactivated',
			'automatewoo-update',
		);

		if ( ! WCAdminHelper::is_wc_admin_active_for( 5 * DAY_IN_SECONDS ) ) {
			$suppress_list[] = 'wc-refund-returns-page';
		}

		return $suppress_list;
	}

	/**
	 * Indicates whether a note must be suppressed.
	 *
	 * @param Note $note
	 * @return boolean
	 */
	protected function is_suppress_listed( $note ) {
		if ( ! $this->has_suppress_list() ) {
			return false;
		}

		return in_array( $note->get_name(), $this->get_suppress_list(), true );
	}

	/**
	 * Method to create a new note in the database conditionally.
	 *
	 * @param Note $note Admin note.
	 */
	public function create( &$note ) {

		// If an allow-list exists and the message is not there, do not create it.
		if ( $this->has_allow_list() && ! $this->is_allow_listed( $note ) ) {
			return;
		}

		// If a suppress-list exists and the message is there, do not create it.
		if ( $this->has_suppress_list() && $this->is_suppress_listed( $note ) ) {
			return;
		}

		parent::create( $note );
	}

	/**
	 * Return where clauses for notes queries without applying woocommerce_note_where_clauses filter.
	 * INTERNAL: This method is not intended to be used by external code, and may change without notice.
	 *
	 * @param array $args Array of arguments for query conditionals.
	 * @return string Where clauses.
	 */
	protected function args_to_where_clauses( $args = array() ) {
		$allowed_types    = Note::get_allowed_types();
		$where_type_array = $this->get_escaped_arguments_array_by( $args, 'type', $allowed_types );

		$allowed_statuses   = Note::get_allowed_statuses();
		$where_status_array = $this->get_escaped_arguments_array_by( $args, 'status', $allowed_statuses );

		$escaped_is_deleted = '';
		if ( isset( $args['is_deleted'] ) ) {
			$escaped_is_deleted = esc_sql( $args['is_deleted'] );
		}

		$args_for_name = $args;
		if ( $this->has_allow_list() ) {
			$args_for_name['name'] = isset( $args['name'] ) ? array_intersect( $args['name'], $this->get_allow_list() ) : $this->get_allow_list();
		}

		$args_for_excluded_name = $args;
		if ( $this->has_suppress_list() ) {
			$args_for_excluded_name['excluded_name'] = isset( $args['excluded_name'] ) ? array_unique( array_merge( $args['excluded_name'], $this->get_suppress_list() ) ) : $this->get_suppress_list();
		}

		$where_name_array          = $this->get_escaped_arguments_array_by( $args_for_name, 'name' );
		$where_excluded_name_array = $this->get_escaped_arguments_array_by( $args_for_excluded_name, 'excluded_name' );
		$where_source_array        = $this->get_escaped_arguments_array_by( $args, 'source' );

		$escaped_where_types          = implode( ',', $where_type_array );
		$escaped_where_status         = implode( ',', $where_status_array );
		$escaped_where_names          = implode( ',', $where_name_array );
		$escaped_where_excluded_names = implode( ',', $where_excluded_name_array );
		$escaped_where_source         = implode( ',', $where_source_array );
		$where_clauses                = '';

		if ( ! empty( $escaped_where_types ) ) {
			$where_clauses .= " AND type IN ($escaped_where_types)";
		}

		if ( ! empty( $escaped_where_status ) ) {
			$where_clauses .= " AND status IN ($escaped_where_status)";
		}

		if ( ! empty( $escaped_where_names ) ) {
			$where_clauses .= " AND name IN ($escaped_where_names)";
		}

		if ( ! empty( $escaped_where_excluded_names ) ) {
			$where_clauses .= " AND name NOT IN ($escaped_where_excluded_names)";
		}

		if ( ! empty( $escaped_where_source ) ) {
			$where_clauses .= " AND source IN ($escaped_where_source)";
		}

		if ( isset( $args['is_read'] ) ) {
			$where_clauses .= $args['is_read'] ? ' AND is_read = 1' : ' AND is_read = 0';
		}

		$where_clauses .= $escaped_is_deleted ? ' AND is_deleted = 1' : ' AND is_deleted = 0';

		return $where_clauses;
	}

	/**
	 * Parses the query arguments passed in as arrays and escapes the values.
	 * Re-declared to be usable from this class.
	 *
	 * @param array      $args          the query arguments.
	 * @param string     $key           the key of the specific argument.
	 * @param array|null $allowed_types optional allowed_types if only a specific set is allowed.
	 *
	 * @return array the escaped array of argument values.
	 */
	private function get_escaped_arguments_array_by( $args = array(), $key = '', $allowed_types = null ) {
		$arg_array = array();
		if ( isset( $args[ $key ] ) ) {
			foreach ( $args[ $key ] as $args_type ) {
				$args_type = trim( $args_type );
				$allowed   = is_null( $allowed_types ) || in_array( $args_type, $allowed_types, true );
				if ( $allowed ) {
					$arg_array[] = sprintf( "'%s'", esc_sql( $args_type ) );
				}
			}
		}

		return $arg_array;
	}
}
