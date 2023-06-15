<?php
/**
 * Individual cache file display.
 *
 * @package ajax-load-more-cache
 */

$alm_cache_file = str_replace( '.json', '', $file );

?>
<li class="file">
	<i class="fa fa-file-text-o"></i>
	<a href="<?php echo esc_url( ALMCache::alm_get_cache_rest_url( $filepath, $alm_cache_file ) ); ?>" target="_blank"><?php echo esc_attr( $alm_cache_file ); ?></a>
</li>
