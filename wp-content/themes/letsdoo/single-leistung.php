<?php
/**
 * Single Leistung — detail page for one service.
 *
 * Same shape as single-referenz.php: the Merkmale strip reuses the field the
 * cards already use (merkmale — see inc/acf-fields.php,
 * blocks/leistungen/render.php and the bento grid on
 * page-templates/template-angebote.php), and the body below is free-form,
 * authored with core blocks. The hero has its own image field (hero_bild),
 * separate from the card image (bild) so a Leistung can have a proper hero
 * photo without being forced into the wide card layout everywhere it's
 * listed — see the "Hero-Bild" instructions in inc/acf-fields.php.
 */

get_header();

while ( have_posts() ) :
	the_post();

	$lead      = get_field( 'beschreibung' );
	$hero_bild = get_field( 'hero_bild' ) ?: get_field( 'bild' );
	$merkmale  = letsdoo_lines( get_field( 'merkmale' ) );
	?>

	<main id="main" class="site-main">

		<section class="hero hero--sub hero--leistung" style="background-image:url('<?php echo esc_url( letsdoo_image_url( $hero_bild, 'placeholder-photo.svg', 'full' ) ); ?>');"></section>

		<?php letsdoo_page_title( get_the_title(), __( 'Leistung', 'letsdoo' ), $lead ); ?>

		<?php if ( $merkmale ) : ?>
			<section class="section section--leistung-merkmale">
				<div class="section__content">
					<ul class="leistung-merkmale-liste">
						<?php foreach ( $merkmale as $merkmal ) : ?>
							<li><?php echo letsdoo_icon( 'check' ); ?> <?php echo esc_html( $merkmal ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</section>
		<?php endif; ?>

		<section class="section section--leistung-inhalt">
			<div class="section__content">
				<div class="entry-content">
					<?php
					// Checked before the_content(), which advances the page globals.
					$has_content = '' !== trim( get_the_content() );

					the_content();

					if ( ! $has_content ) {
						printf(
							'<p class="admin-hint">%s</p>',
							esc_html__( 'Noch kein Inhalt erfasst – diese Leistung im Editor mit Text und Bildern ergänzen.', 'letsdoo' )
						);
					}
					?>
				</div>

				<?php
				$prev_leistung = get_previous_post();
				$next_leistung = get_next_post();
				?>
				<?php if ( $prev_leistung || $next_leistung ) : ?>
					<nav class="entry-nav" aria-label="<?php esc_attr_e( 'Weitere Leistungen', 'letsdoo' ); ?>">
						<?php if ( $prev_leistung ) : ?>
							<a class="entry-nav__link" href="<?php echo esc_url( get_permalink( $prev_leistung ) ); ?>">
								<span class="entry-nav__label"><?php esc_html_e( 'Vorherige Leistung', 'letsdoo' ); ?></span>
								<span class="entry-nav__titel"><?php echo esc_html( get_the_title( $prev_leistung ) ); ?></span>
							</a>
						<?php endif; ?>
						<?php if ( $next_leistung ) : ?>
							<a class="entry-nav__link entry-nav__link--next" href="<?php echo esc_url( get_permalink( $next_leistung ) ); ?>">
								<span class="entry-nav__label"><?php esc_html_e( 'Nächste Leistung', 'letsdoo' ); ?></span>
								<span class="entry-nav__titel"><?php echo esc_html( get_the_title( $next_leistung ) ); ?></span>
							</a>
						<?php endif; ?>
					</nav>
				<?php endif; ?>
			</div>
		</section>

		<section class="section section--cta-banner" id="kontakt">
			<div class="cta-banner">
				<h2><?php esc_html_e( 'Bereit für den nächsten Schritt?', 'letsdoo' ); ?></h2>
				<p><?php esc_html_e( 'Lassen Sie uns gemeinsam herausfinden, wie Odoo Ihr Unternehmen unterstützen kann.', 'letsdoo' ); ?></p>
				<div class="cta-banner__actions">
					<?php letsdoo_button( __( 'Kontakt aufnehmen', 'letsdoo' ), home_url( '/kontakt/' ) ); ?>
					<?php letsdoo_button( __( 'Alle Leistungen', 'letsdoo' ), letsdoo_page_url_by_template( 'page-templates/template-angebote.php', '/angebote/' ) . '#leistungen' ); ?>
				</div>
			</div>
		</section>

	</main>

	<?php
endwhile;

get_footer();
