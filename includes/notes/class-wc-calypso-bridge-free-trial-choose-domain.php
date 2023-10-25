<?php
/**
 * WooCommerce Calypso Bridge
 * Free Trial Choose Domain Note
 *
 * @package WC_Calypso_Bridge/Notes
 * @since   x.x.x
 * @version x.x.x
 */

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;
use Automattic\WooCommerce\Admin\WCAdminHelper;

defined( 'ABSPATH' ) || exit;

/**
 * WC_Calypso_Bridge_Free_Trial_Choose_Domain_Note
 */
class WC_Calypso_Bridge_Free_Trial_Choose_Domain_Note {
	/**
	 * Note traits.
	 */
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-calypso-bridge-free-trial-choose-domain';

	/**
	 * Checks if the note can be added.
	 *
	 * @return bool
	 * @throws NotesUnavailableException Throws exception when notes are unavailable.
	 */
	public static function can_be_added() {
		$note = self::get_note();

		if ( ! $note instanceof Note && ! $note instanceof WC_Admin_Note ) {
			return;
		}

		if ( self::note_exists() ) {
			return false;
		}

		// Free trial plan.
		if ( ! wc_calypso_bridge_is_ecommerce_trial_plan() ) {
			return false;
		}

		// At least 1 day elapsed.
		if ( ! WCAdminHelper::is_wc_admin_active_for( DAY_IN_SECONDS ) ) {
			return false;
		}

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
			'free-trial-free-domain-upgrade',
			__( 'Choose a domain', 'wc-calypso-bridge' ),
			self::get_action_url()
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
