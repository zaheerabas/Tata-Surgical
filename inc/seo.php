<?php
/**
 * SEO: Yoast SEO compatibility and breadcrumbs.
 * Yoast handles titles, meta descriptions, Open Graph, schema and sitemaps.
 * The theme only prints clean, semantic markup and breadcrumbs.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

/**
 * Tell Yoast the theme supports its breadcrumbs.
 */
function tata_seo_setup() {
	add_theme_support( 'yoast-seo-breadcrumbs' );
}
add_action( 'after_setup_theme', 'tata_seo_setup' );

/**
 * Print breadcrumbs: Yoast if enabled, otherwise WooCommerce or a simple trail.
 */
function tata_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}

	if ( function_exists( 'yoast_breadcrumb' ) ) {
		$options = get_option( 'wpseo_titles' );
		if ( ! empty( $options['breadcrumbs-enable'] ) ) {
			yoast_breadcrumb( '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'tata-surgical' ) . '">', '</nav>' );
			return;
		}
	}

	if ( function_exists( 'woocommerce_breadcrumb' ) && function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
		woocommerce_breadcrumb(
			array(
				'wrap_before' => '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'tata-surgical' ) . '">',
				'wrap_after'  => '</nav>',
				'delimiter'   => '<span class="sep" aria-hidden="true">/</span>',
				'home'        => __( 'Home', 'tata-surgical' ),
			)
		);
		return;
	}

	$trail = array( array( home_url( '/' ), __( 'Home', 'tata-surgical' ) ) );

	if ( is_singular( 'post' ) ) {
		$blog = get_option( 'page_for_posts' );
		if ( $blog ) {
			$trail[] = array( get_permalink( $blog ), get_the_title( $blog ) );
		}
		$trail[] = array( '', get_the_title() );
	} elseif ( is_page() ) {
		foreach ( array_reverse( get_post_ancestors( get_the_ID() ) ) as $ancestor ) {
			$trail[] = array( get_permalink( $ancestor ), get_the_title( $ancestor ) );
		}
		$trail[] = array( '', get_the_title() );
	} elseif ( is_search() ) {
		$trail[] = array( '', __( 'Search results', 'tata-surgical' ) );
	} elseif ( is_archive() ) {
		$trail[] = array( '', wp_strip_all_tags( get_the_archive_title() ) );
	} elseif ( is_home() ) {
		$trail[] = array( '', __( 'News', 'tata-surgical' ) );
	} elseif ( is_404() ) {
		$trail[] = array( '', __( 'Page not found', 'tata-surgical' ) );
	}

	echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'tata-surgical' ) . '">';
	$last = count( $trail ) - 1;
	foreach ( $trail as $i => $crumb ) {
		if ( $i > 0 ) {
			echo '<span class="sep" aria-hidden="true">/</span>';
		}
		if ( $crumb[0] && $i !== $last ) {
			printf( '<a href="%s">%s</a>', esc_url( $crumb[0] ), esc_html( $crumb[1] ) );
		} else {
			printf( '<span aria-current="page">%s</span>', esc_html( $crumb[1] ) );
		}
	}
	echo '</nav>';
}

/**
 * Cleaner archive titles ("Dental instruments" rather than "Category: Dental instruments").
 *
 * @param string $title Title.
 * @return string
 */
function tata_archive_title( $title ) {
	if ( is_category() || is_tag() || is_tax() ) {
		return single_term_title( '', false );
	}
	if ( is_post_type_archive() ) {
		return post_type_archive_title( '', false );
	}
	return $title;
}
add_filter( 'get_the_archive_title', 'tata_archive_title' );
