<?php
/**
 * The template for displaying the filters PHP output.
 *
 * @package ALMFilters
 */

?>
<div id="alm-filter-pop-up">
	<div class="inner-wrap">
		<h3><?php esc_attr_e( 'Generated PHP', 'ajax-load-more-filters' ); ?></h3>
		<p><?php esc_attr_e( 'The following PHP has been generated from your filter selections. This code can be added directly to a WordPress template in place of the filter shortcode.', 'ajax-load-more-filters' ); ?></p>
		<p>
		<?php
		echo wp_kses_post(
			__(
				'<strong>Note</strong>: Do <strong><u>NOT</u></strong> delete this filter from the WordPress backend - it is still required for frontend functionality.',
				'ajax-load-more-filters'
			)
		);
		?>
		</p>
		<div class="alm-filter-output output">
			<pre class="output"></pre>
		</div>
		<p><?php echo wp_kses_post( __( 'Don\'t forget to replace the <strong>YOUR_ALM_ID</strong> value above with your Ajax Load More ID.', 'ajax-load-more-filters' ) ); ?></p>
		<button class="button" v-on:click="closeModal"><?php esc_attr_e( 'Close Window', 'ajax-load-more-filters' ); ?></button>
	</div>
</div>
