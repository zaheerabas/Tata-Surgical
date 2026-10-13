<?php
/**
 * Search form.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

$tata_id = wp_unique_id( 'search-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="sr" for="<?php echo esc_attr( $tata_id ); ?>"><?php esc_html_e( 'Search', 'tata-surgical' ); ?></label>
	<?php tata_the_icon( 'i-search' ); ?>
	<input type="search" id="<?php echo esc_attr( $tata_id ); ?>" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Product name or item code', 'tata-surgical' ); ?>">
	<button class="btn btn--primary" type="submit"><?php esc_html_e( 'Search', 'tata-surgical' ); ?></button>
</form>
