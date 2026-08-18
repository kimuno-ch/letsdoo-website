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
 * order, and keep 23-mobile last among the shared files.
 *
 * 24-standort and 25-blocks are the exceptions, and sit after it: their
 * selectors appear nowhere else and 23-mobile never targets them, so they have
 * nothing to override and carry their own breakpoints instead.
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
		/*
		 * 16-angebote-leistungen is gone: the Angebote page rendered its own
		 * near-copy of the Leistungen card (.leistung-bento-card) and drifted
		 * out of step with the real one the moment that was redesigned. It uses
		 * .leistung-card from 07-leistungen.css now, like the Startseite and
		 * the Leistungen block. The numbering is left with the hole rather than
		 * renumbering every file below it.
		 */
		'17-angebote-vertrauen',
		'18-angebote-pakete',
		'19-angebote-cta',
		'20-footer',
		'21-blog-listing',
		'22-blog-single',
		'23-mobile',
		'24-standort',
		'25-blocks',
		'26-kontakt-modal',
		'27-single-leistung',
		'28-plattform',
	);
}

/**
 * Cache-busting version for one theme asset: the file's own modification time.
 *
 * These used to all go out as ?ver=<theme version>, which never changes while
 * the theme is being worked on — so an edited stylesheet kept its old URL and
 * browsers went on serving the copy they had cached, making changes look like
 * they hadn't been applied. Keyed on mtime the URL moves whenever the file
 * does, and only then. Falls back to the theme version if the file is missing.
 */
function letsdoo_asset_version( $relative_path ) {
	$file = get_theme_file_path( $relative_path );

	if ( file_exists( $file ) ) {
		return (string) filemtime( $file );
	}

	return wp_get_theme()->get( 'Version' );
}

function letsdoo_enqueue_assets() {
	wp_enqueue_style( 'letsdoo-style', get_stylesheet_uri(), array(), letsdoo_asset_version( '/style.css' ) );

	$depends_on = array( 'letsdoo-style' );
	foreach ( letsdoo_style_parts() as $part ) {
		$handle = 'letsdoo-' . $part;
		$path   = "/assets/css/{$part}.css";
		wp_enqueue_style( $handle, get_theme_file_uri( $path ), $depends_on, letsdoo_asset_version( $path ) );
		$depends_on = array( $handle );
	}

	wp_enqueue_script(
		'letsdoo-navigation',
		get_theme_file_uri( '/assets/js/navigation.js' ),
		array(),
		letsdoo_asset_version( '/assets/js/navigation.js' ),
		true
	);

	wp_enqueue_script(
		'letsdoo-kontakt-modal',
		get_theme_file_uri( '/assets/js/kontakt-modal.js' ),
		array(),
		letsdoo_asset_version( '/assets/js/kontakt-modal.js' ),
		true
	);

	wp_enqueue_script(
		'letsdoo-zahlen-count',
		get_theme_file_uri( '/assets/js/zahlen-count.js' ),
		array(),
		letsdoo_asset_version( '/assets/js/zahlen-count.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'letsdoo_enqueue_assets' );

/**
 * The wave dividers between the photo and white areas come in three shapes,
 * picked in CSS from the section's position on the page (see 05-sections.css).
 * Position alone would put the same shape under every hero, so this rotates the
 * sequence per page. Derived from the queried object ID rather than randomised,
 * so a given page keeps the same waves on every visit and page caching can't
 * serve a mismatched body class.
 */
function letsdoo_wave_seed_class( $classes ) {
	$classes[] = 'wave-seed-' . ( absint( get_queried_object_id() ) % 3 );

	return $classes;
}
add_filter( 'body_class', 'letsdoo_wave_seed_class' );

require get_theme_file_path( '/inc/cpt.php' );
require get_theme_file_path( '/inc/acf-fields.php' );
require get_theme_file_path( '/inc/settings-page.php' );
require get_theme_file_path( '/inc/template-helpers.php' );
require get_theme_file_path( '/inc/seo.php' );
require get_theme_file_path( '/inc/font-awesome.php' );
/* After template-helpers.php — the block render templates call letsdoo_button()
   and letsdoo_page_url_by_template(). */
require get_theme_file_path( '/inc/blocks.php' );
