<?php
/**
 * CTA-Band block, in the two treatments the design already had:
 *
 *  - "karte"  → .section--cta-banner, the rounded gradient card used on the
 *               Angebote page, the blog index and blog posts.
 *  - "voll"   → .section--standort-cta, the full-bleed blend band with white
 *               type that closed every Standort page.
 *
 * Same content either way, so they are one block with a switch rather than two
 * blocks that would drift apart. Markup matches what the templates emitted, so
 * 19-angebote-cta.css and 24-standort.css apply unchanged.
 *
 * Empty fields fall back to the copy those templates hardcoded, so an inserted
 * block reads as finished rather than as an empty box.
 *
 * @var array $block
 * @var bool  $is_preview
 */

$variante = get_field( 'variante' ) ?: 'karte';
$heading  = get_field( 'heading' );
$text     = get_field( 'text' );
$button   = get_field( 'button' );
$button2  = get_field( 'button2' );

if ( ! $heading ) {
	$ort = get_field( 'ort', get_the_ID() );

	$heading = $ort
		/* translators: %s: town name. */
		? sprintf( __( 'Odoo-Projekt in %s geplant?', 'letsdoo' ), $ort )
		: __( 'Bereit für den nächsten Schritt?', 'letsdoo' );
}

if ( ! $text ) {
	$text = __( 'Lassen Sie uns gemeinsam herausfinden, wie Odoo Ihr Unternehmen unterstützen kann.', 'letsdoo' );
}

$label = ! empty( $button['title'] ) ? $button['title'] : __( 'Kontakt aufnehmen', 'letsdoo' );

/* A Link field carries its own label, so there is no separate "Button Text"
   field to keep in step with the URL. The fallback finds the Kontakt page by
   its template rather than by path, so the link survives a rename. */
$url = ! empty( $button['url'] )
	? $button['url']
	: letsdoo_page_url_by_template( 'page-templates/template-contact.php', '/kontakt/' );

$has_second = ! empty( $button2['title'] ) && ! empty( $button2['url'] );

if ( 'voll' === $variante ) :
	letsdoo_block_section_open( $block, 'section--standort-cta', true );
	?>
	<div class="section__content">
		<h2><?php echo esc_html( $heading ); ?></h2>
		<?php if ( $text ) : ?>
			<p class="section__subheading"><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>
		<?php
		letsdoo_button( $label, $url );
		if ( $has_second ) {
			letsdoo_button( $button2['title'], $button2['url'], 'btn--outline' );
		}
		?>
	</div>
	</section>
	<?php
else :
	letsdoo_block_section_open( $block, 'section--cta-banner' );
	?>
	<div class="cta-banner">
		<h2><?php echo esc_html( $heading ); ?></h2>
		<?php if ( $text ) : ?>
			<p><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>
		<div class="cta-banner__actions">
			<?php
			letsdoo_button( $label, $url );
			if ( $has_second ) {
				letsdoo_button( $button2['title'], $button2['url'] );
			}
			?>
		</div>
	</div>
	</section>
	<?php
endif;
