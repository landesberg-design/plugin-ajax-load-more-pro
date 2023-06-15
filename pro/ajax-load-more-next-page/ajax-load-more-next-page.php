<?php
/**
 * Plugin Name: Ajax Load More: Next Page
 * Plugin URI: https://connekthq.com/plugins/ajax-load-more/add-ons/next-page/
 * Description: Ajax Load More add-on for displaying multipage WordPress content
 * Author: Darren Cooney
 * Twitter: @KaptonKaos
 * Author URI: https://connekthq.com
 * Version: 1.6.4
 * License: GPL
 * Copyright: Darren Cooney & Connekt Media
 *
 * @package ALMNextPage
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ALM_NEXTPAGE_PATH', plugin_dir_path( __FILE__ ) );
define( 'ALM_NEXTPAGE_URL', plugins_url( '', __FILE__ ) );
define( 'ALM_NEXTPAGE_VERSION', '1.6.4' );
define( 'ALM_NEXTPAGE_RELEASE', 'June 11, 2023' );

/**
 * Activation hook.
 *
 * @since 1.0
 */
function alm_nextpage_install() {
	if ( ! is_plugin_active( 'ajax-load-more/ajax-load-more.php' ) ) {
		set_transient( 'alm_nextpage_admin_notice', true, 5 );
	}
}
register_activation_hook( __FILE__, 'alm_nextpage_install' );

/**
 * Display admin notice and de-activate if plugin does not meet the requirements.
 *
 * @since 1.6.0
 */
function alm_nextpage_admin_notice() {
	$slug   = 'ajax-load-more';
	$plugin = $slug . '-next-page';
	// Ajax Load More Notice.
	if ( get_transient( 'alm_nextpage_admin_notice' ) ) {
		$install_url = get_admin_url() . '/update.php?action=install-plugin&plugin=' . $slug . '&_wpnonce=' . wp_create_nonce( 'install-plugin_' . $slug );
		$message     = '<div class="error">';
		$message    .= '<p>' . __( 'You must install and activate the core Ajax Load More plugin before using the Ajax Load More Next Page Add-on.', 'ajax-load-more-nextpage' ) . '</p>';
		$message    .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, __( 'Install Ajax Load More Now', 'ajax-load-more-nextpage' ) ) . '</p>';
		$message    .= '</div>';
		echo wp_kses_post( $message );
		delete_transient( 'alm_nextpage_admin_notice' );
	}
}
add_action( 'admin_notices', 'alm_nextpage_admin_notice' );


