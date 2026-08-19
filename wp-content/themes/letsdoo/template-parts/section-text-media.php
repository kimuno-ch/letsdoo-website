<?php
/**
 * Text column with an optional image beside it — the "Warum Odoo?" shape.
 *
 * Shared by the Startseite template (page-templates/template-home.php), where
 * the section is fixed, and the Text & Bild block (blocks/text-bild/), where an
 * editor can place the same shape on any page that takes the theme's blocks.
 * One part rather than two copies: the split layout, the fallback order and the
 * image markup are exactly the things that drift apart when duplicated.
 *
 * Only the inner markup is emitted. The caller opens and closes the <section>
 * itself, because a template controls its own classes and id while a block gets
 * them from letsdoo_block_section_open() (see inc/blocks.php).
 *
 * @param array $args {
 *     @type string $heading        H2. Omitted when empty.
 *     @type string $subheading     Bold lead line under the heading.
 *     @type string $text           Plain text; printed escaped as one paragraph.
 *     @type string $html           Pre-filtered markup (WYSIWYG). Wins over $text.
 *     @type string $button_label   Empty label prints no button (letsdoo_button).
 *     @type string $button_link
 *     @type string $image_url      Empty means text only — no empty column.
 *     @type string $image_alt      Empty renders the image decorative (alt="").
 *     @type string $image_html     Pre-built markup (e.g. an inline <svg>) —
 *                                  wins over $image_url, same as $html wins
 *                                  over $text on the text side. Trusted, not
 *                                  escaped: the caller is responsible for its
 *                                  own escaping, same contract as $html.
 *     @type string $image_position 'links' or 'rechts' (default).
 * }
 */

$ld_image_url  = $args['image_url'] ?? '';
$ld_image_html = $args['image_html'] ?? '';
$ld_has_media  = '' !== $ld_image_url || '' !== $ld_image_html;

/*
 * Without an image there is no grid to build: the text falls back to the
 * single capped column the section had before, so a Text & Bild block with the
 * image left empty reads like a plain Textabschnitt rather than a half-width
 * column of prose with a hole beside it.
 */
$ld_classes = $ld_has_media
	? array( 'section__content', 'section-split', 'section-split--media-' . ( 'links' === ( $args['image_position'] ?? '' ) ? 'links' : 'rechts' ) )
	: array( 'section__intro', 'section-split', 'section-split--einspaltig' );
?>
<div class="<?php echo esc_attr( implode( ' ', $ld_classes ) ); ?>">

	<div class="section-split__text">
		<?php if ( ! empty( $args['heading'] ) ) : ?>
			<h2><?php echo esc_html( $args['heading'] ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $args['subheading'] ) ) : ?>
			<p class="section__subheading"><?php echo esc_html( $args['subheading'] ); ?></p>
		<?php endif; ?>

		<?php
		if ( ! empty( $args['html'] ) ) {
			/* Already run through the_content by the caller, same as the
			   Textabschnitt block — wp_kses on save is what makes it safe. */
			echo $args['html'];
		} elseif ( ! empty( $args['text'] ) ) {
			echo '<p>' . esc_html( $args['text'] ) . '</p>';
		}
		?>

		<?php letsdoo_button( $args['button_label'] ?? '', $args['button_link'] ?? '' ); ?>
	</div>

	<?php if ( $ld_has_media ) : ?>
		<?php
		/*
		 * The image sits after the text in the DOM whichever side it is painted
		 * on — 06-warum-odoo.css flips it with `order`, so screen readers and
		 * the tab order always get the heading before the picture.
		 */
		$ld_image_alt = $args['image_alt'] ?? '';
		?>
		<div class="section-split__media">
			<?php if ( '' !== $ld_image_html ) : ?>
				<?php echo $ld_image_html; ?>
			<?php else : ?>
				<img
					src="<?php echo esc_url( $ld_image_url ); ?>"
					alt="<?php echo esc_attr( $ld_image_alt ); ?>"
					<?php echo '' === $ld_image_alt ? 'aria-hidden="true" ' : ''; ?>
					loading="lazy"
					decoding="async"
				>
			<?php endif; ?>
		</div>
	<?php endif; ?>

</div>
