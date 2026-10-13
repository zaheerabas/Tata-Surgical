<?php
/**
 * Navigation: Products mega menu (built from WooCommerce categories),
 * dropdown walker for the main menu, and fallbacks for empty menus.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

/**
 * Icons available for categories and menu items.
 *
 * @return array id => label
 */
function tata_icon_choices() {
	return array(
		'c-pouch'     => __( 'Single use (sterile pouch)', 'tata-surgical' ),
		'c-forceps'   => __( 'Forceps', 'tata-surgical' ),
		'c-scissors'  => __( 'Scissors', 'tata-surgical' ),
		'c-bolt'      => __( 'Electrosurgical', 'tata-surgical' ),
		'c-tooth'     => __( 'Dental', 'tata-surgical' ),
		'c-eye'       => __( 'Ophthalmic', 'tata-surgical' ),
		'c-steth'     => __( 'Diagnostic', 'tata-surgical' ),
		'c-kit'       => __( 'Sets and kits', 'tata-surgical' ),
		'c-paw'       => __( 'Veterinary', 'tata-surgical' ),
		'c-nail'      => __( 'Beauty and manicure', 'tata-surgical' ),
		'c-bone'      => __( 'Orthopaedic', 'tata-surgical' ),
		'i-box'       => __( 'Box', 'tata-surgical' ),
		'i-pencil'    => __( 'Drawing', 'tata-surgical' ),
		'i-tag'       => __( 'Label', 'tata-surgical' ),
		'i-shield'    => __( 'Certificate', 'tata-surgical' ),
		'i-cleanroom' => __( 'Clean room', 'tata-surgical' ),
		'i-factory'   => __( 'Factory', 'tata-surgical' ),
		'i-users'     => __( 'People', 'tata-surgical' ),
		'i-calendar'  => __( 'Calendar', 'tata-surgical' ),
		'i-news'      => __( 'News', 'tata-surgical' ),
		'i-download'  => __( 'Download', 'tata-surgical' ),
		'i-mail'      => __( 'Email', 'tata-surgical' ),
	);
}

/**
 * Best-guess icon for a category from its slug, when none was chosen.
 *
 * @param string $slug Slug.
 * @return string
 */
function tata_guess_cat_icon( $slug ) {
	$map = array(
		'electro'  => 'c-bolt',
		'dental'   => 'c-tooth',
		'ophthalm' => 'c-eye',
		'eye'      => 'c-eye',
		'diagnos'  => 'c-steth',
		'set'      => 'c-kit',
		'kit'      => 'c-kit',
		'vet'      => 'c-paw',
		'beauty'   => 'c-nail',
		'manicure' => 'c-nail',
		'ortho'    => 'c-bone',
		'reusable' => 'c-scissors',
		'scissor'  => 'c-scissors',
		'single'   => 'c-pouch',
		'forceps'  => 'c-forceps',
	);
	foreach ( $map as $needle => $icon ) {
		if ( false !== strpos( $slug, $needle ) ) {
			return $icon;
		}
	}
	return 'c-forceps';
}

/**
 * Product category tree for menus. Cached; cleared when categories change.
 *
 * @return array
 */
function tata_product_tree() {
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return array();
	}

	$tree = get_transient( 'tata_product_tree' );
	if ( is_array( $tree ) ) {
		return $tree;
	}

	$tree    = array();
	$exclude = array_filter( array( (int) get_option( 'default_product_cat' ) ) );
	$parents = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'parent'     => 0,
			'hide_empty' => false,
			'exclude'    => $exclude,
			'menu_order' => 'asc',
		)
	);

	if ( is_wp_error( $parents ) ) {
		return array();
	}

	foreach ( $parents as $parent ) {
		$children = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'parent'     => $parent->term_id,
				'hide_empty' => false,
				'number'     => 8,
				'menu_order' => 'asc',
			)
		);

		$kids = array();
		if ( ! is_wp_error( $children ) ) {
			foreach ( $children as $child ) {
				$kids[] = array(
					'id'   => $child->term_id,
					'name' => $child->name,
					'url'  => get_term_link( $child ),
				);
			}
		}

		$icon  = get_term_meta( $parent->term_id, 'tata_icon', true );
		$short = get_term_meta( $parent->term_id, 'tata_short', true );
		if ( ! $short && $kids ) {
			$short = implode( ', ', array_slice( wp_list_pluck( $kids, 'name' ), 0, 3 ) );
		}

		$tree[] = array(
			'id'       => $parent->term_id,
			'name'     => $parent->name,
			'slug'     => $parent->slug,
			'url'      => get_term_link( $parent ),
			'count'    => (int) $parent->count,
			'icon'     => $icon ? $icon : tata_guess_cat_icon( $parent->slug ),
			'short'    => $short,
			'badge'    => get_term_meta( $parent->term_id, 'tata_badge', true ),
			'thumb'    => (int) get_term_meta( $parent->term_id, 'thumbnail_id', true ),
			'children' => $kids,
		);
	}

	set_transient( 'tata_product_tree', $tree, 12 * HOUR_IN_SECONDS );
	return $tree;
}

