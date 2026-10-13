<?php
/**
 * Homepage: shows the page set as "Homepage" in Settings > Reading,
 * full width, so it can be designed with Elementor.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

if ( 'page' !== get_option( 'show_on_front' ) ) {
	require get_index_template();
	return;
}

get_header();

while ( have_posts() ) :
	the_post();
	if ( tata_is_elementor_page() ) {
		the_content();
	} else {
		?>
		<div class="container page-body">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--wide' ); ?>><?php the_content(); ?></article>
		</div>
		<?php
	}
endwhile;

get_footer();
