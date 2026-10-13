<?php
/**
 * WooCommerce integration.
 * Catalogue mode: no prices, no cart, no checkout. Buyers build a quote list instead.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

/* --------------------------------------------------------------------------
 * Catalogue mode
 * ------------------------------------------------------------------------ */

add_filter( 'woocommerce_is_purchasable', '__return_false' );
add_filter( 'woocommerce_get_price_html', '__return_empty_string' );
add_filter( 'woocommerce_variable_price_html', '__return_empty_string' );

/**
 * Remove price-based sorting.
 *
 * @param array $options Options.
 * @return array
 */
function tata_wc_orderby( $options ) {
	unset( $options['price'], $options['price-desc'] );
	return $options;
}
add_filter( 'woocommerce_catalog_orderby', 'tata_wc_orderby' );
add_filter( 'woocommerce_default_catalog_orderby_options', 'tata_wc_orderby' );

/**
 * Send visitors who reach the cart or checkout to the quote page.
 */
function tata_wc_redirect_cart() {
	if ( ( function_exists( 'is_cart' ) && is_cart() ) || ( function_exists( 'is_checkout' ) && is_checkout() && ! is_wc_endpoint_url() ) ) {
		wp_safe_redirect( tata_url( 'quote_url' ) );
		exit;
	}
}
add_action( 'template_redirect', 'tata_wc_redirect_cart' );

/**
 * "Show" options on shop pages.
 *
 * @return int[]
 */
function tata_per_page_options() {
	return apply_filters( 'tata_per_page_options', array( 12, 24, 36, 48 ) );
}

/**
 * Products per page: ?per_page=36 in the address, else the visitor's last choice, else 24.
 *
 * @return int
 */
function tata_wc_per_page() {
	$options = tata_per_page_options();
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only display preference.
	if ( isset( $_GET['per_page'] ) && in_array( absint( $_GET['per_page'] ), $options, true ) ) {
		return absint( $_GET['per_page'] );
	}
	// phpcs:enable
	$cookie = isset( $_COOKIE['tata_per_page'] ) ? absint( $_COOKIE['tata_per_page'] ) : 0;
	return in_array( $cookie, $options, true ) ? $cookie : 24;
}
add_filter( 'loop_shop_per_page', 'tata_wc_per_page', 20 );

/**
 * Layout chosen by the visitor: list, 2, 3 or 4 per row (default 3).
 *
 * @return string
 */
function tata_shop_layout() {
	$value = isset( $_COOKIE['tata_layout'] ) ? sanitize_key( wp_unslash( $_COOKIE['tata_layout'] ) ) : '';
	return in_array( $value, array( 'list', '2', '3', '4' ), true ) ? $value : '3';
}

/**
 * Three columns.
 *
 * @return int
 */
function tata_wc_columns() {
	return 3;
}
add_filter( 'loop_shop_columns', 'tata_wc_columns' );

/* --------------------------------------------------------------------------
 * Layout wrappers
 * ------------------------------------------------------------------------ */

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/**
 * Open wrapper.
 */
function tata_wc_wrapper_start() {
	echo '<div class="wc-page"><div class="container">';
	tata_breadcrumbs();
}
add_action( 'woocommerce_before_main_content', 'tata_wc_wrapper_start', 10 );

/**
 * Close wrapper.
 */
function tata_wc_wrapper_end() {
	echo '</div></div>';
}
add_action( 'woocommerce_after_main_content', 'tata_wc_wrapper_end', 10 );

// The archive template prints its own title and description.
remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );
add_filter( 'woocommerce_show_page_title', '__return_false' );

remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

/**
 * Shop toolbar: categories button (mobile), result count, layout switch, products per page, sorting.
 */
