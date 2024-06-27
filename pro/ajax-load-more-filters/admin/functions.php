<?php
/**
 * Admin function and hooks.
 *
 * @package ALMFilters
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * This function will list all filters.
 *
 * @since 1.5
 * @param string $filter_id The filter ID.
 * @param string $pos       The position of the listing.
 */
function alm_list_all_filters( $filter_id, $pos = 'sidebar' ) {
	$filters = ALMFilters::alm_get_all_filters();
	$params  = filter_input_array( INPUT_GET );
	$order   = isset( $params['order'] ) ? $params['order'] : 'desc';
	$orderby = isset( $params['orderby'] ) ? $params['orderby'] : 'id';
	$results = [];

	if ( $filters ) :
		// Loop each filter.
		foreach ( $filters as $filter ) {
			$filter = preg_replace( '/' . esc_sql( ALM_FILTERS_PREFIX ) . '/', '', $filter, 1 );
			$data   = unserialize( get_option( ALM_FILTERS_PREFIX . $filter ) );

			if ( ! $data ) {
				continue;
			}

			$results[] = [
				'id'     => $data['id'],
				'date'   => $data['date_created'],
				'count'  => isset( $data['filters'] ) ? count( $data['filters'] ) : 0,
				'facets' => isset( $data['facets'] ) ? $data['facets'] : false,
			];

			// Order the results.
			if ( $orderby && version_compare( PHP_VERSION, '5.6', '>=' ) ) {
				if ( $order === 'asc' ) {
					usort(
						$results,
						function( $a, $b ) use ( $orderby ) {
							return $b[ $orderby ] <=> $a[ $orderby ]; // phpcs:ignore
						}
					);
				} else {
					usort(
						$results,
						function( $a, $b ) use ( $orderby ) {
							return $a[ $orderby ] <=> $b[ $orderby ]; // phpcs:ignore
						}
					);
				}
			}
		}
		?>

		<table class="wp-list-table widefat fixed striped table-view-list">
			<thead>
				<tr>
					<th class="title column-title">
						<a href="<?php echo esc_attr( ALM_FILTERS_BASE_URL ); ?>&orderby=id&order=<?php echo strtolower( $order ) === 'asc' ? 'desc' : 'asc'; ?>">
							<?php esc_attr_e( 'Filter ID', 'ajax-load-more-filters' ); ?>
							<?php alm_filters_list_arrow( 'id', $orderby, $order ); ?>
						</a>
					</th>
					<th class="text-center hide-mobile">
						<a href="<?php echo esc_attr( ALM_FILTERS_BASE_URL ); ?>&orderby=count&order=<?php echo strtolower( $order ) === 'asc' ? 'desc' : 'asc'; ?>">
							<?php esc_attr_e( 'Count', 'ajax-load-more-filters' ); ?>
							<?php alm_filters_list_arrow( 'count', $orderby, $order ); ?>
						</a>
					</th>
					<th class="text-center hide-mobile">
						<a href="<?php echo esc_attr( ALM_FILTERS_BASE_URL ); ?>&orderby=facets&order=<?php echo strtolower( $order ) === 'asc' ? 'desc' : 'asc'; ?>">
							<?php esc_attr_e( 'Facets', 'ajax-load-more-filters' ); ?>
							<?php alm_filters_list_arrow( 'facets', $orderby, $order ); ?>
						</a>
					</th>
					<th>
						<a href="<?php echo esc_attr( ALM_FILTERS_BASE_URL ); ?>&orderby=date&order=<?php echo strtolower( $order ) === 'asc' ? 'desc' : 'asc'; ?>">
							<?php esc_attr_e( 'Date', 'ajax-load-more-filters' ); ?>
							<?php alm_filters_list_arrow( 'date', $orderby, $order ); ?>
						</a>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ( $results as $filter ) :
					$id = $filter['id'];
					if ( $id ) {
						?>
					<tr>
						<td class="title column-title">
							<strong>
								<a href="<?php echo esc_attr( ALM_FILTERS_BASE_URL ) . '&filter=' . esc_attr( $id ); ?>" aria-label="<?php echo esc_attr( $id ); ?>">
									<?php echo esc_attr( $id ); ?>
								</a>
							</strong>
							<div class="row-actions">
								<a href="<?php echo esc_attr( ALM_FILTERS_BASE_URL ) . '&filter=' . esc_attr( $id ); ?>" aria-label="Edit <?php echo esc_attr( $id ); ?>">
									<?php esc_attr_e( 'Edit', 'ajax-load-more-filters' ); ?>
								</a>
								<span>|</span>
								<a href="<?php echo esc_attr( get_home_url() ) . '?alm_filters_preview=' . esc_attr( $id ); ?>" target="_blank" aria-label="Preview <?php echo esc_attr( $id ); ?>">
									<?php esc_attr_e( 'Preview', 'ajax-load-more-filters' ); ?>
								</a>
								<span>|</span>
								<a href="<?php echo esc_attr( ALM_FILTERS_BASE_URL ) . '&duplicate_filter=' . esc_attr( $id ); ?>" aria-label="Duplicate <?php echo esc_attr( $id ); ?>" class="duplicate-filter" data-id="<?php echo esc_attr( $id ); ?>">
									<?php esc_attr_e( 'Duplicate', 'ajax-load-more-filters' ); ?>
								</a>
								<span>|</span>
								<?php if ( $filter['facets'] ) { ?>
								<a href="<?php echo esc_attr( ALM_FILTERS_BASE_URL ); ?>&rebuild_facet_index=<?php echo esc_attr( $id ); ?>"><?php esc_attr_e( 'Rebuild Facet Index', 'ajax-load-more-filters' ); ?></a> |
								<?php } ?>
								<a href="<?php echo esc_attr( ALM_FILTERS_BASE_URL ); ?>&delete_filter=<?php echo esc_attr( $id ); ?>" data-name="<?php echo esc_attr( $id ); ?>" class="delete-filter"><?php esc_attr_e( 'Delete', 'ajax-load-more-filters' ); ?></a>
							</div>
						</td>
						<td class="text-center hide-mobile">
							<span class="filter-counter">
								<abbr title="<?php echo esc_attr( $filter['count'] ); ?> <?php esc_attr_e( 'filter block(s) in this filter', 'ajax-load-more-filters' ); ?>">
												<?php echo esc_attr( $filter['count'] ); ?></abbr>
							</span>
						</td>
						<td class="text-center hide-mobile">
							<?php if ( $filter['facets'] ) { ?>
							<i class="fa fa-check-square" aria-hidden="true" style="color: #78ca8e; font-size: 14px; cursor: help;" aria-label="<?php esc_attr_e( 'This filter contains facets.', 'ajax-load-more-filters' ); ?>" title="<?php esc_attr_e( 'This filter contains facets.', 'ajax-load-more-filters' ); ?>"></i>
							<?php } else { ?>
							<i class="fa fa-square" aria-hidden="true" style="opacity:0.2; font-size: 14px; cursor: help;" aria-label="<?php esc_attr_e( 'This filter does not contain facets.', 'ajax-load-more-filters' ); ?>" title="<?php esc_attr_e( 'This filter does not contain facets.', 'ajax-load-more-filters' ); ?>"></i>
							<?php } ?>
						</td>
						<td>
							<?php esc_attr_e( 'Published', 'ajax-load-more-filters' ); ?>:<br/>
							<?php
							if ( isset( $filter['date'] ) ) {
								echo '<abbr title="' . esc_attr( gmdate( 'Y/m/d h:i:s a', $filter['date'] ) ) . '">' . esc_attr( gmdate( 'Y/m/d', $filter['date'] ) ) . '</abbr>';
							}
							?>
						</td>
					</tr>
						<?php
					}
			endforeach;
				?>
			</tbody>
		</table>

		<div id="alm-filter-pop-up">
			<div class="inner-wrap small">
				<h3><?php esc_attr_e( 'Duplicate Filter', 'ajax-load-more-filters' ); ?></h3>
				<p><?php esc_attr_e( 'Enter a unique filter ID and click the `Duplicate` button.', 'ajax-load-more-filters' ); ?></p>
				<form action="" method="GET" class="dup-form">
					<input type="hidden" value="ajax-load-more-filters" name="page">
					<input type="hidden" value="" name="duplicate_filter">
					<input type="text" name="filter_id" onkeypress="window.restrictIDChars(event);">
					<div>
						<button type="submit" class="button button-primary"><?php esc_attr_e( 'Duplicate', 'ajax-load-more-filters' ); ?></button>
						<button type="button" class="button dup-form-close"><?php esc_attr_e( 'Cancel', 'ajax-load-more-filters' ); ?></button>
					</div>
				</form>
			</div>
		</div>
		<?php else : ?>
			<?php echo wp_kses_post( alm_filters_empty_filters( $pos ) ); ?>
		<?php endif; ?>
	<?php
}

