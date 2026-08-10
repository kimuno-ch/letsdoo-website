<?php
/**
 * ACF Blocks — the theme's sections, usable inside the block editor.
 *
 * Used by blog posts and the Standort landing pages. The page templates in
 * page-templates/ deliberately do NOT use these: they keep building their
 * sections from flat ACF fields, because their layout is fixed and the
 * template is the guardrail. Blocks exist where the order genuinely varies
 * from one post to the next.
 *
 * No JS build step, matching the rest of the theme. A block is a block.json
 * plus a PHP render template; ACF server-renders the editor preview over AJAX,
 * so there is nothing to bundle or transpile.
 *
 * Do NOT set "apiVersion" in block.json. ACF derives it from its own block
 * version (acf.blockVersion, default 2) and only ever pairs 3 with 3 — see
 * acf_get_block_type_settings() in pro/blocks.php. Forcing apiVersion 3 while
 * ACF's block version stays at 2 iframes the editor canvas while the block
 * still injects its preview as though it shared the document, and the resulting
 * exception takes down the editor root: every edit screen for a post type using
 * these blocks renders as a blank white page, with nothing in the PHP log
 * because the failure is entirely client-side. The front end is unaffected,
 * which makes it easy to miss. Leave apiVersion out and let ACF pick.
 *
 * Every block renders a top-level <section>, because the wave dividers and the
 * background blends in 05-sections.css are keyed off `section:nth-of-type(3n+k)`
 * among siblings. That selector counts only <section> elements, so core blocks
 * (a paragraph, an image) interleaved between two of ours do not shift the
 * sequence — but a block wrapped in a core Group would, since that opens a new
 * sibling scope. See letsdoo_blocks_allowed() below.
 */

/**
 * The block.json directories, relative to the theme's /blocks folder.
 * Add a directory here and it shows up in the inserter.
 */
function letsdoo_blocks() {
	return array(
		'textabschnitt',
		'leistungen',
		'referenz-karte',
		'zahlen',
		'faq',
		'cta-band',
	);
}

/**
 * Fully-qualified names of the theme's blocks, e.g. "letsdoo/cta-band".
 * Derived from the same list so the two can't drift apart.
 */
function letsdoo_block_names() {
	return array_map(
		function ( $dir ) {
			return 'letsdoo/' . $dir;
		},
		letsdoo_blocks()
	);
}

function letsdoo_register_blocks() {
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	foreach ( letsdoo_blocks() as $dir ) {
		$path = get_theme_file_path( "/blocks/{$dir}" );

		if ( file_exists( $path . '/block.json' ) ) {
			register_block_type( $path );
		}
	}
}
add_action( 'init', 'letsdoo_register_blocks' );

/**
 * Own category in the inserter, so the theme's sections are not scattered
 * through "Widgets" and "Embeds".
 */
function letsdoo_block_category( $categories ) {
	array_unshift(
		$categories,
		array(
			'slug'  => 'letsdoo',
			'title' => __( "Let's Doo", 'letsdoo' ),
			'icon'  => null,
		)
	);

	return $categories;
}
add_filter( 'block_categories_all', 'letsdoo_block_category' );

/**
 * The site's own stylesheets, loaded into the editor canvas so a block preview
 * looks like the published page.
 *
 * Reuses letsdoo_style_parts() rather than a second list — one place to add a
 * stylesheet. Note the preview is close but not exact: the editor nests blocks
 * in its own wrappers, so the nth-of-type wave sequence resolves differently
 * there than on the front end.
 */
function letsdoo_editor_styles() {
	add_theme_support( 'editor-styles' );

	add_editor_style( 'style.css' );

	foreach ( letsdoo_style_parts() as $part ) {
		add_editor_style( "assets/css/{$part}.css" );
	}
}
add_action( 'after_setup_theme', 'letsdoo_editor_styles', 11 );

/**
 * Opening tag for a block's top-level <section>.
 *
 * Every section block needs the same three things and they are easy to get
 * subtly wrong one file at a time, so they live here:
 *
 *  - "align<value>" from the block's align support. Inside a post body,
 *    .entry-content caps its children at 780px (12-single-referenz.css:91) and
 *    "alignfull" is the theme's existing escape hatch. ACF render templates
 *    emit their own markup, so the class is not applied for us.
 *  - The anchor, and only when an editor set one. Defaulting it duplicates the
 *    id="kontakt" that home.php and single.php still hardcode.
 *  - The .section__bg-photo marker, which is what makes 05-sections.css treat
 *    the section as a banner: coloured blend behind it and a wave at each edge.
 *
 * @param array  $block    The block array passed into the render template.
 * @param string $modifier BEM modifier class, e.g. "section--leistungen".
 * @param bool   $blend    Whether to paint the gradient/wave treatment.
 */
