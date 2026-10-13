<?php
/**
 * Theme setup.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme supports and menus.
 */
function tata_setup() {
	load_theme_textdomain( 'tata-surgical', TATA_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 188,
			'width'       => 562,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	// WooCommerce.
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 600,
			'single_image_width'    => 900,
			'product_grid'          => array(
				'default_columns' => 3,
				'min_columns'     => 2,
				'max_columns'     => 4,
			),
		)
	);
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	// Editor styles so the block editor looks like the site.
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );

	add_image_size( 'tata-card', 720, 480, true );

	register_nav_menus(
		array(
			'primary'         => __( 'Main menu (after Products)', 'tata-surgical' ),
			'footer_services' => __( 'Footer: Services', 'tata-surgical' ),
			'footer_company'  => __( 'Footer: Company', 'tata-surgical' ),
			'footer_legal'    => __( 'Footer: Legal', 'tata-surgical' ),
		)
	);
}
add_action( 'after_setup_theme', 'tata_setup' );

/**
 * Content width for embeds.
 */
function tata_content_width() {
	$GLOBALS['content_width'] = 820;
}
add_action( 'after_setup_theme', 'tata_content_width', 0 );

/**
 * Sidebars.
 */
function tata_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Blog sidebar', 'tata-surgical' ),
			'id'            => 'sidebar-blog',
			'description'   => __( 'Shown next to blog posts and archives.', 'tata-surgical' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
	register_sidebar(
		array(
			'name'          => __( 'Shop filters', 'tata-surgical' ),
			'id'            => 'sidebar-shop',
			'description'   => __( 'Shown below the category list on product pages. Add WooCommerce filter widgets here.', 'tata-surgical' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'tata_widgets_init' );

/**
 * Body classes.
 *
 * @param array $classes Classes.
 * @return array
 */
function tata_body_classes( $classes ) {
	if ( is_singular() && tata_is_elementor_page() ) {
		$classes[] = 'is-elementor-page';
	}
	return $classes;
}
add_filter( 'body_class', 'tata_body_classes' );

/**
 * Shorter excerpts.
 *
 * @return int
 */
function tata_excerpt_length() {
	return 26;
}
add_filter( 'excerpt_length', 'tata_excerpt_length' );

/**
 * Plain excerpt ending.
 *
 * @return string
 */
function tata_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'tata_excerpt_more' );

/**
 * Print the SVG icon sprite right after <body>.
 */
function tata_print_sprite() {
	get_template_part( 'template-parts/icons' );
}
add_action( 'wp_body_open', 'tata_print_sprite', 1 );

/**
 * On activation, WordPress maps the previous theme's widgets into this theme's
 * sidebars. Move them to "Inactive widgets" so the shop and blog start clean.
 * Nothing is deleted: they can be dragged back in Appearance > Widgets.
 */
function tata_reset_sidebars_on_activation() {
	$sidebars = get_option( 'sidebars_widgets', array() );
	if ( ! is_array( $sidebars ) ) {
		return;
	}
	$inactive = isset( $sidebars['wp_inactive_widgets'] ) ? (array) $sidebars['wp_inactive_widgets'] : array();
	foreach ( array( 'sidebar-blog', 'sidebar-shop' ) as $id ) {
		if ( ! empty( $sidebars[ $id ] ) ) {
			$inactive        = array_merge( $inactive, (array) $sidebars[ $id ] );
			$sidebars[ $id ] = array();
		}
	}
	$sidebars['wp_inactive_widgets'] = $inactive;
	update_option( 'sidebars_widgets', $sidebars );
}
add_action( 'after_switch_theme', 'tata_reset_sidebars_on_activation', 20 );
