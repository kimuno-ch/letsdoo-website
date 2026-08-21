<?php
/**
 * ACF field groups, registered as code so they're version-controlled
 * instead of living only in the database.
 *
 * This site runs ACF PRO (see "Custom fields (ACF PRO)" in the README), so
 * repeaters, have_rows()/the_row(), Flexible Content, Clone fields and
 * Options Pages all work, and the Pro IDE stubs in composer.json match the
 * runtime.
 *
 * Several free-edition workarounds are still in place and still supported —
 * they hold live content, and converting one moves its meta keys, so each
 * needs its own migration rather than a sweep:
 *
 *   - Paket "Merkmale", Leistung "Merkmale" and Standort "FAQ" are textareas
 *     parsed by letsdoo_merkmale_liste() / letsdoo_faq_liste().
 *   - warum_letsdoo_punkt_1/2/3/4_* are twelve flat fields instead of a repeater.
 *   - Company-wide settings live in inc/settings-page.php, a plain Settings
 *     API page under Einstellungen → Firmenangaben, not an Options Page.
 *
 * New fields should use the Pro types; don't copy the workarounds.
 */

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
	return;
}

/**
 * The "Farbverlauf-Hintergrund" switch shared by the section blocks.
 *
 * A plain function rather than an ACF Clone field: clone needs a source group
 * parked on a location that never matches, and for one boolean that indirection
 * costs more than it saves. Field keys have to be unique across groups, hence
 * the per-block suffix.
 *
 * Turning it on gives the section the coloured blend and a wave at each edge
 * (05-sections.css). The design assumes those alternate down the page, so the
 * instructions say as much where an editor will read them.
 *
 * @param string $key_suffix Unique fragment for the field key, e.g. "leistungen".
 * @param int    $default    1 for sections that carried the treatment before.
 */
function letsdoo_block_blend_field( $key_suffix, $default = 0 ) {
	return array(
		'key'           => "field_ld_block_{$key_suffix}_blend",
		'label'         => 'Farbverlauf-Hintergrund',
		'name'          => 'bg_blend',
		'type'          => 'true_false',
		'ui'            => 1,
		'default_value' => $default,
		'instructions'  => 'Farbiger Hintergrund mit Wellen. Am besten abwechselnd einsetzen – zwei farbige Abschnitte direkt untereinander stossen hart aneinander.',
	);
}

/**
 * A "pick specific posts, else show all" multi-select — post_object with
 * multiple enabled. Every curated grid on the site (Leistungen, Referenzen —
 * in their blocks and in the page templates that still build the grid
 * inline) shares this shape. Left empty, letsdoo_selected_or_all() in
 * inc/template-helpers.php falls back to every post of that type in
 * menu_order — the behaviour these grids had before this field existed — so
 * nothing already published changes until an editor actually picks something.
 *
 * @param string $key_suffix Unique fragment for the field key, e.g. "home_leistungen".
 * @param string $post_type  The CPT to choose from, e.g. "leistung".
 * @param string $name       Field name / meta key.
 * @param string $label      Field label shown to the editor.
 */
function letsdoo_post_selector_field( $key_suffix, $post_type, $name, $label ) {
	return array(
		'key'           => "field_ld_{$key_suffix}_selector",
		'label'         => $label,
		'name'          => $name,
		'type'          => 'post_object',
		'post_type'     => array( $post_type ),
		'multiple'      => 1,
		'return_format' => 'id',
		'ui'            => 1,
		'instructions'  => 'Leer lassen, um automatisch alle zu zeigen. Sonst werden nur die hier ausgewählten angezeigt, in der hier gewählten Reihenfolge.',
	);
}

