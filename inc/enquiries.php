<?php
/**
 * Enquiries: quote requests, custom-order requests with drawing upload,
 * contact messages and newsletter sign-ups.
 *
 * Every submission is emailed to the sales team and saved under
 * "Enquiries" in the WordPress admin, so nothing is lost if email fails.
 *
 * Shortcodes:
 *   [tata_enquiry_form type="quote"]   Request a quote page (shows the visitor's quote list)
 *   [tata_enquiry_form type="custom"]  Custom manufacturing page (drawing upload)
 *   [tata_enquiry_form type="contact"] Contact page
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the private Enquiries post type.
 */
function tata_register_enquiries() {
	register_post_type(
		'tata_enquiry',
		array(
			'labels'          => array(
				'name'          => __( 'Enquiries', 'tata-surgical' ),
				'singular_name' => __( 'Enquiry', 'tata-surgical' ),
				'menu_name'     => __( 'Enquiries', 'tata-surgical' ),
				'all_items'     => __( 'All enquiries', 'tata-surgical' ),
				'edit_item'     => __( 'Enquiry', 'tata-surgical' ),
				'search_items'  => __( 'Search enquiries', 'tata-surgical' ),
				'not_found'     => __( 'No enquiries yet.', 'tata-surgical' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_position'   => 26,
			'menu_icon'       => 'dashicons-email-alt',
			'supports'        => array( 'title', 'editor' ),
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'tata_register_enquiries' );

/**
 * Enquiry types.
 *
 * @return array
 */
function tata_enquiry_types() {
	return array(
		'quote'      => __( 'Quote request', 'tata-surgical' ),
		'custom'     => __( 'Custom order', 'tata-surgical' ),
		'contact'    => __( 'Contact message', 'tata-surgical' ),
		'newsletter' => __( 'Newsletter sign-up', 'tata-surgical' ),
	);
}

/**
 * Buyer types.
 *
 * @return array
 */
function tata_buyer_types() {
	return array(
		'distributor' => __( 'Distributor or wholesaler', 'tata-surgical' ),
		'oem'         => __( 'OEM or private-label brand', 'tata-surgical' ),
		'hospital'    => __( 'Hospital or clinic', 'tata-surgical' ),
		'beauty'      => __( 'Beauty or retail importer', 'tata-surgical' ),
		'other'       => __( 'Other', 'tata-surgical' ),
	);
}

/**
 * Shortcode: enquiry form.
 *
 * @param array $atts Attributes.
 * @return string
 */
function tata_enquiry_form_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'type' => 'quote' ), $atts, 'tata_enquiry_form' );
	$type = in_array( $atts['type'], array( 'quote', 'custom', 'contact' ), true ) ? $atts['type'] : 'quote';

	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- display only.
	$sent  = isset( $_GET['tata_sent'] ) && $type === $_GET['tata_sent'];
	$error = isset( $_GET['tata_error'] ) ? sanitize_key( wp_unslash( $_GET['tata_error'] ) ) : '';
	// phpcs:enable

	ob_start();

	if ( $sent ) {
		?>
		<div class="form-notice form-notice--success" role="status" <?php echo 'quote' === $type ? 'data-clear-quote' : ''; ?>>
			<?php tata_the_icon( 'i-check' ); ?>
			<div>
				<p class="form-notice__title"><?php esc_html_e( 'Thank you, your request has been sent.', 'tata-surgical' ); ?></p>
				<p><?php esc_html_e( 'Our sales team will reply by email. For anything urgent, message us on WhatsApp.', 'tata-surgical' ); ?></p>
				<a class="btn btn--wa" href="<?php echo esc_url( tata_wa() ); ?>" target="_blank" rel="noopener"><?php tata_the_icon( 'i-wa' ); ?><?php esc_html_e( 'Chat on WhatsApp', 'tata-surgical' ); ?></a>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	$errors = array(
		'required' => __( 'Please fill in your name, company, email and country.', 'tata-surgical' ),
		'email'    => __( 'The email address is not valid. Check it and send again.', 'tata-surgical' ),
		'empty'    => __( 'Add at least one product to your quote list, or describe what you need in the message.', 'tata-surgical' ),
		'file'     => __( 'The file could not be uploaded. Use PDF, JPG, PNG or ZIP, up to 10 MB.', 'tata-surgical' ),
		'consent'  => __( 'Please agree to the privacy policy so we can reply to you.', 'tata-surgical' ),
		'spam'     => __( 'Your request could not be sent. Wait a moment and try again, or email us directly.', 'tata-surgical' ),
	);

	$message_label = array(
		'quote'   => __( 'Notes (sizes, packaging, delivery country, anything else)', 'tata-surgical' ),
		'custom'  => __( 'Describe the instrument: type, size, material, finish, quantity and packaging', 'tata-surgical' ),
		'contact' => __( 'Your message', 'tata-surgical' ),
	);

	$submit = array(
		'quote'   => __( 'Send quote request', 'tata-surgical' ),
		'custom'  => __( 'Send custom order request', 'tata-surgical' ),
		'contact' => __( 'Send message', 'tata-surgical' ),
	);
	?>
	<div class="enquiry">
	<form class="enquiry-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" enctype="multipart/form-data" data-enquiry-form="<?php echo esc_attr( $type ); ?>">
		<?php if ( $error && isset( $errors[ $error ] ) ) : ?>
			<div class="form-notice form-notice--error" role="alert"><?php echo esc_html( $errors[ $error ] ); ?></div>
		<?php endif; ?>

		<input type="hidden" name="action" value="tata_enquiry">
		<input type="hidden" name="tata_type" value="<?php echo esc_attr( $type ); ?>">
		<input type="hidden" name="tata_ts" value="<?php echo esc_attr( time() ); ?>">
		<input type="hidden" name="tata_return" value="<?php echo esc_url( get_permalink() ); ?>">
		<?php wp_nonce_field( 'tata_enquiry', 'tata_nonce' ); ?>
		<div class="hp" aria-hidden="true"><label>Website <input type="text" name="tata_website" tabindex="-1" autocomplete="off"></label></div>

		<?php if ( 'quote' === $type ) : ?>
			<fieldset class="ef-items">
				<legend><?php esc_html_e( 'Products', 'tata-surgical' ); ?></legend>
				<div data-form-items><p class="ef-empty"><?php esc_html_e( 'No products added yet. You can still describe what you need below.', 'tata-surgical' ); ?></p></div>
				<input type="hidden" name="tata_items" value="[]" data-form-items-input>
				<p class="ef-hint"><a href="<?php echo esc_url( tata_products_url() ); ?>"><?php esc_html_e( 'Browse products to add more', 'tata-surgical' ); ?></a></p>
			</fieldset>
		<?php endif; ?>

		<div class="ef-grid">
			<p class="ef-field">
				<label for="ef-name"><?php esc_html_e( 'Your name', 'tata-surgical' ); ?> <span class="req">*</span></label>
				<input id="ef-name" name="tata_name" type="text" autocomplete="name" required>
			</p>
			<p class="ef-field">
				<label for="ef-company"><?php esc_html_e( 'Company', 'tata-surgical' ); ?> <span class="req">*</span></label>
				<input id="ef-company" name="tata_company" type="text" autocomplete="organization" required>
			</p>
			<p class="ef-field">
				<label for="ef-email"><?php esc_html_e( 'Work email', 'tata-surgical' ); ?> <span class="req">*</span></label>
				<input id="ef-email" name="tata_email" type="email" autocomplete="email" required>
			</p>
			<p class="ef-field">
				<label for="ef-phone"><?php esc_html_e( 'Phone or WhatsApp', 'tata-surgical' ); ?></label>
				<input id="ef-phone" name="tata_phone" type="tel" autocomplete="tel">
			</p>
			<p class="ef-field">
				<label for="ef-country"><?php esc_html_e( 'Country', 'tata-surgical' ); ?> <span class="req">*</span></label>
				<input id="ef-country" name="tata_country" type="text" autocomplete="country-name" required>
			</p>
			<p class="ef-field">
				<label for="ef-buyer"><?php esc_html_e( 'You are', 'tata-surgical' ); ?></label>
				<select id="ef-buyer" name="tata_buyer">
					<?php foreach ( tata_buyer_types() as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<p class="ef-field ef-field--wide">
				<label for="ef-message"><?php echo esc_html( $message_label[ $type ] ); ?><?php echo 'custom' === $type ? ' <span class="req">*</span>' : ''; ?></label>
				<textarea id="ef-message" name="tata_message" rows="5" <?php echo 'quote' !== $type ? 'required' : ''; ?>></textarea>
			</p>
			<?php if ( 'contact' !== $type ) : ?>
				<p class="ef-field ef-field--wide">
					<label for="ef-file"><?php echo 'custom' === $type ? esc_html__( 'Drawing, photo or specification', 'tata-surgical' ) : esc_html__( 'Attach a file (optional)', 'tata-surgical' ); ?></label>
					<span class="ef-file">
						<?php tata_the_icon( 'i-pencil' ); ?>
						<input id="ef-file" name="tata_file" type="file" accept=".pdf,.jpg,.jpeg,.png,.zip">
						<span class="ef-file__hint"><?php esc_html_e( 'PDF, JPG, PNG or ZIP, up to 10 MB', 'tata-surgical' ); ?></span>
					</span>
				</p>
			<?php endif; ?>
		</div>

		<p class="ef-consent">
			<label><input type="checkbox" name="tata_consent" value="1" required>
			<?php
			$privacy = get_privacy_policy_url();
			if ( $privacy ) {
				/* translators: %s: privacy policy link. */
				printf( wp_kses( __( 'I agree that Tata Surgical stores my details to reply to this request, as described in the <a href="%s">privacy policy</a>.', 'tata-surgical' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( $privacy ) );
			} else {
				esc_html_e( 'I agree that Tata Surgical stores my details to reply to this request.', 'tata-surgical' );
			}
			?>
			</label>
		</p>

		<button class="btn btn--primary btn--lg" type="submit"><?php echo esc_html( $submit[ $type ] ); ?></button>
	</form>
	<?php tata_enquiry_aside( $type ); ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'tata_enquiry_form', 'tata_enquiry_form_shortcode' );

/**
 * Side panel next to enquiry forms: what happens next, and direct contact.
 *
 * @param string $type Form type.
 */
function tata_enquiry_aside( $type ) {
	$steps = 'custom' === $type
		? array(
			__( 'We review your drawing or sample and may ask a few questions.', 'tata-surgical' ),
			__( 'You receive a quotation with price, lead time and shipping terms.', 'tata-surgical' ),
			__( 'We make a prototype for your approval before production.', 'tata-surgical' ),
		)
		: array(
			__( 'Our sales team checks your list and quantities.', 'tata-surgical' ),
			__( 'You receive a quotation with price, lead time and shipping terms.', 'tata-surgical' ),
			__( 'Samples can be sent before you place your order.', 'tata-surgical' ),
		);
	$qr = tata_opt( 'wechat_qr' );
	?>
	<aside class="enquiry__aside">
		<?php if ( 'contact' !== $type ) : ?>
			<div class="aside-card">
				<p class="aside-card__title"><?php esc_html_e( 'What happens next', 'tata-surgical' ); ?></p>
				<ol class="next-steps">
					<?php foreach ( $steps as $step ) : ?>
						<li><?php echo esc_html( $step ); ?></li>
					<?php endforeach; ?>
				</ol>
			</div>
		<?php endif; ?>
		<div class="aside-card aside-card--contact">
			<p class="aside-card__title"><?php esc_html_e( 'Prefer to talk directly?', 'tata-surgical' ); ?></p>
			<ul class="aside-contact">
				<?php if ( tata_opt( 'whatsapp' ) ) : ?>
					<li><?php tata_the_icon( 'i-wa' ); ?><a href="<?php echo esc_url( tata_wa() ); ?>" target="_blank" rel="noopener"><?php echo esc_html( tata_opt( 'whatsapp' ) ); ?></a></li>
				<?php endif; ?>
				<?php if ( tata_opt( 'email' ) ) : ?>
					<li><?php tata_the_icon( 'i-mail' ); ?><a href="mailto:<?php echo esc_attr( antispambot( tata_opt( 'email' ) ) ); ?>"><?php echo esc_html( antispambot( tata_opt( 'email' ) ) ); ?></a></li>
				<?php endif; ?>
				<?php if ( tata_opt( 'phone' ) ) : ?>
					<li><?php tata_the_icon( 'i-phone' ); ?><a href="<?php echo esc_url( tata_tel( tata_opt( 'phone' ) ) ); ?>"><?php echo esc_html( tata_opt( 'phone' ) ); ?></a></li>
				<?php endif; ?>
			</ul>
			<?php if ( $qr ) : ?>
				<div class="aside-qr">
					<img src="<?php echo esc_url( $qr ); ?>" width="84" height="84" alt="<?php esc_attr_e( 'WeChat QR code for Tata Surgical', 'tata-surgical' ); ?>" loading="lazy">
					<span><?php esc_html_e( 'Scan to chat on WeChat', 'tata-surgical' ); ?></span>
				</div>
			<?php endif; ?>
		</div>
	</aside>
	<?php
}

/**
 * Redirect back to the form with a status.
 *
 * @param string $url  Return URL.
 * @param array  $args Query args.
 */
function tata_enquiry_redirect( $url, $args ) {
	$url = wp_validate_redirect( $url, home_url( '/' ) );
	wp_safe_redirect( add_query_arg( $args, $url ) );
	exit;
}

/**
 * Basic rate limit per IP: 6 submissions per 10 minutes.
 *
 * @return bool True if allowed.
 */
function tata_rate_limit_ok() {
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	$key = 'tata_rl_' . md5( $ip );
	$n   = (int) get_transient( $key );
	if ( $n >= 6 ) {
		return false;
	}
	set_transient( $key, $n + 1, 10 * MINUTE_IN_SECONDS );
	return true;
}

/**
 * Save an uploaded file to wp-content/uploads/tata-enquiries/ with a random
 * name. The folder is blocked from public access; staff download files
 * through the admin.
 *
 * @param array $file Entry from $_FILES.
 * @return string Path relative to the uploads folder, or '' on failure.
 */
function tata_store_enquiry_file( $file ) {
	if ( empty( $file['tmp_name'] ) || ! empty( $file['error'] ) || empty( $file['size'] ) || $file['size'] > 10 * MB_IN_BYTES ) {
		return '';
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';

	$mimes = array(
		'pdf'      => 'application/pdf',
		'jpg|jpeg' => 'image/jpeg',
		'png'      => 'image/png',
		'zip'      => 'application/zip',
	);

	$check = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], $mimes );
	if ( empty( $check['ext'] ) || empty( $check['type'] ) ) {
		return '';
	}

	$set_dir = function ( $dirs ) {
		$dirs['subdir'] = '/tata-enquiries';
		$dirs['path']   = $dirs['basedir'] . '/tata-enquiries';
		$dirs['url']    = $dirs['baseurl'] . '/tata-enquiries';
		return $dirs;
	};
	add_filter( 'upload_dir', $set_dir );

	$result = wp_handle_upload(
		$file,
		array(
			'test_form'                => false,
			'mimes'                    => $mimes,
			'unique_filename_callback' => function ( $dir, $name, $ext ) {
				return wp_generate_password( 20, false ) . '-' . sanitize_file_name( pathinfo( $name, PATHINFO_FILENAME ) ) . $ext;
			},
		)
	);

	remove_filter( 'upload_dir', $set_dir );

	if ( empty( $result['file'] ) || ! empty( $result['error'] ) ) {
		return '';
	}

	tata_protect_enquiry_folder( dirname( $result['file'] ) );

	return 'tata-enquiries/' . basename( $result['file'] );
}

/**
 * Block direct web access to the enquiries folder (Apache and LiteSpeed).
 *
 * @param string $dir Folder path.
 */
function tata_protect_enquiry_folder( $dir ) {
	if ( ! file_exists( $dir . '/.htaccess' ) ) {
		file_put_contents( $dir . '/.htaccess', "<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	}
	if ( ! file_exists( $dir . '/index.php' ) ) {
		file_put_contents( $dir . '/index.php', "<?php\n// Silence is golden.\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	}
}

/**
 * Staff download of an enquiry file.
 */
function tata_download_enquiry_file() {
	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
	if ( ! $post_id || ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'tata_file_' . $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		wp_die( esc_html__( 'You are not allowed to download this file.', 'tata-surgical' ), 403 );
	}

	$relative = get_post_meta( $post_id, '_tata_file_path', true );
	$base     = trailingslashit( wp_get_upload_dir()['basedir'] );
	$path     = realpath( $base . $relative );

	if ( ! $relative || ! $path || 0 !== strpos( $path, realpath( $base . 'tata-enquiries' ) ) || ! is_readable( $path ) ) {
		wp_die( esc_html__( 'File not found.', 'tata-surgical' ), 404 );
	}

	$type = wp_check_filetype( $path );
	nocache_headers();
	header( 'Content-Type: ' . ( $type['type'] ? $type['type'] : 'application/octet-stream' ) );
	header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( preg_replace( '/^[A-Za-z0-9]{20}-/', '', basename( $path ) ) ) . '"' );
	header( 'Content-Length: ' . filesize( $path ) );
	readfile( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	exit;
}
add_action( 'admin_post_tata_enquiry_file', 'tata_download_enquiry_file' );

/**
 * Handle an enquiry submission.
 */
function tata_handle_enquiry() {
	$return = isset( $_POST['tata_return'] ) ? esc_url_raw( wp_unslash( $_POST['tata_return'] ) ) : home_url( '/' );

	if ( ! isset( $_POST['tata_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tata_nonce'] ) ), 'tata_enquiry' ) ) {
		tata_enquiry_redirect( $return, array( 'tata_error' => 'spam' ) );
	}

	$type = isset( $_POST['tata_type'] ) ? sanitize_key( wp_unslash( $_POST['tata_type'] ) ) : 'quote';
	if ( ! in_array( $type, array( 'quote', 'custom', 'contact' ), true ) ) {
		$type = 'quote';
	}

	// Spam checks: honeypot, minimum fill time, rate limit.
	$ts = isset( $_POST['tata_ts'] ) ? (int) $_POST['tata_ts'] : 0;
	if ( ! empty( $_POST['tata_website'] ) || ( time() - $ts ) < 3 || ! tata_rate_limit_ok() ) {
		tata_enquiry_redirect( $return, array( 'tata_error' => 'spam' ) );
	}

	$data = array(
		'name'    => isset( $_POST['tata_name'] ) ? sanitize_text_field( wp_unslash( $_POST['tata_name'] ) ) : '',
		'company' => isset( $_POST['tata_company'] ) ? sanitize_text_field( wp_unslash( $_POST['tata_company'] ) ) : '',
		'email'   => isset( $_POST['tata_email'] ) ? sanitize_email( wp_unslash( $_POST['tata_email'] ) ) : '',
		'phone'   => isset( $_POST['tata_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['tata_phone'] ) ) : '',
		'country' => isset( $_POST['tata_country'] ) ? sanitize_text_field( wp_unslash( $_POST['tata_country'] ) ) : '',
		'buyer'   => isset( $_POST['tata_buyer'] ) ? sanitize_key( wp_unslash( $_POST['tata_buyer'] ) ) : '',
		'message' => isset( $_POST['tata_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['tata_message'] ) ) : '',
	);

	if ( ! $data['name'] || ! $data['company'] || ! $data['email'] || ! $data['country'] ) {
		tata_enquiry_redirect( $return, array( 'tata_error' => 'required' ) );
	}
	if ( ! is_email( $data['email'] ) ) {
		tata_enquiry_redirect( $return, array( 'tata_error' => 'email' ) );
	}
	if ( empty( $_POST['tata_consent'] ) ) {
		tata_enquiry_redirect( $return, array( 'tata_error' => 'consent' ) );
	}

	// Quote list items (sent as JSON by the browser).
	$items = array();
	if ( 'quote' === $type && isset( $_POST['tata_items'] ) ) {
		$raw = json_decode( wp_unslash( $_POST['tata_items'] ), true ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- sanitized per field below.
		if ( is_array( $raw ) ) {
			foreach ( array_slice( $raw, 0, 100 ) as $row ) {
				if ( ! is_array( $row ) ) {
					continue;
				}
				$items[] = array(
					'id'   => isset( $row['id'] ) ? absint( $row['id'] ) : 0,
					'sku'  => isset( $row['sku'] ) ? sanitize_text_field( $row['sku'] ) : '',
					'name' => isset( $row['name'] ) ? sanitize_text_field( $row['name'] ) : '',
					'qty'  => isset( $row['qty'] ) ? max( 1, min( 1000000, absint( $row['qty'] ) ) ) : 1,
				);
			}
		}
	}

	if ( 'quote' === $type && ! $items && ! $data['message'] ) {
		tata_enquiry_redirect( $return, array( 'tata_error' => 'empty' ) );
	}
	if ( 'quote' !== $type && ! $data['message'] ) {
		tata_enquiry_redirect( $return, array( 'tata_error' => 'required' ) );
	}

	// Save.
	$types = tata_enquiry_types();
	$body  = $data['message'];
	if ( $items ) {
		$lines = array();
		foreach ( $items as $item ) {
			$lines[] = sprintf( '%s | %s | %d pcs', $item['sku'] ? $item['sku'] : '-', $item['name'], $item['qty'] );
		}
		$body = implode( "\n", $lines ) . ( $body ? "\n\n" . $body : '' );
	}

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'tata_enquiry',
			'post_status'  => 'private',
			/* translators: 1: enquiry type, 2: company, 3: country. */
			'post_title'   => sprintf( __( '%1$s: %2$s (%3$s)', 'tata-surgical' ), $types[ $type ], $data['company'], $data['country'] ),
			'post_content' => $body,
		)
	);

	if ( ! $post_id || is_wp_error( $post_id ) ) {
		tata_enquiry_redirect( $return, array( 'tata_error' => 'spam' ) );
	}

	update_post_meta( $post_id, '_tata_type', $type );
	update_post_meta( $post_id, '_tata_items', $items );
	foreach ( $data as $key => $value ) {
		if ( 'message' !== $key ) {
			update_post_meta( $post_id, '_tata_' . $key, $value );
		}
	}

	// Optional file, stored in a protected folder (buyers' drawings are confidential).
	$attachment_path = '';
	if ( 'contact' !== $type && ! empty( $_FILES['tata_file']['name'] ) ) {
		$file_path = tata_store_enquiry_file( $_FILES['tata_file'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- validated inside.
		if ( $file_path ) {
			update_post_meta( $post_id, '_tata_file_path', $file_path );
			$attachment_path = trailingslashit( wp_get_upload_dir()['basedir'] ) . $file_path;
		} else {
			update_post_meta( $post_id, '_tata_file_error', 1 );
		}
	}

	// Email the sales team.
	$to      = tata_opt( 'enquiry_email' ) ? tata_opt( 'enquiry_email' ) : tata_opt( 'email' );
	$buyers  = tata_buyer_types();
	$summary = array(
		__( 'Name', 'tata-surgical' )    => $data['name'],
		__( 'Company', 'tata-surgical' ) => $data['company'],
		__( 'Email', 'tata-surgical' )   => $data['email'],
		__( 'Phone', 'tata-surgical' )   => $data['phone'],
		__( 'Country', 'tata-surgical' ) => $data['country'],
		__( 'Buyer', 'tata-surgical' )   => isset( $buyers[ $data['buyer'] ] ) ? $buyers[ $data['buyer'] ] : '',
	);
	$mail = '';
	foreach ( $summary as $label => $value ) {
		$mail .= $label . ': ' . $value . "\n";
	}
	$mail .= "\n" . $body . "\n\n" . admin_url( 'post.php?post=' . $post_id . '&action=edit' );

	wp_mail(
		$to,
		get_the_title( $post_id ),
		$mail,
		array( 'Reply-To: ' . $data['name'] . ' <' . $data['email'] . '>' ),
		$attachment_path ? array( $attachment_path ) : array()
	);

	// Confirmation to the buyer.
	wp_mail(
		$data['email'],
		/* translators: %s: site name. */
		sprintf( __( 'We received your request: %s', 'tata-surgical' ), get_bloginfo( 'name' ) ),
		/* translators: 1: buyer name, 2: request summary, 3: WhatsApp number. */
		sprintf( __( "Dear %1\$s,\n\nThank you for contacting Tata Surgical. We received your request and our sales team will reply by email.\n\n%2\$s\n\nWhatsApp: %3\$s\n\nTata Surgical (Pvt) Ltd, Sialkot, Pakistan", 'tata-surgical' ), $data['name'], $body, tata_opt( 'whatsapp' ) ),
		array( 'Reply-To: ' . $to )
	);

	tata_enquiry_redirect( remove_query_arg( 'tata_error', $return ), array( 'tata_sent' => $type ) );
}
add_action( 'admin_post_nopriv_tata_enquiry', 'tata_handle_enquiry' );
add_action( 'admin_post_tata_enquiry', 'tata_handle_enquiry' );

/**
 * Newsletter sign-up (stored as an enquiry so the team can export it).
 */
function tata_handle_newsletter() {
	$return = isset( $_POST['tata_return'] ) ? esc_url_raw( wp_unslash( $_POST['tata_return'] ) ) : home_url( '/' );

	if ( ! isset( $_POST['tata_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tata_nonce'] ) ), 'tata_newsletter' ) || ! empty( $_POST['tata_website'] ) || ! tata_rate_limit_ok() ) {
		tata_enquiry_redirect( $return . '#newsletter', array( 'tata_news' => 'error' ) );
	}

	$email = isset( $_POST['tata_email'] ) ? sanitize_email( wp_unslash( $_POST['tata_email'] ) ) : '';
	if ( ! is_email( $email ) ) {
		tata_enquiry_redirect( $return . '#newsletter', array( 'tata_news' => 'invalid' ) );
	}

	$existing = get_posts(
		array(
			'post_type'   => 'tata_enquiry',
			'post_status' => 'private',
			'meta_key'    => '_tata_email', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'  => $email, // phpcs:ignore WordPress.DB.SlowDBQuery
			'fields'      => 'ids',
			'numberposts' => 1,
		)
	);

	if ( ! $existing || 'newsletter' !== get_post_meta( $existing[0], '_tata_type', true ) ) {
		$post_id = wp_insert_post(
			array(
				'post_type'   => 'tata_enquiry',
				'post_status' => 'private',
				/* translators: %s: email. */
				'post_title'  => sprintf( __( 'Newsletter sign-up: %s', 'tata-surgical' ), $email ),
			)
		);
		if ( $post_id && ! is_wp_error( $post_id ) ) {
			update_post_meta( $post_id, '_tata_type', 'newsletter' );
			update_post_meta( $post_id, '_tata_email', $email );
		}
	}

	tata_enquiry_redirect( $return . '#newsletter', array( 'tata_news' => 'ok' ) );
}
add_action( 'admin_post_nopriv_tata_newsletter', 'tata_handle_newsletter' );
add_action( 'admin_post_tata_newsletter', 'tata_handle_newsletter' );

/**
 * Admin list columns.
 *
 * @param array $columns Columns.
 * @return array
 */
function tata_enquiry_columns( $columns ) {
	return array(
		'cb'           => $columns['cb'],
		'title'        => __( 'Enquiry', 'tata-surgical' ),
		'tata_type'    => __( 'Type', 'tata-surgical' ),
		'tata_email'   => __( 'Email', 'tata-surgical' ),
		'tata_country' => __( 'Country', 'tata-surgical' ),
		'date'         => $columns['date'],
	);
}
add_filter( 'manage_tata_enquiry_posts_columns', 'tata_enquiry_columns' );

/**
 * Admin list column values.
 *
 * @param string $column  Column.
 * @param int    $post_id Post.
 */
function tata_enquiry_column_values( $column, $post_id ) {
	$types = tata_enquiry_types();
	switch ( $column ) {
		case 'tata_type':
			$type = get_post_meta( $post_id, '_tata_type', true );
			echo esc_html( isset( $types[ $type ] ) ? $types[ $type ] : $type );
			break;
		case 'tata_email':
			$email = get_post_meta( $post_id, '_tata_email', true );
			printf( '<a href="mailto:%1$s">%1$s</a>', esc_attr( $email ) );
			break;
		case 'tata_country':
			echo esc_html( get_post_meta( $post_id, '_tata_country', true ) );
			break;
	}
}
add_action( 'manage_tata_enquiry_posts_custom_column', 'tata_enquiry_column_values', 10, 2 );

/**
 * Details box on the enquiry screen.
 */
function tata_enquiry_metabox() {
	add_meta_box( 'tata_enquiry_details', __( 'Buyer details', 'tata-surgical' ), 'tata_enquiry_metabox_html', 'tata_enquiry', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'tata_enquiry_metabox' );

/**
 * Details box markup.
 *
 * @param WP_Post $post Post.
 */
function tata_enquiry_metabox_html( $post ) {
	$buyers = tata_buyer_types();
	$rows   = array(
		__( 'Name', 'tata-surgical' )    => get_post_meta( $post->ID, '_tata_name', true ),
		__( 'Company', 'tata-surgical' ) => get_post_meta( $post->ID, '_tata_company', true ),
		__( 'Email', 'tata-surgical' )   => get_post_meta( $post->ID, '_tata_email', true ),
		__( 'Phone', 'tata-surgical' )   => get_post_meta( $post->ID, '_tata_phone', true ),
		__( 'Country', 'tata-surgical' ) => get_post_meta( $post->ID, '_tata_country', true ),
		__( 'Buyer', 'tata-surgical' )   => isset( $buyers[ get_post_meta( $post->ID, '_tata_buyer', true ) ] ) ? $buyers[ get_post_meta( $post->ID, '_tata_buyer', true ) ] : '',
	);
	echo '<dl>';
	foreach ( $rows as $label => $value ) {
		if ( $value ) {
			printf( '<dt><strong>%s</strong></dt><dd style="margin:0 0 .6em">%s</dd>', esc_html( $label ), esc_html( $value ) );
		}
	}
	echo '</dl>';
	if ( get_post_meta( $post->ID, '_tata_file_path', true ) ) {
		$url = wp_nonce_url( admin_url( 'admin-post.php?action=tata_enquiry_file&post=' . $post->ID ), 'tata_file_' . $post->ID );
		printf( '<p><a class="button" href="%s">%s</a></p>', esc_url( $url ), esc_html__( 'Download attached file', 'tata-surgical' ) );
	}
	if ( get_post_meta( $post->ID, '_tata_file_error', true ) ) {
		echo '<p>' . esc_html__( 'The buyer tried to attach a file but it was rejected (wrong type or too large).', 'tata-surgical' ) . '</p>';
	}
}
