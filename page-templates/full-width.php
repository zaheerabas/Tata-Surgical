<?php
/**
 * Template Name: Full width (no title)
 * Template Post Type: page, post
 *
 * For pages laid out with Elementor or blocks: header and footer, nothing else.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	the_content();
endwhile;

get_footer();
