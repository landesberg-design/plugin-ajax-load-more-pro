<?php
/**
 * Functions related to term listings.
 *
 * @package ALMFilters
 */

/**
 * Render taxonomy terms (cat, tag, custom tax).
 *
 * @param array  $obj         Filter object array.
 * @param string $querystring The current querystring.
 * @param string $id          The filters ID.
 * @return string             Raw HTML output.
 * @since 1.0
 */
function alm_filters_list_terms( $obj, $querystring, $id ) {
	$return         = '';
	$items          = [];
	$items_count    = 0;
	$field_type     = $obj['field_type'];
	$exclude        = explode( ',', $obj['exclude'] ); // Convert excludes into array.
	$selected_value = explode( '+', $obj['selected_value'] ); // Convert selected_value into array.
	$key            = $obj['key'];
	$match_key      = $key;
	$checkbox_limit = isset( $obj['checkbox_limit'] ) && $field_type === 'checkbox' ? (int) $obj['checkbox_limit'] : false;

	// Author.
	if ( $key === '_author' && isset( $obj['author_role'] ) ) {
		$author_args = [
			'role'    => $obj['author_role'],
			'order'   => 'DESC',
			'exclude' => $exclude,
			'orderby' => 'login',
		];

		// Author $args core filter.
		$author_args = apply_filters( 'alm_filters_' . $id . '_author_args', $author_args );

		$authors = get_users( $author_args );
		$terms   = [];
		if ( $authors ) {
			$terms = [];
			foreach ( $authors as $author ) {
				$terms[] = [
					'term_id' => $author->ID,
					'slug'    => $author->ID,
					'name'    => $author->display_name,
				];
			}
			// Convert array into stdClass object.
			$terms = json_decode( wp_json_encode( $terms ) );
		}
	}

	// Category.
	if ( $key === 'category' || $key === 'category_and' ) {
		$cat_args = [
			'order'      => 'ASC',
			'orderby'    => 'name',
			'exclude'    => $exclude,
			'hide_empty' => true,
		];

		$cat_args             = apply_filters( 'alm_filters_' . $id . '_category_args', $cat_args ); // Category $args core filter.
		$cat_args['taxonomy'] = 'category'; // Set taxonomy.
		$parent_term          = alm_filters_get_parent_term( $cat_args ); // Get parent.
		$terms                = alm_filters_get_taxonomy_hierarchy( $cat_args, $parent_term ); // Get terms.
	}

	// Tag.
	if ( $key === '_tag' || $key === 'tag' || $key === 'tag_and' ) {
		$tag_args = [
			'order'      => 'ASC',
			'orderby'    => 'name',
			'exclude'    => $exclude,
			'hide_empty' => true,
		];

		$tag_args             = apply_filters( 'alm_filters_' . $id . '_post_tag_args', $tag_args ); // Tag $args core filter.
		$tag_args['taxonomy'] = 'post_tag'; // Set Taxonomy.
		$parent_term          = alm_filters_get_parent_term( $tag_args ); // Get parent.
		$terms                = alm_filters_get_taxonomy_hierarchy( $tag_args, $parent_term ); // Get terms.
	}

	// Taxonomy.
	if ( $key === 'taxonomy' ) {
		$match_key = alm_filters_add_underscore() . '' . $obj['taxonomy']; // Set $match_key to taxonomy slug.
		$tax_args  = [
			'order'      => 'ASC',
			'orderby'    => 'name',
			'exclude'    => $exclude,
			'hide_empty' => true,
		];

		$tax_args             = apply_filters( 'alm_filters_' . $id . '_' . $obj['taxonomy'] . '_args', $tax_args ); // Taxonomy $args core filter.
		$tax_args['taxonomy'] = $obj['taxonomy']; // Set taxonomy.
		$parent_term          = alm_filters_get_parent_term( $tax_args ); // Get parent.
		$terms                = alm_filters_get_taxonomy_hierarchy( $tax_args, $parent_term ); // Get terms.
	}

	// Querystring params.
	$selected    = '';
	$active      = '';
	$match_array = '';

	$term_count         = count( $terms );
	$has_checkbox_limit = $checkbox_limit && $term_count > $checkbox_limit;

	if ( isset( $querystring[ $match_key ] ) ) {
		// Querystring match.
		$match_array = explode( '+', $querystring[ $match_key ] );

	} else {
		// Selected Value match.
		if ( $field_type === 'checkbox' || $field_type === 'radio' || $field_type === 'select' ) {
			$match_array = $selected_value;
		}
	}

	if ( isset( $terms ) && $terms ) {
		$return .= apply_filters( 'alm_filters_container_open', ALMFilters::alm_filters_get_container( $id, $obj, 'open' ) );
		$return .= ALMFilters::alm_filters_display_toggle( $obj, 'before' );

		switch ( $field_type ) {
			// Select.
			case 'select':
			case 'select_multiple':
				// Loop each term and build an array of terms.
				$items = [];
				foreach ( $terms as $term ) {
					$term = (object) $term;

					// Build terms array, exclude where needed.
					if ( ! in_array( $term->term_id, $exclude, true ) ) {
						$items[] = $term;
						$items   = alm_filters_loop_term_children( $items, $term, $exclude );
					}
				}

				ob_start();
				alm_filters_build_terms_select( $id, $obj, $match_array, $selected, $items );
				$output  = ob_get_contents();
				$return .= $output;
				ob_end_clean();

				break;

			default:
				// Radio/Checkbox.
				ob_start();
				alm_filters_build_terms_list( $id, $obj, $match_array, $terms, true );
				$output  = ob_get_contents();
				$return .= $output;
				ob_end_clean();

				break;
		}
		$return .= ALMFilters::alm_filters_display_toggle( $obj, 'after' );
		$return .= apply_filters( 'alm_filters_container_close', ALMFilters::alm_filters_get_container( $id, $obj, 'close' ) );
		$return .= $has_checkbox_limit ? wp_kses_post( alm_filters_checkbox_limit_button( $obj, $checkbox_limit, $term_count ) ) : '';
	}
	return $return;
}

