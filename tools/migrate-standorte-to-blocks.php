<?php
/**
 * One-off migration: Standort ACF section fields -> blocks in post_content.
 *
 * Reads the pre-block fields (lokal_heading, lokal_text, referenz, faq) and
 * writes the equivalent block markup into post_content. The source fields are
 * left in the database untouched, so this can be re-run and so the old content
 * stays recoverable.
 *
 * Skips any Standort that already has blocks, so re-running is safe.
 *
 * Already run against this site. Kept because it is the only record of how the
 * pre-block content maps onto blocks, and because restoring an older database
 * dump puts the old field content back and needs it again.
 *
 * Usage, from the repository root:
 *
 *   docker cp tools/migrate-standorte-to-blocks.php letsdoo_wordpress:/tmp/m.php
 *   docker compose exec -T wordpress php /tmp/m.php --dry-run
 *   docker compose exec -T wordpress php /tmp/m.php
 *
 * Take a `just backup-db` first.
 */

require '/var/www/html/wp-load.php';

/*
 * A block comment whose attributes carry markup — the Textabschnitt block holds
 * WYSIWYG HTML — gets escaped on save unless the saving user has
 * unfiltered_html. A CLI run has no current user, so KSES is filtering
 * content_save_pre and the block comment comes back as "&lt;!-- wp:…", which
 * parse_blocks() no longer recognises: the section simply disappears from the
 * page, with no error anywhere. kses_init_filters() has already run by this
 * point, so the filters have to be removed explicitly rather than by switching
 * user. This is what WP-CLI does for the same reason.
 */
kses_remove_filters();

$dry_run = in_array( '--dry-run', $argv, true );

/**
 * Splits the old "Frage | Antwort", one per line, FAQ textarea.
 *
 * Inlined rather than calling letsdoo_faq_liste(): that helper was deleted from
 * the theme once the FAQ became a block, and a migration that stops working
 * when the code it migrates away from is cleaned up is no use to anybody.
 */
function ld_parse_faq( $text ) {
	$items = array();

	foreach ( preg_split( '/\r\n|\r|\n/', (string) $text ) as $line ) {
		$line = trim( $line );

		if ( '' === $line ) {
			continue;
		}

		$parts = explode( '|', $line, 2 );

		$items[] = array(
			'frage'   => trim( $parts[0] ),
			'antwort' => isset( $parts[1] ) ? trim( $parts[1] ) : '',
		);
	}

	return $items;
}

/**
 * Serialise one ACF block. ACF reads field values out of the "data" attribute,
 * keyed by field name, each paired with an underscore-prefixed entry naming the
 * field key — that pairing is what lets get_field() resolve types (a repeater,
 * a link, a post object) rather than handing back the raw value.
 */