/**
 * Clear cached tree.
 */
function tata_flush_product_tree() {
	delete_transient( 'tata_product_tree' );
}
add_action( 'created_product_cat', 'tata_flush_product_tree' );
add_action( 'edited_product_cat', 'tata_flush_product_tree' );
add_action( 'delete_product_cat', 'tata_flush_product_tree' );
add_action( 'woocommerce_after_product_ordering', 'tata_flush_product_tree' );

/**
 * The Products menu item with its mega panel.
 *
 * @return string
 */
function tata_products_menu_item() {
	$tree = tata_product_tree();

	if ( empty( $tree ) ) {
		return sprintf( '<li><a class="nav-link" href="%s">%s</a></li>', esc_url( tata_products_url() ), esc_html__( 'Products', 'tata-surgical' ) );
	}

	$current = ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) ? ' is-current' : '';

	ob_start();
	?>
	<li class="has-mega" data-menu>
		<button class="nav-link<?php echo esc_attr( $current ); ?>" type="button" data-menu-toggle aria-expanded="false" aria-controls="mega-products">
			<?php esc_html_e( 'Products', 'tata-surgical' ); ?><?php tata_the_icon( 'i-chev', 'chev' ); ?>
		</button>
		<div class="mega" id="mega-products" hidden>
			<div class="container mega__inner">
				<div>
					<div class="mega__head">
						<p class="mega__heading"><?php esc_html_e( 'Browse by category', 'tata-surgical' ); ?></p>
						<button class="mega__find" type="button" data-open-search>
							<?php tata_the_icon( 'i-search' ); ?><?php esc_html_e( 'Find by name or item code', 'tata-surgical' ); ?><kbd>/</kbd>
						</button>
					</div>
					<div class="cats">
						<?php foreach ( $tree as $cat ) : ?>
							<a class="cat" href="<?php echo esc_url( $cat['url'] ); ?>">
								<span class="cat__ico"><?php tata_the_icon( $cat['icon'] ); ?></span>
								<span>
									<span class="cat__name"><?php echo esc_html( $cat['name'] ); ?><?php if ( $cat['badge'] ) : ?><span class="cat__new"><?php echo esc_html( $cat['badge'] ); ?></span><?php endif; ?></span>
									<?php if ( $cat['short'] ) : ?><span class="cat__sub"><?php echo esc_html( $cat['short'] ); ?></span><?php endif; ?>
								</span>
							</a>
						<?php endforeach; ?>
					</div>
					<div class="mega__foot">
						<a href="<?php echo esc_url( tata_products_url() ); ?>"><?php esc_html_e( 'View all products', 'tata-surgical' ); ?></a>
						<a href="<?php echo esc_url( tata_url( 'catalogue_url' ) ); ?>"><?php tata_the_icon( 'i-download' ); ?><?php esc_html_e( 'Download catalogues (PDF)', 'tata-surgical' ); ?></a>
					</div>
				</div>
				<aside class="custom-card">
					<p class="custom-card__title"><?php esc_html_e( 'Not in our catalogue?', 'tata-surgical' ); ?></p>
					<p><?php esc_html_e( 'We make instruments to your drawing or sample, with your size, finish, logo and packaging.', 'tata-surgical' ); ?></p>
					<ol class="steps">
						<li><?php esc_html_e( 'Send a drawing or sample', 'tata-surgical' ); ?></li>
						<li><?php esc_html_e( 'Approve the prototype', 'tata-surgical' ); ?></li>
						<li><?php esc_html_e( 'We produce and ship', 'tata-surgical' ); ?></li>
					</ol>
					<a class="btn btn--light btn--block" href="<?php echo esc_url( tata_url( 'custom_url' ) ); ?>"><?php esc_html_e( 'Start a custom order', 'tata-surgical' ); ?></a>
				</aside>
			</div>
		</div>
	</li>
	<?php
	return ob_get_clean();
}

/**
 * Walker for the main menu.
 * Top-level items with children become dropdowns. Child items show an icon
 * (add a CSS class like "tata-icon-i-pencil" to the menu item) and the menu
 * item description (enable "Description" under Screen Options in Appearance > Menus).
 */
