<?php
/**
 * Page header with breadcrumbs, title and optional intro.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

$tata_title = isset( $args['title'] ) ? $args['title'] : '';
$tata_intro = isset( $args['intro'] ) ? $args['intro'] : '';

if ( ! $tata_title ) {
	if ( is_singular() ) {
		$tata_title = get_the_title();
	} elseif ( is_search() ) {
		/* translators: %s: search term. */
		$tata_title = sprintf( __( 'Results for “%s”', 'tata-surgical' ), get_search_query() );
	} elseif ( is_404() ) {
		$tata_title = __( 'Page not found', 'tata-surgical' );
	} elseif ( is_home() ) {
		$tata_title = get_option( 'page_for_posts' ) ? get_the_title( get_option( 'page_for_posts' ) ) : __( 'News', 'tata-surgical' );
	} else {
		$tata_title = wp_strip_all_tags( get_the_archive_title() );
	}
}

if ( ! $tata_intro && is_page() && has_excerpt() ) {
	$tata_intro = get_the_excerpt();
}
if ( ! $tata_intro && is_archive() ) {
	$tata_intro = wp_strip_all_tags( get_the_archive_description() );
}
?>
<header class="page-hero">
	<div class="container">
		<?php tata_breadcrumbs(); ?>
		<h1 class="page-hero__title"><?php echo esc_html( $tata_title ); ?></h1>
		<?php if ( $tata_intro ) : ?>
			<p class="page-hero__intro"><?php echo esc_html( $tata_intro ); ?></p>
		<?php endif; ?>
		<?php if ( is_singular( 'post' ) ) : ?>
			<p class="page-hero__meta">
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
				<?php
				$tata_cats = get_the_category_list( ', ' );
				if ( $tata_cats ) {
					echo '<span class="sep" aria-hidden="true">/</span>' . wp_kses_post( $tata_cats );
				}
				?>
			</p>
		<?php endif; ?>
	</div>
</header>
