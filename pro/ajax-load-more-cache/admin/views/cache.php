<?php
/**
 * The view for the ALM Cache admin page.
 *
 * @package ajax-load-more-cache
 */

$alm_cache_array   = ALMCache::alm_get_cache_array(); // Get dynamic cache pages.
$path              = ALMCache::alm_get_cache_path(); // get path to cache.
$cache_build       = false;
$alm_admin_heading = __( 'Cache', 'ajax-load-more-cache' );
$alm_query_params  = filter_input_array( INPUT_GET );

if ( isset( $alm_query_params ) ) {
	// Delete cache action.
	if ( isset( $alm_query_params['action'] ) && $alm_query_params['action'] === 'delete' ) {
		$result = ALMCache::alm_delete_full_cache();

		// Redirect user to ?action=alm-cache-deleted to prevent double form submit.
		echo '<script> window.location="admin.php?page=ajax-load-more-cache&action=alm-cache-deleted"; </script> ';
	}

	// Cache build action.
	if ( isset( $alm_query_params['action'] ) && $alm_query_params['action'] === 'build' ) {
		// Clear current cache before building.
		do_action( 'alm_clear_cache' );

		$cache_build = true;
	}
	unset( $alm_query_params );
}

?>
<div class="wrap ajax-load-more alm-cache main-cnkt-wrap" id="alm-cache">
	<?php
	if ( defined( 'ALM_PATH' ) && file_exists( ALM_PATH . 'admin/includes/components/header.php' ) ) {
		require_once ALM_PATH . 'admin/includes/components/header.php';
	}
	require_once ALM_CACHE_ADMIN_PATH . 'admin/views/includes/upgrade-notification.php';
	?>
	<div class="ajax-load-more-inner-wrapper">
		<section class="cnkt-main">
			<?php
			if ( $cache_build && $alm_cache_array ) {
				// Generate Cache.
				include_once ALM_CACHE_ADMIN_PATH . 'admin/views/auto-generate.php';
			} else {
				// Cache Listing.
				include_once ALM_CACHE_ADMIN_PATH . 'admin/views/file-listing.php';
			}
			?>
		</section>
		<aside class="cnkt-sidebar">
			<div class="cta">
				<h3><?php esc_attr_e( 'Cache Statistics', 'ajax-load-more-cache' ); ?></h3>
				<div class="cta-inner">
				<?php
				// Count cache files and directories.
				$dircount    = 0;
				$filecount   = 0;
				$directories = [];

				// Create directory if it does not exist.
				if ( ! is_dir( $path ) ) {
					wp_mkdir_p( $path );
				}
				foreach ( new DirectoryIterator( $path ) as $file ) {
					if ( $file->isDot() ) {
						continue;
					}

					if ( $file->isDir() ) {
						$directories[] = $file->getFilename();
					}
				}

				foreach ( $directories as $directory ) {
					$val = count( glob( $path . $directory . '/*.json' ) );
					$dircount++;
					$filecount = $filecount + $val;

					// Sub Directories.
					$sub_dir  = array();
					$sub_path = $path . $directory;
					foreach ( new DirectoryIterator( $sub_path ) as $file ) {
						if ( $file->isDot() ) {
							continue;
						}

						if ( $file->isDir() ) {
							$sub_dir[] = $file->getFilename();
						}
					}
					if ( $sub_dir ) {
						foreach ( $sub_dir as $subdirectory ) {
							$val = count( glob( $path . $directory . '/' . $subdirectory . '/*.html' ) );
							$dircount++;
							$filecount = $filecount + $val;
						}
					}
				}
				?>
				<p class="cache-stats">
					<span class="stat" id="dircount"><?php echo $dircount; ?></span>
					<?php esc_attr_e( 'Page', 'ajax-load-more-cache' ); ?><?php echo ( $dircount > 1 || $dircount == 0 ) ? 's' : ''; ?> <?php _e( 'Cached', 'ajax-load-more-cache' ); ?>
				</p>
				<div class="spacer"></div>
					<p class="cache-stats last">
						<span class="stat" id="filecount"><?php echo $filecount; ?></span>
						<?php esc_attr_e( 'File', 'ajax-load-more-cache' ); ?><?php echo ( $filecount > 1 || $filecount == 0 ) ? 's' : ''; ?> <?php _e( 'Cached', 'ajax-load-more-cache' ); ?>
					</p>
				</div>
				<div class="major-publishing-actions">
					<form id="delete-all-cache" name="delete-all-cache" action="admin.php" method="GET" data-path="<?php echo ALMCache::alm_get_cache_path(); ?>">
						<input type="hidden" value="ajax-load-more-cache" name="page">
						<button type="submit" class="button-primary" name="action" value="delete">
							<?php esc_attr_e( 'Delete Cache', 'ajax-load-more-cache' ); ?>
						</button>
					</form>
				</div>
			</div>

			<?php if ( ! $cache_build ) { ?>
			<div class="cta">
				<h3><?php esc_attr_e( 'Auto-Generate Cache', 'ajax-load-more-cache' ); ?></h3>
				<?php
				if ( $alm_cache_array ) {
					?>
				<div class="cta-inner">
					<p><?php echo wp_kses_post( __( 'You have enabled auto-generation of the Ajax Load More cache. Click the <strong>Generate Cache</strong> below to start the process.', 'ajax-load-more-cache' ) ); ?></p>
				</div>
				<div class="major-publishing-actions">
					<button type="button" class="button-primary button-alm-generate-cache">
						<?php esc_attr_e( 'Generate Cache', 'ajax-load-more-cache' ); ?>
					</button>
					<a class="button" href="https://connekthq.com/plugins/ajax-load-more/docs/add-ons/cache/#auto-generate" target="_blank">
						<?php esc_attr_e( 'Documentation', 'ajax-load-more-cache' ); ?>
					</a>
				</div>
				<?php } else { ?>
				<div class="cta-inner">
					<p><?php esc_attr_e( 'Did you know you can auto-generate your entire Ajax Load More cache?', 'ajax-load-more-cache' ); ?></p>
				</div>
				<div class="major-publishing-actions">
					<a class="button-primary" href="https://connekthq.com/plugins/ajax-load-more/docs/add-ons/cache/#auto-generate" target="_blank">
						<?php esc_attr_e( 'Learn More', 'ajax-load-more-cache' ); ?>
					</a>
				</div>
				<?php } ?>
			</div>
			<?php } ?>
			<?php require_once ALM_CACHE_ADMIN_PATH . 'admin/views/includes/writeable.php'; ?>
			<div class="clear"></div>
		</aside>
	</div>
</div>