class Tata_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * Parents seen, to close their dropdowns.
	 *
	 * @var array
	 */
	private $parents = array();

	/**
	 * No <ul> for sub levels; the dropdown wrapper is printed in start_el.
	 *
	 * @param string   $output Output.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {}

	/**
	 * See start_lvl.
	 *
	 * @param string   $output Output.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {}

	/**
	 * Start element.
	 *
	 * @param string   $output Output.
	 * @param WP_Post  $item   Item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 * @param int      $id     ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes  = (array) $item->classes;
		$current  = array_intersect( $classes, array( 'current-menu-item', 'current-menu-ancestor', 'current-menu-parent', 'current_page_item' ) ) ? ' is-current' : '';
		$title    = apply_filters( 'nav_menu_item_title', $item->title, $item, $args, $depth );
		$url      = $item->url ? $item->url : '#';
		$target   = $item->target ? ' target="' . esc_attr( $item->target ) . '" rel="noopener"' : '';

		if ( 0 === $depth ) {
			if ( $this->has_children ) {
				$this->parents[ $item->ID ] = true;
				$panel                      = 'dd-' . (int) $item->ID;
				$output .= '<li data-menu>';
				$output .= '<button class="nav-link' . esc_attr( $current ) . '" type="button" data-menu-toggle aria-expanded="false" aria-controls="' . esc_attr( $panel ) . '">' . esc_html( $title ) . tata_icon( 'i-chev', 'chev' ) . '</button>';
				$output .= '<div class="dropdown" id="' . esc_attr( $panel ) . '" hidden>';
				if ( '#' !== $url && ! empty( $item->url ) ) {
					// Keep the parent page reachable.
					$output .= '<a class="dd-overview" href="' . esc_url( $url ) . '">' . esc_html( sprintf( /* translators: %s: menu item. */ __( '%s overview', 'tata-surgical' ), $title ) ) . '</a>';
				}
			} else {
				$output .= '<li><a class="nav-link' . esc_attr( $current ) . '" href="' . esc_url( $url ) . '"' . $target . '>' . esc_html( $title ) . '</a>';
			}
			return;
		}

		$icon = 'i-link';
		foreach ( $classes as $class ) {
			if ( 0 === strpos( $class, 'tata-icon-' ) ) {
				$icon = substr( $class, 10 );
			}
		}

		$output .= '<a class="dd-item' . esc_attr( $current ) . '" href="' . esc_url( $url ) . '"' . $target . '>';
		$output .= '<span class="dd-ico">' . tata_icon( $icon ) . '</span><span>';
		$output .= '<span class="dd-item__title">' . esc_html( $title ) . '</span>';
		if ( ! empty( $item->description ) ) {
			$output .= '<span class="dd-item__desc">' . esc_html( $item->description ) . '</span>';
		}
		$output .= '</span></a>';
	}

	/**
	 * End element.
	 *
	 * @param string   $output Output.
	 * @param WP_Post  $item   Item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		if ( 0 !== $depth ) {
			return;
		}
		if ( isset( $this->parents[ $item->ID ] ) ) {
			$output .= '</div>';
		}
		$output .= '</li>';
	}
}

/**
 * Print the main navigation list.
 */
function tata_primary_nav() {
	echo '<ul class="nav-list">';
	echo tata_products_menu_item(); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside.

	if ( has_nav_menu( 'primary' ) ) {
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'items_wrap'     => '%3$s',
				'depth'          => 2,
				'walker'         => new Tata_Nav_Walker(),
			)
		);
	} else {
		tata_primary_nav_fallback();
	}

	echo '</ul>';
}

/**
 * Default main menu, used until a menu is assigned in Appearance > Menus.
 */
