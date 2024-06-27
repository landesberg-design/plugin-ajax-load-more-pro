<?php
/**
 * This file builds the filter builder interface/view for the WP admin.
 *
 * @package ALMFilters
 */

$editing           = false;
$deleted           = false;
$filter_id         = '';
$filter_vue        = '';
$section           = 'dashboard';
$query_params      = filter_input_array( INPUT_GET );
$alm_admin_heading = __( 'Filters', 'ajax-load-more' );

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

<div class="admin wrap ajax-load-more main-cnkt-wrap" id="alm-filters">
	<?php
	if ( defined( 'ALM_PATH' ) && file_exists( ALM_PATH . 'admin/includes/components/header.php' ) ) {
		require_once ALM_PATH . 'admin/includes/components/header.php';
	}
	if ( 'dashboard' === $section ) {
		include ALM_FILTERS_PATH . 'admin/views/dashboard.php';
	} elseif ( 'tools' === $section ) {
		include ALM_FILTERS_PATH . 'admin/views/tools.php';
	} else {
		include ALM_FILTERS_PATH . 'admin/views/edit.php';
	}
	?>
</div>
