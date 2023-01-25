<?php
/**
 * Plugin Name: Ajax Load More: Cache
 * Plugin URI: http://connekthq.com/plugins/ajax-load-more/cache/
 * Description: Ajax Load More extension that creates static HTML files from ajax requests.
 * Author: Darren Cooney
 * Twitter: @KaptonKaos
 * Author URI: http://connekthq.com
 * Version: 1.7.6
 * License: GPL
 * Copyright: Darren Cooney & Connekt Media
 *
 * @package ALMCache
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ALM_CACHE_VERSION', '1.7.6' );
define( 'ALM_CACHE_RELEASE', 'January 10, 2023' );

/**
 * Install the add-on.
 *
 * @since 1.0
 */
function alm_cache_install() {
	if ( ! is_plugin_active( 'ajax-load-more/ajax-load-more.php' ) ) {
		set_transient( 'alm_cache_admin_notice', true, 5 );
	} else {
		$upload_dir = wp_upload_dir();
		$dir        = $upload_dir['basedir'] . '/alm-cache';
		// Create alm-cache directory if does not exist.
		if ( ! is_dir( $dir ) ) {
			wp_mkdir_p( $dir );
		}
		// Test directory access.
		if ( ! is_writable( $dir ) ) {
			die( esc_html__( 'Error accessing uploads/alm-cache directory. This add-on is required to read/write to your server. Please contact your hosting administrator.', 'ajax-load-more-cache' ) );
		}
	}
}
register_activation_hook( __FILE__, 'alm_cache_install' );

/**
 * Display admin notice if plugin does not meet the requirements.
 *
 * @since 2.5.6
 */
function alm_cache_admin_notice() {
	$slug   = 'ajax-load-more';
	$plugin = $slug . '-cache';
	// Ajax Load More Notice.
	if ( get_transient( 'alm_cache_admin_notice' ) ) {
		$install_url = get_admin_url() . '/update.php?action=install-plugin&plugin=' . $slug . '&_wpnonce=' . wp_create_nonce( 'install-plugin_' . $slug );
		$message     = '<div class="error">';
		$message    .= '<p>' . __( 'You must install and activate the core Ajax Load More plugin before using the Ajax Load More Cache Add-on.', 'ajax-load-more-cache' ) . '</p>';
		$message    .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, __( 'Install Ajax Load More Now', 'ajax-load-more-cache' ) ) . '</p>';
		$message    .= '</div>';
		echo wp_kses_post( $message );
		delete_transient( 'alm_cache_admin_notice' );
	}
}
add_action( 'admin_notices', 'alm_cache_admin_notice' );

