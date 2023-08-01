<?php
/**
 * Admin view for the WooCommerce add-on.
 *
 * @package ALMWooCommerce
 */

$alm_admin_heading = __( 'WooCommerce', 'alm-woocommerce' );
?>
<div class="admin wrap ajax-load-more main-cnkt-wrap" id="alm-add-ons-woocommerce">
	<?php
	if ( defined( 'ALM_PATH' ) && file_exists( ALM_PATH . 'admin/includes/components/header.php' ) ) {
		require_once ALM_PATH . 'admin/includes/components/header.php';
	}
	?>
	<div class="ajax-load-more-inner-wrapper">
		<div class="cnkt-main">
			<div class="woocommerce-intro">
				<h2><?php esc_attr_e( 'Getting Started', 'alm-woocommerce' ); ?></h2>
				<p class="lg">
					<?php esc_attr_e( 'The Ajax Load More WooCommerce add-on integrates directly into the main shop and archive templates using existing WooCommerce core hooks and filters.', 'alm-woocommerce' ); ?>
				</p>
				<p>
					<?php _e( sprintf( 'Add-on configuration happens within the WooCommerce panel of the <a href="%s">WordPress Customizer</a>', 'customize.php' ), 'alm-woocommerce' ); ?> - <?php _e( 'modification of page templates, building custom shortcodes or creating <a href="admin.php?page=ajax-load-more-repeaters">Repeater Templates</a> is not required for implementation.', 'alm-woocommerce' ); ?>
				</p>
				<p class="warning-callout" style="margin: 0;"><?php _e( sprintf( 'WooCommerce integration is disabled by default, visit the <a href="%s">Customizer</a> to enable Ajax Load More.', 'customize.php?autofocus[panel]=woocommerce&autofocus[section]=woocommerce_alm' ), 'alm-woocommerce' ); ?></p>
			</div>

			<div class="woocommerce-help">
				<div class="woocommerce-help--item">
					<div class="img">
						<img src="<?php echo esc_url( ALM_WOO_URL ); ?>/admin/assets/img/woo-customizer-settings.jpg" alt="" />
					</div>
					<div>
						<h2><?php esc_attr_e( 'Shop, Product Archives & Search', 'alm-woocommerce' ); ?></h2>
						<p class="lg"><?php esc_attr_e( 'Ajax Load More integrates directly into your WooCommerce shop and search templates.', 'alm-woocommerce' ); ?></p>
						<p><?php _e( sprintf( 'To enable Ajax Load More on shop and archive templates visit the WooCommerce <a href="%s">panel</a> of the WordPress Customizer.', 'customize.php?autofocus[panel]=woocommerce' ), 'alm-woocommerce' ); ?></p>
						<p><a class="button" href="customize.php?autofocus[panel]=woocommerce&autofocus[section]=woocommerce_alm"><?php _e( 'Edit in Customizer</a>', 'alm-woocommerce' ); ?></p>
					</div>
				</div>
				<div class="woocommerce-help--item">
					<div class="img">
						<img src="<?php echo esc_url( ALM_WOO_URL ); ?>/admin/assets/img/woo-customizer-display.jpg" alt="" />
					</div>
					<div>
						<h2><?php esc_attr_e( 'Display Options', 'alm-woocommerce' ); ?></h2>
						<p class="lg"><?php esc_attr_e( 'Adjust the look and feel and set default configurations options.', 'alm-woocommerce' ); ?></p>
						<p><?php esc_attr_e( 'Display options allow for configuration of Ajax Load More parameters such and loading style, button labels and scroll settings.', 'alm-woocommerce' ); ?></p>
						<p>
							<a class="button" href="customize.php?autofocus[panel]=woocommerce&autofocus[section]=woocommerce_alm_display">
								<?php esc_attr_e( 'Edit in Customizer', 'alm-woocommerce' ); ?>
							</a>
						</p>
					</div>
				</div>
			</div>

		</div>

		<aside class="cnkt-sidebar">
			<div id="cnkt-sticky-wrapper">
				<div id="cnkt-sticky">
				<div class="cta resources">
						<h3><?php _e( 'WooCommerce Resources', 'alm-woocommerce' ); ?></h3>
						<div class="cta-inner">
							<ul>
								<li>
									<a target="blank" href="https://connekthq.com/plugins/ajax-load-more/docs/add-ons/woocommerce/">
										<i class="fa fa-pencil"></i> <?php _e( 'Documentation', 'alm-woocommerce' ); ?>
									</a>
								</li>
								<li>
									<a target="blank" href="https://connekthq.com/plugins/ajax-load-more/add-ons/woocommerce/">
										<i class="fa fa-mouse-pointer"></i> <?php _e( 'Examples', 'alm-woocommerce' ); ?>
									</a>
								</li>
								<li>
									<a target="blank" href="https://connekthq.com/support/?product=Ajax%20Load%20More">
										<i class="fa fa-question-circle"></i> <?php _e( 'Support and Issues', 'alm-woocommerce' ); ?>
									</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</aside>
	</div>
</div>
