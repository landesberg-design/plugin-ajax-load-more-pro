<?php
/**
 * Parse the Default Values set inside the plugin.
 *
 * @since 1.13.0
 * @package ALMFilters
 */

// Only run if Filters are present.
if ( $alm_filters_array ) {

	// Loop all filters to get the default_values.
	foreach ( $alm_filters_array as $alm_filter ) {

		if ( isset( $alm_filter['default_value'] ) && ! empty( trim( $alm_filter['default_value'] ) ) ) {

			$alm_default_key = $alm_filter['key'];
			$value           = $alm_filter['default_value'];

			switch ( $alm_default_key ) {

				case '_author':
					$author = $author ? "$author, $value" : $value;
					break;

				case 'postType':
					$post_type = $post_type ? "$post_type, $value" : $value;
					break;

				case 'category':
					$category = $category ? "$category, $value" : $value;
					break;

				case 'category_and':
					$category__and = $category__and ? "$category__and, $value" : $value;
					break;

				case 'tag':
				case '_tag':
					$tag = $tag ? "$tag, $value" : $value;
					break;

				case 'tag_and':
					$tag__and = $tag__and ? "$tag__and, $value" : $value;
					break;

				case 'order':
					$order = $value;
					break;

				case 'orderby':
					$orderby = $value;
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
				case 's':
					$search = $value;
					break;

				case 'sort':
					$sortArray = explode( ':', $value ); // Convert value to array at colon.
					if ( count( $sortArray ) > 1 && count( $sortArray ) <= 3 ) { // Between 1 and 3
						$sortOrder   = $sortArray[0];
						$sortOrderby = $sortArray[1];

						if ( in_array( $sortOrderby, alm_filters_get_order_array() ) ) {
							$order   = $sortOrder;
							$orderby = $sortOrderby;
						} else {
							// Get meta order (`meta_value`, `meta_value_num`)
							$metaOrder = isset( $sortArray[2] ) ? $sortArray[2] : 'meta_value';
							$order     = $sortOrder;
							$orderby   = $metaOrder;
							$meta_key  = $sortOrderby;
						}
					}
					break;

				case 'taxonomy':
					// Taxonomy.
					$key                  = $alm_filter['taxonomy'];
					$default_tax_operator = '';

					if ( taxonomy_exists( $key ) ) {
						// Loop filters array to get the taxonomy operator.
						foreach ( $alm_filters_array as $item ) {
							if ( isset( $item['taxonomy'] ) && $item['taxonomy'] === $key ) {
								$default_tax_operator = isset( $item['taxonomy_operator'] ) ? $item['taxonomy_operator'] : 'IN';
							}
						}
						$taxonomy          = $taxonomy ? "$taxonomy:$key" : $key;
						$taxonomy_terms    = $taxonomy_terms ? "$taxonomy_terms:$value" : $value;
						$taxonomy_operator = $taxonomy_operator ? "$taxonomy_operator:$default_tax_operator" : $default_tax_operator;
					}
					break;

				case 'meta':
					// Custom Fields.
					$key                          = $alm_filter['meta_key'];
					$filter_session_meta_operator = $filter_session_meta_type = '';

					if ( $key ) {
						// Loop session array to get meta operator and type values.
						foreach ( $alm_filters_array as $item ) {
							if ( isset( $item['meta_key'] ) && $item['meta_key'] === $key ) {
								$filter_session_meta_operator = isset( $item['meta_operator'] ) ? $item['meta_operator'] : 'IN';
								$filter_session_meta_type     = isset( $item['meta_type'] ) ? $item['meta_type'] : 'CHAR';
							}
						}

						// Defaults.
						$filter_session_meta_operator = empty( $filter_session_meta_operator ) ? 'IN' : $filter_session_meta_operator;
						$filter_session_meta_type     = empty( $filter_session_meta_type ) ? 'CHAR' : $filter_session_meta_type;

						// Set meta query params.
						$meta_key     = $meta_key ? "$meta_key:$key" : $key;
						$meta_value   = $meta_value ? "$meta_value:$value" : $value;
						$meta_compare = $meta_compare ? "$meta_compare:$filter_session_meta_operator" : $filter_session_meta_operator;
						$meta_type    = $meta_type ? "$meta_type:$filter_session_meta_type" : $filter_session_meta_type;
					}
					break;

				default:
					break;

			}
		}
	}
}
