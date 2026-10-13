<?php
/**
 * Page not found.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

get_header();
get_template_part( 'template-parts/page-header' );
?>
<div class="container page-body">
	<div class="empty-state">
		<span class="empty-state__ico"><?php tata_the_icon( 'i-search' ); ?></span>
		<h2><?php esc_html_e( 'This page has moved or no longer exists', 'tata-surgical' ); ?></h2>
		<p><?php esc_html_e( 'Search for a product by name or item code, or start from one of these pages.', 'tata-surgical' ); ?></p>
		<?php get_search_form(); ?>
		<div class="empty-state__actions">
			<a class="btn btn--primary" href="<?php echo esc_url( tata_products_url() ); ?>"><?php esc_html_e( 'View all products', 'tata-surgical' ); ?></a>
			<a class="btn btn--outline" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Go to homepage', 'tata-surgical' ); ?></a>
		</div>
	</div>
</div>
<?php
get_footer();
