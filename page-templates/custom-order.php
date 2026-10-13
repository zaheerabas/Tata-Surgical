<?php
/**
 * Template Name: Custom manufacturing
 *
 * Request form for instruments made to the buyer's drawing or sample, with file upload.
 * Any text in the page editor is shown above the form.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

get_template_part(
	'template-parts/enquiry-page',
	null,
	array(
		'type'   => 'custom',
		'kicker' => __( 'Made to your specification', 'tata-surgical' ),
		'intro'  => __( 'Send a drawing, a photo or a sample. We make new instruments or variants of existing ones, with your size, finish, logo and packaging.', 'tata-surgical' ),
		'steps'  => array(
			array( 'i-pencil', __( 'Drawing or sample', 'tata-surgical' ), __( 'PDF, photo or physical sample', 'tata-surgical' ) ),
			array( 'i-check', __( 'Prototype', 'tata-surgical' ), __( 'Approved by you first', 'tata-surgical' ) ),
			array( 'i-globe', __( 'Production', 'tata-surgical' ), __( 'Shipped with export documents', 'tata-surgical' ) ),
		),
	)
);
