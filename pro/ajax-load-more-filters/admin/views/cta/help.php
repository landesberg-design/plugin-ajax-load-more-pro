<?php
/**
 * CTA to display help text.
 *
 * @package ALMFilters
 */

?>
<div class="cta">
	<h3>
		<?php esc_html_e( 'Help', 'ajax-load-more-filters' ); ?>
	</h3>
	<div class="cta-inner">
		<p style="margin-bottom: 15px;">
		<?php esc_html_e( 'View the Filters documentation for information on implementation methods, hooks and callback functions.', 'ajax-load-more-filters' ); ?>
	</p>
	</div>
	<div class="major-publishing-actions">
		<a class="button" href="https://connekthq.com/plugins/ajax-load-more/docs/add-ons/filters/" target="_blank">
			<?php esc_html_e( 'View Documentation', 'ajax-load-more-filters' ); ?>
		</a>
	</div>
</div>
