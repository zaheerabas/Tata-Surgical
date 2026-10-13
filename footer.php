<?php
/**
 * Site footer.
 *
 * @package TataSurgical
 */

defined( 'ABSPATH' ) || exit;
?>
</main>

<?php
if ( ! tata_elementor_location( 'footer' ) ) :
	if ( tata_show_footer_cta() ) {
		get_template_part( 'template-parts/footer/cta' );
	}
	get_template_part( 'template-parts/footer/main' );
endif;

get_template_part( 'template-parts/footer/whatsapp' );

wp_footer();
?>
</body>
</html>
