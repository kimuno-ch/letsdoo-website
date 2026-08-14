<?php
/**
 * Small helpers shared across the page templates: image fallbacks,
 * button markup, and ordered CPT queries.
 */

/**
 * Resolve an ACF image field (array|ID) to a URL, falling back to a
 * bundled placeholder while real photography hasn't been uploaded yet.
 */
function letsdoo_image_url( $image, $fallback_file, $size = 'large' ) {
	if ( is_array( $image ) && ! empty( $image['sizes'][ $size ] ) ) {
		return $image['sizes'][ $size ];
	}
	if ( is_array( $image ) && ! empty( $image['url'] ) ) {
		return $image['url'];
	}
	if ( is_numeric( $image ) ) {
		$src = wp_get_attachment_image_src( $image, $size );
		if ( $src ) {
			return $src[0];
		}
	}
	return get_theme_file_uri( '/assets/images/' . $fallback_file );
}

function letsdoo_image_alt( $image, $fallback_alt = '' ) {
	if ( is_array( $image ) && ! empty( $image['alt'] ) ) {
		return $image['alt'];
	}
	return $fallback_alt;
}

/**
 * Gradient pill button used throughout the design.
 */
function letsdoo_button( $label, $link, $classes = '' ) {
	if ( empty( $label ) ) {
		return;
	}
	$link = $link ? $link : '#kontakt';
	printf(
		'<a class="btn %1$s" href="%2$s">%3$s</a>',
		esc_attr( $classes ),
		esc_url( $link ),
		esc_html( $label )
	);
}

function letsdoo_get_leistungen() {
	return get_posts( array(
		'post_type'      => 'leistung',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	) );
}

function letsdoo_get_vorgehen_schritte() {
	return get_posts( array(
		'post_type'      => 'vorgehen_schritt',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	) );
}

function letsdoo_get_team() {
	return get_posts( array(
		'post_type'      => 'team_mitglied',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	) );
}

function letsdoo_get_referenzen() {
	return get_posts( array(
		'post_type'      => 'referenz',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	) );
}

function letsdoo_get_pakete() {
	return get_posts( array(
		'post_type'      => 'angebot_paket',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	) );
}

/**
 * Numbered pagination for the blog index and archives, marked up with the
 * theme's pill styling instead of the default prev/next links.
 */
function letsdoo_pagination() {
	$links = paginate_links( array(
		'type'      => 'array',
		'mid_size'  => 1,
		'prev_text' => __( 'Zurück', 'letsdoo' ),
		'next_text' => __( 'Weiter', 'letsdoo' ),
	) );

	if ( empty( $links ) ) {
		return;
	}

	printf(
		'<nav class="pagination" aria-label="%s">%s</nav>',
		esc_attr__( 'Beitragsnavigation', 'letsdoo' ),
		implode( '', $links )
	);
}

/**
 * Rough reading time in minutes, shown next to the date on blog posts.
 * 200 wpm is the usual rule of thumb; always at least 1 so a short post
 * doesn't read "0 Min.".
 */
function letsdoo_reading_time( $post = null ) {
	$words = str_word_count( wp_strip_all_tags( strip_shortcodes( get_the_content( null, false, $post ) ) ) );
	return max( 1, (int) round( $words / 200 ) );
}

/**
 * Permalink of the page using a given page template, so cross-page links
 * (e.g. a Referenz detail page pointing back at the Referenzen list on
 * "Über uns") survive the client renaming or re-slugging that page.
 */
function letsdoo_page_url_by_template( $template, $fallback_path = '/' ) {
	$pages = get_posts( array(
		'post_type'      => 'page',
		'posts_per_page' => 1,
		'post_status'    => 'publish',
		'meta_key'       => '_wp_page_template',
		'meta_value'     => $template,
	) );

	return $pages ? get_permalink( $pages[0] ) : home_url( $fallback_path );
}

