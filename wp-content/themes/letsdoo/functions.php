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

function letsdoo_enqueue_assets() {
	$version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style( 'letsdoo-style', get_stylesheet_uri(), array(), $version );
	wp_enqueue_style( 'letsdoo-theme', get_theme_file_uri( '/assets/css/theme.css' ), array( 'letsdoo-style' ), $version );

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
