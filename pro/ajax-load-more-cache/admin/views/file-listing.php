<?php
/**
 * Standard file listing view.
 *
 * @package ajax-load-more-cache
 */

?>
<h2><?php esc_attr_e( 'Cache Dashboard', 'ajax-load-more-cache' ); ?></h2>
<p><?php echo wp_kses_post( __( 'All cached files in your Ajax Load More cache are listed below. The cache listing is grouped by the <strong>Cache ID</strong> assigned when your <a href="admin.php?page=ajax-load-more-shortcode-builder">Shortcode</a> was created.', 'ajax-load-more-cache' ) ); ?></small></p>
<p>
	<a href="admin.php?page=ajax-load-more#cache_settings">
		<strong><?php esc_attr_e( 'Cache Settings', 'ajax-load-more-cache' ); ?></strong>
	</a> &nbsp;|&nbsp;
	<a href="https://connekthq.com/plugins/ajax-load-more/docs/add-ons/cache/" target="_blank">
		<strong><?php esc_attr_e( 'View Documentation', 'ajax-load-more-cache' ); ?></strong>
	</a>
</p>

<div class="spacer"></div>
<div class="alm-cache-search-wrap" style="margin-top: 3px;">
	<input type="text" name="alm-cache-search" id="alm-cache-search" value="" placeholder="<?php _e( 'Search cache by ID or URL ', 'ajax-load-more-cache' ); ?>">
	<i class="fa fa-search"></i>
</div>

<hr class="cache-break" />

<div class="alm-cache-listing no-shadow">
	<div class="row no-brd">
		<span class="toggle-all" tabindex="0">
			<span class="inner-wrap">
				<em class="collapse"><?php esc_attr_e( 'Collapse All', 'ajax-load-more-cache' ); ?></em>
				<em class="expand"><?php esc_attr_e( 'Expand All', 'ajax-load-more-cache' ); ?></em>
			</span>
		</span>

		<?php
		// Loop Cache Directories.
		$directoy_total    = 0;
		$staticDirectories = [];

		if ( is_dir( $path ) ) {

			// Loop the directories and store values in array for sorting.
			foreach ( new DirectoryIterator( $path ) as $file ) {
				if ( $file->isDot() ) {
					continue;
				}

				if ( $file->isDir() ) {
					$staticDirectories[] = $file->getFilename();
				}
			}

			asort( $staticDirectories ); // Sort the directory array.

			foreach ( $staticDirectories as $directory ) {
				// Loop thru our sorted directories and store files in array for sorting.
				$directoy_total++;
				$filepath = $path . $directory;

				echo '<div class="alm-dir-listing">';
				echo '<h3 class="heading dir dir-title" tabindex="0" title="' . esc_attr( $path ) . esc_attr( $directory ) . '">';
				echo esc_attr( $directory );
				echo '<a href="javascript:void(0);" class="delete" data-id="' . esc_attr( $directory ) . '" data-path="' . esc_attr( $path ) . esc_attr( $directory ) . '" title="' . esc_attr__( 'Delete this cache', 'ajax-load-more-cache' ) . '">';
				echo esc_attr__( 'Delete', 'ajax-load-more-cache' );
				echo '</a>';
				echo '</h3>';
				echo '<div class="expand-wrap">';
				include ALM_CACHE_ADMIN_PATH . 'admin/views/includes/listing.php';
				echo '</div>';
				echo '</div>';
			}
		}
		?>
	</div>
</div>
<?php
// Empty.
if ( $directoy_total === 0 ) {
	include ALM_CACHE_ADMIN_PATH . 'admin/views/includes/empty.php';
}
