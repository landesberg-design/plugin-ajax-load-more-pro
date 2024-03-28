<?php
/**
 * Plugin Name: Ajax Load More: Preloaded
 * Plugin URI: http://connekthq.com/plugins/ajax-load-more/preloaded/
 * Description: Ajax Load More extension to preload content before making Ajax requests to your server.
 * Author: Darren Cooney
 * Twitter: @KaptonKaos
 * Author URI: http://connekthq.com
 * Version: 1.4.0
 * License: GPL
 * Copyright: Darren Cooney & Connekt Media
 *
 * @package ALMPreloaded 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ALM_PRELOADED_PATH', plugin_dir_path( __FILE__ ) );
define( 'ALM_PRELOADED_URL', plugins_url( '', __FILE__ ) );
define( 'ALM_PRELOADED_VERSION', '1.4.0' );
define( 'ALM_PRELOADED_RELEASE', 'January 16, 2024' );

/**
 * Activation hook
 *
 *  @since 1.0
 */
function alm_preloaded_install() {
	if ( ! is_plugin_active( 'ajax-load-more/ajax-load-more.php' ) ) {
		set_transient( 'alm_preloaded_admin_notice', true, 5 );
	}
}
register_activation_hook( __FILE__, 'alm_preloaded_install' );

/**
 * Display admin notice and de-activate if plugin does not meet the requirements.
 *
 * @since 2.0.0
 */
function alm_preloaded_admin_notice() {
	$slug   = 'ajax-load-more';
	$plugin = $slug . '-preloaded';
	// Ajax Load More Notice.
	if ( get_transient( 'alm_preloaded_admin_notice' ) ) {
		$install_url = get_admin_url() . '/update.php?action=install-plugin&plugin=' . $slug . '&_wpnonce=' . wp_create_nonce( 'install-plugin_' . $slug );
		$message     = '<div class="error">';
		$message    .= '<p>' . __( 'You must install and activate the core Ajax Load More plugin before using the Ajax Load More Preloaded Add-on.', 'ajax-load-more-preloaded' ) . '</p>';
		$message    .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, __( 'Install Ajax Load More Now', 'ajax-load-more-preloaded' ) ) . '</p>';
		$message    .= '</div>';
		echo wp_kses_post( $message );
		delete_transient( 'alm_preloaded_admin_notice' );
	}
}
add_action( 'admin_notices', 'alm_preloaded_admin_notice' );

