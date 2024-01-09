<?php
/**
 * WooCommerce Calypso Bridge
 * Choose Domain Note
 *
 * @package WC_Calypso_Bridge/Notes
 * @since   2.2.20
 * @version 2.3.2
 */

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\Notes;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;
use Automattic\WooCommerce\Admin\WCAdminHelper;

defined( 'ABSPATH' ) || exit;

/**
 * WC_Calypso_Bridge_Choose_Domain_Note
 */
class WC_Calypso_Bridge_Choose_Domain_Note {
	/**
	 * Note traits.
	 */
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-calypso-bridge-choose-domain';

	/**
	 * Checks if the note can be added.
	 *
	 * @throws NotesUnavailableException Throws exception when notes are unavailable.
	 * @return bool
	 */
	public static function can_be_added() {
		$note = self::get_note();

		if ( ! $note instanceof Note && ! $note instanceof WC_Admin_Note ) {
			return;
		}

		if ( self::note_exists() ) {
			return false;
		}

		// At least 1 hour elapsed.
		if ( ! WCAdminHelper::is_wc_admin_active_for( HOUR_IN_SECONDS ) ) {
			return false;
		}

		return self::is_applicable();
	}

	/**
	 * Should this note exist? This note should show up only for users who haven't yet purchased or transferred a domain.
	 *
	 * @return bool
	 */
	public static function is_applicable() {

		// Domain has not been purchased?
		if ( ! function_exists( 'wpcom_get_site_purchases' ) ) {
			return false;
		}

		$site_purchases   = wpcom_get_site_purchases();
		$domain_purchases = array_filter(
			$site_purchases,
			function ( $site_purchase ) {
				return in_array( $site_purchase->product_type, array( 'domain_map', 'domain_reg' ), true );
			}
		);

		if ( ! empty( $domain_purchases ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Mark the note as unread for users who just upgraded to a paid plan.
	 *
	 * @return void
	 */
	public static function update_note() {
		$note = Notes::get_note_by_name( self::NOTE_NAME );

		if ( ! $note instanceof Note && ! $note instanceof WC_Admin_Note ) {
			return;
		}

		if ( ! self::note_exists() ) {
			return;
		}

		// Check if an upgrade to a paid plan has been purchased.
		$all_site_purchases = wpcom_get_site_purchases();
		$plan_purchases     = array_filter(
			$all_site_purchases,
			function ( $purchase ) {
				return 'bundle' === $purchase->product_type;
			}
		);

		if ( empty( $plan_purchases ) ) {
			return;
		}

		// Calculate how many hours have passed since the paid plan purchase.
		$subscribed_date = strtotime( get_date_from_gmt( $plan_purchases[0]->subscribed_date ) );
		$current_date    = current_time( 'timestamp', true );
		$hours_passed    = ( $current_date - $subscribed_date ) / HOUR_IN_SECONDS;

		// If less than 2 hours have passed, mark the domain purchase note as unread to increase conversion.
		if ( $hours_passed < 2 ) {
			$note->set_is_read( false );
			$note->save();
		}
	}

	/**
	 * Get the note.
	 *
	 * @return void|Note
	 */
	public static function get_note() {

		$note = new Note();

		$note->set_title( __( 'Pick a domain for your new store', 'wc-calypso-bridge' ) );
		$note->set_content(
			__( 'A short, easy-to-remember domain name is a must-have for your store. It makes it simpler for customers to find you online, reinforces your brand identity, and helps establish trust.', 'wc-calypso-bridge' ) .
			'<br><br>' .
			__( 'Ready to make that perfect domain yours?', 'wc-calypso-bridge' )
		);
		$note->set_content_data( (object) array() );
		$note->set_type( Note::E_WC_ADMIN_NOTE_INFORMATIONAL );
		$note->set_name( self::NOTE_NAME );
		$note->set_source( 'wc-calypso-bridge' );
		$note->add_action(
			'woo-express-domain-upgrade',
			__( 'Choose a domain', 'wc-calypso-bridge' ),
			self::get_action_url(),
			Note::E_WC_ADMIN_NOTE_UNACTIONED
		);

		return $note;
	}

	/**
	 * Domain purchase URL used in message action.
	 *
	 * @return string
	 */
	public static function get_action_url() {

		$site_suffix = WC_Calypso_Bridge_Instance()->get_site_slug();
		$domain_path = sprintf( "https://wordpress.com/domains/add/%s", $site_suffix );
		$home_url    = \home_url( '', 'https' );

		if ( ! \str_ends_with( $home_url, '.wpcomstaging.com' ) ) {
			return $domain_path;
		}

		if ( ! \str_starts_with( $home_url, 'https://woo-' ) && ! \str_starts_with( $home_url, 'https://wooexpress-' ) ) {
			return $domain_path;
		}

		$blog_name = \get_option( 'blogname' );
		if ( empty( $blog_name ) ) {
			return $domain_path;
		}

		return sprintf( '%s?suggestion=%s', $domain_path, rawurlencode( $blog_name ) );
	}
}
