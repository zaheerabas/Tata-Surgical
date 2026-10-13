<?php
/**
 * Custom-order band above the footer.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="cta" aria-labelledby="cta-title">
	<div class="container cta__inner">
		<div data-reveal>
			<h2 id="cta-title"><?php esc_html_e( 'Need an instrument that isn’t in our catalogue?', 'tata-surgical' ); ?></h2>
			<div class="dim" aria-hidden="true"><span><?php esc_html_e( 'your size, your finish, your logo', 'tata-surgical' ); ?></span></div>
			<p class="cta__lead"><?php esc_html_e( 'Send us a drawing, a photo or a sample. We manufacture to your specification, sterile or non-sterile, in your own packaging, and ship worldwide with full export documents.', 'tata-surgical' ); ?></p>
			<div class="cta__btns">
				<a class="btn btn--primary btn--lg" href="<?php echo esc_url( tata_url( 'custom_url' ) ); ?>"><?php tata_the_icon( 'i-pencil' ); ?><?php esc_html_e( 'Send your drawing', 'tata-surgical' ); ?></a>
				<?php if ( tata_opt( 'whatsapp' ) ) : ?>
					<a class="btn btn--outline btn--lg" href="<?php echo esc_url( tata_wa( __( 'Hello Tata Surgical, I would like a quotation for a custom instrument.', 'tata-surgical' ) ) ); ?>" target="_blank" rel="noopener"><?php tata_the_icon( 'i-wa' ); ?><?php esc_html_e( 'Discuss on WhatsApp', 'tata-surgical' ); ?></a>
				<?php endif; ?>
			</div>
		</div>
		<ol class="process" data-reveal style="--d:120ms">
			<li><span class="process__n">1</span><div><h3><?php esc_html_e( 'Send your drawing or sample', 'tata-surgical' ); ?></h3><p><?php esc_html_e( 'Share a technical drawing, a photo or a physical sample, with quantity and packaging needs.', 'tata-surgical' ); ?></p></div></li>
			<li><span class="process__n">2</span><div><h3><?php esc_html_e( 'Approve quotation and prototype', 'tata-surgical' ); ?></h3><p><?php esc_html_e( 'We quote price and lead time, then make a sample for your approval.', 'tata-surgical' ); ?></p></div></li>
			<li><span class="process__n">3</span><div><h3><?php esc_html_e( 'Production and worldwide shipping', 'tata-surgical' ); ?></h3><p><?php esc_html_e( 'Forged, finished and inspected in our Sialkot factory, shipped with CE documentation.', 'tata-surgical' ); ?></p></div></li>
		</ol>
	</div>
</section>
