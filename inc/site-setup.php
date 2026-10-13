<?php
/**
 * Site setup: creates every page and all menus when the theme is first activated.
 *
 * - Missing pages are created empty (no sections), ready to be designed with Elementor.
 * - Pages that already exist (same address) are kept exactly as they are.
 * - The main navbar and the footer menus are built from these pages and assigned.
 * - Runs once, on the first activation. Appearance > Tata Surgical setup can run it again.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

/**
 * Pages the site needs: slug => [title, page template or ''].
 *
 * @return array
 */
function tata_setup_pages() {
	return apply_filters(
		'tata_setup_pages',
		array(
			'home'                   => array( __( 'Home', 'tata-surgical' ), '' ),
			'products'               => array( __( 'Products', 'tata-surgical' ), '' ),
			'custom-manufacturing'   => array( __( 'Custom manufacturing', 'tata-surgical' ), 'page-templates/custom-order.php' ),
			'oem-private-label'      => array( __( 'OEM & private label', 'tata-surgical' ), '' ),
			'quality'                => array( __( 'Quality & certifications', 'tata-surgical' ), '' ),
			'about-us'               => array( __( 'About us', 'tata-surgical' ), '' ),
			'events'                 => array( __( 'Trade fairs & events', 'tata-surgical' ), '' ),
			'catalogues'             => array( __( 'Catalogues', 'tata-surgical' ), '' ),
			'blog'                   => array( __( 'News', 'tata-surgical' ), '' ),
			'request-a-quote'        => array( __( 'Request a quote', 'tata-surgical' ), 'page-templates/request-quote.php' ),
			'contact'                => array( __( 'Contact', 'tata-surgical' ), 'page-templates/contact.php' ),
			'privacy-policy'         => array( __( 'Privacy policy', 'tata-surgical' ), '' ),
			'terms-of-supply'        => array( __( 'Terms of supply', 'tata-surgical' ), '' ),
			'complaints-and-returns' => array( __( 'Complaints and returns', 'tata-surgical' ), '' ),
		)
	);
}

/**
 * Create missing pages. Pages that already exist are left untouched.
 *
 * @return array slug => [id, 'created'|'existing']
 */
function tata_setup_create_pages() {
	$result = array();

	foreach ( tata_setup_pages() as $slug => $page ) {
		$existing = get_page_by_path( $slug, OBJECT, 'page' );

		if ( $existing && 'trash' !== $existing->post_status ) {
			$result[ $slug ] = array( (int) $existing->ID, 'existing' );
			continue;
		}

		$id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $page[0],
				'post_name'    => $slug,
				'post_content' => '',
			),
			true
		);

		if ( is_wp_error( $id ) ) {
			continue;
		}

		if ( $page[1] ) {
			update_post_meta( $id, '_wp_page_template', $page[1] );
		}
		update_post_meta( $id, '_tata_setup_page', $slug );
		$result[ $slug ] = array( $id, 'created' );
	}

	// Reading settings, only where the site has not set them yet.
	$valid = function ( $option ) {
		$id = (int) get_option( $option );
		return $id && 'publish' === get_post_status( $id );
	};
	if ( isset( $result['home'] ) && ! ( 'page' === get_option( 'show_on_front' ) && $valid( 'page_on_front' ) ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $result['home'][0] );
	}
	if ( isset( $result['blog'] ) && ! $valid( 'page_for_posts' ) ) {
		update_option( 'page_for_posts', $result['blog'][0] );
	}
	if ( isset( $result['privacy-policy'] ) && ! $valid( 'wp_page_for_privacy_policy' ) ) {
		update_option( 'wp_page_for_privacy_policy', $result['privacy-policy'][0] );
	}
	if ( isset( $result['products'] ) && class_exists( 'WooCommerce' ) && ! $valid( 'woocommerce_shop_page_id' ) ) {
		update_option( 'woocommerce_shop_page_id', $result['products'][0] );
	}

	update_option( 'tata_setup_pages', wp_list_pluck( $result, 0 ) );
	return $result;
}

