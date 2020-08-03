<aside class="cnkt-sidebar">
   <div id="cnkt-sticky-wrapper">
	   <div id="cnkt-sticky">

   	   <?php if($editing){ ?>
   	   <div class="cta">
   			<h3>
	   			<?php _e('Shortcode Output', 'ajax-load-more'); ?>
	   			<a title="<?php _e('Use the following shortcode to generate this Ajax Load More filter instance', 'ajax-load-more-filters'); ?>" href="javascript:void(0)" class="fa fa-question-circle tooltip"></a>
	   		</h3>
   			<div class="cta-inner no-side-padding">
   				<div class="output-wrap" style="margin-top: 0;">
   					<div id="shortcode_output">[ajax_load_more_filters id="{{ data[0].id }}" target="{your_alm_id}"]</div>
   				</div>
   				<p style="line-height: 1.35; font-size: 12px; padding: 12px 7px 0 0; margin: 0;">
	   				<?php _e('Don\'t forget to update the <strong>target</strong> parameter with your Ajax Load More ID', 'ajax-load-more-filters'); ?>.</p>
   			</div>
   			<div class="major-publishing-actions">
   				<a class="button button-primary copy copy-to-clipboard"><?php _e('Copy Shortcode', 'ajax-load-more-filters'); ?></a>
   				<?php //if( WP_DEBUG ) { ?> &nbsp; <a class="button" v-on:click="showOutput($event)"><?php _e('Generate PHP', 'ajax-load-more-filters'); ?></a>
   				<?php //} ?>
   			</div>
      	</div>
   	   <?php } ?>

	      <?php if($section === 'new'){
		      include(ALM_FILTERS_PATH .'admin/views/cta/filter-list.php');
	      }
	      include(ALM_FILTERS_PATH .'admin/views/cta/help.php');

	      ?>

	   </div>
   </div>
	<div class="clear"></div>
</aside>
