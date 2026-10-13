<?php
/**
 * Tata Surgical theme bootstrap.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

define( 'TATA_VERSION', '2.0.0' );
define( 'TATA_DIR', get_template_directory() );
define( 'TATA_URI', get_template_directory_uri() );

$tata_includes = array(
	'helpers',        // Options, icons, small utilities.
	'setup',          // Theme supports, menus, sidebars.
	'assets',         // CSS and JS.
	'customizer',     // Appearance > Customize > Tata Surgical.
	'menus',          // Products mega menu, dropdown walker, fallbacks.
	'term-meta',      // Icon and short text for product categories.
	'enquiries',      // Quote and custom-order forms, newsletter.
	'rest',           // Live search endpoint.
	'seo',            // Yoast SEO compatibility, breadcrumbs.
	'elementor',      // Elementor and Elementor Pro compatibility.
	'site-setup',     // Creates pages and menus on first activation.
);

foreach ( $tata_includes as $tata_file ) {
	require TATA_DIR . '/inc/' . $tata_file . '.php';
}

if ( class_exists( 'WooCommerce' ) ) {
	require TATA_DIR . '/inc/woocommerce.php';
}
