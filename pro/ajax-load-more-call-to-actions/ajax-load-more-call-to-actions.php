<?php
/*
Plugin Name: Ajax Load More: Call to Actions
Plugin URI: http://connekthq.com/plugins/ajax-load-more/call-to-actions/
Description: Ajax Load More extension for displaying advertisements and call to actions.
Author: Darren Cooney
Twitter: @KaptonKaos
Author URI: http://connekthq.com
Version: 1.0.3
License: GPL
Copyright: Darren Cooney & Connekt Media
*/


define('ALM_CTA_PATH', plugin_dir_path(__FILE__));
define('ALM_CTA_URL', plugins_url('', __FILE__));
define('ALM_CTA_VERSION', '1.0.3');
define('ALM_CTA_RELEASE', 'May 6, 2019');


/*
*  alm_cta_install
*  Install the add-on
*
*  @since 1.0
*/

register_activation_hook( __FILE__, 'alm_cta_install' );
function alm_cta_install() {
   if(!is_plugin_active('ajax-load-more/ajax-load-more.php')){	//if Ajax Load More is activated
   	die('You must install and activate <a href="https://wordpress.org/plugins/ajax-load-more/">Ajax Load More</a> before installing the Ajax Load More Call to Actions add-on.');
	}
}



if( !class_exists('ALMCTA') ):

   class ALMCTA{

   	function __construct(){
   		add_action( 'alm_cta_installed', array(&$this, 'alm_cta_installed') );
   		add_filter( 'alm_cta_shortcode', array(&$this, 'alm_cta_shortcode'), 10, 4);
   		add_action( 'alm_cta_inc', array(&$this, 'alm_cta_inc'), 10, 7);
   		add_filter( 'alm_cta_pos_array', array(&$this, 'alm_cta_pos_array'), 10, 6);
   	}



   	/*
   	*  alm_cta_pos_array
   	*  Build array of CTA position values
   	*
   	*  @return $cta_array
   	*  @since 1.0
   	*/
   	function alm_cta_pos_array($seo_start_page, $page, $posts_per_page, $alm_found_posts, $cta_val, $paging){

      	if($paging === 'true'){

         	$cta_array[] = $cta_val;

      	} else {

   			if($seo_start_page > 1 && $page < $seo_start_page){
   				// If is SEO first load
   				$posts_per_page = floor($posts_per_page/$seo_start_page); // Get orginal $posts_per_page value.
   				//echo $posts_per_page;
   				$pages = $alm_found_posts/$posts_per_page;
   				for($i = 0; $i < $pages; $i++){
   					$cta_array[] = $cta_val + ($i*$posts_per_page);
   				}
   			}
   			else{
   				$cta_array[] = $cta_val;
   			}

			}

			return $cta_array;
   	}



   	/*
   	*  alm_cta_shortcode
   	*  Build shortcode params and send back to core ALM
   	*
   	*  @reutrn $shortcode
   	*  @since 1.0
   	*/

   	function alm_cta_shortcode($cta, $cta_position, $cta_repeater, $cta_theme_repeater){
   		$shortcode  = ' data-cta="'.$cta.'"';
			$shortcode .= ' data-cta-position="'.$cta_position.'"';
			$shortcode .= ' data-cta-repeater="'.$cta_repeater.'"';
			$shortcode .= ' data-cta-theme-repeater="'.$cta_theme_repeater.'"';
			return $shortcode;
   	}



   	/*
   	*  alm_cta_inc
   	*  Get call to action
   	*
   	*  @return $file
   	*  @since 1.0
   	*/

   	function alm_cta_inc($cta_repeater, $cta_theme_repeater, $alm_found_posts, $alm_page, $alm_item, $alm_current, $preloaded){
	   	if($preloaded){
		   	ob_start();
		   }
	   	if($cta_theme_repeater != 'null' && has_filter('alm_get_theme_repeater')){
		   	$type = alm_get_repeater_type($cta_theme_repeater);
				do_action('alm_get_theme_repeater', $cta_theme_repeater, $alm_found_posts, $alm_page, $alm_item, $alm_current); // Theme Repeater
			}else{
		   	$type = alm_get_repeater_type($cta_repeater);
				$file = alm_get_current_repeater($cta_repeater, $type); // Standard Repeaters
            include($file);
			}
			if($preloaded){
				$return = ob_get_clean();
				return $return;
			}
	   }



   	/*
   	*  alm_cta_installed
   	*  an empty function to determine if preload is true.
   	*
   	*  @since 1.0
   	*/

   	function alm_cta_installed(){
   	   //Empty return
   	}



   	/*
   	*  alm_cta_settings
   	*  Create the settings panel.
   	*
   	*  @since 1.0
   	*/

   	function alm_cta_settings(){
      	register_setting(
      		'alm_cta_license',
      		'alm_cta_license_key',
      		'alm_cta_sanitize_license'
      	);
   	}

   }



   /*
   *  alm_cta_sanitize_license
   *  Sanitize our license activation
   *
   *  @since 1.0
   */

   function alm_cta_sanitize_license( $new ) {
   	$old = get_option( 'alm_cta_license_key' );
   	if( $old && $old != $new ) {
   		delete_option( 'alm_cta_license_status' ); // new license has been entered, so must reactivate
   	}
   	return $new;
   }



   /*
   *  ALMAlternatingTemplates
   *  The main function responsible for returning Ajax Load More ALternating Templates.
   *
   *  @since 1.0
   */

   function ALMCTA(){
   	global $ALMCTA;
   	if( !isset($ALMCTA) ){
   		$ALMCTA = new ALMCTA();
   	}
   	return $ALMCTA;
   }

   // initialize
   ALMCTA();

endif; // class_exists check



/* Software Licensing */
function alm_cta_plugin_updater() {
   if(!has_action('alm_pro_installed') && class_exists('EDD_SL_Plugin_Updater')){ // Don't check for updates if Pro is activated
   	$license_key = trim( get_option( 'alm_cta_license_key' ) ); // retrieve our license key from the DB
   	$edd_updater = new EDD_SL_Plugin_Updater( ALM_STORE_URL, __FILE__, array(
   			'version' 	=> ALM_CTA_VERSION,
   			'license' 	=> $license_key,
   			'item_id'   => ALM_CTA_ITEM_NAME,
   			'author' 	=> 'Darren Cooney'
   		)
   	);
   }
}
add_action( 'admin_init', 'alm_cta_plugin_updater', 0 );
/* End Software Licensing */