function tata_wc_toolbar() {
	$layout   = tata_shop_layout();
	$per_page = tata_wc_per_page();
	$layouts  = array(
		'list' => array( 'i-list', __( 'List view', 'tata-surgical' ) ),
		'2'    => array( 'i-grid2', __( '2 per row', 'tata-surgical' ) ),
		'3'    => array( 'i-grid3', __( '3 per row', 'tata-surgical' ) ),
		'4'    => array( 'i-grid4', __( '4 per row', 'tata-surgical' ) ),
	);
	$action = strtok( get_pagenum_link( 1 ), '?' );
	?>
	<div class="shop-toolbar">
		<div class="shop-toolbar__left">
			<button class="filter-toggle" type="button" data-filter-toggle aria-expanded="false" aria-controls="shop-side">
				<?php tata_the_icon( 'i-filter' ); ?><?php esc_html_e( 'Categories', 'tata-surgical' ); ?>
			</button>
			<?php woocommerce_result_count(); ?>
		</div>
		<div class="shop-toolbar__right">
			<div class="view-switch" role="group" aria-label="<?php esc_attr_e( 'Product layout', 'tata-surgical' ); ?>">
				<?php foreach ( $layouts as $value => $info ) : ?>
					<button type="button" data-layout-btn="<?php echo esc_attr( $value ); ?>" aria-pressed="<?php echo $layout === (string) $value ? 'true' : 'false'; ?>" title="<?php echo esc_attr( $info[1] ); ?>">
						<?php tata_the_icon( $info[0] ); ?><span class="sr"><?php echo esc_html( $info[1] ); ?></span>
					</button>
				<?php endforeach; ?>
			</div>
			<form class="per-page" method="get" action="<?php echo esc_url( $action ); ?>">
				<label>
					<span><?php esc_html_e( 'Show', 'tata-surgical' ); ?></span>
					<select name="per_page" data-per-page aria-label="<?php esc_attr_e( 'Products per page', 'tata-surgical' ); ?>">
						<?php foreach ( tata_per_page_options() as $n ) : ?>
							<option value="<?php echo (int) $n; ?>" <?php selected( $per_page, $n ); ?>><?php echo (int) $n; ?></option>
						<?php endforeach; ?>
					</select>
				</label>
				<?php wc_query_string_form_fields( null, array( 'per_page', 'paged', 'product-page', 'submit' ) ); ?>
				<noscript><button class="btn btn--sm btn--outline" type="submit"><?php esc_html_e( 'Apply', 'tata-surgical' ); ?></button></noscript>
			</form>
			<?php woocommerce_catalog_ordering(); ?>
		</div>
	</div>
	<?php
}
add_action( 'woocommerce_before_shop_loop', 'tata_wc_toolbar', 20 );

/* --------------------------------------------------------------------------
 * Single product
 * ------------------------------------------------------------------------ */

remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );

/**
 * Item code and category under the product title.
 */
function tata_wc_single_code() {
	global $product;
	$cats = wc_get_product_category_list( $product->get_id(), ', ' );
	echo '<div class="p-meta">';
	if ( $product->get_sku() ) {
		printf( '<span class="p-code">%s <b class="notranslate">%s</b></span>', esc_html__( 'Item code', 'tata-surgical' ), esc_html( $product->get_sku() ) );
	}
	if ( $cats ) {
		echo '<span class="p-cats">' . wp_kses_post( $cats ) . '</span>';
	}
	echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'tata_wc_single_code', 6 );

/**
 * Key specifications: dimensions, weight and visible attributes.
 */
function tata_wc_single_specs() {
	global $product;
	$rows = array();

	if ( $product->has_dimensions() ) {
		$rows[ __( 'Size', 'tata-surgical' ) ] = wc_format_dimensions( $product->get_dimensions( false ) );
	}
	if ( $product->has_weight() ) {
		$rows[ __( 'Weight', 'tata-surgical' ) ] = $product->get_weight() . ' ' . get_option( 'woocommerce_weight_unit' );
	}
	foreach ( $product->get_attributes() as $attribute ) {
		if ( ! $attribute->get_visible() ) {
			continue;
		}
		$values = $attribute->is_taxonomy()
			? wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'names' ) )
			: $attribute->get_options();
		$rows[ wc_attribute_label( $attribute->get_name() ) ] = implode( ', ', $values );
	}

	if ( ! $rows ) {
		return;
	}

	echo '<dl class="p-specs">';
	foreach ( array_slice( $rows, 0, 6, true ) as $label => $value ) {
		printf( '<div><dt>%s</dt><dd>%s</dd></div>', esc_html( $label ), esc_html( $value ) );
	}
	echo '</dl>';
}
add_action( 'woocommerce_single_product_summary', 'tata_wc_single_specs', 25 );

