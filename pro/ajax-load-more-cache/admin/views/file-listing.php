<?php
// Cache deleted msg
if(isset($result) && $cache_deleted) {
   echo '<div class="cache-cleared"><i class="fa fa-check-square-o"></i> ';
      echo $result;
      echo '<span class="remove"><a href="admin.php?page=ajax-load-more-cache">' . __('Got it', 'ajax-load-more-cache') . '</a></span>';
   echo '</div>';
}
?>

<h3><?php _e('Cache Dashboard', 'ajax-load-more-cache'); ?></h3>
<p><?php _e('All pages and files in your Ajax Load More cache are listed below - the listing is grouped by the <strong>Cache ID</strong> assigned when your <a href="admin.php?page=ajax-load-more-shortcode-builder">Shortcode</a> was created.', 'ajax-load-more-cache'); ?></small></p>
<p><a href="admin.php?page=ajax-load-more"><strong><?php _e('Cache Settings', 'ajax-load-more-cache'); ?></strong></a> &nbsp;|&nbsp;  <a href="https://connekthq.com/plugins/ajax-load-more/docs/add-ons/cache/" target="_blank"><strong><?php _e('View Documentation', 'ajax-load-more-cache'); ?></strong></a></p>

<div class="spacer"></div>
  		   
<div class="group no-shadow">
	<?php if(!$cache_deleted) { ?>
   <span class="toggle-all">
   	<span class="inner-wrap">
   		<em class="collapse"><?php _e('Collapse All', 'ajax-load-more-cache'); ?></em>
   		<em class="expand"><?php _e('Expand All', 'ajax-load-more-cache'); ?></em>
   	</span>
   </span>
   <?php } ?>
   <div class="row no-brd">
	         			      
      <div class="alm-cache-search-wrap" style="margin-top: 3px;">
	      <input type="text" name="alm-cache-search" id="alm-cache-search" value="" placeholder="<?php _e('Search cache by ID or URL ', 'ajax-load-more-cache'); ?>">
	      <i class="fa fa-search"></i>
      </div>

      <?php   
      // Loop thru Cache directories
      $directoy_total = 0;
      $staticDirectories = array();
      
      if(is_dir($path)){ // confirm directory exists

         // Loop the directories and store values in array for sorting
         foreach (new DirectoryIterator($path) as $file) {
            if ($file->isDot())
            	continue;

            if ($file->isDir())
            	$staticDirectories[] = $file->getFilename();
         }

         asort($staticDirectories); // Sort the directory array
         foreach($staticDirectories as $directory){ // Loop thru our sorted directories and store files in array for sorting

            $directoy_total++;

            echo '<div class="alm-dir-listing">';
               echo '<h3 class="heading dir dir-title" title="'. $path . '/' . $directory .'">'. $directory . ' <a href="javascript:void(0);" class="delete" data-id="'. $directory .'" title="'.__('Delete this cache', 'ajax-load-more-cache').'">'. __('Delete', 'ajax-load-more-cache') .'</a></h3>';

               echo '<div class="expand-wrap">';

               $sub_path = $path . $directory;

               // Get value of _info.txt
               $url = file_get_contents(ALMCache::alm_get_cache_path() .'/'. $directory.'/_info.txt');
               
               if($url){
                  echo '<ul class="cache-details">';
                  
               	$info = unserialize($url);                           	
                  $time = strtotime($info['created']);                     
                  
                  echo '<li title="'.__('Cached URL', 'ajax-load-more-cache').'"><i class="fa fa-globe"></i> <a href="' . $info['url'] . '" target="_blank">' . $info['url'] . '</a></li>';
                  echo '<li title="'.__('Date Created', 'ajax-load-more-cache').'"><i class="fa fa-clock-o"></i> ' . date('F d, Y @ h:i:s A', $time) . '</li>';
						echo '</ul>';
               }

               // Display cached pages
               echo '<div class="cache-page-wrap">';
               echo '<ul>';
               echo '<div class="cache-page-title">'.__('Cached files in this directory', 'ajax-load-more-cache').':</div>';

               $staticFiles = array();
               foreach (new DirectoryIterator($sub_path) as $sub_file) { // each file
                  if ($sub_file->isDot() || $sub_file->getFilename() === '_info.txt')
                  	continue;

                  if ($sub_file->isFile())
                  	$staticFiles[] = $sub_file->getFilename();
               }

               asort($staticFiles); // Sort the file array
               foreach($staticFiles as $static){ // Loop the sorted array to display static html
                  echo '<li class="file"><i class="fa fa-file-text-o"></i> <a href="'. ALMCache::alm_get_cache_url() . $directory . '/'. $static .'" target="_blank">'. $static . '</a></li>';
               }

               echo '</div>';
               echo '</ul>';
               echo '</div>';
            echo '</div>';
         }
      }

      // Empty
      if($directoy_total == 0){
         echo '<div class="dir-empty" style="overflow: hidden;">';
            echo '<p style="margin: 0; line-height: 30px;">';
           	echo __('Your Ajax Load More cache is currently empty!', 'ajax-load-more-cache');
               if($alm_cache_array){
                  echo '<a class="button button-primary" style="float: right;" href="admin.php?page=ajax-load-more-cache&action=build">'. __('Generate Cache</a>', 'ajax-load-more-cache');		
               }
            echo '</p>';
         echo '</div>';
         echo '<style>.toggle-all, .alm-cache-search-wrap{display:none !important;}</style>';
      }   
      ?>
   </div>   
</div>