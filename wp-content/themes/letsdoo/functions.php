<?php

function letsdoo_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 120,
		'width'       => 140,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

	// Needed by the Referenz case-study pages: editors build those with core
	// blocks, so embedded videos have to scale and images need to be able to
	// break out of the text column.
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );

	register_nav_menus( array(
		'primary' => __( 'Hauptmenü', 'letsdoo' ),
		'footer'  => __( 'Footer-Navigation', 'letsdoo' ),
	) );
}
add_action( 'after_setup_theme', 'letsdoo_setup' );

/**
 * The stylesheet is split into one file per section under assets/css/.
 * CSS is order-sensitive (later rules win, and 23-mobile overrides the rest),
 * so each part is registered with the previous one as its dependency — that
 * makes WordPress emit them in exactly this order. Keep the list in cascade
 * order, and keep 23-mobile last.
 */
function letsdoo_style_parts() {
	return array(
		'01-base',
		'02-buttons',
		'03-header',
		'04-hero',
		'05-sections',
		'06-warum-odoo',
		'07-leistungen',
		'08-warum-letsdoo',
		'09-vorgehen',
		'10-cta-band',
		'11-referenzen',
		'12-single-referenz',
		'13-zahlen',
		'14-team',
		'15-kontakt',
		'16-angebote-leistungen',
		'17-angebote-vertrauen',
		'18-angebote-pakete',
		'19-angebote-cta',
		'20-footer',
		'21-blog-listing',
		'22-blog-single',
		'23-mobile',
	);
}

function letsdoo_enqueue_assets() {
	$version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style( 'letsdoo-style', get_stylesheet_uri(), array(), $version );

	$depends_on = array( 'letsdoo-style' );
	foreach ( letsdoo_style_parts() as $part ) {
		$handle = 'letsdoo-' . $part;
		wp_enqueue_style( $handle, get_theme_file_uri( "/assets/css/{$part}.css" ), $depends_on, $version );
		$depends_on = array( $handle );
	}

	wp_enqueue_script(
		'letsdoo-navigation',
		get_theme_file_uri( '/assets/js/navigation.js' ),
		array(),
		$version,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'letsdoo_enqueue_assets' );

require get_theme_file_path( '/inc/cpt.php' );
require get_theme_file_path( '/inc/acf-fields.php' );
require get_theme_file_path( '/inc/settings-page.php' );
require get_theme_file_path( '/inc/template-helpers.php' );