function tata_primary_nav_fallback() {
	$event = tata_event_active()
		/* translators: 1: exhibition name, 2: hall and booth. */
		? sprintf( __( 'Meet us at %1$s, %2$s', 'tata-surgical' ), tata_opt( 'event_name' ), tata_opt( 'event_location' ) )
		: __( 'Where to meet our team', 'tata-surgical' );

	$groups = array(
		'custom'  => array(
			__( 'Custom manufacturing', 'tata-surgical' ),
			array(
				array( 'i-pencil', __( 'Made to your drawing', 'tata-surgical' ), __( 'New instruments from your drawing, photo or sample', 'tata-surgical' ), tata_url( 'custom_url' ) ),
				array( 'i-tag', __( 'OEM and private label', 'tata-surgical' ), __( 'Your brand laser-marked on the instrument and box', 'tata-surgical' ), tata_url( 'oem_url' ) ),
				array( 'i-box', __( 'Sterile packing and custom kits', 'tata-surgical' ), __( 'Single-use sets packed in our Class 8 clean room', 'tata-surgical' ), tata_url( 'custom_url' ) ),
			),
		),
		'quality' => array(
			__( 'Quality', 'tata-surgical' ),
			array(
				array( 'i-shield', __( 'Certifications', 'tata-surgical' ), __( 'ISO 13485, CE, EU MDR, GMP. Download certificates', 'tata-surgical' ), tata_url( 'quality_url' ) ),
				array( 'i-cleanroom', __( 'Class 8 clean room', 'tata-surgical' ), __( 'ISO 14644-1 certified sterile production', 'tata-surgical' ), tata_url( 'quality_url' ) ),
				array( 'i-factory', __( 'Manufacturing process', 'tata-surgical' ), __( 'From forging to passivation and final inspection', 'tata-surgical' ), tata_url( 'quality_url' ) ),
			),
		),
		'company' => array(
			__( 'Company', 'tata-surgical' ),
			array(
				/* translators: %s: year established. */
				array( 'i-users', __( 'About Tata Surgical', 'tata-surgical' ), sprintf( __( 'Family-run manufacturer in Sialkot since %s', 'tata-surgical' ), tata_opt( 'established' ) ), tata_url( 'about_url' ) ),
				array( 'i-calendar', __( 'Trade fairs and events', 'tata-surgical' ), $event, tata_url( 'event_url' ) ),
				array( 'i-news', __( 'News', 'tata-surgical' ), __( 'Product launches and company updates', 'tata-surgical' ), get_post_type_archive_link( 'post' ) ),
			),
		),
	);

	foreach ( $groups as $key => $group ) {
		printf(
			'<li data-menu><button class="nav-link" type="button" data-menu-toggle aria-expanded="false" aria-controls="dd-%1$s">%2$s%3$s</button><div class="dropdown" id="dd-%1$s" hidden>',
			esc_attr( $key ),
			esc_html( $group[0] ),
			tata_icon( 'i-chev', 'chev' ) // phpcs:ignore WordPress.Security.EscapeOutput
		);
		foreach ( $group[1] as $item ) {
			printf(
				'<a class="dd-item" href="%4$s"><span class="dd-ico">%1$s</span><span><span class="dd-item__title">%2$s</span><span class="dd-item__desc">%3$s</span></span></a>',
				tata_icon( $item[0] ), // phpcs:ignore WordPress.Security.EscapeOutput
				esc_html( $item[1] ),
				esc_html( $item[2] ),
				esc_url( $item[3] )
			);
		}
		echo '</div></li>';
	}

	printf( '<li><a class="nav-link" href="%s">%s</a></li>', esc_url( tata_url( 'contact_url' ) ), esc_html__( 'Contact', 'tata-surgical' ) );
}

/**
 * Footer menu with fallback links.
 *
 * @param string $location Menu location.
 */
function tata_footer_menu( $location ) {
	if ( has_nav_menu( $location ) ) {
		wp_nav_menu(
			array(
				'theme_location' => $location,
				'container'      => false,
				'menu_class'     => 'footer_legal' === $location ? 'legal' : 'f-links',
				'depth'          => 1,
			)
		);
		return;
	}

	$sets = array(
		'footer_services' => array(
			array( tata_url( 'custom_url' ), __( 'Made to your drawing', 'tata-surgical' ) ),
			array( tata_url( 'oem_url' ), __( 'OEM and private label', 'tata-surgical' ) ),
			array( tata_url( 'custom_url' ), __( 'Sterile packing', 'tata-surgical' ) ),
			array( tata_url( 'custom_url' ), __( 'Custom kits', 'tata-surgical' ) ),
			array( tata_url( 'quote_url' ), __( 'Request a quote', 'tata-surgical' ) ),
			array( tata_url( 'catalogue_url' ), __( 'Download catalogues', 'tata-surgical' ) ),
		),
		'footer_company'  => array(
			array( tata_url( 'about_url' ), __( 'About us', 'tata-surgical' ) ),
			array( tata_url( 'quality_url' ), __( 'Quality and certifications', 'tata-surgical' ) ),
			array( tata_url( 'quality_url' ), __( 'Clean room', 'tata-surgical' ) ),
			array( tata_url( 'event_url' ), __( 'Trade fairs', 'tata-surgical' ) ),
			array( get_post_type_archive_link( 'post' ), __( 'News', 'tata-surgical' ) ),
			array( tata_url( 'contact_url' ), __( 'Contact', 'tata-surgical' ) ),
		),
		'footer_legal'    => array(
			array( get_privacy_policy_url() ? get_privacy_policy_url() : home_url( '/privacy-policy/' ), __( 'Privacy policy', 'tata-surgical' ) ),
			array( home_url( '/terms-of-supply/' ), __( 'Terms of supply', 'tata-surgical' ) ),
			array( home_url( '/complaints-and-returns/' ), __( 'Complaints and returns', 'tata-surgical' ) ),
		),
	);

	if ( empty( $sets[ $location ] ) ) {
		return;
	}

	echo '<ul class="' . ( 'footer_legal' === $location ? 'legal' : 'f-links' ) . '">';
	foreach ( $sets[ $location ] as $link ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( $link[0] ), esc_html( $link[1] ) );
	}
	echo '</ul>';
}
