<?php
/**
 * Plugin Name: Ajax Load More: Next Page
 * Plugin URI: https://connekthq.com/plugins/ajax-load-more/add-ons/next-page/
 * Description: Ajax Load More add-on for displaying multipage WordPress content
 * Author: Darren Cooney
 * Twitter: @KaptonKaos
 * Author URI: https://connekthq.com
 * Version: 1.7.1
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
define( 'ALM_NEXTPAGE_VERSION', '1.7.1' );
define( 'ALM_NEXTPAGE_RELEASE', 'January 16, 2024' );

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
		 * Init flag.
		 *
		 * @var boolean
		 */
		public $init;

		/**
		 * Initial page on page load.
		 *
		 * @var int
		 */
		public $start_page;

		/**
		 * Construct function.
		 */
		public function __construct() {
			$this->init = true;
			add_action( 'alm_nextpage_installed', [ &$this, 'alm_nextpage_installed' ] );
			add_filter( 'alm_init_nextpage', [ &$this, 'alm_nextpage_init' ], 10, 8 );
			add_action( 'wp_ajax_alm_nextpage', [ &$this, 'alm_nextpage_ajax_query' ] );
			add_action( 'wp_ajax_nopriv_alm_nextpage', [ &$this, 'alm_nextpage_ajax_query' ] );
			add_filter( 'alm_nextpage_shortcode', [ &$this, 'alm_nextpage_shortcode' ], 10, 6 );
			add_filter( 'alm_nextpage_noscript_paging', [ &$this, 'alm_nextpage_noscript_paging' ], 10 );
			add_action( 'alm_nextpage_settings', [ &$this, 'alm_nextpage_settings' ] );
			add_filter( 'the_content', [ &$this, 'alm_nextpage_the_content' ], 1 );
			add_action( 'wp_enqueue_scripts', [ &$this, 'alm_nextpage_enqueue_scripts' ] );
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

				if ( ! is_singular( $post_type ) ) {
					return $content;
				}

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
		 * Get the initial server-side load of a nextpage post.
		 *
		 * @author ConnektMedia
		 * @since 1.0
		 * @param string  $post_id  Post ID.
		 * @param string  $page     Current page number.
		 * @param boolean $is_paged Is this a paged URL.
		 * @param boolean $paging   Is this ALM Paging.
		 * @param string  $div_id   Div element ID.
		 * @param string  $id       ALM ID.
		 * @param boolean $nested   Is this a nested ALM instance.
		 * @param string  $type     Next page loading type. (paged/fullpage).
		 * @return string           Paged content as an HTML string.
		 */
		public function alm_nextpage_init( $post_id = null, $page = 0, $is_paged = false, $paging = false, $div_id = '', $id = '', $nested = false, $type = 'paged' ) {
			if ( ! $post_id ) {
				return false; // Exit early if missing post_id.
			}

			$this->start_page = $page;
			$post_content     = get_post( $post_id )->post_content;
			$content          = $this->alm_nextpage_content_to_array( $post_content, $id ); // Get the content.
			$totalposts       = $content ? count( $content ) : 0; // Get total page count.
			$page             = $page - 1; // phpcs:ignore
			$nested           = $nested ? true : false;

			// Add Localized variables.
			$localized_id = ! empty( $id ) ? 'ajax-load-more-' . $id : $div_id;
			ALM_LOCALIZE::add_localized_var( 'page', $page, $localized_id ); // Current page.
			ALM_LOCALIZE::add_localized_var( 'total_posts', $totalposts, $localized_id ); // Total posts.

			// Next Page Full Article.
			if ( $type === 'fullpage' ) {
				return $this->alm_nextpage_article( $post_id, $id, $content, $div_id, $totalposts );
			}

			// Standard Next Page functionality.

			// Not paged, Paging add-on or user disabled loading previous pages.
			if ( ! $is_paged || $paging === 'true' || apply_filters( 'alm_nextpage_paged', false ) ) {
				// Return only a single page.
				return $this->alm_nextpage_render_page( $post_id, $id, $content[ $page ], $page, $totalposts, true, $nested );

			} else {
				// Split pages up into individual content blocks.
				$html = '';
				for ( $i = 0; $i <= $page; $i++ ) {
					// Loop pages and build return.
					$html .= $this->alm_nextpage_render_page( $post_id, $id, $content[ $i ], $i, $totalposts, true, $nested );
				}
				return $html;
			}
		}

		/**
		 * Query nextpage, send results via ajax.
		 *
		 * @author ConnektMedia
		 * @since 1.0
		 * @return JSON
		 */
		public function alm_nextpage_ajax_query() {
			$params = filter_input_array( INPUT_GET, @FILTER_SANITIZE_STRING ); // phpcs:ignore
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
				$nested    = isset( $data['nested'] ) && 'true' === $data['nested'] ? true : false;

				if ( 'totalpages' === $query_type ) {
					// Get totalpages for Paging Add-on.
					wp_send_json(
						[
							'totalpages' => $this->alm_nextpage_get_total_pages( $post_id, $id ),
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
					} elseif ( ! $paging ) {
							$page = $page + 1; //phpcs:ignore

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
						$content = $this->alm_nextpage_content_to_array( $content, $id );

						// Get total page count.
						$totalposts = count( $content );

						if ( isset( $content[ $page ] ) ) {
							// Build page data+.
							$html = $this->alm_nextpage_render_page( $post_id, $id, $content[ $page ], $page, $totalposts, false );

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
		 * Render the complete post for the `article` type.
		 *
		 * @param int    $post_id Current Post ID.
		 * @param string $id      ALM ID.
		 * @param array  $content Post content as an array.
		 * @param string $div_id  ALM div ID.
		 * @param int    $total   Total posts count.
		 * @return string         HTML as a string.
		 */
		public function alm_nextpage_article( $post_id = 0, $id = '', $content = [], $div_id = 'ajax-load-more', $total = 0 ) {
			$html = '';
			if ( $content ) {
				$html .= '<style>.alm-btn-wrap[data-rel="' . $div_id . '"]{display: none !important;}</style>';
				// Loop each page.
				foreach ( $content as $page => $the_content ) {
					$html .= $this->alm_nextpage_render_page( $post_id, $id, $the_content, $page, $total, true );
				}
			}
			return $html;
		}

		/**
		 * Return an individual page of content including wrappers, hooks and content.
		 *
		 * @param string $post_id Current post ID.
		 * @param string $id      ALM ID.
		 * @param string $content Content as a string.
		 * @param int    $page    Current page.
		 * @param int    $total   Total pages.
		 * @param bool   $init    Is this the initial load.
		 * @param bool   $nested  Is this a nested ALM instance.
		 * @return string         Data as raw HTML.
		 */
		public function alm_nextpage_render_page( $post_id, $id, $content, $page, $total, $init = false, $nested = false ) {
			$current = $page + 1;

			// Get the permalink.
			$permalink = $this->alm_nextpage_get_permalink( $post_id, $id, $current, $init, $nested );

			// Start HTML wrapper.
			$html = $this->alm_nextpage_wrap_start( $post_id, $permalink, $current, $total );

			/**
			 * ALM Nextpage Filter Hook.
			 *
			 * @see https://connekthq.com/plugins/ajax-load-more/docs/add-ons/next-page/#alm_nextpage_before
			 * @return string
			 */
			if ( has_filter( 'alm_nextpage_before' ) ) {
				$html .= apply_filters( 'alm_nextpage_before', $current );
			}

			/**
			 * Filter post content .
			 *
			 * @see https://developer.wordpress.org/reference/hooks/the_content/
			 */
			$content = apply_filters( 'the_content', alm_nextpage_remove_block_comments( $content ) ); // phpcs:ignore

			/**
			 * ALM Nextpage Filter Hook.
			 *
			 * @see https://connekthq.com/plugins/ajax-load-more/docs/add-ons/next-page/#alm_nextpage_the_content
			 * @return string
			 */
			if ( has_filter( 'alm_nextpage_the_content' ) ) {
				$content = apply_filters( 'alm_nextpage_the_content', $content, $current );
			}

			$html .= $content;

			/**
			 * ALM Nextpage Filter Hook.
			 *
			 * @see https://connekthq.com/plugins/ajax-load-more/docs/add-ons/next-page/#alm_nextpage_after
			 * @return string
			 */
			if ( has_filter( 'alm_nextpage_after' ) ) {
				$html .= apply_filters( 'alm_nextpage_after', $current );
			}

			// Close HTML Wrapper.
			$html .= $this->alm_nextpage_wrap_end( '</div>' );

			return $html;
		}

		/**
		 * Build the permalink for a next page paged element.
		 *
		 * @param int     $post_id The post ID.
		 * @param string  $id      ALM ID.
		 * @param int     $page    The page number.
		 * @param boolean $init    Is this the initial load.
		 * @param boolean $nested  Is this a nested ALM instance.
		 * @return string          The permalink.
		 */
		public function alm_nextpage_get_permalink( $post_id = 0, $id = '', $page = 1, $init = false, $nested = false ) {
			$base_url = get_permalink( $post_id );

			if ( $nested ) {
				return $base_url; // Return only base_url on nested instances.
			}

			$is_auto_break = has_filter( 'alm_nextpage_break_' . $id ) ? true : false;
			$start_page    = (int) $this->start_page;
			$page          = (int) $page;

			// Get the querystring.
			$querystring = $init ? $_SERVER['QUERY_STRING'] : $this->alm_nextpage_get_querystring();
			$querystring = $is_auto_break ? preg_replace( '/pg=\d+/', '', $querystring ) : $querystring;
			$querystring = ltrim( $querystring, '&' ); // Remove 1st instance of `&`.

			if ( $page > 1 ) {
				if ( $is_auto_break ) {
					$pg_param    = '?pg=' . $page;
					$querystring = $querystring ? $pg_param . '&' . $querystring : $pg_param;
					$permalink   = $base_url . $this->get_leading_slash() . $querystring;
				} else {
					$querystring = $querystring ? '?' . $querystring : '';
					$permalink   = $base_url . $this->get_leading_slash() . $page . $this->get_trailing_slash() . $querystring;
				}
			} else {
				$querystring = $querystring ? '?' . $querystring : '';
				$permalink   = $base_url . $querystring;
			}

			return $permalink;
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
		public function alm_nextpage_get_total_pages( $post_id = null, $id = '' ) {
			$total_pages = 0;
			if ( $post_id ) {
				$post_content = get_post( $post_id )->post_content;
				$content      = $this->alm_nextpage_content_to_array( $post_content, $id );
				$total_pages  = count( $content );
			}
			return $total_pages;
		}

		/**
		 * Return the content as an array
		 *
		 * @author ConnektMedia
		 * @since 1.3
		 * @param string $content The content to split into array.
		 * @param string $id      ALM ID.
		 * @return array          Content as an array.
		 */
		public function alm_nextpage_content_to_array( $content = '', $id = '' ) {
			/**
			 * Inject <!--nextpage--> into content at specific element.
			 *
			 * @see https://connekthq.com/plugins/ajax-load-more/docs/add-ons/next-page/#alm_nextpage_break
			 */
			if ( has_filter( 'alm_nextpage_break_' . $id ) ) {
				$break   = apply_filters( 'alm_nextpage_break_' . $id, '' );
				$content = str_replace( $break, '<!--nextpage-->' . $break, $content );
			}

			// Split $content into array at specific element or <!--nextpage --> quicktag.
			return explode( '<!--nextpage-->', $content );
		}

		/**
		 * The opening html wrapper for nextpage content.
		 *
		 * @author ConnektMedia
		 * @since 1.0
		 * @param string|int $post_id   The post ID.
		 * @param string     $permalink The current URL.
		 * @param string|int $page      The current page number.
		 * @param string|int $total     The total pages.
		 * @return string               Generated HTML as a string.
		 */
		public function alm_nextpage_wrap_start( $post_id = 0, $permalink = '', $page = 0, $total = 0 ) {
			$totalpages = $page === 0 ? ' data-total-posts="' . $total . '"' : '';
			$page_num   = $page === 0 ? '1' : $page;

			return '<div class="alm-nextpage post-' . $post_id . '" data-id="' . $post_id . '" data-title="' . get_the_title( $post_id ) . '" data-url="' . $permalink . '" data-page="' . $page_num . '" data-pages="' . $total . '"' . $totalpages . '>';
		}

		/**
		 * The closing html wrapper element for nextpage content.
		 *
		 * @author ConnektMedia
		 * @since 1.0
		 * @return string
		 */
		public function alm_nextpage_wrap_end() {
			return '</div>';
		}

		/**
		 * Enqueue Next Page scripts.
		 *
		 * @author ConnektMedia
		 * @since 1.0
		 */
		public function alm_nextpage_enqueue_scripts() {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_register_script(
				'ajax-load-more-nextpage',
				plugins_url( '/dist/js/alm-next-page' . $suffix . '.js', __FILE__ ),
				[ 'ajax-load-more' ],
				ALM_NEXTPAGE_VERSION,
				true
			);
			// Localize Nextpage Vars.
			wp_localize_script(
				'ajax-load-more-nextpage',
				'alm_nextpage_localize',
				[
					'leading_slash'  => $this->get_leading_slash(),
					'trailing_slash' => $this->get_trailing_slash(),
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
		public function get_leading_slash() {
			return apply_filters( 'alm_nextpage_leading_slash', false ) ? '/' : '';
		}

		/**
		 * Remove the trailing slash (/) at the end of the URL.
		 *
		 * @author ConnektMedia
		 * @since 1.1
		 * @return string
		 */
		public function get_trailing_slash() {
			return apply_filters( 'alm_nextpage_remove_trailing_slash', false ) ? '' : '/';
		}

		/**
		 * Generate a paging navigation for <noscript/>.
		 *
		 * @author ConnektMedia
		 * @since 1.4
		 * @param string $post_id The current post ID.
		 * @param string $id      The ALM ID.
		 * @return string         Pagination HTML.
		 */
		public function alm_nextpage_noscript_paging( $post_id = '', $id = '' ) {
			if ( empty( $post_id ) ) {
				return false;
			}
			$post_content  = get_post( $post_id )->post_content;
			$pages         = $post_content ? count( $this->alm_nextpage_content_to_array( $post_content, $id ) ) : 0;
			$is_auto_break = has_filter( 'alm_nextpage_break_' . $id ) ? true : false;
			$base_url      = get_permalink( $post_id );
			$paging        = '';

			// Loop pages.
			if ( $pages > 1 ) {
				$paging  = '<noscript>';
				$paging .= '<div class="alm-paging" style="opacity: 1">';
				$paging .= __( 'Pages:', 'ajax-load-more-nextpage' ) . ' ';
				for ( $i = 1; $i <= $pages; $i++ ) {

					// Construct permalink.
					if ( $is_auto_break ) {
						$permalink = $base_url . $this->get_leading_slash() . '?pg=' . $i;
					} else {
						$permalink = $base_url . $this->get_leading_slash() . $i . $this->get_trailing_slash();
					}
					$paging .= '<span class="page" data-page="' . $i . '">';
					$paging .= '<a href="' . $permalink . '">' . $i . '</a>';
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
		public function alm_nextpage_get_querystring() {
			if ( ! apply_filters( 'alm_nextpage_retain_querystring', true ) ) {
				return '';
			}

			$output = '';
			$url    = $_SERVER['HTTP_REFERER']; // Get referring URL.
			if ( $url ) {
				// Parse the full URL.
				$parts = wp_parse_url( $url );
				if ( isset( $parts['query'] ) ) {
					// Parse querystring.
					parse_str( $parts['query'], $querystring );

					if ( $querystring ) {
						$index = 0;
						foreach ( $querystring as $key => $value ) {
							++$index;
							if ( $key === 'pg' ) {
								// Skip if $key is pg.
								continue;
							}
							$output .= $index > 1 ? '&' : '';
							$output .= $key;
							$output .= $value ? '=' . $value : '';
						}
					}
				}
			}
			return $output;
		}

		/**
		 * Build Next Page shortcode params and send back to core ALM.
		 *
		 * @author ConnektMedia
		 * @since 1.0
		 * @param string $urls           Update URL as user scrolls.
		 * @param string $post_id        The current Post ID.
		 * @param string $scroll         Should scroll be enabled.
		 * @param string $title_template Browser title template.
		 * @param string $type           Next page loading type. (paged/article).
		 * @param string $id             ALM ID.
		 * @return string                Data attributes as a string.
		 */
		public function alm_nextpage_shortcode( $urls, $post_id, $scroll, $title_template, $type = 'paged', $id = '' ) {
			$data  = ' data-nextpage="true"';
			$data .= ' data-nextpage-urls="' . $urls . '"';
			$data .= ' data-nextpage-post-id="' . $post_id . '"';
			$data .= ' data-nextpage-scroll="' . $scroll . '"';
			$data .= ' data-nextpage-startpage="' . alm_get_startpage() . '"';
			$data .= has_filter( 'alm_nextpage_break_' . $id ) ? ' data-nextpage-break="true"' : '';

			if ( $type !== 'paged' ) {
				$data .= ' data-nextpage-type="' . $type . '"';
			}
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
			[
				'public'             => true,
				'publicly_queryable' => true,
			]
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
	 * @param string $key The new license key.
	 * @author ConnektMedia
	 * @since 1.0
	 */
	function alm_nextpage_sanitize_license( $key ) {
		$old = get_option( 'alm_nextpage_license_key' );
		if ( $old && $old !== $key ) {
			delete_option( 'alm_nextpage_license_status' );
		}
		return $key;
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
			[
				'version' => ALM_NEXTPAGE_VERSION,
				'license' => $license_key,
				'item_id' => ALM_NEXTPAGE_ITEM_NAME,
				'author'  => 'Darren Cooney',
			]
		);
	}
}
add_action( 'admin_init', 'alm_nextpage_plugin_updater', 0 );
