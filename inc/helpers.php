<?php
/**
 * Helper functions used across templates.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default values for every theme setting.
 * All of these are editable in Appearance > Customize > Tata Surgical.
 *
 * @return array
 */
function tata_option_defaults() {
	return array(
		// Company and contact.
		'established'      => '1984',
		'footer_about'     => __( 'Manufacturer and exporter of single-use and reusable surgical, dental and diagnostic instruments since 1984. Made in Sialkot and supplied to distributors and OEM brands worldwide.', 'tata-surgical' ),
		'markets_title'    => __( 'Europe, USA, Middle East and Asia', 'tata-surgical' ),
		'markets'          => 'United Kingdom, Germany, France, Spain, Netherlands, Switzerland, Italy, USA',
		'email'            => 'info@tatasurgical.com',
		'email_2'          => 'info@tatat.com.pk',
		'enquiry_email'    => '',
		'phone'            => '+92 52 4580 421 / 22',
		'whatsapp'         => '+92 300 8619626',
		'address'          => 'Mughal Street, Muhammad Pura, Commissioner Road, Sialkot 51310, Punjab, Pakistan',
		'map_url'          => 'https://maps.app.goo.gl/Q4cCuXfE9n4CqtMr8',
		'coords'           => '32.49° N, 74.52° E',
		'wechat_qr'        => TATA_URI . '/assets/img/wechat-qr.png',
		'logo_footer'      => TATA_URI . '/assets/img/logo-white.png',
		'wa_greeting'      => __( 'Hello, welcome to Tata Surgical. Looking for an instrument, a catalogue or a custom order? Send us a message.', 'tata-surgical' ),

		// Next exhibition.
		'event_name'       => 'MEDICA 2026',
		'event_city'       => 'Düsseldorf',
		'event_dates'      => '16–19 November 2026',
		'event_month'      => 'Nov',
		'event_days'       => '16–19',
		'event_year'       => '2026',
		'event_location'   => 'Hall 10, Booth C32',
		'event_url'        => '/events/',
		'event_profile_url' => 'https://www.medica-tradefair.com/vis/v1/en/exhprofiles/JVxBy2pXSUGZi4WtnB4L7w',
		'translate_langs'  => 'en,de,fr,es,it,pt,ru,tr,ar,zh-CN',
		'event_note'       => __( 'Meet our directors and see our full range.', 'tata-surgical' ),
		'event_until'      => '2026-11-19',

		// Key pages.
		'quote_url'        => '/request-a-quote/',
		'custom_url'       => '/custom-manufacturing/',
		'oem_url'          => '/oem-private-label/',
		'catalogue_url'    => '/catalogues/',
		'contact_url'      => '/contact/',
		'about_url'        => '/about-us/',
		'quality_url'      => '/quality/',

		// Social.
		'linkedin'         => 'https://www.linkedin.com/in/imtiaz-mughal-2698965b/',
		'facebook'         => 'https://www.facebook.com/tatasurgicalsialkot',
		'instagram'        => 'https://www.instagram.com/surgical_insturments_medical',
		'youtube'          => 'https://www.youtube.com/@tatasurgical',

		// Lists.
		'certifications'   => "ISO 13485\nCE certified\nEU MDR\nISO 14644-1 Class 8 clean room\nGMP",
		'popular_searches' => 'Desmotom, Periodontometer, Artery forceps, Elevator, Needle holder',

	);
}

/**
 * Get a theme setting.
 *
 * @param string $key Setting key.
 * @return string
 */
function tata_opt( $key ) {
	$defaults = tata_option_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	return (string) get_theme_mod( 'tata_' . $key, $default );
}

/**
 * URL setting: values starting with "/" are site-relative.
 *
 * @param string $key Setting key.
 * @return string
 */
function tata_url( $key ) {
	$value = tata_opt( $key );
	if ( '' !== $value && '/' === $value[0] ) {
		return home_url( $value );
	}
	return $value;
}

/**
 * tel: link from a number such as "+92 52 4580 421 / 22" (first number is used).
 *
 * @param string $number Number.
 * @return string
 */
