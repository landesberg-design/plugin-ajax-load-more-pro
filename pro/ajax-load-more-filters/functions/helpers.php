<?php

/**
 * alm_filters_display_label
 * Render filter label.
 *
 * @param $id string
 * @param $obj array
 * @since 1.8.4
 */
function alm_filters_display_label($id = '', $obj = ''){

   if( empty($id) || empty($obj) || !isset($obj['label']) ){
      return false; // Exit if empty
   }

   $filter_key = alm_filters_get_filter_key($obj);
   $value = $obj['label'];

   if( empty($value) && !has_filter('alm_filters_'. $id . '_' . $filter_key .'_label') ){
      return false; // Exit if title is empty && filter doesn't exist
   }

   $output = apply_filters('alm_filters_'. $id . '_' . $filter_key .'_label', $value);

   return $output;

}

/**
 * alm_filters_display_title
 * Render filter title.
 *
 * @param $options array
 * @param $obj array
 * @since 1.0
 */
function alm_filters_display_title($id = '', $obj = ''){

   if( empty($id) || empty($obj) || !isset($obj['title']) ){
      return false; // Exit if empty
   }

   $filter_key = alm_filters_get_filter_key($obj);
   $value = $obj['title'];


   if( empty($value) && !has_filter('alm_filters_'. $id . '_' . $filter_key .'_title') ){
      return false; // Exit if title is empty && filter doesn't exist
   }

	$output = '<div class="alm-filter--title">';
		$output .= '<'. apply_filters('alm_filters_title_element', 'h3') .' id="alm-filter-'. $filter_key .'-title' .'">';

			// WPML
			//if(function_exists('icl_register_string')){
				//$value = icl_register_string( 'Ajax Load More Filters', 'alm_filters_'. $id . '_' . $filter_key .'_title', $value );
			//}
			$output .= apply_filters('alm_filters_'. $id . '_' . $filter_key .'_title', $value);

		$output .= '</'. apply_filters('alm_filters_title_element', 'h3') .'>';
	$output .= '</div>';

   return $output;

}



/**
 * alm_filters_display_description
 * Render filter description.
 *
 * @param $options array
 * @param $obj array
 * @since 1.0
 */
function alm_filters_display_description($id = '', $obj = ''){

   if( empty($id) || empty($obj) || !isset($obj['description']) ){
      return false; // Exit if empty
   }

   $filter_key = alm_filters_get_filter_key($obj);
   $value = $obj['description'];


   if( empty($value) && !has_filter('alm_filters_'. $id . '_' . $filter_key .'_description') ){
      return false; // Exit if description is empty && filter doesn't exist
   }

   $output = '<div class="alm-filter--description">';
		$output .= '<'. apply_filters('alm_filters_description_element', 'p') .' id="alm-filter-'. $filter_key .'-description' .'">';

			// WPML
			//if(function_exists('icl_register_string')){
				//$value = icl_register_string( 'Ajax Load More Filters', 'alm_filters_'. $id . '_' . $filter_key .'_title', $value );
			//}
			$output .= htmlspecialchars_decode(apply_filters('alm_filters_'. $id . '_' . $filter_key .'_description', $value));

		$output .= '</'. apply_filters('alm_filters_description_element', 'p') .'>';
	$output .= '</div>';

   return $output;

}


/**
 * alm_filters_get_filter_key
 * Get the key for a filter group.
 *
 * @param $obj array
 * @since 1.7.1
 */
function alm_filters_get_filter_key($obj = ''){

   if( empty($obj) || !isset($obj['key']) ) return false; // Exit if empty

   $key = $obj['key'];
   // Set `$key` to taxonomy/meta_key	value for core filters
   $key = ($obj['key'] === 'taxonomy') ? $obj['taxonomy'] : $obj['key']; // Convert $key to $taxonomy value
   $key = ($obj['key'] === 'meta') ? $obj['meta_key'] : $key; // Convert $key to $meta_key value

   return $key;

}



/**
 * alm_filters_get_queryParam
 * Get URL Query param for link URLs (Radio/Checkbox)
 *
 * @param $slug string The slug of the URL
 * @since 1.8.1
 */
function alm_filters_build_url($obj, $slug){
   if(!$obj || $obj['base_url'] === '' || !$slug){
	   return false;
   }

   $queryParam = alm_filters_get_queryParam($obj);
   if(!$queryParam){
      return false;
   }

   $url = $obj['base_url'] .'?'. $queryParam .'='. $slug;

   return $url;
}



/**
 * alm_filters_get_queryParam
 * Get URL Query param for link URLs (Radio/Checkbox)
 *
 * @param $obj array
 * @since 1.8.1
 */
function alm_filters_get_queryParam($obj){
   if(!$obj){
	   return false;
   }

   if($obj['key'] === 'taxonomy'){
	   $param = (alm_filters_is_archive()) ? '_'. $obj['taxonomy'] : $obj['taxonomy'];

   }
   elseif($obj['key'] === 'meta'){
	   $param = $obj['meta_key'];
   }
   else{
	   $param = $obj['key'];
   }

   return $param;
}



/**
 * alm_filters_is_archive
 * Is the current page a front page or an archive, add _ to prevent redirects
 *
 * @since 1.8.1
 */
function alm_filters_is_archive(){
	return (is_home() || is_front_page() || is_archive()) ? true : false;
}



/**
 * alm_filters_add_underscore
 * Is the current page a front page or an archive, add _ to prevent redirects
 *
 * @since 1.8.1
 */
function alm_filters_add_underscore(){
	return (alm_filters_is_archive()) ? '_' : '';
}



/**
 * alm_filters_remove_underscore
 * Remove the underscore from the key.
 *
 * @param $str String
 * @since 1.8.1
 */
function alm_filters_remove_underscore($str){
	$first = $str[0];
	return ($first === '_') ? substr($str, 1) : $str;
}
