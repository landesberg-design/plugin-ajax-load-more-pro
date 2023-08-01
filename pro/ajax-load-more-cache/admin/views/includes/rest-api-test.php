<?php
/**
 * Upgrade notification display.
 *
 * @package ajax-load-more-cache
 */

?>
<div style="padding: 10px 20px 0; display: none;" id="alm-cache-rest-api-test">
	<div class="notice error instant-images-err-notice inline" style="margin-bottom: 0;">
		<p><?php echo wp_kses_post( __( 'The Cache add-on is unable to access the WordPress REST API. The REST API is active by default, however sometimes theme functions or a security plugin may block access.', 'ajax-load-more-cache' ) ); ?></p>
	</div>
</div>