function ld_block( $name, array $data, array $extra = array() ) {
	$attrs = array_merge(
		array(
			'name'  => $name,
			'data'  => $data,
			'mode'  => 'preview',
			'align' => 'full',
		),
		$extra
	);

	return '<!-- wp:' . $name . ' ' . wp_json_encode( $attrs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . ' /-->';
}

/** Repeater rows flatten to "<name>" => count plus "<name>_<i>_<sub>" entries. */
function ld_repeater( $name, $field_key, array $rows, array $sub_keys ) {
	$data = array( $name => count( $rows ), '_' . $name => $field_key );

	foreach ( $rows as $i => $row ) {
		foreach ( $sub_keys as $sub => $key ) {
			$data[ "{$name}_{$i}_{$sub}" ]        = $row[ $sub ];
			$data[ "_{$name}_{$i}_{$sub}" ]       = $key;
		}
	}

	return $data;
}

$standorte = get_posts( array(
	'post_type'      => 'standort',
	'posts_per_page' => -1,
	'post_status'    => 'any',
) );

printf( "Found %d Standort(e)%s\n\n", count( $standorte ), $dry_run ? ' [DRY RUN]' : '' );

foreach ( $standorte as $post ) {
	printf( "-- %s (#%d)\n", $post->post_name, $post->ID );

	if ( has_blocks( $post->post_content ) ) {
		echo "   already has blocks, skipping\n\n";
		continue;
	}

	$ort      = get_field( 'ort', $post->ID );
	$blocks   = array();

	/* 1. Local copy. The textarea was split on newlines and rendered as <p>;
	      the WYSIWYG field stores markup, so the paragraphs are made explicit. */
	$lokal_text = (string) get_field( 'lokal_text', $post->ID );
	if ( trim( $lokal_text ) !== '' ) {
		$html = '';
		foreach ( letsdoo_lines( $lokal_text ) as $absatz ) {
			$html .= '<p>' . esc_html( $absatz ) . '</p>';
		}

		$heading = (string) get_field( 'lokal_heading', $post->ID );

		$blocks[] = ld_block( 'letsdoo/textabschnitt', array(
			'heading'   => $heading,
			'_heading'  => 'field_ld_block_text_heading',
			'text'      => $html,
			'_text'     => 'field_ld_block_text_text',
			'bg_blend'  => 0,
			'_bg_blend' => 'field_ld_block_text_blend',
		) );
		printf( "   textabschnitt  (%d Absätze%s)\n", count( letsdoo_lines( $lokal_text ) ), $heading ? ', eigener Titel' : '' );
	}

	/* 2. Leistungen — carried the blend treatment in the old template. */
	$blocks[] = ld_block( 'letsdoo/leistungen', array(
		'heading'   => '',
		'_heading'  => 'field_ld_block_leistungen_heading',
		'bg_blend'  => 1,
		'_bg_blend' => 'field_ld_block_leistungen_blend',
	) );
	echo "   leistungen\n";

	/* 3. Regional reference, only if one was chosen. */
	$referenz_id = get_field( 'referenz', $post->ID );
	if ( $referenz_id ) {
		$blocks[] = ld_block( 'letsdoo/referenz-karte', array(
			'heading'   => '',
			'_heading'  => 'field_ld_block_referenz_heading',
			'referenz'  => (int) $referenz_id,
			'_referenz' => 'field_ld_block_referenz_referenz',
			'bg_blend'  => 0,
			'_bg_blend' => 'field_ld_block_referenz_blend',
		) );
		printf( "   referenz-karte (#%d %s)\n", $referenz_id, get_the_title( $referenz_id ) );
	}

	/* 4. FAQ — "Frage | Antwort" lines become repeater rows. */
	$faq = ld_parse_faq( get_field( 'faq', $post->ID ) );
	if ( $faq ) {
		$rows = array();
		foreach ( $faq as $item ) {
			$rows[] = array( 'frage' => $item['frage'], 'antwort' => $item['antwort'] );
		}

		$data = ld_repeater( 'fragen', 'field_ld_block_faq_fragen', $rows, array(
			'frage'   => 'field_ld_block_faq_frage',
			'antwort' => 'field_ld_block_faq_antwort',
		) );

		$data['heading']   = '';
		$data['_heading']  = 'field_ld_block_faq_heading';
		$data['bg_blend']  = 0;
		$data['_bg_blend'] = 'field_ld_block_faq_blend';

		$blocks[] = ld_block( 'letsdoo/faq', $data );

		$unbeantwortet = count( array_filter( $faq, function ( $i ) { return '' === $i['antwort']; } ) );
		printf( "   faq            (%d Fragen%s)\n", count( $faq ), $unbeantwortet ? ", $unbeantwortet ohne Antwort" : '' );
	}

	/* 5. Closing CTA — the full-bleed variant the template used. */
	$blocks[] = ld_block( 'letsdoo/cta-band', array(
		'variante'  => 'voll',
		'_variante' => 'field_ld_block_cta_variante',
		'heading'   => '',
		'_heading'  => 'field_ld_block_cta_heading',
		'text'      => 'Erstgespräch unverbindlich und kostenlos.',
		'_text'     => 'field_ld_block_cta_text',
	) );
	echo "   cta-band (voll)\n";

	$content = implode( "\n\n", $blocks );

	if ( $dry_run ) {
		printf( "   -> would write %d bytes to post_content\n\n", strlen( $content ) );
		continue;
	}

	/*
	 * wp_slash() is not optional. wp_update_post() runs wp_unslash() over what
	 * it is given, which strips the backslashes wp_json_encode() put in front of
	 * the quotes inside the block attributes — leaving JSON that parse_blocks()
	 * silently rejects. The block comment still looks right in the database; the
	 * section just renders empty. Only bites when a value contains a quote, so
	 * plain-text towns migrate fine and one with a link or a quoted phrase does
	 * not, which is exactly the kind of bug that ships.
	 */
	$result = wp_update_post( array(
		'ID'           => $post->ID,
		'post_content' => wp_slash( $content ),
	), true );

	if ( is_wp_error( $result ) ) {
		printf( "   !! FEHLER: %s\n\n", $result->get_error_message() );
		continue;
	}

	printf( "   -> wrote %d bytes\n\n", strlen( $content ) );
}

echo "done\n";
