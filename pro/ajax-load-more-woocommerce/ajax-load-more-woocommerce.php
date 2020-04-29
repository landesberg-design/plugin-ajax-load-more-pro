<?php
/*
Plugin Name: Ajax Load More: WooCommerce
Plugin URI: https://connekthq.com/plugins/ajax-load-more/add-ons/woocommerce/
Description: Ajax Load More addons for integrating WooCommerce.
Author: Darren Cooney
Twitter: @KaptonKaos
Author URI: http://connekthq.com
Version: 1.0.0
Copyright: Darren Cooney & Connekt Media
*/


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


define('ALM_WOO_VERSION', '1.0.0');
define('ALM_WOO_RELEASE', 'April 22, 2020');


/**
 * alm_woo_install
 * Plugin activation hook
 * @since 1.0
 */
function alm_woo_install() {   
   //if Ajax Load More is activated
   if(!is_plugin_active('ajax-load-more/ajax-load-more.php')){	
   	die( __('You must install and activate <a href="https://wordpress.org/plugins/ajax-load-more/">Ajax Load More</a> before installing the ALM WooCommerce Add-on.', 'alm-woocommerce') );
	}
	
	if(!alm_is_woo_activated()){
   	die( __('WooCommerce must be installed and activated in order to use Ajax Load More WooCommerce Add-on', 'alm-woocommerce') );
	}
	
}
register_activation_hook( __FILE__, 'alm_woo_install' );



/**
 * alm_is_woo_activated
 * Is WooCommerce activated
 */
function alm_is_woo_activated(){
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		return true;	
	} else {
		return false;
	}
}