if ( ! class_exists( 'ALM_Nextpage_Plugin' ) ) :

	/**
	 * ALM NextPage Class.
	 */
	class ALM_Nextpage_Plugin {

		/**
		 * Construct function.
		 */
		public function __construct() {
			$this->init = true;
			add_action( 'alm_nextpage_installed', array( &$this, 'alm_nextpage_installed' ) );
			add_filter( 'alm_init_nextpage', array( &$this, 'alm_init_nextpage' ), 10, 7 );
			add_filter( 'alm_nextpage_wrap_start', array( &$this, 'alm_nextpage_wrap_start' ), 10, 6 );
			add_filter( 'alm_nextpage_wrap_end', array( &$this, 'alm_nextpage_wrap_end' ), 10, 1 );
			add_action( 'wp_ajax_alm_nextpage', array( &$this, 'alm_nextpage_query' ) );
			add_action( 'wp_ajax_nopriv_alm_nextpage', array( &$this, 'alm_nextpage_query' ) );
			add_filter( 'alm_nextpage_shortcode', array( &$this, 'alm_nextpage_shortcode' ), 10, 6 );
			add_filter( 'alm_nextpage_total_pages', array( &$this, 'alm_nextpage_total_pages' ), 10, 3 );
			add_filter( 'alm_nextpage_noscript_paging', array( &$this, 'alm_nextpage_noscript_paging' ), 10 );
			add_action( 'alm_nextpage_settings', array( &$this, 'alm_nextpage_settings' ) );
			add_filter( 'the_content', array( &$this, 'alm_nextpage_the_content' ), 1 );
			add_action( 'wp_enqueue_scripts', array( &$this, 'alm_nextpage_enqueue_scripts' ) );
			load_plugin_textdomain( 'ajax-load-more-nextpage', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
			$this->alm_nextpage_includes();
		}

		/**
		 * Load these files before the theme loads.
		 *
		 * @since 1.6.2
		 */
		public function alm_nextpage_includes() {
			require_once ALM_NEXTPAGE_PATH . 'functions.php';
		}

		/**
		 * Filter the_content to autostart the Next Page functionality.
		 *
		 * @see https://developer.wordpress.org/reference/hooks/the_content/
		 *
		 * @param string $content The content from WordPress.
		 * @author ConnektMedia
		 * @since 1.6.0
		 */
		public function alm_nextpage_the_content( $content ) {
			if ( ! $this->init ) {
				return $content;
			}

			// Check if we're inside the main loop in a single Post.
			if ( in_the_loop() && is_main_query() ) {

				global $post;
				$post_name = isset( $post ) && isset( $post->post_name ) ? $post->post_name : '';

				// Get current post type.
				$post_type = get_post_type();

				// Get plugin options.
				$options       = get_option( 'alm_settings' );
				$post_type_opt = '_alm_nextpage_post_types';

				// Coming Soon is support for post slugs.
				$post_slugs       = isset( $options['_alm_nextpage_slugs'] ) ? nl2br( $options['_alm_nextpage_slugs'] ) : '';
				$post_slugs_array = $post_slugs ? explode( '<br />', $post_slugs ) : [];

				// Get selected post types from ALM Settings and initiate Next Page.
				$selected_post_types = isset( $options[ $post_type_opt ] ) && isset( $options[ $post_type_opt ]['post_types'] ) ? (array) $options[ $post_type_opt ]['post_types'] : [];

				// Post Types & Post Slugs.
				if ( in_array( $post_type, $selected_post_types, true ) || in_array( $post_name, $post_slugs_array, true ) ) {
					$this->init = false;
					$do_display = true;
					$shortcode  = isset( $options[ $post_type_opt ] ) && isset( $options[ $post_type_opt ]['shortcodes'] ) && isset( $options[ $post_type_opt ]['shortcodes'][ $post_type ] ) && ! empty( $options[ $post_type_opt ]['shortcodes'][ $post_type ] ) ? $options[ $post_type_opt ]['shortcodes'][ $post_type ] : '[ajax_load_more nextpage="true"]';

					// Parse shortcode to determine if shortcode should be rendered by specific term.
					$query_params = alm_nextpage_parse_query_params( alm_nextpage_parse_shortcode_atts( $shortcode ) );
					if ( $query_params ) {
						// Conditional render based on taxonomy terms.
						if ( ! has_term( $query_params['terms'], $query_params['tax'], $post ) ) {
							return $content; // Not in term, return orginal $content.
						}
					}

					$content = '<style>';
					if ( isset( $options['_alm_nextpage_css'] ) && ! empty( $options['_alm_nextpage_css'] ) ) {
						$content .= $options['_alm_nextpage_css'];
					}
					$content .= '.post-nav-links{ display: none; }';
					$content .= '</style>';
					$content .= $shortcode;
					return $content;
				}
			}
			return $content;
		}

		/**
		 * Enqueue plugin scripts.
		 *
		 * @author ConnektMedia
		 * @since 1.0
		 */
		public function alm_nextpage_enqueue_scripts() {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_register_script(
				'ajax-load-more-nextpage',
				plugins_url( '/dist/js/alm-next-page' . $suffix . '.js', __FILE__ ),
				array( 'ajax-load-more' ),
				ALM_NEXTPAGE_VERSION,
				true
			);
			// Localize Nextpage Vars.
			wp_localize_script(
				'ajax-load-more-nextpage',
				'alm_nextpage_localize',
				[
					'leading_slash'  => self::get_leading_slash(),
					'trailing_slash' => self::get_trailing_slash(),
				]
			);
		}

		/**
		 * Add a leading slash (/) before the page number.
		 *
		 * @author ConnektMedia
		 * @since 1.1
		 * @return string
		 */
		public static function get_leading_slash() {
			return apply_filters( 'alm_nextpage_leading_slash', false ) ? '/' : '';
		}

		/**
		 * Remove the trailing slash (/) at the end of the URL.
		 *
		 * @author ConnektMedia
		 * @since 1.1
		 * @return string
		 */
		public static function get_trailing_slash() {
			return apply_filters( 'alm_nextpage_remove_trailing_slash', false ) ? '' : '/';
		}

		/**
		 * Query nextpage, send results via ajax.
		 *
		 * @author ConnektMedia
		 * @since 1.0
		 * @return object
		 */
		public function alm_nextpage_query() {
			$params = filter_input_array( INPUT_GET, FILTER_SANITIZE_STRING );
			if ( ! isset( $params ) ) {
				return false;
			}

			$query_type    = isset( $params['query_type'] ) ? $params['query_type'] : 'standard';
			$page          = isset( $params['page'] ) ? $params['page'] : 1;
			$id            = isset( $params['id'] ) ? $params['id'] : '';
			$data          = isset( $params['nextpage'] ) ? $params['nextpage'] : '';
			$paging        = isset( $params['paging'] ) ? $params['paging'] : 'false';
			$canonical_url = isset( $params['canonical_url'] ) ? $params['canonical_url'] : $_SERVER['HTTP_REFERER'];

			// Cache Add-on.
			$cache_id        = isset( $params['cache_id'] ) ? $params['cache_id'] : '';
			$cache_slug      = isset( $params['cache_slug'] ) && $params['cache_slug'] ? $params['cache_slug'] : '';
			$cache_logged_in = isset( $params['cache_logged_in'] ) ? $params['cache_logged_in'] : false;
			$do_create_cache = $cache_logged_in === 'true' && is_user_logged_in() ? false : true;

			// Paging Add-on.
			$paging = 'false' === $paging ? false : $paging;

			if ( $data ) {
				$nextpage  = isset( $data['nextpage'] ) ? $data['nextpage'] : false;
				$post_id   = isset( $data['post_id'] ) ? $data['post_id'] : null;
				$startpage = isset( $data['startpage'] ) ? $data['startpage'] : 0;
				$base_url  = get_permalink( $post_id ); // base_url.
				$nested    = isset( $data['nested'] ) && 'true' === $data['nested'] ? true : false;

				if ( 'totalpages' === $query_type ) {
					// Get totalpages for Paging Add-on.
					wp_send_json(
						[
							'totalpages' => apply_filters( 'alm_nextpage_total_pages', $post_id, $id ),
						]
					);

				} else {
					// Regular nextpage query.
					$postcount = 1;

					// if $startpage > 1 (e.g. user lands on /3/ etc.).
					if ( $startpage > 1 ) {
						if ( ! $paging ) {
							$page = $page + $startpage;
						}
					} else {
						if ( ! $paging ) {
							$page = $page + 1; //phpcs:ignore
						}
					}

					if ( $nextpage === 'true' ) {
						global $post;
						$new_post = get_post( $post_id ); // Must be called $post.

						// Run setup_postdata for oEmbeds in content.
						setup_postdata( $new_post );

						// Support for Visual Composer.
						if ( method_exists( 'WPBMap', 'addAllMappedShortcodes' ) ) {
							WPBMap::addAllMappedShortcodes();
						}

						// Get post content.
						$content = $new_post->post_content;

						// Split $content into array.
						$content = self::alm_nextpage_content( $content, $id );

						// Get total page count.
						$totalposts = count( $content );

						if ( isset( $content[ $page ] ) ) {

							// Prepend `alm_nextpage_break_{id}` value to page.
							$content[ $page ] = ( $page >= 1 ) ? apply_filters( 'alm_nextpage_break_' . $id, '' ) . $content[ $page ] : $content[ $page ];

							// Gutenberg Blocks
							// Remove Gutenberg html comments. These were causing issues with ACF blocks.
							$content = str_replace( '<!-- wp:nextpage -->', '', $content );
							$content = str_replace( '<!-- /wp:nextpage -->', '', $content );

							// Apply `the_content` core WP filter.
							$content = apply_filters( 'the_content', $content[ $page ] ); // phpcs:ignore

							$current   = $page + 1;
							$permalink = $base_url . self::get_leading_slash() . ( $current ) . self::get_trailing_slash();

							$html = apply_filters( 'alm_nextpage_wrap_start', $post_id, $permalink, $current, $totalposts, false, $nested );

							/**
							 * ALM Nextpage Filter Hook
							 *
							 * @return string
							 */
							if ( has_filter( 'alm_nextpage_before' ) ) {
								$html .= apply_filters( 'alm_nextpage_before', $page + 1 );
							}

							/**
							 * ALM Nextpage Filter Hook
							 *
							 * @return string
							 */
							if ( has_filter( 'alm_nextpage_the_content' ) ) {
								$content = apply_filters( 'alm_nextpage_the_content', $content, intval( $page ) + 1 );
							}

							$html .= $content;

							/**
							 * ALM Nextpage Filter Hook
							 *
							 * @return string
							 */
							if ( has_filter( 'alm_nextpage_after' ) ) {
								$html .= apply_filters( 'alm_nextpage_after', $page + 1 );
							}

							$html .= apply_filters( 'alm_nextpage_wrap_end', '' );

							if ( ! empty( $content ) ) {
								$return = [
									'html' => $html,
									'meta' => [
										'postcount'  => $postcount,
										'totalposts' => $totalposts,
										'type'       => 'standard',
									],
								];

								/**
								 * Cache Add-on.
								 * Create the cache file.
								 */
								if ( $cache_id && method_exists( 'ALMCache', 'create_cache_file' ) && $do_create_cache ) {
									$cache_page = $page + 1;
									ALMCache::create_cache_file( $cache_id, $cache_slug, $canonical_url, $html, 1, $totalposts );
								}
							} else {
								$return = [
									'html' => '',
									'meta' => [
										'postcount'  => null,
										'totalposts' => null,
										'type'       => 'standard',
									],
								];
							}
						} else {
							$return = [
								'html' => '',
								'meta' => [
									'postcount'  => null,
									'totalposts' => null,
									'type'       => 'standard',
								],
							];
						}
						wp_send_json( $return );
					}
				}
			}
			wp_die();
		}

		/**
		 * Get the initial server-side load of a nextpage post.
		 *
		 * @author ConnektMedia
		 * @since 1.0
		 * @param string  $post_id      The post ID.
		 * @param string  $page         The current page number.
		 * @param boolean $is_paged    Is this a paged URL.
		 * @param boolean $paging      Is this ALM Paging.
		 * @param string  $div_id       The current div ID.
		 * @param string  $id           The current ALM ID.
		 * @param boolean $nested      Is this a nested ALM instance.
		 * @return string $the_content
		 */
		public static function alm_init_nextpage( $post_id = null, $page = 0, $is_paged = false, $paging = false, $div_id = '', $id = '', $nested = false ) {
			if ( ! $post_id ) {
				return false; // Exit early if missing post_id.
			}

			$the_content = '';
			$nested      = isset( $nested ) && 'true' === $nested ? true : false;
			$the_post    = get_post( $post_id );
			$content     = $the_post->post_content;
			$content     = self::alm_nextpage_content( $content, $id ); // Get the content.
			$totalposts  = count( $content ); // Get total page count.
			$page     = $page - 1; // phpcs:ignore
			$current     = $page;

			// Has user disabled loading previous pages.
			if ( has_filter( 'alm_nextpage_paged' ) ) {
				$nextpage_is_paged = apply_filters( 'alm_nextpage_paged', false );
				if ( ! $nextpage_is_paged ) {
					$current = $current + 1; // phpcs:ignore
				}
			}

			if ( ! $is_paged ) {

				// Not paged, return only a single page.
				$base_url = get_permalink( $post_id );
				if ( $current > 1 ) {
					$permalink = $base_url . self::get_leading_slash() . ( $current ) . self::get_trailing_slash();
				} else {
					$permalink = $base_url;
				}

				$the_content .= apply_filters( 'alm_nextpage_wrap_start', $post_id, $permalink, $current, $totalposts, true, $nested );

				/**
				 * ALM Nextpage Filter Hook.
				 *
				 * @return string
				 */
				if ( has_filter( 'alm_nextpage_before' ) ) {
					$the_content .= apply_filters( 'alm_nextpage_before', $page + 1 );
				}

				// Filter WP Content.
				$content_filtered = apply_filters( 'the_content', $content[ $page ] ); // phpcs:ignore

				/**
				 * ALM Nextpage Filter Hook.
				 *
				 * @return string
				 */
				if ( has_filter( 'alm_nextpage_the_content' ) ) {
					$content_filtered = apply_filters( 'alm_nextpage_the_content', $content_filtered, $page + 1 );
				}

				$the_content .= $content_filtered;

				/**
				 *  ALM Nextpage Filter Hook
				 *
				 * @return string
				 */
				if ( has_filter( 'alm_nextpage_after' ) ) {
					$the_content .= apply_filters( 'alm_nextpage_after', $page + 1 );
				}

				$the_content .= apply_filters( 'alm_nextpage_wrap_end', '</div>' );

			} else {

				// Split pages up into individual content blocks.

				if ( 'true' === $paging ) {
					// Paging Add-on.

					$permalink    = get_permalink( $post_id );
					$the_content .= apply_filters( 'alm_nextpage_wrap_start', $post_id, $permalink, $page, $totalposts, true, $nested );

					/**
					 * ALM Nextpage Filter Hook
					 *
					 * @return string
					*/
					if ( has_filter( 'alm_nextpage_before' ) ) {
						$the_content .= apply_filters( 'alm_nextpage_before', $page + 1 );
					}

					// Prepend `alm_nextpage_break_{id}` value to page.
					$content[ $page ] = apply_filters( 'alm_nextpage_break_' . $id, '' ) . $content[ $page ];

					// Filter WP Content.
					$content_filtered = apply_filters( 'the_content', $content[ $page ] );  // phpcs:ignore

					/**
					 * ALM Nextpage Filter Hook.
					 *
					 * @return string
					 */
					if ( has_filter( 'alm_nextpage_the_content' ) ) {
						$content_filtered = apply_filters( 'alm_nextpage_the_content', $content_filtered, $page + 1 );
					}

					$the_content .= $content_filtered;

					/**
					 * ALM Nextpage Filter Hook
					 *
					 * @return string
					 */
					if ( has_filter( 'alm_nextpage_after' ) ) {
						$the_content .= apply_filters( 'alm_nextpage_after', $page + 1 );
					}

					$the_content .= apply_filters( 'alm_nextpage_wrap_end', '' );

				} else {
					// Standard.

					for ( $i = 0; $i <= $page; $i++ ) { // Loop pages and build return.

						if ( $i < 1 ) {
							$permalink = get_permalink( $post_id );
						} else {
							$permalink = get_permalink( $post_id ) . self::get_leading_slash() . ( $i + 1 ) . self::get_trailing_slash();
						}

						$current      = $i + 1;
						$the_content .= apply_filters( 'alm_nextpage_wrap_start', $post_id, $permalink, $current, $totalposts, true );

						/**
						 * ALM Nextpage Filter Hook
						 *
						 * @return string
						 */
						if ( has_filter( 'alm_nextpage_before' ) ) {
							$the_content .= apply_filters( 'alm_nextpage_before', $page + 1 );
						}

						if ( $i > 0 ) {
							// Prepend `alm_nextpage_break_{id}` value to page.
							$content[ $i ] = apply_filters( 'alm_nextpage_break_' . $id, '' ) . $content[ $i ];
						}

						// Filter WP content.
						$content_filtered = apply_filters( 'the_content', $content[ $i ] ); // phpcs:ignore

						/**
						 * ALM Nextpage Filter Hook.
						 *
						 * @return string
						 */
						if ( has_filter( 'alm_nextpage_the_content' ) ) {
							$content_filtered = apply_filters( 'alm_nextpage_the_content', $content_filtered, $i + 1 );
						}

						$the_content .= $content_filtered;

						/**
						 * ALM Nextpage Filter Hook
						 *
						 * @return string
						 */
						if ( has_filter( 'alm_nextpage_after' ) ) {
							$the_content .= apply_filters( 'alm_nextpage_after', $page + 1 );
						}

						$the_content .= apply_filters( 'alm_nextpage_wrap_end', '' );
					}
				}
			}

			$localized_id = ! empty( $id ) ? 'ajax-load-more-' . $id : $div_id;

			// Add Localized `page` variable.
			ALM_LOCALIZE::add_localized_var( 'page', $page || $current, $localized_id );

			// Add Localized `total_posts` variable.
			ALM_LOCALIZE::add_localized_var( 'total_posts', $totalposts, $localized_id );

			return $the_content;
		}

		/**
		 * Return the total pages for post.
		 *
		 * @author ConnektMedia
		 * @since 1.0
		 * @param string $post_id The post ID.
		 * @param string $id      The Ajax Load More ID.
		 * @return int            Total page count.
		 */
		public function alm_nextpage_total_pages( $post_id = null, $id = '' ) {
			$total_pages = 0;
			if ( $post_id ) {
				$post_content = get_post( $post_id );
				$content      = $post_content->post_content;
				$content      = explode( apply_filters( 'alm_nextpage_break_' . $id, '<!--nextpage-->' ), $content );
				$total_pages  = count( $content );
			}
			return $total_pages;
		}

		/**
		 * Return the html wrapper for nextpage content.
		 *
		 * @param string|int $post_id The post ID.
		 * @param string     $url     The current URL.
		 * @param string|int $page    The current page number.
		 * @param string|int $total   The total pages.
		 * @param boolean    $init    Is this the initial load.
		 * @param boolean    $nested  Is this a nested ALM instance.
		 * @return string             Generated HTML.
		 * @author ConnektMedia
		 * @since 1.0
		 */
		public function alm_nextpage_wrap_start( $post_id = 0, $url = '', $page = 0, $total = 0, $init = false, $nested = false ) {
			$totalpages = $page === 0 ? ' data-total-posts="' . $total . '"' : '';
			$page_num   = $page === 0 ? '1' : $page;
			if ( ! $nested ) {
				if ( $init ) {
					// First load only, get current permalink including querystring.
					$url = $_SERVER['QUERY_STRING'] ? $url . '?' . $_SERVER['QUERY_STRING'] : $url;
				} else {
					$querysrting = self::alm_nextpage_get_querystring();
					$url         = $querysrting ? $url . '?' . $querysrting : $url;
				}
			}
			return '<div class="alm-nextpage post-' . $post_id . '" data-id="' . $post_id . '" data-title="' . get_the_title( $post_id ) . '" data-url="' . $url . '" data-page="' . $page_num . '" data-pages="' . $total . '"' . $totalpages . '>';
		}

		/**
		 * Return the html wrapper closing elements for nextpage content.
		 *
		 * @author ConnektMedia
		 * @since 1.0
		 * @return string
		 */
		public function alm_nextpage_wrap_end() {
			return '</div>';
		}

		/**
		 * Return the content.
		 *
		 * @param string $content The post content.
		 * @param string $id The ALM ID.
		 * @author ConnektMedia
		 * @since 1.3
		 * @return array
		 */
		public static function alm_nextpage_content( $content = '', $id = '' ) {
			/*
			 *	Get content break element.
			 * ALM Nextpage Core Filter Hook.
			 *
			 * @return $content;
			 */
			return explode( apply_filters( 'alm_nextpage_break_' . $id, '<!--nextpage-->' ), $content );
		}

		/**
		 * Generate a paging navigation for <noscript/>.
		 *
		 * @author ConnektMedia
		 * @since 1.4
		 * @param string $post_id The current post ID.
		 * @param string $id   The ALM ID.
		 * @return string
		 */
		public function alm_nextpage_noscript_paging( $post_id = '', $id = '' ) {
			if ( empty( $post_id ) ) {
				return false;
			}
			$post_content = get_post( $post_id );
			$pages        = $post_content ? count( self::alm_nextpage_content( $post_content->post_content, $id ) ) : 0;
			$paging       = '';

			// Loop pages.
			if ( $pages > 1 ) {
				$paging  = '<noscript>';
				$paging .= '<div class="alm-paging" style="opacity: 1">';
				$paging .= __( 'Pages:', 'ajax-load-more-nextpage' ) . ' ';
				for ( $i = 1; $i <= $pages; $i++ ) {
					$paging .= '<span class="page" data-page="' . $i . '">';
					$paging .= '<a href="' . get_permalink( $post_id ) . self::get_leading_slash() . $i . self::get_trailing_slash() . '">' . $i . '</a>';
					$paging .= '</span>';
				}
				$paging .= '</div>';
				$paging .= '</noscript>';
			}
			return $paging;
		}

		/**
		 * Get the current querystring.
		 *
		 * @author ConnektMedia
		 * @since 1.3
		 * @return string
		 */
		public static function alm_nextpage_get_querystring() {
			$url = $_SERVER['HTTP_REFERER']; // Get referring URL.

			$output = '';
			if ( $url ) {
				$parts = wp_parse_url( $url ); // Parse the full URL.

				if ( isset( $parts['query'] ) ) {
					parse_str( $parts['query'], $querystring ); // Parse querystring.
					if ( $querystring ) {
						$index = 0;
						foreach ( $querystring as $key => $value ) {
							$index++;
							$output .= ( $index > 1 ) ? '&' : '';
							$output .= $key . '=' . $value;
						}
					}
				}
			}
			return ( $output ) ? $output : '';
		}

		/**
		 * Build Next Page shortcode params and send back to core ALM.
		 *
		 * @author ConnektMedia
		 * @since 1.0
		 * @param string $urls           Update URL as user scrolls.
		 * @param string $pageviews      Count pageviews.
		 * @param string $post_id        The current Post ID.
		 * @param string $scroll         Should scroll be enabled.
		 * @param string $title_template Browser title template.
		 * @return string $data
		 */
		public function alm_nextpage_shortcode( $urls, $pageviews, $post_id, $scroll, $title_template ) {
			$data  = ' data-nextpage="true"';
			$data .= ' data-nextpage-urls="' . $urls . '"';
			$data .= ' data-nextpage-pageviews="' . $pageviews . '"';
			$data .= ' data-nextpage-post-id="' . $post_id . '"';
			$data .= ' data-nextpage-scroll="' . $scroll . '"';
			$data .= ' data-nextpage-startpage="' . alm_get_startpage() . '"';
			$data .= ' data-nextpage-post-title="' . get_the_title( $post_id ) . '"';
			if ( ! empty( $title_template ) ) {
				$data .= ' data-nextpage-title-template="' . $title_template . '"';
			}
			return $data;
		}

		/**
		 * An empty function to determine if nextpage is true.
		 *
		 * @author ConnektMedia
		 * @since 1.0
		 */
		public function alm_nextpage_installed() {
			// Empty.
		}

		/**
		 * Create the Next Page settings panel.
		 *
		 * @author ConnektMedia
		 * @since 1.0
		 */
		public function alm_nextpage_settings() {
			register_setting(
				'alm_nextpage_license',
				'alm_nextpage_license_key',
				'alm_nextpage_sanitize_license'
			);
			add_settings_section(
				'alm_nextpage_settings',
				__( 'Next Page Settings', 'ajax-load-more-nextpage' ),
				'alm_nextpage_settings_callback',
				'ajax-load-more'
			);
			add_settings_field(
				'_alm_nextpage_post_types',
				__( 'Post Types', 'ajax-load-more-nextpage' ),
				'alm_nextpage_post_types_callback',
				'ajax-load-more',
				'alm_nextpage_settings'
			);
			add_settings_field(
				'_alm_nextpage_css',
				__( 'Custom CSS', 'ajax-load-more-nextpage' ),
				'alm_nextpage_css_callback',
				'ajax-load-more',
				'alm_nextpage_settings'
			);
		}
	}

	/**
	 * Next Page Settings Heading.
	 *
	 * @author ConnektMedia
	 * @since 1.6.0
	 */
	function alm_nextpage_settings_callback() {
		// phpcs:ignore
		echo '<p>' . __( 'Customize your installation of the <a href="http://connekthq.com/plugins/ajax-load-more/add-ons/next-page/">Next Page</a> add-on.', 'ajax-load-more-nextpage' ) . '</p>';
	}

	/**
	 *  Select Post Types for Auto Implementation.
	 *
	 *  @since 1.6.0
	 */
	function alm_nextpage_post_types_callback() {
		$options = get_option( 'alm_settings' );
		$opt     = '_alm_nextpage_post_types';
		$html    = '';
		$exclude = [ 'revision', 'nav_menu_item', 'acf' ];

		$alm_post_types = isset( $options[ $opt ] ) && isset( $options[ $opt ]['post_types'] )
		? (array) $options[ $opt ]['post_types'] : [];

		// Get all post types.
		$types = get_post_types(
			array(
				'public'             => true,
				'publicly_queryable' => true,
			)
		);

		$types['page'] = 'page'; // Add page post type.
		sort( $types ); // Order post types.

		// Exlude these custom post types from other sources.
		$excluded      = [ 'attachment', 'e-landing-page', 'elementor_library' ];
		$exclude_types = apply_filters( 'alm_nextpage_excluded_post_types', $excluded, $excluded );

		// Remove exclude post types.
		$post_types = [];
		foreach ( $types as $type ) {
			if ( ! in_array( $type, $exclude_types, true ) ) {
				$post_types[] = $type;
			}
		}

		if ( $post_types ) {
			$html .= '<p style="margin-bottom: 10px">' . __( 'Activate Next Page functionality on the following post types:', 'ajax-load-more-nextpage' ) . '</p>';
			// phpcs:ignore
			$html .= '<p style="font-size: 12px; margin-bottom: 20px; opacity: 0.75;">' . sprintf( __( 'Once selected, enter the generated Next Page <a href="%s" target="_blank">shortcode</a> for each post type template.', 'ajax-load-more-nextpage' ), '?page=ajax-load-more-shortcode-builder' ) . '</p>';

			foreach ( $post_types as $post_type ) {
				$typeobj       = get_post_type_object( $post_type );
				$name          = $typeobj->name;
				$singular      = $typeobj->labels->singular_name;
				$shortcode_val = isset( $options[ $opt ] ) && isset( $options[ $opt ]['shortcodes'] ) && isset( $options[ $opt ]['shortcodes'][ $name ] ) ? $options[ $opt ]['shortcodes'][ $name ] : '';
				if ( ! in_array( $name, $exclude, true ) ) {
					$active = in_array( $name, $alm_post_types, true ) ? ' active' : '';
					$html  .= '<div class="nextpage-option">';
					$html  .= '<div class="nextpage-option--type">';
					$html  .= '<input id="post_type_' . $name . '" type="checkbox" name="alm_settings[' . $opt . '][post_types][]" ' . checked( in_array( $name, $alm_post_types, true ), 1, false ) . ' value="' . $name . '" />';
					$html  .= '<label for="post_type_' . $name . '">' . $singular . '</label>';
					$html  .= '</div>';
					$html  .= '<div class="nextpage-option--shortcode' . $active . '">';
					$html  .= '<label for="shortcode-' . $name . '">' . __( 'Enter the global shortcode for this post type.', 'ajax-load-more-nextpage' ) . '</label>';
					$html  .= '<textarea id="shortcode-' . $name . '" name="alm_settings[' . $opt . '][shortcodes][' . $name . ']" rows="4">' . $shortcode_val . '</textarea>';
					$html  .= '</div>';
					$html  .= '</div>';
				}
			}
			?>
			<script>
				window.addEventListener('load', function(){
					var nextpageElements = document.querySelectorAll('.nextpage-option input[type="checkbox"]');
					if(nextpageElements){
						for(var i = 0; i < nextpageElements.length - 1; i++){
							nextpageElements[i].addEventListener('change', function(e){
								var el = e.target;
								var parent = el.parentNode.parentNode;
								var shortcode = parent.querySelector('.nextpage-option--shortcode');
								if(el.checked){
									shortcode.classList.add('active');
								} else {
									shortcode.classList.remove('active');
								}
							});
						}
					}
					});
			</script>
			<?php
		}
		echo $html; // phpcs:ignore
	}

	/**
	 * Add post slugs to load.
	 *
	 * @author ConnektMedia
	 * @since 1.6.0
	 */
	function alm_nextpage_slugs_callback() {
		$options = get_option( 'alm_settings' );
		$opt     = '_alm_nextpage_slugs';

		$html  = '<label for="' . $opt . '">';
		$html .= __( 'Activate Next Page functionality on the following slugs: <span style="display: block;">Add one slug per line below.</span>', 'ajax-load-more-nextpage' );
		$html .= '</label>';

		$html .= '<textarea id="' . $opt . '" name="alm_settings[' . $opt . ']" placeholder="my-post-slug" rows="5">';
		$html .= isset( $options[ $opt ] ) ? $options[ $opt ] : '';
		$html .= '</textarea>';

		echo $html; // phpcs:ignore
	}

	/**
	 * Custom CSS for Nextpage.
	 *
	 * @author ConnektMedia
	 * @since 1.6.0
	 */
	function alm_nextpage_css_callback() {
		$options = get_option( 'alm_settings' );

		$html  = '<label for="_alm_nextpage_css">';
		$html .= __( 'Enter Custom Next Page CSS <span style="display: block;">Use this option to hide the existing pagination or inject additional CSS into the post type template.</span>', 'ajax-load-more-nextpage' );
		$html .= '</label>';

		$html .= '<textarea id="_alm_nextpage_css" name="alm_settings[_alm_nextpage_css]" placeholder=".post-navigation { display: none; }" rows="5">';
		$html .= isset( $options['_alm_nextpage_css'] ) ? $options['_alm_nextpage_css'] : '';
		$html .= '</textarea>';

		echo $html; // phpcs:ignore
	}

	/**
	 * Sanitize license activation.
	 *
	 * @param string $new The new license key.
	 * @author ConnektMedia
	 * @since 1.0
	 */
	function alm_nextpage_sanitize_license( $new ) {
		$old = get_option( 'alm_nextpage_license_key' );
		if ( $old && $old !== $new ) {
			delete_option( 'alm_nextpage_license_status' );
		}
		return $new;
	}

	/**
	 * The main function responsible for returning Ajax Load More Next Page.
	 *
	 * @author ConnektMedia
	 * @since 1.0
	 */
	function alm_nextpage_plugin() {
		global $alm_nextpage_plugin;
		if ( ! isset( $alm_nextpage_plugin ) ) {
			$alm_nextpage_plugin = new ALM_Nextpage_Plugin();
		}
		return $alm_nextpage_plugin;
	}
	alm_nextpage_plugin();

endif;

/**
 * Software Licensing
 *
 * @author ConnektMedia
 * @since 1.0
 */
function alm_nextpage_plugin_updater() {
	if ( ! has_action( 'alm_pro_installed' ) && class_exists( 'EDD_SL_Plugin_Updater' ) ) {
		$license_key = trim( get_option( 'alm_nextpage_license_key' ) );
		$edd_updater = new EDD_SL_Plugin_Updater(
			ALM_STORE_URL,
			__FILE__,
			array(
				'version' => ALM_NEXTPAGE_VERSION,
				'license' => $license_key,
				'item_id' => ALM_NEXTPAGE_ITEM_NAME,
				'author'  => 'Darren Cooney',
			)
		);
	}
}
add_action( 'admin_init', 'alm_nextpage_plugin_updater', 0 );
