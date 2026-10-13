<?php
/**
 * Elementor and Elementor Pro compatibility.
 *
 * - Pages built with Elementor are shown full width, without the theme's page title.
 * - Elementor Pro Theme Builder can replace the header, footer, single and archive.
 * - The theme's fonts and colours are used by Elementor by default.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether a post was built with Elementor.
 *
 * @param int|null $post_id Post ID.
 * @return bool
 */
function tata_is_elementor_page( $post_id = null ) {
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return false;
	}
	$post_id = $post_id ? $post_id : get_the_ID();
	if ( ! $post_id ) {
		return false;
	}
	$document = \Elementor\Plugin::$instance->documents->get( $post_id );
	return $document && $document->is_built_with_elementor();
}

/**
 * Register Theme Builder locations (header, footer, single, archive).
 *
 * @param \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $manager Manager.
 */
function tata_elementor_locations( $manager ) {
	$manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'tata_elementor_locations' );

/**
 * Run an Elementor Pro location if one is assigned.
 *
 * @param string $location header|footer|single|archive.
 * @return bool True if Elementor printed the location.
 */
function tata_elementor_location( $location ) {
	return function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( $location );
}

/**
 * On theme activation, let Elementor inherit the theme's fonts and colours.
 */
function tata_elementor_defaults() {
	if ( ! did_action( 'elementor/loaded' ) ) {
		return;
	}
	update_option( 'elementor_disable_color_schemes', 'yes' );
	update_option( 'elementor_disable_typography_schemes', 'yes' );
	if ( ! get_option( 'elementor_container_width' ) ) {
		update_option( 'elementor_container_width', 1280 );
	}
}
add_action( 'after_switch_theme', 'tata_elementor_defaults' );

/**
 * Add the theme's brand colours to the editor colour picker.
 */
function tata_editor_palette() {
	add_theme_support(
		'editor-color-palette',
		array(
			array( 'name' => __( 'Tata navy', 'tata-surgical' ), 'slug' => 'brand', 'color' => '#243565' ),
			array( 'name' => __( 'Deep navy', 'tata-surgical' ), 'slug' => 'navy', 'color' => '#1a2547' ),
			array( 'name' => __( 'Charcoal', 'tata-surgical' ), 'slug' => 'ink', 'color' => '#2c2d2d' ),
			array( 'name' => __( 'Light navy', 'tata-surgical' ), 'slug' => 'brand-tint', 'color' => '#eaeef6' ),
			array( 'name' => __( 'Mist', 'tata-surgical' ), 'slug' => 'mist', 'color' => '#f4f6f9' ),
			array( 'name' => __( 'White', 'tata-surgical' ), 'slug' => 'white', 'color' => '#ffffff' ),
		)
	);
}
add_action( 'after_setup_theme', 'tata_editor_palette' );
