<?php
/**
 * Params in this file are included at /ajax-load-more/core/classes/class.alm-shortcode.php
 * Get filters and operators and over write shortcode parameters based on filter state.
 *
 * @package ALMFilters
 * phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
 */

$filters_target = isset( $target ) ? preg_replace( '/\s/', '', $target ) : ''; // Get the target parameters.
$filters_target = $filters_target ? explode( ',', $filters_target ) : $filters_target; // Convert target to array.

// Get array of Meta Keys and Taxonomies of the current filters.
$alm_filters_meta_array     = ALMFilters::alm_filters_get_query_keys( $filters_target, 'meta_key' );
$alm_filters_taxonomy_array = ALMFilters::alm_filters_get_query_keys( $filters_target, 'taxonomy' );

// Get array of all Ajax Load More filters.
$alm_filters_array = ALMFilters::alm_filters_get_all_filters( $filters_target );

require 'default-value-params.php'; // Get default values.
require 'querystring-params.php'; // Parse querystring params.
