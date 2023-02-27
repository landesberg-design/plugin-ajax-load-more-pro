<?php
/**
 * This file holds all functionality for the dynamic filter variables.
 *
 * @package ALMFilters
 */

/**
 * Parse dynamic variables in a filter config.
 *
 * @param string $key The filter key.
 * @param string $value The filter value.
 * @return string $value The updated value.
 */
function alm_filters_parse_dynamic_vars( $key, $value ) {

	// Archive `%archive%`.
	if ( strtolower( $value ) === '%archive%' && is_archive() ) {
		$obj = get_queried_object();

		// Date Query.
		if ( is_date() ) {
			if ( is_year() ) {
				$value = get_the_date( 'Y' );
			}
			if ( is_month() ) {
				$value = get_the_date( 'm' );
			}
			if ( is_day() ) {
				$value = get_the_date( 'd' );
			}
		}

		// Taxonomy, Tag, Category.
		if ( $key === 'taxonomy' || $key === 'tag' || $key === 'category' ) {
			if ( is_tax() || is_category() || is_tag() ) {
				$value = $obj->slug;
			}
		}
		if ( $key === 'tag__and' || $key === 'category__and' ) {
			$value = $obj->term_taxonomy_id;
		}

		// Author.
		if ( is_author() ) {
			$value = get_the_author_meta( 'ID' );
		}

		// Post Type.
		if ( is_post_type_archive() ) {
			if ( isset( $obj->name ) ) {
				$value = $obj->name;
			}
		}
	}

	// Time.
	if ( strtolower( $value ) === '%time%' ) {
		$value = time();
	}

	return $value;
}
