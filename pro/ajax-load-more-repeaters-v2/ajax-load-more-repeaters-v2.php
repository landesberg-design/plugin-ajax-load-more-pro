<?php
/**
 * Plugin Name: Ajax Load More: Custom Repeaters
 * Plugin URI: http://connekthq.com/plugins/ajax-load-more/custom-repeaters/
 * Description: Ajax Load More add-on to allow for unlimited repeater templates.
 * Author: Darren Cooney
 * Twitter: @KaptonKaos
 * Author URI: http://connekthq.com
 * Version: 2.5.9
 * License: GPL
 * Copyright: Darren Cooney & Connekt Media
 *
 * @package AjaxLoadMoreRepeaters
 */

define( 'ALM_UNLIMITED_PATH', plugin_dir_path( __FILE__ ) );
define( 'ALM_UNLIMITED_URL', plugins_url( '', __FILE__ ) );
define( 'ALM_UNLIMITED_VERSION', '2.5.9' );
define( 'ALM_UNLIMITED_RELEASE', 'January 5, 2023' );

/**
 * Core activation hook function.
 *
 * @param boolean $network_wide To enable the plugin for all sites in the network or just the current site. Multisite only.
 * @since 2.0
 */
function alm_unlimited_activation( $network_wide ) {
	if ( is_plugin_active( 'ajax-load-more/ajax-load-more.php' ) ) {
		// Ajax Load More is activated.

		global $wpdb;
		add_option( 'alm_unlimited_version', ALM_UNLIMITED_VERSION ); // Add to WP Options table.

		if ( is_multisite() && $network_wide ) {
			// Get all blogs in the network and create `alm_unlimited` table for each.
			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				alm_unlimited_create_table();
				restore_current_blog();
			}
		} else {
			alm_unlimited_create_table();

		}
	} else {
		set_transient( 'alm_custom_repeaters_admin_notice', true, 5 );
	}
}
register_activation_hook( __FILE__, 'alm_unlimited_activation' );
add_action( 'wpmu_new_blog', 'alm_unlimited_activation' );

/**
 * Display admin notice and de-activate if plugin does not meet the requirements.
 *
 * @since 2.5.6
 */
function alm_unlimited_admin_notice() {
	$slug   = 'ajax-load-more';
	$plugin = $slug . '-repeaters-v2';
	// Ajax Load More Notice.
	if ( get_transient( 'alm_custom_repeaters_admin_notice' ) ) {
		$install_url = get_admin_url() . '/update.php?action=install-plugin&plugin=' . $slug . '&_wpnonce=' . wp_create_nonce( 'install-plugin_' . $slug );
		$message     = '<div class="error">';
		$message    .= '<p>' . __( 'You must install and activate the core Ajax Load More plugin before using the Ajax Load More Custom Repeaters Add-on.', 'ajax-load-more-repeaters-v2' ) . '</p>';
		$message    .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, __( 'Install Ajax Load More Now', 'ajax-load-more-repeaters-v2' ) ) . '</p>';
		$message    .= '</div>';
		echo wp_kses_post( $message );
		delete_transient( 'alm_custom_repeaters_admin_notice' );
	}
}
add_action( 'admin_notices', 'alm_unlimited_admin_notice' );

/**
 * Create Table in WP DB.
 *
 * @since 2.0
 * @updated 2.5.2
 */
function alm_unlimited_create_table() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'alm_unlimited';
	// Create table, if it doesn't already exist.
	if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) !== $table_name ) { // phpcs:ignore
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			name text NOT NULL,
			repeaterDefault longtext NOT NULL,
			alias TEXT NOT NULL,
			pluginVersion text NOT NULL,
			UNIQUE KEY id (id)
		);";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}
}

/**
 * Table exist checker function
 *
 * @since 2.5.2
 */
function alm_unlimited_check_table() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'alm_unlimited';
	if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) !== $table_name ) { // phpcs:ignore
		alm_unlimited_create_table();
	}
}

