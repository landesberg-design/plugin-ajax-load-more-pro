<?php
/**
 * CTA to display what's new text.
 *
 * @package ALMFilters
 */

?>
<div class="cta">
	<h3><?php esc_html_e( 'What\'s New', 'ajax-load-more-filters' ); ?></h3>
	<div class="cta-inner">
		<p>🔥 Filters v1.5 introduces <strong>import</strong> and <strong>export</strong> functionality of Ajax Load More filters!</p>
	</div>
	<div class="major-publishing-actions">
		<a href="<?php echo esc_html( ALM_FILTERS_BASE_URL ); ?>&action=tools" class="button">
			<?php esc_html_e( 'Learn More', 'ajax-load-more-filters' ); ?>
		</a>
	</div>
</div>
