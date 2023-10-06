<?php
/**
 * This file holds the majority of functionality for the facet filtering.
 *
 * @since 2.0.0
 * @package ALMFilters
 *
 * What does this functionality do?
 * 1. Reads from an index created and stored in the options table.
 * 2. Matches returned query results (get_posts -1) to the items in the content index.
 * 3. Runs functionality to pull oand organize facets.
 * 4. Returns an array of objects containing the facets and counts.
 */

/*********** GET FACET RESULTS *********** */

/**
 * Get the ALM facets for this filter.
 *
 * @param  object $args     The query args.
 * @param  string $facet_id The facet ID to retrieve data from DB.
 * @return object           A JSON object of key/value pair facets.
 */
function alm_filters_get_facets( $args = [], $facet_id = '' ) {
	if ( ! $facet_id ) {
		return null;
	}

	// Supported query keys.
	$supported_keys = [ 'taxonomy', 'meta', 'category', 'category__and', 'tag', 'tag__and', 'author', 'year', 'month', 'day', 'post_type' ];

	/**
	 * Store facets in site transient to reduce query load.
	 *
	 * @see https://developer.wordpress.org/apis/transients/
	 */
	$alm_id         = array_key_exists( 'alm_id', $args ) ? $args['alm_id'] : '';
	$transient_name = alm_filters_facet_get_transient_name( $facet_id, $alm_id );
	$transient      = get_transient( $transient_name );
	if ( $transient ) {
		return $transient; // If found, return the transient data.
	}

	// Override ALM query $args for the facet query.
	$args['fields']         = 'ids'; // Return only post IDs.
	$args['posts_per_page'] = apply_filters( 'alm_filters_facets_posts_per_page', -1 ); // Get all posts.

	// Get all posts from the query.
	$posts = get_posts( apply_filters( 'alm_filters_facet_query_args_' . $facet_id, $args ) );

	// Get the facet index from the options table.
	$facet = get_option( ALM_FILTERS_FACET_PREFIX . $facet_id );
	$index = $facet ? unserialize( $facet ) : [];

	// Get the filter options from the options table.
	$filter  = get_option( ALM_FILTERS_PREFIX . $facet_id );
	$filter  = $filter ? unserialize( $filter ) : [];
	$filters = isset( $filter['filters'] ) ? $filter['filters'] : [];
	if ( ! $filters ) {
		return [];
	}

	$results = [];

	// Loop each filter and compare.
	foreach ( $filters as $filter ) {
		$field_type    = $filter['field_type'];
		$single_select = [ 'radio', 'select' ];
		$key           = $filter['key'];

		switch ( $key ) {
			case 'taxonomy':
				$slug                           = $filter['taxonomy'];
				$operator                       = $filter['taxonomy_operator'];
				$data                           = in_array( $field_type, $single_select, true ) ? alm_filters_single_select_facet_args( $key, $slug, $args, $posts ) : $posts;
				$results['taxonomies'][ $slug ] = alm_filters_compare_index( $data, $index, $slug, 'taxonomy' );
				break;

			case 'meta':
				$slug                     = $filter['meta_key'];
				$data                     = in_array( $field_type, $single_select, true ) ? alm_filters_single_select_facet_args( $key, $slug, $args, $posts ) : $posts;
				$results['meta'][ $slug ] = alm_filters_compare_index( $data, $index, $slug, 'meta' );
				break;

			default:
				$slug = $key;
				switch ( $key ) {
					case 'category':
						// Convert `category` to category_name for arg parsing.
						$slug = 'category_name';
						break;
				}
				if ( in_array( $key, $supported_keys, true ) ) {
					$data            = in_array( $field_type, $single_select, true ) ? alm_filters_single_select_facet_args( $key, $slug, $args, $posts ) : $posts;
					$results[ $key ] = alm_filters_compare_index( $data, $index, $key );
				}
				break;
		}
	}

	set_transient( $transient_name, $results, apply_filters( 'alm_filters_facets_transient_expiration', 7200 ) ); // Save results in transient for 2 hours.
	return $results;
}

