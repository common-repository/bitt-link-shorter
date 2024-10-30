<?php
/**
 * WP Shorter Links
 *
 * Uninstalling WP Shorter Links deletes options.
 *
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/*
 * Delete all data created by plugin, such as any options that were added to the options table.
 *
 */
delete_option( 'srx_wpsl_activated' );
delete_option( 'srx_wpsl_deactivated' );

/**
 * Clear any cached data that has been removed.
 *
 */
wp_cache_flush();