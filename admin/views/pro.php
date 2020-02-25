<?php
   $target = 'target="_blank"';
   $addons = alm_get_addons();
   $total = count($addons);
   $is_odd = false;
   if ($total % 2 != 0) {
      $is_odd = true;
   }
?>
<div class="admin ajax-load-more" id="alm-pro">
	<div class="wrap main-cnkt-wrap">
      <header class="header-wrap">
         <h1>
            <?php echo ALM_TITLE; ?>: <strong><?php _e('Pro', 'ajax-load-more-pro'); ?></strong>
            <em><?php _e('Manage your Ajax Load More Pro add-on activations', 'ajax-load-more-pro'); ?></em>
         </h1>
      </header>
      
      <div class="ajax-load-more-inner-wrapper">
         
   		<div class="cnkt-main full"> 
		      <h3><?php _e('Add-on Activations', 'ajax-load-more-pro'); ?></h3>
		      <p><?php _e('Toggle the activation status of Ajax Load More add-ons below', 'ajax-load-more-pro'); ?>:</p>

		      <div class="spacer"></div>
            
            <section class="alm-pro-listing--header">               
               <?php echo $total; ?> <?php _e('Add-ons Available', 'ajax-load-more-pro'); ?>
               <div><span class="num"></span> <?php _e('of', 'ajax-load-more-pro'); ?> <?php echo $total; ?> <?php _e('activated', 'ajax-load-more-pro'); ?></div>
            </section>
	           
	   		<div class="alm-pro-listing">  
   	   		<div class="loader"></div>  
   	   		<div class="alm-pro-listing--wrap">
               <?php                   
      	         $i = 0;    
   	            foreach($addons as $addon){ 
   	         		$name = $addon['name'];
   	         		$intro = $addon['intro'];
   	         		$desc = $addon['desc'];
   	         		$action = $addon['action'];
   	         		$key = $addon['key'];
   	         		$status = $addon['status'];
   	         		$version = $addon['version'];
   	         		$settings_field = $addon['settings_field'];
   	         		$url = $addon['url'];
   	         		$img = $addon['img'];
   	         		$slug = $addon['slug'];	
   	         		$option_name = ALM_PRO_OPTION_PREFIX . $slug;
   	         		$option_value = (get_option($option_name)) ? get_option($option_name) : update_option($option_name, 'inactive');   	         		
   	         		$i++;
   	      		?>   	      		
   	      		<section class="item <?php echo get_option($option_name); if($is_odd && $i == $total) echo ' last';  if(!$is_odd && $i >= ($total - 1)) echo ' last-row';?>" data-status="<?php echo $option_value; ?>" data-slug="<?php echo $slug; ?>">
   		      		<a href="#">     
   			      		<div class="state"><span class="offscreen"><?php _e('Toggle activation', 'ajax-load-more-pro'); ?></span></div>
   		               <div class="item--detail">
   		                  <img src="<?php echo ALM_ADMIN_URL; ?><?php echo $img; ?>" alt="">   		                  
   		                  <div>
   		                     <h2><?php echo $name; ?> <span><?php if(defined($version)) {echo constant($version);} ?></span></h2>
                              <p><?php echo $desc; ?></p>
   		                  </div>
   		               </div>		
   		               <div class="result">
      		               <span class="type active">
      		                  <?php _e('Activated', 'ajax-load-more-pro'); ?>
      		               </span>
      		               <span class="type inactive">
      		                  <?php _e('Deactivated', 'ajax-load-more-pro'); ?>
      		               </span>
   		               </div>            
   		   		   </a>    
   	      		</section>		   		
   	      		<?php } unset($addons); ?>		
   	   		</div>			
				</div>	
				
				<div class="call-out light no-shadow" style="width: 100%;">
      		   <p><?php _e('New <a href="https://connekthq.com/plugins/ajax-load-more/add-ons/" target="_blank"><strong>add-ons</strong></a> will be deactivated by default and must be activated before being used', 'ajax-load-more'); ?>.</p>
         	</div>      		
   
   	   </div>
   	   
   	   <div class="clear"></div>
      </div>
	</div>
</div>
