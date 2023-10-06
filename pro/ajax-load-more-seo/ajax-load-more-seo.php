<?php // phpcs:ignore
/**
 * Plugin Name: Ajax Load More: SEO
 * Plugin URI: https://connekthq.com/plugins/ajax-load-more/add-ons/search-engine-optimization/
 * Description: Ajax Load More extension to generate unique paging URLs with each query.
 * Author: Darren Cooney
 * Twitter: @KaptonKaos
 * Author URI: https://connekthq.com
 * Version: 1.9.5
 * License: GPL
 * Copyright: Darren Cooney & Connekt Media
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ALM_SEO_PATH', plugin_dir_path( __FILE__ ) );
define( 'ALM_SEO_URL', plugins_url( '', __FILE__ ) );
define( 'ALM_SEO_VERSION', '1.9.5' );
define( 'ALM_SEO_RELEASE', 'September 27, 2023' );

/**
 * Install the SEO add-on
 *
 * @since 1.0
 */
function alm_seo_install() {
	if ( ! is_plugin_active( 'ajax-load-more/ajax-load-more.php' ) ) {
		set_transient( 'alm_seo_admin_notice', true, 5 );
	}
}
register_activation_hook( __FILE__, 'alm_seo_install' );

/**
 * Display admin notice and de-activate if plugin does not meet the requirements.
 *
 * @since 1.9.4
 */
function alm_seo_admin_notice() {
	$slug   = 'ajax-load-more';
	$plugin = $slug . '-seo';
	// Ajax Load More Notice.
	if ( get_transient( 'alm_seo_admin_notice' ) ) {
		$install_url = get_admin_url() . '/update.php?action=install-plugin&plugin=' . $slug . '&_wpnonce=' . wp_create_nonce( 'install-plugin_' . $slug );
		$message     = '<div class="error">';
		$message    .= '<p>' . __( 'You must install and activate the core Ajax Load More plugin before using the Ajax Load More SEO Add-on.', 'ajax-load-more-seo' ) . '</p>';
		$message    .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, __( 'Install Ajax Load More Now', 'ajax-load-more-seo' ) ) . '</p>';
		$message    .= '</div>';
		echo wp_kses_post( $message );
		delete_transient( 'alm_seo_admin_notice' );
	}
}
add_action( 'admin_notices', 'alm_seo_admin_notice' );

