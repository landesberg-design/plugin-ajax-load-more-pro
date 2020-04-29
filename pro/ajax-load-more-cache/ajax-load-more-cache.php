<?php
/*
Plugin Name: Ajax Load More: Cache
Plugin URI: http://connekthq.com/plugins/ajax-load-more/cache/
Description: Ajax Load More extension that creates static HTML files from ajax requests.
Author: Darren Cooney
Twitter: @KaptonKaos
Author URI: http://connekthq.com
Version: 1.6.0
License: GPL
Copyright: Darren Cooney & Connekt Media
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define('ALM_CACHE_VERSION', '1.6.0');
define('ALM_CACHE_RELEASE', 'December 28, 2018');


/*
*  alm_cache_install
*  Install the Cache add-on
*
*  @since 1.0
*  @updated 1.4
*/
register_activation_hook( __FILE__, 'alm_cache_install' );
function alm_cache_install() {

   if(!is_plugin_active('ajax-load-more/ajax-load-more.php')){	//if Ajax Load More is activated

   	die(__('You must install and activate <a href="https://wordpress.org/plugins/ajax-load-more/">Ajax Load More</a> before installing the Ajax Load More Cache add-on.', 'ajax-load-more-cache'));

	}else{

      $upload_dir = wp_upload_dir();
      $dir = $upload_dir['basedir'].'/alm-cache';

      // Create alm-cache directory if does not exist
      if(!is_dir($dir)){
         wp_mkdir_p($dir);
         //mkdir($dir) or die(__('Error creating cache directory. Please contact your hosting administrator.', 'ajax-load-more-cache'));
      }

      // Test directory access
      if(!is_writable($dir)){
         die(__('Error accessing uploads/alm-cache directory. This add-on is required to read/write to your server. Please contact your hosting administrator.', 'ajax-load-more-cache'));
      }
	}
}



