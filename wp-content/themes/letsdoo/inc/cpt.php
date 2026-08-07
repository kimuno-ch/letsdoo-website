<?php
/**
 * Custom post types used to back the repeatable sections of the design
 * (services, team members, partner references). Most of these are not
 * publicly routed — they're admin-managed lists pulled into the page
 * templates via WP_Query.
 *
 * `referenz` is the exception: it has its own single view (single-referenz.php)
 * so each reference can be shown as a full case study, so it's registered
 * public with the block editor enabled.
 */

function letsdoo_register_post_types() {

	register_post_type( 'leistung', array(
		'labels' => array(
			'name'               => __( 'Leistungen', 'letsdoo' ),
			'singular_name'      => __( 'Leistung', 'letsdoo' ),
			'add_new_item'       => __( 'Neue Leistung hinzufügen', 'letsdoo' ),
			'edit_item'          => __( 'Leistung bearbeiten', 'letsdoo' ),
			'all_items'          => __( 'Leistungen', 'letsdoo' ),
			'menu_name'          => __( 'Leistungen', 'letsdoo' ),
		),
		'public'             => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'menu_icon'          => 'dashicons-hammer',
		'supports'           => array( 'title', 'page-attributes' ),
		'has_archive'        => false,
		'exclude_from_search'=> true,
		'publicly_queryable' => false,
	) );

	/*
	 * The Vorgehen timeline. A post type rather than a repeater field because
	 * repeaters are ACF Pro only and this install runs the free build — a
	 * repeater simply never appears in the admin. Ordering is by menu_order, so
	 * the steps are rearranged with the "Reihenfolge" box like the other lists.
	 */
	register_post_type( 'vorgehen_schritt', array(
		'labels' => array(
			'name'               => __( 'Vorgehen', 'letsdoo' ),
			'singular_name'      => __( 'Schritt', 'letsdoo' ),
			'add_new_item'       => __( 'Neuen Schritt hinzufügen', 'letsdoo' ),
			'edit_item'          => __( 'Schritt bearbeiten', 'letsdoo' ),
			'all_items'          => __( 'Vorgehen', 'letsdoo' ),
			'menu_name'          => __( 'Vorgehen', 'letsdoo' ),
		),
		'public'             => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'menu_icon'          => 'dashicons-chart-line',
		'supports'           => array( 'title', 'page-attributes' ),
		'has_archive'        => false,
		'exclude_from_search'=> true,
		'publicly_queryable' => false,
	) );

	register_post_type( 'team_mitglied', array(
		'labels' => array(
			'name'               => __( 'Team', 'letsdoo' ),
			'singular_name'      => __( 'Teammitglied', 'letsdoo' ),
			'add_new_item'       => __( 'Neues Teammitglied hinzufügen', 'letsdoo' ),
			'edit_item'          => __( 'Teammitglied bearbeiten', 'letsdoo' ),
			'all_items'          => __( 'Team', 'letsdoo' ),
			'menu_name'          => __( 'Team', 'letsdoo' ),
		),
		'public'             => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'menu_icon'          => 'dashicons-groups',
		'supports'           => array( 'title', 'thumbnail', 'page-attributes' ),
		'has_archive'        => false,
		'exclude_from_search'=> true,
		'publicly_queryable' => false,
	) );

	register_post_type( 'referenz', array(
		'labels' => array(
			'name'               => __( 'Referenzen', 'letsdoo' ),
			'singular_name'      => __( 'Referenz', 'letsdoo' ),
			'add_new_item'       => __( 'Neue Referenz hinzufügen', 'letsdoo' ),
			'edit_item'          => __( 'Referenz bearbeiten', 'letsdoo' ),
			'all_items'          => __( 'Referenzen', 'letsdoo' ),
			'menu_name'          => __( 'Referenzen', 'letsdoo' ),
		),
		'public'             => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_rest'       => true,
		'menu_icon'          => 'dashicons-star-filled',
		'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
		'has_archive'        => false,
		'exclude_from_search'=> false,
		'publicly_queryable' => true,
		'rewrite'            => array( 'slug' => 'referenzen', 'with_front' => false ),
	) );

	register_post_type( 'angebot_paket', array(
		'labels' => array(
			'name'               => __( 'Pakete', 'letsdoo' ),
			'singular_name'      => __( 'Paket', 'letsdoo' ),
			'add_new_item'       => __( 'Neues Paket hinzufügen', 'letsdoo' ),
			'edit_item'          => __( 'Paket bearbeiten', 'letsdoo' ),
			'all_items'          => __( 'Pakete', 'letsdoo' ),
			'menu_name'          => __( 'Pakete', 'letsdoo' ),
		),
		'public'             => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'menu_icon'          => 'dashicons-cart',
		'supports'           => array( 'title', 'page-attributes' ),
		'has_archive'        => false,
		'exclude_from_search'=> true,
		'publicly_queryable' => false,
	) );
}
add_action( 'init', 'letsdoo_register_post_types' );

/**
 * Pre-fills the post title on "Paket hinzufügen" with example content, so
 * the field-level ACF defaults (see inc/acf-fields.php) don't sit next to
 * an empty, unhelpful title field.
 */
add_filter( 'default_title', function ( $title, $post ) {
	if ( $post && 'angebot_paket' === $post->post_type ) {
		return 'Starter Basic';
	}
	return $title;
}, 10, 2 );

/**
 * page-attributes only exposes the order box, not a "Parent" dropdown,
 * for these flat post types — but WP still shows the meta box; nothing
 * further to do here. Ordered queries use orderby => menu_order.
 */
