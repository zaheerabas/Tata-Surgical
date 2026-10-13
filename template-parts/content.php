<?php
/**
 * Post card in lists.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
	<a class="post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'tata-card', array( 'loading' => 'lazy' ) ); ?>
		<?php else : ?>
			<span class="post-card__placeholder"><?php tata_the_icon( 'i-news' ); ?></span>
		<?php endif; ?>
	</a>
	<div class="post-card__body">
		<p class="post-card__meta">
			<?php if ( 'post' === get_post_type() ) : ?>
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
			<?php else : ?>
				<?php echo esc_html( get_post_type_object( get_post_type() )->labels->singular_name ); ?>
			<?php endif; ?>
		</p>
		<h2 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p class="post-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
	</div>
</article>
