<?php	
	
if(!class_exists('ALMWooCustomizer')) :

	class ALMWooCustomizer {
		
		/**
		 * Setup the class
		 */
		public function setup() {	
			// empty
		}
		
		
		public static function loading_style(){
			$alm_options = get_option( 'alm_settings' );
			return (isset($alm_options['_alm_btn_color'])) ? $alm_options['_alm_btn_color'] : 'default';
		}
		
		public static function default_button_label(){
			return apply_filters('alm_woocommerce_button_label', __( 'Load More Products', 'alm-woocommerce' ));
		}
		
		public static function default_button_loading_label(){
			return apply_filters('alm_woocommerce_button_loading_label', '');
		}
		
		
	}
	
	
	function ALMWooCustomizer(){
		$ALMWooCustomizer = new ALMWooCustomizer();
		$ALMWooCustomizer->setup();
	}    
	// initialize
	ALMWooCustomizer();
	
endif;