/**
 * Page ID by setup slug.
 *
 * @param array  $pages slug => id.
 * @param string $slug  Slug.
 * @return int
 */
function tata_setup_page_id( $pages, $slug ) {
	return isset( $pages[ $slug ] ) ? (int) $pages[ $slug ] : 0;
}

/**
 * Create one menu (replacing a menu with the same name) and assign it.
 *
 * Item format: [title, page slug or '#'/URL, parent key or '', icon class, description, key].
 *
 * @param string $name     Menu name.
 * @param string $location Theme location.
 * @param array  $items    Items.
 * @param array  $pages    slug => page id.
 * @return int Menu ID.
 */
function tata_setup_menu( $name, $location, $items, $pages ) {
	$old = wp_get_nav_menu_object( $name );
	if ( $old ) {
		wp_delete_nav_menu( $old->term_id );
	}

	$menu_id = wp_create_nav_menu( $name );
	if ( is_wp_error( $menu_id ) ) {
		return 0;
	}

	$keys = array();
	foreach ( $items as $item ) {
		list( $title, $target, $parent, $icon, $desc, $key ) = array_pad( $item, 6, '' );

		$args = array(
			'menu-item-title'       => $title,
			'menu-item-status'      => 'publish',
			'menu-item-parent-id'   => ( $parent && isset( $keys[ $parent ] ) ) ? $keys[ $parent ] : 0,
			'menu-item-classes'     => $icon ? 'tata-icon-' . $icon : '',
			'menu-item-description' => $desc,
		);

		$hash    = '';
		$pageref = $target;
		if ( false !== strpos( $target, '#' ) && '#' !== $target ) {
			list( $pageref, $hash ) = explode( '#', $target, 2 );
		}
		$page_id = tata_setup_page_id( $pages, $pageref );

		if ( $page_id && ! $hash ) {
			$args['menu-item-type']      = 'post_type';
			$args['menu-item-object']    = 'page';
			$args['menu-item-object-id'] = $page_id;
		} else {
			$args['menu-item-type'] = 'custom';
			$args['menu-item-url']  = $page_id ? get_permalink( $page_id ) . '#' . $hash : '#';
		}

		$item_id = wp_update_nav_menu_item( $menu_id, 0, $args );
		if ( $key && ! is_wp_error( $item_id ) ) {
			$keys[ $key ] = $item_id;
		}
	}

	$locations              = get_theme_mod( 'nav_menu_locations', array() );
	$locations[ $location ] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );

	return $menu_id;
}

/**
 * Build the navbar and footer menus.
 *
 * @param array $pages slug => page id.
 */
