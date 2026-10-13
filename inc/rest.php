<?php
/**
 * Live search endpoint: /wp-json/tata/v1/search?q=TADB 7688
 * Matches product names and item codes (SKU).
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

/**
 * Product IDs whose SKU contains the term (variations map to their parent).
 *
 * @param string $term Search term.
 * @param int    $limit Max IDs.
 * @return int[]
 */
function tata_sku_matches( $term, $limit = 50 ) {
	global $wpdb;

	$term = trim( $term );
	if ( '' === $term ) {
		return array();
	}

	// Also match codes typed without the space, e.g. "TADB7688".
	$compact = str_replace( ' ', '', $term );

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery
	$ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT DISTINCT IF(p.post_type = 'product_variation', p.post_parent, p.ID)
			 FROM {$wpdb->postmeta} pm
			 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
			 WHERE pm.meta_key = '_sku'
			   AND p.post_status = 'publish'
			   AND ( pm.meta_value LIKE %s OR REPLACE(pm.meta_value, ' ', '') LIKE %s )
			 LIMIT %d",
			'%' . $wpdb->esc_like( $term ) . '%',
			'%' . $wpdb->esc_like( $compact ) . '%',
			$limit
		)
	);

	return array_values( array_filter( array_map( 'absint', (array) $ids ) ) );
}

/**
 * Register the route.
 */
function tata_register_rest() {
	register_rest_route(
		'tata/v1',
		'/search',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'permission_callback' => '__return_true',
			'args'                => array(
				'q' => array(
					'type'              => 'string',
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
			'callback'            => 'tata_rest_search',
		)
	);
}
add_action( 'rest_api_init', 'tata_register_rest' );

/**
 * Search callback.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function tata_rest_search( $request ) {
	$q = trim( (string) $request->get_param( 'q' ) );

	if ( mb_strlen( $q ) < 2 || ! tata_has_wc() ) {
		return rest_ensure_response( array( 'items' => array(), 'total' => 0 ) );
	}

	$sku_ids = tata_sku_matches( $q, 20 );

	$query = new WP_Query(
		array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			's'              => $q,
			'posts_per_page' => 20,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'tata_live'      => true,
		)
	);

	$ids   = array_values( array_unique( array_merge( $sku_ids, array_map( 'absint', $query->posts ) ) ) );
	$total = count( $ids );
	$items = array();

	foreach ( array_slice( $ids, 0, 8 ) as $id ) {
		$product = wc_get_product( $id );
		if ( ! $product || 'publish' !== $product->get_status() ) {
			continue;
		}
		$cats    = wc_get_product_terms( $id, 'product_cat', array( 'fields' => 'names' ) );
		$items[] = array(
			'id'    => $id,
			'name'  => wp_strip_all_tags( $product->get_name() ),
			'sku'   => $product->get_sku(),
			'cat'   => $cats ? $cats[0] : '',
			'url'   => $product->get_permalink(),
			'image' => wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' ),
		);
	}

	$response = rest_ensure_response(
		array(
			'items'  => $items,
			'total'  => $total,
			'allUrl' => add_query_arg(
				array(
					's'         => $q,
					'post_type' => 'product',
				),
				home_url( '/' )
			),
		)
	);
	$response->header( 'Cache-Control', 'public, max-age=300' );
	return $response;
}