if ( ! class_exists( 'ALMPreloaded' ) ) :
	/**
	 * Ajax Load More Preloaded Class
	 */
	class ALMPreloaded {

		/**
		 * Constuct function.
		 */
		public function __construct() {
			add_action( 'alm_preload_installed', [ &$this, 'alm_is_preloaded_installed' ] );
			add_filter( 'alm_preload_args', [ &$this, 'alm_preloaded_args' ], 10, 1 );
			add_filter( 'alm_preload_inc', [ &$this, 'alm_preloaded_inc' ], 10, 7 );
			add_action( 'alm_preloaded_settings', [ &$this, 'alm_preloaded_settings' ] );
		}

		/**
		 * Build the preload query $args
		 *
		 * @since 1.0
		 */
		public function alm_preloaded_args( $a ) {

			$post_id          = $a['post_id'];
			$posts_per_page   = $a['posts_per_page'];
			$post_type        = explode( ',', $a['post_type'] );
			$post_format      = ( isset( $a['post_format'] ) ) ? $a['post_format'] : '';
			$category         = ( isset( $a['category'] ) ) ? $a['category'] : '';
			$category__not_in = ( isset( $a['category__not_in'] ) ) ? $a['category__not_in'] : '';
			$tag              = ( isset( $a['tag'] ) ) ? $a['tag'] : '';
			$tag__not_in      = ( isset( $a['tag__not_in'] ) ) ? $a['tag__not_in'] : '';

			// Taxonomy
			$taxonomy          = ( isset( $a['taxonomy'] ) ) ? $a['taxonomy'] : '';
			$taxonomy_terms    = ( isset( $a['taxonomy_terms'] ) ) ? $a['taxonomy_terms'] : '';
			$taxonomy_operator = $a['taxonomy_operator'];
			if ( empty( $taxonomy_operator ) ) {
				$taxonomy_operator = 'IN';
			}
			$taxonomy_relation = $a['taxonomy_relation'];

			// Date
			$year  = ( isset( $a['year'] ) ) ? $a['year'] : '';
			$month = ( isset( $a['month'] ) ) ? $a['month'] : '';
			$day   = ( isset( $a['day'] ) ) ? $a['day'] : '';

			// Custom Fields
			$meta_key     = ( isset( $a['meta_key'] ) ) ? $a['meta_key'] : '';
			$meta_value   = ( isset( $a['meta_value'] ) ) ? $a['meta_value'] : '';
			$meta_compare = $a['meta_compare'];
			if ( $meta_compare == '' ) {
				$meta_compare = 'IN';
			}
			if ( $meta_compare === 'lessthan' ) {
				$meta_compare = '<'; // do_shortcode fix (shortcode was rendering as HTML)
			}
			if ( $meta_compare === 'lessthanequalto' ) {
				$meta_compare = '<='; // do_shortcode fix (shortcode was rendering as HTML)
			}
			$meta_relation = $a['meta_relation'];
			if ( $meta_relation == '' ) {
				$meta_relation = 'AND';
			}
			$meta_type = $a['meta_type'];
			if ( $meta_type == '' ) {
				$meta_type = 'CHAR';
			}

			$s           = ( isset( $a['search'] ) ) ? $a['search'] : '';
			$custom_args = ( isset( $a['custom_args'] ) ) ? $a['custom_args'] : '';

			// Author
			$author_id = ( isset( $a['author'] ) ) ? $a['author'] : '';

			// Ordering
			$order   = ( isset( $a['order'] ) ) ? $a['order'] : 'DESC';
			$orderby = ( isset( $a['orderby'] ) ) ? $a['orderby'] : 'date';

			// Sticky, Include, Exclude, Offset, Status
			$sticky       = ( isset( $a['sticky_posts'] ) ) ? $a['sticky_posts'] : '';
			$sticky       = ( $sticky === 'true' ) ? true : false;
			$post__in     = ( isset( $a['post__in'] ) ) ? $a['post__in'] : '';
			$post__not_in = ( isset( $a['post__not_in'] ) ) ? $a['post__not_in'] : '';
			$exclude      = ( isset( $a['exclude'] ) ) ? $a['exclude'] : '';
			$offset       = ( isset( $a['offset'] ) ) ? $a['offset'] : 0;
			$post_status  = $a['post_status'];
			if ( $post_status == '' ) {
				$post_status = 'publish';
			}
			if ( $post_status != 'publish' && $post_status != 'inherit' ) {
				// If not 'publish', confirm user has rights to view these old posts.
				if ( current_user_can( 'edit_theme_options' ) ) {
					$post_status = $post_status;
				} else {
					$post_status = 'publish';
				}
			}

			// Advanced Custom Fields
			$acf = ( isset( $a['acf'] ) ) ? $a['acf'] : false;
			if ( $acf === 'true' ) {
				$acf_post_id    = ( isset( $a['acf_post_id'] ) ) ? $a['acf_post_id'] : ''; // Post ID
				$acf_field_type = ( isset( $a['acf_field_type'] ) ) ? $a['acf_field_type'] : ''; // ACF Field Type
				$acf_field_name = ( isset( $a['acf_field_name'] ) ) ? $a['acf_field_name'] : ''; // ACF Field Type
			}

			// Create $args array
			$args = [
				'post_type'           => $post_type,
				'posts_per_page'      => $posts_per_page,
				'offset'              => $offset,
				'order'               => $order,
				'orderby'             => $orderby,
				'post_status'         => $post_status,
				'ignore_sticky_posts' => true,
			];

			// Post Format & Taxonomy
			// * Both use tax_query, so we need to combine these queries
			if ( ! empty( $post_format ) || ! empty( $taxonomy ) ) {

				$tax_query_total   = count( explode( ':', $taxonomy ) ); // Total $taxonomy objects
				$taxonomy          = explode( ':', $taxonomy ); // convert to array
				$taxonomy_terms    = explode( ':', $taxonomy_terms ); // convert to array
				$taxonomy_operator = explode( ':', $taxonomy_operator ); // convert to array

				if ( empty( $taxonomy ) ) {

					// Post Format only
					$args['tax_query'] = [
						alm_get_post_format( $post_format ),
					];

				} else {

					// Post Formats
					$args['tax_query'] = [
						'relation' => $taxonomy_relation,
						alm_get_post_format( $post_format ),
					];

					// Loop Taxonomies
					for ( $tax_i = 0; $tax_i < $tax_query_total; $tax_i++ ) {
						$args['tax_query'][] = alm_get_taxonomy_query( $taxonomy[ $tax_i ], $taxonomy_terms[ $tax_i ], $taxonomy_operator[ $tax_i ] );
					}
				}
			}

			// Category
			if ( ! empty( $category ) ) {
				$args['category_name'] = $category;
			}

			// Category Not In
			if ( ! empty( $category__not_in ) ) {
				$exclude_cats             = explode( ',', $category__not_in );
				$args['category__not_in'] = $exclude_cats;
			}

			// Tag
			if ( ! empty( $tag ) ) {
				$args['tag'] = $tag;
			}

			// Tag Not In
			if ( ! empty( $tag__not_in ) ) {
				$exclude_tags        = explode( ',', $tag__not_in );
				$args['tag__not_in'] = $exclude_tags;
			}

			// Date (not using date_query as there was issue with year/month archives)
			if ( ! empty( $year ) ) {
				$args['year'] = $year;
			}
			if ( ! empty( $month ) ) {
				$args['monthnum'] = $month;
			}
			if ( ! empty( $day ) ) {
				$args['day'] = $day;
			}

			// Meta Query
			if ( ! empty( $meta_key ) && ! empty( $meta_value ) || ! empty( $meta_key ) && $meta_compare !== 'IN' ) {

				// Parse multiple meta query
				$meta_query_total = count( explode( ':', $meta_key ) ); // Total meta_query objects
				$meta_keys        = explode( ':', $meta_key ); // convert to array
				$meta_value       = explode( ':', $meta_value ); // convert to array
				$meta_compare     = explode( ':', $meta_compare ); // convert to array
				$meta_type        = explode( ':', $meta_type ); // convert to array

				// Loop Meta Query
				$args['meta_query'] = [
					'relation' => $meta_relation,
				];
				for ( $mq_i = 0; $mq_i < $meta_query_total; $mq_i++ ) {
					$args['meta_query'][] = alm_get_meta_query( $meta_keys[ $mq_i ], $meta_value[ $mq_i ], $meta_compare[ $mq_i ], $meta_type[ $mq_i ] );
				}
			}

			// Meta_key, used for ordering by meta value
			if ( ! empty( $meta_key ) ) {
				if ( strpos( $orderby, 'meta_value' ) !== false ) { // Only order by meta_key, if $orderby is set to meta_value{_num}
					$meta_key_single  = explode( ':', $meta_key );
					$args['meta_key'] = $meta_key_single[0];
				}
			}

			// Author
			if ( ! empty( $author_id ) ) {
				$args['author'] = $author_id;
			}

			// Search Term
			if ( ! empty( $s ) ) {
				$args['s'] = $s;
			}

			// Custom Args
			if ( ! empty( $custom_args ) ) {
				$custom_args_array = explode( ';', $custom_args ); // Split the $custom_args at ','
				foreach ( $custom_args_array as $argument ) { // Loop each $argument

					$argument     = preg_replace( '/\s+/', '', $argument ); // Remove all whitespace
					$argument     = explode( ':', $argument );  // Split the $argument at ':'
					$argument_arr = explode( ',', $argument[1] );  // explode $argument[1] at ','
					if ( sizeof( $argument_arr ) > 1 ) {
						$args[ $argument[0] ] = $argument_arr;
					} else {
						$args[ $argument[0] ] = $argument[1];
					}
				}
			}

			// Include posts
			if ( ! empty( $post__in ) ) {
				$post__in         = explode( ',', $post__in );
				$args['post__in'] = $post__in;
			}

			// Exclude posts
			if ( ! empty( $post__not_in ) ) {
				$post__not_in         = explode( ',', $post__not_in );
				$args['post__not_in'] = $post__not_in;
			}
			if ( ! empty( $exclude ) ) { // Deprecate this soon - 2.8.5 */
				$exclude              = explode( ',', $exclude );
				$args['post__not_in'] = $exclude;
			}

			// Language
			if ( ! empty( $lang ) ) {
				$args['lang'] = $lang;
			}

			// Sticky Posts
			if ( $sticky ) {
				$sticky_posts = get_option( 'sticky_posts' ); // Get all sticky post ids

				// If more sticky posts than $posts_per_page run a secondary query to get posts to fill query.
				if ( count( $sticky_posts ) <= $posts_per_page ) {

					$sticky_query_args                   = $args;
					$sticky_query_args['post__not_in']   = $sticky_posts;
					$sticky_query_args['posts_per_page'] = $posts_per_page;
					$sticky_query_args['fields']         = 'ids';

					$sticky_query = new WP_Query( $sticky_query_args ); // Query all non sticky posts

					// If has sticky and regular posts
					if ( $sticky_posts && $sticky_query->posts ) {
							$standard_posts = $sticky_query->posts;
						if ( $standard_posts ) {
							$sticky_ids       = array_merge( $sticky_posts, $standard_posts ); // merge regular posts with sticky
							$args['post__in'] = $sticky_ids;
							$args['orderby']  = 'post__in'; // set orderby to order by post__in.
						}
					}
				} else {
					// Pass get_option('sticky_posts');
					$sticky_ids = $sticky_posts;

				}

				// If has sticky posts.
				if ( $sticky_posts ) {
					$args['post__in'] = $sticky_ids;
					$args['orderby']  = 'post__in'; // set orderby to order by post__in.
				}
			}

			// Advanced Custom Fields
			if ( ! empty( $acf ) && ! empty( $acf_field_type ) && ! empty( $acf_field_name ) && function_exists( 'get_field' ) ) {
				if ( $acf_field_type === 'relationship' ) { // Relationship Field
					if ( empty( $acf_post_id ) ) {
						$acf_post_id = $post_id;
					}
					$acf_post_ids = get_field( $acf_field_name, $acf_post_id ); // Get field value from ACF
					if ( $acf_post_ids ) {
						$args['post__in'] = $acf_post_ids;
					} else {
						$args['post__in'] = [ 0 ];
					}
				}
			}

			return $args;
		}

		/**
		 * Get the preloaded post include file
		 *
		 * @since 1.0
		 */
		public function alm_preloaded_inc( $repeater, $preload_type, $theme_repeater, $alm_found_posts, $alm_page, $alm_item, $alm_current ) {
			ob_start();
			if ( $theme_repeater !== 'null' && has_filter( 'alm_get_theme_repeater' ) ) {
				do_action( 'alm_get_theme_repeater', $theme_repeater, $alm_found_posts, $alm_page, $alm_item, $alm_current );
			} else {
				$file = alm_get_current_repeater( $repeater, $preload_type );
				include $file;
			}
			$return = ob_get_contents();
			ob_end_clean();
			return $return;
		}



		/**
		 * Empty function to determine if preload is true.
		 *
		 * @since 1.0
		 */
		public function alm_is_preloaded_installed() {
			// Empty return.
		}



		/**
		 * Create the Preloaded settings panel.
		 *
		 * @since 1.2
		 */
		public function alm_preloaded_settings() {
			register_setting(
				'alm_preloaded_license',
				'alm_preloaded_license_key',
				'alm_preloaded_sanitize_license'
			);
		}
	}



	/**
	 * Sanitize our license activation
	 *
	 * @since 1.0.0
	 */

	function alm_preloaded_sanitize_license( $new ) {
		$old = get_option( 'alm_preloaded_license_key' );
		if ( $old && $old != $new ) {
				delete_option( 'alm_preloaded_license_status' ); // new license has been entered, so must reactivate
		}
		return $new;
	}

	/**
	 * The main function responsible for returning Ajax Load More PRELOAD.
	 *
	 *  @since 1.0
	 */
	function alm_preloaded() {
		global $alm_preloaded_posts;
		if ( ! isset( $alm_preloaded_posts ) ) {
			$alm_preloaded_posts = new ALMPreloaded();
		}
		return $alm_preloaded_posts;
	}
	alm_preloaded();
endif;

/**
 * Software Licensing
 */
function alm_preloaded_plugin_updater() {
	if ( ! has_action( 'alm_pro_installed' ) && class_exists( 'EDD_SL_Plugin_Updater' ) ) {
		$license_key = trim( get_option( 'alm_preloaded_license_key' ) );
		$edd_updater = new EDD_SL_Plugin_Updater(
			ALM_STORE_URL,
			__FILE__,
			[
				'version' => ALM_PRELOADED_VERSION,
				'license' => $license_key,
				'item_id' => ALM_PRELOADED_ITEM_NAME,
				'author'  => 'Darren Cooney',
			]
		);
	}
}
add_action( 'admin_init', 'alm_preloaded_plugin_updater', 0 );
/* End Software Licensing */
