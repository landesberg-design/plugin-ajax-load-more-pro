<?php
/**
 * Plugin Name: Ajax Load More: Cache
 * Plugin URI: http://connekthq.com/plugins/ajax-load-more/cache/
 * Description: Ajax Load More extension that creates static HTML files from ajax requests.
 * Author: Darren Cooney
 * Twitter: @KaptonKaos
 * Author URI: http://connekthq.com
 * Version: 2.0.3
 * License: GPL
 * Copyright: Darren Cooney & Connekt Media
 *
 * @package ALMCache
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ALM_CACHE_VERSION', '2.0.3' );
define( 'ALM_CACHE_RELEASE', 'March 21, 2024' );

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
			register_activation_hook( __FILE__, [ &$this, 'alm_cache_activation' ] );
			add_action( 'alm_cache_installed', [ &$this, 'alm_cache_installed' ] );
			add_action( 'admin_init', [ &$this, 'alm_cache_2_0_upgrader' ] );
			add_action( 'alm_clear_cache', [ &$this, 'alm_clear_cache' ], 10, 2 );
			add_filter( 'alm_get_cache_array', [ &$this, 'alm_get_cache_array' ], 10, 2 );
			add_filter( 'alm_cache_create_directory', [ &$this, 'alm_cache_create_directory' ], 10, 3 );
			add_action( 'wp_ajax_alm_delete_cache', [ &$this, 'alm_delete_cache' ] );
			add_action( 'admin_notices', [ &$this, 'alm_cache_admin_notices' ] );

			add_action( 'init', [ &$this, 'alm_cache_create_publish_actions' ] );
			add_action( 'admin_bar_menu', [ &$this, 'alm_add_toolbar_items' ], 100 );
			add_action( 'alm_cache_settings', [ &$this, 'alm_cache_settings' ] );
			add_filter( 'alm_cache_shortcode', [ &$this, 'alm_cache_shortcode' ], 10, 3 );
			load_plugin_textdomain( 'ajax-load-more-cache', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
		}

		/**
		 * Cache admin notices.
		 */
		public function alm_cache_admin_notices() {
			$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
			if ( $screen && $screen->id === 'ajax-load-more_page_ajax-load-more-cache' ) {
				$params = filter_input_array( INPUT_GET );

				// Cache deleted.
				if ( array_key_exists( 'action', $params ) && $params['action'] === 'alm-cache-deleted' ) {
					?>
					<div class="notice notice-success is-dismissible">
						<p><?php esc_attr_e( 'Ajax Load More Cache has been deleted successfully.', 'ajax-load-more-cache' ); ?></p>
					</div>
					<?php
				}
			}
		}

		/**
		 * Install the add-on.
		 *
		 * @since 1.0
		 */
		public function alm_cache_activation() {
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
				if ( ! is_writable( $dir ) ) { // phpcs:ignore
					wp_die( esc_html__( 'Error accessing uploads/alm-cache directory. This add-on is required to read/write to your server. Please contact your hosting administrator.', 'ajax-load-more-cache' ) );
				}
			}
		}

		/**
		 * Include these files in the admin.
		 *
		 * @since 1.4
		 */
		public function init() {
			define( 'ALM_CACHE_ADMIN_PATH', plugin_dir_path( __FILE__ ) );
			define( 'ALM_CACHE_ADMIN_URL', plugins_url( '', __FILE__ ) );
			require_once 'api/create.php';
			require_once 'api/get.php';
			require_once 'api/test.php';
		}

		/**
		 * V2 Upgrade routine to delete the existing ALM Cache.
		 *
		 * @return void
		 */
		public function alm_cache_2_0_upgrader() {
			$v2_upgrade = get_option( 'alm_cache_v2_upgrade' );
			if ( ! $v2_upgrade ) {
				self::alm_delete_full_cache();
				update_option( 'alm_cache_v2_upgrade', true );
			}
		}

		/**
		 * Get array of cache items to prebuild.
		 *
		 * @return array
		 * @since 1.6
		 */
		public static function alm_get_cache_array() {
			$array  = apply_filters( 'alm_cache_array', '' );
			$return = is_array( $array ) ? wp_json_encode( $array ) : null;
			return $return;
		}

		/**
		 * Get absolute path to cache directory path
		 *
		 * @return string
		 * @since 1.5
		 */
		public static function alm_get_cache_path() {
			$upload_dir = wp_upload_dir();
			return apply_filters( 'alm_cache_path', $upload_dir['basedir'] . '/alm-cache/' );
		}

		/**
		 * Get cache directory URL
		 *
		 * @return string
		 * @since 1.5
		 */
		public static function alm_get_cache_url() {
			$upload_dir = wp_upload_dir();
			return apply_filters( 'alm_cache_url', $upload_dir['baseurl'] . '/alm-cache/' );
		}

		/**
		 * Get the rest api URL for a cache item.
		 *
		 * @param string $id The cache ID.
		 * @param string $name The cache name.
		 * @return string
		 * @since 2.5
		 */
		public static function alm_get_cache_rest_url( $id, $name ) {
			if ( ! $id || ! $name ) {
				return;
			}
			$base = esc_url_raw( rest_url() ) . 'ajax-load-more/cache/get?';
			return $base . 'id=' . $id . '&name=' . $name;
		}

		/**
		 * Get information for `_info.txt` file in each cache.
		 *
		 * @param string $file      The file.
		 * @param string $path      File path.
		 * @param string $directory Directory path.
		 * @param string $key       The key to pluck data from.
		 * @return string The file info.
		 * @since 1.7.0
		 */
		public static function alm_cache_get_info( $file = '', $path = null, $directory = null, $key = null ) {
			if ( $file && file_exists( $file ) ) {
				$data = '';

				// Load WP filesystem.
				global $wp_filesystem;
				include_once ABSPATH . 'wp-admin/includes/file.php';
				WP_Filesystem();

				// Get file contents.
				$contents = $wp_filesystem->get_contents( $file );

				if ( $contents ) {
					// Get contents of the info file.
					$info = unserialize( $contents ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize

					if ( $key ) {
						// Pluck data from a specific key.
						$data = $info[ $key ] ? $info[ $key ] : '';
					} else {
						$time         = strtotime( $info['created'] );
						$time_display = date( 'F d, Y @ h:i:s A', $time ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date

						// Build path display.
						$tmp          = explode( '/', $path );
						$tmp_count    = count( $tmp );
						$start        = $tmp_count - 3;
						$path_display = '';
						for ( $i = $start; $i < $tmp_count; $i++ ) {
							$path_display .= $tmp[ $i ] . '/';
							if ( $i === $tmp_count - 1 ) {
								$path_display = rtrim( $path_display, '/' );
							}
						}
						$data .= '<ul class="cache-details">';
						$data .= '<li title="' . __( 'Cache URL', 'ajax-load-more-cache' ) . '">';
						$data .= '<i class="fa fa-globe" aria-hidden="true"></i> <a href="' . urldecode( $info['url'] ) . '" target="_blank">' . urldecode( $info['url'] ) . '</a>';
						$data .= '</li>';
						$data .= '<li title="' . __( 'Date Created:', 'ajax-load-more-cache' ) . ' ' . $time_display . '">';
						$data .= '<i class="fa fa-clock-o" aria-hidden="true"></i> ' . $time_display;
						$data .= '</li>';
						$data .= '<li title="' . __( 'Cache Path:', 'ajax-load-more-cache' ) . ' ' . $path . '">';
						$data .= '<i class="fa fa-folder-open" aria-hidden="true"></i> <button type="button" title="' . __( 'Show Full Cache Path', 'ajax-load-more-cache' ) . '" class="cache-full-path-button">.../<span class="offscreen">' . __( 'Show Full Cache Path', 'ajax-load-more-cache' ) . '</span></button><span class="cache-full-path">' . self::alm_get_cache_path() . '</span><span class="end-path">' . $path_display . '</span>';
						$data .= '</li>';
						$data .= '</ul>';
					}
				}
				return $data;
			}
		}

		/**
		 * Create the cache directory by id and store data about cache in .txt file
		 *
		 * @param string $cache_id The cache id.
		 * @param string $url      The cache url.
		 * @return string          The cache directory path.
		 * @since 1.0
		 */
		public static function alm_cache_create_directory( $cache_id, $url ) {
			$path = self::alm_get_cache_path(); // Test directory before creating files.

			if ( ! is_dir( $path ) ) {
				wp_mkdir_p( $path ); // Create directory if it doesn't exist.
			}

			$cache_path = $path . $cache_id; // Full cache path.

			// Make the directory and text file to store data.
			if ( ! is_dir( $cache_path ) ) {

				// Make the cache directory.
				wp_mkdir_p( $cache_path );

				// Load WP filesystem.
				global $wp_filesystem;
				include_once ABSPATH . 'wp-admin/includes/file.php';
				WP_Filesystem();

				// Write data to file.
				$data = [
					'url'     => $url,
					'created' => date( 'Y-m-d H:i:s' ), // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
				];

				// Create info file.
				$success = $wp_filesystem->put_contents( $cache_path . '/_info.txt', serialize( $data ), FS_CHMOD_FILE ); // phpcs:ignore

				if ( ! $success ) {
					wp_die( esc_attr__( 'Unable to write to text file. Please contact your hosting administrator.', 'ajax-load-more-cache' ) );
				}
			}

			return $cache_path;
		}

		/**
		 * Retrieve the cache file from filesystem.
		 *
		 * @param string $id     The cache id.
		 * @param string $slug   Cache slug/hash.
		 * @return boolean|array Array containing the cache file contents and totalposts.
		 * @since 2.0
		 */
		public static function get_cache_file( $id, $slug ) {
			$path       = self::alm_get_cache_path() . $id;
			$cache_file = $path . '/' . $slug . '.json';
			$contents   = null;
			$totalposts = 0;

			if ( ! file_exists( $cache_file ) ) {
				return false;
			}

			// Load WP Filesystem.
			global $wp_filesystem;
			include_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();

			// Get cache file contents.
			$contents = $wp_filesystem->get_contents( $cache_file );

			if ( ! $contents ) {
				// Missing content.
				return false;
			}

			// Return cache data.
			return $contents;
		}

		/**
		 * Create a cached file and write it to cache directory.
		 *
		 * @param string     $id     The cache id.
		 * @param string     $slug   Cache slug/hash.
		 * @param string     $url    Cache URL.
		 * @param string     $data   Raw HTML data.
		 * @param string|int $count  Post count in the cache html file.
		 * @param string|int $total  The total posts in the entire query.
		 * @param array      $facets Array of active facets from the filters add-on.
		 * @return void
		 * @since 2.0
		 */
		public static function create_cache_file( $id, $slug, $url, $data, $count = 0, $total = 0, $facets = [] ) {
			if ( ! $id || ! $data ) {
				return;
			}

			// Create the cache directory.
			$path = apply_filters( 'alm_cache_create_directory', $id, $url );
			if ( ! $path ) {
				return;
			}

			// Load WP Filesystem.
			global $wp_filesystem;
			include_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();

			$json = [
				'html' => $data,
				'meta' => [
					'postcount'  => $count,
					'totalposts' => $total,
				],
			];

			if ( $facets ) {
				$json['facets'] = $facets; // Append facets to the cache file.
			}

			// Create the cache file.
			$success = $wp_filesystem->put_contents( $path . '/' . $slug . '.json', wp_json_encode( $json ), FS_CHMOD_FILE );

			if ( ! $success ) {
				// Error handling.
				wp_die( esc_attr__( 'Unable to create cache file. Please contact your hosting administrator.', 'ajax-load-more-cache' ) );
			}

			/**
			 * ALM Cache Hook.
			 * Dispatched after cache has been created.
			 *
			 * @since 1.6
			 */
			do_action( 'alm_cache_created' );
		}

		/**
		 * Call this function when posts are published to determine if we should flush the cache
		 *
		 * @since 1.0
		 */
		public function alm_cache_post_published() {
			$options = get_option( 'alm_settings' ); // Get plugin options.
			if ( isset( $options['_alm_cache_publish'] ) && $options['_alm_cache_publish'] === '1' ) {
				$path = self::alm_delete_full_cache();
			}
		}

		/**
		 * An empty function to determine if cache is activated.
		 *
		 * @since 1.0
		 */
		public function alm_cache_installed() {
			// phpcs:ignore
			// Empty return.
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
			$params = filter_input_array( INPUT_POST );
			if ( current_user_can( apply_filters( 'alm_custom_user_role', 'edit_theme_options' ) ) ) {
				$nonce = $params['nonce'];
				$id    = sanitize_text_field( $params['cache'] );

				// Check the nonce, don't match then bounce!
				if ( ! wp_verify_nonce( $nonce, 'alm_cache_nonce' ) ) {
					die( esc_attr__( 'Error - Unable to verify nonce.', 'ajax-load-more-cache' ) );
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
		 * @param boolean $is_cron Is this a cron job.
		 * @return string
		 * @since 1.6
		 */
		public static function alm_delete_full_cache( $is_cron = false ) {
			if ( $is_cron || ! $is_cron && current_user_can( apply_filters( 'alm_custom_user_role', 'edit_theme_options' ) ) ) {
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

				// Hook dispatched after cache has been deleted.
				do_action( 'alm_cache_deleted' );
				return __( 'Cache deleted successfully', 'ajax-load-more-cache' );
			}

			wp_die();
		}

		/**
		 *  Recurrsively delete cache directory and files.
		 *
		 *  @param string $dir The directory.
		 *  @return null
		 *  @since 1.6
		 */
		public static function alm_cache_rmdir( $dir ) {
			if ( current_user_can( apply_filters( 'alm_custom_user_role', 'edit_theme_options' ) ) ) {
				$cache_path = self::alm_get_cache_path();

				// Confirm is directory & directory is found in the `alm_get_cache_path`.
				if ( ! is_dir( $dir ) || strpos( $dir, $cache_path ) === false ) {
					return;
				}

				// Recurrsively remove nested directories.
				if ( is_dir( $dir ) ) {
					$objects = scandir( $dir );
					foreach ( $objects as $object ) {
						if ( $object !== '.' && $object !== '..' ) {
							if ( filetype( $dir . '/' . $object ) === 'dir' ) {
								self::alm_cache_rmdir( $dir . '/' . $object );
							} else {
								unlink( $dir . '/' . $object );  // phpcs:ignore
							}
						}
					}
					reset( $objects );
					rmdir( $dir ); // phpcs:ignore
				}
			}
		}

		/**
		 * Enqueue Cache admin js and css.
		 *
		 * @since 1.3.1
		 */
		public static function alm_enqueue_cache_admin_scripts() {
			wp_enqueue_style( 'alm-cache-css', ALM_CACHE_ADMIN_URL . '/build/index.css', '', ALM_CACHE_VERSION );
			wp_enqueue_script( 'alm-cache-admin', ALM_CACHE_ADMIN_URL . '/build/index.js', [ 'jquery' ], ALM_CACHE_VERSION, false );

			// Localized JS variables.
			wp_localize_script(
				'alm-cache-admin',
				'alm_cache_localize',
				[
					'root'              => esc_url_raw( rest_url() ),
					'nonce'             => wp_create_nonce( 'wp_rest' ),
					'ajax_admin_url'    => admin_url( 'admin-ajax.php' ),
					'alm_cache_nonce'   => wp_create_nonce( 'alm_cache_nonce' ),
					'are_you_sure'      => __( 'Are you sure you want to delete the following Ajax Load More Cache and all of it\'s contents?', 'ajax-load-more-cache' ),
					'are_you_sure_full' => __( 'Are you sure you want to delete the entire Ajax Load More Cache?', 'ajax-load-more-cache' ),
				]
			);
		}

		/**
		 * Build Cache shortcode params and send back to core ALM.
		 *
		 * @param string $cache    The cache.
		 * @param string $cache_id The cache id.
		 * @param array  $options  Plugin options.
		 * @return string          Data params.
		 * @since 1.2
		 */
		public function alm_cache_shortcode( $cache, $cache_id, $options ) {
			$cache_id = str_replace( '%post_id%', $options['post_id'], $cache_id ); // Replace template variable with post id.
			$cache_id = str_replace( '%post_slug%', $options['slug'], $cache_id ); // Replace template variable with post id.

			$data  = ' data-cache="' . $cache . '"';
			$data .= ' data-cache-id="' . $cache_id . '"';
			$data .= ' data-cache-path="' . self::alm_get_cache_url() . '"';

			// Cache auto generate query param.
			$auto_generate = isset( $_GET['alm_auto_cache'] ) ? true : false;

			// Check for known users.
			if ( is_user_logged_in() && isset( $options['_alm_cache_known_users'] ) && $options['_alm_cache_known_users'] === '1' && ! $auto_generate ) {
				$data .= ' data-cache-logged-in="true"';
			}
			return $data;
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
				$pt_args = [ 'public' => true ];
				$types   = get_post_types( $pt_args );
				if ( $types ) {
					foreach ( $types as $type ) {
						$typeobj = get_post_type_object( $type );
						$name    = $typeobj->name;
						if ( $name !== 'revision' && $name !== 'attachment' && $name !== 'nav_menu_item' && $name !== 'acf' ) {
							add_action( 'publish_' . $name . '', [ &$this, 'alm_cache_post_published' ] );
						}
					}
				}
				add_action( 'future_to_publish', [ &$this, 'alm_cache_post_published' ] );
			}
		}

		/**
		 * Create admin bar menu.
		 *
		 * @param object $admin_bar The admin bar object.
		 * @since 1.0
		 */
		public function alm_add_toolbar_items( $admin_bar ) {
			if ( ! is_super_admin() || ! is_admin_bar_showing() ) {
				return;
			}

			$admin_bar->add_menu(
				[
					'id'    => 'alm-cache',
					'title' => 'ALM - Cache',
					'href'  => admin_url( 'admin.php?page=ajax-load-more-cache' ),
					'meta'  => [
						'title' => __( 'Ajax Load More Cache', 'ajax-load-more-cache' ),
					],
				]
			);
			$admin_bar->add_menu(
				[
					'id'     => 'alm-cache-delete',
					'parent' => 'alm-cache',
					'title'  => __( 'Delete Cache', 'ajax-load-more-cache' ),
					'href'   => admin_url( 'admin.php?page=ajax-load-more-cache&action=delete' ),
					'meta'   => [
						'title'  => __( 'Delete Cache', 'ajax-load-more-cache' ),
						'target' => '_self',
					],
				]
			);
			$generate_cache = ALMCACHE::alm_get_cache_array();
			if ( $generate_cache ) {
				$admin_bar->add_menu(
					[
						'id'     => 'alm-cache-build',
						'parent' => 'alm-cache',
						'title'  => __( 'Auto-Generate Cache', 'ajax-load-more-cache' ),
						'href'   => admin_url( 'admin.php?page=ajax-load-more-cache&action=build' ),
						'meta'   => [
							'title'  => __( 'Generate Cache', 'ajax-load-more-cache' ),
							'target' => '_self',
						],
					]
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
		$html .= '<span style="display:block">' . __( 'Ajax Load More Cache will be fully cleared whenever a post, page or Custom Post Type is published or modified.', 'ajax-load-more-cache' ) . '</span>';
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
	 * @param string $key The API Key.
	 * @return string The API key as a string.
	 * @since 1.3.0
	 */
	function alm_cache_sanitize_license( $key ) {
		$old = get_option( 'alm_cache_license_key' );
		if ( $old && $old !== $key ) {
			delete_option( 'alm_cache_license_status' );
		}
		return $key;
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
			[
				'version' => ALM_CACHE_VERSION,
				'license' => $license_key,
				'item_id' => ALM_CACHE_ITEM_NAME,
				'author'  => 'Darren Cooney',
			]
		);
	}
}
add_action( 'admin_init', 'alm_cache_plugin_updater', 0 );
