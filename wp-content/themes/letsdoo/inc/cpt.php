<?php
/**
 * Custom post types used to back the repeatable sections of the design
 * (services, team members, partner references). None of these are
 * publicly routed — they're admin-managed lists pulled into the page
 * templates via WP_Query.
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
		'public'             => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'menu_icon'          => 'dashicons-star-filled',
		'supports'           => array( 'title', 'thumbnail', 'page-attributes' ),
		'has_archive'        => false,
		'exclude_from_search'=> true,
		'publicly_queryable' => false,
	) );
}
add_action( 'init', 'letsdoo_register_post_types' );

/**
 * page-attributes only exposes the order box, not a "Parent" dropdown,
 * for these flat post types — but WP still shows the meta box; nothing
 * further to do here. Ordered queries use orderby => menu_order.
 */
