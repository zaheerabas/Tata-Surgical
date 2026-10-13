<?php
/**
 * Search window: live results by product name or item code.
 * Pressing Enter opens the full results page.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="overlay" id="search" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Search products', 'tata-surgical' ); ?>" hidden>
	<div class="search-box">
		<form class="search-field" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php tata_the_icon( 'i-search' ); ?>
			<label class="sr" for="search-input"><?php esc_html_e( 'Search by product name or item code', 'tata-surgical' ); ?></label>
			<input id="search-input" type="search" name="s" autocomplete="off" placeholder="<?php esc_attr_e( 'Search by name or item code, e.g. TADB 7688', 'tata-surgical' ); ?>">
			<?php if ( tata_has_wc() ) : ?>
				<input type="hidden" name="post_type" value="product">
			<?php endif; ?>
			<button class="esc" type="button" data-close-search><?php esc_html_e( 'Esc', 'tata-surgical' ); ?></button>
		</form>
		<div class="search-body">
			<?php $tata_popular = tata_popular_searches(); ?>
			<?php if ( $tata_popular ) : ?>
				<div data-search-start>
					<p class="search-label"><?php esc_html_e( 'Popular searches', 'tata-surgical' ); ?></p>
					<div class="chips">
						<?php foreach ( $tata_popular as $tata_term ) : ?>
							<button class="chip" type="button"><?php echo esc_html( $tata_term ); ?></button>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
			<p class="search-label" data-results-label hidden></p>
			<div class="results" data-results aria-live="polite"></div>
		</div>
	</div>
</div>
