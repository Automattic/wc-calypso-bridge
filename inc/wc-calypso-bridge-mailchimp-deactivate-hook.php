<?php
/**
 * Clear out MailChimp tables on deactivation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

register_deactivation_hook( 'mailchimp-for-woocommerce/mailchimp-woocommerce.php', 'wc_calypso_bridge_clear_mailchimp_tables' );

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

	foreach( (array) $mailchimp_tables as $table ) {
		$sql = $wpdb->prepare( "TRUNCATE TABLE `$table`" );

		if ( $wpdb->query( $sql ) ) {
			$log_message = "Plugin Deactivated: success clearing `$table` table.";
		} else {
			$log_message = "Plugin Deactivated: FAILURE clearing `$table` table.";
		}

		if ( $logger ) {
			$logger->add( 'mailchimp-for-woocommerce', $log_message );
		}
	}
}