/**
 * Term render method.
 * List elements in ul -> li
 *
 * @param string  $id Filter ID.
 * @param object  $obj Filter object.
 * @param array   $match_array Array of items to match.
 * @param array   $terms Tax terms.
 * @param boolean $init Is this the first run.
 * @return mixed
 * @since 1.10.2
 */
function alm_filters_build_terms_list( $id, $obj, $match_array, $terms, $init ) {
	if ( empty( $terms ) ) {
		// Bail early if empty terms.
		return;
	}

	$key            = $obj['key'];
	$filter_key     = alm_filters_get_filter_key( $obj );
	$field_type     = $obj['field_type'];
	$checkbox_limit = isset( $obj['checkbox_limit'] ) && $field_type === 'checkbox' ? (int) $obj['checkbox_limit'] : false;

	if ( $init ) {
		/**
		 * Get items before & after hook - Core Filter hook
		 *
		 * @since 1.13.0
		 */
		$before = apply_filters( 'alm_filters_' . $id . '_' . $filter_key . '_before', '' );
		if ( $before && is_array( $before ) ) {
			$before = array_reverse( $before );
			foreach ( $before as $item ) {
				array_unshift( $terms, (object) $item );
			}
		}

		$after = apply_filters( 'alm_filters_' . $id . '_' . $filter_key . '_after', '' );
		if ( $after && is_array( $after ) ) {
			foreach ( $after as $item ) {
				$terms[] = (object) $item;
			}
		}
	}

	$term_count         = $init ? count( $terms ) : 0;
	$has_checkbox_limit = $checkbox_limit && $term_count > $checkbox_limit;

	echo $init ? '' : '<ul>';

	// Loop terms.
	foreach ( $terms as $index => $item ) {
		$name  = $item->name;
		$slug  = $item->slug;
		$total = ALMFilters::$facets && $obj['show_count'] ? '<span class="alm-filter-counter"></span>' : alm_filters_build_count( $obj['show_count'], $item, true );

		$past_limit       = $has_checkbox_limit && (int) $index >= $checkbox_limit ? alm_filters_checkbox_limit_style() : '';
		$past_limit_class = $has_checkbox_limit && (int) $index >= $checkbox_limit ? alm_filters_checkbox_limit_class() : '';

		// If category_and use ID.
		$slug = $key === 'category_and' ? $item->term_id : $slug;

		// If tag_and use ID.
		$slug = $key === 'tag_and' ? $item->term_id : $slug;

		$obj['id']     = $key . '-' . $field_type . '-' . $obj['count'];
		$fieldname_val = $key . '-' . $field_type . '-' . $obj['count'];
		$fieldname     = $field_type === 'radio' ? ' name="' . $fieldname_val . '"' : '';
		$field_level   = $init ? ' field-parent' : ' field-child';

		$aria_checked = 'aria-checked="false"';
		if ( ! empty( $match_array ) ) { // Get active list item.
			$active       = in_array( (string) $slug, $match_array, true ) ? ' active' : '';
			$aria_checked = in_array( (string) $slug, $match_array, true ) ? 'aria-checked="true"' : $aria_checked;
		}

		// Build `<li/>`.
		echo '<li class="alm-filter--' . esc_attr( $field_type ) . esc_attr( $field_level ) . ' field-' . esc_attr( $index ) . esc_attr( $past_limit_class ) . '"' . wp_kses_post( $past_limit ) . '>';
		echo '<div class="alm-filter--link field-' . esc_attr( $field_type ) . ' field-' . esc_attr( $slug ) . ' ' . esc_attr( $active ) . '" id="' . esc_attr( $field_type ) . '-' . esc_attr( sanitize_title( $slug ) ) . '-' . esc_attr( $obj['count'] ) . '" data-type="' . esc_attr( $field_type ) . '" data-value="' . esc_attr( $slug ) . '" role="' . esc_attr( $field_type ) . '" tabindex="0" ' . esc_attr( $aria_checked ) . '>';
		echo wp_kses_post( $name . $total );
		echo '</div>';

		if ( isset( $item->children ) ) {
			alm_filters_build_terms_list( $id, $obj, $match_array, $item->children, false );
		}
		echo '</li>';
		continue;
	}

	echo $init ? '' : '</ul>';
}