if( !class_exists('ALMWooCommerce') ):

   class ALMWooCommerce{	    
	     
   	function __construct(){		   	
	   	
   		define('ALM_WOO_PATH', plugin_dir_path(__FILE__)); 
			define('ALM_WOO_URL', plugins_url('', __FILE__));	
			define('ALM_WOO_PREFIX', 'alm_woo_');
			   	
   		add_action( 'alm_woocommerce_installed', array(&$this, 'alm_woocommerce_installed' ));
   		add_action( 'wp_enqueue_scripts', array(&$this, 'alm_woocommerce_enqueue_scripts' )); 
   		add_filter( 'alm_woocommerce_init', array(&$this, 'alm_woocommerce_init' ), 10, 3);
   		add_action( 'woocommerce_before_shop_loop', array(&$this, 'alm_woocommerce_before_shop_loop' ));
   		add_action( 'woocommerce_after_shop_loop', array(&$this, 'alm_woocommerce_after_shop_loop' )); 
   		add_action( 'alm_woocommerce_settings', array(&$this, 'alm_woocommerce_settings' ));
   		$this->includes();
   		
   		load_plugin_textdomain( 'alm-woocommerce', false, dirname(plugin_basename( __FILE__ )).'/lang'); //load text domain
			   		       		
   	}
   	
   	
   	/**
   	 * includes
   	 * Load these files before the plugin loads
   	 *
   	 * @since 1.0
   	 */
   	public function includes(){
	   	if(alm_is_woo_activated()){
   			require_once('core/functions.php');
   			require_once('admin/customizer/customizer.php');
   		}
      }
   	
	   
   	
   	/**
   	 * alm_woocommerce_after_shop_loop
   	 * Set up ALM shortcode and params
   	 *
   	 * @since 1.0
   	 */
   	public function alm_woocommerce_after_shop_loop(){
	   	
	   	if(!alm_is_woo_archive()){ // WooCommerce Archive
		   	return false;	
		   }
	   	
	   	if( !alm_woo_is_shop_enabled() ){ // Shop
		   	return false;
	   	}
	   		
			if( !alm_woo_is_shop_archive_enabled() ){ // Shop Archives
		   	return false;
	   	}
	   		
			if( !alm_woo_is_shop_search_enabled() ){ // Product Search
		   	return false;
	   	}
	   	
	   	
	   	// Configuration
			$woo_config = array(
				'post_type' => 'product',
				'container_element' => 'div',
				'classes' 	=> 'stylefree',
				'columns' 	=> alm_woo_get_loop_prop('columns', '3'),
				'per_page' 	=> alm_woo_get_loop_prop('per_page', 6),
			);
			
			
			/**
			 *	alm_woo_config
			 *
			 * WooCommerce hook to filter columns, per_page, classes etc
			 *
			 * @return $config;
			 */	
			$woo_config = apply_filters('alm_woocommerce_config', $woo_config);
			
			$orderby = apply_filters('alm_woocommerce_orderby', 'menu_order title');
			
			
			// Default $args
			$args = array(
				'id' => 'alm_woocommerce',
				'woo' => 'true',
				'post_type' => $woo_config['post_type'],
				//'offset' => $woo_config['per_page'],
				'posts_per_page' => $woo_config['per_page'],
				'pause' => 'true',
				'order' => 'ASC',
				'orderby' => $orderby, 
				'container_type' => $woo_config['container_element'],
				'css_classes' => $woo_config['classes']
			);
			
			
			// Loading Style
			$loading_style = ALMWooCustomizer::loading_style();
			$args['loading_style'] = ( get_option( ALM_WOO_PREFIX. 'button_style', $loading_style ) === 'default' ) ? 'default' : get_option( ALM_WOO_PREFIX. 'button_style', $loading_style );
			$is_infinite = (strpos($args['loading_style'], 'infinite') !== false) ? true : false;
			
			
			// Button Labels
			$args['button_label'] = ( get_option( ALM_WOO_PREFIX. 'button_label' ) ) ? get_option( ALM_WOO_PREFIX. 'button_label' ) : ALMWooCustomizer::default_button_label();
			$loading_label = (get_option(ALM_WOO_PREFIX. 'button_loading_label')) ? get_option(ALM_WOO_PREFIX. 'button_loading_label') : ALMWooCustomizer::default_button_loading_label();
			
			if($loading_label && !$is_infinite){
				$args['button_loading_label'] = $loading_label;
			}
			
			
			// Scroll, Distance & Override
			$scroll = ( get_option( ALM_WOO_PREFIX. 'scroll', 'true' ) === 'false' ) ? 'false' : 'true';
			$scroll_override = ( get_option( ALM_WOO_PREFIX. 'scroll_override', 'true' ) === 'false' ) ? 'false' : 'true';
			$scroll_distance = ( get_option( ALM_WOO_PREFIX. 'scroll_distance') ) ? get_option( ALM_WOO_PREFIX. 'scroll_distance') : 100;
			
			if( $scroll === 'true' || $is_infinite ){
				// Scroll false OR loading infinite style
				$args['scroll'] = 'true';
				
				// Pause Override
				if($scroll_override === 'true'){
					$args['pause_override'] = 'true';
				} else {
					// If loading style is 'infinite'
					if( $is_infinite ){
						$args['pause_override'] = 'true';
					}
				}
				
				// Scroll Distance
				if($scroll_distance !== 100){
					$args['scroll_distance'] = (int) $scroll_distance;
				}
				
			} else {
				$args['scroll'] = 'false';
				
			}
			
			
			// Core WooCommerce Hook
			$args = apply_filters('alm_woocommerce_args', $args);
			
			
			// Render ALM
			alm_render($args);
			
   	}
   	
   	
   	
   	/**
   	 * alm_woocommerce_init
   	 * Start the ALM integration on WooCommerce pages
   	 *
   	 * @param {String} $id
   	 * @param {Array} $args
   	 * @since 1.0
   	 */
   	public function alm_woocommerce_init($id, $args){
	   	
			if(!alm_is_woo_archive()){ // Exit if not an archive page
				return false;
			}
			
			$posts_per_page = ($args['posts_per_page']) ? (int) $args['posts_per_page'] : 6;
			self::alm_woocommerce_set_localized_vars(null, $id, $posts_per_page);
			
	   }
	   
	   
	   
	   /**
	    * alm_woocommerce_set_localized_vars
	    * Set localized ALM variables for WooCommerce
   	 *s
   	 * @since 1.0
   	 */ 
	   public static function alm_woocommerce_set_localized_vars( $query, $id, $posts_per_page ){
		   
		   if( !alm_is_woo_archive() || !class_exists( 'ALM_LOCALIZE' )){ // Exit if not an archive page
			   return false;
		   }
		   
			   
		   $total_posts = wc_get_loop_prop( 'total' );
		   ALM_LOCALIZE::add_localized_var( 'total_posts', $total_posts, $id );
		   ALM_LOCALIZE::add_localized_var( 'post_count', 3, $id );
	   	
	   	
	   	// Create localized Paged URLs 
			$urlArray = array();
			$pages = ceil($total_posts / $posts_per_page);
			for($i = 1; $i <= $pages; $i++){
				array_push( $urlArray, htmlspecialchars_decode( get_pagenum_link($i) ) );
			}
			
	   	$params = array(
		   	'container' => apply_filters('alm_woocommerce_container', 'ul.products'),
		   	'products' => apply_filters('alm_woocommerce_products', '.product'),
		   	'results' => apply_filters('alm_woocommerce_results', '.woocommerce-result-count'),
		   	'columns' => alm_woo_get_loop_prop('columns', '3'),
		   	'paged' => wc_get_loop_prop('current_page'),
		   	'pages' => $pages,
		   	'paged_urls' => $urlArray,
	   	);
	   	
	   	
	   	$scrolltop = (get_option( ALM_WOO_PREFIX. 'scrolltop')) ? get_option(ALM_WOO_PREFIX. 'scrolltop') : 50;
	   	$scrolltop = apply_filters('alm_woocommerce_scrolltop', $scrolltop);
	   	
	   	$controls = (get_option( ALM_WOO_PREFIX. 'controls')) ? get_option(ALM_WOO_PREFIX. 'controls') : 'true';
	   	$controls = apply_filters('alm_woocommerce_controls', $controls);
	   	
	   	$params['settings'] = array(
		   	'scrolltop' => $scrolltop,
		   	'controls' => $controls
	   	);
	   	
	   	
   		// Previous Pages Link
   		if(wc_get_loop_prop('current_page') > 1){
	   		$page_link = apply_filters('alm_woocommerce_previous_link_sep', ' - ');
	   		$page_link .= '<a href="'. get_pagenum_link() .'" class="alm-woo-prev">';
	   			$page_link .= apply_filters('alm_woocommerce_previous_link', 'Previous Products');
	   		$page_link .= '</a>';
	   		
	   		$params['settings']['previous_page_link'] = $page_link;
	   		
   		}
	   	
	   	//alm_pretty_print($params);
	   	
	   	ALM_LOCALIZE::add_localized_var( 'woocommerce', $params, $id );
	   }
   	
   	
   	
   	/**
	    * alm_woocommerce_before_shop_loop
	    * Fired before the shop loop
   	 *
   	 * @since 1.0
   	 */ 
   	public function alm_woocommerce_before_shop_loop(){
	   		   	
	   	if(!alm_is_woo_archive()){ // Exit if not WooCommerce archive page
		   	return false;	
		   }
	   	
	   	if( !alm_woo_is_shop_enabled() ){ // Shop
		   	return false;
	   	}
	   		
			if( !alm_woo_is_shop_archive_enabled() ){ // Shop Archives
		   	return false;
	   	}
	   	
	   	
	   	$hide_pagination = alm_woo_hide_pagination();
   		$hide_orderby = alm_woo_hide_orderby();	
   		
   		$return = '<style>';
   		$return .= $hide_pagination; // Hide Pagination
   		$return .= $hide_orderby; // Hide Orderby (If set)
   		$return .= '</style>';
   		
   		echo $return;
   	}
	      	   	
   	
   	
   	/**
   	 * alm_woocommerce_installed
   	 * an empty function to determine if add-on is activated.
   	 *
   	 * @since 1.0
   	 */   	
   	public function alm_woocommerce_installed(){
   	   //Empty return
   	} 
   	
   	
   	
   	/**
   	 * alm_woocommerce_enqueue_scripts
   	 * Enqueue our scripts
   	 *
   	 * @since 1.0
   	 */   
   	public function alm_woocommerce_enqueue_scripts(){
	   	
	   	// Use minified libraries if SCRIPT_DEBUG is turned off
	   	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

   		// Enqueue JS
   		wp_register_script( 'ajax-load-more-woocommerce', plugins_url( '/core/js/alm-woocommerce'. $suffix .'.js', __FILE__ ), array('ajax-load-more'), ALM_WOO_VERSION, true );
          
   	}



   	/*
   	*  alm_woocommerce_settings
   	*  Create the WooCommerce settings panel.
   	*
   	*  @since 1.0
   	*/

   	function alm_woocommerce_settings(){
      	register_setting(
      		'alm_woocommerce_license',
      		'alm_woocommerce_license_key',
      		'alm_woocommerce_sanitize_license'
      	);
   	}
   	
   }
   	
   	
   /**
    * alm_tabs
    * The main function responsible for returning Ajax Load More Tabs.
    *
    * @since 1.0
    */	
   
   function ALMWooCommerce(){
   	global $ALMWooCommerce;   
   	if(!isset($ALMWooCommerce)){
   		$ALMWooCommerce = new ALMWooCommerce();
   	}   
   	return $ALMWooCommerce;
   }
   
   // initialize
   ALMWooCommerce();

endif; 




/* Software Licensing */
function alm_woocommerce_plugin_updater() {
   if(!has_action('alm_pro_installed') && class_exists('EDD_SL_Plugin_Updater')){ // Don't check for updates if Pro is activated
   	$license_key = trim( get_option( 'alm_woocommerce_license_key' ) ); // retrieve our license key from the DB
   	$edd_updater = new EDD_SL_Plugin_Updater( ALM_STORE_URL, __FILE__, array( 
   			'version' 	=> ALM_WOO_VERSION,
   			'license' 	=> $license_key,
   			'item_id'   => ALM_WOO_ITEM_NAME,
   			'author' 	=> 'Darren Cooney'
   		)
   	);
   }
}
add_action( 'admin_init', 'alm_woocommerce_plugin_updater', 0 );	
/* End Software Licensing */