function tata_tel( $number ) {
	$parts = explode( '/', $number );
	return 'tel:' . preg_replace( '/[^0-9+]/', '', $parts[0] );
}

/**
 * WhatsApp link.
 *
 * @param string $text Optional pre-filled message.
 * @return string
 */
function tata_wa( $text = '' ) {
	$digits = preg_replace( '/\D/', '', tata_opt( 'whatsapp' ) );
	return 'https://wa.me/' . $digits . ( $text ? '?text=' . rawurlencode( $text ) : '' );
}

/**
 * Inline icon that references the SVG sprite printed after <body>.
 *
 * @param string $name  Icon id without prefix.
 * @param string $class Extra CSS class.
 * @return string
 */
function tata_icon( $name, $class = '' ) {
	return sprintf(
		'<svg class="icon%1$s" aria-hidden="true" focusable="false"><use href="#%2$s"></use></svg>',
		$class ? ' ' . esc_attr( $class ) : '',
		esc_attr( $name )
	);
}

/**
 * Echo an icon.
 *
 * @param string $name  Icon id.
 * @param string $class Extra CSS class.
 */
function tata_the_icon( $name, $class = '' ) {
	echo tata_icon( $name, $class ); // phpcs:ignore WordPress.Security.EscapeOutput -- built from escaped parts.
}

/**
 * Whether the next exhibition should be shown.
 *
 * @return bool
 */
function tata_event_active() {
	if ( '' === trim( tata_opt( 'event_name' ) ) ) {
		return false;
	}
	$until = tata_opt( 'event_until' );
	return ! ( $until && current_time( 'Y-m-d' ) > $until );
}

/**
 * Certifications as array of [code, meaning].
 *
 * @return array
 */
function tata_certifications() {
	$out = array();
	foreach ( preg_split( '/\r\n|\r|\n/', tata_opt( 'certifications' ) ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		$out[] = array( $parts[0], isset( $parts[1] ) ? $parts[1] : '' );
	}
	return $out;
}

/**
 * Popular search terms.
 *
 * @return array
 */
function tata_popular_searches() {
	return array_filter( array_map( 'trim', explode( ',', tata_opt( 'popular_searches' ) ) ) );
}

/**
 * Social profiles.
 *
 * @return array network => url
 */
function tata_social_links() {
	$links = array();
	foreach ( array( 'linkedin', 'facebook', 'instagram', 'youtube' ) as $network ) {
		$url = tata_opt( $network );
		if ( $url ) {
			$links[ $network ] = $url;
		}
	}
	return $links;
}

/**
 * Languages from WPML or Polylang, if either is active.
 *
 * @return array List of [code, name, url, current].
 */
function tata_languages() {
	$out = array();

	if ( function_exists( 'pll_the_languages' ) ) {
		$langs = pll_the_languages( array( 'raw' => 1, 'hide_if_empty' => 0 ) );
		foreach ( (array) $langs as $lang ) {
			$out[] = array(
				'code'    => strtoupper( $lang['slug'] ),
				'name'    => $lang['name'],
				'url'     => $lang['url'],
				'current' => ! empty( $lang['current_lang'] ),
			);
		}
	} elseif ( has_filter( 'wpml_active_languages' ) ) {
		$langs = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
		foreach ( (array) $langs as $lang ) {
			$out[] = array(
				'code'    => strtoupper( $lang['language_code'] ),
				'name'    => $lang['native_name'],
				'url'     => $lang['url'],
				'current' => ! empty( $lang['active'] ),
			);
		}
	}

	return $out;
}

/**
 * Header logo: Customizer logo, else the bundled Tata logo.
 */
function tata_header_logo() {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	printf(
		'<a class="brand-logo notranslate" href="%1$s" rel="home"><img src="%2$s" width="562" height="188" alt="%3$s"></a>',
		esc_url( home_url( '/' ) ),
		esc_url( TATA_URI . '/assets/img/logo.png' ),
		esc_attr( get_bloginfo( 'name' ) )
	);
}

/**
 * Whether WooCommerce is active.
 *
 * @return bool
 */
function tata_has_wc() {
	return class_exists( 'WooCommerce' );
}

/**
 * URL of the main products page.
 *
 * @return string
 */
function tata_products_url() {
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		$shop = wc_get_page_permalink( 'shop' );
		if ( $shop ) {
			return $shop;
		}
	}
	return home_url( '/products/' );
}