/**
 * Run a separate `get_posts` query for each single select field type (Radio, Select).
 *
 * This function will only run if the filter is present in the existing query arags.
 * The idea is that if the filter is a single select then it needs to be excludes from the
 * query to get the true facets.
 *
 * @param  string $key   The filter key.
 * @param  string $slug  The filter slug (taxonomy, meta only).
 * @param  string $args  Array of query args.
 * @param  string $posts Current posts from query.
 * @return array         An array of post IDs.
 */
function alm_filters_single_select_facet_args( $key, $slug, $args, $posts ) {
	$found = false;

	// Taxonomy & Meta Query.
	if ( $key === 'taxonomy' || $key === 'meta' ) {
		$query_var = $key === 'taxonomy' ? 'tax_query' : 'meta_query';
		$var       = $key === 'taxonomy' ? 'taxonomy' : 'key';

		$nested_query = isset( $args[ $query_var ] ) && is_array( $args[ $query_var ] ) ? $args[ $query_var ] : '';
		if ( ! $nested_query ) {
			return $posts;
		}

		// Loop each nested_query and remove matched filter from args if found.
		foreach ( $nested_query as $index => $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			if ( $item[ $var ] === $slug ) {
				// Matched - remove query arg.
				unset( $args[ $query_var ][ $index ] );
				$found = true;
				break; // Exit foreach loop.
			}
		}
	} else {
		// Standard args.
		if ( isset( $args[ $slug ] ) ) {
			$found = true;
			unset( $args[ $slug ] ); // Matched - remove query arg.
		}
	}

	return $found ? get_posts( $args ) : $posts;
}

/**
 * Search post index for matching filter terms.
 * This function loops all posts in the index and looks for matching post IDs in the returned get_posts query.
 * If found, the values and counts are returned in an array.
 *
 * @param  array       $posts The array of posts from the current query.
 * @param  array       $index The facet index of posts.
 * @param  string      $slug  The taxonomy/meta slug.
 * @param  string|null $type  The query type.
 * @return array         An array of matched results.
 */
function alm_filters_compare_index( $posts = [], $index = [], $slug = '', $type = null ) {
	$results = [];
	if ( ! $posts || ! $index ) {
		return [];
	}

	// Loop all items in the index.
	foreach ( $index as $item ) {
		// Find posts in index by ID.
		if ( in_array( $item['id'], $posts, true ) ) {

			if ( $type ) { // Nested query args.
				$value = isset( $item[ $type ] ) && isset( $item[ $type ][ $slug ] ) ? $item[ $type ][ $slug ] : '';
			} else { // Standard query args.
				$value = isset( $item[ $slug ] ) ? $item[ $slug ] : '';
			}

			// Split value into array at comma.
			$value = explode( ',', $value );

			// Create an array entry for each key and value.
			foreach ( $value as $term ) {
				if ( ! empty( $term ) ) {
					$results[] = trim( $term );
				}
			}
		}
	}
	return ! empty( $results ) ? alm_filters_organize_results( $results, true ) : [];
}

/**
 * Count and remove duplicate results of an array.
 *
 * @param  array   $array The array to organize.
 * @param  boolean $flat  Is this a flat array and not multidimensional.
 * @return array          The modified array.
 */
function alm_filters_organize_results( $array, $flat = false ) {
	$results = [];
	if ( ! $array ) {
		return [];
	}

	if ( ! $flat ) {
		foreach ( $array as $key => $item ) {
			$results[ $key ] = array_count_values( $array[ $key ] );
		}
	} else {
		$results = array_count_values( $array );
	}
	return $results;
}

/******************* - *********************/
/*********** BUILD FACET INDEX *********** */
/******************* - *********************/

/**
 * Save the facet index.
 *
 * @param  array $filter An array containing the filter options.
 * @return void
 */
