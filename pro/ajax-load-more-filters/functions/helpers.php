<?php
/**
 * A file full of plugin helpers.
 *
 * @package ALMFilters
 */

/**
 * Render Reset/Clear Button.
 *
 * @param object $options The filter config.
 * @param object $obj The filter object.
 * @return string $output
 * @since 1.11.0
 */
function alm_filters_render_controls( $options, $obj ) {
	$output     = '';
	$reset_btn  = '';
	$submit_btn = '';

	// Reset Button.
	if ( $options['reset_button'] && ! empty( $options['reset_button_label'] ) ) {
		$has_qs    = ( $_GET ) ? true : false; // phpcs:ignore
		$classname = apply_filters( 'alm_filters_reset_button_class', 'alm-filters--reset-button' );
		$btn_class = ( $has_qs ) ? $classname : $classname . ' hidden';

		$reset_btn .= '<div class="alm-filters--reset">';
		$reset_btn .= '<button type="reset" id="alm-filters-reset-button" class="' . $btn_class . '"><span>' . $options['reset_button_label'] . '</span></button>';
		$reset_btn .= '</div>';
	}

	// Submit Button.
	$hide_submit = ( $obj['count'] === '1' && ! empty( $obj['button_label'] ) && 'text' === $obj['field_type'] ) ? true : false; // Hide Submit button if count is 1 and field type is textfield.
	if ( 'button' === $options['style'] && ! $hide_submit ) {
		$submit_btn .= '<div class="alm-filters--submit">';
		$submit_btn .= '<button type="button" class="alm-filters--button"><span>' . $options['button_text'] . '</span></button>';
		$submit_btn .= '</div>';
	}

	// Build output.
	if ( ! empty( $reset_btn ) || ! empty( $submit_btn ) ) {
		$output .= '<div class="alm-filters--controls">';
		$output .= $reset_btn;
		$output .= $submit_btn;
		$output .= '</div>';
	}

	return $output;
}

/**
 * Render filter label.
 *
 * @param string $id     The filter ID.
 * @param array  $obj    The filter array/object.
 * @param string $target THe target HTML element.
 * @since 1.8.4
 */
function alm_filters_render_label( $id = '', $obj = '', $target = '' ) {

	if ( empty( $id ) || empty( $obj ) || empty( $target ) || ! isset( $obj['label'] ) ) {
		return;
	}

	$filter_key = alm_filters_get_filter_key( $obj );
	$value      = $obj['label'];

	if ( empty( $value ) && ! has_filter( 'alm_filters_' . $id . '_' . $filter_key . '_label' ) ) {
		return; // Bail early if title is empty && filter doesn't exist.
	}

	$output = '<label for="' . $target . '">' . apply_filters( 'alm_filters_' . $id . '_' . $filter_key . '_label', $value ) . '</label>';
	return $output;
}

/**
 * Render filter title.
 *
 * @since 1.0
 * @param string  $id     The filter ID.
 * @param array   $obj    The filter array/object.
 * @param boolean $toggle Toggle or not.
 * @param boolean $status The status of the toggle.
 * @return string         Raw HTML output.
 */
function alm_filters_display_title( $id = '', $obj = '', $toggle = false, $status = 'expanded' ) {
	if ( empty( $id ) || empty( $obj ) || ! isset( $obj['title'] ) ) {
		return false; // Bail early if empty.
	}

	$filter_key = alm_filters_get_filter_key( $obj );
	$value      = $obj['title'];

	if ( empty( $value ) && ! has_filter( 'alm_filters_' . $id . '_' . $filter_key . '_title' ) ) {
		return false; // Bail early if title is empty && filter doesn't exist.
	}

	$aria_expanded = ( 'expanded' === $status ) ? 'true' : 'false';

	$toggle_opts = '';
	if ( $toggle ) {
		$toggle_opts = ' class="alm-filter--toggle" tabindex="0" aria-expanded="' . $aria_expanded . '" aria-controls="alm-filter-' . $filter_key . '-inner" role="button"';
	}

	$output  = '<div class="alm-filter--title">';
	$output .= '<' . apply_filters( 'alm_filters_title_element', 'h3' ) . ' id="alm-filter-' . $filter_key . '-title" ' . $toggle_opts . '>';
	$output .= apply_filters( 'alm_filters_' . $id . '_' . $filter_key . '_title', $value );
	$output .= '</' . apply_filters( 'alm_filters_title_element', 'h3' ) . '>';
	$output .= '</div>';

	return $output;
}

/**
 * Render filter description.
 *
 * @since 1.0
 * @param string $id  Filter ID.
 * @param array  $obj Filter array.
 * @return string     Raw HTML output.
 */
function alm_filters_display_description( $id = '', $obj = '' ) {
	if ( empty( $id ) || empty( $obj ) || ! isset( $obj['description'] ) ) {
		return false; // Bail early if empty.
	}

	$filter_key = alm_filters_get_filter_key( $obj );
	$value      = $obj['description'];

	if ( empty( $value ) && ! has_filter( 'alm_filters_' . $id . '_' . $filter_key . '_description' ) ) {
		return false; // Bail early if description is empty && filter doesn't exist.
	}

	$output  = '<div class="alm-filter--description">';
	$output .= '<' . apply_filters( 'alm_filters_description_element', 'p' ) . ' id="alm-filter-' . $filter_key . '-description' . '">'; // phpcs:ignore
	$output .= htmlspecialchars_decode( apply_filters( 'alm_filters_' . $id . '_' . $filter_key . '_description', $value ) );
	$output .= '</' . apply_filters( 'alm_filters_description_element', 'p' ) . '>';
	$output .= '</div>';

	return $output;
}

