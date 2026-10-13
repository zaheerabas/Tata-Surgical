<?php
/**
 * Template Name: Request a quote
 *
 * Shows the visitor's quote list with quantities, the request form and contact options.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;

get_template_part(
	'template-parts/enquiry-page',
	null,
	array(
		'type'   => 'quote',
		'kicker' => __( 'Quotation', 'tata-surgical' ),
		'intro'  => __( 'Review your list, set quantities and tell us where to deliver. We reply with prices, lead time and shipping terms.', 'tata-surgical' ),
		'steps'  => array(
			array( 'i-quote', __( 'Your list', 'tata-surgical' ), __( 'Products and quantities', 'tata-surgical' ) ),
			array( 'i-mail', __( 'Quotation', 'tata-surgical' ), __( 'Prices, lead time, shipping', 'tata-surgical' ) ),
			array( 'i-box', __( 'Samples', 'tata-surgical' ), __( 'Before you place your order', 'tata-surgical' ) ),
		),
	)
);
