<?php
/**
 * Plugin Name: Ajax Load More: Layouts
 * Plugin URI: https://connekthq.com/plugins/ajax-load-more/add-ons/theme-repeaters/
 * Description: Ajax Load More extension that adds predefined layouts for your repeater templates.
 * Author: Darren Cooney
 * Twitter: @KaptonKaos
 * Author URI: http://connekthq.com
 * Version: 2.0.1
 * Copyright: Darren Cooney & Connekt Media
 *
 * @package ALMLayouts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ALM_LAYOUTS_VERSION', '2.0.1' );
define( 'ALM_LAYOUTS_RELEASE', 'February 14, 2023' );
define( 'ALM_LAYOUTS_PATH', plugin_dir_path( __FILE__ ) );
define( 'ALM_LAYOUTS_URL', plugins_url( '', __FILE__ ) );

/**
 * Activation hook
 *
 *  @since 1.0
 */
function alm_layouts_install() {
	if ( ! is_plugin_active( 'ajax-load-more/ajax-load-more.php' ) ) {
		set_transient( 'alm_layouts_admin_notice', true, 5 );
	}
}
register_activation_hook( __FILE__, 'alm_layouts_install' );

/**
 * Display admin notice and de-activate if plugin does not meet the requirements.
 *
 * @since 2.0.0
 */
function alm_layouts_admin_notice() {
	$slug   = 'ajax-load-more';
	$plugin = $slug . '-layouts';
	// Ajax Load More Notice.
	if ( get_transient( 'alm_layouts_admin_notice' ) ) {
		$install_url = get_admin_url() . '/update.php?action=install-plugin&plugin=' . $slug . '&_wpnonce=' . wp_create_nonce( 'install-plugin_' . $slug );
		$message     = '<div class="error">';
		$message    .= '<p>' . __( 'You must install and activate the core Ajax Load More plugin before using the Ajax Load More Layouts Add-on.', 'ajax-load-more-layouts' ) . '</p>';
		$message    .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, __( 'Install Ajax Load More Now', 'ajax-load-more-layouts' ) ) . '</p>';
		$message    .= '</div>';
		echo wp_kses_post( $message );
		delete_transient( 'alm_layouts_admin_notice' );
	}
}
add_action( 'admin_notices', 'alm_layouts_admin_notice' );

