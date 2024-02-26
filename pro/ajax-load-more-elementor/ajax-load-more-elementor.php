<?php
/**
 * Plugin Name: Ajax Load More: Elementor
 * Plugin URI: https://connekthq.com/plugins/ajax-load-more/add-ons/elementor/
 * Description: Infinite scroll Elementor Posts Widget content with Ajax Load More.
 * Author: Darren Cooney
 * Twitter: @KaptonKaos
 * Author URI: http://connekthq.com
 * Copyright: Darren Cooney & Connekt Media
 * Version: 1.2.0
 * Elementor tested up to: 3.19.1
 * Elementor Pro tested up to: 3.19.1
 *
 * @package ALMElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ALM_ELEMENTOR_VERSION', '1.2.0' );
define( 'ALM_ELEMENTOR_RELEASE', 'February 8, 2024' );

/**
 * Plugin activation hook.
 *
 * @since 1.0
 */
function alm_elementor_install() {
	// if Ajax Load More is not activated.
	if ( ! is_plugin_active( 'ajax-load-more/ajax-load-more.php' ) ) {
		set_transient( 'alm-core-elementor-admin-notice', true, 5 );
	}
	if ( ! alm_is_elementor_activated() ) {
		set_transient( 'alm_elementor_admin_notice', true, 5 );
	}
}
register_activation_hook( __FILE__, 'alm_elementor_install' );

/**
 * Display admin notice if plugin does not meet the requirements
 *
 * @since 1.1
 */
function alm_elementor_admin_notice() {

	$slug   = 'ajax-load-more';
	$plugin = $slug . '-elementor';
	// Ajax Load More Notice.
	if ( get_transient( 'alm_elementor_admin_notice' ) ) {
		$install_url = get_admin_url() . '/update.php?action=install-plugin&plugin=' . $slug . '&_wpnonce=' . wp_create_nonce( 'install-plugin_' . $slug );
		$message     = '<div class="error">';
		$message    .= '<p>' . __( 'You must install and activate the core Ajax Load More plugin before using the Ajax Load More Elementor Add-on.', 'alm-elementor' ) . '</p>';
		$message    .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, __( 'Install Ajax Load More Now', 'alm-elementor' ) ) . '</p>';
		$message    .= '</div>';
		echo wp_kses_post( $message );
		delete_transient( 'alm_elementor_admin_notice' );
	}

	// Elementor Pro Notice.
	if ( get_transient( 'alm-elementor-admin-notice', true, 5 ) ) {
		$message  = '<div class="error">';
		$message .= '<p>' . __( 'Elementor Pro must be installed and activated to use the Ajax Load More Elementor Add-on', 'alm-elementor' ) . '</p>';
		$message .= '</div>';
		echo wp_kses_post( $message );
		delete_transient( 'alm-elementor-admin-notice' );
	}
}
add_action( 'admin_notices', 'alm_elementor_admin_notice' );

/**
 * Is Elementor activated.
 *
 * @since 1.0
 */
function alm_is_elementor_activated() {
	if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
		return true;
	} else {
		return false;
	}
}

