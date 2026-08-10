<?php
/**
 * Zahlen block — the kennzahlen grid from the Über-uns page, available inside
 * blog posts and Standort pages.
 *
 * The Über-uns page keeps its own zahlen_liste repeater and its own hardcoded
 * fallbacks; this block is a separate instance with its own numbers rather than
 * a second view of those, because a post citing figures wants its own.
 *
 * @var array $block
 * @var bool  $is_preview
 */

$heading = get_field( 'heading' );
$text    = get_field( 'text' );
$zahlen  = get_field( 'zahlen' );
$blend   = (bool) get_field( 'bg_blend' );

if ( ! $zahlen ) {
	if ( $is_preview ) {
		echo '<p class="admin-hint">' . esc_html__( 'Zahlen: noch keine Werte erfasst.', 'letsdoo' ) . '</p>';
	}
	return;
}

letsdoo_block_section_open( $block, 'section--zahlen', $blend );
?>
	<div class="section__content">
		<?php if ( $heading ) : ?>
			<h2><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<?php if ( $text ) : ?>
			<p class="zahlen__intro"><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>

		<div class="zahlen-grid">
			<?php foreach ( $zahlen as $zahl ) : ?>
				<div class="zahl-card">
					<span class="zahl-card__wert"><?php echo esc_html( $zahl['wert'] ); ?></span>
					<span class="zahl-card__label"><?php echo esc_html( $zahl['label'] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
