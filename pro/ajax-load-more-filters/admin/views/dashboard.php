<?php
/**
 * The dashboard view.
 *
 * @see admin.php?page=ajax-load-more-filters
 * @package ALMFilters
 */

//@codingStandardsIgnoreStart
?>
<div class="ajax-load-more-inner-wrapper">
	<div class="cnkt-main stylefree">
		<div class="alm-filters">
			<?php require_once ALM_FILTERS_PATH . 'admin/views/includes/navigation.php'; ?>
			<div class="alm-content-wrap">
				<header class="alm-filter--intro">
					<div>
						<h2><?php _e( 'Filters', 'ajax-load-more-filters' ); ?></h2>
						<p><?php _e( 'Your custom Ajax Load More filters are listed below.', 'ajax-load-more-filters' ); ?></p>
					</div>
					<a href="<?php echo ALM_FILTERS_BASE_URL; ?>&action=new" class="button button-large button-primary"><?php _e( 'Add New', 'ajax-load-more-filters' ); ?></a>
				</header>
				<div class="filter-listing--main">
					<?php echo alm_list_all_filters( $filter_id, 'main' ); ?>
					<script>
					   var deleteLink = document.querySelectorAll('.filter-listing--main a.delete-filter');
						for (var i = 0; i < deleteLink.length; i++) {
							deleteLink[i].addEventListener('click', function(event) {
								var id = event.target.dataset.name;
								if (!confirm(alm_filters_localize.delete_filter + ' ' + id +'?')) {
									event.preventDefault();
								}
							});
						}
					</script>
				</div>
			</div>
		</div>
	</div>
	<aside class="cnkt-sidebar" data-sticky>
		<?php require_once ALM_FILTERS_PATH . 'admin/views/cta/whats-new.php'; ?>
		<?php require_once ALM_FILTERS_PATH . 'admin/views/cta/help.php'; ?>
	</aside>
</div>