if ( ! class_exists( 'ALMElementor' ) ) :

	/**
	 * ALM Elementor Class.
	 *
	 * @since 1.0
	 */
	class ALMElementor {

		/**
		 * Set up plugin.
		 *
		 * @since 1.0
		 */
		public function __construct() {
			define( 'ALM_ELEMENTOR_PATH', plugin_dir_path( __FILE__ ) );
			define( 'ALM_ELEMENTOR_URL', plugins_url( '', __FILE__ ) );
			define( 'ALM_ELEMENTOR_PREFIX', 'alm_elementor_' );

			add_action( 'alm_elementor_installed', [ &$this, 'alm_elementor_installed' ] );
			add_action( 'wp_enqueue_scripts', [ &$this, 'alm_elementor_enqueue_scripts' ] );
			add_filter( 'alm_elementor_params', [ &$this, 'alm_elementor_params' ], 10, 2 );
			add_filter( 'alm_elementor_page_link', [ &$this, 'alm_elementor_page_link' ], 10, 3 );
			add_filter( 'alm_elementor_hide_pagination', [ &$this, 'alm_elementor_hide_pagination' ] );
			add_action( 'alm_elementor_settings', [ &$this, 'alm_elementor_settings' ] );
			add_action( 'wp_loaded', [ $this, 'init_widget' ] );
			load_plugin_textdomain( 'alm-elementor', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
		}


		/**
		 * Init the custom Elementor widget on `wp_loaded`.
		 *
		 * @since 1.0
		 */
		public function init_widget() {
			// Check if Elementor installed and activated.
			if ( alm_is_elementor_activated() ) {
				require_once ALM_ELEMENTOR_PATH . 'module/plugin.php';
			}
		}


		/**
		 * Create link for going back to page 1.
		 *
		 * @param int    $paged The page number.
		 * @param string $label The label.
		 * @return string
		 * @since 1.0
		 */
		public function alm_elementor_page_link( $paged, $label ) {
			if ( $paged > 1 && ! empty( $label ) ) {
				return '<a href=' . get_permalink() . ' class="' . apply_filters( 'alm_elementor_link_class', 'alm-elementor-link' ) . '">' . $label . '</a>';
			}
		}

		/**
		 * Set up initial Elemntor params.
		 *
		 * @param array $params The Elementor parameters.
		 * @return string The settings as raw JSON.
		 * @since 1.0
		 */
		public function alm_elementor_params( $params ) {
			$elementor_params = [
				'target'                     => $params['target'],
				'url'                        => $params['url'],
				'controls'                   => $params['controls'] ? $params['controls'] : 'true',
				'scrolltop'                  => $params['scrolltop'] ? $params['scrolltop'] : '50',
				'paged'                      => $params['paged'],
				'container_class'            => apply_filters( 'alm_elementor_container_class', 'elementor-grid' ),
				'woo_item_class'             => apply_filters( 'alm_elementor_woo_item_class', 'product' ),
				'woo_pagination_class'       => apply_filters( 'alm_elementor_woo_pagination_class', 'woocommerce-pagination' ),
				'posts_item_class'           => apply_filters( 'alm_elementor_posts_item_class', 'elementor-grid-item' ),
				'posts_pagination_class'     => apply_filters( 'alm_elementor_posts_pagination_class', 'elementor-pagination' ),
				'loop_grid_item_class'       => apply_filters( 'alm_elementor_loop_grid_item_class', 'e-loop-item' ),
				'loop_grid_pagination_class' => apply_filters( 'alm_elementor_loop_grid_pagination_class', 'elementor-pagination' ),
				'pagination_item'            => apply_filters( 'alm_elementor_pagination_item', 'a.page-numbers' ),

				// Deprecated.
				'woo_container_class'        => apply_filters( 'alm_elementor_woo_container_class', 'products' ),
				'posts_container_class'      => apply_filters( 'alm_elementor_posts_container_class', 'elementor-posts' ),
				'loop_grid_container_class'  => apply_filters( 'alm_elementor_loop_grid_container_class', 'elementor-grid' ),
			];

			return 'data-elementor-settings="' . htmlspecialchars( wp_json_encode( $elementor_params ), ENT_QUOTES, 'UTF-8' ) . '"';
		}


		/**
		 * Hide the Elementor Post List navigation on ALM pages.
		 *
		 * @since 1.0
		 */
		public function alm_elementor_hide_pagination() {
			$posts_cn = apply_filters( 'alm_elementor_posts_pagination_class', 'elementor-pagination' );
			$woo_cn   = apply_filters( 'alm_elementor_woo_pagination_class', 'woocommerce-pagination' );

			$styles = '.' . $posts_cn . ', .' . $woo_cn . '{display:none;}';

			echo '<style>' . esc_html( $styles ) . '</style>';
		}

		/**
		 * A helper function to determine if add-on is activated.
		 *
		 * @since 1.0
		 * @author Darren Cooney
		 */
		public function alm_elementor_installed() {
			// Empty.
		}

		/**
		 * Enqueue our scripts.
		 *
		 * @since 1.0
		 */
		public function alm_elementor_enqueue_scripts() {
			// Use minified libraries if SCRIPT_DEBUG is turned off.
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			// Enqueue JS.
			wp_register_script(
				'ajax-load-more-elementor',
				plugins_url( '/core/js/alm-elementor' . $suffix . '.js', __FILE__ ),
				[ 'ajax-load-more' ],
				ALM_ELEMENTOR_VERSION,
				true
			);
		}

		/**
		 * Create the settings panel.
		 *
		 * @since 1.0
		 */
		public function alm_elementor_settings() {
			register_setting(
				'alm_elementor_license',
				'alm_elementor_license_key',
				'alm_elementor_sanitize_license'
			);
		}
	}

	/**
	 * Sanitize the license activation.
	 *
	 * @param string $key The new license key.
	 * @since 1.0
	 * @return string
	 */
	function alm_elementor_sanitize_license( $key ) {
		$old = get_option( 'alm_elementor_license_key' );
		if ( $old && $key !== $old ) {
			delete_option( 'alm_elementor_license_status' );
		}
		return $key;
	}

	/**
	 * Initiate the class
	 *
	 * @since 1.0
	 */
	function alm_elementor() {
		global $alm_elementor;
		if ( ! isset( $alm_elementor ) ) {
			$alm_elementor = new ALMElementor();
		}
		return $alm_elementor;
	}
	alm_elementor();

endif;

/**
 * Software Licensing.
 *
 * @since 1.0
 */
function alm_elementor_plugin_updater() {
	if ( ! has_action( 'alm_pro_installed' ) && class_exists( 'EDD_SL_Plugin_Updater' ) ) {
		// retrieve our license key from the DB.
		$license_key = trim( get_option( 'alm_elementor_license_key' ) );
		$edd_updater = new EDD_SL_Plugin_Updater(
			ALM_STORE_URL,
			__FILE__,
			[
				'version' => ALM_ELEMENTOR_VERSION,
				'license' => $license_key,
				'item_id' => ALM_ELEMENTOR_ITEM_NAME,
				'author'  => 'Darren Cooney',
			]
		);
	}
}
add_action( 'admin_init', 'alm_elementor_plugin_updater', 0 );
