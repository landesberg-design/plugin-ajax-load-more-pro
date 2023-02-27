<?php
/**
 * CTA to list all filters.
 *
 * @package ALMFilters
 */

?>
<div class="cta">
	<h3>
		<?php esc_html_e( 'Ajax Load More Filters', 'ajax-load-more-filters' ); ?>
	</h3>
	<div class="cta-inner filter-listing">
		<?php echo alm_list_all_filters( $filter_id ); // phpcs:ignore ?>
	</div>
</div>
