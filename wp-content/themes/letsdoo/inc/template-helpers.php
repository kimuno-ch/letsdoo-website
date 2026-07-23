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
 * Splits a textarea field into trimmed, non-empty lines — used for the
 * Paket "Merkmale" field since the free ACF plugin doesn't support
 * repeater sub-fields.
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

/**
 * Small line-style icon set for the Angebote page (bento cards, checkmarks),
 * matching the inline SVG icons already used in header.php / footer.php
 * rather than pulling in an icon font.
 */
function letsdoo_icon( $key ) {
	$icons = array(
		'architecture' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V9l7-6 7 6v12"/><path d="M9 21v-6h6v6"/></svg>',
		'settings'     => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>',
		'support'      => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/></svg>',
		'training'     => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10 12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c0 1.7 2.7 3 6 3s6-1.3 6-3v-5"/></svg>',
		'check'        => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>',
	);
	return isset( $icons[ $key ] ) ? $icons[ $key ] : $icons['check'];
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
