<?php
/**
 * Leistungen block — the small-card grid.
 *
 * Cards come from the Leistung post type, so they stay in step with the home
 * page automatically; the block only carries the heading and the background
 * treatment. Small cards only, as single-standort.php did: the wide variant
 * with images belongs to the home page, where the section is the main event
 * rather than supporting material.
 *
 * On a Standort the heading falls back to "Unsere Leistungen für <Ort>".
 *
 * @var array $block
 * @var bool  $is_preview
 */

$heading = get_field( 'heading' );
$blend   = (bool) get_field( 'bg_blend' );

if ( ! $heading ) {
	$ort     = get_field( 'ort', get_the_ID() );
	$heading = $ort
		/* translators: %s: town name. */
		? sprintf( __( 'Unsere Leistungen für %s', 'letsdoo' ), $ort )
		: __( 'Unsere Leistungen', 'letsdoo' );
}

$leistungen = letsdoo_get_leistungen();

letsdoo_block_section_open( $block, 'section--leistungen', $blend );
?>
	<div class="section__content">
		<h2><?php echo esc_html( $heading ); ?></h2>

		<?php if ( $leistungen ) : ?>
			<div class="leistungen-grid">
				<?php foreach ( $leistungen as $leistung ) : ?>
					<?php
					$leistung_id = $leistung->ID;
					$icon        = get_field( 'icon', $leistung_id ) ?: letsdoo_icon( 'check' );
					$text        = get_field( 'beschreibung', $leistung_id );
					?>
					<a class="leistung-card" href="<?php echo esc_url( get_permalink( $leistung_id ) ); ?>">
						<div class="leistung-card__body">
							<div class="icon-tile"><?php echo $icon; ?></div>
							<h3><?php echo esc_html( get_the_title( $leistung_id ) ); ?></h3>
							<?php if ( $text ) : ?>
								<p><?php echo esc_html( $text ); ?></p>
							<?php endif; ?>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<p class="admin-hint"><?php esc_html_e( 'Noch keine Leistungen erfasst – unter „Leistungen“ im Menü hinzufügen.', 'letsdoo' ); ?></p>
		<?php endif; ?>
	</div>
</section>
