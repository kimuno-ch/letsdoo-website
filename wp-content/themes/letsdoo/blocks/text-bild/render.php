<?php
/**
 * Text & Bild block — the "Warum Odoo?" section from the Startseite, made
 * reusable: a text column (heading, lead line, copy, button) with an image
 * beside it, on either side.
 *
 * The markup comes from template-parts/section-text-media.php, which the
 * Startseite template renders too, so both stay on one set of classes and one
 * stylesheet (06-warum-odoo.css).
 *
 * Everything is optional. Without an image the part falls back to a single
 * capped text column; without a button no link is printed. An empty block still
 * renders nothing broken — but it also has nothing to say, so the editor gets a
 * hint instead of an empty band.
 *
 * @var array $block
 * @var bool  $is_preview
 */

$heading   = get_field( 'heading' );
$text      = get_field( 'text' );
$bild      = get_field( 'bild' );
$button    = get_field( 'button' );
$blend     = (bool) get_field( 'bg_blend' );

if ( ! $heading && ! $text && ! $bild ) {
	if ( $is_preview ) {
		echo '<p class="admin-hint">' . esc_html__( 'Text & Bild: noch kein Inhalt erfasst.', 'letsdoo' ) . '</p>';
	}
	return;
}

/* Same Link-field handling as the CTA-Band block: one field carries label and
   URL, and an empty one prints no button at all — this section is often just
   copy next to a picture. */
$button_label = ! empty( $button['title'] ) ? $button['title'] : '';
$button_link  = ! empty( $button['url'] ) ? $button['url'] : '';

letsdoo_block_section_open( $block, 'section--text-bild', $blend );

get_template_part(
	'template-parts/section-text-media',
	null,
	array(
		'heading'        => $heading,
		'subheading'     => get_field( 'subheading' ),
		/* WYSIWYG: filtered here rather than in the part, so the part stays
		   agnostic about where its markup came from. */
		'html'           => $text ? apply_filters( 'the_content', $text ) : '',
		'button_label'   => $button_label,
		'button_link'    => $button_link,
		/* No placeholder fallback: an unset image means "text only" here,
		   unlike the Startseite section which always shows the Odoo diagram. */
		'image_url'      => $bild ? letsdoo_image_url( $bild, '', 'large' ) : '',
		'image_alt'      => letsdoo_image_alt( $bild ),
		'image_position' => get_field( 'bild_position' ) ?: 'rechts',
	)
);
?>
</section>
