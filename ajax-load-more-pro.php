<?php
/*
Plugin Name: Ajax Load More: Pro
Plugin URI: https://connekthq.com/plugins/ajax-load-more/pro/
Description: All the add-ons for Ajax Load More in a single installation.
Author: Darren Cooney
Twitter: @KaptonKaos
Author URI: https://connekthq.com
Version: 1.0.23
License: GPL
Copyright: Darren Cooney & Connekt Media
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


define('ALM_PRO_VERSION', '1.0.23');
define('ALM_PRO_RELEASE', 'December 17, 2019');



/*
*  alm_pro_install
*  Plugin installation hook
*
*  @since 1.0
*/

register_activation_hook( __FILE__, 'alm_pro_install' );
function alm_pro_install() {
   if(!is_plugin_active('ajax-load-more/ajax-load-more.php')){	//if Ajax Load More is activated
   	die(__('You must install and activate <a href="https://wordpress.org/plugins/ajax-load-more/">Ajax Load More</a> before installing Ajax Load More Pro.', 'ajax-load-more-pro'));
	} else {

		global $ajax_load_more;

		if(!$ajax_load_more) return false;

		// Loop all addons
	   foreach($ajax_load_more->alm_return_addons() as $plugin){

		   // Check if standalone addon is active
		   if(is_plugin_active($plugin['path'] .'/'. $plugin['path'] .'.php')){
      		deactivate_plugins($plugin['path'] .'/'. $plugin['path'] .'.php'); // deactivate it.
      	}

		   // Set status option.
		   if(!get_option('alm_pro_status_'. $plugin['slug'])){
		   	update_option('alm_pro_status_'. $plugin['slug'], 'active');
		   }
	   }
	}
}


if( !class_exists('ALMPro') ):

   class ALMPro{

   	function __construct(){

   		add_action( 'alm_pro_installed', array(&$this, 'alm_pro_installed') );
   		add_action( 'plugins_loaded', array(&$this, 'alm_pro_load_addons') );
         add_action( 'wp_ajax_alm_pro_toggle_activation', array(&$this, 'alm_pro_toggle_activation') );
   		load_plugin_textdomain( 'ajax-load-more-pro', false, dirname(plugin_basename( __FILE__ )).'/lang/');
			$this->constants();
   	}



   	/*
      *  constants
      *  Include these files in the admin
      *
      *  @since 1.0
      */

      private function constants(){
      	define('ALM_PRO_ADMIN_PATH', plugin_dir_path(__FILE__)); // Plugin Dir Path
      	define('ALM_PRO_ADMIN_URL', plugins_url('', __FILE__)); // Plugin URL
      	define('ALM_PRO_OPTION_PREFIX', 'alm_pro_status_');

      }



   	/*
   	*  alm_pro_load_addons
   	*  Include these addons at runtime
   	*
   	*  @since 1.0
   	*/

		function alm_pro_load_addons(){

			global $ajax_load_more;

			if(!$ajax_load_more) return false;

			// Loop all addons
		   foreach($ajax_load_more->alm_return_addons() as $plugin){
			   if(!has_action($plugin['action'])){
				   if(get_option(ALM_PRO_OPTION_PREFIX . $plugin['slug']) === 'active'){
         			include_once('pro/'. $plugin['path'] .'/'. $plugin['path'] .'.php');
         		}
         	}
		   }
      }


      /*
   	*  alm_enqueue_pro_admin_scripts
   	*  Enqueue pro admin js
   	*
   	*  @since 1.0
   	*/
   	public static function alm_enqueue_pro_admin_scripts(){
      	wp_enqueue_script( 'alm-pro-admin', ALM_PRO_ADMIN_URL. '/admin/js/ajax-load-more-pro.js', array( 'jquery' ));
   	}



   	/*
      *  alm_pro_toggle_activation
      *  Toggle active/inactive add-on states
      *
      *  @return   null
      *  @since 1.0
      */

      function alm_pro_toggle_activation(){

			$nonce = $_POST["nonce"];
   		$slug = $_POST['slug'];

      	if ($slug && current_user_can( apply_filters('alm_custom_user_role', 'edit_theme_options') )){

      		// Check the nonce, don't match then bounce!
      		if (! wp_verify_nonce( $nonce, 'alm_repeater_nonce' ))
      			die(__('Error - Unable to verify nonce.', 'ajax-load-more-pro'));


				if(get_option(ALM_PRO_OPTION_PREFIX . $slug) !== 'active'){
					$result = 'active';
					update_option(ALM_PRO_OPTION_PREFIX . $slug, $result);
				} else {
					$result = 'inactive';
					update_option(ALM_PRO_OPTION_PREFIX . $slug, $result);
				}

				$return = array(
					'success' => true,
					'slug' => $slug,
					'result' => $result,
					'msg' => __('Add-on status updated', 'ajax-load-more-pro')
				);

				wp_send_json($return);

      	} else {

	      	$return = array(
					'success' => false,
					'slug' => $slug,
					'result' => $result,
					'msg' => __('Add-on status NOT updated', 'ajax-load-more-pro')
				);

				wp_send_json($return);

      	}

      	wp_die();

      }



   	/*
   	*  alm_pro_installed
   	*  an empty function to determine if pro is true.
   	*
   	*  @since 1.0
   	*/

   	function alm_pro_installed(){
   	   //Empty return
   	}



   	/*
   	*  alm_pro_settings
   	*  Create the settings panel.
   	*
   	*  @since 1.0
   	*/

   	function alm_pro_settings(){
      	register_setting(
      		'alm_pro_license',
      		'alm_pro_license_key',
      		'alm_pro_sanitize_license'
      	);
			add_settings_section(
	   		'alm_pro_settings',
	   		'Pro Settings',
	   		'alm_pro_settings_callback',
	   		'ajax-load-more'
	   	);
   	}
   }


   /*
   *  alm_pro_sanitize_license
   *  Sanitize our license activation
   *
   *  @since 1.0
   */

   function alm_pro_sanitize_license( $new ) {
   	$old = get_option( 'alm_pro_license_key' );
   	if( $old && $old != $new ) {
   		delete_option( 'alm_proe_license_status' ); // new license has been entered, so must reactivate
   	}
   	return $new;
   }



   /*
   *  ALMPro
   *  The main function responsible for returning Ajax Load More Users.
   *
   *  @since 1.0
   */

   function ALMPro(){
   	global $ALMPro;
   	if( !isset($ALMPro) ){
   		$ALMPro = new ALMPro();
   	}
   	return $ALMPro;
   }
   ALMPro(); // initialize


endif; // class_exists check




/* Software Licensing */
function alm_pro_plugin_updater() {
	$license_key = trim( get_option( 'alm_pro_license_key' ) );

	if(!class_exists('EDD_SL_Plugin_Updater')) return false;

	$edd_updater = new EDD_SL_Plugin_Updater( ALM_STORE_URL, __FILE__, array(
			'version' 	=> ALM_PRO_VERSION,
			'license' 	=> $license_key,
			'item_id'   => ALM_PRO_ITEM_NAME,
			'author' 	=> 'Darren Cooney'
		)
	);
}
add_action( 'admin_init', 'alm_pro_plugin_updater', 0 );
/* End Software Licensing */
