<?php
/**
 * Functions related to custom value display.
 *
 * @package ALMFilters
 */

/**
 * Custom Values render method (cat, tag, custom tax, custom fields etc).
 *
 * @param string $id            Filter ID.
 * @param array  $custom_values Custom value array.
 * @param array  $obj           Filter object array.
 * @param string $querystring   The querystring.
 * @return string               Raw HTML output.
 * @since 1.0
 */
function alm_filters_list_custom_values( $id, $custom_values, $obj, $querystring ) {
	$field_type = $obj['field_type'];
	$key        = $obj['key'];

	if ( $field_type === 'text' || empty( $custom_values ) ) {
		// Bail early if textfield or missing custom values array.
		return;
	}

	$return             = '';
	$items_count        = 0;
	$term_count         = count( $custom_values );
	$selected_value     = explode( '+', $obj['selected_value'] ); // parse selected_value into array.
	$checkbox_limit     = isset( $obj['checkbox_limit'] ) && $field_type === 'checkbox' ? (int) $obj['checkbox_limit'] : false;
	$has_checkbox_limit = $checkbox_limit && $term_count > $checkbox_limit;

	$return .= apply_filters( 'alm_filters_container_open', ALMFilters::alm_filters_get_container( $id, $obj, 'open' ) );
	$return .= ALMFilters::alm_filters_display_toggle( $obj, 'before' );

	// Loop custom values.
	foreach ( $custom_values as $index => $v ) {
		$items_count++;
		$name          = wp_kses_post( $v['label'] );
		$slug          = esc_attr( $v['value'] );
		$nested        = isset( $v['nested'] ) && $v['nested'] ? true : false;
		$obj['id']     = $key . '-' . $field_type . '-' . $obj['count'];
		$fieldname_val = $key . '-' . $field_type . '-' . $obj['count'];
		$fieldname     = $field_type === 'radio' ? ' name="' . $fieldname_val . '"' : '';

		if ( $name === '' && $slug === '' ) {
			// Exit this iteration if name and slug are empty.
			continue;
		}

		// Querystring params.
		$selected    = '';
		$active      = '';
		$match_array = '';

		if ( $key === 'meta' && isset( $querystring[ $obj['meta_key'] ] ) ) {
			// Custom Fields.
			$match_array = explode( '+', $querystring[ $obj['meta_key'] ] );

		} elseif ( $key === 'taxonomy' && isset( $querystring[ alm_filters_add_underscore( $id ) . $obj['taxonomy'] ] ) ) {
			// Taxonomy.
			$match_array = explode( '+', $querystring[ alm_filters_add_underscore( $id ) . $obj['taxonomy'] ] );

		} else {
			// Everything else.
			if ( isset( $querystring[ $key ] ) ) {
				$match_array = explode( '+', $querystring[ $key ] );
			} else {
				// Selected Value match.
				if ( $field_type === 'checkbox' || $field_type === 'radio' || $field_type === 'select' ) {
					$match_array = $selected_value;
				}
			}
		}

		switch ( $field_type ) {
			case 'select':
			case 'select_multiple':
				if ( ! empty( $match_array ) ) {
					$selected = in_array( $slug, $match_array, true ) ? ' selected="selected"' : '';
				}
				$parent = $nested ? ' - ' : '';

				// Default Select Option.
				if ( $items_count === 1 && $obj['default_select_option'] ) {
					$filter_key            = alm_filters_get_filter_key( $obj );
					$default_select_option = apply_filters( 'alm_filters_' . $id . '_' . $filter_key . '_default_select_option', $obj['default_select_option'] );
					$return               .= '<option value="#"' . $selected . '>' . $default_select_option . '</option>';
				}

				$return .= '<option id="' . $field_type . '-' . $slug . '"' . $fieldname . ' value="' . esc_attr( $slug ) . '" data-name="' . $name . '"' . $selected . '>';
				$return .= esc_attr( $parent ) . esc_attr( $name );
				$return .= '</option>';

				break;

			default:
				$aria_checked     = 'aria-checked="false"';
				$parent           = $nested ? ' has_parent' : '';
				$field_level      = $nested ? ' field-child' : ' field-parent';
				$past_limit       = $has_checkbox_limit && (int) $index >= $checkbox_limit ? alm_filters_checkbox_limit_style() : '';
				$past_limit_class = $has_checkbox_limit && (int) $index >= $checkbox_limit ? alm_filters_checkbox_limit_class() : '';

				// Get active list item.
				if ( ! empty( $match_array ) ) {
					$active       = in_array( (string) $slug, $match_array, true ) ? ' active' : '';
					$aria_checked = in_array( (string) $slug, $match_array, true ) ? 'aria-checked="true"' : $aria_checked;
				}

				$return .= '<li class="alm-filter--' . $field_type . $field_level . ' field-' . $index . '' . $parent . $past_limit_class . '"' . $past_limit . '>';
				$return .= '<div class="alm-filter--link field-' . $field_type . ' field-' . $slug . $active . '"
					id="' . $field_type . '-' . sanitize_title( $slug ) . '-' . $obj['count'] . '"
					data-type="' . $field_type . '"
					data-value="' . $slug . '"
					role="' . $field_type . '"
					tabindex="0"
					' . $aria_checked . '>';
				$return .= $name;
				$return .= ALMFilters::$facets && $obj['show_count'] ? '<span class="alm-filter-counter"></span>' : '';
				$return .= '</div>';
				$return .= '</li>';
		}
	}

	$return .= ALMFilters::alm_filters_display_toggle( $obj, 'after' );
	$return .= apply_filters( 'alm_filters_container_close', ALMFilters::alm_filters_get_container( $id, $obj, 'close' ) );
	$return .= $has_checkbox_limit ? wp_kses_post( alm_filters_checkbox_limit_button( $obj, $checkbox_limit, $term_count ) ) : '';
	return $return;
}
