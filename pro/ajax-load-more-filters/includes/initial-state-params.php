<?php
/**
 * Params in this file are included at /ajax-load-more/core/classes/class.alm-shortcode
 * Get filters and operators and over write shortcode parameters based on filter state.
 *
 * @package ALMFilters
 */

// Get the target parameters.
$filters_target = isset( $target ) ? preg_replace( '/\s/', '', $target ) : '';
$filters_target = $filters_target ? explode( ',', $filters_target ) : $filters_target; // Convert target to array.

// Get array of Meta Keys for the current filters.
$meta_key_array = ALMFilters::alm_filters_get_meta_keys( $filters_target );

// Get array of all Ajax Load More filters.
$alm_filters_array = ALMFilters::alm_filters_get_all_filters( $filters_target );

// Get default values.
require 'default-value-params.php';

// Parse querystring params.
require 'querystring-params.php';
