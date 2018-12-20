<?php
/**
 * Clear out MailChimp tables on deactivation
 *
 * @package WC_Calypso_Bridge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

register_deactivation_hook( 'mailchimp-for-woocommerce/mailchimp-woocommerce.php', 'wc_calypso_bridge_clear_mailchimp_tables' );

/**
 * Clears out MailChimp queue tables on deactivation.
 */
function wc_calypso_bridge_clear_mailchimp_tables() {
	global $wpdb;
	$logger = false;

	if ( class_exists( 'WC_Logger' ) ) {
		$logger = new WC_Logger();
	}

	$mailchimp_tables = array(
		"{$wpdb->prefix}queue",
		"{$wpdb->prefix}failed_jobs",
		"{$wpdb->prefix}mailchimp_carts",
	);

	foreach ( (array) $mailchimp_tables as $table ) {
		if ( $wpdb->query( "TRUNCATE TABLE `$table`" ) ) { // WPCS: unprepared SQL ok.
			$log_message = "Plugin Deactivated: success clearing `$table` table.";
		} else {
			$log_message = "Plugin Deactivated: FAILURE clearing `$table` table.";
		}

		if ( $logger ) {
			$logger->add( 'mailchimp-for-woocommerce', $log_message );
		}
	}
}
