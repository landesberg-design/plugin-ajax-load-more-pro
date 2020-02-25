<?php
/*
Plugin Name: Ajax Load More: Theme Repeaters
Plugin URI: https://connekthq.com/plugins/ajax-load-more/add-ons/theme-repeaters/
Description: Ajax Load More extension allowing repeater template selection from the current theme directory.
Author: Darren Cooney
Twitter: @KaptonKaos
Author URI: http://connekthq.com
Version: 1.1.1
License: GPL
Copyright: Darren Cooney & Connekt Media

*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly		

define('ALM_THEME_REPEATERS_VERSION', '1.1.1');
define('ALM_THEME_REPEATERS_RELEASE', 'May 6, 2019');


/*
*  alm_theme_repeaters_install
*  
*  Activation hook
*
*  @since 1.0
*/

function alm_theme_repeaters_install() {   
   //if Ajax Load More is activated
   if(!is_plugin_active('ajax-load-more/ajax-load-more.php')){	
   	die('You must install and activate <a href="https://wordpress.org/plugins/ajax-load-more/">Ajax Load More</a> before installing the Theme Repeaters Add-on.');
	}
}
register_activation_hook( __FILE__, 'alm_theme_repeaters_install' );




if( !class_exists('ALMTHEMEREPEATERS') ):

   class ALMTHEMEREPEATERS{	
      
   	function __construct(){			 
	   	
   		add_action( 'alm_theme_repeaters_installed', array(&$this, 'alm_theme_repeaters_installed') );
   		add_action( 'alm_theme_repeaters_settings', array(&$this, 'alm_theme_repeaters_settings') );      	
   		add_filter( 'alm_get_theme_repeater', array(&$this, 'alm_get_theme_repeater' ), 10, 5);	      	
   		add_filter( 'alm_get_acf_gallery_theme_repeater', array(&$this, 'alm_get_acf_gallery_theme_repeater' ), 10, 6);	
   		add_filter( 'alm_get_users_theme_repeater', array(&$this, 'alm_get_users_theme_repeater' ), 10, 6);	     	
   		add_filter( 'alm_get_rest_theme_repeater', array(&$this, 'alm_get_rest_theme_repeater' ), 10, 1);	   		
   		add_action( 'alm_theme_repeaters_selection', array(&$this, 'alm_theme_repeaters_selection' ));	
   		
   		//Load text domain
   		load_plugin_textdomain( 'ajax-load-more-theme-repeaters', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
   		
   	}  
   	
   	
   	/*
   	*  alm_theme_repeaters_installed
   	*  an empty function to determine if Local Templates is activated.
   	*
   	*  @since 1.0
   	*/
   	
   	function alm_theme_repeaters_installed(){
   	   //Empty return
   	} 
   	
   	
   	
   	/*
   	*  alm_theme_repeaters_settings
   	*  Create the Local Templates settings panel.
   	*
   	*  @since 1.0
   	*/
   	
   	function alm_theme_repeaters_settings(){
      	register_setting(
      		'alm_theme_repeaters_license', 
      		'alm_theme_repeaters_license_key', 
      		'alm_theme_repeaters_sanitize_license'
      	);	  
      	add_settings_section( 
	   		'alm_theme_repeaters_settings',  
	   		'Theme Repeater Settings', 
	   		'alm_theme_repeaters_callback', 
	   		'ajax-load-more' 
	   	);
	   	add_settings_field(  // Theme Repeater directory
				'_alm_theme_repeaters_dir', 
				__('Directory Selection', ALM_NAME ), 
				'alm_theme_repeaters_dir_callback', 
				'ajax-load-more', 
				'alm_theme_repeaters_settings' 
			); 	
	   }
	   
	   
	   
	   /*
		*  alm_verfify_filepath
		*  Security prevention. Don't allow users to back out and move directories.
		*
		*  @return $filepath
		*  @since 1.1
		*/	
	   function alm_verfify_filepath($filepath){
		   $filepath = str_replace('../', '', $filepath); // ../
		   $filepath = str_replace('..%2f', '', $filepath); // ..%2f
		   
		   return $filepath;
	   }
	   
	   
	   
	   /*
		*  alm_get_theme_repeaters_dir
		*  Get the current Theme Repeater dir
		*
		*  @return $options['_alm_theme_repeaters_dir']
		*  @since 1.1
		*/	
	   function alm_get_theme_repeaters_dir(){
		   
		   $options = get_option( 'alm_settings' );		   
		   if(!isset($options['_alm_theme_repeaters_dir'])){
		      $options['_alm_theme_repeaters_dir'] = 'alm_templates';
		   }
		   
		   return $options['_alm_theme_repeaters_dir'];
	   }
	   
	   
	   
	   /*
		*  alm_get_theme_repeater_file
		*  Get the complete file path to the Theme Repeater
		*
		*  @return $file
		*  @since 1.1
		*/	
	   function alm_get_theme_repeater_file($theme_repeater){
		   
		   // Check for Child Theme
		   if(is_child_theme()){		      
				$file = get_stylesheet_directory() . $theme_repeater;
	      } else {
		      $file = get_template_directory() . $theme_repeater;
	      }
	      
	      // Confirm file exists and run secondary security check
	   	if(!file_exists($file) || false !== strpos( $file, './')){ 
	   		$file = alm_get_default_repeater();
	   	}
	      
	      return $file;
	   }
   	 
   	
   	
   	
   	/*
		*  alm_get_theme_repeater
		*  Get the theme repeater template
		*
		*  @return $include (file path)
		*  @since 1.0
		*/		  	
   	function alm_get_theme_repeater($theme_repeater, $alm_found_posts, $alm_page, $alm_item, $alm_current){   	
      	
	   	if($theme_repeater != 'null'){
		   	
		   	// Get template directory
		   	$dir = $this->alm_get_theme_repeaters_dir();
		   	
		   	// Security prevention
		   	$theme_repeater = '/'. $dir . '/' .$this->alm_verfify_filepath($theme_repeater);
		   			   			   	
		   	// Get the complete file path
		   	$file = $this->alm_get_theme_repeater_file($theme_repeater);
				
				include($file);
				
			}else{
				
				include( alm_get_default_repeater() ); //Include default repeater template
			
			}
   	}
   	 
   	
   	
   	/*
		*  alm_get_acf_gallery_theme_repeater
		*  Get the theme repeater template for ACF Galleries
		*
		*  @return $include (file path)
		*  @since 1.0.8
		*/		
   		
   	
   	function alm_get_acf_gallery_theme_repeater($theme_repeater, $alm_found_posts, $alm_page, $alm_item, $alm_current, $image){
      	
	   	if($theme_repeater != 'null'){
		   	
		   	// Get template directory
		   	$dir = $this->alm_get_theme_repeaters_dir();
		   	
		   	// Security prevention
		   	$theme_repeater = '/'. $dir . '/' .$this->alm_verfify_filepath($theme_repeater);
		   			   			   	
		   	// Get the complete file path
		   	$file = $this->alm_get_theme_repeater_file($theme_repeater);
				
				include($file);
				
			}else{
				
				include( alm_get_default_repeater() ); //Include default repeater template
				
			}
   	}
   	 
   	
   	
   	/*
		*  alm_get_users_theme_repeater
		*  Get the theme repeater template for Users add-on
		*
		*  @return $include (file path)
		*  @since 1.1
		*/		
   		
   	
   	function alm_get_users_theme_repeater($theme_repeater, $alm_found_posts, $alm_page, $alm_item, $alm_current, $user){
      	
	   	if($theme_repeater != 'null'){
		   	
		   	// Get template directory
		   	$dir = $this->alm_get_theme_repeaters_dir();
		   	
		   	// Security prevention
		   	$theme_repeater = '/'. $dir . '/' .$this->alm_verfify_filepath($theme_repeater);
		   			   			   	
		   	// Get the complete file path
		   	$file = $this->alm_get_theme_repeater_file($theme_repeater);
				
				include($file);
				
			}else{
				
				include( alm_get_default_repeater() ); //Include default repeater template
				
			}
   	}
   	
   	
   	
   	 
   	
   	
   	/*
		*  alm_get_rest_theme_repeater
		*  Get the theme repeater template for the REST API add-on
		*
		*  @return $include (file path)
		*  @since 1.1
		*/		
   		
   	
   	function alm_get_rest_theme_repeater($theme_repeater){
      	
	   	if($theme_repeater != 'null'){
		   	
		   	// Get template directory
		   	$dir = $this->alm_get_theme_repeaters_dir();
		   	
		   	// Security prevention
		   	$theme_repeater = '/'. $dir . '/' .$this->alm_verfify_filepath($theme_repeater);
		   			   			   	
		   	// Get the complete file path
		   	$file = $this->alm_get_theme_repeater_file($theme_repeater);
				
				include($file);
				
			}else{
				
				include( alm_get_default_repeater() ); //Include default repeater template
				
			}
   	}
   		
   	
   	
   	/*
   	*  alm_theme_repeaters_selection
   	*  List the templates within the /alm_templates dir. within the current theme directory
   	*
   	*  @since 1.0
   	*/
   	
   	function alm_theme_repeaters_selection(){       	
   		$options = get_option( 'alm_settings' );
   		if(!isset($options['_alm_theme_repeaters_dir'])) 
   		   $options['_alm_theme_repeaters_dir'] = 'alm_templates';
   		   
   	?>
			<div class="spacer"></div>
   	   <div class="clear"></div>
   	   <div class="select-theme-repeater">
      	   <span class="or">or</span>
      	   <hr/>
				<div class="spacer"></div>
      	   <div class="section-title">	      	   
					<h4><?php _e('Theme Repeater', ALM_NAME); ?></h4>
         	   <p><?php _e('Select a repeater template from the <span>'.$options['_alm_theme_repeaters_dir'].'</span> (<a href="admin.php?page=ajax-load-more" target="_parent">update</a>) directory within your current theme folder', ALM_NAME); ?>.</p>
      	   </div>
      	   <div class="wrap">    
         	   <div class="inner">  	            
      	         <?php	      	         
	      	         // Get template location
	      	         if(is_child_theme()){
                     	$dir = get_stylesheet_directory() . '/' . $options['_alm_theme_repeaters_dir']; 		      	         
	      	         }else{		      	         
                     	$dir = get_template_directory() . '/' . $options['_alm_theme_repeaters_dir']; 
	      	         } 
                         
                     $count = 0;
                     
                     echo '<select name="theme-repeater-select" class="alm_element"><option value="" selected="selected">-- '.__('Select Theme Repeater', ALM_NAME).' --</option>';
                     foreach (glob($dir.'/*') as $file) {   
	                     $count++;                     
                        $file = realpath($file);
                        $link = substr($file, strlen($dir) + 1);
                        
                        // Only display .php, .html files files
                        $file_extension = strtolower(substr(basename($file), strrpos(basename($file), '.') + 1));
                        if($file_extension == 'php'){
                        	echo '<option value="'.basename($file).'">'.basename($file).'</option>';
                        }
                        
                     }
                     if($count==0) echo '<option value="null">'.__('No Templates Found', ALM_NAME).'</option>';
                     echo '</select>';
                  ?>                     
               </div>
      	   </div>
   	   </div>
      <?php
      	
   	}   	
   	
   }     
   
   
   /*
	*  alm_theme_repeaters_callback
	*  Theme Repeater Settings Heading
	*
	*  @since 1.0
	*/
	
	function alm_theme_repeaters_callback() {
	   $html = '<p>' . __('Customize your installation of the <a href="http://connekthq.com/plugins/ajax-load-more/theme-repeaters/">Theme Repeaters</a> add-on.', ALM_NAME) . '</p>';
	   
	   echo $html;
	}
	
	
	/*
	*  alm_theme_repeaters_dir_callback
	*  Select directory for theme level repeaters.
	*
	*  @since 1.0
	*/
	
	function alm_theme_repeaters_dir_callback(){
		$options = get_option( 'alm_settings' );
		if(!isset($options['_alm_theme_repeaters_dir'])) 
		   $options['_alm_theme_repeaters_dir'] = '/alm_templates';

		$html = '<p>'.__('Select the directory that will hold your <strong>Theme Repeater</strong> templates - all templates <u>must</u> be stored in a top level directory within your current theme folder', ALM_NAME).'.</p>';
		$html .= '<p class="notify">'.__('If a directory has not been specified, Ajax Load More will attempt to load templates from <span><i class="fa fa-folder"></i> alm_templates</span>', ALM_NAME).'</p>';
		
		$html .= '<div class="alm-dir-listing theme-repeaters"><ul>';
      
      $theme = wp_get_theme();
      $html .= '<p class="theme-title"><i class="fa fa-folder-open"></i> '. $theme->get( 'Name' ) .'</p>';
            
      if(is_child_theme()){
	      $dir = new DirectoryIterator(get_stylesheet_directory());
      }else{		  
	      $dir = new DirectoryIterator(get_template_directory());    	         
      }
      
      $dir_array = array();
      foreach ($dir as $fileinfo) {
         if ($fileinfo->isDir() && !$fileinfo->isDot()) {
            $dir_array[] = $fileinfo->getFilename();
         }
      }
      sort($dir_array);
      foreach ($dir_array as $directory) {
            $html .= '<li>';
            if($options['_alm_theme_repeaters_dir'] == $directory){
               $html .= '<input type="radio" id="dir_'.$directory.'" name="alm_settings[_alm_theme_repeaters_dir]" value="'.$directory.'" checked="checked">';
            }else{
               $html .= '<input type="radio" id="dir_'.$directory.'" name="alm_settings[_alm_theme_repeaters_dir]" value="'.$directory.'">';
            }
            $html .= '<label for="dir_'.$directory.'"><i class="fa fa-folder"></i> '.$directory.'</label></li>';
           
      }      
      $html .= '</ul></div>';
      

		
		echo $html;
	}
   
   
	   
   /*
   *  alm_theme_repeaters_sanitize_license
   *  Sanitize our license activation
   *
   *  @since 1.0
   */
   
   function alm_theme_repeaters_sanitize_license( $new ) {
   	$old = get_option( 'alm_theme_repeaters_license_key' );
   	if( $old && $old != $new ) {
   		delete_option( 'alm_theme_repeaters_license_status' ); // new license has been entered, so must reactivate
   	}
   	return $new;
   } 
   
   	
   	
   /*
   *  ALMTHEMEREPEATERS
   *  The main function responsible for returning Ajax Load More Local Templates.
   *
   *  @since 1.0
   */	
   
   function ALMTHEMEREPEATERS(){
   	global $ALMTHEMEREPEATERS;
   
   	if( !isset($ALMTHEMEREPEATERS) )
   	{
   		$ALMTHEMEREPEATERS = new ALMTHEMEREPEATERS();
   	}
   
   	return $ALMTHEMEREPEATERS;
   }
      
   
   // initialize
   ALMTHEMEREPEATERS();

endif; // class_exists check


/* Software Licensing */
function alm_theme_repeaters_plugin_updater() {	
   if(!has_action('alm_pro_installed') && class_exists('EDD_SL_Plugin_Updater')){ // Don't check for updates if Pro is activated
   	$license_key = trim( get_option( 'alm_theme_repeaters_license_key' ) ); // retrieve our license key from the DB
   	$edd_updater = new EDD_SL_Plugin_Updater( ALM_STORE_URL, __FILE__, array( 
   			'version' 	=> ALM_THEME_REPEATERS_VERSION,
   			'license' 	=> $license_key,
   			'item_id'   => ALM_THEME_REPEATERS_ITEM_NAME,
   			'author' 	=> 'Darren Cooney'
   		)
   	);
	}
}
add_action( 'admin_init', 'alm_theme_repeaters_plugin_updater', 0 );	
/* End Software Licensing */
