<?php
/**
 * Logo carousel block — an endless horizontal band of Referenz logos plus
 * the Odoo Ready Partner badge, scrolling sideways on its own. Pure CSS (one
 * @keyframes loop over a track rendered twice — see 29-logo-carousel.css for
 * why it's doubled and how the reduced-motion fallback works), so there's
 * nothing here to wire up beyond the list of images.
 *
 * Same "pick specific or leave empty for all" Referenzen selection every
 * other curated grid on the site uses (letsdoo_post_selector_field() /
 * letsdoo_get_referenzen()) — empty means every Referenz, in menu_order.
 *
 * @var array $block
 * @var bool  $is_preview
 */

$referenz_ids   = get_field( 'referenzen' );
$referenzen     = letsdoo_get_referenzen( $referenz_ids );
$show_badge_raw = get_field( 'show_odoo_badge' );
$heading        = get_field( 'heading' );
$blend          = (bool) get_field( 'bg_blend' );

/*
 * get_field() returns null (not false) when the block was inserted but its
 * sidebar fields were never opened — no "data" ever lands in the block
 * comment, ACF has nothing to read, and there's no true_false value to fall
 * back to. null is exactly the "never touched" case, and the field's
 * default is "on", so null has to mean shown; only a real false (someone
 * actually unchecked it) hides the badge.
 */
$show_badge = null === $show_badge_raw ? true : (bool) $show_badge_raw;

if ( ! $referenzen && ! $show_badge ) {
	if ( $is_preview ) {
		echo '<p class="admin-hint">' . esc_html__( 'Logo-Karussell: keine Referenzen gewählt und das Odoo-Badge ist ausgeblendet — es gibt nichts zu zeigen.', 'letsdoo' ) . '</p>';
	}
	return;
}

$logos = array();
foreach ( $referenzen as $referenz ) {
	$logos[] = array(
		'src'  => letsdoo_image_url( get_post_thumbnail_id( $referenz->ID ), 'placeholder-logo.svg' ),
		'alt'  => get_the_title( $referenz ),
		// The Odoo badge isn't a Referenz and has nowhere to link to — this
		// is what the markup below uses to decide whether an item is a
		// plain logo or a link with a hover CTA.
		'link' => get_permalink( $referenz->ID ),
	);
}
if ( $show_badge ) {
	$logos[] = array(
		'src'  => get_theme_file_uri( '/assets/images/odoo-ready-partners.svg' ),
		'alt'  => 'Odoo Ready Partner',
		'link' => null,
	);
}

letsdoo_block_section_open( $block, 'section--logo-carousel', $blend );
?>
	<div class="section__content">
		<?php if ( $heading ) : ?>
			<h2><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<div class="logo-carousel">
			<div class="logo-carousel__track">
				<?php
				/*
				 * The track is the logo list twice, back to back. The
				 * animation translates it by exactly one copy's width and
				 * loops — since the two copies are identical, the loop
				 * point is invisible instead of snapping. The second copy is
				 * decorative (same images again, purely for the seam), so
				 * it's hidden from assistive tech (and out of tab order)
				 * rather than announced/reachable twice.
				 *
				 * A Referenz logo is a link to that Referenz — hovering it
				 * pauses the scroll (the container-level :hover in
				 * 29-logo-carousel.css covers any item) and reveals a
				 * "Referenz ansehen" button over the logo, same wording as
				 * the card link elsewhere on the site. The Odoo badge has
				 * nowhere to link to, so it stays a plain, non-interactive
				 * logo.
				 */
				for ( $repeat = 0; $repeat < 2; $repeat++ ) :
					$decorative = 0 !== $repeat;
					foreach ( $logos as $logo ) :
						$tag = $logo['link'] ? 'a' : 'div';
						?>
						<<?php echo $tag; ?>
							class="logo-carousel__item"
							<?php if ( $logo['link'] ) : ?>
								href="<?php echo esc_url( $logo['link'] ); ?>"
								<?php echo $decorative ? 'tabindex="-1"' : ''; ?>
							<?php endif; ?>
							<?php echo $decorative ? 'aria-hidden="true"' : ''; ?>
						>
							<img src="<?php echo esc_url( $logo['src'] ); ?>" alt="<?php echo $decorative ? '' : esc_attr( $logo['alt'] ); ?>" loading="lazy">
							<?php if ( $logo['link'] ) : ?>
								<span class="btn btn--sm logo-carousel__cta">
									<?php esc_html_e( 'Referenz ansehen', 'letsdoo' ); ?>
									<span class="screen-reader-text">: <?php echo esc_html( $logo['alt'] ); ?></span>
								</span>
							<?php endif; ?>
						</<?php echo $tag; ?>>
						<?php
					endforeach;
				endfor;
				?>
			</div>
		</div>
	</div>
</section>