if ( ! class_exists( 'AjaxLoadMoreRepeaters' ) ) :

	/**
	 * Initiate the core AjaxLoadMoreRepeaters class.
	 */
	class AjaxLoadMoreRepeaters {

		/**
		 * Construst the class.
		 */
		public function __construct() {
			add_action( 'alm_unlimited_repeaters', [ &$this, 'alm_unlimited_add_ons' ] );
			add_action( 'alm_get_unlimited_repeaters', [ &$this, 'alm_get_unlimited_add_ons' ] );
			add_action( 'alm_unlimited_installed', [ &$this, 'alm_is_unlimited_installed' ] );
			add_action( 'plugins_loaded', [ &$this, 'alm_unlimited_update' ] );
			add_action( 'alm_unlimited_settings', [ &$this, 'alm_unlimited_settings' ] );

			// Ajax actions.
			add_action( 'wp_ajax_alm_unlimited_create', [ &$this, 'alm_unlimited_create' ] );
			add_action( 'wp_ajax_alm_unlimited_delete', [ &$this, 'alm_unlimited_delete' ] );

			// Load text domain.
			load_plugin_textdomain( 'ajax-load-more-repeaters-v2', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
		}

		/**
		 * Get absolute path to repeater directory base
		 *
		 * Multisite installs directories will be `uploads/sites/{id}/alm_templates`
		 *
		 * @return $path;
		 * @since 2.5
		 */
		public static function alm_unlimited_get_repeater_path() {
			$upload_dir = wp_upload_dir();
			$path       = apply_filters( 'alm_repeater_path', $upload_dir['basedir'] . '/alm_templates' );
			return $path;
		}

		/**
		 * Create repeater template directory.
		 *
		 * @param string $dir The directory path.
		 * @since 2.5
		 */
		public static function alm_unlimited_mkdir( $dir ) {

			// Does $dir exist?
			if ( ! is_dir( $dir ) ) {
				wp_mkdir_p( $dir );

				// Check again after creating it (permission checker).
				if ( ! is_dir( $dir ) ) {
					echo esc_html_e( 'Error creating repeater template directory', 'ajax-load-more-repeaters-v2' );
					echo ' - ' . esc_html( $dir );
				}
			}
		}

		/**
		 * Update repeaters if the database version of the repeater doesn't match the current plugin version.
		 * Check by version numbers.
		 *
		 * @since 2.0
		 */
		public function alm_unlimited_update() {
			if ( ! get_option( 'alm_unlimited_version' ) ) {
				// Add to WP options.
				add_option( 'alm_unlimited_version', ALM_UNLIMITED_VERSION );
			}

			// Get version from WP options.
			$alm_unlimited_installed_ver = get_option( 'alm_unlimited_version' );
			if ( $alm_unlimited_installed_ver !== ALM_UNLIMITED_VERSION ) {
				$this->alm_unlimited_run_update();
			}
		}

		/**
		 * Run the plugin update on all 'blogs'.
		 *
		 * @since 2.4
		 */
		public function alm_unlimited_run_update() {
			global $wpdb;

			if ( is_multisite() ) {
				$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

				// Loop all blogs and run update routine.
				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					$this->alm_update_unlimited_template_files();
					update_option( 'alm_unlimited_version', ALM_UNLIMITED_VERSION );
					restore_current_blog();
				}
			} else {
				$this->alm_update_unlimited_template_files();
				update_option( 'alm_unlimited_version', ALM_UNLIMITED_VERSION );
			}
		}

		/**
		 * Update routine for Custom Repeater templates.
		 *
		 * @throws Exception Unable to write to template file.
		 * @since 2.4
		 */
		public function alm_update_unlimited_template_files() {
			global $wpdb;
			$table_name = $wpdb->prefix . 'alm_unlimited';

			// Get all templates from DB.
			$rows = $wpdb->get_results( "SELECT * FROM $table_name" ); // phpcs:ignore

			// If required: Create base directory (alm_templates).
			$base_dir = self::alm_unlimited_get_repeater_path();
			self::alm_unlimited_mkdir( $base_dir );

			if ( $rows ) {
				// Loop rows.
				foreach ( $rows as $row ) {
					$repeater = $row->name;
					$version  = $row->pluginVersion; // phpcs:ignore

					// Update `pluginVersion` value in database.
					// Note: This might not be necessary with the update in v2.5.
					$data_update = [ 'pluginVersion' => ALM_UNLIMITED_VERSION ];
					$data_where  = [ 'name' => $repeater ];
					$wpdb->update( $table_name, $data_update, $data_where );

					// Write to repeater file.
					$data = $wpdb->get_var( "SELECT repeaterDefault FROM $table_name WHERE name = '$repeater'" ); // phpcs:ignore

					// Current Repeater.
					$file = $base_dir . '/' . $repeater . '.php';

					/**
					 * Create template only if the template does not exist.
					 *
					 * Note: This should never ever run, but this is used as a fallback incase for some reason Repeater
					 *       have been deleted or cleaned by another plugin.
					 */
					if ( ! file_exists( $file ) ) {
						try {
							// phpcs:ignore
							$o = fopen( $file, 'w+' ); // Open file.
							if ( ! $o ) {
								throw new Exception( '[Ajax Load More] Unable to open Repeater Template - ' . $file );
							}
							// phpcs:ignore
							$w = fwrite( $o, $data ); // Save the file.
							if ( ! $w ) {
								throw new Exception( '[Ajax Load More] Unable to save Repeater Template - ' . $file );
							}
							// phpcs:ignore
							fclose( $o );

						} catch ( Exception $e ) {
							// Display error message in console.
							if ( ! isset( $options['_alm_error_notices'] ) || $options['_alm_error_notices'] === '1' ) {
								echo '<script>console.log("' . wp_kses_post( $e->getMessage() ) . '");</script>';
							}
						}
					}
				}
			}
		}

		/**
		 * List repeaters for selection on shortcode builder page.
		 *
		 * @since 2.0
		 */
		public function alm_get_unlimited_add_ons() {
			global $wpdb;
			$table_name = $wpdb->prefix . 'alm_unlimited';
			$rows       = $wpdb->get_results( "SELECT * FROM $table_name" ); // phpcs:ignore
			$i          = 0;
			foreach ( $rows as $repeater ) {
				// Get repeater alias, if avaialble.
				$i++;
				$name           = $repeater->name;
				$repeater_alias = $repeater->alias;
				if ( empty( $repeater_alias ) ) {
					echo '<option name="' . esc_attr( $name ) . '" id="chk-' . esc_attr( $name ) . '" value="' . esc_attr( $name ) . '">Template #' . esc_attr( $i ) . '</option>';
				} else {
					echo '<option name="' . esc_attr( $name ) . '" id="chk-' . esc_attr( $name ) . '" value="' . esc_attr( $name ) . '">' . esc_attr( $repeater_alias ) . '</option>';
				}
			}
		}

		/**
		 * An empty function to determine if custom repeater is true.
		 *
		 * @since 2.0
		 */
		public function alm_is_unlimited_installed() {
			// Empty return
			// Function called from /ajax-load-more/admin/admin.php.
		}

		/**
		 * Our front end for the repeaters.
		 *
		 * @since 2.0
		 */
		public function alm_unlimited_add_ons() {

			// Create table if it doesn't exist.
			alm_unlimited_check_table();

			// Repeater loop.
			global $wpdb;
			$table_name = $wpdb->prefix . 'alm_unlimited';
			$rowcount   = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" ); // phpcs:ignore
			$rows       = $wpdb->get_results( "SELECT * FROM $table_name" ); // phpcs:ignore
			?>
			<div id="unlmited-container">
				<?php
				if ( $rowcount > 0 ) :
					$i        = 0;
					$base_dir = self::alm_unlimited_get_repeater_path();
					self::alm_unlimited_mkdir( $base_dir );

					foreach ( $rows as $repeater ) :
						$i++;
						$repeater_file  = $repeater->name;
						$repeater_name  = 'Template #' . $i;
						$repeater_alias = $repeater->alias;
						if ( ! empty( $repeater_alias ) ) { // Set alias.
							$heading = $repeater_alias;
						} else {
							$heading = $repeater_name;
						}
						?>
				<div class="row template unlimited">
					<div>
						<h3 class="heading" data-default="<?php echo esc_html( $repeater_name ); ?>"><?php echo wp_kses_post( $heading ); ?></h3>
						<div class="expand-wrap">
							<div class="wrap repeater-wrap" data-name="<?php echo wp_kses_post( $repeater_file ); ?>" data-type="unlimited">

								<div class="alm-row alm-row--margin-btm">
									<div class="column column--half">
										<label class="template-title has-margin-btm" for="alias-<?php echo esc_html( $repeater_file ); ?>">
											<?php esc_html_e( 'Template Alias:', 'ajax-load-more-repeaters-v2' ); ?>
											<span><?php esc_html_e( 'Enter a unique name for this template.', 'ajax-load-more-repeaters-v2' ); ?></span>
										</label>
										<?php
											$alias = ( empty( $repeater_alias ) ) ? $repeater_name : $repeater_alias;
											echo '<input type="text" id="alias-' . esc_html( $repeater_file ) . '" class="_alm_repeater_alias" value="' . esc_html( $alias ) . '" maxlength="55" placeholder="' . esc_html__( 'Blog Listing', 'ajax-load-more-repeaters-v2' ) . '">';
										?>
									</div>
									<div class="column column--half">
										<label class="template-title has-margin-btm" for="id-<?php echo esc_html( $repeater_file ); ?>">
											<?php esc_html_e( 'Template ID:', 'ajax-load-more-repeaters-v2' ); ?>
											<span><?php esc_html_e( 'The unique ID assigned to this template (non-editable).', 'ajax-load-more-repeaters-v2' ); ?></span>
										</label>
										<input type="text" class="disabled-input" id="id-<?php echo esc_html( $repeater_file ); ?>" value="<?php echo esc_html( $repeater_file ); ?>" readonly="readonly">
									</div>
								</div><!-- /.alm-row -->

								<div class="alm-row alm-row--margin-btm">
									<div class="column column--two-third">
										<label class="template-title trigger-codemirror" data-id="<?php echo esc_html( $repeater_file ); ?>" for="template-<?php echo esc_html( $repeater_file ); ?>">
											<?php esc_html_e( 'Template Code:', 'ajax-load-more-repeaters-v2' ); ?>
											<span><?php esc_html_e( 'Enter the PHP and HTML markup for this template.', 'ajax-load-more-repeaters-v2' ); ?></span>
										</label>
										</div>
										<div class="column column--one-third">
											<?php
												do_action( 'alm_get_layouts' ); // Layouts - Template Selection.
											?>
										</div>
									</div><!-- /.alm-row -->

									<div class="alm-row alm-row--margin-btm">
										<div class="column textarea-wrap">
											<?php
												$filename = $base_dir . '/' . $repeater_file . '.php';
												// phpcs:ignore
												$handle   = fopen( $filename, 'r' );
												// phpcs:ignore
												$content = filesize( $filename ) !== 0 ? fread( $handle, filesize( $filename ) ) : '';
												// phpcs:ignore
												fclose( $handle );
											?>
											<textarea rows="10" id="<?php echo esc_html( $repeater_file ); ?>" class="_alm_repeater"><?php echo $content ? $content : ''; //phpcs:ignore ?></textarea>
											<script>
											var editor_<?php echo esc_html( $repeater_file ); ?> = CodeMirror.fromTextArea(document.getElementById("<?php echo esc_html( $repeater_file ); ?>"),
											{
												mode:  "application/x-httpd-php",
												lineNumbers: true,
												lineWrapping: true,
												indentUnit: 0,
												matchBrackets: true,
												viewportMargin: Infinity,
												extraKeys: {"Ctrl-Space": "autocomplete"},
											});
											</script>
										</div>
									</div><!-- /.alm-row -->

									<div class="alm-row">
										<div class="column">
											<input type="submit" value="<?php esc_html_e( 'Save Template', 'ajax-load-more-repeaters-v2' ); ?>" class="button button-primary save-repeater" data-editor-id="<?php echo esc_html( $repeater_file ); ?>">
											<div class="saved-response">&nbsp;</div>
											<p class="alm-delete">
												<a href="javascript:void(0);"><?php esc_html_e( 'Delete', 'ajax-load-more-repeaters-v2' ); ?></a>
											</p>
											<?php
												$repeater_options = [
													'path' => $filename,
													'name' => $repeater_file,
													'type' => 'standard',
												];
												include ALM_PATH . 'admin/includes/components/repeater-options.php';
												unset( $repeater_options );
												?>
										</div>
									</div><!-- /.alm-row -->

								</div><!-- /.wrap -->
							</div><!-- /.expand-wrap -->
							<div class="clear"></div>
						</div>
					</div><!-- /.row.template -->
						<?php
					endforeach;
				endif;
				?>
			</div><!-- /#unlmited-container -->
			<p class="alm-add-template" id="alm-add-template" style="margin-top:30px">
				<a href="javascript:void(0);">
					<i class="fa fa-plus-square"></i> <?php esc_html_e( 'Add New Template', 'ajax-load-more-repeaters-v2' ); ?>
				</a>
			</p>
			<script>
				jQuery(document).ready(function($) {
					// Check alias.
					$(document).on('keyup', '._alm_repeater_alias', function(){
						var el = $(this),
						heading = el.parent().parent().parent().parent().find('h3.heading');
						var val = el.val(),
						defaultVal = heading.data('default');
						if(val === ''){
							heading.text(defaultVal);
						}else{
							heading.text(val);
						}
					});

					// ADD template.
					$('#alm-add-template a').on('click', function(){
						var el = $(this);
						if(!el.hasClass('active')){
							el.addClass('active');

							// Create div
							var container = $('#unlmited-container'),
							div = $('<div class="row unlimited new" />');
							div.appendTo(container);
							div.fadeIn(250);

							// Run ajax.
							$.ajax({
								type: 'POST',
								url: alm_admin_localize.ajax_admin_url,
								data: {
								action: 'alm_unlimited_create',
								nonce: alm_admin_localize.alm_admin_nonce,
								},
								dataType: "JSON",
								success: function(data) {
								div.load("<?php echo esc_html( ALM_UNLIMITED_URL ); ?>/includes/template.php", {
									id: data.id,
									alias: data.alias,
									defaultVal: data.defaultVal
								}, function(){ // .load() complete.
									div.addClass('done');
									$('.unlimited-wrap', div).slideDown(350, 'alm_unlimited_ease', function(){
										div.removeClass('new');
										div.removeClass('done');
										el.removeClass('active');
										$('.CodeMirror').each(function(i, el){
											el.CodeMirror.refresh();
										});
									});
								});
							},
							error: function(xhr, status, error) {
								responseText.html('<p><?php esc_attr_e( 'Error - Something went wrong and the template could not be created.', 'ajax-load-more-repeaters-v2' ); ?></p>');
								div.remove();
								el.removeClass('active');
							}
							});
						}
					});

					// DELETE template.
					$(document).on('click', '.alm-delete', function(){

						var r = confirm("<?php esc_attr_e( 'Are you sure you want to delete this template?', 'ajax-load-more-repeaters-v2' ); ?>");
						if (r == true && !$(this).hasClass('deleting')) {
							var el = $(this);
							var container = el.closest('.repeater-wrap');
							var item = container.parent().parent().parent('.row.unlimited');
							var repeater = container.data('name');

							el.addClass('deleting');
							item.addClass('deleting');
							$.ajax({
								type: 'POST',
								url: alm_admin_localize.ajax_admin_url,
								data: {
									action: 'alm_unlimited_delete',
									repeater: repeater,
									nonce: alm_admin_localize.alm_admin_nonce
								},
								dataType: "html",
								success: function(data) {
									setTimeout(function() {
										item.addClass('deleted');
										item.slideUp(350, 'alm_unlimited_ease', function(){
											item.remove();
										})
									}, 250);
									console.log('Template Deleted');
								},
								error: function(xhr, status, error) {
									item.removeClass('deleting');
									el.removeClass('deleting');
									responseText.html('<p><?php esc_attr_e( 'Error - Something went wrong and the template could not be deleted.', 'ajax-load-more-repeaters-v2' ); ?></p>');
								}
							});
						}
					});
					$.easing.alm_unlimited_ease = function (x, t, b, c, d) {
						if ((t /= d / 2) < 1) {
							return c / 2 * t * t + b;
						}
						return -c / 2 * ((--t) * (t - 2) - 1) + b;
					};

				});
				</script>
			<?php
		}

		/**
		 * Create a new Repeater Template.
		 *
		 * @since 2.0
		 */
		public function alm_unlimited_create() {
			$form_data = filter_input_array( INPUT_POST );

			if ( ! current_user_can( 'edit_theme_options' ) || ! isset( $form_data['nonce'] ) ) {
				// Bail early if missing WP capabilities or nonce.
				wp_die( esc_attr__( 'You don\'t belong here.', 'ajax-load-more-repeaters-v2' ) );
			}

			if ( ! wp_verify_nonce( $form_data['nonce'], 'alm_repeater_nonce' ) ) {
				// Verify nonce.
				wp_die( esc_attr__( 'Error - unable to verify nonce, please try again.', 'ajax-load-more-repeaters-v2' ) );
			}

			// Create table if it doesn't exist.
			alm_unlimited_check_table();

			// Get values from DB.
			global $wpdb;
			$table_name = $wpdb->prefix . 'alm_unlimited';
			$blog_id    = $wpdb->blogid;

			$count = floatval( $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" ) ); // phpcs:ignore
			$count = ++$count;

			$default_value = '<?php // ' . __( 'Enter your template code here', 'ajax-load-more-repeaters-v2' ) . '.  ?>';

			// Insert into DB.
			$wpdb->insert(
				$table_name,
				[
					'name'            => 'temp',
					'repeaterDefault' => $default_value,
					'alias'           => '',
					'pluginVersion'   => ALM_UNLIMITED_VERSION,
				]
			);

			$id            = $wpdb->insert_id; // Get new primary key value (id).
			$data_new      = [ 'name' => 'template_' . $id ];
			$data_previous = [ 'name' => 'temp' ];
			$wpdb->update( $table_name, $data_new, $data_previous );

			// Set new template name.
			$template = 'template_' . $id;

			$base_dir = self::alm_unlimited_get_repeater_path();
			self::alm_unlimited_mkdir( $base_dir );

			$f = $base_dir . '/' . $template . '.php';

			// phpcs:ignore
			$file = fopen( $f, 'w' ) or die( 'Error opening file' );
			// phpcs:ignore
			$w    = fwrite( $file, $default_value ) or die( 'Error writing file' );

			$return               = [];
			$return['id']         = $template;
			$return['alias']      = __( 'Template #', 'ajax-load-more-repeaters-v2' ) . '' . $count;
			$return['defaultVal'] = $default_value;

			wp_send_json( $return );
		}

		/**
		 * Delete a Repeater Template.
		 *
		 * @since 2.0
		 */
		public function alm_unlimited_delete() {
			$form_data = filter_input_array( INPUT_POST );

			if ( ! current_user_can( 'edit_theme_options' ) || ! isset( $form_data['nonce'] ) ) {
				// Bail early if missing WP capabilities or nonce.
				wp_die( esc_attr__( 'You don\'t belong here.', 'ajax-load-more-repeaters-v2' ) );
			}

			if ( ! wp_verify_nonce( $form_data['nonce'], 'alm_repeater_nonce' ) ) {
				// Verify nonce.
				wp_die( esc_attr__( 'Error - unable to verify nonce, please try again.', 'ajax-load-more-repeaters-v2' ) );
			}

			// Create table if it doesn't exist.
			alm_unlimited_check_table();

			global $wpdb;
			$table_name = $wpdb->prefix . 'alm_unlimited';

			$template = Trim( stripslashes( $form_data['repeater'] ) ); // Repeater name for deletion.

			$wpdb->delete( $table_name, [ 'name' => $template ] ); // Delete from db.

			// Get base directory.
			$base_dir = self::alm_unlimited_get_repeater_path();

			// Delete file from server.
			$file_delete = $base_dir . '/' . $template . '.php';
			if ( file_exists( $file_delete ) ) {
				unlink( $file_delete );
			}

			// See if repeater exists again to be sure it was removed.
			if ( file_exists( $file_delete ) ) {
				esc_html_e( 'Template could not be deleted.', 'ajax-load-more-repeaters-v2' );
			} else {
				esc_html_e( 'Template deleted successfully.', 'ajax-load-more-repeaters-v2' );
			}

			wp_die();
		}

		/**
		 * Create the Custom Repeaters settings panel.
		 *
		 * @since 2.4
		 */
		public function alm_unlimited_settings() {
			register_setting(
				'alm_unlimited_license',
				'alm_unlimited_license_key',
				'alm_unlimited_sanitize_license'
			);
		}
	}

	/**
	 * Sanitize the license activation
	 *
	 * @param string $new The new key.
	 * @since 2.4
	 */
	function alm_unlimited_sanitize_license( $new ) {
		$old = get_option( 'alm_unlimited_license_key' );
		if ( $old && $old !== $new ) {
			delete_option( 'alm_unlimited_license_status' ); // new license has been entered, so must reactivate.
		}
		return $new;
	}

	/**
	 * The main function responsible for returning Ajax Load More Unlimited Repeaters.
	 *
	 * @since 2.0
	 */
	function alm_unlimited_repeaters() {
		global $alm_unlimited_repeaters;
		if ( ! isset( $alm_unlimited_repeaters ) ) {
			$alm_unlimited_repeaters = new AjaxLoadMoreRepeaters();
		}
		return $alm_unlimited_repeaters;
	}
	alm_unlimited_repeaters();

endif;

/**
 * Software Licensing
 *
 * @since 1.0
 */
function alm_unlimited_plugin_updater() {
	if ( ! has_action( 'alm_pro_installed' ) && class_exists( 'EDD_SL_Plugin_Updater' ) ) { // Don't check for updates if Pro is activated.
		$license_key = trim( get_option( 'alm_unlimited_license_key' ) ); // Retrieve our license key from the DB.
		$edd_updater = new EDD_SL_Plugin_Updater(
			ALM_STORE_URL,
			__FILE__,
			[
				'version' => ALM_UNLIMITED_VERSION,
				'license' => $license_key,
				'item_id' => ALM_UNLIMITED_ITEM_NAME,
				'author'  => 'Darren Cooney',
			]
		);
	}
}
add_action( 'admin_init', 'alm_unlimited_plugin_updater', 0 );
/* End Software Licensing */
