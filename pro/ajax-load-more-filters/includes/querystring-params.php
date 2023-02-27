<?php
/**
 * This file parses the browser querystring to set the proper query args.
 *
 * @since 1.0
 * @package ALMFilters
 */

// Get querystring array.
$alm_filters_querystring = ALMFilters::alm_filters_parse_url();

if ( $alm_filters_querystring ) {
	// Set initial Taxonomy and Meta Query variables.
	$filter_taxonomy_count            = 0;
	$filter_taxonomy                  = '';
	$filter_taxonomy_terms            = '';
	$filter_taxonomy_operator         = '';
	$filter_taxonomy_include_children = '';
	$filter_meta_count                = 0;
	$filter_meta_key                  = '';
	$filter_meta_value                = '';
	$filter_meta_compare              = '';
	$filter_meta_type                 = '';

	// Loop all querystrings.
	foreach ( $alm_filters_querystring as $key => $value ) {
		$alt_key = '';

		// Meta Query.
		if ( $alm_filters_meta_array && in_array( $key, $alm_filters_meta_array, true ) ) {
			$alt_key = $key;
			$key     = 'custom_field_query';
		}

		// Taxonomy Query.
		if ( $alm_filters_taxonomy_array && in_array( $key, $alm_filters_taxonomy_array, true ) ) {
			$alt_key = $key;
			$key     = 'taxonomy_query';
		}

		// Remove HTML tags from the querystring values.
		$value = htmlspecialchars( wp_strip_all_tags( $value ) );

		switch ( $key ) {
			case 'order':
				$order = str_replace( '+', ',', $value );
				break;

			case 'orderby':
				$orderby = str_replace( '+', ',', $value );
				break;

			case '_author':
				$author = str_replace( '+', ',', $value );
				break;

			case 'postType':
				$post_type = str_replace( '+', ',', $value );
				break;

			case 'category':
			case '_category':
				$category = str_replace( '+', ',', $value );
				break;

			case 'category_and':
				$category__and = str_replace( '+', ',', $value );
				break;

			case 'tag':
			case '_tag':
				$tag = str_replace( '+', ',', $value );
				break;

			case 'tag_and':
				$tag__and = str_replace( '+', ',', $value );
				break;

			case '_year':
				$year = $value;
				break;

			case '_month':
				$month = $value;
				break;

			case '_day':
				$day = $value;
				break;

			case 'search':
				$search = $value;
				break;

			case 's':
				$search = $value;
				break;

			case 'sort':
				// Sort order.
				$sortArray = explode( ':', $value ); // Convert value to array at colon.
				if ( count( $sortArray ) > 1 && count( $sortArray ) <= 3 ) { // Between 1 and 3.
					$sortOrder   = $sortArray[0];
					$sortOrderby = $sortArray[1];
					if ( in_array( $sortOrderby, alm_filters_get_order_array(), true ) ) {
						$order   = $sortOrder;
						$orderby = $sortOrderby;
					} else {
						// Get meta order (`meta_value`, `meta_value_num`).
						$metaOrder = isset( $sortArray[2] ) ? $sortArray[2] : 'meta_value';
						$order     = $sortOrder;
						$orderby   = $metaOrder;
						$meta_key  = $sortOrderby;
					}
				}

				break;

			case 'custom_field_query':
				// Meta Query.
				$filter_session_meta_operator = '';
				$filter_session_meta_type     = '';
				// Loop session array to get meta operator and type values.
				foreach ( $alm_filters_array as $item ) {
					if ( isset( $item['meta_key'] ) && $item['meta_key'] === $alt_key ) {
						$filter_session_meta_operator = isset( $item['meta_operator'] ) ? $item['meta_operator'] : 'IN';
						$filter_session_meta_type     = isset( $item['meta_type'] ) ? $item['meta_type'] : 'CHAR';
					}
				}

				$filter_meta_key     .= $filter_meta_count > 0 ? ':' . $alt_key : $alt_key;
				$filter_meta_value   .= $filter_meta_count > 0 ? ':' . str_replace( '+', ',', $value ) : str_replace( '+', ',', $value );
				$filter_meta_compare .= $filter_meta_count > 0 ? ':' . $filter_session_meta_operator : $filter_session_meta_operator;
				$filter_meta_type    .= $filter_meta_count > 0 ? ':' . $filter_session_meta_type : $filter_session_meta_type;
				$filter_meta_count++;

				break;

			case 'taxonomy_query':
				// Taxonomy.
				$key = alm_filters_is_archive() ? alm_filters_remove_underscore( $alt_key ) : $alt_key;
				if ( taxonomy_exists( $key ) ) {
					$filter_session_tax_operator         = '';
					$filter_session_tax_include_children = '';

					// Loop filters array to get the taxonomy operator.
					foreach ( $alm_filters_array as $item ) {
						if ( isset( $item['taxonomy'] ) && $item['taxonomy'] === $key ) {
							$filter_session_tax_operator         = isset( $item['taxonomy_operator'] ) ? $item['taxonomy_operator'] : 'IN';
							$filter_session_tax_include_children = isset( $item['taxonomy_include_children'] ) ? $item['taxonomy_include_children'] : 'true';
						}
					}
					$filter_taxonomy                  .= $filter_taxonomy_count > 0 ? ':' . $key : $key;
					$filter_taxonomy_terms            .= $filter_taxonomy_count > 0 ? ':' . str_replace( '+', ',', $value ) : str_replace( '+', ',', $value );
					$filter_taxonomy_operator         .= $filter_taxonomy_count > 0 ? ':' . $filter_session_tax_operator . '' : $filter_session_tax_operator;
					$filter_taxonomy_include_children .= $filter_taxonomy_count > 0 ? ':' . $filter_session_tax_include_children . '' : $filter_session_tax_include_children;
					$filter_taxonomy_count++;
				}

				break;

			case 'pg':
				$pg = $value;

				break;

			default:
				break;
		}
	}

	// Apply Taxonomies.
	if ( ! empty( $filter_taxonomy ) && ! empty( $filter_taxonomy_terms ) ) {
		// Append querystring taxonomy query params to existing taxonomy query.
		$taxonomy                  = $taxonomy ? $taxonomy . ':' . $filter_taxonomy : $filter_taxonomy;
		$taxonomy_terms            = $taxonomy_terms ? $taxonomy_terms . ':' . $filter_taxonomy_terms : $filter_taxonomy_terms;
		$taxonomy_operator         = $taxonomy_operator ? $taxonomy_operator . ':' . $filter_taxonomy_operator : $filter_taxonomy_operator;
		$taxonomy_include_children = $taxonomy_include_children ? $taxonomy_include_children . ':' . $filter_taxonomy_include_children : $filter_taxonomy_include_children;
	}

	// Apply Meta Queries.
	if ( ! empty( $filter_meta_key ) && isset( $filter_meta_value ) ) {
		// Append querystring meta query params to existing meta query.
		$meta_key     = $meta_key ? $meta_key . ':' . $filter_meta_key : $filter_meta_key;
		$meta_value   = $meta_value ? $meta_value . ':' . $filter_meta_value : $filter_meta_value;
		$meta_compare = $meta_compare ? $meta_compare . ':' . $filter_meta_compare : $filter_meta_compare;
		$meta_type    = $meta_type ? $meta_type . ':' . $filter_meta_type : $filter_meta_type;
	}
}
