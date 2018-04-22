<?php
/**
 * Class file for the Last Updates tools.
 *
 * @package forkriver\api-tools\last-updates
 */

/**
 * Last Updates class.
 *
 * @since 1.0.0
 */
class FR_API_Last_Updates {

	const PREFIX = '_fr_api_';

	const API_NAMESPACE = 'forkriver/v1';

	/**
	 * Class constructor.
	 *
	 * @return void
	 * @since  1.0.0
	 */
	function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_last_update_routes' ) );
	}

	/**
	 * Registers the 'last_update' routes.
	 *
	 * @return void
	 * @since  1.0.0
	 */
	function register_last_update_routes() {
		$args = array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_last_updates' ),
		);
		register_rest_route( FR_API_Last_Updates::API_NAMESPACE, '/last_update', $args );
		register_rest_route( FR_API_Last_Updates::API_NAMESPACE, '/last_update/(?P<post_type>[a-z0-9_]+)', $args );
	}

	/**
	 * Gets the last update(s).
	 *
	 * @param  WP_REST_Request $request The REST request.
	 * @return array                    The last updated date(s) for the post type(s), in UNIX Epoch format.
	 * @since  1.0.0
	 */
	function get_last_updates( $request ) {
		$last_updates = array();
		$requested_post_type = (string) $request['post_type'];
		if ( '' !== $requested_post_type ) {
			$post_types = array( $requested_post_type );
		} else {
			$post_types = get_post_types();
		}
		if ( empty( $post_types ) ) {
			return $last_updates;
		}
		foreach ( $post_types as $post_type ) {
			$args = array(
				'posts_per_page' => 1,
				'post_type'      => $post_type,
				'orderby'        => 'modified',
				'order'          => 'DESC',
			);
			$posts = get_posts( $args );
			if ( ! empty( $posts ) ) {
				$last_updates[ $post_type ] = array( 'last_update' => strtotime( $posts[0]->post_modified_gmt ), 'post' => $posts[0] );
			}
		}
		return $last_updates;
	}
}

new FR_API_Last_Updates;
