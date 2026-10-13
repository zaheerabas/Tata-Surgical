<?php
/**
 * Footer: highlight cards, link columns with contact, certifications, bottom bar.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

$tata_tree    = tata_product_tree();
$tata_qr      = tata_opt( 'wechat_qr' );
$tata_logo    = tata_opt( 'logo_footer' );
$tata_social  = tata_social_links();
$tata_certs   = tata_certifications();
$tata_markets = tata_markets();
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only.
$tata_news = isset( $_GET['tata_news'] ) ? sanitize_key( wp_unslash( $_GET['tata_news'] ) ) : '';
?>
<footer class="footer">
	<div class="container f-highlights">
		<?php if ( tata_event_active() ) : ?>
			<div class="hl" id="exhibition" data-reveal>
				<div class="hl__top"><span class="hl__ico"><?php tata_the_icon( 'i-calendar' ); ?></span><span class="hl__kicker"><?php esc_html_e( 'Next exhibition', 'tata-surgical' ); ?></span></div>
				<h3><?php echo esc_html( trim( tata_opt( 'event_name' ) . ', ' . tata_opt( 'event_city' ), ', ' ) ); ?></h3>
				<p><?php echo esc_html( trim( tata_opt( 'event_dates' ) . '. ' . tata_opt( 'event_location' ) . '. ' . tata_opt( 'event_note' ) ) ); ?></p>
				<div class="hl__actions">
					<a class="btn btn--light" href="<?php echo esc_url( tata_url( 'event_url' ) ); ?>"><?php esc_html_e( 'Book a meeting', 'tata-surgical' ); ?></a>
					<?php if ( tata_opt( 'event_profile_url' ) ) : ?>
						<a class="hl__link" href="<?php echo esc_url( tata_opt( 'event_profile_url' ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Our exhibitor profile', 'tata-surgical' ); ?><?php tata_the_icon( 'i-link' ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $tata_markets ) : ?>
			<div class="hl" data-reveal style="--d:90ms">
				<div class="hl__top"><span class="hl__ico"><?php tata_the_icon( 'i-globe' ); ?></span><span class="hl__kicker"><?php esc_html_e( 'Supplying distributors in', 'tata-surgical' ); ?></span></div>
				<h3><?php echo esc_html( tata_opt( 'markets_title' ) ); ?></h3>
				<ul class="markets">
					<?php foreach ( $tata_markets as $tata_market ) : ?>
						<li><?php echo esc_html( $tata_market ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<div class="hl" id="newsletter" data-reveal style="--d:180ms">
			<div class="hl__top"><span class="hl__ico"><?php tata_the_icon( 'i-download' ); ?></span><span class="hl__kicker"><?php esc_html_e( 'Catalogue updates', 'tata-surgical' ); ?></span></div>
			<h3><?php esc_html_e( 'Get new catalogues by email', 'tata-surgical' ); ?></h3>
			<p><?php esc_html_e( 'New products and updated catalogues, a few times a year.', 'tata-surgical' ); ?></p>
			<?php if ( 'ok' === $tata_news ) : ?>
				<p class="news-msg" role="status"><?php esc_html_e( 'Subscribed. We will send you the next catalogue.', 'tata-surgical' ); ?></p>
			<?php else : ?>
				<form class="news" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
					<input type="hidden" name="action" value="tata_newsletter">
					<input type="hidden" name="tata_return" value="<?php echo esc_url( home_url( add_query_arg( array() ) ) ); ?>">
					<?php wp_nonce_field( 'tata_newsletter', 'tata_nonce', false ); ?>
					<div class="hp" aria-hidden="true"><input type="text" name="tata_website" tabindex="-1" autocomplete="off"></div>
					<label class="sr" for="news-email"><?php esc_html_e( 'Work email', 'tata-surgical' ); ?></label>
					<input id="news-email" name="tata_email" type="email" placeholder="<?php esc_attr_e( 'Your work email', 'tata-surgical' ); ?>" required>
					<button class="btn btn--light" type="submit"><?php esc_html_e( 'Subscribe', 'tata-surgical' ); ?></button>
				</form>
				<?php if ( 'invalid' === $tata_news || 'error' === $tata_news ) : ?>
					<p class="news-msg news-msg--error" role="alert"><?php esc_html_e( 'Enter a valid email address, for example name@company.com, and try again.', 'tata-surgical' ); ?></p>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>

	<div class="container f-main">
		<div class="f-brand" data-reveal>
			<a class="f-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php if ( $tata_logo ) : ?>
					<img src="<?php echo esc_url( $tata_logo ); ?>" width="600" height="199" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" loading="lazy">
				<?php else : ?>
					<span class="f-logo__text"><?php bloginfo( 'name' ); ?></span>
				<?php endif; ?>
			</a>
			<?php if ( tata_opt( 'footer_about' ) ) : ?>
				<p class="f-about"><?php echo esc_html( tata_opt( 'footer_about' ) ); ?></p>
			<?php endif; ?>
			<?php if ( $tata_social ) : ?>
				<ul class="social">
					<?php foreach ( $tata_social as $tata_network => $tata_link ) : ?>
						<li><a href="<?php echo esc_url( $tata_link ); ?>" target="_blank" rel="noopener"><?php tata_the_icon( 's-' . $tata_network ); ?><span class="sr"><?php echo esc_html( ucfirst( $tata_network ) ); ?></span></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<nav aria-labelledby="f-products" data-reveal style="--d:60ms">
			<h2 class="f-title" id="f-products"><?php esc_html_e( 'Products', 'tata-surgical' ); ?></h2>
			<ul class="f-links">
				<?php if ( $tata_tree ) : ?>
					<?php foreach ( array_slice( $tata_tree, 0, 10 ) as $tata_cat ) : ?>
						<li><a href="<?php echo esc_url( $tata_cat['url'] ); ?>"><?php echo esc_html( $tata_cat['name'] ); ?></a></li>
					<?php endforeach; ?>
				<?php else : ?>
					<li><a href="<?php echo esc_url( tata_products_url() ); ?>"><?php esc_html_e( 'All products', 'tata-surgical' ); ?></a></li>
				<?php endif; ?>
			</ul>
		</nav>

		<nav aria-labelledby="f-services" data-reveal style="--d:120ms">
			<h2 class="f-title" id="f-services"><?php esc_html_e( 'Services', 'tata-surgical' ); ?></h2>
			<?php tata_footer_menu( 'footer_services' ); ?>
		</nav>

		<nav aria-labelledby="f-company" data-reveal style="--d:180ms">
			<h2 class="f-title" id="f-company"><?php esc_html_e( 'Company', 'tata-surgical' ); ?></h2>
			<?php tata_footer_menu( 'footer_company' ); ?>
		</nav>

		<div class="f-contactcol" data-reveal style="--d:240ms">
			<h2 class="f-title"><?php esc_html_e( 'Contact', 'tata-surgical' ); ?></h2>
			<ul class="f-contact">
				<?php if ( tata_opt( 'address' ) ) : ?>
					<li><?php tata_the_icon( 'i-pin' ); ?>
						<?php if ( tata_opt( 'map_url' ) ) : ?>
							<a href="<?php echo esc_url( tata_opt( 'map_url' ) ); ?>" target="_blank" rel="noopener"><address><?php echo esc_html( tata_opt( 'address' ) ); ?></address></a>
						<?php else : ?>
							<address><?php echo esc_html( tata_opt( 'address' ) ); ?></address>
						<?php endif; ?>
					</li>
				<?php endif; ?>
				<?php if ( tata_opt( 'phone' ) ) : ?>
					<li><?php tata_the_icon( 'i-phone' ); ?><a class="notranslate" href="<?php echo esc_url( tata_tel( tata_opt( 'phone' ) ) ); ?>"><?php echo esc_html( tata_opt( 'phone' ) ); ?></a></li>
				<?php endif; ?>
				<?php if ( tata_opt( 'whatsapp' ) ) : ?>
					<li><?php tata_the_icon( 'i-wa' ); ?><a class="notranslate" href="<?php echo esc_url( tata_wa() ); ?>" target="_blank" rel="noopener"><?php echo esc_html( tata_opt( 'whatsapp' ) ); ?></a></li>
				<?php endif; ?>
				<?php if ( tata_opt( 'email' ) ) : ?>
					<li><?php tata_the_icon( 'i-mail' ); ?><span class="notranslate">
						<a href="mailto:<?php echo esc_attr( antispambot( tata_opt( 'email' ) ) ); ?>"><?php echo esc_html( antispambot( tata_opt( 'email' ) ) ); ?></a>
						<?php if ( tata_opt( 'email_2' ) ) : ?>
							<br><a href="mailto:<?php echo esc_attr( antispambot( tata_opt( 'email_2' ) ) ); ?>"><?php echo esc_html( antispambot( tata_opt( 'email_2' ) ) ); ?></a>
						<?php endif; ?>
					</span></li>
				<?php endif; ?>
			</ul>
			<?php if ( $tata_qr ) : ?>
				<div class="qr-row">
					<div class="qr"><img src="<?php echo esc_url( $tata_qr ); ?>" width="92" height="92" alt="<?php esc_attr_e( 'WeChat QR code', 'tata-surgical' ); ?>" loading="lazy">WeChat</div>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $tata_certs ) : ?>
		<div class="container f-certs">
			<span class="f-certs__label"><?php esc_html_e( 'Certified quality', 'tata-surgical' ); ?></span>
			<ul class="certs notranslate">
				<?php foreach ( $tata_certs as $tata_cert ) : ?>
					<li><?php tata_the_icon( 'i-check' ); ?><?php echo esc_html( $tata_cert[0] ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<div class="container f-bottom">
		<p>© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> (Pvt) Ltd. <?php esc_html_e( 'All rights reserved.', 'tata-surgical' ); ?></p>
		<nav aria-label="<?php esc_attr_e( 'Legal', 'tata-surgical' ); ?>"><?php tata_footer_menu( 'footer_legal' ); ?></nav>
		<button class="to-top" type="button" data-top><?php tata_the_icon( 'i-chev' ); ?><?php esc_html_e( 'Back to top', 'tata-surgical' ); ?></button>
	</div>
</footer>