/**
 * Splits a textarea field into trimmed, non-empty lines — used for the
 * Paket "Merkmale" field and the Standort "Lokaler Inhalt" paragraphs.
 *
 * Dates from the free ACF build, where repeater sub-fields were unavailable.
 * PRO is installed now, so these textareas are a pending migration; they still
 * hold live content, and converting one moves its meta keys.
 */
function letsdoo_lines( $text ) {
	if ( ! $text ) {
		return array();
	}
	$lines = preg_split( '/\r\n|\r|\n/', $text );
	$lines = array_map( 'trim', $lines );
	return array_values( array_filter( $lines, function ( $line ) {
		return '' !== $line;
	} ) );
}

/**
 * Parses a Paket "Merkmale" textarea into a list of
 * [ 'text' => ..., 'enthalten' => bool ], where a leading "-" marks a
 * feature as not included (rendered greyed out / crossed off).
 */
function letsdoo_merkmale_liste( $text ) {
	$items = array();
	foreach ( letsdoo_lines( $text ) as $line ) {
		if ( '-' === substr( $line, 0, 1 ) ) {
			$items[] = array( 'text' => trim( substr( $line, 1 ) ), 'enthalten' => false );
		} else {
			$items[] = array( 'text' => $line, 'enthalten' => true );
		}
	}
	return $items;
}

/*
 * letsdoo_faq_liste() used to live here — it split the Standort "FAQ" textarea
 * on "|" into question/answer pairs. The FAQ is a block with a proper repeater
 * now (blocks/faq/), so nothing calls it. The one-off conversion carries its
 * own copy of the parser: tools/migrate-standorte-to-blocks.php.
 */

/**
 * Small named icon set for spots that aren't backed by an ACF Font Awesome
 * field (fixed UI chrome, and the PHP-level fallback content that stands in
 * before a client has entered anything). ACF font-awesome fields render
 * their own markup directly via get_field() and don't go through this.
 */
function letsdoo_icon( $key ) {
	/*
	 * Font Awesome Free's Regular (outline) style only covers a narrow
	 * ~270-icon subset — house/gear/headset/graduation-cap/check/
	 * magnifying-glass/rocket/location-dot/phone/xmark aren't in it (Pro
	 * has full regular coverage; Free doesn't). Where the literal icon
	 * has no regular drawing, this swaps in the closest available regular
	 * icon instead of falling back to solid, so the whole set stays one
	 * consistent weight.
	 */
	$icons = array(
		'architecture' => 'fa-regular fa-house',
		'settings'     => 'fa-regular fa-pen-to-square',
		'support'      => 'fa-regular fa-headphones-simple',
		'training'     => 'fa-regular fa-id-badge',
		'check'        => 'fa-regular fa-circle-check',
		'search'       => 'fa-regular fa-eye',
		'rocket'       => 'fa-regular fa-lightbulb',
		'pin'          => 'fa-regular fa-map',
		'mail'         => 'fa-solid fa-envelope',
		'phone'        => 'fa-solid fa-phone',
		'cross'        => 'fa-regular fa-circle-xmark',
		'linkedin'     => 'fa-brands fa-linkedin-in',
		'instagram'    => 'fa-brands fa-instagram',
	);
	$class = isset( $icons[ $key ] ) ? $icons[ $key ] : $icons['check'];
	return '<i class="' . esc_attr( $class ) . '" aria-hidden="true"></i>';
}

/**
 * Company contact details from the Firmenangaben settings page
 * (inc/settings-page.php), with sane fallbacks before the client fills
 * them in via Einstellungen → Firmenangaben.
 */
function letsdoo_company_field( $field, $fallback = '' ) {
	$values = get_option( LETSDOO_SETTINGS_OPTION, array() );
	if ( ! empty( $values[ $field ] ) ) {
		return $values[ $field ];
	}
	if ( $fallback ) {
		return $fallback;
	}
	$fields = letsdoo_settings_fields();
	return isset( $fields[ $field ]['default'] ) ? $fields[ $field ]['default'] : '';
}
