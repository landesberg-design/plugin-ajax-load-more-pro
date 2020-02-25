<?php
/*
Plugin Name: Ajax Load More: Single Post
Plugin URI: https://connekthq.com/plugins/ajax-load-more/add-ons/single-post/
Description: Ajax Load More add-on for infinite scrolling single posts
Author: Darren Cooney
Twitter: @KaptonKaos
Author URI: https://connekthq.com
Version: 1.4.2
License: GPL
Copyright: Darren Cooney & Connekt Media
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define('ALM_PREV_POST_PATH', plugin_dir_path(__FILE__));
define('ALM_PREV_POST_URL', plugins_url('', __FILE__));
define('ALM_PREV_POST_VERSION', '1.4.2');
define('ALM_PREV_POST_RELEASE', 'October 1, 2019');


/*
*  alm_single_post_install
*  Activation hook
*
*  @since 1.0
*/

register_activation_hook( __FILE__, 'alm_single_post_install' );
function alm_single_post_install() {
   // If Ajax Load More is activated
   if(!is_plugin_active('ajax-load-more/ajax-load-more.php')){
   	die('You must install and activate <a href="https://wordpress.org/plugins/ajax-load-more/">Ajax Load More</a> before installing the ALM Single Post Add-on.');
	}
}


if( !class_exists('ALM_SINGLEPOST') ):

   class ALM_SINGLEPOST{		
	   
   	function __construct(){
   		add_action( 'alm_prev_post_installed', array( &$this, 'alm_prev_post_installed') );
   		add_action( 'alm_single_post_installed', array( &$this, 'alm_single_post_installed') );
      	add_action( 'wp_ajax_alm_get_single', array(&$this, 'alm_query_single_post') );
   		add_action( 'wp_ajax_nopriv_alm_get_single', array(&$this, 'alm_query_single_post') );
   	   add_filter( 'alm_single_post_inc', array( &$this, 'alm_single_post_inc' ), 10, 5 );
   	   add_filter( 'alm_single_post_args', array( &$this, 'alm_single_post_args' ), 10, 2 );
   		add_filter( 'alm_single_post_shortcode', array( &$this, 'alm_single_post_shortcode'), 10, 6 );
   		add_action( 'alm_prev_post_settings', array( &$this, 'alm_prev_post_settings') );
   		add_action( 'wp_enqueue_scripts', array( &$this, 'alm_single_post_enqueue_scripts' ));
   		add_action( 'posts_where', array( &$this, 'alm_single_query_where' ), 10, 2);  
   		load_plugin_textdomain( 'ajax-load-more-single-post', false, dirname(plugin_basename( __FILE__ )).'/lang/'); //load text domain
   	}
   	
   	
   	
      /**
   	 * alm_single_query_where
   	 * Set WP Query params using `posts_where` clause
   	 * Force is_single() and is_singular() to be true in the ajax call.
   	 *
   	 *  @since 1.3.3
   	 */
		function alm_single_query_where($where, $query) {	
   					
			$alm_single_query = $query->get('alm_query');	
			
			if ( $alm_single_query && $alm_single_query === 'single_posts') {		
				
				global $wp_query;
				$wp_query->is_single = true;
				$wp_query->is_feed = true;
				$wp_query->is_singular = true;
				$wp_query->in_the_loop = true;
				
				// Remove errors
				error_reporting(0);
			}	
			return $where;		
		}



      /**
   	 *  Enqueue Previous Post scripts
   	 *
   	 *  @since 1.0
   	 */
   	function alm_single_post_enqueue_scripts(){
      	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min'; // Use minified libraries if SCRIPT_DEBUG is turned off
   		wp_register_script( 'ajax-load-more-single-posts', plugins_url( '/js/alm-single-posts'.$suffix.'.js', __FILE__ ), array('ajax-load-more'),  ALM_PREV_POST_VERSION, true );
   	}
   	
   	
   	
   	/**
		 *  Get the post id and return the next post ID
		 *
		 *  @return JSON
		 *  @since 1.0
		 *  @updated 1.3
		 */
   	function alm_query_single_post(){
	   		   	
         $init = (isset($_GET['init'])) ? $_GET['init'] : false;
         $id = (isset($_GET['id'])) ? $_GET['id'] : '';
         $exclude_post_id = (isset($_GET['initial_id'])) ? $_GET['initial_id'] : '';
         $tax = (isset($_GET['taxonomy'])) ? $_GET['taxonomy'] : '';
         $exclude_terms = (isset($_GET['excluded_terms'])) ? $_GET['excluded_terms'] : '';
     		$postType = (isset($_GET['post_type'])) ? $_GET['post_type'] : 'post';
     		$order = (isset($_GET['order']) && !empty($_GET['order'])) ? $_GET['order'] : 'previous'; 
     		
     		// Order - If order is `latest` and first run and ordered by latest, set posts to load in order by date 
     		$order = ($init === 'false' && $order === 'latest') ? 'previous' : $order;

         if($id){
	         
	         switch ($order) {
		         
		         // Get the latest (newest) post
		         case 'latest': 
			         $data = self::alm_get_latest_post($exclude_post_id, $postType, $tax, $exclude_terms);
			         wp_send_json($data);
		         break;
		         
		         // Get post in array
		         case 'post__in': 
		         	$data = self::alm_get_post_in_array($id, $post__in);
			         wp_send_json($data);
		         break;
		         
		         // Get next post ordered by date
		         case 'next': 
			         $data = self::alm_get_next_post($id, $tax, $exclude_terms, $exclude_post_id);
		            wp_send_json($data);		         
		         break;
		         
		         // Get previous post ordered by date
		         case 'previous': 
			         $data = self::alm_get_previous_post($id, $tax, $exclude_terms, $exclude_post_id);
		            wp_send_json($data);		         
		         break;		
		         
		         // Get post ID array (use as default for ease)
		         default : 
		         	$data = self::alm_get_post_in_array($id, $order);
			         wp_send_json($data);
		         break;     
	         }	      
	      }	         
      }
		
		
		
      /**
		 *  Get the next post in the array
		 *
		 *  @return JSON
		 *  @since 1.0
		 *  @updated 1.3
		 */
      public static function alm_get_post_in_array($id, $array){
	      
	      global $post;
	      
	      // Store the existing post object for later so we don't lose it
			$oldGlobal = $post;			
			
			$previous_post = '';
			
			$array = explode(',', str_replace(' ', '', $array)); // Remove whitespace and convert to array
			
			if(in_array($id, $array)){	
   			// ID found in array			
				$length = count($array);
				$index = array_search($id, $array);				
				if($index < $length-1){ // Last element
					$previous_post = get_post($array[$index+1]);
				}			
			} else { 
   			// Get first element in array
				$previous_post = get_post($array[0]);
			}				
	      
	      // Reset global $post object
			$post = $oldGlobal;

         // Build the $data object
			$data = self::alm_build_data_object($id, $previous_post);
         
         return $data; 	   
	   }
      
      
      
      /**
		 *  alm_get_previous_post
		 *  Get the previous post by date using `previous_post` method 
		 *
		 *  @return JSON
		 *  @since 1.3
		 */
      public static function alm_get_previous_post($id, $tax, $exclude_terms, $exclude_post_id){
	      
	      global $post;
	
         // Store the existing post object for later so we don't lose it
			$oldGlobal = $post;

			// Get post object
			$post = get_post($id);

			// Get Previous Post
			$previous_post = (!empty($tax)) ? get_previous_post(true, $exclude_terms, $tax) : get_previous_post(false, $exclude_terms);
			
			// If Previous Post === Original post
			if($previous_post && $previous_post->ID == $exclude_post_id){
				$post = get_post($previous_post->ID);
				$previous_post = (!empty($tax)) ? get_previous_post(true, $exclude_terms, $tax) : get_previous_post(false, $exclude_terms);	           
         }		
			
			// Reset global $post object
			$post = $oldGlobal;
			
			// Build the $data object
			$data = self::alm_build_data_object($id, $previous_post);
         
         return $data;         
      }
      
      
      
      /**
		 *  alm_get_next_post
		 *  Get the next post by date using `next_post` method 
		 *
		 *  @return JSON
		 *  @since 1.3
		 */
      public static function alm_get_next_post($id, $tax, $exclude_terms, $exclude_post_id){
	      
	      global $post;
	
         // Store the existing post object for later so we don't lose it
			$oldGlobal = $post;

			// Get post object
			$post = get_post($id);

			// Get Previous Post
			$next_post = (!empty($tax)) ? get_next_post(true, $exclude_terms, $tax) : get_next_post(false, $exclude_terms);
			
			// If Previous Post === Original post
			if($next_post && $next_post->ID == $exclude_post_id){
				$post = get_post($previous_post->ID);
				$next_post = (!empty($tax)) ? get_next_post(true, $exclude_terms, $tax) : get_next_post(false, $exclude_terms);	           
         }		
			
			// Reset global $post object
			$post = $oldGlobal;
			
			// Build the $data object
			$data = self::alm_build_data_object($id, $next_post);
         
         return $data;         
      }
      
      
      
      /**
		 *  alm_get_latest_post
		 *  Get the latest (newest) post and return the data 
		 *
		 *  @return JSON
		 *  @since 1.3
		 */
      public static function alm_get_latest_post($id, $postType, $tax, $exclude_terms){
	   
	      global $post;
	
         // Store the existing post object for later so we don't lose it
			$oldGlobal = $post;
			
			// Get post object			
	      $previous_post = get_post( self::alm_query_latest_post_id($id, $postType, $tax, $exclude_terms) );	      
			
			// Reset global $post object
			$post = $oldGlobal; 
			
			// Build the $data object
			$data = self::alm_build_data_object($id, $previous_post);
	      
	      return $data;	      
      }
      
      
      
      /**
		 *  alm_query_latest_post_id
		 *  Run a get_posts function to get the most recent post ID
		 *
		 *  @return string (ID)
		 *  @since 1.3
		 */
      public static function alm_query_latest_post_id($id, $postType, $tax, $exclude_terms){
	      	      
	      // Get latest post not including the current
	      $args = array(
		      'post_type' => $postType,
		      'posts_per_page' => 1,
		      'post__not_in' => array($id),
		      'orderby' => 'date',
		      'order' => 'DESC',
		      'fields' => 'ids',
		      'suppress_filters' => false
	      );	    
	      
	      // If $in_same_term, loop all tax terms and query based on the terms
	      if($tax){
		      $terms = get_the_terms( $id, $tax );
		      if($terms){
			      $found_terms = [];
			      foreach ( $terms as $term ) {
			      	$found_terms[] = $term->slug;
				   }
			      $args['tax_query'][] = array(
				      'taxonomy' 	=> $tax,
				      'field' 		=> 'slug',
				      'terms' 		=> $found_terms,
				      'operator' 	=> 'IN'				      
			      );
		      }
	      }
	      
	      // Exclude certain terms
	      if($exclude_terms){
   	      $exclude_terms = explode(',', $exclude_terms);
   	      foreach ( $exclude_terms as $id ) {
               $term_data = get_term( $id );
               if($term_data){
                  $args['tax_query'][] = array(
   				      'taxonomy' 	=> $term_data->taxonomy,
   				      'field' 		=> 'term_id',
   				      'terms' 		=> $id,
   				      'operator' 	=> 'NOT IN'				      
   			      );
			      }
            }
	      }	
	      
	      
	      // Get the posts
	      $posts = get_posts($args);	
	      
	            
	      if($posts){
		      foreach($posts as $post_id){
				   return $post_id;
		      }
	      } else {
		      return null;
	      }	      
      }
      
      
      
      /**
	    *  alm_build_data_object
   	 *  Build the data object based on the $previous_post object
   	 *
   	 * @since 1.3
   	 */
      public static function alm_build_data_object($id, $previous_post){
	      $data = array();
	      if($previous_post){
				$data['has_previous_post'] = true;
				$data['prev_id'] = $previous_post->ID;
				$data['prev_slug'] = $previous_post->post_name;
				$data['prev_permalink'] = get_permalink($previous_post->ID);
				$data['prev_title'] = strip_tags(get_the_title($previous_post->ID));
	      } else {
		      $data['has_previous_post'] = false;
	      }
	      
			$data['current_id'] = $id;
			$data['permalink'] = get_permalink($id);
			$data['title'] = strip_tags(get_the_title($id));
			
			return $data;
      }
      
      
      
      
      /**
	    *  alm_single_post_args
   	 *  Set the `single_post` query args
   	 *
   	 *  @param $id   int
   	 *  @param $post_type  array
   	 *  @return $args
   	 *  @since 1.0
   	 */
   	function alm_single_post_args($id, $post_type){
	   	
      	$args = array(
         	'post__in' => array($id),
            'post_type' => $post_type,
   			'posts_per_page' => 1
         );
         return $args;
         
   	}



   	/**
	    *  alm_single_post_inc
   	 *  Get the content for the first single post include
   	 *
   	 *  @return ob_get_contents()
   	 *  @updated 1.3
   	 *  @since 1.0
   	 */

   	function alm_single_post_inc($repeater, $repeater_type, $theme_repeater, $id, $post_type){
         ob_start();
	   	if($theme_repeater != 'null' && has_filter('alm_get_theme_repeater')){
		   	// Theme Repeater
				do_action('alm_get_theme_repeater', $theme_repeater, 1, 1, 1, 1); // Returns include
			}else{
				// Standard Repeaters
            include(alm_get_current_repeater($repeater, $repeater_type));
			}
			$return = ob_get_contents();
			ob_end_clean();
			return $return;
	   }



      /**
   	 *  alm_single_post_shortcode
   	 *  Build Next Post shortcode params and send back to core ALM
   	 *
   	 *  @since 1.0
   	 */

   	function alm_single_post_shortcode($id, $order, $tax, $excluded, $progress_bar, $options){
	   	
   		$return = ' data-single-post="true"';
		   $return .= ' data-single-post-id="'.$id.'"';
		   if(!empty($order))
		   	$return .= ' data-single-post-order="'.$order.'"';
		   if(!empty($tax))
		   	$return .= ' data-single-post-taxonomy="'.$tax.'"';
		   if(!empty($excluded))
		   	$return .= ' data-single-post-excluded-terms="'.$excluded.'"';

		   // Set scrolltop
		   $single_post_scrolltop = '30';
		   
		   // Update settings
   		$single_post_scrolltop = (isset($options['_alm_prev_post_scrolltop'])) ? $options['_alm_prev_post_scrolltop'] : $single_post_scrolltop;

		   // Enabled Scrolling
			$single_post_enable_scroll = $options['_alm_prev_post_scroll'];
   		if(!isset($single_post_enable_scroll)){
   			$single_post_enable_scroll = 'false';
         }else{
      		if($single_post_enable_scroll == '1'){
      		   $single_post_enable_scroll = 'true';
            }else{
      		   $single_post_enable_scroll = 'false';
      		}
   		}
   		
   		$single_post_controls = '1';
   		if(isset($options['_alm_prev_post_browser_controls'])){	   		
   			$single_post_controls = $options['_alm_prev_post_browser_controls'];
   		}

   		// Page Title
   		$single_post_title_template = '';
   		if(isset($options['_alm_prev_post_title'])){
	   		$single_post_title_template = $options['_alm_prev_post_title'];
   		}

		   // GA send Pageview
   		if(!isset($options['_alm_prev_post_ga'])){
   			$single_post_send_pageview = 'true';
         }else{
            $single_post_send_pageview = $options['_alm_prev_post_ga'];
      		if($single_post_send_pageview == '1'){
      		   $single_post_send_pageview = 'true';
            }else{
      		   $single_post_send_pageview = 'false';
      		}
   		}

			$return .= ' data-single-post-title-template="'.$single_post_title_template.'"';
			$return .= ' data-single-post-site-title="'.get_bloginfo('name').'"';
			$return .= ' data-single-post-site-tagline="'.get_bloginfo('description').'"';
			$return .= ' data-single-post-scroll="'.$single_post_enable_scroll.'"';
			$return .= ' data-single-post-scrolltop="'.$single_post_scrolltop.'"';			
			$return .= ' data-single-post-controls="'.$single_post_controls.'"';		
			$return .= ' data-single-post-progress-bar="'.$progress_bar.'"';			
		   $return .= ' data-single-post-pageview="'.$single_post_send_pageview.'"';

		   return $return;
   	}



   	/**
   	 *  alm_prev_post_installed
   	 *  an empty function to determine if Previous Post is active.
   	 *
   	 *  @since 1.0
   	 */
 
   	function alm_prev_post_installed(){
   	   //Empty return
   	}



   	/**
   	 *  alm_single_post_installed
   	 *  an empty function to determine if Single Posts is active.
   	 *
   	 *  @since 1.0
   	 */
 
   	function alm_single_post_installed(){
   	   //Empty return
   	}



   	/**
   	 *  alm_prev_post_settings
   	 *  Create the Previous Post settings panel.
   	 *
   	 *  @since 1.2
   	 */

   	function alm_prev_post_settings(){

      	register_setting(
      		'alm_prev_post_license',
      		'alm_prev_post_license_key',
      		'alm_prev_post_sanitize_license'
      	);

   	   add_settings_section(
	   		'alm_prev_post_settings',
	   		'Single Post Settings',
	   		'alm_prev_post_callback',
	   		'ajax-load-more'
	   	);

	   	add_settings_field(
	   		'_alm_prev_post_title',
	   		__('Page Title Template', 'ajax-load-more-single-post' ),
	   		'alm_prev_post_title_callback',
	   		'ajax-load-more',
	   		'alm_prev_post_settings'
	   	);

	   	add_settings_field(
	   		'_alm_prev_post_ga',
	   		__('Google Analytics', 'ajax-load-more-single-post' ),
	   		'alm_prev_post_ga_callback',
	   		'ajax-load-more',
	   		'alm_prev_post_settings'
	   	);

	   	add_settings_field(
	   		'_alm_prev_post_scroll',
	   		__('Scroll to Post', 'ajax-load-more-single-post' ),
	   		'alm_prev_post_scroll_callback',
	   		'ajax-load-more',
	   		'alm_prev_post_settings'
	   	);

	   	add_settings_field(
	   		'_alm_prev_post_scrolltop',
	   		__('Scroll Top', 'ajax-load-more-single-post' ),
	   		'alm_prev_post_scrolltop_callback',
	   		'ajax-load-more',
	   		'alm_prev_post_settings'
	   	);
	   	add_settings_field( 
	   		'_alm_prev_post_browser_controls', 
	   		__('Back/Fwd Buttons', 'ajax-load-more-single-post' ), 
	   		'_alm_prev_post_browser_controls_callback', 
	   		'ajax-load-more', 
	   		'alm_prev_post_settings' 
	   	);	
   	}

   }





	/*
   *  alm_prev_post_sanitize_license
   *  Sanitize our license activation
   *
   *  @since 1.0.0
   */

   function alm_prev_post_sanitize_license( $new ) {
   	$old = get_option( 'alm_prev_post_license_key' );
   	if( $old && $old != $new ) {
   		delete_option( 'alm_prev_post_license_status' ); // new license has been entered, so must reactivate
   	}
   	return $new;
   }


   /* Next Post Settings (Displayed in ALM Core) */


	/*
	*  alm_prev_post_callback
	*  Next Post Setting Heading
	*
	*  @since 1.0
	*/

	function alm_prev_post_callback() {
	   $html = '<p>' . __('Customize your installation of the <a href="http://connekthq.com/plugins/ajax-load-more/add-ons/single-post/">Single Post</a> add-on.', 'ajax-load-more-single-post') . '</p>';

	   echo $html;
	}



	/*
	*  alm_prev_post_ga_callback
	*  Send pageviews to Google Analytics
	*
	*  @since 1.0
	*/

	function alm_prev_post_ga_callback(){
		$options = get_option( 'alm_settings' );
		if(!isset($options['_alm_prev_post_ga'])){
		   $options['_alm_prev_post_ga'] = '1';
		}

		$html = '<input type="hidden" name="alm_settings[_alm_prev_post_ga]" value="0" /><input type="checkbox" id="_alm_prev_post_ga" name="alm_settings[_alm_prev_post_ga]" value="1"'. (($options['_alm_prev_post_ga']) ? ' checked="checked"' : '') .' />';
		$html .= '<label for="_alm_prev_post_ga">'.__('Send pageviews to Google Analytics.', 'ajax-load-more-single-post').'<br/><span>Each time a post is loaded it will count as a pageview. You must have a reference to your Google Analytics tracking code on the page.</span></label>';

		echo $html;
	}



	/*
	*  alm_prev_post_title_callback
	*  Update the page title
	*
	*  @since 1.0
	*/

	function alm_prev_post_title_callback(){
		$options = get_option( 'alm_settings' );
		if(!isset($options['_alm_prev_post_title'])){
		   $options['_alm_prev_post_title'] = '';
		}

		$html = '<label for="_alm_prev_post_title">';
		$html .= __('The page title template is used to update the browser title each time a new post is loaded.', 'ajax-load-more-single-post');
		$html .= '<br/><span>'.__('If empty the page title will <u>NOT</u> be updated', 'ajax-load-more-single-post').'</span></label><br/>';
		$html .= '<input type="text" class="full" id="_alm_prev_post_title" name="alm_settings[_alm_prev_post_title]" value="'.$options['_alm_prev_post_title'].'" placeholder="{post-title} - {site-title}" /> ';
		$html .= '<div class="template-tags"><h4>'.__('Template Tags', 'ajax-load-more-single-post').'</h4>';
		$html .= '<ul>';
		$html .= '<li><pre>{post-title}</pre> '.__('Title of Post', 'ajax-load-more-single-post').'</li>';
		$html .= '<li><pre>{site-title}</pre> '.__('Site Title', 'ajax-load-more-single-post').'</li>';
		$html .= '<li><pre>{tagline}</pre> '.__('Site Tagline', 'ajax-load-more-single-post').'</li>';
		$html .= '</ul>';

		$html .= '</ul>';


		echo $html;
	}



	/*
	*  alm_prev_post_scroll_callback
	*  Allow window scrolling
	*
	*  @since 1.0
	*/

	function alm_prev_post_scroll_callback(){
		$options = get_option( 'alm_settings' );

		if(!isset($options['_alm_prev_post_scroll'])){
			$options['_alm_prev_post_scroll'] = '0';
		}

		$html = '<input type="hidden" name="alm_settings[_alm_prev_post_scroll]" value="0" />';
		$html .= '<input type="checkbox" name="alm_settings[_alm_prev_post_scroll]" id="alm_prev_scroll_page" value="1"'. (($options['_alm_prev_post_scroll']) ? ' checked="checked"' : '') .' />';
		
		$html .= '<label for="alm_prev_scroll_page">';
			$html .= __('Enable Window Scrolling.', 'ajax-load-more-single-post');
			$html .= '<span>'. __('If scrolling is enabled, the users window will scroll to the current page on \'Load More\' action.</span>', 'ajax-load-more-seo').'</span>';
		$html .= '</label>';

		echo $html;
	}



	/*
	*  alm_prev_post_scrolltop_callback
	*  Set the scrolltop value
	*
	*  @since 1.0
	*/

	function alm_prev_post_scrolltop_callback() {

	   $options = get_option( 'alm_settings' );
	   if(!isset($options['_alm_prev_post_scrolltop'])){
			$options['_alm_prev_post_scrolltop'] = '30';
		}

		$html = '<label for="alm_settings[_alm_prev_post_scrolltop]">';
			$html .= __('Set the scrolltop position of the window when scrolling to a post.', 'ajax-load-more-single-post');
		$html .= '</label><br/>';
		$html .= '<input type="number" class="sm" id="alm_settings[_alm_prev_post_scrolltop]" name="alm_settings[_alm_prev_post_scrolltop]" step="1" min="0" value="'.$options['_alm_prev_post_scrolltop'].'" placeholder="30" /> ';

		echo $html;
	}	
	
	
	
	
	/*
	*  _alm_prev_post_browser_controls_callback
	*  Disable back/fwd button when URLs updated (uses replaceState vs pushState)
	*
	*  @since 1.2.2
	*/
		
	function _alm_prev_post_browser_controls_callback() {
	 
	   $options = get_option( 'alm_settings' );
	   
		if(!isset($options['_alm_prev_post_browser_controls'])){
			$options['_alm_prev_post_browser_controls'] = '1';
		}
		
		$html = '<input type="hidden" name="alm_settings[_alm_prev_post_browser_controls]" value="0" />';
		$html .='<input type="checkbox" id="_alm_prev_post_browser_controls" name="alm_settings[_alm_prev_post_browser_controls]" value="1"'. (($options['_alm_prev_post_browser_controls']) ? ' checked="checked"' : '') .' />';
		$html .= '<label for="_alm_prev_post_browser_controls">'.__('Enable Back/Fwd Browser Buttons.', 'ajax-load-more-single-post');
			$html .= '<span>'.__('Allow users to navigate Ajax generated content using the back and forward browser buttons.', 'ajax-load-more-single-post').'</span>';
		$html .= '</label>';	
		
		echo $html;
	}
	



   /*
   *  ALM_SINGLEPOST
   *  The main function responsible for returning Ajax Load More Single Post.
   *
   *  @since 1.0
   */

   function ALM_SINGLEPOST(){
   	global $ALM_SINGLEPOST;
   	if(!isset($ALM_SINGLEPOST)){
   		$ALM_SINGLEPOST = new ALM_SINGLEPOST();
   	}
   	return $ALM_SINGLEPOST;
   }
   
   ALM_SINGLEPOST(); // initialize

endif; // class_exists check


/* Software Licensing */
function alm_single_post_updater() {
	if(!has_action('alm_pro_installed') && class_exists('EDD_SL_Plugin_Updater')){ // Don't check for updates if Pro is activated
		$license_key = trim( get_option( 'alm_prev_post_license_key' ) );
		$edd_updater = new EDD_SL_Plugin_Updater( ALM_STORE_URL, __FILE__, array(
				'version' 	=> ALM_PREV_POST_VERSION,
				'license' 	=> $license_key,
				'item_id'   => ALM_PREV_POST_ITEM_NAME, // Found in core ALM
				'author' 	=> 'Darren Cooney'
			)
		);
	}
}
add_action( 'admin_init', 'alm_single_post_updater', 0 );
/* End Software Licensing */
