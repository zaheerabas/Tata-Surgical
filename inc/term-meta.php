<?php
/**
 * Extra fields on Products > Categories: menu icon, short description, badge.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

/**
 * Field markup for the "Add category" screen.
 */
function tata_cat_add_fields() {
	?>
	<div class="form-field">
		<label for="tata_icon"><?php esc_html_e( 'Menu icon', 'tata-surgical' ); ?></label>
		<select name="tata_icon" id="tata_icon">
			<option value=""><?php esc_html_e( 'Automatic', 'tata-surgical' ); ?></option>
			<?php foreach ( tata_icon_choices() as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="form-field">
		<label for="tata_short"><?php esc_html_e( 'Short description for the menu', 'tata-surgical' ); ?></label>
		<input type="text" name="tata_short" id="tata_short" maxlength="80">
		<p><?php esc_html_e( 'One line, shown under the category name in the Products menu.', 'tata-surgical' ); ?></p>
	</div>
	<div class="form-field">
		<label for="tata_badge"><?php esc_html_e( 'Badge', 'tata-surgical' ); ?></label>
		<input type="text" name="tata_badge" id="tata_badge" maxlength="12">
		<p><?php esc_html_e( 'Optional short label like "New".', 'tata-surgical' ); ?></p>
	</div>
	<?php
}
add_action( 'product_cat_add_form_fields', 'tata_cat_add_fields' );

/**
 * Field markup for the "Edit category" screen.
 *
 * @param WP_Term $term Term.
 */
function tata_cat_edit_fields( $term ) {
	$icon  = get_term_meta( $term->term_id, 'tata_icon', true );
	$short = get_term_meta( $term->term_id, 'tata_short', true );
	$badge = get_term_meta( $term->term_id, 'tata_badge', true );
	?>
	<tr class="form-field">
		<th scope="row"><label for="tata_icon"><?php esc_html_e( 'Menu icon', 'tata-surgical' ); ?></label></th>
		<td>
			<select name="tata_icon" id="tata_icon">
				<option value=""><?php esc_html_e( 'Automatic', 'tata-surgical' ); ?></option>
				<?php foreach ( tata_icon_choices() as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $icon, $value ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="tata_short"><?php esc_html_e( 'Short description for the menu', 'tata-surgical' ); ?></label></th>
		<td><input type="text" name="tata_short" id="tata_short" maxlength="80" value="<?php echo esc_attr( $short ); ?>">
		<p class="description"><?php esc_html_e( 'One line, shown under the category name in the Products menu.', 'tata-surgical' ); ?></p></td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="tata_badge"><?php esc_html_e( 'Badge', 'tata-surgical' ); ?></label></th>
		<td><input type="text" name="tata_badge" id="tata_badge" maxlength="12" value="<?php echo esc_attr( $badge ); ?>">
		<p class="description"><?php esc_html_e( 'Optional short label like "New".', 'tata-surgical' ); ?></p></td>
	</tr>
	<?php
}
add_action( 'product_cat_edit_form_fields', 'tata_cat_edit_fields' );

/**
 * Save fields. WordPress verifies the term form nonce before these hooks run.
 *
 * @param int $term_id Term ID.
 */
function tata_cat_save_fields( $term_id ) {
	if ( ! current_user_can( 'manage_product_terms' ) && ! current_user_can( 'manage_categories' ) ) {
		return;
	}
	// phpcs:disable WordPress.Security.NonceVerification.Missing
	if ( isset( $_POST['tata_icon'] ) ) {
		$icon = sanitize_key( wp_unslash( $_POST['tata_icon'] ) );
		if ( '' === $icon || array_key_exists( $icon, tata_icon_choices() ) ) {
			update_term_meta( $term_id, 'tata_icon', $icon );
		}
	}
	if ( isset( $_POST['tata_short'] ) ) {
		update_term_meta( $term_id, 'tata_short', sanitize_text_field( wp_unslash( $_POST['tata_short'] ) ) );
	}
	if ( isset( $_POST['tata_badge'] ) ) {
		update_term_meta( $term_id, 'tata_badge', sanitize_text_field( wp_unslash( $_POST['tata_badge'] ) ) );
	}
	// phpcs:enable
	tata_flush_product_tree();
}
add_action( 'created_product_cat', 'tata_cat_save_fields' );
add_action( 'edited_product_cat', 'tata_cat_save_fields' );
