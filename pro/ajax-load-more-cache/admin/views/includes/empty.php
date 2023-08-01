<?php
/**
 * Empty cache display.
 *
 * @package ajax-load-more-cache
 */

echo '<div class="call-out light radius-normal text-left margin-top">';
echo '<p>' . esc_attr__( 'The Ajax Load More Cache is currently empty.', 'ajax-load-more-cache' ) . '</p>';
if ( $alm_cache_array ) {
	echo '<a class="button button-primary" href="admin.php?page=ajax-load-more-cache&action=build">' . esc_attr__( 'Generate Cache', 'ajax-load-more-cache' ) . '</a>';
}
echo '</div>';
echo '<style>hr.cache-break, .toggle-all, .alm-cache-search-wrap{display:none ! important;}</style>';