function alm_filters_save_facet( $filter ) {
	if ( ! $filter ) {
		return;
	}

	// Create index.
	$index = alm_filters_build_facet_index( $filter );

	// Update/Create facet option on success.
	update_option( ALM_FILTERS_FACET_PREFIX . $filter['id'], serialize( $index ) );
}

/**
 * Build the facet index.
 * - The index will contain an array of post IDs and arrays of taxonomy and meta values.
 * - The index is then compared to the facets when they are returned from the database.
 * - The facet index is stored in the options tables. e.g. `alm_facet_{id}`
 *
 * @param  array $filter An array containing the filter options.
 * @return array         The facet index as an array.
 */
function alm_filters_build_facet_index( $filter ) {
	$index      = [];
	$post_types = isset( $filter['facets_post_types'] ) ? $filter['facets_post_types'] : [ 'post' ];
	$args       = array(
		'post_type'      => $post_types,
		'post_status'    => 'publish',
		'posts_per_page' => -1,
	);

	// Set `inherit` post_status for attachments.
	if ( in_array( 'attachment', $post_types, true ) ) {
		$args['post_status'] = [ 'publish', 'inherit' ];
	}

	$filters = isset( $filter ) && isset( $filter['filters'] ) ? $filter['filters'] : [];
	$facets  = alm_filters_pluck_facet_keys( $filters );

	// WP_Query.
	$query = new WP_Query( $args );
	while ( $query->have_posts() ) :
		$query->the_post();
		$post_id = get_the_ID();
		$index[] = alm_filters_build_post_index( $post_id, $facets );
	endwhile;
	wp_reset_postdata();

	return $index;
}

/**
 * Construct an index for an individual post by ID.
 *
 * @param  string $post_id The post ID.
 * @param  array  $facets  The post facets.
 * @return array           The facet index for an individual post.
 */
function alm_filters_build_post_index( $post_id, $facets ) {
	if ( ! $post_id || ! $facets ) {
		return [];
	}

	$cats = alm_filters_facets_get_cats_tags( $post_id, 'category' ); // Get categories.
	$tags = alm_filters_facets_get_cats_tags( $post_id, 'post_tag' ); // Get tags.
	$date = alm_filters_get_facet_dates( $post_id ); // Get date.

	$index = [
		'id'            => $post_id,
		'category'      => $cats['slugs'],
		'category__and' => $cats['ids'],
		'tag'           => $tags['slugs'],
		'tag__and'      => $tags['ids'],
		'taxonomy'      => alm_filters_get_facet_taxonomies( $post_id, $facets['taxonomies'] ),
		'meta'          => alm_filters_get_facet_meta( $post_id, $facets['meta'] ),
		'author'        => alm_filters_get_facet_author( $post_id ),
		'year'          => $date['year'],
		'month'         => $date['month'],
		'day'           => $date['day'],
		'post_type'     => get_post_type( $post_id ),
	];

	return $index;
}

/**
 * Pull the possible facet keys from the filters object and return them as an array.
 *
 * @param  array $filters The array of filters to loop.
 * @return array           A constructed array of possible facets.
 */
function alm_filters_pluck_facet_keys( $filters ) {
	$array = [
		'taxonomies'    => [],
		'meta'          => [],
		'category'      => false,
		'category__and' => false,
		'tag'           => false,
		'tag_and'       => false,
		'author'        => false,
		'year'          => false,
		'month'         => false,
		'day'           => false,
		'post_type'     => false,
	];

	// phpcs:disable
	/*
	$facets = [
		'taxonomies' => [ 'actor', 'post_tag', 'movie_type' ],
		'meta'       => [ 'test_cf' ],
		'author'     => true
	];
	*/
	// phpcs:enable

	foreach ( $filters as $filter ) {
		$key = $filter['key'];

		if ( $key === 'taxonomy' ) {
			$array['taxonomies'][] = $filter['taxonomy'];
		}

		if ( $key === 'category' ) {
			$array['category'] = true;
		}

		if ( $key === 'category__and' ) {
			$array['category__and'] = true;
		}

		if ( $key === 'tag' ) {
			$array['tag'] = true;
		}

		if ( $key === 'meta' ) {
			$array['meta'][] = $filter['meta_key'];
		}

		if ( $key === 'author' ) {
			$array['author'] = true;
		}

		if ( $key === 'year' ) {
			$array['year'] = true;
		}

		if ( $key === 'month' ) {
			$array['month'] = true;
		}

		if ( $key === 'day' ) {
			$array['day'] = true;
		}
	}
	return $array;
}

