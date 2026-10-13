<?php
/**
 * Site header.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip" href="#main"><?php esc_html_e( 'Skip to content', 'tata-surgical' ); ?></a>

<?php
if ( ! tata_elementor_location( 'header' ) ) :
	get_template_part( 'template-parts/header/topbar' );
	?>
	<header class="site-header" data-header>
		<div class="container header-inner">
			<?php tata_header_logo(); ?>

			<nav class="primary-nav" id="primary-nav" aria-label="<?php esc_attr_e( 'Main', 'tata-surgical' ); ?>">
				<?php tata_primary_nav(); ?>
				<div class="drawer-extra">
					<a class="btn btn--primary btn--block" href="<?php echo esc_url( tata_url( 'quote_url' ) ); ?>"><?php esc_html_e( 'Request a quote', 'tata-surgical' ); ?></a>
					<?php if ( tata_opt( 'whatsapp' ) ) : ?>
						<a class="btn btn--wa btn--block" href="<?php echo esc_url( tata_wa() ); ?>" target="_blank" rel="noopener"><?php tata_the_icon( 'i-wa' ); ?><?php esc_html_e( 'Chat on WhatsApp', 'tata-surgical' ); ?></a>
					<?php endif; ?>
				</div>
			</nav>

			<div class="header-actions">
				<button class="icon-btn" type="button" data-open-search aria-haspopup="dialog">
					<?php tata_the_icon( 'i-search' ); ?><span class="sr"><?php esc_html_e( 'Search products', 'tata-surgical' ); ?></span>
				</button>
				<button class="icon-btn" type="button" data-open-quote aria-haspopup="dialog">
					<?php tata_the_icon( 'i-quote' ); ?><span class="badge" data-quote-badge hidden>0</span><span class="sr"><?php esc_html_e( 'Your quote list', 'tata-surgical' ); ?></span>
				</button>
				<a class="btn btn--primary header-cta" href="<?php echo esc_url( tata_url( 'quote_url' ) ); ?>"><?php esc_html_e( 'Request a quote', 'tata-surgical' ); ?></a>
				<button class="icon-btn nav-toggle" type="button" data-nav-toggle aria-expanded="false" aria-controls="primary-nav" aria-label="<?php esc_attr_e( 'Open menu', 'tata-surgical' ); ?>">
					<span data-ico-open><?php tata_the_icon( 'i-menu' ); ?></span><span data-ico-close hidden><?php tata_the_icon( 'i-close' ); ?></span>
				</button>
			</div>
		</div>
		<div class="backdrop" data-backdrop hidden></div>
	</header>
	<?php
endif;

get_template_part( 'template-parts/header/search' );
get_template_part( 'template-parts/header/quote-drawer' );
?>

<main id="main" class="site-main">
