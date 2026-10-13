<?php
/**
 * Template Name: Contact
 *
 * Contact form with direct contact options. Any text in the page editor is shown above the form.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

get_template_part(
	'template-parts/enquiry-page',
	null,
	array(
		'type'   => 'contact',
		'kicker' => __( 'Contact', 'tata-surgical' ),
		'intro'  => __( 'Write to our sales team, or reach us directly by WhatsApp, WeChat, phone or email.', 'tata-surgical' ),
		'steps'  => array(),
	)
);
