<?php
/**
 * Listing display.
 *
 * @package ajax-load-more-cache
 */

$files          = [];
$sub_path       = $path . $directory;
$filepath       = $directory;
$alm_cache_info = ALMCache::alm_get_cache_path() . '/' . $directory . '/_info.txt';

echo ALMCache::alm_cache_get_info( $alm_cache_info, $sub_path, $directory );

// Display cached pages.
echo '<div class="cache-page-wrap">';
echo '<ul>';
foreach ( new DirectoryIterator( $sub_path ) as $sub_file ) {
	if ( $sub_file->isDot() || $sub_file->getFilename() === '_info.txt' ) {
		continue;
	}
	if ( $sub_file->isFile() ) {
		$files[] = $sub_file->getFilename();
	}
}
if ( $files ) {
	asort( $files ); // Sort the file array.
	foreach ( $files as $file ) {
		include ALM_CACHE_ADMIN_PATH . 'admin/views/includes/file.php';
	}
} else {
	include ALM_CACHE_ADMIN_PATH . 'admin/views/includes/no-files.php';
}
echo '</ul>';
echo '</div>';