function tata_setup_create_menus( $pages ) {
	// Main navbar. "Products" with the category mega menu is always added first by the theme.
	tata_setup_menu(
		__( 'Main menu', 'tata-surgical' ),
		'primary',
		array(
			array( __( 'Custom manufacturing', 'tata-surgical' ), '#', '', '', '', 'custom' ),
			array( __( 'Made to your drawing', 'tata-surgical' ), 'custom-manufacturing', 'custom', 'i-pencil', __( 'New instruments from your drawing, photo or sample', 'tata-surgical' ) ),
			array( __( 'OEM and private label', 'tata-surgical' ), 'oem-private-label', 'custom', 'i-tag', __( 'Your brand laser-marked on the instrument and box', 'tata-surgical' ) ),
			array( __( 'Sterile packing and custom kits', 'tata-surgical' ), 'custom-manufacturing#sterile-packing', 'custom', 'i-box', __( 'Single-use sets packed in our Class 8 clean room', 'tata-surgical' ) ),

			array( __( 'Quality', 'tata-surgical' ), '#', '', '', '', 'quality' ),
			array( __( 'Certifications', 'tata-surgical' ), 'quality#certifications', 'quality', 'i-shield', __( 'ISO 13485, CE, EU MDR, GMP. Download certificates', 'tata-surgical' ) ),
			array( __( 'Class 8 clean room', 'tata-surgical' ), 'quality#clean-room', 'quality', 'i-cleanroom', __( 'ISO 14644-1 certified sterile production', 'tata-surgical' ) ),
			array( __( 'Manufacturing process', 'tata-surgical' ), 'quality#process', 'quality', 'i-factory', __( 'From forging to passivation and final inspection', 'tata-surgical' ) ),

			array( __( 'Company', 'tata-surgical' ), '#', '', '', '', 'company' ),
			array( __( 'About Tata Surgical', 'tata-surgical' ), 'about-us', 'company', 'i-users', __( 'Family-run manufacturer in Sialkot since 1984', 'tata-surgical' ) ),
			array( __( 'Trade fairs and events', 'tata-surgical' ), 'events', 'company', 'i-calendar', __( 'Exhibitions where you can meet our team', 'tata-surgical' ) ),
			array( __( 'Catalogues', 'tata-surgical' ), 'catalogues', 'company', 'i-download', __( 'Download our product catalogues (PDF)', 'tata-surgical' ) ),
			array( __( 'News', 'tata-surgical' ), 'blog', 'company', 'i-news', __( 'Product launches and company updates', 'tata-surgical' ) ),

			array( __( 'Contact', 'tata-surgical' ), 'contact' ),
		),
		$pages
	);

	tata_setup_menu(
		__( 'Footer: Services', 'tata-surgical' ),
		'footer_services',
		array(
			array( __( 'Made to your drawing', 'tata-surgical' ), 'custom-manufacturing' ),
			array( __( 'OEM and private label', 'tata-surgical' ), 'oem-private-label' ),
			array( __( 'Sterile packing', 'tata-surgical' ), 'custom-manufacturing#sterile-packing' ),
			array( __( 'Custom kits', 'tata-surgical' ), 'custom-manufacturing#custom-kits' ),
			array( __( 'Request a quote', 'tata-surgical' ), 'request-a-quote' ),
			array( __( 'Download catalogues', 'tata-surgical' ), 'catalogues' ),
		),
		$pages
	);

	tata_setup_menu(
		__( 'Footer: Company', 'tata-surgical' ),
		'footer_company',
		array(
			array( __( 'About us', 'tata-surgical' ), 'about-us' ),
			array( __( 'Quality and certifications', 'tata-surgical' ), 'quality' ),
			array( __( 'Clean room', 'tata-surgical' ), 'quality#clean-room' ),
			array( __( 'Trade fairs', 'tata-surgical' ), 'events' ),
			array( __( 'News', 'tata-surgical' ), 'blog' ),
			array( __( 'Contact', 'tata-surgical' ), 'contact' ),
		),
		$pages
	);

	tata_setup_menu(
		__( 'Footer: Legal', 'tata-surgical' ),
		'footer_legal',
		array(
			array( __( 'Privacy policy', 'tata-surgical' ), 'privacy-policy' ),
			array( __( 'Terms of supply', 'tata-surgical' ), 'terms-of-supply' ),
			array( __( 'Complaints and returns', 'tata-surgical' ), 'complaints-and-returns' ),
		),
		$pages
	);
}

/**
 * Run the full setup.
 *
 * @return array Page results.
 */
function tata_run_site_setup() {
	$result = tata_setup_create_pages();
	tata_setup_create_menus( wp_list_pluck( $result, 0 ) );
	tata_flush_product_tree();
	flush_rewrite_rules( false );
	update_option( 'tata_setup_done', TATA_VERSION );
	set_transient( 'tata_setup_report', $result, HOUR_IN_SECONDS );
	return $result;
}

/**
 * First activation only. Later activations leave pages and menus alone.
 */
function tata_setup_on_activation() {
	if ( get_option( 'tata_setup_done' ) ) {
		return;
	}
	tata_run_site_setup();
}
add_action( 'after_switch_theme', 'tata_setup_on_activation', 30 );

/**
 * Admin screen: Appearance > Tata Surgical setup.
 */
function tata_setup_admin_menu() {
	add_theme_page(
		__( 'Tata Surgical setup', 'tata-surgical' ),
		__( 'Tata Surgical setup', 'tata-surgical' ),
		'manage_options',
		'tata-setup',
		'tata_setup_admin_page'
	);
}
add_action( 'admin_menu', 'tata_setup_admin_menu' );

