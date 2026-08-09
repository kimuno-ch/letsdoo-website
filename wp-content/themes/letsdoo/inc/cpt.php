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
	/*
	 * Local SEO landing pages — one per town, targeting "Odoo <Ort>" searches.
	 *
	 * Public and indexable, but deliberately unreachable by browsing: nothing in
	 * the theme links to them, has_archive is false so there is no index, and
	 * show_in_nav_menus keeps them out of the menu picker so one can't be added
	 * by accident. The only route in is wp-sitemap.xml, which core builds from
	 * every public post type.
	 *
	 * exclude_from_search is a different thing from being findable on Google —
	 * it only governs WP_Query's own search, so the pages stay out of the site's
	 * internal search results while remaining fully crawlable.
	 */
	register_post_type( 'standort', array(
		'labels' => array(
			'name'               => __( 'Standorte', 'letsdoo' ),
			'singular_name'      => __( 'Standort', 'letsdoo' ),
			'add_new_item'       => __( 'Neuen Standort hinzufügen', 'letsdoo' ),
			'edit_item'          => __( 'Standort bearbeiten', 'letsdoo' ),
			'all_items'          => __( 'Standorte', 'letsdoo' ),
			'menu_name'          => __( 'Standorte (SEO)', 'letsdoo' ),
		),
		'public'             => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_nav_menus'  => false,
		'menu_icon'          => 'dashicons-location',
		'supports'           => array( 'title', 'thumbnail' ),
		'has_archive'        => false,
		'exclude_from_search'=> true,
		'publicly_queryable' => true,
		/* Permalinks are built by hand below so these sit at the site root. */
		'rewrite'            => false,
		'query_var'          => 'standort',
	) );
}
add_action( 'init', 'letsdoo_register_post_types' );

/**
 * Root-level permalinks for Standort (/odoo-hochdorf/ rather than
 * /standorte/odoo-hochdorf/), because the keyword is wanted in the first path
 * segment.
 *
 * Done by remapping the query rather than by adding a rewrite rule. A rule like
 * ^([^/]+)/?$ never gets reached however it is registered: the rule core
 * generates for pages, (.?.+?)/?$, is the last one in the list and matches every
 * single-segment URL, so /odoo-hochdorf/ resolves to pagename=odoo-hochdorf,
 * finds no page and 404s first. Registering the rule at the top instead fixes
 * the Standort pages by breaking every real page.
 *
 * So core is left to route the URL as it likes, and the query is corrected on
 * the way past: only when the requested path matches no page and no post, but
 * does match a Standort. Pages and posts therefore always win a slug collision,
 * and nothing needs flushing when a Standort is added.
 *
 * Both query vars have to be checked. The permalink structure here is
 * /%postname%/, which puts WP_Rewrite into verbose-page-rules mode: the page
 * rule is tried first, but WP::parse_request() looks the page up while matching
 * and moves on to the next rule when there isn't one. A single-segment URL for
 * a Standort therefore arrives as name=, not pagename= — pagename only survives
 * on installs whose permalink structure leaves verbose rules off.
 */
function letsdoo_standort_request( $query_vars ) {
	if ( ! empty( $query_vars['post_type'] ) ) {
		return $query_vars;
	}

	if ( ! empty( $query_vars['pagename'] ) ) {
		$slug = $query_vars['pagename'];
	} elseif ( ! empty( $query_vars['name'] ) ) {
		$slug = $query_vars['name'];
	} else {
		return $query_vars;
	}

	/* Nested paths belong to the page hierarchy; Standorte are always flat. */
	if ( false !== strpos( $slug, '/' ) ) {
		return $query_vars;
	}

	if ( get_page_by_path( $slug ) || get_page_by_path( $slug, OBJECT, 'post' ) ) {
		return $query_vars;
	}

	if ( ! get_page_by_path( $slug, OBJECT, 'standort' ) ) {
		return $query_vars;
	}

	unset( $query_vars['pagename'] );

	$query_vars['standort']  = $slug;
	$query_vars['post_type'] = 'standort';
	$query_vars['name']      = $slug;

	return $query_vars;
}
add_filter( 'request', 'letsdoo_standort_request' );

function letsdoo_standort_permalink( $post_link, $post ) {
	if ( $post && 'standort' === $post->post_type ) {
		return user_trailingslashit( home_url( '/' . $post->post_name ) );
	}

	return $post_link;
}
add_filter( 'post_type_link', 'letsdoo_standort_permalink', 10, 2 );

/**
 * Registering a post type changes the rules core generates, and those are
 * cached in the database — so a new post type is inert until they are rebuilt.
 * Flushing on every request is expensive, so it is keyed to a stamp bumped by
 * hand whenever anything in this file changes what the rules should be.
 */
function letsdoo_maybe_flush_rewrites() {
	$version = '2';

	if ( get_option( 'letsdoo_rewrite_version' ) !== $version ) {
		flush_rewrite_rules();
		update_option( 'letsdoo_rewrite_version', $version );
	}
}
add_action( 'init', 'letsdoo_maybe_flush_rewrites', 99 );

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
