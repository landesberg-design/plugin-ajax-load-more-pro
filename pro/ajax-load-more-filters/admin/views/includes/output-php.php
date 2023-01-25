<div id="alm-filter-pop-up">
	<div class="inner-wrap">
		<h3><?php _e( 'Generated PHP', 'ajax-load-more-filters' ); ?></h3>
		<p><?php _e( 'The following PHP has been generated from your filter selection. Add this code can be added directly to a WordPress template in place of the filter shortcode.', 'ajax-load-more-filters' ); ?></p>
		<p><?php _e( '<strong>Note</strong>: Do <u>NOT</u> delete this filter from the WordPress backend - it is still required for frontend functionality.', 'ajax-load-more-filters' ); ?></p>
		<div class="alm-filter-output output">
			<pre class="output"></pre>
		</div>
		<p><?php _e( 'Don\'t forget to replace the <strong>{YOUR_ALM_ID}</strong> value above with your Ajax Load More ID.', 'ajax-load-more-filters' ); ?></p>
		<button class="button" v-on:click="closeModal"><?php _e( 'Close Window', 'ajax-load-more-filters' ); ?></button>
	</div>
</div>
