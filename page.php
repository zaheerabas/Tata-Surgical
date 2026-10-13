<?php
/**
 * Pages. Pages built with Elementor are shown full width without the theme title.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	if ( tata_is_elementor_page() ) :
		the_content();
	else :
		get_template_part( 'template-parts/page-header' );
		?>
		<div class="container page-body">
			<article id="post-<?php the_ID(); ?>" <?php post_class( has_shortcode( get_post()->post_content, 'tata_enquiry_form' ) ? 'entry entry--wide' : 'entry' ); ?>>
				<?php
				the_content();
				wp_link_pages();
				?>
			</article>
			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</div>
		<?php
	endif;
endwhile;

get_footer();
