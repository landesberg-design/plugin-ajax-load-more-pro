<?php
	// @codingStandardsIgnoreStart
?>
<div class="cta">
	<h3><?php _e('Cache Status', 'ajax-load-more-cache'); ?></h3>
	<div class="cta-inner">
   	<?php
      //Test server for write capabilities
      $path = ALMCache::alm_get_cache_path();
      if ( is_writable( $path ) ) {
			echo '<div class="alm-status success"><span><i class="fa fa-check"></i>' . esc_html__( 'Enabled', 'ajax-load-more' ) . '</span></div>';
			echo '<p>' . esc_html__( 'Read/Write access is enabled within the cache directory.', 'ajax-load-more') . '</p>';

      } else {
			echo '<div class="alm-status failed"><span><i class="fa fa-exclamation"></i>' . esc_html__( 'Access Denied', 'ajax-load-more' ) . '</span></div>';
			echo '<p>' . esc_html__( 'You must enable read and write access for the Ajax Load More cache directory to save cache data.', 'ajax-load-more') . '</p>';
			echo '<p>' . esc_html__( 'Please contact your hosting provider or site administrator for more information.', 'ajax-load-more') . '</p>';
		}
		?>

      <div class="alm-file-location">
         <input type="text" value="<?php echo $path; ?>" class="alm-file-location" readonly="readonly">
      </div>
	</div>
</div>