if( !class_exists('ALMCache') ):
   class ALMCache{
   	function __construct(){
         $this->constants();

   		add_action( 'alm_cache_installed', array(&$this, 'alm_cache_installed') );
   		add_action( 'alm_clear_cache', array(&$this, 'alm_clear_cache') );
   	   add_filter( 'alm_cache_inc', array(&$this, 'alm_cache_inc' ), 10, 7 );
   	   add_filter( 'alm_cache_file', array(&$this, 'alm_cache_file' ), 10, 5 );
   	   add_filter( 'alm_get_cache_array', array(&$this, 'alm_get_cache_array' ), 10, 2 );
   	   add_filter( 'alm_previous_post_cache_file', array(&$this, 'alm_previous_post_cache_file' ), 10, 3 );
   	   add_filter( 'alm_nextpage_cache_file', array(&$this, 'alm_nextpage_cache_file' ), 10, 3 );
   	   add_filter( 'alm_cache_create_dir', array(&$this, 'alm_cache_create_dir' ), 10, 3 );
         add_action( 'wp_ajax_alm_delete_cache', array(&$this, 'alm_delete_cache') ); // Delete Cache
   	   add_action( 'init', array(&$this, 'alm_cache_create_publish_actions') ); // Publish actions
   	   add_action( 'admin_bar_menu', array(&$this, 'alm_add_toolbar_items'), 100 );
   		add_action( 'alm_cache_settings', array(&$this, 'alm_cache_settings') );
   		add_filter( 'alm_cache_shortcode', array(&$this, 'alm_cache_shortcode'), 10, 3 );
         add_action( 'admin_head', array(&$this, 'alm_cache_vars') ); // Localized Cache Variables
   		load_plugin_textdomain( 'ajax-load-more-cache', false, dirname(plugin_basename( __FILE__ )).'/lang/'); //load text domain

   	}


      /*
      *  constants
      *  Include these files in the admin
      *
      *  @since 1.4
      */

      private function constants(){
      	define('ALM_CACHE_ADMIN_PATH', plugin_dir_path(__FILE__)); // Plugin Dir Path
      	define('ALM_CACHE_ADMIN_URL', plugins_url('', __FILE__)); // Plugin URL
      }



      /*
      *  alm_get_cache_array
      *  Get array of cache items to prebuild
      *
      *  @return array;
      *  @since 1.6
      */
      public static function alm_get_cache_array(){
         $array = apply_filters('alm_cache_array', '');
         $return = (is_array($array)) ? json_encode($array) : null;
         return $return;
      }



      /*
      *  alm_get_cache_path
      *  Get absolute path to cache directory path
      *
      *  @return $path;
      *  @since 1.5
      */
      public static function alm_get_cache_path(){
         $upload_dir = wp_upload_dir();
         $path = apply_filters('alm_cache_path', $upload_dir['basedir'].'/alm-cache/');
         return $path;
      }



      /*
      *  alm_get_cache_url
      *  Get cache directory URL
      *
      *  @return $path;
      *  @since 1.5
      */
      public static function alm_get_cache_url(){
         $upload_dir = wp_upload_dir();
         $path = apply_filters('alm_cache_url', $upload_dir['baseurl'].'/alm-cache/');
         return $path;
      }



   	/*
   	*  alm_enqueue_cache_admin_scripts
   	*  Enqueue cache admin js
   	*
   	*  @since 1.3.1
   	*/
   	public static function alm_enqueue_cache_admin_scripts(){	   	
      	wp_enqueue_style( 'alm-cache-css', ALM_CACHE_ADMIN_URL. '/admin/css/cache.css');
      	wp_enqueue_script( 'alm-cache-admin', ALM_CACHE_ADMIN_URL. '/admin/js/alm-cache.js', array( 'jquery' ));
   	}



   	/*
   	*  alm_cache_shortcode
   	*  Build Cache shortcode params and send back to core ALM
   	*
   	*  @since 1.2
   	*/

   	function alm_cache_shortcode($cache, $cache_id, $options){
   		$return = ' data-cache="'.$cache.'"';
		   $return .= ' data-cache-id="'.$cache_id.'"';
         $return .= ' data-cache-path="'. ALMCache::alm_get_cache_url() .'"';

			// Cache auto generate
			$autoGenerate = (isset($_GET['auto'])) ? true : false;
			
         // Check for known users
         if(isset($options['_alm_cache_known_users']) && $options['_alm_cache_known_users'] === '1' && is_user_logged_in() && !$autoGenerate){
		   	$return .= ' data-cache-logged-in="true"';
		   }

		   return $return;
   	}



   	/*
   	*  alm_cache_settings
   	*  Create the Cache settings panel.
   	*
   	*  @since 1.2
   	*/

   	function alm_cache_settings(){
      	register_setting(
      		'alm_cache_license',
      		'alm_cache_license_key',
      		'alm_cache_sanitize_license'
      	);
	   	add_settings_section(
	   		'alm_cache_settings',
	   		__('Cache Settings', 'ajax-load-more-cache' ),
	   		'alm_cache_settings_callback',
	   		'ajax-load-more'
	   	);
	   	add_settings_field(
	   		'_alm_cache_publish',
	   		__('Published Posts', 'ajax-load-more-cache' ),
	   		'_alm_cache_publish_callback',
	   		'ajax-load-more',
	   		'alm_cache_settings'
	   	);
	   	add_settings_field(
	   		'_alm_cache_known_users',
	   		__('Known Users', 'ajax-load-more-cache' ),
	   		'_alm_cache_known_users_callback',
	   		'ajax-load-more',
	   		'alm_cache_settings'
	   	);
	   }



   	/*
   	*  alm_cache_create_publish_actions
   	*  Create the publish actions for when new posts are added
   	*
   	*  @since 1.0
   	*/

   	function alm_cache_create_publish_actions(){
         $pt_args = array('public' => true);
         $types = get_post_types($pt_args);
         if($types){
      	   foreach( $types as $type ){
      	      $typeobj = get_post_type_object( $type );
               $name = $typeobj->name;
               if( $name != 'revision' && $name != 'attachment' && $name != 'nav_menu_item' && $name != 'acf'){
                  add_action( 'publish_'.$name.'', array(&$this, 'alm_cache_post_published') );
               }
            }
         }
         add_action( 'future_to_publish', array(&$this, 'alm_cache_post_published') );
	   }



   	/*
   	*  alm_cache_create_dir
   	*  Create the cache directory by id and store data about cache in .txt file
   	*
   	*  @since   1.0
   	*  @updated 1.4
   	*/

   	function alm_cache_create_dir($cache_id, $url){

      	// Test upload directory before creating files
      	$path = ALMCache::alm_get_cache_path();

         // Create alm-cache directory if does not exist
         if(!is_dir($path)){             
            wp_mkdir_p($path);
            //mkdir($path) or die(__('Error creating cache directory. Please contact your hosting administrator.', 'ajax-load-more-cache'));
      	}

         $cdir = $path . $cache_id;
         // make the directory and text file to store data
         if(!is_dir($cdir)){             
            wp_mkdir_p($cdir);
            //mkdir($cdir) or die(__('Error creating cache directory. Please contact your hosting administrator.', 'ajax-load-more-cache'));

            // Create text file
            $txtfile = fopen($cdir . '/_info.txt', 'w') or die(__('Unable to create text file!', 'ajax-load-more-cache'));

            // Set $data and write to file
            $data = array(
               'url' => $url,
               'created' => date('Y-m-d H:i:s')
            );

            fwrite($txtfile, serialize($data)) or die(__('Unable to write to text file. Please contact your hosting administrator.', 'ajax-load-more-cache'));
            fclose($txtfile);
         }

	   }



   	/*
   	*  alm_cache_inc
   	*  Get repeater file and store it for caching
   	*
   	*  @since 1.0
   	*/

   	function alm_cache_inc($repeater, $type, $theme_repeater, $alm_page, $alm_found_posts, $alm_item, $alm_current){
	   	ob_start();
			if($theme_repeater != 'null' && has_filter('alm_get_theme_repeater')){
				do_action('alm_get_theme_repeater', $theme_repeater, $alm_found_posts, $alm_page, $alm_item, $alm_current);
			}else{
				include( alm_get_current_repeater($repeater, $type) );
			}
			$return = ob_get_contents();
			ob_end_clean();
			return $return;
	   }



   	/*
   	*  alm_cache_file
   	*  Create the cached file and write it to uploads/alm-cache
   	*
   	*  @since 1.0
   	*  @updated 1.3.0
   	*/

   	function alm_cache_file($cache_id, $page, $start_page, $data, $preloaded){

      	$path = ALMCache::alm_get_cache_path();
         $dir = $path . $cache_id;

         $firstpage = '1';


			// Added the $page < $start_page today


         if($start_page > 1 && $page < $start_page){
            $cached_file = fopen($dir . '/page-'.''. $firstpage .'-'. $start_page .'.html', 'w') or die(__('Error opening file - please contact your hosting administrator.', 'ajax-load-more-cache'));
         } else {
            $cached_file = fopen($dir . '/page-' . ($page + 1) .'.html', 'w') or die(__('Error opening file - please contact your hosting administrator.', 'ajax-load-more-cache'));
         }

         fwrite($cached_file, $data) or die(__('Error writing to cache file. Please contact your hosting administrator.', 'ajax-load-more-cache'));

         /*
          *	alm_cache_created
          *
          * ALM Cache Hook
          * Dispatched after cache has been created
          *
          * Since 1.6
          */
         do_action('alm_cache_created');
	   }



   	/*
   	*  alm_previous_post_cache_file
   	*  Create the cached file for Previous Post Add-on and write it to cache dir.
   	*
   	*
   	*  @param $cache_id   string
   	*  @param $slug   string
   	*  @param $data   string
   	*  @since 1.5.0
   	*/

   	function alm_previous_post_cache_file($cache_id, $slug, $data){

         $path = ALMCache::alm_get_cache_path();

         $dir = $path . $cache_id;

         $cached_file = fopen($dir . '/'. $slug .'.html', 'w') or die(__('Error opening file - please contact your hosting administrator.', 'ajax-load-more-cache'));

         fwrite($cached_file, $data) or die(__('Error writing to cache file. Please contact your hosting administrator.', 'ajax-load-more-cache'));

         /*
          *	alm_cache_created
          *
          * ALM Cache Hook
          * Dispatched after cache has been created
          *
          * Since 1.6
          */
         do_action('alm_cache_created');
	   }



   	/*
   	*  alm_nextpage_cache_file
   	*  Create the cached file for Nextpage Add-on and write it to cache dir.
   	*
   	*
   	*  @param $cache_id   string
   	*  @param $page   string
   	*  @param $data   string
   	*  @since 1.4.0
   	*/

   	function alm_nextpage_cache_file($cache_id, $page, $data){

         $path = ALMCache::alm_get_cache_path();

         $dir = $path . $cache_id;

         $cached_file = fopen($dir . '/page-'.''. $page .'.html', 'w') or die(__('Error opening file - please contact your hosting administrator.', 'ajax-load-more-cache'));

         fwrite($cached_file, $data) or die(__('Error writing to cache file. Please contact your hosting administrator.', 'ajax-load-more-cache'));

         /*
          *	alm_cache_created
          *
          * ALM Cache Hook
          * Dispatched after cache has been created
          *
          * Since 1.6
          */
         do_action('alm_cache_created');
	   }



   	/*
   	*  alm_cache_post_published
   	*  Call this function when posts are published to determine if we should flush the cache
   	*
   	*  @since 1.0
   	*/

   	function alm_cache_post_published( ){

   	   $options = get_option( 'alm_settings' ); //Get plugin options

         if($options['_alm_cache_publish'] === '1'){

            $path = ALMCache::alm_get_cache_path();

            // Confirm directory exists
            if(!is_dir( $path )) return;

            // loop each dir

            $delete_count = 0;
         	foreach (new DirectoryIterator($path) as $directory) {
               if ($directory->isDot())  continue;

               if ($directory->isDir()){
                  $file = $path . $directory;
                  if (is_dir($file)) {
                     foreach (glob($file."/*.*") as $filename) {
                         if (is_file($filename)) {
                             unlink($filename);
                         }
                     }
                     rmdir($file);
                     $delete_count++;
                  }
               }
         	}

         	if($delete_count > 0){

               /*
                *	alm_cache_deleted
                *
                * ALM Cache Hook
                * Dispatched after cache has been deleted
                *
                * Since 1.6
                */
               do_action('alm_cache_deleted');
         	}

         }
   	}



   	/*
   	*  alm_cache_installed
   	*  an empty function to determine if cache is activated.
   	*
   	*  @since 1.0
   	*/

   	function alm_cache_installed(){
   	   //Empty return
   	}



   	/*
   	*  alm_delete_cache
   	*  Delete cache action.
   	*
   	*  @since 1.6
   	*/

   	function alm_clear_cache(){
      	if (current_user_can( apply_filters('alm_custom_user_role', 'edit_theme_options') )){
         	self::alm_delete_full_cache();
         }
   	}



   	/*
      *  alm_delete_cache (Ajax)
      *  Delete individual cached items
      *
      *  @return   null
      *  @since 1.0
      */

      function alm_delete_cache(){

      	if (current_user_can( apply_filters('alm_custom_user_role', 'edit_theme_options') )){

      		$nonce = $_POST["nonce"];
      		$cache = $_POST["cache"];

      		// Check the nonce, don't match then bounce!
      		if (! wp_verify_nonce( $nonce, 'alm_cache_nonce' ))
      			die(__('Error - Unable to verify nonce.', 'ajax-load-more-cache'));

      		$path = ALMCache::alm_get_cache_path();
      		$dir = $path .''. $cache;

      		if (is_dir($dir)) {
      	      foreach (glob($dir."/*.*") as $filename) {
      	          if (is_file($filename)) {
      	              unlink($filename);
      	          }
      	      }
      	      rmdir($dir);

      	      /*
                *	alm_cache_deleted
                *
                * ALM Cache Hook
                * Dispatched after cache has been deleted
                *
                * Since 1.6
                */
      	      do_action('alm_cache_deleted');
      	   }

      	}

      	wp_die();
      }



   	/*
      *  alm_delete_full_cache 
      *  Delete entire ALM cache
      *
      *  @return   string
      *  @since 1.6
      */

      public static function alm_delete_full_cache(){

      	if (current_user_can( apply_filters('alm_custom_user_role', 'edit_theme_options') )){

      		$path = ALMCache::alm_get_cache_path();
		   	foreach (new DirectoryIterator($path) as $directory) {
		         if ($directory->isDot())  continue;

		         if ($directory->isDir()){
		            $path_to_directory = $path . $directory;
		            self::alm_cache_rmdir($path_to_directory);
		         }
		   	}

		   	// Hook dispatched after cache has been deleted
		   	do_action('alm_cache_deleted');
		   	return __('Cache deleted successfully', 'ajax-load-more-cache');

      	}

      	wp_die();
      }
      


      /*
      *  alm_cache_rmdir
      *  Remove cache directory and files
      *
      *  @return   null
      *  @since 1.6
      */

		public static function alm_cache_rmdir($path_to_directory) {

			if (current_user_can( apply_filters('alm_custom_user_role', 'edit_theme_options') )){
				$file = $path_to_directory;
				if (is_dir($file)) {
					foreach (glob($file."/*.*") as $filename) {
						if (is_file($filename)) {
							unlink($filename);
						}
					}
					rmdir($file);
				}
			}

		}



      /*
      *  alm_cache_vars
      *  Create admin variables for cache add-on
      *
      *  @since 1.2.2
      */
      function alm_cache_vars() { ?>
          <script type='text/javascript'>
      	 /* <![CDATA[ */
          var alm_cache_localize = <?php echo json_encode( array(
              'ajax_admin_url' => admin_url( 'admin-ajax.php' ),
              'alm_cache_nonce' => wp_create_nonce( 'alm_cache_nonce' )
          )); ?>
          /* ]]> */
          </script>
      <?php }



   	/*
   	*  alm_add_toolbar_items
   	*  Create admin bar menu
   	*
   	*  @since 1.0
   	*/
   	function alm_add_toolbar_items($admin_bar){
   		if ( !is_super_admin() || !is_admin_bar_showing() )
   			return;

         $admin_bar->add_menu( array(
           'id'    => 'alm-cache',
           'title' => 'ALM - Cache',
           'href'  => admin_url('admin.php?page=ajax-load-more-cache'),
           'meta'  => array(
               'title' => __('Ajax Load More Cache', 'ajax-load-more-cache'),
           ),
         ));
         $admin_bar->add_menu( array(
            'id'    => 'alm-cache-delete',
            'parent' => 'alm-cache',
            'title' => __('Delete Cache', 'ajax-load-more-cache'),
            'href'  => admin_url('admin.php?page=ajax-load-more-cache&action=delete'),
            'meta'  => array(
            	'title' => __('Delete Cache', 'ajax-load-more-cache'),
            	'target' => '_self',
            ),
         ));
         $generate_cache = ALMCACHE::alm_get_cache_array();
         if($generate_cache){
	         $admin_bar->add_menu( array(
					'id'    => 'alm-cache-build',
					'parent' => 'alm-cache',
					'title'  => __('Auto-Generate Cache', 'ajax-load-more-cache'),
					'href'  => admin_url('admin.php?page=ajax-load-more-cache&action=build'),
					'meta'  => array(
            		'title' => __('Generate Cache', 'ajax-load-more-cache'),
						'target' => '_self',
					),
				));
         }
      }
   }


   /*
	*  alm_cache_settings_callback
	*  Cache Setting Heading
	*
	*  @since 2.6.0
	*/

	function alm_cache_settings_callback() {
	   $html = '<p>' . __('Customize your installation of the <a href="http://connekthq.com/plugins/ajax-load-more/cache/">Cache</a> add-on.', 'ajax-load-more-cache') . '</p>';

	   echo $html;
	}



	/*
	*  _alm_cache_publish_callback
	*  Clear cache when a new post is published
	*
	*  @since 2.6.0
	*/

	function _alm_cache_publish_callback() {

	   $options = get_option( 'alm_settings' );

		if(!isset($options['_alm_cache_publish']))
		   $options['_alm_cache_publish'] = '0';

		$html = '<input type="hidden" name="alm_settings[_alm_cache_publish]" value="0" /><input type="checkbox" id="alm_cache_publish" name="alm_settings[_alm_cache_publish]" value="1"'. (($options['_alm_cache_publish']) ? ' checked="checked"' : '') .' />';
		$html .= '<label for="alm_cache_publish">'.__('Delete cache when new posts are published.', 'ajax-load-more-cache');
		$html .= '<span style="display:block">'.__('Cache will be fully cleared whenever a post, page or Custom Post Type is published or updated.', 'ajax-load-more-cache').'</span>';
		$html .=' </label>';


		echo $html;

	}



	/*
	*  _alm_cache_known_users_callback
	*  Don't cache files for known users
	*
	*  @since 2.6.0
	*/

	function _alm_cache_known_users_callback() {

	   $options = get_option( 'alm_settings' );

		if(!isset($options['_alm_cache_known_users']))
		   $options['_alm_cache_known_users'] = '0';

		$html = '<input type="hidden" name="alm_settings[_alm_cache_known_users]" value="0" /><input type="checkbox" id="alm_cache_known_users" name="alm_settings[_alm_cache_known_users]" value="1"'. (($options['_alm_cache_known_users']) ? ' checked="checked"' : '') .' />';
		$html .= '<label for="alm_cache_known_users">'.__('Don\'t cache files for logged in users.', 'ajax-load-more-cache');
		$html .= '<span style="display:block">'.__('Logged in users will retrieve content directly from the database and will not view any cached content.', 'ajax-load-more-cache').'</span>';
		$html .=' </label>';

		echo $html;

	}


   /*
   *  alm_cache_sanitize_license
   *  Sanitize our license activation
   *
   *  @since 1.3.0
   */

   function alm_cache_sanitize_license( $new ) {
   	$old = get_option( 'alm_cache_license_key' );
   	if( $old && $old != $new ) {
   		delete_option( 'alm_cache_license_status' ); // new license has been entered, so must reactivate
   	}
   	return $new;
   }



   /*
   *  ALMCache
   *  The main function responsible for returning Ajax Load More CACHE.
   *
   *  @since 1.0
   */

   function ALMCache(){
   	global $alm_cache;

   	if( !isset($alm_cache) ){
   		$alm_cache = new ALMCache();
   	}

   	return $alm_cache;
   }

   // initialize
   ALMCache();

endif; // class_exists check


/* Software Licensing */

function alm_cache_plugin_updater() {
	if(!has_action('alm_pro_installed') && class_exists('EDD_SL_Plugin_Updater')){ // Don't check for updates if Pro is activated
		$license_key = trim( get_option( 'alm_cache_license_key' ) ); // retrieve our license key from the DB
		$edd_updater = new EDD_SL_Plugin_Updater( ALM_STORE_URL, __FILE__, array(
				'version' 	=> ALM_CACHE_VERSION,
				'license' 	=> $license_key,
				'item_id'   => ALM_CACHE_ITEM_NAME,
				'author' 	=> 'Darren Cooney'
			)
		);
	}
}
add_action( 'admin_init', 'alm_cache_plugin_updater', 0 );

/* End Software Licensing */
