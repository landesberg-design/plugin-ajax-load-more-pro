<?php
/**
 * Filter preview functions.
 *
 * @package ALMFilters
 */

/**
 * Display the Filter preview.
 *
 * @since 2.1
 * @return void
 */
function alm_filters_preview_filter() {
	$params = filter_input_array( INPUT_GET );
	if ( empty( $params['alm_filters_preview'] ) || ! current_user_can( apply_filters( 'alm_user_role', 'edit_theme_options' ) ) ) {
		return;
	}

	$filter_id = $params['alm_filters_preview'];
	$filter    = get_option( ALM_FILTERS_PREFIX . $filter_id );
	if ( ! $filter ) {
		return;
	}
	get_header();
	echo '<title>Ajax Load More Filter</title>';
	$custom_css = '
	body{
		padding: 10px;
		margin: 0 auto;
		background: #f7f7f7;
	}
	body > *:not(.alm-filter-preview) {
		display: none;
	}
	body .alm-filter-preview span.alm-filter-preview-title{
		font-size: 12px;
		color: #777;
		display: block;
		padding: 12px 20px;
		margin: 0;
		border-bottom: 1px solid #efefef;
	}
	body .alm-filter-preview span.alm-filter-preview-title strong{
		text-transform: uppercase;
		font-weight: 600;
	}
	.alm-filter-preview{
		display: block;
		background: #fff;
		padding: 0;
		margin: 0 auto;
		border: 1px solid #efefef;
		border-radius: 3px;
		max-width: 650px;
		box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
	}
	body .alm-filter-preview .alm-filters-container{
		padding: 20px;
		margin:0;
	}
	body .alm-filter-preview .alm-filters-edit{
		display: none !important;
	}';
	echo '<style>' . wp_strip_all_tags( $custom_css ) . '</style>'; // phpcs:ignore
	echo '<div class="' . esc_attr( apply_filters( 'alm_filters_preview_class', 'alm-filter-preview' ) ) . '">';
	echo '<span class="alm-filter-preview-title"><strong>' . esc_attr( __( 'Ajax Load More Filter Preview', 'ajax-load-more-filters' ) ) . '</strong> ';
	echo '(<a href="' . esc_attr( ALM_FILTERS_BASE_URL ) . '&filter=' . esc_attr( $filter_id ) . '">' . esc_attr( $filter_id ) . '</a>)';
	echo '</span>';
	echo do_shortcode( '[ajax_load_more_filters id="' . $filter_id . '" target="test" preview="true"]' );
	echo '</div>';

	echo '<script>';
	echo 'document.title = "ALM Filter Preview: ' . esc_attr( $filter_id ) . '";';
	echo 'var container = document.querySelector(".alm-filter-preview");';
	echo 'document.body.append(container);';
	echo 'var filter = document.querySelector(".alm-filters-facets");';
	echo 'if(filter){ filter.classList.add("alm-filters-facets-loaded");}';
	echo '</script>';
	get_footer();
	exit;
}
add_action( 'template_redirect', 'alm_filters_preview_filter' );
