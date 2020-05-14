<?php
$alm_cache_array = ALMCACHE::alm_get_cache_array(); // Get dynamic cache pages 
$path = ALMCache::alm_get_cache_path(); // get path to cache
$cache_deleted = $cache_build = false;

if(isset($_GET['action'])) {	
	
	// Delete cache action
   if($_GET['action'] === 'delete'){ 	   
	   $result = ALMCache::alm_delete_full_cache();
   	// Redirect user to ?action=deleted to prevent double form submit
   	echo'<script> window.location="admin.php?page=ajax-load-more-cache&action=deleted"; </script> ';
	}
	
	// Cache deleted action
	if($_GET['action'] === 'deleted'){ 
		$cache_deleted = true;
   	$result = __('Cache deleted successfully', 'ajax-load-more-cache');
   }   
	
	// Cache build action
	if($_GET['action'] === 'build'){ 
		do_action('alm_clear_cache'); // Clear current cache before building
		$cache_deleted = false;
   	$cache_build = true;
   }   
   
   unset($_GET);   
}
?>
   
<div class="admin ajax-load-more alm-cache" id="alm-cache" data-msg="<?php _e('Are you sure you want to delete this cache?', 'ajax-load-more-cache'); ?>">
	<div class="wrap main-cnkt-wrap">
      <header class="header-wrap">
         <h1>
            <?php echo ALM_TITLE; ?>: <strong><?php _e('Cache', 'ajax-load-more-cache'); ?></strong>
            <em><?php _e('Manage your active Ajax Load More Cache', 'ajax-load-more-cache'); ?></em>
         </h1>
      </header>
      
      <div class="ajax-load-more-inner-wrapper">
         
   		<div class="cnkt-main">
	   		
	   		<?php 
		   		
		   	if($cache_build && $alm_cache_array){
			   	// Generate Cache
		   		include_once(ALM_CACHE_ADMIN_PATH .'admin/views/auto-generate.php'); 
		   		
		   	} else {
			   	// Cache Listing
			   	include_once(ALM_CACHE_ADMIN_PATH .'admin/views/file-listing.php');
			   
			   }
		   	?>
   			
   	   </div>
   
   	   <aside class="cnkt-sidebar">	 	   	     
   	      <div class="cta">
   	         <h3><?php _e('Statistics', 'ajax-load-more-cache'); ?></h3>
	   	      <div class="cta-inner">
	   	         <?php
	                  $dircount = 0;
	                  $filecount = 0;
	                  $directories = glob($path . "*");
	                  foreach($directories as $directory){
	                     $dir = glob($directory . "*");
	                     $val = count(glob($dir[0] .'/*.html'));
	                     if($val > 0){
	                        $dircount++;
	                        $filecount = $filecount + $val;
	                     }
	                  }
	               ?>
	   	         <p class="cache-stats">
		   	         <span class="stat" id="dircount"><?php echo $dircount; ?></span><?php _e('Page', 'ajax-load-more-cache'); ?><?php echo ($dircount > 1 || $dircount == 0) ? 's' : ''; ?> <?php _e('cached', 'ajax-load-more-cache'); ?>
		   	      </p>
	   	         <div class="spacer"></div>	  
	   	         <p class="cache-stats last">
		   	         <span class="stat" id="filecount"><?php echo $filecount; ?></span><?php _e('File', 'ajax-load-more-cache'); ?><?php echo ($filecount > 1 || $filecount == 0) ? 's' : ''; ?> <?php _e('cached', 'ajax-load-more-cache'); ?>
		   	      </p> 	         
   	         </div>
   	         <div class="major-publishing-actions">
	   	         <form id="delete-all-cache" name="delete-all-cache" action="admin.php" method="GET" data-msg="<?php _e('Are you sure you want to delete the entire Ajax Load More cache?', 'ajax-load-more-cache'); ?>">
	   		         <input type="hidden" value="ajax-load-more-cache" name="page">
	   		         <button type="submit" class="button-primary" name="action" value="delete"><?php _e('Delete Cache', 'ajax-load-more-cache'); ?></button>
	   		      </form>
	         	</div>   
   	      </div>
   	      
   	      <?php if(!$cache_build){ ?>
   	      <div class="cta">	   	      
   	         <h3><?php _e('Auto-Generate Cache', 'ajax-load-more-cache'); ?></h3>	
   	         <?php
	   	          if($alm_cache_array){ ?>				
   	   	      <div class="cta-inner">
      	   	      <p><?php _e('You have enabled auto-generation of the Ajax Load More cache. Click the <strong>Generate Cache</strong> below to start the process.', 'ajax-load-more-cache'); ?></p>
   	   	      </div>
   	   	      <div class="major-publishing-actions">
      		         <button type="button" class="button-primary button-alm-generate-cache">
   	   		         <?php _e('Generate Cache', 'ajax-load-more-cache'); ?>
      		         </button>&nbsp;
      		         <a class="button" href="https://connekthq.com/plugins/ajax-load-more/docs/add-ons/cache/#auto-generate" target="_blank">
   	   		         <?php _e('Documentation', 'ajax-load-more-cache'); ?>
      		         </a>
   	         	</div>
               <?php } else { ?>
                  <div class="cta-inner">
      	   	      <p><?php _e('Did you know you can auto-generate your entire Ajax Load More cache?', 'ajax-load-more-cache'); ?></p>
   	   	      </div>
   	   	      <div class="major-publishing-actions">
      		         <a class="button-primary" href="https://connekthq.com/plugins/ajax-load-more/docs/add-ons/cache/#auto-generate" target="_blank">
   	   		         <?php _e('Learn More', 'ajax-load-more-cache'); ?>
      		         </a>
   	         	</div>               
               <?php } ?>	   	      
   	      </div>
   	      <?php } ?>
   	      
   	   	<?php include_once( ALM_CACHE_ADMIN_PATH . 'admin/includes/cta/writeable.php'); ?>	
				<div class="clear"></div>
   	   </aside>
   	   
   	   <div class="clear"></div>
      </div>
	</div>
</div>
