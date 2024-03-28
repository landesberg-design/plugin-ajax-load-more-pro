<?php
/**
 * The tools view.
 *
 * @see admin.php?page=ajax-load-more-filters&action=tools
 * @package ALMFilters
 */

?>
<div class="ajax-load-more-inner-wrapper">
	<div class="cnkt-main stylefree">
		<div class="alm-filters">
			<?php require ALM_FILTERS_PATH . 'admin/views/includes/navigation.php'; ?>
			<div class="alm-content-wrap">
				<?php $alm_filters = ALMFilters::alm_get_all_filters(); ?>
				<?php if ( $alm_filters ) : ?>
				<section class="alm-filter-tools-wrap" id="export">
					<header class="alm-filter--intro full">
						<h2><?php esc_attr_e( 'Export Filters', 'ajax-load-more-filters' ); ?></h2>
						<p><?php esc_attr_e( 'Use the Export button to export a JSON file that can be imported into another ALM Filters instance.', 'ajax-load-more-filters' ); ?></p>
					</header>

					<form method="post" class="alm-filter--tools">
					<?php
					echo '<ul class="alm-import-wrap">';
					echo '<span>' . esc_attr__( 'Select Filters for Export', 'ajax-load-more-filters' ) . '</span>';
					if ( count( $alm_filters ) > 1 ) {
						echo '<li><label><input type="checkbox" id="toggle-all-filters" name="filter_keys_master" value="">' . esc_attr__( 'Toggle All', 'ajax-load-more-filters' ) . '</label></li>';
					}
						echo '<div class="export-columns">';
					foreach ( $alm_filters as $filter ) {
						?>
									<li>
									<label>
										<input type="checkbox" name="filter_keys[]" id="<?php echo esc_attr( $filter ); ?>" value="<?php echo esc_attr( $filter ); ?>">
							<?php echo esc_attr( ALMFilters::alm_filters_replace_string( $filter ) ); ?>
									</label>
									</li>
						<?php
					}
						echo '</div>';
					echo '</ul>';
					?>
						<div class="button-wrap">
							<button class="button button-primary" id="export-filters" name="button"><?php esc_attr_e( 'Export', 'ajax-load-more-filters' ); ?></button>
							<input type="hidden" name="alm_filters_export" value="true">
						</div>
					</form>
				</section>
			<?php endif; ?>


				<section
				<?php
				if ( $alm_filters ) {
					echo 'style="padding-top: 30px;"'; }
				?>
				id="import">
					<header class="alm-filter--intro full">
						<h2><?php esc_attr_e( 'Import Filters', 'ajax-load-more-filters' ); ?></h2>
						<p><?php esc_attr_e( 'Select the Ajax Load More JSON file you would like to import. When you click the import button below, ALM will import the filter groups.', 'ajax-load-more-filters' ); ?></p>
					</header>

					<form method="post" class="alm-filter--tools" enctype="multipart/form-data">
						<div class="alm-import-wrap">
							<label for="alm_import_file" class="import"><?php esc_attr_e( 'Select File', 'ajax-load-more-filters' ); ?></label>
							<input name="alm_import_file" id="alm_import_file" type="file" >
						</div>
						<div class="button-wrap">
							<button class="button button-primary" id="import-filters" name="button"><?php esc_attr_e( 'Import', 'ajax-load-more-filters' ); ?></button>
							<input type="hidden" name="alm_filters_import" value="true">
						</div>
					</form>
				</section>

			</div>
		</div>
	</div>
	<aside class="cnkt-sidebar" data-sticky>
		<?php require_once ALM_FILTERS_PATH . 'admin/views/cta/help.php'; ?>
	</aside>
</div>
