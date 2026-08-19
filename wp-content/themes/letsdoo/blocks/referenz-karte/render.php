<?php
/**
 * Referenzen block.
 *
 * On a Standort page this is the strongest evidence that the page describes
 * real work in the region rather than a template with the town name swapped in,
 * which is why the Standort starter template includes it.
 *
 * Renders nothing when no Referenz is chosen — except in the editor, where a
 * silently empty block looks like a bug rather than an unfinished one. One
 * selected Referenz renders as a single full-width card, exactly as this
 * block always has; more than one wraps them in the same .referenzen-grid the
 * About page uses, rather than stretching the single-card layout out to a
 * lopsided one-item row.
 *
 * @var array $block
 * @var bool  $is_preview
 */

$referenz_ids = array_filter( (array) get_field( 'referenz' ) );
$heading      = get_field( 'heading' );
$blend        = (bool) get_field( 'bg_blend' );

if ( ! $referenz_ids ) {
	if ( $is_preview ) {
		echo '<p class="admin-hint">' . esc_html__( 'Referenzen: bitte rechts mindestens eine Referenz auswählen.', 'letsdoo' ) . '</p>';
	}
	return;
}

if ( ! $heading ) {
	$ort     = get_field( 'ort', get_the_ID() );
	$heading = $ort
		/* translators: %s: town name. */
		? sprintf( __( 'Aus der Region %s', 'letsdoo' ), $ort )
		: __( 'Referenz', 'letsdoo' );
}

$is_grid = count( $referenz_ids ) > 1;

letsdoo_block_section_open( $block, 'section--standort-referenz', $blend );
?>
	<div class="section__content">
		<h2><?php echo esc_html( $heading ); ?></h2>

		<?php if ( $is_grid ) : ?><div class="referenzen-grid"><?php endif; ?>
		<?php foreach ( $referenz_ids as $referenz_id ) : ?>
			<div class="referenz-card">
				<div class="referenz-card__logo">
					<img src="<?php echo esc_url( letsdoo_image_url( get_post_thumbnail_id( $referenz_id ), 'placeholder-logo.svg' ) ); ?>" alt="<?php echo esc_attr( get_the_title( $referenz_id ) ); ?>">
				</div>
				<div class="referenz-card__body">
					<h3><?php echo esc_html( get_the_title( $referenz_id ) ); ?></h3>
					<p><?php echo esc_html( get_field( 'beschreibung', $referenz_id ) ); ?></p>
					<a class="btn btn--sm referenz-card__link" href="<?php echo esc_url( get_permalink( $referenz_id ) ); ?>">
						<?php esc_html_e( 'Referenz ansehen', 'letsdoo' ); ?>
						<span class="screen-reader-text">: <?php echo esc_html( get_the_title( $referenz_id ) ); ?></span>
					</a>
				</div>
			</div>
		<?php endforeach; ?>
		<?php if ( $is_grid ) : ?></div><?php endif; ?>
	</div>
</section>
