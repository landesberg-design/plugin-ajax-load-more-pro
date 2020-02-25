<?php
/*
Plugin Name: Ajax Load More: Next Page
Plugin URI: https://connekthq.com/plugins/ajax-load-more/add-ons/next-page/
Description: Ajax Load More add-on for displaying multipage WordPress content
Author: Darren Cooney
Twitter: @KaptonKaos 
Author URI: https://connekthq.com
Version: 1.4.2
License: GPL
Copyright: Darren Cooney & Connekt Media

*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define('ALM_NEXTPAGE_PATH', plugin_dir_path(__FILE__));
define('ALM_NEXTPAGE_URL', plugins_url('', __FILE__));
define('ALM_NEXTPAGE_VERSION', '1.4.2');
define('ALM_NEXTPAGE_RELEASE', 'December 17, 2019');



/**
* alm_nextpage_install
* Activation hook
*
* @since 1.0
*/

register_activation_hook( __FILE__, 'alm_nextpage_install' );
function alm_nextpage_install() {   
   //if Ajax Load More is activated
   if(!is_plugin_active('ajax-load-more/ajax-load-more.php')){	
   	die('You must install and activate <a href="https://wordpress.org/plugins/ajax-load-more/">Ajax Load More</a> before installing the ALM Nextpage Add-on.');
	}	
}



