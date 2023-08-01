<?php
/**
 * Auto-generate file listing view.
 *
 * @package ajax-load-more-cache
 */

?>
<h2><?php esc_attr_e( 'Cache Auto-Generation', 'ajax-load-more-cache' ); ?></h2>
<p>
	<?php esc_attr_e( 'You have enabled auto-generation of the Ajax Load More cache. The cache IDs listed below indicate which instances will be created during the build process.', 'ajax-load-more-cache' ); ?>
</p>
<p>
	<a href="https://connekthq.com/plugins/ajax-load-more/docs/add-ons/cache/#auto-generate" target="_blank">
		<strong><?php esc_attr_e( 'View Documentation', 'ajax-load-more-cache' ); ?></strong>
	</a>
</p>
<div class="alm-generate-cache">
	<p class="alm-cache-status">
		<span id="alm-cache-processing-txt">
			<?php esc_attr_e( 'Building cache, this may take a bit...', 'ajax-load-more-cache' ); ?>
		</span>
		<span id="alm-cache-paused-txt">
			<i class="fa fa-pause" aria-hidden="true"></i>
			<?php esc_attr_e( 'Cache Paused', 'ajax-load-more-cache' ); ?>
		</span>
		<span id="alm-cache-complete-txt">
			<i class="fa fa-check" aria-hidden="true"></i> <?php esc_attr_e( 'Cache created successfully!', 'ajax-load-more-cache' ); ?> &nbsp;&nbsp; <a style="float: right;" href="admin.php?page=ajax-load-more-cache"><?php _e( 'View Cache', 'ajax-load-more-cache' ); ?></a>
		</span>
	</p>
	<?php if ( $alm_cache_array ) { ?>
	<div class="iframe-target"></div>
	<script>
		var alm_cache_array = <?php echo $alm_cache_array; ?>;
	</script>
	<ul class="alm-generate-cache--list">
		<?php
		foreach ( json_decode( $alm_cache_array ) as $cache ) {
			echo '<li data-id="' . $cache->id . '" data-url="' . $cache->url . '">' . $cache->id . ' <small><a href="' . $cache->url . '" target="_blank">' . $cache->url . '</a></small></li>';
		}
		?>
	</ul>
	<div class="alm-generate-cache--controls">
		<button class="button cache-pause"><i class="fa fa-pause"></i> &nbsp;<?php esc_attr_e( 'Pause', 'ajax-load-more-cache' ); ?></button>
		<button style="display: none;" class="button button-primary cache-resume"><i class="fa fa-play"></i> &nbsp;<?php esc_attr_e( 'Resume', 'ajax-load-more-cache' ); ?></button>
		<button style="display: none;" class="button button-primary cache-rebuild"><i class="fa fa-refresh"></i> &nbsp;<?php esc_attr_e( 'Rebuild Cache', 'ajax-load-more-cache' ); ?></button>
		<p id="alm-elapsed-time"><?php esc_attr_e( 'Elapsed time:', 'ajax-load-more-cache' ); ?> <span></span></p>
	</div>
	<?php } else { ?>
		<p><?php esc_attr_e( 'No cached items found.', 'ajax-load-more-cache' ); ?></p>
	<?php } ?>
</div>
<p><?php echo wp_kses_post( __( '<strong>Note</strong>: The time required to build the entire cache depends on the amount of pages needed to complete the process. If you leave this page, the cache build process will stop.', 'ajax-load-more-cache' ) ); ?></p>
