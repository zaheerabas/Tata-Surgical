<?php
/**
 * Customizer: Appearance > Customize > Tata Surgical.
 * Staff can edit contact details, exhibition, links and homepage text without code.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

/**
 * Field definitions: key => [label, section, type, description].
 *
 * @return array
 */
function tata_customizer_fields() {
	return array(
		// Contact.
		'established'      => array( __( 'Year established', 'tata-surgical' ), 'tata_contact', 'text', '' ),
		'email'            => array( __( 'Main email', 'tata-surgical' ), 'tata_contact', 'email', '' ),
		'email_2'          => array( __( 'Second email', 'tata-surgical' ), 'tata_contact', 'email', __( 'Optional.', 'tata-surgical' ) ),
		'enquiry_email'    => array( __( 'Send enquiries to', 'tata-surgical' ), 'tata_contact', 'email', __( 'Quote and custom-order requests are emailed here. Leave empty to use the main email.', 'tata-surgical' ) ),
		'phone'            => array( __( 'Office phone', 'tata-surgical' ), 'tata_contact', 'text', '' ),
		'whatsapp'         => array( __( 'WhatsApp number', 'tata-surgical' ), 'tata_contact', 'text', __( 'With country code, e.g. +92 300 8619626', 'tata-surgical' ) ),
		'address'          => array( __( 'Factory address', 'tata-surgical' ), 'tata_contact', 'textarea', '' ),
		'map_url'          => array( __( 'Google Maps link', 'tata-surgical' ), 'tata_contact', 'url', '' ),
		'coords'           => array( __( 'Coordinates line (footer)', 'tata-surgical' ), 'tata_contact', 'text', '' ),
		'wechat_qr'        => array( __( 'WeChat QR code', 'tata-surgical' ), 'tata_contact', 'image', __( 'Remove to hide WeChat.', 'tata-surgical' ) ),
		'wa_greeting'      => array( __( 'WhatsApp chat greeting', 'tata-surgical' ), 'tata_contact', 'textarea', '' ),

		// Exhibition.
		'event_name'       => array( __( 'Exhibition name', 'tata-surgical' ), 'tata_event', 'text', __( 'Leave empty to hide the exhibition everywhere.', 'tata-surgical' ) ),
		'event_city'       => array( __( 'City', 'tata-surgical' ), 'tata_event', 'text', '' ),
		'event_dates'      => array( __( 'Dates (full text)', 'tata-surgical' ), 'tata_event', 'text', __( 'e.g. 16–19 November 2026', 'tata-surgical' ) ),
		'event_month'      => array( __( 'Calendar tile: month', 'tata-surgical' ), 'tata_event', 'text', __( 'e.g. Nov', 'tata-surgical' ) ),
		'event_days'       => array( __( 'Calendar tile: days', 'tata-surgical' ), 'tata_event', 'text', __( 'e.g. 16–19', 'tata-surgical' ) ),
		'event_year'       => array( __( 'Calendar tile: year', 'tata-surgical' ), 'tata_event', 'text', '' ),
		'event_location'   => array( __( 'Hall and booth', 'tata-surgical' ), 'tata_event', 'text', '' ),
		'event_profile_url' => array( __( 'Exhibitor profile link', 'tata-surgical' ), 'tata_event', 'url', __( 'Your page on the trade fair website. The hall and booth in the top bar link here.', 'tata-surgical' ) ),
		'event_url'        => array( __( '"Book a meeting" link', 'tata-surgical' ), 'tata_event', 'text', '' ),
		'event_note'       => array( __( 'Short note (footer card)', 'tata-surgical' ), 'tata_event', 'text', '' ),
		'event_until'      => array( __( 'Hide after', 'tata-surgical' ), 'tata_event', 'date', __( 'The exhibition disappears automatically after this date.', 'tata-surgical' ) ),

		// Links.
		'quote_url'        => array( __( 'Request a quote page', 'tata-surgical' ), 'tata_links', 'text', __( 'Site path like /request-a-quote/ or a full URL.', 'tata-surgical' ) ),
		'custom_url'       => array( __( 'Custom manufacturing page', 'tata-surgical' ), 'tata_links', 'text', '' ),
		'oem_url'          => array( __( 'OEM and private label page', 'tata-surgical' ), 'tata_links', 'text', '' ),
		'catalogue_url'    => array( __( 'Catalogues page', 'tata-surgical' ), 'tata_links', 'text', '' ),
		'quality_url'      => array( __( 'Quality page', 'tata-surgical' ), 'tata_links', 'text', '' ),
		'about_url'        => array( __( 'About page', 'tata-surgical' ), 'tata_links', 'text', '' ),
		'contact_url'      => array( __( 'Contact page', 'tata-surgical' ), 'tata_links', 'text', '' ),

		// Translator.
		'translate_langs'  => array( __( 'Translator languages', 'tata-surgical' ), 'tata_footer', 'text', __( 'Comma separated codes: en, de, fr, es, it, pt, nl, pl, ru, tr, ar, fa, ur, zh-CN, ja, ko. Leave empty to hide the language menu. If WPML or Polylang is active, their languages are used instead.', 'tata-surgical' ) ),

		// Social.
		'linkedin'         => array( 'LinkedIn', 'tata_social', 'url', '' ),
		'facebook'         => array( 'Facebook', 'tata_social', 'url', '' ),
		'instagram'        => array( 'Instagram', 'tata_social', 'url', '' ),
		'youtube'          => array( 'YouTube', 'tata_social', 'url', '' ),

		// Footer and search.
		'footer_about'     => array( __( 'Short company description', 'tata-surgical' ), 'tata_footer', 'textarea', '' ),
		'markets_title'    => array( __( 'Markets heading', 'tata-surgical' ), 'tata_footer', 'text', __( 'e.g. Europe, USA, Middle East and Asia', 'tata-surgical' ) ),
		'markets'          => array( __( 'Countries you supply', 'tata-surgical' ), 'tata_footer', 'text', __( 'Comma separated.', 'tata-surgical' ) ),
		'logo_footer'      => array( __( 'Footer logo (white)', 'tata-surgical' ), 'tata_footer', 'image', '' ),
		'certifications'   => array( __( 'Certifications', 'tata-surgical' ), 'tata_footer', 'textarea', __( 'One per line.', 'tata-surgical' ) ),
		'popular_searches' => array( __( 'Popular searches', 'tata-surgical' ), 'tata_footer', 'text', __( 'Shown in the search window. Comma separated.', 'tata-surgical' ) ),

	);
}

