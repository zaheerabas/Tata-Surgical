<?php
/**
 * Nothing found.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="empty-state">
	<span class="empty-state__ico"><?php tata_the_icon( 'i-search' ); ?></span>
	<h2><?php esc_html_e( 'Nothing found', 'tata-surgical' ); ?></h2>
	<p><?php esc_html_e( 'Try a product name or an item code, or browse the product categories.', 'tata-surgical' ); ?></p>
	<?php get_search_form(); ?>
	<div class="empty-state__actions">
		<a class="btn btn--outline" href="<?php echo esc_url( tata_products_url() ); ?>"><?php esc_html_e( 'View all products', 'tata-surgical' ); ?></a>
		<a class="btn btn--primary" href="<?php echo esc_url( tata_url( 'contact_url' ) ); ?>"><?php esc_html_e( 'Contact us', 'tata-surgical' ); ?></a>
	</div>
</div>
