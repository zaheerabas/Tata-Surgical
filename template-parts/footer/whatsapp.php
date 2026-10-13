<?php
/**
 * Floating WhatsApp chat (replaces the Joinchat plugin).
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

if ( ! tata_opt( 'whatsapp' ) ) {
	return;
}
?>
<div class="wa">
	<div class="wa__card" id="wa-card" hidden>
		<div class="wa__head">
			<span class="wa__avatar"><img src="<?php echo esc_url( TATA_URI . '/assets/img/logo.png' ); ?>" alt="" width="34" height="11" loading="lazy"></span>
			<div><strong><?php bloginfo( 'name' ); ?></strong><span><?php esc_html_e( 'Sales team, Sialkot (PKT)', 'tata-surgical' ); ?></span></div>
		</div>
		<div class="wa__body"><p class="wa__msg"><?php echo esc_html( tata_opt( 'wa_greeting' ) ); ?></p></div>
		<div class="wa__foot">
			<a class="btn btn--wa btn--block" href="<?php echo esc_url( tata_wa( __( 'Hello Tata Surgical, I have a question about your instruments.', 'tata-surgical' ) ) ); ?>" target="_blank" rel="noopener"><?php tata_the_icon( 'i-wa' ); ?><?php esc_html_e( 'Open chat', 'tata-surgical' ); ?></a>
		</div>
	</div>
	<button class="wa__btn" type="button" data-wa aria-expanded="false" aria-controls="wa-card"><?php tata_the_icon( 'i-wa' ); ?><span class="sr"><?php esc_html_e( 'Chat with us on WhatsApp', 'tata-surgical' ); ?></span></button>
</div>