/**
 * Register settings.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function tata_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'tata_panel',
		array(
			'title'    => __( 'Tata Surgical', 'tata-surgical' ),
			'priority' => 25,
		)
	);

	$sections = array(
		'tata_contact' => __( 'Contact details', 'tata-surgical' ),
		'tata_event'   => __( 'Next exhibition', 'tata-surgical' ),
		'tata_links'   => __( 'Key page links', 'tata-surgical' ),
		'tata_social'  => __( 'Social profiles', 'tata-surgical' ),
		'tata_footer'  => __( 'Footer, search and languages', 'tata-surgical' ),
	);
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section(
			$id,
			array(
				'title' => $title,
				'panel' => 'tata_panel',
			)
		);
	}

	$defaults = tata_option_defaults();

	foreach ( tata_customizer_fields() as $key => $field ) {
		list( $label, $section, $type, $description ) = $field;

		switch ( $type ) {
			case 'email':
				$sanitize = 'sanitize_email';
				break;
			case 'url':
			case 'image':
				$sanitize = 'esc_url_raw';
				break;
			case 'textarea':
				$sanitize = 'sanitize_textarea_field';
				break;
			default:
				$sanitize = 'sanitize_text_field';
		}

		$wp_customize->add_setting(
			'tata_' . $key,
			array(
				'default'           => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '',
				'sanitize_callback' => $sanitize,
			)
		);

		$args = array(
			'label'       => $label,
			'section'     => $section,
			'description' => $description,
		);

		if ( 'image' === $type ) {
			$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'tata_' . $key, $args ) );
		} else {
			$args['type'] = $type;
			$wp_customize->add_control( 'tata_' . $key, $args );
		}
	}
}
add_action( 'customize_register', 'tata_customize_register' );