/**
 * Build the facet category & tag results.
 * Note: This function is used to generate the index.
 *
 * @param  int    $id  The post ID.
 * @param  string $tax The taxonomy slug.
 * @return array       An array of slugs and ids.
 */
function alm_filters_facets_get_cats_tags( $id = 0, $tax = 'category' ) {
	$terms = get_the_terms( $id, $tax );
	$slugs = join( ', ', wp_list_pluck( $terms, 'slug' ) );
	$ids   = join( ', ', wp_list_pluck( $terms, 'term_taxonomy_id' ) );

	return [
		'slugs' => $slugs,
		'ids'   => $ids,
	];
}

/**
 * Build the facet taxonomy results.
 * Note: This function is used to generate the index.
 *
 * @param  number  $id         The post ID.
 * @param  array   $taxonomies The array of specific taxonomies to loop over.
 * @param  boolean $flat       Is this a flat array and not multidimensional.
 * @return string|array        The taxonomy facets as an array or string.
 */
function alm_filters_get_facet_taxonomies( $id = 0, $taxonomies = [], $flat = false ) {
	if ( ! $taxonomies ) {
		return [];
	}

	if ( $flat ) {
		$terms = get_the_terms( $id, $taxonomies );
		$terms = join( ', ', wp_list_pluck( $terms, 'slug' ) );
		return $terms;

	} else {
		// Taxonomies.
		foreach ( $taxonomies as $tax ) {
			$terms          = get_the_terms( $id, $tax );
			$terms          = join( ', ', wp_list_pluck( $terms, 'slug' ) );
			$facets[ $tax ] = $terms;
		}
		return $facets;
	}
}

/**
 * Build the facet custom field results.
 * Note: This function is used to generate the index.
 *
 * @param  number $id    The post ID.
 * @param  array  $meta  The array of specific custom fields to loop over.
 * @return array         The custom field facets.
 */
function alm_filters_get_facet_meta( $id = 0, $meta = [] ) {
	if ( ! $meta ) {
		return [];
	}

	$facets = [];
	foreach ( $meta as $field ) {
		$value            = get_post_meta( $id, $field, true );
		$facets[ $field ] = is_array( $value ) ? implode( ',', $value ) : $value;
	}
	return $facets;
}

/**
 * Build the facet results for post authors.
 * Note: This function is used to generate the index.
 *
 * @param  number $id The post ID.
 * @return string     The author ID.
 */
function alm_filters_get_facet_author( $id = 0 ) {
	if ( ! $id ) {
		return '';
	}
	$author_id = get_post_field( 'post_author', $id );
	return $author_id;
}

/**
 * Build the facet results for post dates.
 * Note: This function is used to generate the index.
 *
 * @param  number $id The post ID.
 * @return array      The year, month and day as an array.
 */
function alm_filters_get_facet_dates( $id = 0 ) {
	if ( ! $id ) {
		return '';
	}
	$date = get_the_date( 'Y,m,d', $id );
	$date = explode( ',', $date );

	$facets = [
		'year'  => isset( $date[0] ) ? $date[0] : null,
		'month' => isset( $date[1] ) ? $date[1] : null,
		'day'   => isset( $date[2] ) ? $date[2] : null,
	];

	return $facets;
}

/**
 * Delete facet transients by filter ID.
 *
 * @param string $filter_id The filter ID.
 * @param string $prefix    The transient prefix.
 * @return void
 */
