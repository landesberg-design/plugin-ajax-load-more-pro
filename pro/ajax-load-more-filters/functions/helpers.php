<?php


/**
 * Render filter label.
 *
 * @param $id string
 * @param $obj array
 * @since 1.8.4
 */
function alm_filters_display_label( $id = '', $obj = '' ) {

	if ( empty( $id ) || empty( $obj ) || !isset( $obj['label'] ) ) {
		return false; // Exit if empty.
	}
	$filter_key = alm_filters_get_filter_key( $obj );
	$value      = $obj['label'];

	if ( empty( $value ) && !has_filter( 'alm_filters_'. $id . '_' . $filter_key .'_label' ) ){
		return false; // Exit if title is empty && filter doesn't exist.
	}
	$output = apply_filters( 'alm_filters_'. $id . '_' . $filter_key .'_label', $value );
	return $output;
}

/**
 * Render filter title.
 *
 * @param array $id
 * @param array $obj
 * @param boolean $toggle
 * @since 1.0
 * @updated 1.10.1
 */
function alm_filters_display_title( $id = '', $obj = '', $toggle = false ) {

	if ( empty( $id ) || empty( $obj ) || ! isset( $obj['title'] ) ) {
		return false; // exit if empty.
	}

	$filter_key = alm_filters_get_filter_key( $obj );
	$value      = $obj['title'];

	if ( empty( $value ) && ! has_filter( 'alm_filters_' . $id . '_' . $filter_key . '_title' ) ) {
		return false; // Exit if title is empty && filter doesn't exist.
	}

	$toggle_opts = '';
	if ( $toggle ) {
		$toggle_opts = ' class="alm-filter--toggle" tabindex="0" aria-expanded="true" aria-controls="alm-filter-' . $filter_key . '-inner" role="button"';
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
 * @param array $options
 * @param array $obj
 * @since 1.0
 */
function alm_filters_display_description( $id = '', $obj = '' ) {

	if ( empty( $id ) || empty( $obj ) || ! isset( $obj['description'] ) ) {
		return false; // exit if empty.
	}

	$filter_key = alm_filters_get_filter_key( $obj );
	$value      = $obj['description'];

	if ( empty( $value ) && ! has_filter( 'alm_filters_' . $id . '_' . $filter_key . '_description' ) ) {
		return false; // Exit if description is empty && filter doesn't exist.
	}

	$output  = '<div class="alm-filter--description">';
	$output .= '<' . apply_filters( 'alm_filters_description_element', 'p' ) . ' id="alm-filter-' . $filter_key . '-description' .'">';
	$output .= htmlspecialchars_decode( apply_filters( 'alm_filters_' . $id . '_' . $filter_key . '_description', $value ) );
	$output .= '</' . apply_filters( 'alm_filters_description_element', 'p' ) . '>';
	$output .= '</div>';

	return $output;
}

/**
 * Get the key for a filter group.
 *
 * @param array $obj
 * @since 1.7.1
 */
function alm_filters_get_filter_key( $obj = '' ) {

	if ( empty( $obj ) || ! isset( $obj['key'] ) ) {
		return false; // Exit if empty.
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
 * @param string $slug The slug of the URL
 * @since 1.8.1
 */
function alm_filters_build_url( $obj, $slug ){
	if ( ! $obj || $obj['base_url'] === '' || ! $slug ) {
		return false;
	}
	$params = alm_filters_get_queryParam( $obj );
	if ( ! $params ) {
		return false;
	}
	$url = $obj['base_url'] . '?' . $params . '=' . $slug;
	return $url;
}



/**
 * Get URL Query param for link URLs (Radio/Checkbox).
 *
 * @param $obj array
 * @since 1.8.1
 */
function alm_filters_get_queryParam( $obj ) {
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
	return ( is_home() || is_front_page() || is_archive() ) ? true : false;
}

/**
 * Is the current page a front page or an archive, add _ to prevent redirects.
 *
 * @since 1.8.1
 */
function alm_filters_add_underscore() {
	return ( alm_filters_is_archive() ) ? '_' : '';
}

/**
 * Remove the underscore from the key.
 *
 * @param string $str
 * @since 1.8.1
 */
function alm_filters_remove_underscore( $str ) {
	$first = $str[0];
	return ( '_' === $first ) ? substr( $str, 1 ) : $str;
}

/**
 * Open the `inner` wrapper for each filter.
 *
 * @param array $obj
 * @param boolean $toggle
 * @since 1.10.1
 */
function alm_filters_open_filter_container( $obj = '', $toggle = false ) {

	if ( empty( $obj ) ) {
		return false; // exit if empty.
	}

	$key  = alm_filters_get_filter_key( $obj );
	$aria = ( $toggle ) ? ' aria-hidden="false" aria-labelledby="alm-filter-' . $key . '-title" id="alm-filter-' . $key . '-inner"' : '';

	return '<div class="alm-filter--inner"' . $aria . '>';
}

/**
 * Close the `inner` wrapper for each filter.
 *
 * @since 1.10.1
 */
function alm_filters_close_filter_container() {
	return '</div>';
}
