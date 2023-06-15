<?php
/**
 * Cache API functions.
 *
 * @package ajax-load-more-cache
 * @version 2.0
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
		$my_endpoint  = '/get';
		register_rest_route(
			$my_namespace,
			$my_endpoint,
			[
				'methods'             => 'GET',
				'callback'            => 'alm_cache_get',
				'args'                => [],
				'permission_callback' => function () {
					return true;
				},
			]
		);
	}
);

/**
 * Get cached data from .json file.
 *
 * @param WP_REST_Request $request Rest request object.
 */
function alm_cache_get( WP_REST_Request $request ) {
	$cache_id   = $request->get_param( 'id' );
	$cache_slug = $request->get_param( 'name' );

	if ( ! $cache_id || ! $cache_slug ) {
		return false;
	}

	return json_decode( ALMCache::get_cache_file( $cache_id, $cache_slug ) );
}
