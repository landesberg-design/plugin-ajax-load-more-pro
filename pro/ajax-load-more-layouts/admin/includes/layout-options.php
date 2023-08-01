<?php
/**
 * Layout list options.
 *
 * @package AjaxLoadMoreLayouts.
 */

$alm_layouts = [
	[
		'label' => __( 'Default', 'ajax-load-more-layouts' ),
		'type'  => 'standard',
	],
	[
		'label' => __( 'Blog Card', 'ajax-load-more-layouts' ),
		'type'  => 'blog-card',
	],
	[
		'label' => __( 'Blog Card #2', 'ajax-load-more-layouts' ),
		'type'  => 'blog-card-two',
	],
	[
		'label' => __( 'Blog Card #3', 'ajax-load-more-layouts' ),
		'type'  => 'blog-card-three',
	],
	[
		'label' => __( 'Call to Action', 'ajax-load-more-layouts' ),
		'type'  => 'cta',
	],
	[
		'label' => __( 'Gallery', 'ajax-load-more-layouts' ),
		'type'  => 'gallery',
	],
	[
		'label' => __( 'Card Flip', 'ajax-load-more-layouts' ),
		'type'  => 'card-flip',
	],
	[
		'label' => __( 'Column Grid', 'ajax-load-more-layouts' ),
		'type'  => 'grid',
	],
];
?>

<ul>
<?php
foreach ( $alm_layouts as $alm_layout ) {
	?>
	<li>
		<button class="layout" data-type="<?php echo esc_attr( $alm_layout['type'] ); ?>">
			<i class="fa fa-file-code-o" aria-hidden="true"></i>
			<span><?php echo esc_attr( $alm_layout['label'] ); ?></span>
		</button>
	</li>
	<?php
}
?>
</ul>
<div class="drop-cta">
	<a class="button button-primary" href="https://connekthq.com/plugins/ajax-load-more/add-ons/layouts/#layouts" target="_blank">
		<?php esc_attr_e( 'Preview Layouts', 'ajax-load-more-layouts' ); ?>
	</a>
</div>