if ( ! class_exists( 'ALMLayouts' ) ) :
	/**
	 * Initiate the class.
	 */
	class ALMLayouts {

		/**
		 * Set up constructors.
		 */
		public function __construct() {
			add_action( 'after_setup_theme', array( &$this, 'alm_layouts_image_sizes' ) );
			add_action( 'alm_layouts_installed', array( &$this, 'alm_lay outs_installed' ) );
			add_action( 'alm_layouts_settings', array( &$this, 'alm_layouts_settings' ) );
			add_action( 'alm_layouts_custom_css', array( &$this, 'alm_layouts_custom_css' ), 10, 2 );
			add_action( 'wp_enqueue_scripts', array( &$this, 'alm_layouts_enqueue_scripts' ) );
			add_action( 'alm_get_layouts_add_on', array( &$this, 'alm_get_layouts_add_on' ) );
			add_filter( 'alm_get_layout_classes', array( &$this, 'alm_get_layout_classes' ), 10, 3 );
		}

		/**
		 * Get the classes for the layouts.
		 *
		 * @param int    $cols    The amount of columns to render.
		 * @param string $gap     The grid gap.
		 * @param string $classes Classes to append.
		 * @return string
		 */
		public function alm_get_layout_classes( $cols = 3, $gap = 'default', $classes = '' ) {
			$classes .= ' alm-grid alm-grid-cols-' . $cols;
			$classes .= $gap !== 'default' ? ' alm-grid-gap-' . $gap : '';
			return $classes;
		}

		/**
		 * Return custom CSS from ALM Settings page.
		 *
		 * @param string $index The current ALM index count to prevent loading the CSS multiple times.
		 * @since 1.0
		 */
		public function alm_layouts_custom_css( $index ) {
			$options = get_option( 'alm_settings' );
			if ( $index === 1 && isset( $options['_alm_layouts_css'] ) && $options['_alm_layouts_css'] ) {
				echo '<style>' . $options['_alm_layouts_css'] . '</style>'; //phpcs:ignore
			}
		}

		/**
		 * Add the required image sizes.
		 *
		 * @since 1.0
		 */
		public function alm_layouts_image_sizes() {
			add_image_size( 'alm-cta', 800, 450, true ); // cta.
			add_image_size( 'alm-gallery', 800, 600, true ); // gallery.
		}

		/**
		 * Get custom layouts list.
		 *
		 * @since 1.0
		 */
		public function alm_get_layouts_add_on() {
			include ALM_LAYOUTS_PATH . 'admin/includes/layout-options.php';
		}

		/**
		 * An empty function to determine if add-on is activated.
		 *
		 * @since 1.0
		 * @return void
		 */
		public function alm_layouts_installed() {
			// Empty.
		}

		/**
		 * Enqueue our scripts.
		 *
		 *  @since 1.0
		 */
		public function alm_layouts_enqueue_scripts() {
			if ( ! alm_do_inline_css( '_alm_inline_css' ) && class_exists( 'ALM_ENQUEUE' ) ) {
				// Use minified libraries if SCRIPT_DEBUG is turned off.
				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				$file   = ALM_LAYOUTS_URL . '/core/css/ajax-load-more-layouts' . $suffix . '.css';
				ALM_ENQUEUE::alm_enqueue_css( 'ajax-load-more-layouts', $file );
			}
		}

		/**
		 * Create the Layouts settings panel.
		 *
		 * @since 1.0
		 */
		public function alm_layouts_settings() {
			register_setting(
				'alm_layouts_license',
				'alm_layouts_license_key',
				'alm_layouts_sanitize_license'
			);

			add_settings_section(
				'alm_layouts_settings',
				'Layouts Settings',
				'alm_layouts_callback',
				'ajax-load-more'
			);

			add_settings_field(
				'_alm_layouts_css',
				__( 'Styling', 'ajax-load-more' ),
				'alm_layouts_css_callback',
				'ajax-load-more',
				'alm_layouts_settings'
			);
		}
	}

	/**
	 * Sanitize our license activation
	 *
	 * @param string $new The new key.
	 * @since 1.0
	 */
	function alm_layouts_sanitize_license( $new ) {
		$old = get_option( 'alm_layouts_license_key' );
		if ( $old && $old !== $new ) {
			delete_option( 'alm_layouts_license_status' );
		}
		return $new;
	}

	/**
	 * Section setting heading
	 *
	 * @since 1.0
	 */
	function alm_layouts_callback() {
		$html = '<p>' . __( 'Customize your installation of the <a href="https://connekthq.com/plugins/ajax-load-more/add-ons/layouts/">Layouts</a> add-on.', 'ajax-load-more-layouts' ) . '</p>';
		echo $html; //phpcs:ignore
	}

	/**
	 * Custom CSS for layouts
	 *
	 * @since 1.0
	 */
	function alm_layouts_css_callback() {
		$options = get_option( 'alm_settings' );

		$html  = '<label for="_alm_layouts_css">' . __( 'Enter Custom Layout CSS <span style="display: block;">Use this section to inject custom CSS related to the Layouts add-on.</span>', 'ajax-load-more-layouts' );
		$html .= '<span style="display:block;"><i class="fa fa-file-text-o"></i> &nbsp;<a target="blank" href="' . ALM_LAYOUTS_URL . '/core/css/ajax-load-more-layouts.css">View Layouts CSS</a></span>';
		$html .= '</label>';

		$html .= '<textarea id="_alm_layouts_css" name="alm_settings[_alm_layouts_css]">';
		$html .= $options['_alm_layouts_css'];
		$html .= '</textarea>';

		$html .= '<label style="cursor: default;"><span style="display:block">You should prefix all CSS overrides with <pre style="display:inline;">.alm-layouts .alm-listing .alm-layout{ }</pre></span></label>';

		echo $html; //phpcs:ignore
	}

	// Helper Functions.

	/**
	 * Get custom excerpt.
	 *
	 * @param string $limit The max amount of words to render.
	 * @param string $after Optional text to display after.
	 * @return void
	 */
	function alm_get_excerpt( $limit, $after = null ) {
		$excerpt = explode( ' ', get_the_excerpt(), $limit );
		if ( count( $excerpt ) >= $limit ) {
			array_pop( $excerpt );
			$excerpt = implode( ' ', $excerpt ) . '...';
		} else {
			$excerpt = implode( ' ', $excerpt );
		}
		$excerpt = preg_replace( '`[[^]]*]`', '', $excerpt );
		if ( $after ) {
			$excerpt = $excerpt . $after;
		}
		if ( $excerpt ) {
			echo '<p>' . wp_kses_post( $excerpt ) . '</p>';
		}
	}

	/**
	 * Is item odd
	 *
	 * @param int $number The number to compare.
	 * @return void
	 */
	function alm_is_odd( $number ) {
		if ( $number % 2 !== 0 ) {
			echo 'odd';
		}
	}

	/**
	 * Is last item in 3 column layout.
	 *
	 * @param int $number The number to compare.
	 * @return void
	 */
	function alm_is_last( $number ) {
		if ( $number % 3 == 0 ) { // phpcs:ignore
			echo 'last';
		}
	}

	/**
	 * Is last item in 4 column layout.
	 *
	 * @param int $number The number to compare.
	 * @return void
	 */
	function alm_is_4col_last( $number ) {
		if ( $number % 4 == 0 ) { // phpcs:ignore
			echo 'last';
		}
	}

	/**
	 * The main function responsible for returning Ajax Load More Layouts.
	 *
	 * @since 1.0
	 */
	function alm_layouts() {
		global $alm_layouts;
		if ( ! isset( $alm_layouts ) ) {
			$alm_layouts = new ALMLayouts();
		}
		return $alm_layouts;
	}

	// initialize.
	alm_layouts();

endif;

/**
 * Software Licensing
 *
 * @since 1.0
 */
function alm_layouts_plugin_updater() {
	if ( ! has_action( 'alm_pro_installed' ) && class_exists( 'EDD_SL_Plugin_Updater' ) ) {
		$license_key = trim( get_option( 'alm_layouts_license_key' ) );
		$edd_updater = new EDD_SL_Plugin_Updater(
			ALM_STORE_URL,
			__FILE__,
			array(
				'version' => ALM_LAYOUTS_VERSION,
				'license' => $license_key,
				'item_id' => ALM_LAYOUTS_ITEM_NAME,
				'author'  => 'Darren Cooney',
			)
		);
	}
}
add_action( 'admin_init', 'alm_layouts_plugin_updater', 0 );