/**
 * Handle the "Run setup again" button.
 */
function tata_setup_handle_rerun() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'tata-surgical' ), 403 );
	}
	check_admin_referer( 'tata_setup_rerun' );
	tata_run_site_setup();
	wp_safe_redirect( admin_url( 'themes.php?page=tata-setup&done=1' ) );
	exit;
}
add_action( 'admin_post_tata_setup_rerun', 'tata_setup_handle_rerun' );

/**
 * Admin screen markup.
 */
function tata_setup_admin_page() {
	$pages = (array) get_option( 'tata_setup_pages', array() );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Tata Surgical setup', 'tata-surgical' ); ?></h1>
		<?php if ( isset( $_GET['done'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success"><p><?php esc_html_e( 'Missing pages were created and the menus were rebuilt.', 'tata-surgical' ); ?></p></div>
		<?php endif; ?>

		<p><?php esc_html_e( 'The theme creates these pages when they are missing. Pages that already exist are never changed.', 'tata-surgical' ); ?></p>
		<table class="widefat striped" style="max-width:760px">
			<thead><tr><th><?php esc_html_e( 'Page', 'tata-surgical' ); ?></th><th><?php esc_html_e( 'Address', 'tata-surgical' ); ?></th><th></th></tr></thead>
			<tbody>
				<?php foreach ( tata_setup_pages() as $slug => $page ) : ?>
					<?php $id = isset( $pages[ $slug ] ) ? (int) $pages[ $slug ] : 0; ?>
					<tr>
						<td><?php echo esc_html( $page[0] ); ?></td>
						<td><code>/<?php echo esc_html( $slug ); ?>/</code></td>
						<td>
							<?php if ( $id && get_post_status( $id ) && 'trash' !== get_post_status( $id ) ) : ?>
								<a href="<?php echo esc_url( get_edit_post_link( $id ) ); ?>"><?php esc_html_e( 'Edit', 'tata-surgical' ); ?></a> |
								<a href="<?php echo esc_url( get_permalink( $id ) ); ?>" target="_blank"><?php esc_html_e( 'View', 'tata-surgical' ); ?></a>
							<?php else : ?>
								<?php esc_html_e( 'Missing (run setup to create it)', 'tata-surgical' ); ?>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<h2 style="margin-top:2rem"><?php esc_html_e( 'Run setup again', 'tata-surgical' ); ?></h2>
		<p><?php esc_html_e( 'Creates any missing pages (existing pages are kept as they are) and rebuilds the menus "Main menu", "Footer: Services", "Footer: Company" and "Footer: Legal".', 'tata-surgical' ); ?></p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('<?php echo esc_js( __( 'Create missing pages and rebuild the theme menus?', 'tata-surgical' ) ); ?>');">
			<input type="hidden" name="action" value="tata_setup_rerun">
			<?php wp_nonce_field( 'tata_setup_rerun' ); ?>
			<?php submit_button( __( 'Run setup again', 'tata-surgical' ), 'secondary' ); ?>
		</form>
	</div>
	<?php
}

/**
 * One-time notice after activation listing what was created or replaced.
 */
function tata_setup_notice() {
	$report = get_transient( 'tata_setup_report' );
	if ( ! $report || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	delete_transient( 'tata_setup_report' );

	$created  = count( array_filter( $report, function ( $r ) { return 'created' === $r[1]; } ) );
	$kept     = count( $report ) - $created;
	?>
	<div class="notice notice-success is-dismissible">
		<p>
			<strong><?php esc_html_e( 'Tata Surgical theme is ready.', 'tata-surgical' ); ?></strong>
			<?php
			/* translators: 1: pages created, 2: existing pages kept. */
			echo esc_html( sprintf( __( '%1$d pages created, %2$d existing pages kept as they are. The main menu and footer menus were built and assigned.', 'tata-surgical' ), $created, $kept ) );
			?>
			<a href="<?php echo esc_url( admin_url( 'themes.php?page=tata-setup' ) ); ?>"><?php esc_html_e( 'See all pages', 'tata-surgical' ); ?></a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'tata_setup_notice' );