add_action( 'acf/init', function () {

	/* -------------------------------------------------- */
	/* Leistung (CPT single fields)                        */
	/* -------------------------------------------------- */
	acf_add_local_field_group( array(
		'key'    => 'group_ld_leistung',
		'title'  => 'Leistung Details',
		'fields' => array(
			array(
				'key'   => 'field_ld_leistung_beschreibung',
				'label' => 'Beschreibung',
				'name'  => 'beschreibung',
				'type'  => 'textarea',
				'rows'  => 3,
			),
			array(
				'key'   => 'field_ld_leistung_hero_bild',
				'label' => 'Hero-Bild',
				'name'  => 'hero_bild',
				'type'  => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'instructions'  => 'Hintergrundbild auf der Detailseite dieser Leistung. Unabhängig vom Bild weiter unten, das die Karte auf der Startseite/Angebote-Seite steuert. Leer lassen, um stattdessen das Kartenbild zu verwenden.',
			),

			array(
				'key'   => 'field_ld_leistung_angebote_tab',
				'label' => 'Angebote-Seite',
				'name'  => '',
				'type'  => 'tab',
				'instructions' => 'Diese Felder werden zusätzlich für die Leistungskarten auf der Angebote-Seite verwendet.',
			),
			array(
				'key'           => 'field_ld_leistung_icon',
				'label'         => 'Icon',
				'name'          => 'icon',
				'type'          => 'font-awesome',
				'icon_sets'     => array( 'classic_solid', 'classic_regular', 'brands' ),
				'save_format'   => 'element',
				'default_value' => '{"id":"house","style":"regular"}',
			),
			array(
				'key'   => 'field_ld_leistung_bild',
				'label' => 'Bild',
				'name'  => 'bild',
				'type'  => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'instructions'  => 'Mit Bild wird die Karte auf der Startseite/Angebote-Seite gross (Bild neben Text) dargestellt, ohne Bild klein. Für das Hero-Bild der Detailseite siehe „Hero-Bild“ oben.',
			),
			array(
				'key'   => 'field_ld_leistung_merkmale',
				'label' => 'Merkmale',
				'name'  => 'merkmale',
				'type'  => 'textarea',
				'rows'  => 4,
				'instructions' => 'Ein Merkmal pro Zeile (optional, nur auf grossen Karten sichtbar).',
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'leistung',
				),
			),
		),
	) );

	/* -------------------------------------------------- */
	/* Vorgehen-Schritt (CPT single fields)                */
	/* -------------------------------------------------- */
	acf_add_local_field_group( array(
		'key'    => 'group_ld_vorgehen_schritt',
		'title'  => 'Schritt Details',
		'fields' => array(
			array(
				'key'          => 'field_ld_vorgehen_schritt_beschreibung',
				'label'        => 'Beschreibung',
				'name'         => 'beschreibung',
				'type'         => 'textarea',
				'rows'         => 3,
				'instructions' => 'Ein bis zwei Sätze. Erscheint unter dem Titel im Zeitstrahl.',
			),
			array(
				'key'           => 'field_ld_vorgehen_schritt_icon',
				'label'         => 'Icon',
				'name'          => 'icon',
				'type'          => 'font-awesome',
				'icon_sets'     => array( 'classic_solid', 'classic_regular', 'brands' ),
				'save_format'   => 'element',
				'default_value' => '{"id":"circle-check","style":"regular"}',
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'vorgehen_schritt',
				),
			),
		),
	) );

	/* -------------------------------------------------- */
	/* Teammitglied (CPT single fields)                    */
	/* -------------------------------------------------- */
	acf_add_local_field_group( array(
		'key'    => 'group_ld_team',
		'title'  => 'Teammitglied Details',
		'fields' => array(
			array(
				'key'   => 'field_ld_team_position',
				'label' => 'Position',
				'name'  => 'position',
				'type'  => 'text',
			),
			array(
				'key'          => 'field_ld_team_bio',
				'label'        => 'Kurzbeschreibung',
				'name'         => 'bio',
				'type'         => 'textarea',
				'rows'         => 3,
				'instructions' => 'Ein bis zwei Sätze. Erscheint als Overlay über dem Foto, wenn man die Karte antippt oder mit der Maus darüber fährt.',
			),
			array(
				'key'   => 'field_ld_team_linkedin',
				'label' => 'LinkedIn URL',
				'name'  => 'linkedin_url',
				'type'  => 'url',
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'team_mitglied',
				),
			),
		),
	) );

	/* -------------------------------------------------- */
	/* Referenz (CPT single fields)                        */
	/* -------------------------------------------------- */
	acf_add_local_field_group( array(
		'key'    => 'group_ld_referenz',
		'title'  => 'Referenz Details',
		'fields' => array(
			array(
				'key'   => 'field_ld_referenz_beschreibung',
				'label' => 'Beschreibung',
				'name'  => 'beschreibung',
				'type'  => 'textarea',
				'rows'  => 4,
				'instructions' => 'Kurzbeschreibung für die Karte auf „Über uns" und als Lead auf der Detailseite.',
			),

			array(
				'key'   => 'field_ld_referenz_detail_tab',
				'label' => 'Detailseite',
				'name'  => '',
				'type'  => 'tab',
				'instructions' => 'Der eigentliche Inhalt der Detailseite (Bilder, Videos, Text) wird im Editor oben erfasst. Das Beitragsbild wird als Kundenlogo verwendet.',
			),
			array(
				'key'   => 'field_ld_referenz_hero_image',
				'label' => 'Hintergrundbild',
				'name'  => 'hero_image',
				'type'  => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'instructions'  => 'Grosses Bild hinter dem Titel der Detailseite.',
			),
			array(
				'key'   => 'field_ld_referenz_branche',
				'label' => 'Branche',
				'name'  => 'branche',
				'type'  => 'text',
				'wrapper' => array( 'width' => '33' ),
			),
			array(
				'key'   => 'field_ld_referenz_jahr',
				'label' => 'Jahr',
				'name'  => 'jahr',
				'type'  => 'text',
				'wrapper' => array( 'width' => '33' ),
			),
			array(
				'key'   => 'field_ld_referenz_website',
				'label' => 'Website',
				'name'  => 'website',
				'type'  => 'url',
				'wrapper' => array( 'width' => '34' ),
			),
			array(
				'key'   => 'field_ld_referenz_module',
				'label' => 'Eingesetzte Odoo-Module',
				'name'  => 'module',
				'type'  => 'textarea',
				'rows'  => 4,
				'instructions' => 'Ein Modul pro Zeile, z.B. „CRM".',
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'referenz',
				),
			),
		),
	) );

	/* -------------------------------------------------- */
	/* Paket (CPT single fields)                            */
	/* -------------------------------------------------- */
	acf_add_local_field_group( array(
		'key'    => 'group_ld_paket',
		'title'  => 'Paket Details',
		'fields' => array(
			array(
				'key'   => 'field_ld_paket_untertitel',
				'label' => 'Untertitel',
				'name'  => 'untertitel',
				'type'  => 'text',
				'instructions'  => 'z.B. „Ideal für Einzelunternehmer"',
				'default_value' => 'Ideal für Einzelunternehmer',
			),
			array(
				'key'   => 'field_ld_paket_preis',
				'label' => 'Preis',
				'name'  => 'preis',
				'type'  => 'text',
				'instructions'  => 'z.B. „€990" oder „Auf Anfrage".',
				'default_value' => '€990',
				'wrapper' => array( 'width' => '34' ),
			),
			array(
				'key'   => 'field_ld_paket_preis_suffix',
				'label' => 'Preiszusatz',
				'name'  => 'preis_suffix',
				'type'  => 'text',
				'instructions'  => 'z.B. „/einmalig"',
				'default_value' => '/einmalig',
				'wrapper' => array( 'width' => '33' ),
			),
			array(
				'key'   => 'field_ld_paket_preis_hinweis',
				'label' => 'Hinweis',
				'name'  => 'preis_hinweis',
				'type'  => 'text',
				'instructions'  => 'z.B. „+ Odoo Subscription"',
				'default_value' => '+ Odoo Subscription',
				'wrapper' => array( 'width' => '33' ),
			),
			array(
				'key'   => 'field_ld_paket_hervorgehoben',
				'label' => 'Hervorgehoben',
				'name'  => 'hervorgehoben',
				'type'  => 'true_false',
				'ui'    => 1,
				'default_value' => 0,
				'instructions' => 'Als empfohlenes Paket optisch hervorheben.',
				'wrapper' => array( 'width' => '50' ),
			),
			array(
				'key'   => 'field_ld_paket_badge',
				'label' => 'Badge-Text',
				'name'  => 'badge',
				'type'  => 'text',
				'default_value' => 'Empfohlen',
				'wrapper' => array( 'width' => '50' ),
			),
			array(
				'key'   => 'field_ld_paket_merkmale',
				'label' => 'Merkmale',
				'name'  => 'merkmale',
				'type'  => 'textarea',
				'rows'  => 6,
				'instructions'  => 'Ein Merkmal pro Zeile. Zeile mit „-" davor beginnen, um sie ausgegraut/als nicht enthalten darzustellen, z.B. „-Individualentwicklung".',
				'default_value' => "Standard-Implementierung\n3 Kernmodule (CRM, Rechnungen)\nE-Mail Support\n-Individualentwicklung",
			),
			array(
				'key'   => 'field_ld_paket_button_label',
				'label' => 'Button Text',
				'name'  => 'button_label',
				'type'  => 'text',
				'default_value' => 'Jetzt anfragen',
				'wrapper' => array( 'width' => '50' ),
			),
			array(
				'key'   => 'field_ld_paket_button_link',
				'label' => 'Button Link',
				'name'  => 'button_link',
				'type'  => 'url',
				'default_value' => home_url( '/kontakt/' ),
				'wrapper' => array( 'width' => '50' ),
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'angebot_paket',
				),
			),
		),
	) );

	/* -------------------------------------------------- */
	/* Home template                                       */
	/* -------------------------------------------------- */
	acf_add_local_field_group( array(
		'key'    => 'group_ld_home',
		'title'  => 'Startseite Inhalte',
		'fields' => array(
			array(
				'key'   => 'field_ld_home_hero_tab',
				'label' => 'Hero',
				'name'  => '',
				'type'  => 'tab',
			),
			array(
				'key'     => 'field_ld_home_hero_heading',
				'label'   => 'Titel',
				'name'    => 'hero_heading',
				'type'    => 'text',
				'default_value' => 'Odoo einfach gemacht',
			),
			array(
				'key'     => 'field_ld_home_hero_text',
				'label'   => 'Text',
				'name'    => 'hero_text',
				'type'    => 'textarea',
				'rows'    => 3,
			),
			array(
				'key'   => 'field_ld_home_hero_button_label',
				'label' => 'Button Text',
				'name'  => 'hero_button_label',
				'type'  => 'text',
				'default_value' => 'Kontakt aufnehmen',
			),
			array(
				'key'   => 'field_ld_home_hero_button_link',
				'label' => 'Button Link',
				'name'  => 'hero_button_link',
				'type'  => 'url',
			),
			array(
				'key'   => 'field_ld_home_hero_image',
				'label' => 'Hintergrundbild',
				'name'  => 'hero_image',
				'type'  => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
			),

			array(
				'key'   => 'field_ld_home_warum_odoo_tab',
				'label' => 'Warum Odoo?',
				'name'  => '',
				'type'  => 'tab',
			),
			array(
				'key'   => 'field_ld_home_warum_odoo_heading',
				'label' => 'Titel',
				'name'  => 'warum_odoo_heading',
				'type'  => 'text',
				'default_value' => 'Warum Odoo?',
			),
			array(
				'key'   => 'field_ld_home_warum_odoo_subheading',
				'label' => 'Untertitel',
				'name'  => 'warum_odoo_subheading',
				'type'  => 'text',
				'default_value' => 'Eine Software für Ihr gesamtes Unternehmen.',
			),
			array(
				'key'   => 'field_ld_home_warum_odoo_text',
				'label' => 'Text',
				'name'  => 'warum_odoo_text',
				'type'  => 'textarea',
				'rows'  => 3,
			),
			array(
				'key'   => 'field_ld_home_warum_odoo_button_label',
				'label' => 'Button Text',
				'name'  => 'warum_odoo_button_label',
				'type'  => 'text',
				'default_value' => 'Kontakt aufnehmen',
			),
			array(
				'key'   => 'field_ld_home_warum_odoo_button_link',
				'label' => 'Button Link',
				'name'  => 'warum_odoo_button_link',
				'type'  => 'url',
			),
			array(
				'key'   => 'field_ld_home_warum_odoo_image',
				'label' => 'Bild (rechts)',
				'name'  => 'warum_odoo_image',
				'type'  => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'instructions'  => 'Leer lassen für die mitgelieferte Odoo-Übersicht. Freigestelltes PNG mit transparentem Hintergrund funktioniert hier am besten.',
			),

			array(
				'key'   => 'field_ld_home_leistungen_tab',
				'label' => 'Unsere Leistungen',
				'name'  => '',
				'type'  => 'tab',
			),
			array(
				'key'   => 'field_ld_home_leistungen_heading',
				'label' => 'Titel',
				'name'  => 'leistungen_heading',
				'type'  => 'text',
				'default_value' => 'Unsere Leistungen',
				'instructions' => 'Die Karten selbst werden unter Leistungen im Menü gepflegt.',
			),
			letsdoo_post_selector_field( 'home_leistungen', 'leistung', 'leistungen_auswahl', 'Leistungen auswählen' ),
			array(
				'key'   => 'field_ld_home_leistungen_bg_image',
				'label' => 'Hintergrundbild',
				'name'  => 'leistungen_bg_image',
				'type'  => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
			),

			array(
				'key'   => 'field_ld_home_warum_letsdoo_tab',
				'label' => "Warum Let's Doo?",
				'name'  => '',
				'type'  => 'tab',
			),
			array(
				'key'   => 'field_ld_home_warum_letsdoo_heading',
				'label' => 'Titel',
				'name'  => 'warum_letsdoo_heading',
				'type'  => 'text',
				'default_value' => "Warum Let's Doo?",
			),
			array(
				'key'   => 'field_ld_home_warum_letsdoo_subheading',
				'label' => 'Untertitel',
				'name'  => 'warum_letsdoo_subheading',
				'type'  => 'text',
				'default_value' => 'Persönlich. Transparent. Lösungsorientiert.',
			),
			array(
				'key'   => 'field_ld_home_warum_letsdoo_image',
				'label' => 'Bild (Mitte)',
				'name'  => 'warum_letsdoo_image',
				'type'  => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'instructions'  => 'Leer lassen für ein Platzhalterbild. Sitzt zwischen den zwei Zweierspalten der Punkte.',
			),

			/*
			 * Four flat groups rather than a repeater — repeaters are Pro-only
			 * and never render in this admin (see the file header). The layout
			 * is a fixed two-by-two around the image, so there is nothing to
			 * add or reorder and the textarea + letsdoo_lines() trick the
			 * Paket "Merkmale" field uses would only get in the way of the icon
			 * select. Leaving a Titel empty drops that pillar from the grid.
			 */
			array(
				'key'   => 'field_ld_home_warum_letsdoo_punkte_tab',
				'label' => 'Punkte',
				'name'  => '',
				'type'  => 'message',
				'message' => 'Die vier Punkte links und rechts vom Bild, zwei pro Seite.',
			),

			array(
				'key'           => 'field_ld_home_warum_letsdoo_punkt_1_icon',
				'label'         => 'Punkt 1 – Icon',
				'name'          => 'warum_letsdoo_punkt_1_icon',
				'type'          => 'font-awesome',
				'icon_sets'     => array( 'classic_solid', 'classic_regular', 'brands' ),
				'save_format'   => 'element',
				'default_value' => '{"id":"headphones-simple","style":"regular"}',
				'wrapper'       => array( 'width' => '25' ),
			),
			array(
				'key'   => 'field_ld_home_warum_letsdoo_punkt_1_titel',
				'label' => 'Punkt 1 – Titel',
				'name'  => 'warum_letsdoo_punkt_1_titel',
				'type'  => 'text',
				'default_value' => 'Persönlich',
				'wrapper' => array( 'width' => '75' ),
			),
			array(
				'key'   => 'field_ld_home_warum_letsdoo_punkt_1_text',
				'label' => 'Punkt 1 – Text',
				'name'  => 'warum_letsdoo_punkt_1_text',
				'type'  => 'textarea',
				'rows'  => 2,
			),

			array(
				'key'           => 'field_ld_home_warum_letsdoo_punkt_2_icon',
				'label'         => 'Punkt 2 – Icon',
				'name'          => 'warum_letsdoo_punkt_2_icon',
				'type'          => 'font-awesome',
				'icon_sets'     => array( 'classic_solid', 'classic_regular', 'brands' ),
				'save_format'   => 'element',
				'default_value' => '{"id":"eye","style":"regular"}',
				'wrapper'       => array( 'width' => '25' ),
			),
			array(
				'key'   => 'field_ld_home_warum_letsdoo_punkt_2_titel',
				'label' => 'Punkt 2 – Titel',
				'name'  => 'warum_letsdoo_punkt_2_titel',
				'type'  => 'text',
				'default_value' => 'Transparent',
				'wrapper' => array( 'width' => '75' ),
			),
			array(
				'key'   => 'field_ld_home_warum_letsdoo_punkt_2_text',
				'label' => 'Punkt 2 – Text',
				'name'  => 'warum_letsdoo_punkt_2_text',
				'type'  => 'textarea',
				'rows'  => 2,
			),

			array(
				'key'           => 'field_ld_home_warum_letsdoo_punkt_3_icon',
				'label'         => 'Punkt 3 – Icon',
				'name'          => 'warum_letsdoo_punkt_3_icon',
				'type'          => 'font-awesome',
				'icon_sets'     => array( 'classic_solid', 'classic_regular', 'brands' ),
				'save_format'   => 'element',
				'default_value' => '{"id":"lightbulb","style":"regular"}',
				'wrapper'       => array( 'width' => '25' ),
			),
			array(
				'key'   => 'field_ld_home_warum_letsdoo_punkt_3_titel',
				'label' => 'Punkt 3 – Titel',
				'name'  => 'warum_letsdoo_punkt_3_titel',
				'type'  => 'text',
				'default_value' => 'Lösungsorientiert',
				'wrapper' => array( 'width' => '75' ),
			),
			array(
				'key'   => 'field_ld_home_warum_letsdoo_punkt_3_text',
				'label' => 'Punkt 3 – Text',
				'name'  => 'warum_letsdoo_punkt_3_text',
				'type'  => 'textarea',
				'rows'  => 2,
			),

			array(
				'key'           => 'field_ld_home_warum_letsdoo_punkt_4_icon',
				'label'         => 'Punkt 4 – Icon',
				'name'          => 'warum_letsdoo_punkt_4_icon',
				'type'          => 'font-awesome',
				'icon_sets'     => array( 'classic_solid', 'classic_regular', 'brands' ),
				'save_format'   => 'element',
				'default_value' => '{"id":"id-badge","style":"regular"}',
				'wrapper'       => array( 'width' => '25' ),
			),
			array(
				'key'   => 'field_ld_home_warum_letsdoo_punkt_4_titel',
				'label' => 'Punkt 4 – Titel',
				'name'  => 'warum_letsdoo_punkt_4_titel',
				'type'  => 'text',
				'default_value' => 'Erfahren',
				'wrapper' => array( 'width' => '75' ),
			),
			array(
				'key'   => 'field_ld_home_warum_letsdoo_punkt_4_text',
				'label' => 'Punkt 4 – Text',
				'name'  => 'warum_letsdoo_punkt_4_text',
				'type'  => 'textarea',
				'rows'  => 2,
			),
			array(
				'key'   => 'field_ld_home_warum_letsdoo_button_label',
				'label' => 'Button Text',
				'name'  => 'warum_letsdoo_button_label',
				'type'  => 'text',
				'default_value' => 'Kontakt aufnehmen',
			),
			array(
				'key'   => 'field_ld_home_warum_letsdoo_button_link',
				'label' => 'Button Link',
				'name'  => 'warum_letsdoo_button_link',
				'type'  => 'url',
			),

			array(
				'key'   => 'field_ld_home_vorgehen_tab',
				'label' => 'Unser Vorgehen',
				'name'  => '',
				'type'  => 'tab',
			),
			array(
				'key'   => 'field_ld_home_vorgehen_heading',
				'label' => 'Titel',
				'name'  => 'vorgehen_heading',
				'type'  => 'text',
				'default_value' => 'Unser Vorgehen',
			),
			array(
				'key'   => 'field_ld_home_vorgehen_subheading',
				'label' => 'Untertitel',
				'name'  => 'vorgehen_subheading',
				'type'  => 'text',
				'default_value' => 'Schritt für Schritt zur passenden Lösung.',
			),
			array(
				'key'   => 'field_ld_home_vorgehen_hinweis',
				'label' => 'Schritte',
				'type'  => 'message',
				'message' => 'Die einzelnen Schritte werden unter „Vorgehen“ im Menü gepflegt. Die Reihenfolge steuerst du dort über das Feld „Reihenfolge“.',
			),
			array(
				'key'   => 'field_ld_home_vorgehen_bg_image',
				'label' => 'Hintergrundbild',
				'name'  => 'vorgehen_bg_image',
				'type'  => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
			),

			array(
				'key'   => 'field_ld_home_cta_tab',
				'label' => 'Abschluss CTA',
				'name'  => '',
				'type'  => 'tab',
			),
			array(
				'key'   => 'field_ld_home_cta_heading',
				'label' => 'Titel',
				'name'  => 'cta_heading',
				'type'  => 'text',
				'default_value' => 'Bereit für den nächsten Schritt?',
			),
			array(
				'key'   => 'field_ld_home_cta_text',
				'label' => 'Text',
				'name'  => 'cta_text',
				'type'  => 'textarea',
				'rows'  => 2,
			),
			array(
				'key'   => 'field_ld_home_cta_button_label',
				'label' => 'Button Text',
				'name'  => 'cta_button_label',
				'type'  => 'text',
				'default_value' => 'Jetzt kontaktieren',
			),
			array(
				'key'   => 'field_ld_home_cta_button_link',
				'label' => 'Button Link',
				'name'  => 'cta_button_link',
				'type'  => 'url',
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'page_template',
					'operator' => '==',
					'value'    => 'page-templates/template-home.php',
				),
			),
		),
	) );

	/* -------------------------------------------------- */
	/* Blog overview (the page set as "Beitragsseite")      */
	/* -------------------------------------------------- */
	acf_add_local_field_group( array(
		'key'    => 'group_ld_blog',
		'title'  => 'Blog Inhalte',
		'fields' => array(
			array(
				'key'   => 'field_ld_blog_hero_heading',
				'label' => 'Titel',
				'name'  => 'hero_heading',
				'type'  => 'text',
				'instructions' => 'Leer lassen, um den Seitentitel zu verwenden.',
			),
			array(
				'key'   => 'field_ld_blog_hero_text',
				'label' => 'Text',
				'name'  => 'hero_text',
				'type'  => 'textarea',
				'rows'  => 3,
			),
			array(
				'key'   => 'field_ld_blog_hero_image',
				'label' => 'Hintergrundbild',
				'name'  => 'hero_image',
				'type'  => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'page_type',
					'operator' => '==',
					'value'    => 'posts_page',
				),
			),
		),
	) );

	/* -------------------------------------------------- */
	/* About template                                       */
	/* -------------------------------------------------- */
	acf_add_local_field_group( array(
		'key'    => 'group_ld_about',
		'title'  => 'Über-uns Inhalte',
		'fields' => array(
			array(
				'key'   => 'field_ld_about_hero_tab',
				'label' => 'Hero',
				'name'  => '',
				'type'  => 'tab',
			),
			array(
				'key'   => 'field_ld_about_hero_heading',
				'label' => 'Titel',
				'name'  => 'hero_heading',
				'type'  => 'text',
				'default_value' => 'Über uns',
			),
			array(
				'key'   => 'field_ld_about_hero_text',
				'label' => 'Text',
				'name'  => 'hero_text',
				'type'  => 'textarea',
				'rows'  => 3,
			),
			array(
				'key'   => 'field_ld_about_hero_image',
				'label' => 'Hintergrundbild',
				'name'  => 'hero_image',
				'type'  => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
			),

			array(
				'key'   => 'field_ld_about_referenzen_tab',
				'label' => 'Referenzen',
				'name'  => '',
				'type'  => 'tab',
			),
			array(
				'key'   => 'field_ld_about_referenzen_heading',
				'label' => 'Titel',
				'name'  => 'referenzen_heading',
				'type'  => 'text',
				'default_value' => 'Referenzen',
				'instructions' => 'Die Karten selbst werden unter Referenzen im Menü gepflegt.',
			),
			letsdoo_post_selector_field( 'about_referenzen', 'referenz', 'referenzen_auswahl', 'Referenzen auswählen' ),

			array(
				'key'   => 'field_ld_about_zahlen_tab',
				'label' => 'Zahlen',
				'name'  => '',
				'type'  => 'tab',
			),
			array(
				'key'   => 'field_ld_about_zahlen_heading',
				'label' => 'Titel',
				'name'  => 'zahlen_heading',
				'type'  => 'text',
				'default_value' => 'Du glaubst an Zahlen?',
			),
			array(
				'key'   => 'field_ld_about_zahlen_text',
				'label' => 'Text',
				'name'  => 'zahlen_text',
				'type'  => 'textarea',
				'rows'  => 2,
			),
			array(
				'key'   => 'field_ld_about_zahlen_bg_image',
				'label' => 'Hintergrundbild',
				'name'  => 'zahlen_bg_image',
				'type'  => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
			),
			array(
				'key'          => 'field_ld_about_zahlen_liste',
				'label'        => 'Zahlen',
				'name'         => 'zahlen_liste',
				'type'         => 'repeater',
				'layout'       => 'table',
				'button_label' => 'Zahl hinzufügen',
				'sub_fields'   => array(
					array(
						'key'           => 'field_ld_about_zahl_icon',
						'label'         => 'Icon',
						'name'          => 'icon',
						'type'          => 'font-awesome',
						'icon_sets'     => array( 'classic_solid', 'classic_regular', 'brands' ),
						'save_format'   => 'element',
						'default_value' => '{"id":"circle-check","style":"regular"}',
						'wrapper'       => array( 'width' => '20' ),
					),
					array(
						'key'   => 'field_ld_about_zahl_wert',
						'label' => 'Zahl',
						'name'  => 'zahl_wert',
						'type'  => 'text',
						'wrapper' => array( 'width' => '25' ),
					),
					array(
						'key'   => 'field_ld_about_zahl_label',
						'label' => 'Beschriftung',
						'name'  => 'zahl_label',
						'type'  => 'text',
						'wrapper' => array( 'width' => '55' ),
					),
				),
			),

			array(
				'key'   => 'field_ld_about_team_tab',
				'label' => 'Team',
				'name'  => '',
				'type'  => 'tab',
			),
			array(
				'key'   => 'field_ld_about_team_heading',
				'label' => 'Titel',
				'name'  => 'team_heading',
				'type'  => 'text',
				'default_value' => 'Unser Team',
				'instructions' => 'Die Teammitglieder selbst werden unter Team im Menü gepflegt.',
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'page_template',
					'operator' => '==',
					'value'    => 'page-templates/template-about.php',
				),
			),
		),
	) );

	/* -------------------------------------------------- */
	/* Contact template                                     */
	/* -------------------------------------------------- */
	acf_add_local_field_group( array(
		'key'    => 'group_ld_contact',
		'title'  => 'Kontakt Inhalte',
		'fields' => array(
			array(
				'key'   => 'field_ld_contact_hero_heading',
				'label' => 'Titel',
				'name'  => 'hero_heading',
				'type'  => 'text',
				'default_value' => 'Kontakt',
			),
			array(
				'key'   => 'field_ld_contact_hero_subheading',
				'label' => 'Untertitel',
				'name'  => 'hero_subheading',
				'type'  => 'text',
				'default_value' => 'Kontaktiere uns',
			),
			array(
				'key'   => 'field_ld_contact_hero_text',
				'label' => 'Text',
				'name'  => 'hero_text',
				'type'  => 'textarea',
				'rows'  => 2,
			),
			array(
				'key'   => 'field_ld_contact_hero_image',
				'label' => 'Hintergrundbild',
				'name'  => 'hero_image',
				'type'  => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
			),
			array(
				'key'   => 'field_ld_contact_form_shortcode',
				'label' => 'Contact Form 7 Shortcode',
				'name'  => 'form_shortcode',
				'type'  => 'text',
				'instructions' => 'z.B. [contact-form-7 id="12" title="Kontaktformular"]',
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'page_template',
					'operator' => '==',
					'value'    => 'page-templates/template-contact.php',
				),
			),
		),
	) );

	/* -------------------------------------------------- */
	/* Angebote template                                    */
	/* -------------------------------------------------- */
	acf_add_local_field_group( array(
		'key'    => 'group_ld_angebote',
		'title'  => 'Angebote Inhalte',
		'fields' => array(

			array(
				'key'   => 'field_ld_angebote_hero_tab',
				'label' => 'Hero',
				'name'  => '',
				'type'  => 'tab',
			),
			array(
				'key'   => 'field_ld_angebote_hero_badge',
				'label' => 'Badge',
				'name'  => 'hero_badge',
				'type'  => 'text',
				'default_value' => 'Offizieller Odoo Partner',
			),
			array(
				'key'   => 'field_ld_angebote_hero_heading',
				'label' => 'Titel',
				'name'  => 'hero_heading',
				'type'  => 'text',
				'default_value' => 'Angebote & Pakete',
			),
			array(
				'key'   => 'field_ld_angebote_hero_text',
				'label' => 'Text',
				'name'  => 'hero_text',
				'type'  => 'textarea',
				'rows'  => 3,
			),
			array(
				'key'   => 'field_ld_angebote_hero_image',
				'label' => 'Hintergrundbild',
				'name'  => 'hero_image',
				'type'  => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
			),
			array(
				'key'   => 'field_ld_angebote_hero_button_label',
				'label' => 'Button Text',
				'name'  => 'hero_button_label',
				'type'  => 'text',
				'default_value' => 'Jetzt beraten lassen',
			),
			array(
				'key'   => 'field_ld_angebote_hero_button_link',
				'label' => 'Button Link',
				'name'  => 'hero_button_link',
				'type'  => 'url',
			),

			array(
				'key'   => 'field_ld_angebote_leistungen_tab',
				'label' => 'Unsere Leistungen',
				'name'  => '',
				'type'  => 'tab',
			),
			/*
			 * Only the section heading. The cards themselves come from the
			 * Leistung post type, the same ones the home page renders — there
			 * was a second, parallel "Leistungen" repeater here that no template
			 * ever read. It was invisible while this site ran ACF free, so it
			 * did no harm; under PRO it rendered as a working field that
			 * silently threw its content away, so it is gone.
			 */
			array(
				'key'   => 'field_ld_angebote_leistungen_heading',
				'label' => 'Titel',
				'name'  => 'leistungen_heading',
				'type'  => 'text',
				'default_value' => 'Unsere Leistungen',
				'instructions' => 'Die Karten selbst werden unter Leistungen im Menü gepflegt.',
			),
			letsdoo_post_selector_field( 'angebote_leistungen', 'leistung', 'leistungen_auswahl', 'Leistungen auswählen' ),

			array(
				'key'   => 'field_ld_angebote_vertrauen_tab',
				'label' => 'Vertrauen',
				'name'  => '',
				'type'  => 'tab',
			),
			array(
				'key'   => 'field_ld_angebote_vertrauen_heading',
				'label' => 'Titel',
				'name'  => 'vertrauen_heading',
				'type'  => 'text',
				'default_value' => 'Expertise aus Leidenschaft',
			),
			array(
				'key'   => 'field_ld_angebote_vertrauen_text',
				'label' => 'Text',
				'name'  => 'vertrauen_text',
				'type'  => 'textarea',
				'rows'  => 3,
			),
			array(
				'key'   => 'field_ld_angebote_vertrauen_image',
				'label' => 'Bild',
				'name'  => 'vertrauen_image',
				'type'  => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
			),
			array(
				'key'          => 'field_ld_angebote_vertrauen_vorteile',
				'label'        => 'Vorteile',
				'name'         => 'vertrauen_vorteile',
				'type'         => 'repeater',
				'layout'       => 'table',
				'button_label' => 'Vorteil hinzufügen',
				'sub_fields'   => array(
					array(
						'key'   => 'field_ld_angebote_vorteil_titel',
						'label' => 'Titel',
						'name'  => 'titel',
						'type'  => 'text',
						'wrapper' => array( 'width' => '30' ),
					),
					array(
						'key'   => 'field_ld_angebote_vorteil_text',
						'label' => 'Text',
						'name'  => 'text',
						'type'  => 'text',
						'wrapper' => array( 'width' => '70' ),
					),
				),
			),

			array(
				'key'   => 'field_ld_angebote_pakete_tab',
				'label' => 'Pakete',
				'name'  => '',
				'type'  => 'tab',
			),
			array(
				'key'   => 'field_ld_angebote_pakete_heading',
				'label' => 'Titel',
				'name'  => 'pakete_heading',
				'type'  => 'text',
				'default_value' => 'Wählen Sie Ihr Paket',
			),
			array(
				'key'   => 'field_ld_angebote_pakete_subheading',
				'label' => 'Untertitel',
				'name'  => 'pakete_subheading',
				'type'  => 'text',
				'default_value' => 'Transparente Preise für jedes Unternehmensstadium.',
				'instructions' => 'Die Paket-Karten selbst werden unter Pakete im Menü gepflegt.',
			),

			array(
				'key'   => 'field_ld_angebote_cta_tab',
				'label' => 'Abschluss CTA',
				'name'  => '',
				'type'  => 'tab',
			),
			array(
				'key'   => 'field_ld_angebote_cta_heading',
				'label' => 'Titel',
				'name'  => 'cta_heading',
				'type'  => 'text',
				'default_value' => 'Bereit für den nächsten Schritt?',
			),
			array(
				'key'   => 'field_ld_angebote_cta_text',
				'label' => 'Text',
				'name'  => 'cta_text',
				'type'  => 'textarea',
				'rows'  => 2,
			),
			array(
				'key'   => 'field_ld_angebote_cta_button_label',
				'label' => 'Button Text (primär)',
				'name'  => 'cta_button_label',
				'type'  => 'text',
				'default_value' => 'Kostenloser Demo-Termin',
				'wrapper' => array( 'width' => '50' ),
			),
			array(
				'key'   => 'field_ld_angebote_cta_button_link',
				'label' => 'Button Link (primär)',
				'name'  => 'cta_button_link',
				'type'  => 'url',
				'wrapper' => array( 'width' => '50' ),
			),
			array(
				'key'   => 'field_ld_angebote_cta_button2_label',
				'label' => 'Button Text (sekundär)',
				'name'  => 'cta_button2_label',
				'type'  => 'text',
				'default_value' => 'Kontakt aufnehmen',
				'wrapper' => array( 'width' => '50' ),
			),
			array(
				'key'   => 'field_ld_angebote_cta_button2_link',
				'label' => 'Button Link (sekundär)',
				'name'  => 'cta_button2_link',
				'type'  => 'url',
				'wrapper' => array( 'width' => '50' ),
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'page_template',
					'operator' => '==',
					'value'    => 'page-templates/template-angebote.php',
				),
			),
		),
	) );

	/* -------------------------------------------------- */
	/* Odoo-Seite (page_template fields)                   */
	/* -------------------------------------------------- */

	/*
	 * Only the hero — a guaranteed H1 and lead text, same reasoning as the
	 * Standort hero (inc/cpt.php). Everything below it is the_content(),
	 * built from the theme's blocks, so the page reads like a competitor's
	 * dedicated Odoo landing page but stays fully editable and reorderable.
	 */
	acf_add_local_field_group( array(
		'key'    => 'group_ld_page_odoo',
		'title'  => 'Odoo-Seite: Hero',
		'fields' => array(
			array(
				'key'   => 'field_ld_odoo_hero_heading',
				'label' => 'Titel',
				'name'  => 'hero_heading',
				'type'  => 'text',
				'default_value' => 'Odoo',
			),
			array(
				'key'   => 'field_ld_odoo_hero_subheading',
				'label' => 'Untertitel',
				'name'  => 'hero_subheading',
				'type'  => 'text',
				'default_value' => 'Eine Plattform. Alle Prozesse.',
			),
			array(
				'key'   => 'field_ld_odoo_hero_text',
				'label' => 'Text',
				'name'  => 'hero_text',
				'type'  => 'textarea',
				'rows'  => 2,
			),
			array(
				'key'   => 'field_ld_odoo_hero_image',
				'label' => 'Hintergrundbild',
				'name'  => 'hero_image',
				'type'  => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
			),
			array(
				'key'   => 'field_ld_odoo_hero_button_label',
				'label' => 'Button Text',
				'name'  => 'hero_button_label',
				'type'  => 'text',
				'default_value' => 'Kontakt aufnehmen',
			),
			array(
				'key'   => 'field_ld_odoo_hero_button_link',
				'label' => 'Button Link',
				'name'  => 'hero_button_link',
				'type'  => 'url',
				'instructions' => 'Leer lassen für die Kontaktseite.',
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'page_template',
					'operator' => '==',
					'value'    => 'page-templates/template-odoo.php',
				),
			),
		),
	) );

	/* -------------------------------------------------- */
	/* Block: Textabschnitt                                */
	/* -------------------------------------------------- */

	acf_add_local_field_group( array(
		'key'    => 'group_ld_block_textabschnitt',
		'title'  => 'Textabschnitt',
		'fields' => array(
			array(
				'key'   => 'field_ld_block_text_heading',
				'label' => 'Titel',
				'name'  => 'heading',
				'type'  => 'text',
				'instructions' => 'Leer lassen für „Odoo-Partner in &lt;Ort&gt;“ (nur auf Standortseiten).',
			),
			/*
			 * WYSIWYG, not a textarea. This is the field the Standort pages live
			 * or die on — Google filters out sets of near-identical location
			 * pages — and until now it could not carry so much as a link.
			 */
			array(
				'key'          => 'field_ld_block_text_text',
				'label'        => 'Text',
				'name'         => 'text',
				'type'         => 'wysiwyg',
				'tabs'         => 'visual',
				'media_upload' => 0,
				'delay'        => 1,
				'instructions' => 'Auf Standortseiten bitte pro Ort neu schreiben – Branchen in der Region, Anfahrt, konkrete Projekte. Derselbe Text mit ausgetauschtem Ortsnamen wird von Google als Doorway-Page erkannt.',
			),
			letsdoo_block_blend_field( 'text' ),
		),
		'location' => array(
			array(
				array( 'param' => 'block', 'operator' => '==', 'value' => 'letsdoo/textabschnitt' ),
			),
		),
	) );

	/* -------------------------------------------------- */
	/* Block: Text & Bild                                  */
	/* -------------------------------------------------- */

	acf_add_local_field_group( array(
		'key'    => 'group_ld_block_text_bild',
		'title'  => 'Text & Bild',
		'fields' => array(
			array(
				'key'   => 'field_ld_block_text_bild_heading',
				'label' => 'Titel',
				'name'  => 'heading',
				'type'  => 'text',
				'placeholder' => 'Warum Odoo?',
			),
			array(
				'key'   => 'field_ld_block_text_bild_subheading',
				'label' => 'Untertitel',
				'name'  => 'subheading',
				'type'  => 'text',
				'instructions' => 'Fett gesetzte Lead-Zeile unter dem Titel. Optional.',
			),
			array(
				'key'          => 'field_ld_block_text_bild_text',
				'label'        => 'Text',
				'name'         => 'text',
				'type'         => 'wysiwyg',
				'tabs'         => 'visual',
				'media_upload' => 0,
				'delay'        => 1,
			),
			array(
				'key'   => 'field_ld_block_text_bild_bild',
				'label' => 'Bild',
				'name'  => 'bild',
				'type'  => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'instructions'  => 'Ohne Bild wird nur die Textspalte ausgegeben.',
				'wrapper' => array( 'width' => '50' ),
			),
			array(
				'key'     => 'field_ld_block_text_bild_position',
				'label'   => 'Bild-Seite',
				'name'    => 'bild_position',
				'type'    => 'select',
				'choices' => array(
					'rechts' => 'Rechts vom Text',
					'links'  => 'Links vom Text',
				),
				'default_value' => 'rechts',
				'instructions'  => 'Bei mehreren Abschnitten untereinander am besten abwechselnd.',
				'wrapper' => array( 'width' => '50' ),
			),
			/* Link field rather than a label + URL pair, same as the CTA-Band
			   block: one field, and the editor gets the WordPress link picker
			   instead of a URL that breaks when a page is re-slugged. */
			array(
				'key'   => 'field_ld_block_text_bild_button',
				'label' => 'Button',
				'name'  => 'button',
				'type'  => 'link',
				'return_format' => 'array',
				'instructions'  => 'Optional. Wird nur angezeigt, wenn gesetzt.',
			),
			letsdoo_block_blend_field( 'text_bild' ),
		),
		'location' => array(
			array(
				array( 'param' => 'block', 'operator' => '==', 'value' => 'letsdoo/text-bild' ),
			),
		),
	) );

	/* -------------------------------------------------- */
	/* Block: Leistungen                                   */
	/* -------------------------------------------------- */

	acf_add_local_field_group( array(
		'key'    => 'group_ld_block_leistungen',
		'title'  => 'Leistungen',
		'fields' => array(
			array(
				'key'   => 'field_ld_block_leistungen_heading',
				'label' => 'Titel',
				'name'  => 'heading',
				'type'  => 'text',
				'instructions' => 'Leer lassen für „Unsere Leistungen für &lt;Ort&gt;“. Die Karten selbst werden unter Leistungen im Menü gepflegt.',
			),
			letsdoo_post_selector_field( 'block_leistungen', 'leistung', 'leistungen', 'Leistungen auswählen' ),
			letsdoo_block_blend_field( 'leistungen', 1 ),
		),
		'location' => array(
			array(
				array( 'param' => 'block', 'operator' => '==', 'value' => 'letsdoo/leistungen' ),
			),
		),
	) );

	/* -------------------------------------------------- */
	/* Block: Referenzen (letsdoo/referenz-karte)           */
	/* -------------------------------------------------- */

	acf_add_local_field_group( array(
		'key'    => 'group_ld_block_referenz',
		'title'  => 'Referenzen',
		'fields' => array(
			array(
				'key'   => 'field_ld_block_referenz_heading',
				'label' => 'Titel',
				'name'  => 'heading',
				'type'  => 'text',
				'instructions' => 'Leer lassen für „Aus der Region &lt;Ort&gt;“.',
			),
			array(
				'key'   => 'field_ld_block_referenz_referenz',
				'label' => 'Referenzen',
				'name'  => 'referenz',
				'type'  => 'post_object',
				'post_type' => array( 'referenz' ),
				'multiple' => 1,
				'return_format' => 'id',
				'allow_null' => 1,
				'ui' => 1,
				'instructions' => 'Auf einer Standortseite der stärkste Beleg dafür, dass die Seite echt ist. Eine Referenz zeigt eine einzelne Karte, mehrere ein kleines Raster.',
			),
			letsdoo_block_blend_field( 'referenz' ),
		),
		'location' => array(
			array(
				array( 'param' => 'block', 'operator' => '==', 'value' => 'letsdoo/referenz-karte' ),
			),
		),
	) );

	/* -------------------------------------------------- */
	/* Block: Zahlen                                       */
	/* -------------------------------------------------- */

	acf_add_local_field_group( array(
		'key'    => 'group_ld_block_zahlen',
		'title'  => 'Zahlen',
		'fields' => array(
			array(
				'key'   => 'field_ld_block_zahlen_heading',
				'label' => 'Titel',
				'name'  => 'heading',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_ld_block_zahlen_text',
				'label' => 'Text',
				'name'  => 'text',
				'type'  => 'textarea',
				'rows'  => 2,
			),
			array(
				'key'          => 'field_ld_block_zahlen_liste',
				'label'        => 'Zahlen',
				'name'         => 'zahlen',
				'type'         => 'repeater',
				'layout'       => 'table',
				'button_label' => 'Zahl hinzufügen',
				'sub_fields'   => array(
					array(
						'key'           => 'field_ld_block_zahl_icon',
						'label'         => 'Icon',
						'name'          => 'icon',
						'type'          => 'font-awesome',
						'icon_sets'     => array( 'classic_solid', 'classic_regular', 'brands' ),
						'save_format'   => 'element',
						'default_value' => '{"id":"circle-check","style":"regular"}',
						'wrapper'       => array( 'width' => '20' ),
					),
					array(
						'key'   => 'field_ld_block_zahl_wert',
						'label' => 'Zahl',
						'name'  => 'wert',
						'type'  => 'text',
						'wrapper' => array( 'width' => '25' ),
					),
					array(
						'key'   => 'field_ld_block_zahl_label',
						'label' => 'Beschriftung',
						'name'  => 'label',
						'type'  => 'text',
						'wrapper' => array( 'width' => '55' ),
					),
				),
			),
			letsdoo_block_blend_field( 'zahlen', 1 ),
		),
		'location' => array(
			array(
				array( 'param' => 'block', 'operator' => '==', 'value' => 'letsdoo/zahlen' ),
			),
		),
	) );

	/* -------------------------------------------------- */
	/* Block: FAQ                                          */
	/* -------------------------------------------------- */

	acf_add_local_field_group( array(
		'key'    => 'group_ld_block_faq',
		'title'  => 'FAQ',
		'fields' => array(
			array(
				'key'   => 'field_ld_block_faq_heading',
				'label' => 'Titel',
				'name'  => 'heading',
				'type'  => 'text',
				'placeholder' => 'Häufige Fragen',
			),
			/*
			 * Replaces the "Frage | Antwort, eine pro Zeile" textarea. Besides
			 * being easier to fill in, the split fields are what let inc/seo.php
			 * emit FAQPage structured data without re-parsing free text.
			 */
			array(
				'key'          => 'field_ld_block_faq_fragen',
				'label'        => 'Fragen',
				'name'         => 'fragen',
				'type'         => 'repeater',
				'layout'       => 'row',
				'button_label' => 'Frage hinzufügen',
				'sub_fields'   => array(
					array(
						'key'   => 'field_ld_block_faq_frage',
						'label' => 'Frage',
						'name'  => 'frage',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_ld_block_faq_antwort',
						'label' => 'Antwort',
						'name'  => 'antwort',
						'type'  => 'textarea',
						'rows'  => 3,
						'instructions' => 'Ohne Antwort erscheint die Frage auf der Seite, aber nicht im Google-Schema.',
					),
				),
			),
			letsdoo_block_blend_field( 'faq' ),
		),
		'location' => array(
			array(
				array( 'param' => 'block', 'operator' => '==', 'value' => 'letsdoo/faq' ),
			),
		),
	) );

	/* -------------------------------------------------- */
	/* Block: CTA-Band                                     */
	/* -------------------------------------------------- */

	/*
	 * Fields for the letsdoo/cta-band block (see blocks/cta-band/). Located by
	 * block name rather than post type, so the group follows the block wherever
	 * it is inserted.
	 *
	 * Everything is optional: render.php falls back to the copy these bands
	 * used while they were hardcoded, so an inserted block is presentable
	 * before it is filled in.
	 */
	acf_add_local_field_group( array(
		'key'    => 'group_ld_block_cta_band',
		'title'  => 'CTA-Band',
		'fields' => array(
			array(
				'key'     => 'field_ld_block_cta_variante',
				'label'   => 'Darstellung',
				'name'    => 'variante',
				'type'    => 'select',
				'choices' => array(
					'karte' => 'Karte mit Farbverlauf',
					'voll'  => 'Vollbreite mit Farbverlauf-Hintergrund',
				),
				'default_value' => 'karte',
				'instructions'  => 'Die Vollbreiten-Variante bringt eigene Wellen mit – nicht direkt neben einen anderen farbigen Abschnitt setzen.',
			),
			array(
				'key'   => 'field_ld_block_cta_heading',
				'label' => 'Titel',
				'name'  => 'heading',
				'type'  => 'text',
				'placeholder' => 'Bereit für den nächsten Schritt?',
				'instructions' => 'Leer lassen für „Odoo-Projekt in &lt;Ort&gt; geplant?“ (auf Standortseiten) bzw. „Bereit für den nächsten Schritt?“.',
			),
			array(
				'key'   => 'field_ld_block_cta_text',
				'label' => 'Text',
				'name'  => 'text',
				'type'  => 'textarea',
				'rows'  => 2,
			),
			/*
			 * Link fields rather than the label + URL text pairs used elsewhere
			 * in this file: one field instead of two, and the editor gets the
			 * WordPress link picker with page search instead of having to paste
			 * a full URL that breaks when a page is re-slugged.
			 */
			array(
				'key'   => 'field_ld_block_cta_button',
				'label' => 'Button (primär)',
				'name'  => 'button',
				'type'  => 'link',
				'return_format' => 'array',
				'instructions'  => 'Leer lassen für „Kontakt aufnehmen“ zur Kontaktseite.',
				'wrapper' => array( 'width' => '50' ),
			),
			array(
				'key'   => 'field_ld_block_cta_button2',
				'label' => 'Button (sekundär)',
				'name'  => 'button2',
				'type'  => 'link',
				'return_format' => 'array',
				'instructions'  => 'Optional. Wird nur angezeigt, wenn gesetzt.',
				'wrapper' => array( 'width' => '50' ),
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'letsdoo/cta-band',
				),
			),
		),
	) );

	/* -------------------------------------------------- */
	/* Block: Plattform (App-Raster)                       */
	/* -------------------------------------------------- */

	acf_add_local_field_group( array(
		'key'    => 'group_ld_block_plattform',
		'title'  => 'Plattform (App-Raster)',
		'fields' => array(
			array(
				'key'   => 'field_ld_block_plattform_heading',
				'label' => 'Titel',
				'name'  => 'heading',
				'type'  => 'text',
				'placeholder' => 'Eine Plattform für jeden Bereich',
			),
			array(
				'key'   => 'field_ld_block_plattform_text',
				'label' => 'Text',
				'name'  => 'text',
				'type'  => 'textarea',
				'rows'  => 2,
			),
			array(
				'key'          => 'field_ld_block_plattform_kategorien',
				'label'        => 'Kategorien',
				'name'         => 'kategorien',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Kategorie hinzufügen',
				'sub_fields'   => array(
					array(
						'key'   => 'field_ld_block_plattform_label',
						'label' => 'Kategorie',
						'name'  => 'label',
						'type'  => 'text',
					),
					/*
					 * Each app gets Odoo's own official icon (vendored in
					 * assets/images/odoo-icons/, see letsdoo_odoo_icon_choices()
					 * in inc/template-helpers.php) plus a link to that app's page
					 * on odoo.com — the same "icon + name, linked" row Mint
					 * System's Odoo page uses.
					 */
					array(
						'key'          => 'field_ld_block_plattform_apps',
						'label'        => 'Apps',
						'name'         => 'apps',
						'type'         => 'repeater',
						'layout'       => 'table',
						'button_label' => 'App hinzufügen',
						'sub_fields'   => array(
							array(
								'key'     => 'field_ld_block_plattform_app_icon',
								'label'   => 'Icon',
								'name'    => 'icon',
								'type'    => 'select',
								'choices' => letsdoo_odoo_icon_choices(),
								'ui'      => 1,
								'wrapper' => array( 'width' => '30' ),
							),
							array(
								'key'     => 'field_ld_block_plattform_app_label',
								'label'   => 'Name',
								'name'    => 'label',
								'type'    => 'text',
								'wrapper' => array( 'width' => '30' ),
							),
							array(
								'key'          => 'field_ld_block_plattform_app_url',
								'label'        => 'Link',
								'name'         => 'url',
								'type'         => 'url',
								'instructions' => 'z. B. https://www.odoo.com/app/crm',
								'wrapper'      => array( 'width' => '40' ),
							),
						),
					),
				),
			),
			/*
			 * Optional last grid card — "Individuelle Module & Anbindungen?"
			 * or similar — that breaks from the app-list cards to a gradient
			 * card with a contact button, same recipe as .cta-banner
			 * (19-angebote-cta.css). Presence-triggered like the rest of the
			 * theme's optional content (the CTA-Band's second button, the
			 * Referenzen block): fill in a title and the card appears, leave
			 * it empty and the grid renders exactly as it did before this
			 * existed. No link field for the button — it always reads
			 * "Kontakt aufnehmen" and always opens the Kontakt modal, not
			 * something an editor can point elsewhere.
			 */
			array(
				'key'   => 'field_ld_block_plattform_cta_heading',
				'label' => 'Zusatzkarte: Titel',
				'name'  => 'cta_heading',
				'type'  => 'text',
				'placeholder'  => 'Individuelle Module & Anbindungen?',
				'instructions' => 'Optional: hängt dem Raster eine letzte Karte mit Farbverlauf und Kontakt-Button an. Leer lassen, um keine Zusatzkarte zu zeigen.',
			),
			array(
				'key'   => 'field_ld_block_plattform_cta_text',
				'label' => 'Zusatzkarte: Text',
				'name'  => 'cta_text',
				'type'  => 'textarea',
				'rows'  => 3,
				'conditional_logic' => array(
					array(
						array( 'field' => 'field_ld_block_plattform_cta_heading', 'operator' => '!=empty' ),
					),
				),
			),
			letsdoo_block_blend_field( 'plattform' ),
		),
		'location' => array(
			array(
				array( 'param' => 'block', 'operator' => '==', 'value' => 'letsdoo/plattform' ),
			),
		),
	) );

	/* -------------------------------------------------- */
	/* Nav menu item: mega-menu promo tile                 */
	/* -------------------------------------------------- */

	/*
	 * Shows up on every primary-nav menu item's own edit panel in
	 * Appearance → Menüs, not just ones with a submenu — ACF's "Menu Item"
	 * location can't be conditioned on whether an item happens to have
	 * children, only on which registered menu it's in. inc/nav-walker.php
	 * only ever calls letsdoo_nav_promo_card() while rendering a depth-0
	 * item's dropdown, so filling this in on a childless item is harmless:
	 * nothing reads it.
	 */
	acf_add_local_field_group( array(
		'key'    => 'group_ld_nav_promo',
		'title'  => 'Mega-Menü: Promo-Kachel',
		'fields' => array(
			array(
				'key'   => 'field_ld_nav_promo_badge',
				'label' => 'Badge',
				'name'  => 'promo_badge',
				'type'  => 'text',
				'instructions' => 'Optional. Kurzes Label über dem Titel, z. B. „Neu“.',
			),
			array(
				'key'   => 'field_ld_nav_promo_heading',
				'label' => 'Titel',
				'name'  => 'promo_heading',
				'type'  => 'text',
				'instructions' => 'Nur wirksam bei Menüpunkten mit Untermenü. Leer lassen, um im Dropdown keine Promo-Kachel zu zeigen.',
			),
			array(
				'key'   => 'field_ld_nav_promo_text',
				'label' => 'Text',
				'name'  => 'promo_text',
				'type'  => 'textarea',
				'rows'  => 2,
				'conditional_logic' => array(
					array(
						array( 'field' => 'field_ld_nav_promo_heading', 'operator' => '!=empty' ),
					),
				),
			),
			array(
				'key'   => 'field_ld_nav_promo_image',
				'label' => 'Bild',
				'name'  => 'promo_image',
				'type'  => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'instructions'  => 'Optional. Leer lassen für den einfarbigen Marken-Farbverlauf. Ein Bild wird darüber gelegt (leicht abgedunkelt, damit Titel/Text/Button lesbar bleiben).',
				'conditional_logic' => array(
					array(
						array( 'field' => 'field_ld_nav_promo_heading', 'operator' => '!=empty' ),
					),
				),
			),
		),
		'location' => array(
			array(
				array( 'param' => 'nav_menu_item', 'operator' => '==', 'value' => 'primary' ),
			),
		),
	) );

	/* -------------------------------------------------- */
	/* Standort (SEO landing page fields)                  */
	/* -------------------------------------------------- */

	/*
	 * These pages live or die on the copy, not the layout. Google treats a set
	 * of near-identical location pages as doorway pages and filters them out, so
	 * the fields that carry the genuinely local content (Lokal-Text, Referenz,
	 * FAQ) are the point of the whole post type — the instructions say so where
	 * the editor will actually read them.
	 */
	acf_add_local_field_group( array(
		'key'    => 'group_ld_standort',
		'title'  => 'Standort',
		'fields' => array(
			array(
				'key'   => 'field_ld_standort_seo_tab',
				'label' => 'Suchmaschine',
				'name'  => '',
				'type'  => 'tab',
			),
			array(
				'key'   => 'field_ld_standort_ort',
				'label' => 'Ort',
				'name'  => 'ort',
				'type'  => 'text',
				'instructions' => 'Nur der Ortsname, z.B. „Hochdorf“. Wird in Titel, Text und Schema verwendet.',
				'required' => 1,
				'wrapper' => array( 'width' => '50' ),
			),
			array(
				'key'   => 'field_ld_standort_kanton',
				'label' => 'Kanton',
				'name'  => 'kanton',
				'type'  => 'text',
				'default_value' => 'Luzern',
				'wrapper' => array( 'width' => '50' ),
			),
			array(
				'key'   => 'field_ld_standort_seo_title',
				'label' => 'SEO-Titel',
				'name'  => 'seo_title',
				'type'  => 'text',
				/*
				 * The entities are load-bearing. ACF prints instructions as raw
				 * HTML, so a literal <title> here opened a RAWTEXT element in the
				 * middle of the edit screen and the browser swallowed the whole
				 * rest of the document as title text — including the footer where
				 * the block editor scripts are printed. The result was a blank
				 * white edit screen with no JS error and nothing in the PHP log.
				 * Placeholders like <Ort> are less dramatic but still wrong: the
				 * parser drops them as unknown tags, so the hint rendered as
				 * „Odoo  – “. Escape angle brackets in every instruction.
				 */
				'instructions' => 'Der &lt;title&gt; für Google. Leer lassen für „Odoo &lt;Ort&gt; – &lt;Seitenname&gt;“. Ziel: unter 60 Zeichen.',
			),
			array(
				'key'   => 'field_ld_standort_meta_description',
				'label' => 'Meta-Description',
				'name'  => 'meta_description',
				'type'  => 'textarea',
				'rows'  => 2,
				'instructions' => 'Der Beschreibungstext im Suchergebnis. Ziel: 150–160 Zeichen, mit Ortsname.',
			),

			array(
				'key'   => 'field_ld_standort_hero_tab',
				'label' => 'Hero',
				'name'  => '',
				'type'  => 'tab',
			),
			array(
				'key'   => 'field_ld_standort_hero_heading',
				'label' => 'Titel (H1)',
				'name'  => 'hero_heading',
				'type'  => 'text',
				'instructions' => 'Leer lassen für „Odoo &lt;Ort&gt;“.',
			),
			array(
				'key'   => 'field_ld_standort_hero_text',
				'label' => 'Lead-Text',
				'name'  => 'hero_text',
				'type'  => 'textarea',
				'rows'  => 3,
			),
			array(
				'key'   => 'field_ld_standort_hero_image',
				'label' => 'Hero-Bild',
				'name'  => 'hero_image',
				'type'  => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
			),

			/*
			 * The local copy, the regional Referenz and the FAQ used to be
			 * fields here. They are blocks now (blocks/textabschnitt,
			 * blocks/referenz-karte, blocks/faq) so each town can carry a
			 * different set in a different order.
			 *
			 * What stays is what describes the page rather than a section of
			 * it: the town, and what Google is shown in the result. Those
			 * belong in the sidebar, not in the canvas.
			 *
			 * The old meta is deliberately left in the database — removing a
			 * field definition does not delete its values, so the pre-block
			 * content is still recoverable if a migration turns out to have
			 * dropped something.
			 */
		),
		'location' => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'standort',
				),
			),
		),
	) );

} );
