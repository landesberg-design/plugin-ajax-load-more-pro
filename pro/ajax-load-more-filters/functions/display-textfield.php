<?php
/**
 * Functions related to the textfield field type.
 *
 * @package ALMFilters
 */

/**
 * Renders a filter textfield.
 *
 * @param string $id          Filter ID.
 * @param array  $obj         Filter object array.
 * @param string $querystring The querystring.
 * @return string             Raw HTML output.
 * @since 1.0
 */
function alm_filters_display_textfield( $id, $obj, $querystring ) {
	$text_id = $obj['key'] . '-' . $obj['field_type'];
	$output  = '';

	// Parse Querystring params.
	if ( $obj['key'] === 'meta' ) {
		$selected = isset( $querystring[ $obj['meta_key'] ] ) ? $querystring[ $obj['meta_key'] ] : '';
	} elseif ( $obj['key'] === 'taxonomy' ) {
		$selected = isset( $querystring[ $obj['taxonomy'] ] ) ? $querystring[ $obj['taxonomy'] ] : '';
	} else {
		$selected = isset( $querystring[ $obj['key'] ] ) ? $querystring[ $obj['key'] ] : '';
	}

	$textfield_type = 'text';
	$placeholder    = isset( $obj['placeholder'] ) ? 'placeholder="' . $obj['placeholder'] . '"' : '';
	$datepicker     = $obj['field_type'] === 'date_picker' ? true : false;
	$rangeslider    = $obj['field_type'] === 'range_slider' ? true : false;
	$has_button     = ! empty( $obj['button_label'] ) ? true : false;
	$has_button     = $rangeslider ? false : $has_button; // Set false if range slider.
	$field_class    = $has_button ? ' has-button' : '';
	$display_style  = '';

	$output .= '<div class="alm-filter--' . $obj['field_type'] . '">';

	if ( $obj['label'] ) {
		$output .= alm_filters_render_label( $id, $obj, $text_id . '-' . $obj['index'] );
	}

	if ( $rangeslider ) {
		// Range Slider opts.
		$range_min = isset( $obj['rangeslider_min'] ) ? $obj['rangeslider_min'] : 0;
		$range_max = isset( $obj['rangeslider_max'] ) ? $obj['rangeslider_max'] : 100;

		$range_start      = isset( $obj['rangeslider_start'] ) ? $obj['rangeslider_start'] : $range_min;
		$range_start_orig = $range_start === '' ? $range_min : $range_start;
		$range_end        = isset( $obj['rangeslider_end'] ) ? $obj['rangeslider_end'] : $range_max;
		$range_end_orig   = $range_end === '' ? $range_max : $range_end;

		$rangeslider_label       = isset( $obj['rangeslider_label'] ) ? $obj['rangeslider_label'] : '{start} - {end}';
		$rangeslider_steps       = isset( $obj['rangeslider_steps'] ) ? $obj['rangeslider_steps'] : 1;
		$rangeslider_orientation = isset( $obj['rangeslider_orientation'] ) ? $obj['rangeslider_orientation'] : 'horizontal';
		$rangeslider_decimals    = isset( $obj['rangeslider_decimals'] ) ? $obj['rangeslider_decimals'] : 'true';
		$rangeslider_reset       = isset( $obj['rangeslider_reset'] ) ? $obj['rangeslider_reset'] : 'true';

		// Parse selected value.
		$values = ! empty( $selected ) ? explode( ',', $selected ) : '';
		if ( ! empty( $values ) ) {
			$range_start = $values[0];
			$range_end   = isset( $values[1] ) ? $values[1] : $range_max;
		}

		$output .= '<div class="alm-range-slider"
						data-min="' . $range_min . '"
						data-max="' . $range_max . '"
						data-start-reset="' . $range_start_orig . '"
						data-start="' . $range_start . '"
						data-end-reset="' . $range_end_orig . '"
						data-end="' . $range_end . '"
						data-label="' . $rangeslider_label . '"
						data-steps="' . $rangeslider_steps . '"
						data-orientation="' . $rangeslider_orientation . '"
						data-decimals="' . $rangeslider_decimals . '"
						>';
		$output .= '<div class="alm-range-slider--target"></div>';
		$output .= '<div class="alm-range-slider--wrap">';
		$output .= '<div class="alm-range-slider--label"></div>';

		if ( $rangeslider_reset !== 'false' ) {
			// Reset Button.
			$output .= '<button class="alm-range-slider--reset alm-range-reset" type="button" style="display: none;">';
			$output .= apply_filters( 'alm_filters_range_slider_reset_label', __( 'Reset', 'ajax-load-more-filters' ) );
			$output .= '</button>';
		}

		$output       .= '</div>';
		$output       .= '</div>';
		$display_style = ' style="display: none;"';
	}

	$output .= '<div class="alm-filter--text-wrap' . $field_class . '"' . $display_style . '>';

	if ( $datepicker ) {
		// Date Picker.
		$datepicker_mode        = isset( $obj['datepicker_mode'] ) ? $obj['datepicker_mode'] : 'single';
		$datepicker_mode_return = isset( $obj['datepicker_mode'] ) ? ' data-display-mode="' . $datepicker_mode . '"' : ' data-display-mode="single"';
		$datepicker_format      = isset( $obj['datepicker_format'] ) ? ' data-display-format="' . $obj['datepicker_format'] . '"' : ' data-display-format="Y-m-d"';
		$datepicker_locale      = isset( $obj['datepicker_locale'] ) ? ' data-date-locale="' . $obj['datepicker_locale'] . '"' : ' data-date-locale="en"';

		// Replace `+` with ` | ` for range mode.
		$selected = $datepicker_mode === 'range' ? str_replace( '+', ' | ', $selected ) : $selected;

		$output .= '<input class="alm-filter--textfield textfield alm-flatpickr" id="' . $text_id . '-' . $obj['index'] . '" name="' . $text_id . '" type="text" value="' . esc_attr( $selected ) . '" ' . $placeholder . '' . $datepicker_format . '' . $datepicker_mode_return . '' . $datepicker_locale . ' />';

	} else {
		// Standard.
		$output .= '<input class="alm-filter--textfield textfield"';
		$output .= ' id="' . $text_id . '-' . $obj['index'] . '"';
		$output .= ' name="' . $text_id . '"';
		$output .= ' type="' . $textfield_type . '"';
		$output .= ' value="' . esc_attr( $selected ) . '"';
		$output .= ' ' . $placeholder . ' />';
	}

	$output .= $has_button ? '<button type="button">' . $obj['button_label'] . '</button>' : '';
	$output .= '</div>';
	$output .= '</div>';

	return $output;
}
