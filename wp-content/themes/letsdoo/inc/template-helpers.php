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
