<?php
/**
 * ACF field groups, registered as code so they're version-controlled
 * instead of living only in the database.
 *
 * Note: company-wide settings (address, phone, socials) are NOT modeled
 * as an ACF Options Page — that feature is ACF PRO-only (the free plugin
 * only ships a locked preview of it, calling acf_add_options_page() on
 * free fatals). Those fields live in inc/settings-page.php instead, a
 * plain Settings API page under Einstellungen → Firmenangaben.
 *
 * Heads-up on autocomplete: the IDE stubs in composer.json are ACF *Pro*
 * (no free-only package exists), but this site runs ACF free. So the editor
 * will happily suggest Pro-only APIs — have_rows(), the_row(), repeater
 * fields — that do not work here. On free ACF, get_field() on a repeater
 * returns the row count as a *string*, which is truthy and silently renders
 * an empty list. Use a textarea + letsdoo_lines() instead (see the Paket
 * "Merkmale" field for the established pattern).
 */

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
	return;
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
				'key'   => 'field_ld_leistung_angebote_tab',
				'label' => 'Angebote-Seite',
				'name'  => '',
				'type'  => 'tab',
				'instructions' => 'Diese Felder werden zusätzlich für die Leistungskarten auf der Angebote-Seite verwendet.',
			),
			array(
				'key'     => 'field_ld_leistung_icon',
				'label'   => 'Icon',
				'name'    => 'icon',
				'type'    => 'select',
				'choices' => array(
					'architecture' => 'Implementierung',
					'settings'     => 'Customizing',
					'support'      => 'Support',
					'training'     => 'Schulung',
					'check'        => 'Haken',
				),
				'default_value' => 'architecture',
			),
			array(
				'key'   => 'field_ld_leistung_bild',
				'label' => 'Bild',
				'name'  => 'bild',
				'type'  => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'instructions'  => 'Mit Bild wird die Karte gross (Bild neben Text) dargestellt, ohne Bild klein.',
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
				'key'     => 'field_ld_vorgehen_schritt_icon',
				'label'   => 'Icon',
				'name'    => 'icon',
				'type'    => 'select',
				'choices' => array(
					'search'       => 'Analyse',
					'architecture' => 'Implementierung',
					'settings'     => 'Customizing',
					'training'     => 'Schulung',
					'support'      => 'Support',
					'rocket'       => 'Go-live',
					'check'        => 'Haken',
				),
				'default_value' => 'check',
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
			/*
			 * Three flat groups rather than a repeater — repeaters are Pro-only
			 * and never render in this admin (see the file header). The row is
			 * a fixed three across in the layout, so there is nothing to add or
			 * reorder and the textarea + letsdoo_lines() trick the Paket
			 * "Merkmale" field uses would only get in the way of the icon
			 * select. Leaving a Titel empty drops that pillar from the row.
			 */
			array(
				'key'   => 'field_ld_home_warum_letsdoo_punkte_tab',
				'label' => 'Punkte',
				'name'  => '',
				'type'  => 'message',
				'message' => 'Die drei Punkte, die nebeneinander unter dem Untertitel stehen.',
			),

			array(
				'key'     => 'field_ld_home_warum_letsdoo_punkt_1_icon',
				'label'   => 'Punkt 1 – Icon',
				'name'    => 'warum_letsdoo_punkt_1_icon',
				'type'    => 'select',
				'choices' => array(
					'support'  => 'Persönlich',
					'search'   => 'Transparent',
					'rocket'   => 'Lösungsorientiert',
					'training' => 'Schulung',
					'settings' => 'Customizing',
					'check'    => 'Haken',
				),
				'default_value' => 'support',
				'wrapper' => array( 'width' => '25' ),
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
				'key'     => 'field_ld_home_warum_letsdoo_punkt_2_icon',
				'label'   => 'Punkt 2 – Icon',
				'name'    => 'warum_letsdoo_punkt_2_icon',
				'type'    => 'select',
				'choices' => array(
					'support'  => 'Persönlich',
					'search'   => 'Transparent',
					'rocket'   => 'Lösungsorientiert',
					'training' => 'Schulung',
					'settings' => 'Customizing',
					'check'    => 'Haken',
				),
				'default_value' => 'search',
				'wrapper' => array( 'width' => '25' ),
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
				'key'     => 'field_ld_home_warum_letsdoo_punkt_3_icon',
				'label'   => 'Punkt 3 – Icon',
				'name'    => 'warum_letsdoo_punkt_3_icon',
				'type'    => 'select',
				'choices' => array(
					'support'  => 'Persönlich',
					'search'   => 'Transparent',
					'rocket'   => 'Lösungsorientiert',
					'training' => 'Schulung',
					'settings' => 'Customizing',
					'check'    => 'Haken',
				),
				'default_value' => 'rocket',
				'wrapper' => array( 'width' => '25' ),
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
						'key'   => 'field_ld_about_zahl_wert',
						'label' => 'Zahl',
						'name'  => 'zahl_wert',
						'type'  => 'text',
						'wrapper' => array( 'width' => '30' ),
					),
					array(
						'key'   => 'field_ld_about_zahl_label',
						'label' => 'Beschriftung',
						'name'  => 'zahl_label',
						'type'  => 'text',
						'wrapper' => array( 'width' => '70' ),
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
			array(
				'key'   => 'field_ld_angebote_leistungen_heading',
				'label' => 'Titel',
				'name'  => 'leistungen_heading',
				'type'  => 'text',
				'default_value' => 'Unsere Leistungen',
			),
			array(
				'key'          => 'field_ld_angebote_leistungen',
				'label'        => 'Leistungen',
				'name'         => 'leistungen',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Leistung hinzufügen',
				'instructions' => 'Karten werden abwechselnd gross (mit Bild) und klein dargestellt.',
				'sub_fields'   => array(
					array(
						'key'     => 'field_ld_angebote_leistung_icon',
						'label'   => 'Icon',
						'name'    => 'icon',
						'type'    => 'select',
						'choices' => array(
							'architecture' => 'Implementierung',
							'settings'     => 'Customizing',
							'support'      => 'Support',
							'training'     => 'Schulung',
							'check'        => 'Haken',
						),
						'default_value' => 'architecture',
						'wrapper' => array( 'width' => '25' ),
					),
					array(
						'key'   => 'field_ld_angebote_leistung_titel',
						'label' => 'Titel',
						'name'  => 'titel',
						'type'  => 'text',
						'wrapper' => array( 'width' => '75' ),
					),
					array(
						'key'   => 'field_ld_angebote_leistung_text',
						'label' => 'Text',
						'name'  => 'text',
						'type'  => 'textarea',
						'rows'  => 3,
					),
					array(
						'key'   => 'field_ld_angebote_leistung_bild',
						'label' => 'Bild',
						'name'  => 'bild',
						'type'  => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'instructions'  => 'Wird nur auf den grossen Karten angezeigt.',
					),
					array(
						'key'          => 'field_ld_angebote_leistung_merkmale',
						'label'        => 'Merkmale',
						'name'         => 'merkmale',
						'type'         => 'repeater',
						'layout'       => 'table',
						'button_label' => 'Merkmal hinzufügen',
						'sub_fields'   => array(
							array(
								'key'   => 'field_ld_angebote_leistung_merkmal_text',
								'label' => 'Merkmal',
								'name'  => 'text',
								'type'  => 'text',
							),
						),
					),
				),
			),

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
				'instructions' => 'Der <title> für Google. Leer lassen für „Odoo <Ort> – <Seitenname>“. Ziel: unter 60 Zeichen.',
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
				'instructions' => 'Leer lassen für „Odoo <Ort>“.',
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

			array(
				'key'   => 'field_ld_standort_lokal_tab',
				'label' => 'Lokaler Inhalt',
				'name'  => '',
				'type'  => 'tab',
			),
			array(
				'key'   => 'field_ld_standort_lokal_heading',
				'label' => 'Titel',
				'name'  => 'lokal_heading',
				'type'  => 'text',
				'instructions' => 'Leer lassen für „Odoo-Partner in <Ort>“.',
			),
			array(
				'key'   => 'field_ld_standort_lokal_text',
				'label' => 'Text',
				'name'  => 'lokal_text',
				'type'  => 'textarea',
				'rows'  => 8,
				'instructions' => 'Der wichtigste Teil der Seite. Bitte pro Standort neu schreiben – Branchen in der Region, Anfahrt, konkrete Projekte. Derselbe Text mit ausgetauschtem Ortsnamen wird von Google als Doorway-Page erkannt und fliegt aus dem Index.',
			),
			array(
				'key'   => 'field_ld_standort_referenz',
				'label' => 'Referenz aus der Region',
				'name'  => 'referenz',
				'type'  => 'post_object',
				'post_type' => array( 'referenz' ),
				'return_format' => 'id',
				'allow_null' => 1,
				'ui' => 1,
				'instructions' => 'Optional, aber der stärkste Beleg dafür, dass die Seite echt ist.',
			),

			array(
				'key'   => 'field_ld_standort_faq_tab',
				'label' => 'FAQ',
				'name'  => '',
				'type'  => 'tab',
			),
			array(
				'key'   => 'field_ld_standort_faq',
				'label' => 'Fragen',
				'name'  => 'faq',
				'type'  => 'textarea',
				'rows'  => 8,
				'instructions' => 'Eine Frage pro Zeile, Frage und Antwort mit „|“ getrennt. Beispiel: Betreut ihr auch Firmen in Hochdorf? | Ja – Hochdorf liegt 20 Minuten von unserem Büro in Horw.',
			),
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
