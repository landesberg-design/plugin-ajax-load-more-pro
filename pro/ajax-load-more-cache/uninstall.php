<?php
/**
 * Uninstall Ajax Load More: Cache
 *
 * Deletes all the plugin data i.e.
 *  1. Cache Directory.
 *
 * @package ajax-load-more-cache
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

alm_delete_cache_files( $path );

/**
 * Loop directories and remove files.
 */
function alm_delete_cache_files() {
	$upload_dir = wp_upload_dir();
	$path       = apply_filters( 'alm_cache_path', $upload_dir['basedir'] . '/alm-cache/' );

	// Loop all cached directories.
	foreach ( new DirectoryIterator( $path ) as $directory ) {
		if ( $directory->isDot() ) {
			continue;
		}
		if ( $directory->isDir() ) {
			$path_to_directory = $path . $directory;
			alm_cache_rmdir( $path_to_directory );
		}
	}

	rmdir( $path ); // Delete main cache directory.
}

/**
 * Delete all files and parent directory.
 *
 * @param string $directory Path to directory.
 */
function alm_cache_rmdir( $directory ) {
	if ( current_user_can( apply_filters( 'alm_custom_user_role', 'edit_theme_options' ) ) ) {
		if ( is_dir( $directory ) ) {
			foreach ( glob( $directory . '/*.*' ) as $filename ) {
				if ( is_file( $filename ) ) {
					unlink( $filename );
				}
			}
			rmdir( $directory );
		}
	}
}
