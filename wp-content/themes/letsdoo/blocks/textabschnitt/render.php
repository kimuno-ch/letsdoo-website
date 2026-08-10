<?php
/**
 * Textabschnitt block.
 *
 * Replaces the Standort "Lokaler Inhalt" section. The text is a WYSIWYG field
 * rather than the textarea that single-standort.php used to split on newlines
 * with letsdoo_lines() — this copy is the whole point of a Standort page, and
 * it could not previously carry a link or any emphasis.
 *
 * On a Standort the heading falls back to "Odoo-Partner in <Ort>", matching
 * what the template did, so the section keeps a sensible H2 if it is left
 * blank.
 *
 * @var array $block
 */

$heading = get_field( 'heading' );
$text    = get_field( 'text' );
$blend   = (bool) get_field( 'bg_blend' );

if ( ! $heading ) {
	$ort = get_field( 'ort', get_the_ID() );
	if ( $ort ) {
		/* translators: %s: town name. */
		$heading = sprintf( __( 'Odoo-Partner in %s', 'letsdoo' ), $ort );
	}
}

letsdoo_block_section_open( $block, 'section--standort-lokal', $blend );
?>
	<div class="section__content">
		<?php if ( $heading ) : ?>
			<h2><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<div class="standort-lokal__text">
			<?php
			/* WYSIWYG output is already sanitised markup from wp_kses on save;
			   the_content filters run it through wpautop so paragraphs come out
			   the way .standort-lokal__text p expects. */
			echo apply_filters( 'the_content', $text );
			?>
		</div>
	</div>
</section>
