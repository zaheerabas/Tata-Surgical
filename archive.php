<?php
/**
 * Archives: categories, tags, dates, authors.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

get_header();
get_template_part( 'template-parts/page-header' );
?>
<div class="container page-body">
	<?php if ( have_posts() ) : ?>
		<div class="post-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content' );
			endwhile;
			?>
		</div>
		<?php
		the_posts_pagination(
			array(
				'prev_text' => __( 'Previous', 'tata-surgical' ),
				'next_text' => __( 'Next', 'tata-surgical' ),
			)
		);
		?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content-none' ); ?>
	<?php endif; ?>
</div>
<?php
get_footer();