/**
 * Get the key for a filter group.
 *
 * @since 1.7.1
 * @param array $obj Filter array.
 * @return string    The key to locate.
 */
function alm_filters_get_filter_key( $obj = [] ) {
	if ( empty( $obj ) || ! isset( $obj['key'] ) ) {
		return false; // Bail early if empty.
	}

	$key = $obj['key'];
	// Set `$key` to taxonomy/meta_key	value for core filters.
	$key = ( $obj['key'] === 'taxonomy' ) ? $obj['taxonomy'] : $obj['key']; // Convert $key to $taxonomy value.
	$key = ( $obj['key'] === 'meta' ) ? $obj['meta_key'] : $key; // Convert $key to $meta_key value.

	return $key;
}

/**
 * Get URL Query param for link URLs (Radio/Checkbox).
 *
 * @since 1.8.1
 * @param array  $obj  The filters array.
 * @param string $slug The slug of the URL.
 * @return string      The final URL.
 */
function alm_filters_build_url( $obj, $slug ) {
	if ( ! $obj || $obj['base_url'] === '' || ! $slug ) {
		return false;
	}
	$params = alm_filters_get_query_param( $obj );
	if ( ! $params ) {
		return false;
	}
	$url = $obj['base_url'] . '?' . $params . '=' . $slug;
	return $url;
}

/**
 * Get URL Query param for link URLs (Radio/Checkbox).
 *
 * @since 1.8.1
 * @param array $obj The filters array.
 */
function alm_filters_get_query_param( $obj ) {
	if ( ! $obj ) {
		return false;
	}

	if ( 'taxonomy' === $obj['key'] ) {
		$param = ( alm_filters_is_archive() ) ? '_' . $obj['taxonomy'] : $obj['taxonomy'];
	} elseif ( 'meta' === $obj['key'] ) {
		$param = $obj['meta_key'];
	} else {
		$param = $obj['key'];
	}

	return $param;
}

/**
 * Is the current page a front page or an archive, add _ to prevent redirects.
 *
 * @since 1.8.1
 */
function alm_filters_is_archive() {
	return is_home() || is_front_page() || is_archive() ? true : false;
}

/**
 * Is the current page a front page or an archive, add _ to prevent redirects.
 *
 * @since 1.8.1
 */
function alm_filters_add_underscore() {
	return alm_filters_is_archive() ? '_' : '';
}

/**
 * Remove the underscore from the key.
 *
 * @param string $str The string to search.
 * @since 1.8.1
 */
function alm_filters_remove_underscore( $str ) {
	$first = $str[0];
	return $first === '_' ? substr( $str, 1 ) : $str;
}

/**
 * Open the `inner` wrapper for each filter.
 *
 * @param array   $obj    The filters array object.
 * @param boolean $toggle To toggle or not.
 * @param string  $status The current section status.
 * @return string         Raw HTML output.
 * @since 1.10.1
 */
function alm_filters_open_filter_container( $obj = '', $toggle = false, $status = 'expanded' ) {
	if ( empty( $obj ) ) {
		// Bail early if empty.
		return;
	}

	$aria_hidden = ( 'expanded' === $status ) ? 'false' : 'true';
	$style       = ( 'collapsed' === $status ) ? ' style="display: none;"' : '';

	$key  = alm_filters_get_filter_key( $obj );
	$aria = $toggle ? ' aria-hidden="' . $aria_hidden . '" aria-labelledby="alm-filter-' . $key . '-title" id="alm-filter-' . $key . '-inner"' : '';

	return '<div class="alm-filter--inner"' . $aria . $style . '>';
}

/**
 * Close the `inner` wrapper for each filter.
 *
 * @since 1.10.1
 */
function alm_filters_close_filter_container() {
	return '</div>';
}

/**
 * Render the text for `show_count`.
 *
 * @param boolean $show_count Should we display the count.
 * @param object  $item       The current filter item.
 * @param boolean $html       Render html or just a count.
 * @return string|mixed       Raw HTML output.
 * @since 1.11.0
 */
function alm_filters_build_count( $show_count, $item, $html ) {
	if ( ! isset( $item->count ) || ! isset( $show_count ) || ! $show_count ) {
		return;
	}
	$title = apply_filters( 'alm_filters_show_count_title', $item->count . __( ' results for ', 'ajax-load-more-filters' ) . $item->name, $item );
	$text  = apply_filters( 'alm_filters_show_count_display', $item->count, $item->count );
	return $html ? ' <span class="alm-filter-count" title="' . $title . '">' . $text . '</span>' : $text;
}

/**
 * The possible values for the sort order.
 */
function alm_filters_get_order_array() {
	return array(
		'id',
		'author',
		'title',
		'name',
		'type',
		'date',
		'modified',
		'parent',
		'rand',
		'relevance',
		'menu_order',
		'post__in',
		'post__name_in',
		'post_parent__in',
	);
}

/**
 * Get all the facet post types as an array.
 *
 * @param  array $facets The current facets from options table.
 * @return array         An array of post types and facet IDs.
 */
function alm_filters_facet_get_post_types( $facets ) {
	if ( ! $facets ) {
		return '';
	}

	$results = [];
	foreach ( $facets as $facet ) { // Loop each facet.
		$id     = str_replace( ALM_FILTERS_FACET_PREFIX, '', $facet );
		$filter = unserialize( get_option( ALM_FILTERS_PREFIX . $id ) );
		$types  = $filter && isset( $filter['facets_post_types'] ) ? $filter['facets_post_types'] : [];
		if ( $types ) {
			foreach ( $types as $type ) {
				$results[] = array(
					'post_type' => $type,
					'id'        => $id,
				);
			}
		}
	}

	return $results;
}
