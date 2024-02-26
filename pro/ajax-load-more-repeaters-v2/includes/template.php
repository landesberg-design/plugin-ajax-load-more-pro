<?php
/**
 * Template file used to create a new Repeater Template.
 *
 * @package AjaxLoadMoreRepeaters
 * @since   2.0.0
 */

$alm_form_data = filter_input_array( INPUT_POST );
$alm_parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once $alm_parse_uri[0] . 'wp-load.php';
$alm_alias         = $alm_form_data['alias'];
$alm_id            = $alm_form_data['id'];
$alm_default_value = nl2br( $alm_form_data['defaultVal'] );
?>
<div class="unlimited-wrap">
	<h3 class="heading" data-default="<?php echo esc_attr( $alm_alias ); ?>"><?php echo esc_attr( $alm_alias ); ?></h3>
	<div class="expand-wrap">
		<div class="wrap repeater-wrap" data-name="<?php echo esc_attr( $alm_id ); ?>" data-type="unlimited">
			<div class="alm-row">
				<div class="column column-6">
					<label class="template-title has-margin-btm" for="alias-<?php echo esc_attr( $alm_id ); ?>">
						<?php esc_attr_e( 'Template Alias:', 'ajax-load-more-repeaters-v2' ); ?>
						<span><?php esc_attr_e( 'Enter a unique name for this template.', 'ajax-load-more-repeaters-v2' ); ?></span>
					</label>
					<?php echo '<input type="text" id="alias-' . esc_attr( $alm_id ) . '" class="_alm_repeater_alias" value="' . esc_attr( $alm_alias ) . '" maxlength="55">'; ?>
				</div>
				<div class="column column-6">
					<label class="template-title has-margin-btm" for="id-<?php echo esc_attr( $alm_id ); ?>">
						<?php esc_attr_e( 'Template ID:', 'ajax-load-more-repeaters-v2' ); ?>
						<span><?php esc_attr_e( 'The unique ID assigned to this template.', 'ajax-load-more-repeaters-v2' ); ?></span>
					</label>
					<input type="text" class="disabled-input" id="id-<?php echo esc_attr( $alm_id ); ?>" value="<?php echo esc_attr( $alm_id ); ?>" readonly="readonly">
				</div>
			</div>
			<div class="alm-row no-padding-btm">
				<div class="column column-9">
					<label class="template-title has-margin-btm trigger-codemirror" data-id="<?php echo esc_attr( $alm_id ); ?>" for="template-<?php echo esc_attr( $alm_id ); ?>">
						<?php esc_attr_e( 'Template Code:', 'ajax-load-more-repeaters-v2' ); ?>
						<span><?php esc_attr_e( 'Enter the PHP and HTML markup for this template.', 'ajax-load-more-repeaters-v2' ); ?></span>
					</label>
				</div>
				<div class="column column-3">
					<?php require ALM_PATH . 'admin/includes/components/layout-list.php'; ?>
				</div>
			</div>
			<div class="alm-row">
				<div class="column textarea-wrap">
					<textarea rows="10" id="<?php echo esc_attr( $alm_id ); ?>" class="_alm_repeater"><?php echo esc_attr( $alm_default_value ); ?></textarea>
					<script>
					var editor_<?php echo esc_attr( $alm_id ); ?> = CodeMirror.fromTextArea(document.getElementById("<?php echo esc_attr( $alm_id ); ?>"), {
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
			</div>
			<div class="alm-row">
				<div class="column">				
					<input type="submit" value="<?php esc_attr_e( 'Save Template', 'ajax-load-more-repeaters-v2' ); ?>" class="button button-primary save-repeater" data-editor-id="<?php echo esc_attr( $alm_id ); ?>">
					<div class="saved-response">&nbsp;</div>
					<button type="button" class="alm-delete">
						<?php esc_attr_e( 'Delete', 'ajax-load-more-repeaters-v2' ); ?>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
