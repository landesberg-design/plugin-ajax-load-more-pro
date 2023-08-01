<?php
/**
 * Ajax Load More Pro Page.
 *
 * @package AjaxLoadMorePro
 * phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
 * phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
 */

$alm_addons        = alm_get_addons();
$alm_admin_heading = __( 'Ajax Load More Pro', 'ajax-load-more' );
?>
<div class="wrap ajax-load-more main-cnkt-wrap" id="alm-pro">
	<?php
	if ( defined( 'ALM_PATH' ) && file_exists( ALM_PATH . 'admin/includes/components/header.php' ) ) {
		require_once ALM_PATH . 'admin/includes/components/header.php';
	}
	?>
	<div class="ajax-load-more-inner-wrapper no-flex">
		<div class="cnkt-main full">
			<section class="alm-pro-listing--header">
				<div>
					<h2><?php esc_attr_e( 'Add-ons', 'ajax-load-more-pro' ); ?></h2>
					<p><?php esc_attr_e( 'Toggle the activation status of Ajax Load More add-ons below', 'ajax-load-more-pro' ); ?>:</p>
				</div>
				<div class="totals">
					<span class="num"></span> <?php esc_attr_e( 'of', 'ajax-load-more-pro' ); ?> <span><?php echo esc_attr( count( $alm_addons ) ); ?></span> <?php esc_attr_e( 'activated', 'ajax-load-more-pro' ); ?>
				</div>
			</section>
			<style>
				.alm-pro-listing .item--detail p:before{
					display: none;
				}
			</style>
			<div class="alm-pro-listing">
				<?php
					$i = 0;
				foreach ( $alm_addons as $addon ) {
					$name           = $addon['name'];
					$intro          = $addon['intro'];
					$desc           = $addon['desc'];
					$action         = $addon['action'];
					$key            = $addon['key'];
					$status         = $addon['status'];
					$version        = $addon['version'];
					$settings_field = $addon['settings_field'];
					$url            = $addon['url'];
					$img            = $addon['img'];
					$slug           = $addon['slug'];
					$option_name    = ALM_PRO_OPTION_PREFIX . $slug;
					$option_value   = get_option( $option_name ) ? get_option( $option_name ) : update_option( $option_name, 'inactive' );
					$plugin_path    = ALM_PRO_ADMIN_PATH . 'pro/ajax-load-more-' . $slug . '/ajax-load-more-' . $slug . '.php';
					$i++;
					$installed       = true;
					$installed_class = 'installed';
					if ( ! file_exists( $plugin_path ) ) {
						$installed       = false;
						$installed_class = 'not-installed';
					}
					?>
					<section class="item <?php echo esc_attr( get_option( $option_name ) ); ?>
					" data-status="<?php echo esc_attr( $option_value ); ?>" data-slug="<?php echo esc_attr( $slug ); ?>">
						<a href="<?php echo esc_url( $url ); ?>" class="<?php echo esc_attr( $installed_class ); ?>" title="
							<?php
							if ( ! $installed ) {
								esc_attr_e( 'Add-on not installed', 'ajax-load-more-pro' );}
							?>
						">
						<?php if ( $installed ) { ?>
							<div class="state"><span class="offscreen"><?php esc_attr_e( 'Toggle activation', 'ajax-load-more-pro' ); ?></span></div>
							<?php } ?>
							<div class="item--detail">
								<img src="<?php echo esc_url( ALM_ADMIN_URL ); ?><?php echo esc_attr( $img ); ?>" alt="">
								<div>
									<h2><?php echo esc_attr( $name ); ?>
										<span>
										<?php
										if ( defined( $version ) ) {
											echo esc_attr( constant( $version ) );
										}
										?>
										</span>
									</h2>
									<p><?php echo esc_attr( $desc ); ?></p>

								<?php
								if ( ! $installed ) {
									echo '<p class="highlight-addon"><span>' . esc_attr__( 'Add-on Not Installed', 'ajax-load-more-pro' ) . '</span><br/>';
									echo esc_attr__( 'You need to update ALM Pro or renew your subscription to access this add-on.', 'ajax-load-more-pro' );
									echo '</p>';
								}
								?>

								</div>
							</div>
							<div class="loader"></div>
							<?php if ( $installed ) { ?>
							<div class="result">
								<span class="type active">
									<?php esc_attr_e( 'Activated', 'ajax-load-more-pro' ); ?>
								</span>
								<span class="type inactive">
									<?php esc_attr_e( 'Deactivated', 'ajax-load-more-pro' ); ?>
								</span>
							</div>
							<?php } ?>
						</a>
					</section>
					<?php } unset( $alm_addons ); ?>
			</div>
		</div>
		<div class="call-out call-out--centered light no-shadow" style="width: 100%;">
			<p><?php echo wp_kses_post( __( 'New <a href="https://connekthq.com/plugins/ajax-load-more/add-ons/" target="_blank"><strong>add-ons</strong></a> will be deactivated by default and must be activated before being used', 'ajax-load-more' ) ); ?>.</p>
		</div>
		<div class="spacer lg"></div>
	</div>
</div>
