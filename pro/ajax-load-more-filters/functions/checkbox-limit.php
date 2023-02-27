<?php
/**
 * Functions related to the checkbox limit feature.
 *
 * @package ALMFilters
 */

/**
 * The checkbox limit classname.
 *
 * @param array $obj   Filter object array.
 * @param int   $limit Limit display count.
 * @param int   $count Term count.
 * @return string      Raw HTML markup.
 */
function alm_filters_checkbox_limit_button( $obj, $limit, $count ) {
	$limit_open = isset( $obj['checkbox_limit_label_open'] ) ? $obj['checkbox_limit_label_open'] : apply_filters( 'alm_filters_checkbox_limit_label_open', __( 'Show More', 'ajax-load-more-filters' ) );
	$open       = str_replace( '%total%', $count - $limit, $limit_open );

	$close = isset( $obj['checkbox_limit_label_close'] ) ? $obj['checkbox_limit_label_close'] : apply_filters( 'alm_filters_checkbox_limit_label_close', __( 'Show Less', 'ajax-load-more-filters' ) );
	return '<a class="alm-filter--checkbox-limit" role="button" tabindex="0" data-limit="' . esc_attr( $obj ['checkbox_limit'] ) . '" data-template="' . wp_kses_post( $limit_open ) . '" data-open="' . wp_kses_post( $open ) . '" data-close="' . wp_kses_post( $close ) . '">' . wp_kses_post( $open ) . '</a>';
}

/**
 * The checkbox limit classname.
 *
 * @return string The classname.
 */
function alm_filters_checkbox_limit_class() {
	return ' alm-filters-limit';
}

/**
 * The checkbox limit style attribute.
 *
 * @return string The style attribute.
 */
function alm_filters_checkbox_limit_style() {
	return ' style="display: none;"';
}
