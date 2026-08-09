<?php
/**
 * Search-engine output the theme has to produce itself.
 *
 * There is no SEO plugin on this install — only ACF and Akismet — so the
 * <title> beyond core's title-tag support, the meta description and the
 * structured data are all emitted here. Kept deliberately small: if Yoast or
 * Rank Math is ever installed, everything in this file becomes a duplicate and
 * should be removed rather than left to fight with the plugin.
 */

/**
 * The Standort landing pages carry their own <title>, falling back to
 * "Odoo <Ort>" so a page is never published with a title that doesn't mention
 * the town it exists to rank for.
 */
function letsdoo_document_title( $parts ) {
	if ( ! is_singular( 'standort' ) ) {
		return $parts;
	}

	$seo_title = get_field( 'seo_title' );
	if ( $seo_title ) {
		$parts['title'] = $seo_title;

		/* A hand-written SEO title is a complete title — appending the site
		   name would push it past what Google shows. */
		unset( $parts['tagline'] );

		return $parts;
	}

	$ort = get_field( 'ort' );
	if ( $ort ) {
		$parts['title'] = sprintf( 'Odoo %s', $ort );
	}

	return $parts;
}
add_filter( 'document_title_parts', 'letsdoo_document_title' );

/**
 * Meta description. Only ever printed when there is something deliberate to
 * say — an auto-generated one trimmed from body copy is worse than none, since
 * Google will write its own snippet from the page anyway.
 */
function letsdoo_meta_description() {
	$description = '';

	if ( is_singular( 'standort' ) ) {
		$description = get_field( 'meta_description' );

		if ( ! $description ) {
			$ort  = get_field( 'ort' );
			$text = get_field( 'hero_text' );

			if ( $ort && $text ) {
				$description = $text;
			}
		}
	} elseif ( is_singular() ) {
		$description = get_the_excerpt();
	}

	$description = trim( wp_strip_all_tags( (string) $description ) );

	if ( ! $description ) {
		return;
	}

	printf(
		'<meta name="description" content="%s">' . "\n",
		esc_attr( wp_html_excerpt( $description, 160, '…' ) )
	);
}
add_action( 'wp_head', 'letsdoo_meta_description', 1 );

/**
 * ProfessionalService structured data for the Standort pages.
 *
 * areaServed is the field doing the work: it is what tells Google this page is
 * about serving a named town, without pretending there is a branch office
 * there. The address stays the real one from Einstellungen → Firmenangaben —
 * inventing a local address is exactly the kind of thing that gets a business
 * profile pulled.
 */
function letsdoo_standort_schema() {
	if ( ! is_singular( 'standort' ) ) {
		return;
	}

	$ort = get_field( 'ort' );
	if ( ! $ort ) {
		return;
	}

	$address = letsdoo_company_field( 'company_address' );
	$lines   = letsdoo_lines( $address );

	$schema = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'ProfessionalService',
		'name'        => letsdoo_company_field( 'company_name' ),
		'url'         => get_permalink(),
		'description' => wp_strip_all_tags( (string) get_field( 'meta_description' ) ),
		'areaServed'  => array(
			'@type' => 'City',
			'name'  => $ort,
		),
		'serviceType' => 'Odoo ERP',
	);

	$email = letsdoo_company_field( 'company_email' );
	if ( $email ) {
		$schema['email'] = $email;
	}

	$phone = letsdoo_company_field( 'company_phone' );
	if ( $phone ) {
		$schema['telephone'] = $phone;
	}

	if ( $lines ) {
		$schema['address'] = array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => $lines[0],
			'addressLocality' => isset( $lines[1] ) ? $lines[1] : '',
			'addressCountry'  => 'CH',
		);
	}

	$schema = array_filter( $schema );

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
	);

	/* The FAQ block is emitted separately so a page without questions simply
	   doesn't carry the markup, rather than carrying an empty mainEntity. */
	$faq = letsdoo_faq_liste( get_field( 'faq' ) );
	if ( ! $faq ) {
		return;
	}

	$entities = array();
	foreach ( $faq as $item ) {
		if ( ! $item['frage'] || ! $item['antwort'] ) {
			continue;
		}

		$entities[] = array(
			'@type'          => 'Question',
			'name'           => $item['frage'],
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => $item['antwort'],
			),
		);
	}

	if ( ! $entities ) {
		return;
	}

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode(
			array(
				'@context'   => 'https://schema.org',
				'@type'      => 'FAQPage',
				'mainEntity' => $entities,
			),
			JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
		)
	);
}
add_action( 'wp_head', 'letsdoo_standort_schema', 2 );