if ( ! class_exists( 'ALMSEO' ) ) :

	/**
	 * ALM_Seo Class.
	 */
	class ALM_Seo {

		/**
		 * Construct function.
		 */
		public function __construct() {
			add_action( 'alm_seo_installed', array( &$this, 'alm_is_seo_installed' ) );
			add_action( 'wp_enqueue_scripts', array( &$this, 'alm_seo_enqueue_scripts' ) );
			add_action( 'alm_seo_settings', array( &$this, 'alm_seo_settings' ) );
			add_filter( 'alm_seo_shortcode', array( &$this, 'alm_seo_shortcode' ), 10, 4 );
			load_plugin_textdomain( 'ajax-load-more-seo', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
		}

		/**
		 * This function will build SEO shortcode params and send back to core ALM
		 *
		 * @param string $seo The value of SEO from the shortcode.
		 * @param string $preloaded The value of Preloaded from the shortcode.
		 * @param array  $options Plugin options.
		 * @param string $seo_offset The value of SEO Offset from the shortcode.
		 * @return $return string
		 * @author ConnektMedia
		 * @since 1.2
		 */
		public function alm_seo_shortcode( $seo, $preloaded, $options, $seo_offset ) {

			$seo_scrolltop = '30';
			if ( isset( $options['_alm_seo_scrolltop'] ) ) {
				$seo_scrolltop = $options['_alm_seo_scrolltop'];
			}

			$seo_controls = '1';
			if ( isset( $options['_alm_seo_browser_controls'] ) ) {
				$seo_controls = $options['_alm_seo_browser_controls'];
			}

			$seo_enable_scroll = 'false';
			if ( isset( $options['_alm_seo_scroll'] ) ) {
				$seo_enable_scroll = $options['_alm_seo_scroll'];
				if ( '1' === $seo_enable_scroll ) {
					$seo_enable_scroll = 'true';
				} else {
					$seo_enable_scroll = 'false';
				}
			} else {
				$seo_enable_scroll = 'false';
			}

			// Permalink Structure.
			$permalink_structure = get_option( 'permalink_structure' );
			$seo_permalink       = empty( $permalink_structure ) ? 'default' : 'pretty';

			// Get $paged var from WP.
			if ( get_query_var( 'paged' ) ) {
				$current_page = get_query_var( 'paged' );
			} elseif ( get_query_var( 'page' ) ) {
				$current_page = get_query_var( 'page' );
			} else {
				$current_page = 1;
			}

			// If preloaded then minus 1 page from SEO.
			$current_page = 'true' === $preloaded ? $current_page - 1 : $current_page;

			// Build data atts.
			$return  = ' data-seo="true"';
			$return .= $seo_offset === 'true' ? ' data-seo-offset="true"' : ''; // phpcs:ignore
			$return .= ' data-seo-start-page="' . $current_page . '"';
			$return .= ' data-seo-scroll="' . $seo_enable_scroll . '"';
			$return .= ' data-seo-scrolltop="' . $seo_scrolltop . '"';
			$return .= ' data-seo-controls="' . $seo_controls . '"';
			$return .= ' data-seo-permalink="' . $seo_permalink . '"';

			/**
			 * This function will remove the trailing slash from the URL.
			 * Core Plugin Hook.
			 *
			 * @return string
			 * @author ConnektMedia
			 * @since 1.7
			 */
			$trailing_slash = apply_filters( 'alm_seo_remove_trailing_slash', '' );
			if ( $trailing_slash ) {
				$return .= ' data-seo-trailing-slash="false"';
			}

			/**
			 * This function will remove the leading slash from the URL.
			 * Core Plugin Hook.
			 *
			 * @return string
			 * @author ConnektMedia
			 * @since 1.7
			 */
			$leading_slash = apply_filters( 'alm_seo_leading_slash', '' );
			if ( $leading_slash ) {
				$return .= ' data-seo-leading-slash="true"';
			}

			return $return;
		}

		/**
		 * Enqueue our scripts.
		 *
		 * @author ConnektMedia
		 * @since 1.0
		 */
		public function alm_seo_enqueue_scripts() {
			// Use minified libraries if SCRIPT_DEBUG is turned off.
			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
			wp_register_script( 'ajax-load-more-seo', plugins_url( '/js/alm-seo' . $suffix . '.js', __FILE__ ), array( 'ajax-load-more' ), ALM_SEO_VERSION, true );
		}

		/**
		 * Empty function to determine if seo is true.
		 *
		 * @author ConnektMedia
		 * @since 1.0
		 */
		public function alm_is_seo_installed() {
			// phpcs:disable
			// Empty return.
			// phpcs:enable
		}

		/**
		 * Create the SEO settings panel.
		 *
		 * @author ConnektMedia
		 * @since 1.2
		 */
		public function alm_seo_settings() {
			register_setting(
				'alm_seo_license',
				'alm_seo_license_key',
				'alm_seo_sanitize_license'
			);
			add_settings_section(
				'alm_seo_settings',
				'SEO Settings',
				'alm_seo_settings_callback',
				'ajax-load-more'
			);
			add_settings_field(
				'_alm_seo_scroll',
				__( 'Scroll to Page', 'ajax-load-more-seo' ),
				'alm_seo_scroll_callback',
				'ajax-load-more',
				'alm_seo_settings'
			);
			add_settings_field(
				'_alm_seo_scrolltop',
				__( 'Scroll Top', 'ajax-load-more-seo' ),
				'alm_seo_scrolltop_callback',
				'ajax-load-more',
				'alm_seo_settings'
			);
			add_settings_field(
				'_alm_seo_browser_controls',
				__( 'Back/Fwd Buttons', 'ajax-load-more-seo' ),
				'alm_seo_browser_controls_callback',
				'ajax-load-more',
				'alm_seo_settings'
			);
		}

	}

	/**
	 * SEO Setting Heading
	 *
	 * @author ConnektMedia
	 * @since 1.0
	 */
	function alm_seo_settings_callback() {
		$html = '<p>' . __( 'Customize your installation of the <a href="http://connekthq.com/plugins/ajax-load-more/seo/">Search Engine Optimization</a> add-on.', 'ajax-load-more-seo' ) . '</p>';
		echo wp_kses_post( $html );
	}

	/**
	 * Set the speed of auto scroll.
	 *
	 * @author ConnektMedia
	 * @since 1.0
	 */
	function alm_seo_scroll_callback() {
		$options = get_option( 'alm_settings' );
		if ( ! isset( $options['_alm_seo_scroll'] ) ) {
			$options['_alm_seo_scroll'] = '0';
		}
		$html  = '<input type="hidden" name="alm_settings[_alm_seo_scroll]" value="0" />';
		$html .= '<input type="checkbox" name="alm_settings[_alm_seo_scroll]" id="alm_scroll_page" value="1"' . ( ( $options['_alm_seo_scroll'] ) ? ' checked="checked"' : '' ) . ' />';
		$html .= '<label for="alm_scroll_page">' . __( 'Enable window scrolling', 'ajax-load-more-seo' ) . '<br/><span>' . __( 'If scrolling is enabled, the users window will scroll to the current page on \'Load More\' action.', 'ajax-load-more-seo' ) . '</span></label>';

		echo $html; // phpcs:ignore
	}

	/**
	 * Set the scrlltop value of window scrolling.
	 *
	 * @author ConnektMedia
	 * @since 1.0
	 */
	function alm_seo_scrolltop_callback() {
		$options = get_option( 'alm_settings' );
		if ( ! isset( $options['_alm_seo_scrolltop'] ) ) {
			$options['_alm_seo_scrolltop'] = '30';
		}
		echo '<label for="alm_settings[_alm_seo_scrolltop]">' . __( 'Set the scrolltop position of the window when scrolling to post.', 'ajax-load-more-seo' ) . '</label><br/><input type="number" class="sm" id="alm_settings[_alm_seo_scrolltop]" name="alm_settings[_alm_seo_scrolltop]" step="1" min="0" value="' . $options['_alm_seo_scrolltop'] . '" placeholder="30" /> '; // phpcs:ignore
	}

	/**
	 * Disable back/fwd button when URLs updated (uses replaceState vs pushState).
	 *
	 * @author ConnektMedia
	 * @since 1.7
	 */
	function alm_seo_browser_controls_callback() {
		$options = get_option( 'alm_settings' );

		if ( ! isset( $options['_alm_seo_browser_controls'] ) ) {
			$options['_alm_seo_browser_controls'] = '1';
		}

		$html  = '<input type="hidden" name="alm_settings[_alm_seo_browser_controls]" value="0" />';
		$html .= '<input type="checkbox" id="_alm_seo_browser_controls" name="alm_settings[_alm_seo_browser_controls]" value="1"' . ( ( $options['_alm_seo_browser_controls'] ) ? ' checked="checked"' : '' ) . ' />';
		$html .= '<label for="_alm_seo_browser_controls">' . __( 'Enable Back/Fwd Browser Buttons.', 'ajax-load-more-seo' ) . '<br/><span>' . __( 'Allow users to navigate Ajax generated content using the back and forward browser buttons.', 'ajax-load-more-seo' ) . '</span></label>';

		echo $html; // phpcs:ignore
	}

	/**
	 *  Sanitize our license activation.
	 *
	 * @param string $new The new license key.
	 * @author ConnektMedia
	 * @since 1.3.0
	 */
	function alm_seo_sanitize_license( $new ) {
		$old = get_option( 'alm_seo_license_key' );
		if ( $old && $old !== $new ) {
			delete_option( 'alm_seo_license_status' );
		}
		return $new;
	}

	/**
	 * The main function responsible for returning Ajax Load More SEO.
	 *
	 * @author ConnektMedia
	 * @since 1.0
	 */
	function alm_seo_plugin() {
		global $alm_seo_plugin;
		if ( ! isset( $alm_seo_plugin ) ) {
			$alm_seo_plugin = new ALM_Seo();
		}
		return $alm_seo_plugin;
	}
	alm_seo_plugin();

endif;


/**
 * Software Licensing
 */
function alm_seo_plugin_updater() {
	if ( ! has_action( 'alm_pro_installed' ) && class_exists( 'EDD_SL_Plugin_Updater' ) ) {
		$license_key = trim( get_option( 'alm_seo_license_key' ) );
		$edd_updater = new EDD_SL_Plugin_Updater(
			ALM_STORE_URL,
			__FILE__,
			array(
				'version' => ALM_SEO_VERSION,
				'license' => $license_key,
				'item_id' => ALM_SEO_ITEM_NAME,
				'author'  => 'Darren Cooney',
			)
		);
	}
}
add_action( 'admin_init', 'alm_seo_plugin_updater', 0 );

/* End Software Licensing */