/**
 * Data attributes for an "Add to quote" button.
 *
 * @param WC_Product $product Product.
 * @return string
 */
function tata_quote_attrs( $product ) {
	$cats = wc_get_product_terms( $product->get_id(), 'product_cat', array( 'fields' => 'names' ) );
	return sprintf(
		'data-add-quote data-id="%1$d" data-sku="%2$s" data-name="%3$s" data-url="%4$s" data-cat="%5$s"',
		(int) $product->get_id(),
		esc_attr( $product->get_sku() ),
		esc_attr( wp_strip_all_tags( $product->get_name() ) ),
		esc_url( $product->get_permalink() ),
		esc_attr( $cats ? $cats[0] : '' )
	);
}

/**
 * Whether to show the custom-order panel above the footer.
 * Hidden on the custom manufacturing and quote pages, where it would repeat the page.
 *
 * @return bool
 */
function tata_show_footer_cta() {
	$show = ! is_page_template( array( 'page-templates/request-quote.php', 'page-templates/custom-order.php' ) );
	if ( $show && is_page() ) {
		$here = untrailingslashit( (string) get_permalink() );
		foreach ( array( 'custom_url', 'quote_url' ) as $key ) {
			if ( untrailingslashit( tata_url( $key ) ) === $here ) {
				$show = false;
			}
		}
	}
	return (bool) apply_filters( 'tata_show_footer_cta', $show );
}

/**
 * Markets list.
 *
 * @return array
 */
function tata_markets() {
	return array_filter( array_map( 'trim', explode( ',', tata_opt( 'markets' ) ) ) );
}

/**
 * Languages offered by the built-in translator (Google Translate).
 *
 * @return array code => [label, native name]
 */
function tata_translate_languages() {
	$names = array(
		'en'    => array( 'EN', 'English' ),
		'de'    => array( 'DE', 'Deutsch' ),
		'fr'    => array( 'FR', 'Français' ),
		'es'    => array( 'ES', 'Español' ),
		'it'    => array( 'IT', 'Italiano' ),
		'pt'    => array( 'PT', 'Português' ),
		'nl'    => array( 'NL', 'Nederlands' ),
		'pl'    => array( 'PL', 'Polski' ),
		'ru'    => array( 'RU', 'Русский' ),
		'tr'    => array( 'TR', 'Türkçe' ),
		'ar'    => array( 'AR', 'العربية' ),
		'fa'    => array( 'FA', 'فارسی' ),
		'ur'    => array( 'UR', 'اردو' ),
		'zh-CN' => array( 'ZH', '中文' ),
		'ja'    => array( 'JA', '日本語' ),
		'ko'    => array( 'KO', '한국어' ),
	);
	$out = array();
	foreach ( array_map( 'trim', explode( ',', tata_opt( 'translate_langs' ) ) ) as $code ) {
		if ( isset( $names[ $code ] ) ) {
			$out[ $code ] = $names[ $code ];
		}
	}
	if ( $out && ! isset( $out['en'] ) ) {
		$out = array( 'en' => $names['en'] ) + $out;
	}
	return count( $out ) > 1 ? $out : array();
}

/**
 * Language currently chosen in the translator (from the googtrans cookie).
 *
 * @return string
 */
function tata_translate_current() {
	$cookie = isset( $_COOKIE['googtrans'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['googtrans'] ) ) : '';
	$parts  = explode( '/', trim( $cookie, '/' ) );
	$code   = isset( $parts[1] ) ? $parts[1] : 'en';
	return array_key_exists( $code, tata_translate_languages() ) ? $code : 'en';
}
