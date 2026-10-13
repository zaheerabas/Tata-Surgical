<?php
/**
 * Product card in loops.
 *
 * Overrides woocommerce/templates/content-product.php
 *
 * @package TataSurgical
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}

$tata_link = $product->get_permalink();
$tata_cats = wc_get_product_terms( $product->get_id(), 'product_cat', array( 'fields' => 'names' ) );
?>
<li <?php wc_product_class( 'p-card', $product ); ?>>
	<a class="p-card__media" href="<?php echo esc_url( $tata_link ); ?>" tabindex="-1" aria-hidden="true">
		<?php
		if ( $product->get_image_id() ) {
			echo wp_get_attachment_image( $product->get_image_id(), 'woocommerce_thumbnail', false, array( 'loading' => 'lazy' ) );
		} else {
			echo wc_placeholder_img( 'woocommerce_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		?>
	</a>
	<div class="p-card__body">
		<?php if ( $tata_cats ) : ?>
			<p class="p-card__cat"><?php echo esc_html( $tata_cats[0] ); ?></p>
		<?php endif; ?>
		<h2 class="woocommerce-loop-product__title p-card__title"><a href="<?php echo esc_url( $tata_link ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h2>
		<?php if ( $product->get_sku() ) : ?>
			<p class="p-card__code"><?php esc_html_e( 'Item code', 'tata-surgical' ); ?> <b class="notranslate"><?php echo esc_html( $product->get_sku() ); ?></b></p>
		<?php endif; ?>
		<?php
		$tata_excerpt = $product->get_short_description() ? $product->get_short_description() : $product->get_description();
		if ( $tata_excerpt ) :
			?>
			<p class="p-card__excerpt"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $tata_excerpt ), 24 ) ); ?></p>
		<?php endif; ?>
		<div class="p-card__actions">
			<a class="btn btn--outline btn--sm" href="<?php echo esc_url( $tata_link ); ?>"><?php esc_html_e( 'Details', 'tata-surgical' ); ?></a>
			<button class="btn btn--primary btn--sm" type="button" <?php echo tata_quote_attrs( $product ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside. ?>>
				<?php tata_the_icon( 'i-quote' ); ?><span data-label><?php esc_html_e( 'Add to quote', 'tata-surgical' ); ?></span>
			</button>
		</div>
	</div>
</li>
