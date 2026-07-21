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
			array(
				'key'   => 'field_ld_home_warum_letsdoo_text',
				'label' => 'Text',
				'name'  => 'warum_letsdoo_text',
				'type'  => 'textarea',
				'rows'  => 3,
			),
			array(
				'key'   => 'field_ld_home_warum_letsdoo_image',
				'label' => 'Bild',
				'name'  => 'warum_letsdoo_image',
				'type'  => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
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
				'key'          => 'field_ld_home_vorgehen_schritte',
				'label'        => 'Schritte',
				'name'         => 'vorgehen_schritte',
				'type'         => 'repeater',
				'layout'       => 'table',
				'button_label' => 'Schritt hinzufügen',
				'sub_fields'   => array(
					array(
						'key'   => 'field_ld_home_vorgehen_schritt_text',
						'label' => 'Schritt',
						'name'  => 'schritt_text',
						'type'  => 'text',
					),
				),
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
				'key'   => 'field_ld_contact_side_image',
				'label' => 'Bild neben Formular',
				'name'  => 'side_image',
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

} );
