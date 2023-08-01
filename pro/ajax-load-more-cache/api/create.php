<?php
/**
 * Cache API functions.
 *
 * @package ajax-load-more-cache
 * @version 1.0.
 */

/**
 * Custom API route for creating a cached file from params.
 *
 * @author ConnektMedia <support@connekthq.com>
 * @since 2.0
 */

add_action(
	'rest_api_init',
	function () {
		$my_namespace = 'ajax-load-more/cache';
		$my_endpoint  = '/create';
		register_rest_route(
			$my_namespace,
			$my_endpoint,
			[
				'methods'             => 'POST',
				'callback'            => 'alm_cache_create',
				'args'                => [],
				'permission_callback' => function () {
					return true;
				},
			]
		);
	}
);

/**
 * Create Cache from HTML data.
 *
 * @param WP_REST_Request $request Rest request object.
 */
function alm_cache_create( WP_REST_Request $request ) {
	$form_data = $request->get_params();

	// Pluck data from request.
	$html            = isset( $form_data['html'] ) ? trim( stripcslashes( $form_data['html'] ) ) : false;
	$cache_id        = isset( $form_data['cache_id'] ) ? $form_data['cache_id'] : '';
	$cache_logged_in = isset( $form_data['cache_logged_in'] ) ? $form_data['cache_logged_in'] : false;
	$do_create_cache = $cache_logged_in === 'true' && is_user_logged_in() ? false : true;
	$canonical_url   = isset( $form_data['canonical_url'] ) ? $form_data['canonical_url'] : $_SERVER['HTTP_REFERER'];
	$name            = isset( $form_data['name'] ) ? $form_data['name'] : 0;
	$postcount       = isset( $form_data['postcount'] ) ? $form_data['postcount'] : 1;
	$totalposts      = isset( $form_data['totalposts'] ) ? $form_data['totalposts'] : 1;

	if ( ! has_action( 'alm_cache_installed' ) || ! $do_create_cache ) {
		return false;
	}

	// Handle missing data.
	if ( ! $cache_id || ! $name || ! $html ) {
		return new WP_REST_Response(
			[
				'success' => false,
				'msg'     => __( 'An error has occurred while creating the Ajax Load More cache.', 'ajax-load-more-cache' ),
			],
			401
		);
	}

	// Create cache file.
	ALMCache::create_cache_file( $cache_id, $name, $canonical_url, $html, $postcount, $totalposts );

	// Send the response.
	return new WP_REST_Response(
		[
			'success' => true,
			'msg'     => __( 'Cache created successfully for:', 'ajax-load-more-cache' ) . ' ' . $name,
		],
		200
	);
}