/**
 * Quote box: quantity, Add to quote, WhatsApp.
 */
function tata_wc_single_quote() {
	global $product;
	$label = $product->get_sku() ? $product->get_sku() . ', ' . $product->get_name() : $product->get_name();
	?>
	<div class="p-quote">
		<div class="p-quote__row">
			<label class="sr" for="p-qty"><?php esc_html_e( 'Quantity', 'tata-surgical' ); ?></label>
			<div class="qty qty--lg">
				<button type="button" data-qty-step="-50" aria-label="<?php esc_attr_e( 'Decrease quantity', 'tata-surgical' ); ?>">−</button>
				<input id="p-qty" type="number" min="1" step="1" value="100" inputmode="numeric" data-qty-input>
				<button type="button" data-qty-step="50" aria-label="<?php esc_attr_e( 'Increase quantity', 'tata-surgical' ); ?>">+</button>
			</div>
			<button class="btn btn--primary btn--lg" type="button" <?php echo tata_quote_attrs( $product ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside. ?> data-qty-from="#p-qty">
				<?php tata_the_icon( 'i-quote' ); ?><span data-label><?php esc_html_e( 'Add to quote', 'tata-surgical' ); ?></span>
			</button>
		</div>
		<p class="p-quote__note"><?php esc_html_e( 'Quantity in pieces. Prices depend on quantity, packaging and destination; we reply with a full quotation.', 'tata-surgical' ); ?></p>
		<a class="p-quote__wa" href="<?php echo esc_url( tata_wa( sprintf( /* translators: %s: product. */ __( 'Hello Tata Surgical, I would like a price for %s.', 'tata-surgical' ), $label ) ) ); ?>" target="_blank" rel="noopener">
			<?php tata_the_icon( 'i-wa' ); ?><?php esc_html_e( 'Ask about this product on WhatsApp', 'tata-surgical' ); ?>
		</a>
	</div>
	<?php
}
add_action( 'woocommerce_single_product_summary', 'tata_wc_single_quote', 30 );

/**
 * Custom variant prompt and assurances.
 */
function tata_wc_single_custom() {
	global $product;
	$url = add_query_arg( 'variant', rawurlencode( $product->get_sku() ? $product->get_sku() : $product->get_name() ), tata_url( 'custom_url' ) );
	?>
	<div class="p-custom">
		<span class="p-custom__ico"><?php tata_the_icon( 'i-pencil' ); ?></span>
		<div>
			<p class="p-custom__title"><?php esc_html_e( 'Need a different size, finish or your own logo?', 'tata-surgical' ); ?></p>
			<p><?php esc_html_e( 'We can make a variant of this instrument to your specification.', 'tata-surgical' ); ?> <a href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( 'Request a variant', 'tata-surgical' ); ?></a></p>
		</div>
	</div>
	<ul class="p-assure">
		<li><?php tata_the_icon( 'i-shield' ); ?><?php esc_html_e( 'ISO 13485 and CE certified production', 'tata-surgical' ); ?></li>
		<li><?php tata_the_icon( 'i-tag' ); ?><?php esc_html_e( 'OEM and private-label marking available', 'tata-surgical' ); ?></li>
		<li><?php tata_the_icon( 'i-globe' ); ?><?php esc_html_e( 'Worldwide shipping with export documents', 'tata-surgical' ); ?></li>
	</ul>
	<?php
}
add_action( 'woocommerce_single_product_summary', 'tata_wc_single_custom', 35 );

/**
 * Tabs: rename additional information, drop reviews (B2B catalogue).
 *
 * @param array $tabs Tabs.
 * @return array
 */
function tata_wc_tabs( $tabs ) {
	unset( $tabs['reviews'] );
	if ( isset( $tabs['additional_information'] ) ) {
		$tabs['additional_information']['title'] = __( 'Specifications', 'tata-surgical' );
	}
	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'tata_wc_tabs', 98 );

/**
 * Related products: 4.
 *
 * @param array $args Args.
 * @return array
 */
function tata_wc_related( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'tata_wc_related' );

/**
 * Related heading.
 *
 * @return string
 */
function tata_wc_related_heading() {
	return __( 'Related instruments', 'tata-surgical' );
}
add_filter( 'woocommerce_product_related_products_heading', 'tata_wc_related_heading' );

/* --------------------------------------------------------------------------
 * Search: include item codes (SKU) in normal site search
 * ------------------------------------------------------------------------ */

/**
 * Add SKU matches to the main search query.
 *
 * @param string   $search SQL.
 * @param WP_Query $query  Query.
 * @return string
 */
function tata_search_by_sku( $search, $query ) {
	global $wpdb;

	$is_live = $query->get( 'tata_live' );
	if ( ( ! $is_live && ( is_admin() || ! $query->is_main_query() ) ) || ! $query->is_search() || '' === trim( (string) $search ) ) {
		return $search;
	}

	$ids = tata_sku_matches( (string) $query->get( 's' ) );
	if ( ! $ids ) {
		return $search;
	}

	$clause = trim( $search );
	if ( 0 === stripos( $clause, 'AND' ) ) {
		$clause = substr( $clause, 3 );
	}

	return " AND ( {$wpdb->posts}.ID IN (" . implode( ',', $ids ) . ") OR {$clause} ) ";
}
add_filter( 'posts_search', 'tata_search_by_sku', 10, 2 );

/**
 * Category sidebar for shop pages: each category opens to show its subcategories.
 */
function tata_wc_category_nav() {
	$tree = tata_product_tree();
	if ( ! $tree ) {
		return;
	}

	$current = is_product_category() ? get_queried_object_id() : 0;
	$active  = $current ? array_merge( array( $current ), get_ancestors( $current, 'product_cat' ) ) : array();
	?>
	<nav class="cat-nav" aria-label="<?php esc_attr_e( 'Product categories', 'tata-surgical' ); ?>">
		<p class="cat-nav__title"><?php esc_html_e( 'Categories', 'tata-surgical' ); ?></p>
		<ul>
			<li><div class="cat-nav__row"><a class="<?php echo is_shop() && ! is_search() ? 'is-active' : ''; ?>" href="<?php echo esc_url( tata_products_url() ); ?>"><?php tata_the_icon( 'i-grid3' ); ?><span><?php esc_html_e( 'All products', 'tata-surgical' ); ?></span></a></div></li>
			<?php foreach ( $tree as $cat ) : ?>
				<?php
				$open = in_array( $cat['id'], $active, true );
				$sub  = 'cat-sub-' . (int) $cat['id'];
				?>
				<li class="<?php echo $open ? 'is-open' : ''; ?>">
					<div class="cat-nav__row">
						<a class="<?php echo $current === $cat['id'] ? 'is-active' : ''; ?>" href="<?php echo esc_url( $cat['url'] ); ?>">
							<?php tata_the_icon( $cat['icon'] ); ?><span><?php echo esc_html( $cat['name'] ); ?></span>
							<?php if ( $cat['count'] ) : ?><small class="cat-nav__count"><?php echo (int) $cat['count']; ?></small><?php endif; ?>
						</a>
						<?php if ( $cat['children'] ) : ?>
							<button class="cat-nav__toggle" type="button" data-cat-toggle aria-expanded="<?php echo $open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $sub ); ?>">
								<?php tata_the_icon( 'i-chev' ); ?>
								<span class="sr">
									<?php
									/* translators: %s: category. */
									echo esc_html( sprintf( __( 'Show subcategories of %s', 'tata-surgical' ), $cat['name'] ) );
									?>
								</span>
							</button>
						<?php endif; ?>
					</div>
					<?php if ( $cat['children'] ) : ?>
						<ul id="<?php echo esc_attr( $sub ); ?>">
							<?php foreach ( $cat['children'] as $child ) : ?>
								<li><a class="<?php echo in_array( $child['id'], $active, true ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( $child['url'] ); ?>"><?php echo esc_html( $child['name'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>
	<?php
}
