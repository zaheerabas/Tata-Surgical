<?php
/**
 * Product archive: shop page, categories and product search.
 *
 * Overrides woocommerce/templates/archive-product.php
 *
 * @package TataSurgical
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

do_action( 'woocommerce_before_main_content' );

$tata_term     = is_product_category() ? get_queried_object() : null;
$tata_children = array();

if ( $tata_term ) {
	$tata_children = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'parent'     => $tata_term->term_id,
			'hide_empty' => false,
			'menu_order' => 'asc',
		)
	);
}
?>

<header class="shop-head">
	<div class="shop-head__text">
		<?php if ( is_search() ) : ?>
			<h1 class="shop-head__title">
				<?php
				/* translators: %s: search term. */
				printf( esc_html__( 'Results for “%s”', 'tata-surgical' ), esc_html( get_search_query() ) );
				?>
			</h1>
		<?php else : ?>
			<h1 class="shop-head__title"><?php woocommerce_page_title(); ?></h1>
		<?php endif; ?>

		<?php
		$tata_desc = '';
		if ( $tata_term ) {
			$tata_desc = term_description( $tata_term->term_id, 'product_cat' );
		} elseif ( is_shop() && ! is_search() ) {
			$tata_shop = get_post( wc_get_page_id( 'shop' ) );
			$tata_desc = $tata_shop ? wpautop( $tata_shop->post_content ) : '';
		}
		if ( $tata_desc ) :
			?>
			<div class="shop-head__desc" data-collapse>
				<div class="shop-head__desc-inner"><?php echo wp_kses_post( $tata_desc ); ?></div>
				<button class="text-btn" type="button" data-collapse-toggle hidden><?php esc_html_e( 'Read more', 'tata-surgical' ); ?></button>
			</div>
		<?php endif; ?>
		<?php do_action( 'woocommerce_archive_description' ); ?>
	</div>
	<button class="shop-head__search" type="button" data-open-search>
		<?php tata_the_icon( 'i-search' ); ?><?php esc_html_e( 'Find by name or item code', 'tata-surgical' ); ?>
	</button>
</header>

<?php if ( $tata_children && ! is_wp_error( $tata_children ) ) : ?>
	<ul class="subcats" aria-label="<?php esc_attr_e( 'Subcategories', 'tata-surgical' ); ?>">
		<?php foreach ( $tata_children as $tata_child ) : ?>
			<li><a href="<?php echo esc_url( get_term_link( $tata_child ) ); ?>"><?php echo esc_html( $tata_child->name ); ?><?php if ( $tata_child->count ) : ?><span><?php echo (int) $tata_child->count; ?></span><?php endif; ?></a></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>

<div class="shop-backdrop" data-filter-backdrop hidden></div>
<div class="shop-layout">
	<aside class="shop-side" id="shop-side" aria-label="<?php esc_attr_e( 'Categories', 'tata-surgical' ); ?>">
		<div class="shop-side__head">
			<p class="shop-side__title"><?php esc_html_e( 'Categories', 'tata-surgical' ); ?></p>
			<button class="icon-btn" type="button" data-filter-close><?php tata_the_icon( 'i-close' ); ?><span class="sr"><?php esc_html_e( 'Close categories', 'tata-surgical' ); ?></span></button>
		</div>
		<?php tata_wc_category_nav(); ?>
		<div class="side-custom">
			<p class="side-custom__title"><?php esc_html_e( 'Not in our catalogue?', 'tata-surgical' ); ?></p>
			<p><?php esc_html_e( 'Send a drawing or sample and we make it to your specification.', 'tata-surgical' ); ?></p>
			<a class="btn btn--primary btn--block" href="<?php echo esc_url( tata_url( 'custom_url' ) ); ?>"><?php esc_html_e( 'Start a custom order', 'tata-surgical' ); ?></a>
		</div>
		<?php if ( is_active_sidebar( 'sidebar-shop' ) ) : ?>
			<div class="shop-widgets"><?php dynamic_sidebar( 'sidebar-shop' ); ?></div>
		<?php endif; ?>
	</aside>

	<div class="shop-main" data-layout="<?php echo esc_attr( tata_shop_layout() ); ?>">
		<?php
		if ( woocommerce_product_loop() ) {
			do_action( 'woocommerce_before_shop_loop' );

			woocommerce_product_loop_start();

			if ( wc_get_loop_prop( 'total' ) ) {
				while ( have_posts() ) {
					the_post();
					do_action( 'woocommerce_shop_loop' );
					wc_get_template_part( 'content', 'product' );
				}
			}

			woocommerce_product_loop_end();

			do_action( 'woocommerce_after_shop_loop' );
		} else {
			?>
			<div class="empty-state">
				<span class="empty-state__ico"><?php tata_the_icon( 'i-search' ); ?></span>
				<h2><?php esc_html_e( 'No products found', 'tata-surgical' ); ?></h2>
				<p><?php esc_html_e( 'We may still be able to make it. Send us a drawing, photo or description and we will quote.', 'tata-surgical' ); ?></p>
				<div class="empty-state__actions">
					<a class="btn btn--primary" href="<?php echo esc_url( tata_url( 'custom_url' ) ); ?>"><?php esc_html_e( 'Send a drawing', 'tata-surgical' ); ?></a>
					<a class="btn btn--outline" href="<?php echo esc_url( tata_products_url() ); ?>"><?php esc_html_e( 'View all products', 'tata-surgical' ); ?></a>
				</div>
			</div>
			<?php
		}
		?>
	</div>
</div>

<?php
do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );
