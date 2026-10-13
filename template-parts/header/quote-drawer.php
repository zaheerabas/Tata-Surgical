<?php
/**
 * Quote list drawer. Items are kept in the visitor's browser until sent.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="side" id="quote" role="dialog" aria-modal="true" aria-labelledby="quote-title" hidden>
	<div class="side__panel">
		<div class="side__head">
			<h2 id="quote-title"><?php esc_html_e( 'Your quote list', 'tata-surgical' ); ?></h2>
			<button class="icon-btn" type="button" data-close-quote><?php tata_the_icon( 'i-close' ); ?><span class="sr"><?php esc_html_e( 'Close quote list', 'tata-surgical' ); ?></span></button>
		</div>
		<div class="side__body">
			<p class="side__intro"><?php esc_html_e( 'Add the instruments you need and set quantities. We reply with prices, lead time and shipping terms.', 'tata-surgical' ); ?></p>
			<div data-quote-items></div>
		</div>
		<div class="side__foot">
			<a class="btn btn--primary btn--block" href="<?php echo esc_url( tata_url( 'quote_url' ) ); ?>"><?php esc_html_e( 'Continue to quote request', 'tata-surgical' ); ?></a>
			<?php if ( tata_opt( 'whatsapp' ) ) : ?>
				<a class="btn btn--outline btn--block" href="<?php echo esc_url( tata_wa() ); ?>" target="_blank" rel="noopener" data-quote-wa><?php tata_the_icon( 'i-wa' ); ?><?php esc_html_e( 'Send list on WhatsApp', 'tata-surgical' ); ?></a>
			<?php endif; ?>
			<p><?php esc_html_e( 'Need something that is not listed? Add a note or a drawing on the next step.', 'tata-surgical' ); ?></p>
		</div>
	</div>
</div>