function alm_filters_delete_facet_transients( $filter_id, $prefix = ALM_FILTERS_FACET_PREFIX ) {
	if ( ! $filter_id ) {
		return;
	}

	global $wpdb;
	$default = '_transient_';
	$prefix  = esc_sql( $default . ALM_FILTERS_FACET_PREFIX . $filter_id ); // e.g. _transient_alm_facet_filter_actors.
	$options = $wpdb->options;
	$t       = esc_sql( "$prefix%" );

	// Get all transients that match.
	$sql        = $wpdb->prepare( "SELECT option_name FROM $options WHERE option_name LIKE '%s'", $t ); // phpcs:ignore
	$transients = $wpdb->get_col( $sql ); // phpcs:ignore

	if ( $transients ) {
		foreach ( $transients as $transient ) {
			$name = str_replace( $default, '', $transient ); // Replace `_transient_` from returned transient.
			delete_transient( $name ); // Delete the transient.
		}
	}
}

/**
 * Determine if facets are "true" in filter.
 *
 * @param string $target The filter target.
 * @return boolean       Does the filter contain facets.
 */
function alm_filters_has_facets( $target ) {
	if ( ! $target ) {
		return false;
	}

	$filter = ALMFilters::alm_filters_get_filter_by_id( $target );
	return $filter && isset( $filter['facets'] ) && $filter['facets'] ? true : false;
}

/**
 * Parse the current URL to pluck query params.
 *
 * @return array Array of query params.
 */
function alm_filters_facet_get_querystring() {
	$params  = filter_input_array( INPUT_GET, @FILTER_SANITIZE_STRING ); // phpcs:ignore
	$is_ajax = isset( $params ) && isset( $params['facets'] );
	if ( $is_ajax ) {
		// Ajax request.
		$parts = wp_parse_url( $_SERVER['HTTP_REFERER'] );
		$query = isset( $parts['query'] ) ? $parts['query'] : '';
		parse_str( $query, $params );
		return $params;
	} else {
		// Serverside request (Preloaded).
		parse_str( $_SERVER['QUERY_STRING'], $params );
		return $params;
	}
}

/**
 * Build and return a transient name based on query arg params.
 *
 * @param string $id     The facet ID.
 * @param string $alm_id The Ajax Load More ID.
 * @return string        The generated transient name as a string from URL.
 */
function alm_filters_facet_get_transient_name( $id = '', $alm_id = '' ) {
	if ( empty( $id ) || empty( $alm_id ) ) {
		// Bail early if empty.
		return '';
	}

	// Parse querystring into array and then into a string.
	$params = implode( '-', alm_filters_facet_get_querystring() );

	// Create unique MD5 hash from params.
	$hash = md5( $alm_id . $params );

	// Return the unique transient name.
	// e.g. alm_facet_filter_{id}_{alm_id}_d41d8cd98f00b204e980099.
	return strtolower( ALM_FILTERS_FACET_PREFIX . $id . '_' . $hash );
}

/******************* - *********************/
/************* TEST INDEX ******************/
/******************* - *********************/

/**
 * Build a test index.
 */
function alm_filters_test_index() {
	$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
	$args  = array(
		'post_type'      => array( 'movie' ),
		'post_status'    => 'publish',
		'posts_per_page' => -1,
	);

	$array  = [];
	$facets = [
		'taxonomies' => [ 'actor', 'post_tag', 'movie_type' ],
		'meta'       => [ 'test_cf' ],
		'author'     => true,
	];

	// WP_Query.
	$query = new WP_Query( $args );
	while ( $query->have_posts() ) :
		$query->the_post();
		$post_id = get_the_ID();
		$array[] = [
			'id'       => $post_id,
			'taxonomy' => alm_filters_get_facet_taxonomies( $post_id, $facets['taxonomies'] ),
			'meta'     => alm_filters_get_facet_meta( $post_id, $facets['meta'] ),
			'author'   => alm_filters_get_facet_author( $post_id ),
		];
	endwhile;
	wp_reset_postdata();

	return $array;
}
