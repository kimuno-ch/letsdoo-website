<?php
/**
 * Font Awesome is provided by the "Advanced Custom Fields: Font Awesome"
 * plugin. header.php/footer.php render icons on every page without going
 * through an ACF field at all (mail/phone/social links), so loading FA
 * can't be conditional on a font-awesome field being present — it has to
 * be unconditional.
 *
 * The plugin's own ACFFA_always_enqueue_fa hooks wp_enqueue_scripts, but
 * that fires too early to work here: ACF only loads a field type's PHP
 * class the first time a field of that type is actually read via
 * get_field(), which for us happens later, while the template body
 * renders — well after wp_enqueue_scripts already ran. That leaves
 * ACFFA_get_fa_url unregistered at head-enqueue time, so the plugin's own
 * auto-enqueue silently does nothing.
 *
 * Enqueueing on wp_footer instead sidesteps that: by then the page body
 * (and its get_field() calls) has already rendered, so the plugin's
 * classes are loaded and ACFFA_get_fa_url works. This is the same
 * footer-enqueue the plugin uses itself for its per-field "Enqueue
 * FontAwesome?" option — we're just making it unconditional.
 */
add_action( 'wp_footer', 'letsdoo_enqueue_font_awesome', 1 );
function letsdoo_enqueue_font_awesome() {
	$fa_url = apply_filters( 'ACFFA_get_fa_url', '' );
	if ( ! $fa_url ) {
		return;
	}
	if ( 0 === stripos( $fa_url, 'https://kit.fontawesome.com/' ) ) {
		wp_enqueue_script( 'acffa_font-awesome-kit', $fa_url, array(), null, true );
	} else {
		wp_enqueue_style( 'acffa_font-awesome', $fa_url, array(), null );
	}
	wp_print_styles();
	wp_print_scripts();
}
