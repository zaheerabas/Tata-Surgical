<?php
/**
 * Styles and scripts.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue front-end assets.
 */
function tata_assets() {
	wp_enqueue_style(
		'tata-fonts',
		'https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'tata-main', TATA_URI . '/assets/css/main.css', array( 'tata-fonts' ), TATA_VERSION );

	if ( tata_has_wc() ) {
		wp_enqueue_style( 'tata-woocommerce', TATA_URI . '/assets/css/woocommerce.css', array( 'tata-main' ), TATA_VERSION );
	}

	wp_enqueue_script( 'tata-main', TATA_URI . '/assets/js/main.js', array(), TATA_VERSION, array( 'strategy' => 'defer', 'in_footer' => true ) );
	wp_localize_script(
		'tata-main',
		'tataData',
		array(
			'searchUrl'   => esc_url_raw( rest_url( 'tata/v1/search' ) ),
			'searchPage'  => esc_url_raw( home_url( '/' ) ),
			'hasWc'       => tata_has_wc(),
			'quoteUrl'    => esc_url_raw( tata_url( 'quote_url' ) ),
			'customUrl'   => esc_url_raw( tata_url( 'custom_url' ) ),
			'whatsapp'    => preg_replace( '/\D/', '', tata_opt( 'whatsapp' ) ),
			'i18n'        => array(
				'openMenu'      => __( 'Open menu', 'tata-surgical' ),
				'closeMenu'     => __( 'Close menu', 'tata-surgical' ),
				'submenu'       => __( 'Show submenu for', 'tata-surgical' ),
				'addToQuote'    => __( 'Add to quote', 'tata-surgical' ),
				'added'         => __( 'Added', 'tata-surgical' ),
				'itemCode'      => __( 'Item code', 'tata-surgical' ),
				'results'       => __( '%d results', 'tata-surgical' ),
				'oneResult'     => __( '1 result', 'tata-surgical' ),
				'searching'     => __( 'Searching…', 'tata-surgical' ),
				'noResults'     => __( 'No product matches “%s”. We can still make it:', 'tata-surgical' ),
				'sendDrawing'   => __( 'send us a drawing or description', 'tata-surgical' ),
				'viewAll'       => __( 'View all results', 'tata-surgical' ),
				'emptyQuote'    => __( 'Your quote list is empty. Search for a product and choose “Add to quote”.', 'tata-surgical' ),
				'remove'        => __( 'Remove', 'tata-surgical' ),
				'qtyFor'        => __( 'Quantity for %s', 'tata-surgical' ),
				'decrease'      => __( 'Decrease quantity', 'tata-surgical' ),
				'increase'      => __( 'Increase quantity', 'tata-surgical' ),
				'pcs'           => __( 'pcs', 'tata-surgical' ),
				'waQuoteIntro'  => __( 'Hello Tata Surgical, please quote for:', 'tata-surgical' ),
				'listEmptyForm' => __( 'No products added yet. You can still describe what you need below.', 'tata-surgical' ),
			),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'tata_assets' );

/**
 * Preconnect to Google Fonts.
 *
 * @param array  $urls          URLs.
 * @param string $relation_type Relation.
 * @return array
 */
function tata_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'tata_resource_hints', 10, 2 );

/**
 * WooCommerce: keep the layout stylesheet, drop the visual ones (the theme styles them).
 *
 * @param array $styles Styles.
 * @return array
 */
function tata_wc_styles( $styles ) {
	unset( $styles['woocommerce-general'], $styles['woocommerce-smallscreen'] );
	return $styles;
}
add_filter( 'woocommerce_enqueue_styles', 'tata_wc_styles' );

/**
 * Enable reveal animations only when JavaScript runs. If the theme script
 * fails to load, content is shown anyway after a short delay.
 */
function tata_reveal_flag() {
	echo "<script>document.documentElement.classList.add('js-reveal');setTimeout(function(){if(!window.tataRevealReady){document.documentElement.classList.remove('js-reveal');}},2500);</script>\n";
}
add_action( 'wp_head', 'tata_reveal_flag', 1 );