/**
 * Table header column order arrows.
 *
 * @param string $column  The column heading.
 * @param string $orderby The orderby param.
 * @param string $order   The order param.
 * @return void
 */
function alm_filters_list_arrow( $column = '', $orderby = null, $order = 'asc' ) {
	if ( $orderby === $column ) {
		if ( $order === 'asc' ) {
			?>
		<span class="dashicons dashicons-arrow-up" style="color: #555;"></span>
		<?php } else { ?>
		<span class="dashicons dashicons-arrow-down" style="color: #555;"></span>
			<?php
		}
	}
}

/**
 * This function is called when filters do not exist.
 *
 * @since 1.5
 * @param string $pos Position of the filter.
 * @return string     Raw HTML for display.
 */
function alm_filters_empty_filters( $pos ) {
	$response  = '<div class="alm-no-filters ' . $pos . '">';
	$response .= '<div class="alm-no-filters--inner">';
	$response .= '<p class="first-intro">';
	$response .= __( 'It appears you don\'t have any filters!', 'ajax-load-more-filters' );
	$response .= '</p>';
	$response .= '<p>';
	$response .= __( 'The first step in filtering with Ajax Load More is to create one!', 'ajax-load-more-filters' );
	$response .= '</p>';

	if ( $pos !== 'sidebar' ) {
		$response .= '<p class="create-btn"><a href="' . ALM_FILTERS_BASE_URL . '&action=new" class="button button-primary button-large"> ' . __( 'Create Filter', 'ajax-load-more-filters' ) . '</a></p>';
	}

	$response .= '</div>';
	$response .= '</div>';

	return $response;
}