if ( ! class_exists( ' ALMCache' ) ) :

	/**
	 * Initiate the class.
	 */
	class ALMCache {
		/**
		 * Constructor function.
		 */
		public function __construct() {
			$this->init();

			add_action( 'alm_cache_installed', array( &$this, 'alm_cache_installed' ) );
			add_action( 'alm_clear_cache', array( &$this, 'alm_clear_cache' ), 10, 2 );
			add_filter( 'alm_cache_inc', array( &$this, 'alm_cache_inc' ), 10, 7 );
			add_filter( 'alm_cache_file', array( &$this, 'alm_cache_file' ), 10, 5 );
			add_filter( 'alm_get_cache_array', array( &$this, 'alm_get_cache_array' ), 10, 2 );
			add_filter( 'alm_previous_post_cache_file', array( &$this, 'alm_previous_post_cache_file' ), 10, 3 );
			add_filter( 'alm_html_cache_file', array( &$this, 'alm_html_cache_file' ), 10, 3 );
			add_filter( 'alm_nextpage_cache_file', array( &$this, 'alm_nextpage_cache_file' ), 10, 3 );
			add_filter( 'alm_cache_create_dir', array( &$this, 'alm_cache_create_dir' ), 10, 3 );
			add_filter( 'alm_cache_create_nested_id', array( &$this, 'alm_cache_create_nested_id' ), 10, 2 );
			add_action( 'wp_ajax_alm_delete_cache', array( &$this, 'alm_delete_cache' ) );

			add_action( 'init', array( &$this, 'alm_cache_create_publish_actions' ) );
			add_action( 'admin_bar_menu', array( &$this, 'alm_add_toolbar_items' ), 100 );
			add_action( 'alm_cache_settings', array( &$this, 'alm_cache_settings' ) );
			add_filter( 'alm_cache_shortcode', array( &$this, 'alm_cache_shortcode' ), 10, 3 );
			add_action( 'admin_head', array( &$this, 'alm_cache_vars' ) );
			load_plugin_textdomain( 'ajax-load-more-cache', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
		}

		/**
		 * Include these files in the admin.
		 *
		 * @since 1.4
		 */
		public function init() {
			define( 'ALM_CACHE_ADMIN_PATH', plugin_dir_path( __FILE__ ) ); // Plugin Dir Path
			define( 'ALM_CACHE_ADMIN_URL', plugins_url( '', __FILE__ ) ); // Plugin URL
			require_once 'api/create.php';
		}

		/**
		 * Get array of cache items to prebuild.
		 *
		 * @return array;
		 * @since 1.6
		 */
		public static function alm_get_cache_array() {
			$array  = apply_filters( 'alm_cache_array', '' );
			$return = ( is_array( $array ) ) ? json_encode( $array ) : null;
			return $return;
		}

		/**
		 * Get absolute path to cache directory path
		 *
		 * @return $path;
		 * @since 1.5
		 */
		public static function alm_get_cache_path() {
			$upload_dir = wp_upload_dir();
			$path       = apply_filters( 'alm_cache_path', $upload_dir['basedir'] . '/alm-cache/' );
			return $path;
		}

		/**
		 * Get cache directory URL
		 *
		 * @return $path;
		 * @since 1.5
		 */
		public static function alm_get_cache_url() {
			$upload_dir = wp_upload_dir();
			$path       = apply_filters( 'alm_cache_url', $upload_dir['baseurl'] . '/alm-cache/' );
			return $path;
		}

		/**
		 * Enqueue cache admin js
		 *
		 * @since 1.3.1
		 */
		public static function alm_enqueue_cache_admin_scripts() {
			wp_enqueue_style( 'alm-cache-css', ALM_CACHE_ADMIN_URL . '/admin/css/cache.css' );
			wp_enqueue_script( 'alm-cache-admin', ALM_CACHE_ADMIN_URL . '/admin/js/alm-cache.js', array( 'jquery' ) );
		}

		/**
		 * Get information for `_info.txt` file in each cache.
		 *
		 * @param string $file The file.
		 * @param string $path File path.
		 * @param string $directory Directory path.
		 * @since 1.7.0
		 */
		public static function alm_get_cache_info( $file, $path, $directory ) {
			if ( file_exists( $file ) ) {
				$data = '';
				$url  = file_get_contents( $file );
				if ( $url ) { // Cache Info
					$data .= '<ul class="cache-details">';
					$info  = unserialize( $url );
					$time  = strtotime( $info['created'] );
					$data .= '<li title="' . __( 'Cache URL', 'ajax-load-more-cache' ) . '"><i class="fa fa-globe"></i> <a href="' . $info['url'] . '" target="_blank">' . $info['url'] . '</a></li>';
					$data .= '<li title="' . __( 'Cache Path: ', 'ajax-load-more-cache' ) . $path . '"><i class="fa fa-folder-open"></i> <button type="button" title="' . __( 'Show Full Cache Path', 'ajax-load-more-cache' ) . '" class="cache-full-path-button">.../<span class="offscreen">' . __( 'Show Full Cache Path', 'ajax-load-more-cache' ) . '</span></button><span class="cache-full-path">' . self::alm_get_cache_path() . '</span><span class="end-path">' . $directory . '</span>' . '</li>';
					$data .= '<li title="' . __( 'Date Created', 'ajax-load-more-cache' ) . '"><i class="fa fa-clock-o"></i> ' . date( 'F d, Y @ h:i:s A', $time ) . '</li>';
					$data .= '</ul>';
				}

				return $data;
			}
		}

		/**
		 * Build Cache shortcode params and send back to core ALM.
		 *
		 * @since 1.2
		 */
		public function alm_cache_shortcode( $cache, $cache_id, $options ) {
			$return  = ' data-cache="' . $cache . '"';
			$return .= ' data-cache-id="' . $cache_id . '"';
			$return .= ' data-cache-path="' . self::alm_get_cache_url() . '"';

			// Cache auto generate.
			$auto_generate = isset( $_GET['alm_auto_cache'] ) ? true : false;

			// Check for known users.
			if ( is_user_logged_in() && isset( $options['_alm_cache_known_users'] ) && $options['_alm_cache_known_users'] === '1' && ! $auto_generate ) {
				$return .= ' data-cache-logged-in="true"';
			}
			return $return;
		}

		/**
		 * Create the Cache settings panel.
		 *
		 * @since 1.2
		 */
		public function alm_cache_settings() {
			register_setting(
				'alm_cache_license',
				'alm_cache_license_key',
				'alm_cache_sanitize_license'
			);
			add_settings_section(
				'alm_cache_settings',
				__( 'Cache Settings', 'ajax-load-more-cache' ),
				'alm_cache_settings_callback',
				'ajax-load-more'
			);
			add_settings_field(
				'_alm_cache_publish',
				__( 'Published Posts', 'ajax-load-more-cache' ),
				'alm_cache_publish_callback',
				'ajax-load-more',
				'alm_cache_settings'
			);
			add_settings_field(
				'_alm_cache_known_users',
				__( 'Known Users', 'ajax-load-more-cache' ),
				'alm_cache_known_users_callback',
				'ajax-load-more',
				'alm_cache_settings'
			);
		}

		/**
		 * Create the publish actions for when new posts are added
		 *
		 * @since 1.0
		 */
		public function alm_cache_create_publish_actions() {
			if ( current_user_can( apply_filters( 'alm_custom_user_role', 'edit_theme_options' ) ) ) {
				$pt_args = array( 'public' => true );
				$types   = get_post_types( $pt_args );
				if ( $types ) {
					foreach ( $types as $type ) {
						$typeobj = get_post_type_object( $type );
						$name    = $typeobj->name;
						if ( $name !== 'revision' && $name !== 'attachment' && $name !== 'nav_menu_item' && $name !== 'acf' ) {
							add_action( 'publish_' . $name . '', array( &$this, 'alm_cache_post_published' ) );
						}
					}
				}
				add_action( 'future_to_publish', array( &$this, 'alm_cache_post_published' ) );
			}
		}

		/**
		 * Create the cache directory by id and store data about cache in .txt file
		 *
		 * @since 1.0
		 */
		public function alm_cache_create_dir( $cache_id, $url ) {

			// Test upload directory before creating files
			$path = self::alm_get_cache_path();

			// Create alm-cache directory if not exists
			if ( ! is_dir( $path ) ) {
				wp_mkdir_p( $path );
			}

			$cdir = $path . $cache_id;

			// make the directory and text file to store data
			if ( ! is_dir( $cdir ) ) {

				wp_mkdir_p( $cdir );

				// Create text file
				$txtfile = fopen( $cdir . '/_info.txt', 'w' ) or wp_die( __( 'Unable to create text file!', 'ajax-load-more-cache' ) );

				// Set $data and write to file
				$data = array(
					'url'     => $url,
					'created' => date( 'Y-m-d H:i:s' ),
				);

				fwrite( $txtfile, serialize( $data ) ) or wp_die( __( 'Unable to write to text file. Please contact your hosting administrator.', 'ajax-load-more-cache' ) );
				fclose( $txtfile );

			}
		}

		/**
		 * Create nested cache from ID - used with Filters and WooCommerce.
		 *
		 * @since 1.0
		 */
		public function alm_cache_create_nested_id( $cache_id ) {
			$url   = $_SERVER['HTTP_REFERER']; // Get referring URL
			$parts = parse_url( $url ); // Parse the URL
			$url   = $cache_id . '/';
			parse_str( $parts['query'], $querystring );

			// Remove pg parameters from cache ID
			if ( isset( $querystring['pg'] ) ) {
				unset( $querystring['pg'] );
			}

			// Remove auto parameters from cache ID (Auto Generate Cache param)
			if ( isset( $querystring['auto'] ) ) {
				unset( $querystring['auto'] );
			}

			if ( $querystring ) {
				$i = 0;
				foreach ( $querystring as $key => $value ) {
					if ( $i > 0 ) {
						$url .= '--';
					}
					$url .= $key . '--' . str_replace( ' ', '-', $value );
					$i++;
				}
			}
			return $url;
		}

		/**
		 * Get repeater file and store it for caching.
		 *
		 * @since 1.0
		 */
		public function alm_cache_inc( $repeater, $type, $theme_repeater, $alm_page, $alm_found_posts, $alm_item, $alm_current ) {
			ob_start();
			if ( $theme_repeater != 'null' && has_filter( 'alm_get_theme_repeater' ) ) {
				do_action( 'alm_get_theme_repeater', $theme_repeater, $alm_found_posts, $alm_page, $alm_item, $alm_current );
			} else {
				require_once alm_get_current_repeater( $repeater, $type );
			}
			$return = ob_get_contents();
			ob_end_clean();
			return $return;
		}

		/**
		 * Create the cached file and write it to uploads/alm-cache.
		 *
		 * @param string  $cache_id   The cache ID.
		 * @param string  $page       The current page number.
		 * @param string  $start_page The SEO start page.
		 * @param string  $data       Raw HTML data.
		 * @param boolean $preloaded  Is this preloaded.
		 * @since 1.0
		 * @updated 1.3.0
		 */
		public function alm_cache_file( $cache_id, $page, $start_page, $data, $preloaded ) {
			$path      = self::alm_get_cache_path();
			$dir       = $path . $cache_id;
			$firstpage = '1';

			if ( $start_page > 1 && $page < $start_page ) {
				$cached_file = fopen( $dir . '/page-' . '' . $firstpage . '-' . $start_page . '.html', 'w' ) or wp_die( esc_attr( __( 'Error opening file - please contact your hosting administrator.', 'ajax-load-more-cache' ) ) );
			} else {
				$cached_file = fopen( $dir . '/page-' . ( $page + 1 ) . '.html', 'w' ) or wp_die( esc_attr( __( 'Error opening file - please contact your hosting administrator.', 'ajax-load-more-cache' ) ) );
			}

			fwrite( $cached_file, $data ) or wp_die( esc_attr( __( 'Error writing to cache file. Please contact your hosting administrator.', 'ajax-load-more-cache' ) ) );

			/**
			 * ALM Cache Hook.
			 * Dispatched after cache has been created.
			 *
			 * @since 1.6
			 */
			do_action( 'alm_cache_created' );

		}

		/**
		 * Create the cached file for Previous Post Add-on and write it to cache dir.
		 *
		 * @param string $cache_id The cache ID.
		 * @param string $slug     URL slug.
		 * @param string $data     Raw HTML data.
		 * @since 1.5.0
		 */
		public function alm_previous_post_cache_file( $cache_id, $slug, $data ) {
			$path        = self::alm_get_cache_path();
			$dir         = $path . $cache_id;
			$cached_file = fopen( $dir . '/' . $slug . '.html', 'w' ) or wp_die( __( 'Error opening file - please contact your hosting administrator.', 'ajax-load-more-cache' ) );

			fwrite( $cached_file, $data ) or wp_die( __( 'Error writing to cache file. Please contact your hosting administrator.', 'ajax-load-more-cache' ) );

			/**
			 * ALM Cache Hook.
			 * Dispatched after cache has been created.
			 *
			 * @since 1.6
			 */
			do_action( 'alm_cache_created' );
		}

		/**
		 * Create cache file from HTML
		 *
		 * @param string $cache_id The cache ID.
		 * @param string $slug     URL slug.
		 * @param string $data     Raw HTML data.
		 * @since 1.6.1
		 */
		public function alm_html_cache_file( $cache_id, $slug, $data ) {
			$path        = self::alm_get_cache_path();
			$dir         = $path . $cache_id;
			$cached_file = fopen( $dir . '/' . $slug . '.html', 'w' ) or wp_die( __( 'Error opening file - please contact your hosting administrator.', 'ajax-load-more-cache' ) );

			fwrite( $cached_file, $data ) or wp_die( __( 'Error writing to cache file. Please contact your hosting administrator.', 'ajax-load-more-cache' ) );

			/**
			 * ALM Cache Hook.
			 * Dispatched after cache has been created.
			 *
			 * @since 1.6
			 */
			do_action( 'alm_cache_created' );
		}

		/**
		 * Create the cached file for Nextpage Add-on and write it to cache dir.
		 *
		 * @param string $cache_id The cache ID.
		 * @param string $page     The current page.
		 * @param string $data     Raw HTML data.
		 * @since 1.4.0
		 */
		public function alm_nextpage_cache_file( $cache_id, $page, $data ) {
			$path        = self::alm_get_cache_path();
			$dir         = $path . $cache_id;
			$cached_file = fopen( $dir . '/page-' . '' . $page . '.html', 'w' ) or wp_die( __( 'Error opening file - please contact your hosting administrator.', 'ajax-load-more-cache' ) );

			fwrite( $cached_file, $data ) or wp_die( __( 'Error writing to cache file. Please contact your hosting administrator.', 'ajax-load-more-cache' ) );

			/*
			 * ALM Cache Hook
			 * Dispatched after cache has been created
			 *
			 * Since 1.6
			 */
			do_action( 'alm_cache_created' );
		}

		/**
		 * Call this function when posts are published to determine if we should flush the cache
		 *
		 * @since 1.0
		 */
		public function alm_cache_post_published() {
			$options = get_option( 'alm_settings' ); // Get plugin options
			if ( $options['_alm_cache_publish'] === '1' ) {
				$path = self::alm_delete_full_cache();
			}
		}

		/**
		 * An empty function to determine if cache is activated.
		 *
		 * @since 1.0
		 */
		public function alm_cache_installed() {
			// Empty return
		}

		/**
		 * Delete cache action.
		 *
		 * @param string $id $id The cache ID to be removed.
		 * @since 1.6
		 */
		public function alm_clear_cache( $id ) {
			if ( current_user_can( apply_filters( 'alm_custom_user_role', 'edit_theme_options' ) ) ) {
				if ( ! empty( $id ) ) {
					self::alm_delete_cache_by_id( $id );
				} else {
					self::alm_delete_full_cache();
				}
			}
		}

		/**
		 * Delete an individual cache directory by ID.
		 *
		 * @param string $id The cache ID to be removed.
		 */
		public static function alm_delete_cache_by_id( $id ) {

			if ( current_user_can( apply_filters( 'alm_custom_user_role', 'edit_theme_options' ) ) ) {
				$path = self::alm_get_cache_path();
				// Confirm directory exists.
				if ( ! is_dir( $path ) ) {
					return;
				}

				// Confirm directory path exists.
				$path = self::alm_get_cache_path();
				if ( ! is_dir( $path ) || empty( $id ) ) {
					wp_die( esc_attr( __( 'Error - Cache directory does not exist.', 'ajax-load-more-cache' ) ) );
				}
				// Cache full path.
				$dir = $path . $id;

				if ( is_dir( $dir ) ) {
					self::alm_cache_rmdir( $dir );

					/**
					 * ALM Cache Hook.
					 * Dispatched after cache has been deleted.
					 *
					 * Since 1.6
					 */
					do_action( 'alm_cache_deleted' );
				}
			}
		}

		/**
		 * Delete individual cached items.
		 *
		 * @since 1.0
		 */
		public function alm_delete_cache() {

			if ( current_user_can( apply_filters( 'alm_custom_user_role', 'edit_theme_options' ) ) ) {

				$nonce = $_POST['nonce'];
				$id    = sanitize_text_field( $_POST['cache'] );

				// Check the nonce, don't match then bounce!
				if ( ! wp_verify_nonce( $nonce, 'alm_cache_nonce' ) ) {
					die( __( 'Error - Unable to verify nonce.', 'ajax-load-more-cache' ) );
				}

				$path = self::alm_get_cache_path();

				// Confirm directory exists.
				if ( ! is_dir( $path ) || ! $id ) {
					return;
				}

				self::alm_delete_cache_by_id( $id );
			}
			wp_die();
		}

		/**
		 * Delete entire ALM cache.
		 *
		 * @return string
		 * @since 1.6
		 */
		public static function alm_delete_full_cache() {

			if ( current_user_can( apply_filters( 'alm_custom_user_role', 'edit_theme_options' ) ) ) {

				$path = self::alm_get_cache_path();

				if ( ! is_dir( $path ) ) {
					return;
				}

				foreach ( new DirectoryIterator( $path ) as $directory ) {
					if ( $directory->isDot() ) {
						continue;
					}

					if ( $directory->isDir() ) {
						$dir = $path . $directory;
						self::alm_cache_rmdir( $dir );
					}
				}

				// Hook dispatched after cache has been deleted
				do_action( 'alm_cache_deleted' );
				return __( 'Cache deleted successfully', 'ajax-load-more-cache' );

			}

			wp_die();
		}

		/**
		 *  Recurrsively delete cache directory and files.
		 *
		 *  @param {String} $dir
		 *  @return null
		 *  @since 1.6
		 */
		public static function alm_cache_rmdir( $dir ) {

			if ( current_user_can( apply_filters( 'alm_custom_user_role', 'edit_theme_options' ) ) ) {

				$cache_path = self::alm_get_cache_path();

				// Confirm is directory & directory is found in the `alm_get_cache_path`
				if ( ! is_dir( $dir ) || strpos( $dir, $cache_path ) === false ) {
					return;
				}

				// Recurrsively remove nested directories.
				if ( is_dir( $dir ) ) {
					$objects = scandir( $dir );
					foreach ( $objects as $object ) {
						if ( $object != '.' && $object != '..' ) {
							if ( filetype( $dir . '/' . $object ) == 'dir' ) {
								self::alm_cache_rmdir( $dir . '/' . $object );
							} else {
								unlink( $dir . '/' . $object );
							}
						}
					}
					reset( $objects );
					rmdir( $dir );
				}
			}
		}

		/**
		 * Create admin variables for cache add-on.
		 *
		 * @since 1.2.2
		 */
		public function alm_cache_vars() { ?>
		  <script type='text/javascript'>
		   /* <![CDATA[ */
		  var alm_cache_localize =
			<?php
			echo json_encode(
				array(
					'ajax_admin_url'    => admin_url( 'admin-ajax.php' ),
					'alm_cache_nonce'   => wp_create_nonce( 'alm_cache_nonce' ),
					'are_you_sure'      => __( 'Are you sure you want to delete the following Ajax Load More Cache and all of it\'s contents?', 'ajax-load-more-cache' ),
					'are_you_sure_full' => __( 'Are you sure you want to delete the entire Ajax Load More Cache?', 'ajax-load-more-cache' ),
				)
			);
			?>
		  /* ]]> */
		  </script>
			<?php
		}

		/**
		 * Create admin bar menu.
		 *
		 * @since 1.0
		 */
		public function alm_add_toolbar_items( $admin_bar ) {
			if ( ! is_super_admin() || ! is_admin_bar_showing() ) {
				return;
			}

			$admin_bar->add_menu(
				array(
					'id'    => 'alm-cache',
					'title' => 'ALM - Cache',
					'href'  => admin_url( 'admin.php?page=ajax-load-more-cache' ),
					'meta'  => array(
						'title' => __( 'Ajax Load More Cache', 'ajax-load-more-cache' ),
					),
				)
			);
			$admin_bar->add_menu(
				array(
					'id'     => 'alm-cache-delete',
					'parent' => 'alm-cache',
					'title'  => __( 'Delete Cache', 'ajax-load-more-cache' ),
					'href'   => admin_url( 'admin.php?page=ajax-load-more-cache&action=delete' ),
					'meta'   => array(
						'title'  => __( 'Delete Cache', 'ajax-load-more-cache' ),
						'target' => '_self',
					),
				)
			);
			$generate_cache = ALMCACHE::alm_get_cache_array();
			if ( $generate_cache ) {
				$admin_bar->add_menu(
					array(
						'id'     => 'alm-cache-build',
						'parent' => 'alm-cache',
						'title'  => __( 'Auto-Generate Cache', 'ajax-load-more-cache' ),
						'href'   => admin_url( 'admin.php?page=ajax-load-more-cache&action=build' ),
						'meta'   => array(
							'title'  => __( 'Generate Cache', 'ajax-load-more-cache' ),
							'target' => '_self',
						),
					)
				);
			}
		}
	}

	/**
	 * Cache Setting Heading.
	 *
	 * @since 2.6.0
	 */
	function alm_cache_settings_callback() {
		$html = '<p>' . __( 'Customize your installation of the <a href="http://connekthq.com/plugins/ajax-load-more/cache/">Cache</a> add-on.', 'ajax-load-more-cache' ) . '</p>';
		echo $html; //phpcs:ignore
	}

	/**
	 * Clear cache when a new post is published.
	 *
	 * @since 2.6.0
	 */
	function alm_cache_publish_callback() {
		$options = get_option( 'alm_settings' );
		if ( ! isset( $options['_alm_cache_publish'] ) ) {
			$options['_alm_cache_publish'] = '0';
		}
		$html  = '<input type="hidden" name="alm_settings[_alm_cache_publish]" value="0" /><input type="checkbox" id="alm_cache_publish" name="alm_settings[_alm_cache_publish]" value="1"' . ( ( $options['_alm_cache_publish'] ) ? ' checked="checked"' : '' ) . ' />';
		$html .= '<label for="alm_cache_publish">' . __( 'Delete cache when new posts are published.', 'ajax-load-more-cache' );
		$html .= '<span style="display:block">' . __( 'Cache will be fully cleared whenever a post, page or Custom Post Type is published or updated.', 'ajax-load-more-cache' ) . '</span>';
		$html .= ' </label>';
		echo $html; //phpcs:ignore
	}

	/**
	 * Don't cache files for known users.
	 *
	 * @since 2.6.0
	 */
	function alm_cache_known_users_callback() {
		$options = get_option( 'alm_settings' );
		if ( ! isset( $options['_alm_cache_known_users'] ) ) {
			$options['_alm_cache_known_users'] = '0';
		}
		$html  = '<input type="hidden" name="alm_settings[_alm_cache_known_users]" value="0" /><input type="checkbox" id="alm_cache_known_users" name="alm_settings[_alm_cache_known_users]" value="1"' . ( ( $options['_alm_cache_known_users'] ) ? ' checked="checked"' : '' ) . ' />';
		$html .= '<label for="alm_cache_known_users">' . __( 'Don\'t cache files for logged in users.', 'ajax-load-more-cache' );
		$html .= '<span style="display:block">' . __( 'Logged in users will retrieve content directly from the database and will not view any cached content.', 'ajax-load-more-cache' ) . '</span>';
		$html .= ' </label>';
		echo $html; //phpcs:ignore
	}

	/**
	 * Sanitize our license activation.
	 *
	 * @param string $new The API Key.
	 * @return string The API key as a string.
	 * @since 1.3.0
	 */
	function alm_cache_sanitize_license( $new ) {
		$old = get_option( 'alm_cache_license_key' );
		if ( $old && $old !== $new ) {
			delete_option( 'alm_cache_license_status' );
		}
		return $new;
	}

	/**
	 * The main function responsible for returning Ajax Load More Cache.
	 *
	 * @since 1.0
	 */
	function alm_cache() {
		global $alm_cache;
		if ( ! isset( $alm_cache ) ) {
			$alm_cache = new ALMCache();
		}
		return $alm_cache;
	}
	alm_cache();

endif;

/**
 * Software Licensing
 */
function alm_cache_plugin_updater() {
	if ( ! has_action( 'alm_pro_installed' ) && class_exists( 'EDD_SL_Plugin_Updater' ) ) { // Don't check for updates if Pro is activated.
		$license_key = trim( get_option( 'alm_cache_license_key' ) );
		$edd_updater = new EDD_SL_Plugin_Updater(
			ALM_STORE_URL,
			__FILE__,
			array(
				'version' => ALM_CACHE_VERSION,
				'license' => $license_key,
				'item_id' => ALM_CACHE_ITEM_NAME,
				'author'  => 'Darren Cooney',
			)
		);
	}
}
add_action( 'admin_init', 'alm_cache_plugin_updater', 0 );
