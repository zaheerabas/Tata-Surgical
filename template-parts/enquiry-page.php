<?php
/**
 * Shared layout for the Request a quote, Custom manufacturing and Contact templates.
 *
 * Expects $args: type (quote|custom|contact), kicker, title, intro, steps (array of [icon, label]).
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

$tata_type  = isset( $args['type'] ) ? $args['type'] : 'quote';
$tata_steps = isset( $args['steps'] ) ? $args['steps'] : array();

get_header();

while ( have_posts() ) :
	the_post();
	$tata_intro = has_excerpt() ? get_the_excerpt() : $args['intro'];
	?>
	<header class="page-hero page-hero--enquiry">
		<div class="container page-hero__grid">
			<div data-reveal>
				<?php tata_breadcrumbs(); ?>
				<p class="page-hero__kicker"><?php echo esc_html( $args['kicker'] ); ?></p>
				<h1 class="page-hero__title"><?php the_title(); ?></h1>
				<p class="page-hero__intro"><?php echo esc_html( $tata_intro ); ?></p>
			</div>
			<?php if ( $tata_steps ) : ?>
				<ol class="hero-steps" data-reveal style="--d:120ms">
					<?php foreach ( $tata_steps as $tata_i => $tata_step ) : ?>
						<li style="--i:<?php echo (int) $tata_i; ?>">
							<span class="hero-steps__ico"><?php tata_the_icon( $tata_step[0] ); ?></span>
							<span class="hero-steps__text"><b><?php echo esc_html( $tata_step[1] ); ?></b><?php echo esc_html( $tata_step[2] ); ?></span>
						</li>
					<?php endforeach; ?>
				</ol>
			<?php endif; ?>
		</div>
	</header>

	<div class="container page-body">
		<?php if ( trim( get_the_content() ) && false === strpos( get_the_content(), '[tata_enquiry_form' ) ) : ?>
			<div class="entry enquiry-intro" data-reveal><?php the_content(); ?></div>
		<?php endif; ?>
		<div data-reveal style="--d:80ms">
			<?php echo tata_enquiry_form_shortcode( array( 'type' => $tata_type ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside. ?>
		</div>
	</div>
	<?php
endwhile;

get_footer();
