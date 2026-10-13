<?php
/**
 * Single post.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	if ( tata_elementor_location( 'single' ) ) {
		continue;
	}

	if ( tata_is_elementor_page() ) {
		the_content();
		continue;
	}

	get_template_part( 'template-parts/page-header' );
	?>
	<div class="container page-body">
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--post' ); ?>>
			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="entry__media"><?php the_post_thumbnail( 'large' ); ?></figure>
			<?php endif; ?>
			<?php
			the_content();
			wp_link_pages();
			the_tags( '<p class="entry__tags">', '', '</p>' );
			?>
		</article>
		<?php
		the_post_navigation(
			array(
				'prev_text' => '<span class="nav-label">' . esc_html__( 'Previous', 'tata-surgical' ) . '</span>%title',
				'next_text' => '<span class="nav-label">' . esc_html__( 'Next', 'tata-surgical' ) . '</span>%title',
			)
		);
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>
	</div>
	<?php
endwhile;

get_footer();
