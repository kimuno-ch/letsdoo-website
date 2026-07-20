<?php

function letsdoo_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'letsdoo' ),
	) );
}
add_action( 'after_setup_theme', 'letsdoo_setup' );

function letsdoo_enqueue_assets() {
	$version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style( 'letsdoo-style', get_stylesheet_uri(), array(), $version );

	wp_enqueue_script(
		'letsdoo-navigation',
		get_theme_file_uri( '/assets/js/navigation.js' ),
		array(),
		$version,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'letsdoo_enqueue_assets' );