if( !class_exists('ALMNEXTPAGE') ):

   class ALMNEXTPAGE{	   
   	function __construct(){			
      	
   		add_action( 'alm_nextpage_installed', array(&$this, 'alm_nextpage_installed') );   		
   	   add_filter( 'alm_init_nextpage', array(&$this, 'alm_init_nextpage' ), 10, 6 );
   	   add_filter( 'alm_nextpage_wrap_start', array(&$this, 'alm_nextpage_wrap_start' ), 10, 6 );
   	   add_filter( 'alm_nextpage_wrap_end', array(&$this, 'alm_nextpage_wrap_end' ), 10, 1 );   	 	   
   	   add_action( 'wp_ajax_alm_nextpage', array(&$this, 'alm_nextpage_query') );
   	   add_action( 'wp_ajax_nopriv_alm_nextpage', array(&$this, 'alm_nextpage_query') );		
   		add_filter( 'alm_nextpage_shortcode', array(&$this, 'alm_nextpage_shortcode'), 10, 6 );    
   		add_filter( 'alm_nextpage_total_pages', array(&$this, 'alm_nextpage_total_pages'), 10, 3 );
   		add_filter( 'alm_nextpage_noscript_paging', array(&$this, 'alm_nextpage_noscript_paging'), 10 );
   		add_action( 'alm_nextpage_settings', array(&$this, 'alm_nextpage_settings') );
   		add_action( 'wp_enqueue_scripts', array(&$this, 'alm_nextpage_enqueue_scripts' )); 
   			
   	} 
   	
   	
   	/**
   	 * alm_nextpage_enqueue_scripts
   	 * Enqueue scripts
   	 *
   	 * @since 1.0
   	 */
   
   	function alm_nextpage_enqueue_scripts(){
	   	
	   	// Use minified libraries if SCRIPT_DEBUG is turned off
	   	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

   		wp_register_script( 'ajax-load-more-nextpage', plugins_url( '/js/alm-next-page'. $suffix .'.js', __FILE__ ), array('ajax-load-more'),  ALM_NEXTPAGE_VERSION, true );
   		
   		// Localize Nextpage Vars
   		wp_localize_script(
   			'ajax-load-more-nextpage',
   			'alm_nextpage_localize',
   			array(
   				'leading_slash' => self::get_leading_slash(),
   				'trailing_slash' => self::get_trailing_slash()
   			)
   		);
   	}
   	
   	
      
      /**
   	 *  get_leading_slash
   	 *  Get filter hook value
   	 *
   	 *  @since 1.1
   	 *  @return string;
   	 */
      public static function get_leading_slash(){
	      /*
			 *	alm_nextpage_leading_slash
			 * Add a leading slash (/) before the page number
			 */
	      return (apply_filters('alm_nextpage_leading_slash', false)) ? '/' : '';
      }
      
      
      
      /**
   	 * get_trailing_slash
   	 * Get filter hook value
   	 *
   	 * @since 1.1
   	 * @return string;
   	 */
      public static function get_trailing_slash(){
	      /*
			 *	alm_nextpage_remove_trailing_slash
			 * Remove the trailing slash (/) at the end of the URL
			 */
	      return (apply_filters('alm_nextpage_remove_trailing_slash', false)) ? '' : '/';
      }
      
      
      
      /**
   	 * alm_nextpage_query
   	 * Query nextpage, send results via ajax
   	 *
   	 * @since 1.0
   	 */
   	public function alm_nextpage_query(){
   	
   		if(!isset($_GET)){ return false; }
	
			error_reporting(E_ALL|E_STRICT);	  
		
		   $queryType = (isset($_GET['query_type'])) ? $_GET['query_type'] : 'standard';
		   $page = (isset($_GET['page'])) ? $_GET['page'] : 1;   		
		   $id = (isset($_GET['id'])) ? $_GET['id'] : '';   		
		   $data = (isset($_GET['nextpage'])) ? $_GET['nextpage'] : ''; // Nextpage data array - from ajax-load-more.js           
		   $paging = (isset($_GET['paging'])) ? $_GET['paging'] : 'false';         
		   $canonical_url = (isset($_GET['canonical_url'])) ? $_GET['canonical_url'] : $_SERVER['HTTP_REFERER'];
		   
		   // Cache Add-on
		   $cache_id = (isset($_GET['cache_id'])) ? $_GET['cache_id'] : '';
		   
		   // Paging Add-on
		   $paging = ($paging === 'false') ? false : $paging; 
		   
		   
		   
		   /*
			 *	alm_cache_create_dir
			 *
			 * Cache Add-on hook
			 * Create cache directory + meta .txt file
			 *
			 * @return null
			 */
		   if(!empty($cache_id) && has_action('alm_cache_create_dir')){
		      apply_filters('alm_cache_create_dir', $cache_id, $canonical_url);
		      $page_cache = ''; // set our page cache variable
		   }
		   
		        
		   
			if($data){
				
				$nextpage = (isset($data['nextpage'])) ? $data['nextpage'] : false; // true : false
				$post_id = (isset($data['post_id'])) ? $data['post_id'] : 'null'; // current post id
				$startpage = (isset($data['startpage'])) ? $data['startpage'] : 'null'; // startpage			
				$base_url = get_permalink($post_id); // base_url	
									
				
				if($queryType === 'totalpages'){ // Get totalpages for Paging Add-on
					
					wp_send_json(array(
						'totalpages' => apply_filters('alm_nextpage_total_pages', $post_id, $id)
					));
					
					
				}else{ // Regular nextpage query				
				
					$postcount = 1;					
					
		         if($startpage > 1){ // if $startpage > 1 (e.g. user lands on /3/ etc.)      
		            if(!$paging){ 
			            $page = $page + $startpage;
		            }	               	               
		         } else { 
		            if(!$paging){ 	            
		               $page = $page + 1;
		            }	               
		         }
					
					if($nextpage === 'true'){				
								
						global $post;									
								
		            $post = get_post($post_id); // Must be called $post	
		                    
		            // Run setup_postdata for oEmbeds in content      
		            setup_postdata($post); 
		            
		            // Support for Visual Composer
		            if(method_exists('WPBMap', 'addAllMappedShortcodes')) {
		               WPBMap::addAllMappedShortcodes();
		            }
					   					
					  	// Get post content				
		            $content = $post->post_content; 					
						 
						// Split $content into array					 
						$content = ALMNEXTPAGE::alm_nextpage_content($content, $id);
		            			
		            // Get total page count	  
		            $length = count($content);
		            
		            if( isset($content[$page] ) ){
		               
		               // Prepend `alm_nextpage_break_{id}` value to page.
		               $content[$page] = ($page >= 1) ? apply_filters('alm_nextpage_break_'.$id, '') . $content[$page] : $content[$page];
		               
		               
		               // Gutenberg Blocks
		               // Remove Gutenberg html comments. These were causing issues with ACF blocks.
		               $content = str_replace('<!-- wp:nextpage -->', '', $content);
		               $content = str_replace('<!-- /wp:nextpage -->', '', $content);
		               
		               
		               // Apply `the_content` core WP filter
		               $content = apply_filters('the_content', $content[$page]);		               
							
							$current = $page + 1;
							$permalink = $base_url . ALMNEXTPAGE::get_leading_slash() . ($current) . ALMNEXTPAGE::get_trailing_slash();
							
							$html = apply_filters('alm_nextpage_wrap_start', $post_id, $permalink, $current, $length, false);
							
								/*
		   			   	 *	alm_nextpage_before
		   			   	 * ALM Nextpage Filter Hook
		   			   	 *
		   			   	 * @return HTML/PHP
		   			   	 */   			   	 
		                  if(has_filter('alm_nextpage_before')){
		                     $html .= apply_filters('alm_nextpage_before', $page+1);
								}
								
								$html .= $content;
								
								/*
		   			   	 *	alm_nextpage_after
		   			   	 * ALM Nextpage Filter Hook
		   			   	 *
		   			   	 * @return HTML/PHP
		   			   	 */  
		   			      if(has_filter('alm_nextpage_after')){ 			   	 
								   $html .= apply_filters('alm_nextpage_after', $page+1);
								}
							
							$html .= apply_filters('alm_nextpage_wrap_end', '');
								
							if(!empty($content)){					          
			               $return = array(
			                  'html' => $html,
			                  'meta'  => array(
			                     'postcount' => $postcount,
			                     'totalposts' => $length - $startpage
			                  )
			               );
			            } else {
			               $return = array(
			                  'html' => '',
			                  'meta'  => array(
			                     'postcount' => null,
			                     'totalposts' => null
			                  )
			               );	
			            }	 
		            
			            /*
		                *	alm_cache_file
		                *
		                * Cache Add-on hook
		                * If Cache is enabled, check the cache file
		                *
		                * @return null
		                */
		               
		               if(!empty($content) && !empty($cache_id) && has_action('alm_cache_installed')){
		                  $cache_page = $page + 1;
		                  apply_filters('alm_nextpage_cache_file', $cache_id, $cache_page, $html);
		               }
		               
		            } else {
		               $return = array(
		                  'html' => '',
		                  'meta'  => array(
		                     'postcount' => null,
		                     'totalposts' => null
		                  )
		               );	
		            }
		                       
		            wp_send_json($return);	            
		         }	
		         
		      }
		      			
			}
			
			wp_die();			
   	} 	
   	
      
            
      /**
   	 * alm_init_nextpage
   	 * Get initial page(preloaded) of nextpage post.
   	 *
   	 * @return $content
   	 * @since 1.0
   	 */
   	public static function alm_init_nextpage($post_id = null, $page, $is_paged, $paging, $div_id, $id){
      	
      	if($post_id){         	
            $post_content = get_post($post_id);
            $content = $post_content->post_content;		
            		
				// Get the content
				$content = self::alm_nextpage_content($content, $id); 
            
            // Get total page count
            $length = count($content);
            
            $page = $page - 1;
            $the_content = '';		
            
            if(!$is_paged){ // Not paged, only return first page    
	                                   
               $current = 1;
	            $permalink = get_permalink($post_id);
	            
               $the_content .= apply_filters('alm_nextpage_wrap_start', $post_id, $permalink, $current, $length, true);
               
                  /*
   			   	 *	alm_nextpage_before
   			   	 * ALM Nextpage Filter Hook
   			   	 *
   			   	 * @return HTML/PHP
   			   	 */   	
   			      if(has_filter('alm_nextpage_before')){		   	 
   					   $the_content .= apply_filters('alm_nextpage_before', $page+1);
   					}
   					
   					// Apply `the_content` core WP filter
                  $the_content .= apply_filters('the_content', $content[$page]);
		            
                  
                  /*
   			   	 *	alm_nextpage_after
   			   	 * ALM Nextpage Filter Hook
   			   	 *
   			   	 * @return HTML/PHP
   			   	 */   	
   			      if(has_filter('alm_nextpage_after')){ 		   	 
   					   $the_content .= apply_filters('alm_nextpage_after', $page+1);
   					}
					
               $the_content .= apply_filters('alm_nextpage_wrap_end', '</div>');
               
            } else { // Split pages up into individual content blocks              
               
               if($paging === 'true'){ // Paging Add-on
                  
	               $permalink = get_permalink($post_id);
	               $the_content .= apply_filters('alm_nextpage_wrap_start', $post_id, $permalink, $page, $length, true);
	              
   	               /*
      			   	 *	alm_nextpage_before
      			   	 * ALM Nextpage Filter Hook
      			   	 *
      			   	 * @return HTML/PHP
      			   	 */  
      			      if(has_filter('alm_nextpage_before')){ 			   	 
   						   $the_content .= apply_filters('alm_nextpage_before', $page+1);
   						}  						
   						
   						// Prepend `alm_nextpage_break_{id}` value to page.
			            $content[$page] = apply_filters('alm_nextpage_break_'.$id, '') . $content[$page];
   						
   						// Apply `the_content` core WP filter
                     $the_content .= apply_filters('the_content', $content[$page]);
                     
                     /*
      			   	 *	alm_nextpage_after
      			   	 * ALM Nextpage Filter Hook
      			   	 *
      			   	 * @return HTML/PHP
      			   	 */   
      			      if(has_filter('alm_nextpage_after')){ 			   	 
   						   $the_content .= apply_filters('alm_nextpage_after', $page+1);
   						}
						
                  $the_content .= apply_filters('alm_nextpage_wrap_end', '');
	               
               } else { // Standard
               
	               for($i = 0; $i <= $page; $i++){ // Loop pages and build return
	                  
	                  if($i < 1){
	                     $permalink = get_permalink($post_id);
	                  } else {
	                     $permalink = get_permalink($post_id). self::get_leading_slash() . ($i + 1) . self::get_trailing_slash();
	                  }                 
	                  
	                  $current = $i + 1;
	                  $the_content .= apply_filters('alm_nextpage_wrap_start', $post_id, $permalink, $current, $length, true);
	                  
   	                  /*
         			   	 *	alm_nextpage_before
         			   	 * ALM Nextpage Filter Hook
         			   	 *
         			   	 * @return HTML/PHP
         			   	 */  
         			      if(has_filter('alm_nextpage_before')){ 			   	 
      						   $the_content .= apply_filters('alm_nextpage_before', $page+1);
      						}
      						
      						if($i > 0){                 
			                  // Prepend `alm_nextpage_break_{id}` value to page.
					            $content[$i] = apply_filters('alm_nextpage_break_'.$id, '') . $content[$i];
      						}
      						
      						// Apply `the_content` core WP filter
   	                  $the_content .= apply_filters('the_content', $content[$i]);
   	                  
   	                  /*
         			   	 *	alm_nextpage_after
         			   	 * ALM Nextpage Filter Hook
         			   	 *
         			   	 * @return HTML/PHP
         			   	 */   	
         			      if(has_filter('alm_nextpage_after')){ 		   	 
      						   $the_content .= apply_filters('alm_nextpage_after', $page+1);
      						}
   						
	                  $the_content .= apply_filters('alm_nextpage_wrap_end', '');
	                  
	               } 
	               
               }      
                                             
            } 
            
            
            // Add Localized `page` variable
            ALM_LOCALIZE::add_localized_var('page', $page || $current, $div_id);
            
            // Add Localized `total_posts` variable
            ALM_LOCALIZE::add_localized_var('total_posts', $length, $div_id);
            
            return $the_content;
      	}      	
   	}
   	
      
            
      /**
   	 * alm_nextpage_total_pages
   	 * Return the total pages for post
   	 *
   	 * @return $total_pages
   	 * @since 1.0
   	*/   	
   	
   	public function alm_nextpage_total_pages($post_id = null, $id = ''){
      	$total_pages = 0;
			if($post_id){
				$post_content = get_post($post_id);
				$content = $post_content->post_content;					
				$content = explode(apply_filters('alm_nextpage_break_'.$id, '<!--nextpage-->'), $content);				
				$total_pages = count($content);  	   		
			}			
			return $total_pages;			
   	}
   	
      
            
      /**
   	 * alm_nextpage_wrap_start
   	 * Return the html wrapper for nextpage content
   	 *
   	 * @since 1.0
   	 * @updated 1.1.2
   	 */   	
   	
   	public function alm_nextpage_wrap_start($post_id, $url, $page, $total, $init = false){ 

      	$totalpages = ($page == 1) ? ' data-total-posts="'. $total .'"' : '';       	
      	
      	if($init){ 
         	// Get current permalink including querystring
				$url =  ($_SERVER["QUERY_STRING"]) ? $url .'?'. $_SERVER["QUERY_STRING"] : $url;
			} else {
   			$querysrting = ALMNEXTPAGE::alm_nextpage_get_querystring();      		
            $url =  ($querysrting) ? $url .'?'. $querysrting : $url;
			}
			
			
      	return '<div class="alm-nextpage post-'. $post_id .'" data-url="'. $url .'" data-id="'. $page .'"'. $totalpages .'>';;
   	
   	}
   	
      
            
      /**
   	 * alm_nextpage_wrap_end
   	 * Return the html wrapper closing elements for nextpage content
   	 *
   	 * @since 1.0
   	 */   	
   	
   	public function alm_nextpage_wrap_end(){   	
      	$wrap = '</div>';      	
      	return $wrap;
   	}
   	
      
            
      /**
   	 * alm_nextpage_content
   	 * Return the content
   	 *
   	 * @since 1.3
   	 */ 
   	public static function alm_nextpage_content($content = '', $id = ''){   
	   	/*
			 *	Get content break element
			 * ALM Nextpage Core Filter Hook 
			 *
			 * @return $content;
			 */
	   	$content = explode(apply_filters('alm_nextpage_break_'.$id, '<!--nextpage-->'), $content);
	   	
      	return $content;
      	
   	}
   	
   	
   	
   	/**
   	 * alm_nextpage_noscript_paging
   	 * Generate a paging navigation for <noscript/>
   	 *
   	 * @param $post_id 	 current post ID
   	 * @param $id 			 ALM ID
    	 * @return string
    	 * @since 1.4
   	 */ 
   	function alm_nextpage_noscript_paging($post_id = '', $id = ''){
	   	if(empty($post_id)){
		   	return false;	
		   }
			$post_content = get_post($post_id);
			$pages = ($post_content) ? count(self::alm_nextpage_content($post_content->post_content, $id)) : 0;
						
			$paging = '';
	      if($pages > 1){
         	$paging = '<noscript>';
	         	$paging .= '<div class="alm-paging" style="opacity: 1">';
	         	$paging .= __('Pages: ', 'ajax-load-more');
	         	for($i = 1; $i <= $pages; $i++){
	            	$paging .= '<span class="page" data-page="'. $i .'">';
	            		$paging .= '<a href="'. get_permalink($post_id) . ALMNEXTPAGE::get_leading_slash() . $i . ALMNEXTPAGE::get_trailing_slash() .'">'. $i .'</a>';
	            	$paging .= '</span>';
	         	}
	         	$paging .= '</div>';         	
         	$paging .= '</noscript>';
         }			
			return $paging;			
   	}
   	
   	
   	
   	/**
   	 * alm_nextpage_get_querystring
   	 * Get the current querystring
   	 *
   	 * @since 1.3
   	 */ 
   	public static function alm_nextpage_get_querystring(){      	
      	$url = $_SERVER["HTTP_REFERER"]; // Get referring URL
      	$output = '';
      	
      	if($url){
	      	
	      	$parts = parse_url($url); // Parse the URL
	      	
	      	if(isset($parts['query'])){
		      	
		      	parse_str($parts['query'], $querystring); // Parse querystring  	      			  
		      	if($querystring){
		         	$index = 0;
		         	foreach ($querystring as $key => $value){
		            	$index++;
		            	$output .= ($index > 1) ? '&' : '';
		            	$output .= $key .'='. $value;
		         	}
		      	} 
	      	}
      	} 
      	
      	    	
      	return ($output) ? $output : '';
   	}
   	
      
            
      /**
   	 * alm_nextpage_shortcode
   	 * Build Next Post shortcode params and send back to core ALM
   	 *
   	 * @since 1.0
   	 */   	
   	function alm_nextpage_shortcode($urls, $pageviews, $post_id, $scroll, $options){
      	
   		$return = ' data-nextpage="true"';
		   $return .= ' data-nextpage-urls="'.$urls.'"';
		   $return .= ' data-nextpage-pageviews="'.$pageviews.'"';
		   $return .= ' data-nextpage-post-id="'.$post_id.'"';
		   $return .= ' data-nextpage-scroll="'.$scroll.'"';
		   $return .= ' data-nextpage-startpage="'.alm_get_startpage().'"';
		   		   
		   return $return;
   	}	
   		
   	
   	
   	/**
   	 * alm_nextpage_installed
   	 * an empty function to determine if nextpage is true.
   	 *
   	 * @since 1.0
   	 */   	
   	function alm_nextpage_installed(){
   	   //Empty return
   	} 
   	
   	
   	
   	/**
   	 * alm_nextpage_settings
   	 * Create the Next Page settings panel.
   	 *
   	 * @since 1.0
   	 */
   	
   	function alm_nextpage_settings(){
      	
      	register_setting(
      		'alm_nextpage_license', 
      		'alm_nextpage_license_key', 
      		'alm_nextpage_sanitize_license'
      	);
      	
   	}  	
   	
   }   


	
	/**
    * alm_nextpage_sanitize_license
    * Sanitize license activation
    *
    * @since 1.0
    */
   
   function alm_nextpage_sanitize_license( $new ) {
   	$old = get_option( 'alm_nextpage_license_key' );
   	if( $old && $old != $new ) {
   		delete_option( 'alm_nextpage_license_status' ); // new license has been entered, reactivate
   	}
   	return $new;
   }
     	   	
   	
   	
   /**
    * ALMNEXTPAGE
    * The main function responsible for returning Ajax Load More Nextpost.
    *
    * @since 1.0
    */	
   
   function ALMNEXTPAGE(){
   	global $ALMNEXTPAGE;   
   	if( !isset($ALMNEXTPAGE) ){
   		$ALMNEXTPAGE = new ALMNEXTPAGE();
   	}   
   	return $ALMNEXTPAGE;
   }
      
   
   // initialize
   ALMNEXTPAGE();

endif; // class_exists check



/* Software Licensing */
function alm_nextpage_plugin_updater() {	
	if(!has_action('alm_pro_installed') && class_exists('EDD_SL_Plugin_Updater')){ // Don't check for updates if Pro is activated
		$license_key = trim( get_option( 'alm_nextpage_license_key' ) ); // retrieve our license key from the DB
		$edd_updater = new EDD_SL_Plugin_Updater( ALM_STORE_URL, __FILE__, array( 
				'version' 	=> ALM_NEXTPAGE_VERSION,
				'license' 	=> $license_key,
				'item_id'   => ALM_NEXTPAGE_ITEM_NAME, // Found in core ALM
				'author' 	=> 'Darren Cooney'
			)
		);
	}
}
add_action( 'admin_init', 'alm_nextpage_plugin_updater', 0 );	
/* End Software Licensing */