function letsdoo_block_section_open( $block, $modifier, $blend = false ) {
	/*
	 * ld-block-section marks a section that came from a block rather than from
	 * a page template. The section CSS hardcodes its type colour for one
	 * background — .section--leistungen expects the gradient and paints its
	 * headings white, .standort-faq expects white and paints them dark — which
	 * is safe when a template decides, and not safe once an editor can toggle
	 * the background per block. 25-blocks.css corrects for whichever way the
	 * switch is set, scoped to this class so the page templates are untouched.
	 */
	$classes = array( 'section', 'ld-block-section', $modifier );

	if ( ! empty( $block['align'] ) ) {
		$classes[] = 'align' . $block['align'];
	}

	printf(
		'<section class="%s"%s>',
		esc_attr( implode( ' ', $classes ) ),
		empty( $block['anchor'] ) ? '' : ' id="' . esc_attr( $block['anchor'] ) . '"'
	);

	if ( $blend ) {
		echo '<div class="section__bg-photo"></div>';
	}
}

/**
 * Every block in a post, innerBlocks included, as a flat list.
 *
 * parse_blocks() only returns the top level. Nothing nests our sections today —
 * Group and Columns are kept out of the Standort inserter precisely so they
 * can't — but a reusable block or a future container would hide a block from a
 * non-recursive search, and the failure mode there is silent.
 */
function letsdoo_flatten_blocks( $blocks ) {
	$flat = array();

	foreach ( $blocks as $block ) {
		$flat[] = $block;

		if ( ! empty( $block['innerBlocks'] ) ) {
			$flat = array_merge( $flat, letsdoo_flatten_blocks( $block['innerBlocks'] ) );
		}
	}

	return $flat;
}

/**
 * Question/answer pairs from every FAQ block in a post.
 *
 * inc/seo.php needs these for the FAQPage structured data, which is emitted
 * into <head> long before the blocks themselves render — so it cannot piggyback
 * on the render template and has to read the saved block attributes instead.
 *
 * ACF stores a block's field values in its attributes as flattened meta
 * ("fragen" => 2, "fragen_0_frage" => "…"). acf_setup_meta() with $is_main
 * makes that visible to a plain get_field(), which is how ACF's own render
 * pipeline does it — so the repeater comes back properly formatted rather than
 * being reassembled from raw keys here.
 *
 * Only complete pairs are returned. A question without an answer is valid on
 * the page but would make the schema invalid.
 */
function letsdoo_faq_block_items( $post = null ) {
	$post = get_post( $post );

	if ( ! $post || ! function_exists( 'acf_setup_meta' ) || ! has_blocks( $post->post_content ) ) {
		return array();
	}

	$items = array();

	foreach ( letsdoo_flatten_blocks( parse_blocks( $post->post_content ) ) as $index => $block ) {
		if ( 'letsdoo/faq' !== ( $block['blockName'] ?? '' ) || empty( $block['attrs']['data'] ) ) {
			continue;
		}

		/* Unique per block so two FAQ blocks on one page don't share storage. */
		$context = 'letsdoo_faq_' . $post->ID . '_' . $index;

		acf_setup_meta( $block['attrs']['data'], $context, true );
		$fragen = get_field( 'fragen' );
		acf_reset_meta( $context );

		if ( ! $fragen ) {
			continue;
		}

		foreach ( $fragen as $row ) {
			if ( empty( $row['frage'] ) || empty( $row['antwort'] ) ) {
				continue;
			}

			$items[] = array(
				'frage'   => $row['frage'],
				'antwort' => $row['antwort'],
			);
		}
	}

	return $items;
}

/**
 * Restrict the inserter on Standort pages to the theme's own sections plus the
 * handful of core blocks that are safe between them.
 *
 * Group and Columns are deliberately absent: they would wrap our sections in an
 * extra element, which breaks the nth-of-type wave sequence described at the
 * top of this file. Blog posts are left alone — a post body is prose, and
 * authors there need the full core set.
 */
function letsdoo_blocks_allowed( $allowed, $context ) {
	if ( empty( $context->post ) || 'standort' !== $context->post->post_type ) {
		return $allowed;
	}

	return array_merge(
		letsdoo_block_names(),
		array( 'core/paragraph', 'core/heading', 'core/image', 'core/list', 'core/list-item' )
	);
}
add_filter( 'allowed_block_types_all', 'letsdoo_blocks_allowed', 10, 2 );
