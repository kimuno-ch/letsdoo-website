<?php
/**
 * Resource hints for the two things on this site the browser cannot discover
 * for itself until it is already too late to help.
 *
 * The heroes are CSS backgrounds and the brand fonts are declared inside
 * 01-base.css, so both sit two levels down the request chain: the browser has
 * to parse the document, fetch the stylesheet, and match a rule before it even
 * knows either file exists. Measured on staging that put the first Mazzard
 * request at 812ms with the hero — the page's LCP element — waiting behind it.
 * Preloading is the only way to move either one earlier, since neither is an
 * <img> that could carry fetchpriority itself.
 */

/**
 * The hero photo for the current request, resolved the way the templates
 * resolve it so the preload can never point at a different file than the one
 * the page goes on to paint.
 *
 * Mirrors three cases: home.php reads the field off the posts page,
 * single-leistung.php falls back from hero_bild to bild, and every other
 * template reads hero_image off the queried object. index.php and archive.php
 * are deliberately absent — they use the bundled placeholder SVG, which is
 * 1 KB and not worth a hint.
 */
function letsdoo_hero_image_for_request() {
	if ( ! function_exists( 'get_field' ) ) {
		return null;
	}

	if ( is_home() ) {
		$blog_page_id = (int) get_option( 'page_for_posts' );

		return $blog_page_id ? get_field( 'hero_image', $blog_page_id ) : null;
	}

	/* Explicit ID rather than letting get_field() fall back to the $post
	   global. This runs during wp_head, before the template has reached its
	   loop, so $post is whatever WP::register_globals() left there — and any
	   plugin that ran a secondary query on an earlier hook without calling
	   wp_reset_postdata() would leave it pointing somewhere else entirely.
	   The queried object is the page being rendered by definition, so the
	   preload can't drift off the image the template goes on to paint. */
	$post_id = get_queried_object_id();

	if ( ! $post_id ) {
		return null;
	}

	if ( is_singular( 'leistung' ) ) {
		return get_field( 'hero_bild', $post_id ) ?: get_field( 'bild', $post_id );
	}

	if ( is_singular() ) {
		return get_field( 'hero_image', $post_id );
	}

	return null;
}

/**
 * Priority 2 on wp_head: after wp_enqueue_scripts has run (priority 1) so the
 * theme's own registrations exist, but before wp_print_styles (priority 8)
 * puts the stylesheet <link>s on the page — a font preload that lands after
 * the CSS it is meant to front-run buys nothing.
 */
function letsdoo_preload_critical_assets() {
	/* Only the two faces that actually render above the fold. 01-base.css
	   declares five Mazzard weights; light, light-italic and semibold are
	   used further down the page and can wait for the normal CSS-driven
	   fetch. The hrefs carry no ?ver= because the @font-face src doesn't
	   either — a preload whose URL differs from the one CSS resolves by so
	   much as a query string downloads the font a second time. crossorigin
	   is required even though these are same-origin: fonts are always
	   fetched in CORS mode, and without it the preload simply doesn't
	   match. */
	foreach ( array( 'mazzardh-regular', 'mazzardh-bold' ) as $font ) {
		printf(
			'<link rel="preload" as="font" type="font/woff2" crossorigin href="%s">' . "\n",
			esc_url( get_theme_file_uri( "/assets/fonts/{$font}.woff2" ) )
		);
	}

	$hero_image = letsdoo_hero_image_for_request();

	if ( ! $hero_image ) {
		return;
	}

	/* The media conditions have to be the exact complement of the width
	   queries in 04-hero.css, or the browser preloads one size and then
	   paints another — two downloads instead of none saved. */
	$steps = array(
		'large'     => '(max-width: 899.98px)',
		'1536x1536' => '(min-width: 900px) and (max-width: 1599.98px)',
		'full'      => '(min-width: 1600px)',
	);

	foreach ( $steps as $size => $media ) {
		printf(
			'<link rel="preload" as="image" fetchpriority="high" media="%s" href="%s">' . "\n",
			esc_attr( $media ),
			esc_url( letsdoo_image_url( $hero_image, 'placeholder-photo.svg', $size ) )
		);
	}
}
add_action( 'wp_head', 'letsdoo_preload_critical_assets', 2 );
