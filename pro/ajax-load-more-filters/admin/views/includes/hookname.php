<?php
/**
 * The template for displaying the template hookname.
 *
 * @package ALMFilters
 */

?>
<div class="hookname" v-show="filter.hookname">
	<p>
		<i class="fa fa-lightbulb-o" aria-hidden="true"></i>
		<?php esc_html_e( 'The unique id for this filter is:', 'ajax-load-more-filters' ); ?> <span>{{filter.hookname}}</span>
	</p>
</div>
