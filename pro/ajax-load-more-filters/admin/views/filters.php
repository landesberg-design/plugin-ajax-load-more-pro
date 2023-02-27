<?php
/**
 * This file builds the filter builder interface/view for the WP admin.
 *
 * @package ALMFilters
 */

$editing      = false;
$deleted      = false;
$filter_id    = '';
$filter_vue   = '';
$section      = 'dashboard';
$query_params = filter_input_array( INPUT_GET, FILTER_SANITIZE_STRING );
// Export/Import, New.
if ( isset( $query_params['action'] ) ) {
	if ( $query_params['action'] === 'tools' ) {
		$section = 'tools';
	}
	if ( $query_params['action'] === 'new' ) {
		$section = 'new';
	}
}

// Edit Filter [EDIT Mode].
if ( isset( $query_params['filter'] ) ) {
	$filter = get_option( 'alm_filter_' . $query_params['filter'] );
	if ( $filter ) {
		$filter_id  = $query_params['filter'];
		$section    = 'edit';
		$editing    = true;
		$filter     = unserialize( $filter ); // Unseralize the filter array.
		$filter_vue = wp_json_encode( $filter ); // encode json to read in Vue.
	}
}

// Delete Filter.
$deleted_filter = '';
if ( isset( $query_params['delete_filter'] ) ) {
	$section = 'dashboard';
}

$selected = ' selected="selected"';
?>

<div class="admin ajax-load-more" id="alm-filters">
	<div class="wrap main-cnkt-wrap">
		<header class="header-wrap">
			<h1>
				<?php echo esc_html( ALM_TITLE ); ?>: <strong><?php esc_html_e( 'Filters', 'ajax-load-more-filters' ); ?></strong>
				<em><?php esc_html_e( 'Build and manage your Ajax Load More filters.', 'ajax-load-more-filters' ); ?></em>
			</h1>
		</header>
		<?php
		if ( 'dashboard' === $section ) {
			include ALM_FILTERS_PATH . 'admin/views/includes/dashboard.php';
		} elseif ( 'tools' === $section ) {
			include ALM_FILTERS_PATH . 'admin/views/includes/tools.php';
		} else {
			include ALM_FILTERS_PATH . 'admin/views/includes/edit.php';
		}
		?>
	</div>
</div>
