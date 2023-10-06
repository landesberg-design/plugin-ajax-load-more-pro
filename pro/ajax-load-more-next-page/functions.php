<?php
/**
 * Various helper functions for use with plugin.
 *
 * @package ALMNextPage
 */

/**
 * Remove Gutenberg block html comments.
 * Comments were causing issues with ACF blocks.
 *
 * @param string $content Content to search.
 * @return string         Modified content.
 */
function alm_nextpage_remove_block_comments( $content = '' ) {
	$content = str_replace( '<!-- wp:nextpage -->', '', $content );
	$content = str_replace( '<!-- /wp:nextpage -->', '', $content );
	return $content;
}

/**
 * Parse the ALM shortcode for query params.
 *
 * @param string $shortcode The shortcode.
 * @return array Array of parameters.
 */
function alm_nextpage_parse_shortcode_atts( $shortcode ) {
	$shortcode = str_replace( '[ajax_load_more ', '', $shortcode );
	$shortcode = str_replace( '/] ', '', $shortcode );
	$shortcode = str_replace( '] ', '', $shortcode );
	return alm_nextpage_shortcode_parse_atts( $shortcode );
}

/**
 * Parse the shortcode attributes.
 * Core WP shortcode_parse_atts doesn't work properly.
 *
 * @see https://developer.wordpress.org/reference/functions/shortcode_parse_atts/
 * @see https://stackoverflow.com/a/71785599/921927
 *
 * @param string $shortcode The shortcode.
 * @return array Array of attributes.
 */
function alm_nextpage_shortcode_parse_atts( $shortcode ) {
	$attributes = [];
	if ( preg_match_all( '/\w+\=\".*?\"/', $shortcode, $key_value_pairs ) ) {
		// Now split up the key value pairs.
		foreach ( $key_value_pairs[0] as $kvp ) {
			$kvp                    = str_replace( '"', '', $kvp );
			$pair                   = explode( '=', $kvp );
			$attributes[ $pair[0] ] = $pair[1];
		}
	}
	return $attributes;
}

/**
 * Pull query params from shortcode for querying auto implementation.
 *
 * @param array $params The current shortcode params.
 * @return array Array of query params.
 */
function alm_nextpage_parse_query_params( $params ) {
	if ( isset( $params['taxonomy'] ) && isset( $params['taxonomy_terms'] ) ) {
		$terms = trim( $params['taxonomy_terms'], ' ' );
		$terms = str_replace( ', ', ',', $terms );
		return [
			'tax'   => $params['taxonomy'],
			'terms' => explode( ',', $terms ),
		];
	} else {
		return [];
	}
}