/**
 * Build the terms list for a select listing.
 *
 * @param string $id          The filter ID.
 * @param array  $obj         Filter object array.
 * @param string $match_array The array to match for 'selected'.
 * @param string $selected    The selected item.
 * @param array  $terms       The terms to display.
 * @since 10.1.2
 */
function alm_filters_build_terms_select( $id, $obj, $match_array, $selected, $terms ) {
	if ( ! $terms ) {
		// Bail early if empty.
		return;
	}

	$field_type = $obj['field_type'];
	$key        = $obj['key'];

	// Default Select Option.
	if ( $obj['default_select_option'] ) {
		$filter_key            = alm_filters_get_filter_key( $obj );
		$default_select_option = apply_filters( 'alm_filters_' . $id . '_' . $filter_key . '_default_select_option', $obj['default_select_option'] );
		echo '<option value="#"' . $selected . '>' . $default_select_option . '</option>'; // phpcs:ignore
	}

	// Loop items.
	$items_count = 0;
	foreach ( $terms as $item ) {
		$items_count++;
		$name  = $item->name;
		$slug  = $item->slug;
		$total = alm_filters_build_count( $obj['show_count'], $item, false );
		if ( isset( $item->count ) && $obj['show_count'] ) {
			// If item count is available.
			$total = ! ALMFilters::$facets ? apply_filters( 'alm_filters_show_count_select_display', ' (' . $item->count . ')', $item->count ) : '';
		}
		$has_parent    = isset( $item->parent ) && $item->parent > 0 ? true : false;
		$parent        = $has_parent ? ' has_parent' : '';
		$fieldname_val = $key . '-' . $field_type . '-' . $obj['count'];
		$fieldname     = $field_type === 'radio' ? ' name="' . $fieldname_val . '"' : '';
		$display_name  = ! empty( $parent ) ? apply_filters( 'alm_filters_select_terms_indent', ' - ' ) . $name . $total : $name . $total;

		$slug = $key === 'category_and' ? $item->term_id : $slug; // If category_and use ID.
		$slug = $key === 'tag_and' ? $item->term_id : $slug; // If tag_and use ID.

		if ( ! empty( $match_array ) ) {
			$selected = in_array( $slug, $match_array, true ) ? ' selected="selected"' : '';
		}

		// Create the <option />.
		echo '<option id="' . $field_type . '-' . $slug . '"' . $fieldname . ' value="' . $slug . '" data-name="' . $display_name . '"' . $selected . '">';  // phpcs:ignore
		echo $display_name;  // phpcs:ignore
		echo '</option>';
	}
}

/**
 * Recursively get taxonomy and its children.
 *
 * @param  array $args   The query arguments.
 * @param  int   $parent Parent term id.
 * @return array         An array of term children.
 */
function alm_filters_get_taxonomy_hierarchy( $args = [], $parent = 0 ) {
	if ( ! $args || ! isset( $args['taxonomy'] ) || empty( $args['taxonomy'] ) ) {
		return;
	}

	$args['parent'] = $parent; // Add parent relationship.
	$terms          = get_terms( $args ); // Get the terms.
	$children       = []; // Prepare new array.

	// Loop direct decendants of $parent, and get children.
	if ( $terms ) {
		foreach ( $terms as $term ) {
			$term->children = alm_filters_get_taxonomy_hierarchy( $args, $term->term_id ); // Get direct decendants of "this" term.
			$children[]     = $term; // Add term to new array.
		}
	}
	return $children;
}

/**
 * Recursively get taxonomy children.
 *
 * @param array  $items   Terms array.
 * @param object $term    Current term object.
 * @param array  $exclude Excluded terms IDs.
 * @return array          Array of terms.
 */
function alm_filters_loop_term_children( $items, $term, $exclude ) {
	if ( ! $items || ! $term ) {
		return false;
	}

	// Confirm child term exists.
	if ( isset( $term->children ) ) {
		// Loop child terms.
		foreach ( $term->children as $child ) {
			if ( ! in_array( $child->term_id, $exclude, true ) ) {
				$items[] = $child;
				$items   = alm_filters_loop_term_children( $items, $child, $exclude );
			}
		}
	}

	return $items;
}

/**
 * Get parent or child_of value.
 *
 * @param array $args The array of arguments.
 * @return int        The parent ID.
 */
function alm_filters_get_parent_term( $args ) {
	$parent = isset( $args['parent'] ) && $args['parent'] ? $args['parent'] : 0; // Get parent.
	$parent = isset( $args['child_of'] ) && $args['child_of'] ? $args['child_of'] : $parent; // Get child_of.
	return $parent;
}
