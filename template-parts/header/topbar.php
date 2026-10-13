<?php
/**
 * Top bar: next exhibition, email, WhatsApp, WeChat, language.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

$tata_langs = tata_languages();
$tata_qr    = tata_opt( 'wechat_qr' );
?>
<div class="topbar">
	<div class="container topbar__inner">
		<?php if ( tata_event_active() ) : ?>
			<p class="notice">
				<span class="notice__chip"><i></i><?php esc_html_e( 'Next exhibition', 'tata-surgical' ); ?></span>
				<span class="notice__text">
					<?php echo esc_html( trim( tata_opt( 'event_name' ) . ', ' . tata_opt( 'event_city' ) . ', ' . tata_opt( 'event_dates' ), ', ' ) ); ?>:
					<?php $tata_profile = tata_opt( 'event_profile_url' ) ? tata_opt( 'event_profile_url' ) : tata_url( 'event_url' ); ?>
					<a href="<?php echo esc_url( $tata_profile ); ?>"<?php echo tata_opt( 'event_profile_url' ) ? ' target="_blank" rel="noopener"' : ''; ?> title="<?php esc_attr_e( 'Our exhibitor profile', 'tata-surgical' ); ?>"><?php echo esc_html( tata_opt( 'event_location' ) ); ?></a>
				</span>
			</p>
		<?php else : ?>
			<p class="notice notice--plain"><span class="notice__text">
				<?php
				/* translators: %s: year established. */
				printf( esc_html__( 'Surgical instruments manufacturer and exporter, Sialkot, since %s', 'tata-surgical' ), esc_html( tata_opt( 'established' ) ) );
				?>
			</span></p>
		<?php endif; ?>

		<ul class="tb-list">
			<?php if ( tata_opt( 'email' ) ) : ?>
				<li><a class="tb-link" href="mailto:<?php echo esc_attr( antispambot( tata_opt( 'email' ) ) ); ?>"><?php tata_the_icon( 'i-mail' ); ?><span class="tb-text notranslate"><?php echo esc_html( antispambot( tata_opt( 'email' ) ) ); ?></span></a></li>
			<?php endif; ?>
			<?php if ( tata_opt( 'whatsapp' ) ) : ?>
				<li><a class="tb-link" href="<?php echo esc_url( tata_wa() ); ?>" target="_blank" rel="noopener"><?php tata_the_icon( 'i-wa' ); ?><span class="tb-text notranslate"><?php echo esc_html( tata_opt( 'whatsapp' ) ); ?></span></a></li>
			<?php endif; ?>
			<?php if ( $tata_qr ) : ?>
				<li class="tb-hide-sm">
					<button class="tb-link" type="button" data-pop="pop-wechat" aria-expanded="false" aria-controls="pop-wechat"><?php tata_the_icon( 'i-wechat' ); ?>WeChat</button>
					<div class="pop pop--qr" id="pop-wechat" hidden>
						<img src="<?php echo esc_url( $tata_qr ); ?>" width="150" height="150" alt="<?php esc_attr_e( 'WeChat QR code for Tata Surgical', 'tata-surgical' ); ?>" loading="lazy">
						<p><?php esc_html_e( 'Scan with WeChat to chat with our sales team', 'tata-surgical' ); ?></p>
					</div>
				</li>
			<?php endif; ?>
			<?php if ( count( $tata_langs ) > 1 ) : ?>
				<?php
				// WPML or Polylang: real translated pages.
				$tata_current = $tata_langs[0]['code'];
				foreach ( $tata_langs as $tata_lang ) {
					if ( $tata_lang['current'] ) {
						$tata_current = $tata_lang['code'];
					}
				}
				?>
				<li class="notranslate">
					<button class="tb-link" type="button" data-pop="pop-lang" aria-expanded="false" aria-controls="pop-lang">
						<?php tata_the_icon( 'i-globe' ); ?><?php echo esc_html( $tata_current ); ?><?php tata_the_icon( 'i-chev', 'chev' ); ?>
						<span class="sr"><?php esc_html_e( 'Change language', 'tata-surgical' ); ?></span>
					</button>
					<div class="pop lang" id="pop-lang" hidden>
						<?php foreach ( $tata_langs as $tata_lang ) : ?>
							<a href="<?php echo esc_url( $tata_lang['url'] ); ?>" <?php echo $tata_lang['current'] ? 'aria-current="true"' : ''; ?> lang="<?php echo esc_attr( strtolower( $tata_lang['code'] ) ); ?>"><?php echo esc_html( $tata_lang['name'] ); ?> <small><?php echo esc_html( $tata_lang['code'] ); ?></small></a>
						<?php endforeach; ?>
					</div>
				</li>
			<?php elseif ( tata_translate_languages() ) : ?>
				<?php
				// Built-in translator (Google Translate), loaded only after a visitor picks a language.
				$tata_tl      = tata_translate_languages();
				$tata_current = tata_translate_current();
				?>
				<li class="notranslate" data-translator data-langs="<?php echo esc_attr( implode( ',', array_keys( $tata_tl ) ) ); ?>">
					<button class="tb-link" type="button" data-pop="pop-lang" aria-expanded="false" aria-controls="pop-lang">
						<?php tata_the_icon( 'i-globe' ); ?><span data-lang-label><?php echo esc_html( $tata_tl[ $tata_current ][0] ); ?></span><?php tata_the_icon( 'i-chev', 'chev' ); ?>
						<span class="sr"><?php esc_html_e( 'Change language', 'tata-surgical' ); ?></span>
					</button>
					<div class="pop lang" id="pop-lang" hidden>
						<?php foreach ( $tata_tl as $tata_code => $tata_lang ) : ?>
							<button type="button" class="lang__opt" data-lang="<?php echo esc_attr( $tata_code ); ?>" lang="<?php echo esc_attr( $tata_code ); ?>" <?php echo $tata_code === $tata_current ? 'aria-current="true"' : ''; ?>>
								<?php echo esc_html( $tata_lang[1] ); ?> <small><?php echo esc_html( $tata_lang[0] ); ?></small>
							</button>
						<?php endforeach; ?>
						<p class="lang__note"><?php esc_html_e( 'Automatic translation by Google', 'tata-surgical' ); ?></p>
					</div>
					<div id="google_translate_element" hidden></div>
				</li>
			<?php endif; ?>
		</ul>
	</div>
</div>
