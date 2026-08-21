<?php
/**
 * Single Referenz — case-study page for one reference.
 *
 * The body is authored with core blocks (images, videos, text), so the
 * markup here only frames it: hero, a strip of key facts, the block
 * content, and a closing CTA. Reuses the hero / glass-panel / cta-banner
 * patterns from the page templates rather than introducing a new look.
 */

get_header();

while ( have_posts() ) :
	the_post();

	$hero_image = get_field( 'hero_image' );
	$lead       = get_field( 'beschreibung' );
	$website    = get_field( 'website' );
	$module     = letsdoo_lines( get_field( 'module' ) );
	$referenzen_url = letsdoo_page_url_by_template( 'page-templates/template-about.php', '/ueber-uns/' );

	// Only the facts the editor actually filled in get a row.
	$facts = array_filter( array(
		__( 'Branche', 'letsdoo' ) => get_field( 'branche' ),
		__( 'Jahr', 'letsdoo' )    => get_field( 'jahr' ),
	) );
	?>

	<main id="main" class="site-main">

		<section class="hero hero--sub hero--referenz" style="background-image:url('<?php echo esc_url( letsdoo_image_url( $hero_image, 'placeholder-photo.svg', 'full' ) ); ?>');"></section>

		<?php letsdoo_page_title( get_the_title(), __( 'Referenz', 'letsdoo' ), $lead ); ?>

		<?php if ( $facts || $website || $module ) : ?>
			<section class="section section--referenz-fakten">
				<div class="section__content">
					<dl class="referenz-fakten">
						<?php foreach ( $facts as $label => $value ) : ?>
							<div class="referenz-fakt">
								<dt><?php echo esc_html( $label ); ?></dt>
								<dd><?php echo esc_html( $value ); ?></dd>
							</div>
						<?php endforeach; ?>

						<?php if ( $module ) : ?>
							<div class="referenz-fakt referenz-fakt--module">
								<dt><?php esc_html_e( 'Odoo-Module', 'letsdoo' ); ?></dt>
								<dd>
									<ul class="referenz-module">
										<?php foreach ( $module as $modul ) : ?>
											<li><?php echo esc_html( $modul ); ?></li>
										<?php endforeach; ?>
									</ul>
								</dd>
							</div>
						<?php endif; ?>

						<?php if ( $website ) : ?>
							<div class="referenz-fakt">
								<dt><?php esc_html_e( 'Website', 'letsdoo' ); ?></dt>
								<dd>
									<a href="<?php echo esc_url( $website ); ?>" target="_blank" rel="noopener noreferrer">
										<?php echo esc_html( preg_replace( '#^https?://(www\.)?#i', '', untrailingslashit( $website ) ) ); ?>
									</a>
								</dd>
							</div>
						<?php endif; ?>
					</dl>
				</div>
			</section>
		<?php endif; ?>

		<section class="section section--referenz-inhalt">
			<div class="section__content">
				<div class="entry-content">
					<?php
					// Checked before the_content(), which advances the page globals.
					$has_content = '' !== trim( get_the_content() );

					the_content();

					if ( ! $has_content ) {
						printf(
							'<p class="admin-hint">%s</p>',
							esc_html__( 'Noch kein Inhalt erfasst – diese Referenz im Editor mit Text, Bildern und Videos ergänzen.', 'letsdoo' )
						);
					}
					?>
				</div>

				<?php
				$prev_referenz = get_previous_post();
				$next_referenz = get_next_post();
				?>
				<?php if ( $prev_referenz || $next_referenz ) : ?>
					<nav class="entry-nav" aria-label="<?php esc_attr_e( 'Weitere Referenzen', 'letsdoo' ); ?>">
						<?php if ( $prev_referenz ) : ?>
							<a class="entry-nav__link" href="<?php echo esc_url( get_permalink( $prev_referenz ) ); ?>">
								<span class="entry-nav__label"><?php esc_html_e( 'Vorherige Referenz', 'letsdoo' ); ?></span>
								<span class="entry-nav__titel"><?php echo esc_html( get_the_title( $prev_referenz ) ); ?></span>
							</a>
						<?php endif; ?>
						<?php if ( $next_referenz ) : ?>
							<a class="entry-nav__link entry-nav__link--next" href="<?php echo esc_url( get_permalink( $next_referenz ) ); ?>">
								<span class="entry-nav__label"><?php esc_html_e( 'Nächste Referenz', 'letsdoo' ); ?></span>
								<span class="entry-nav__titel"><?php echo esc_html( get_the_title( $next_referenz ) ); ?></span>
							</a>
						<?php endif; ?>
					</nav>
				<?php endif; ?>
			</div>
		</section>

		<section class="section section--cta-banner" id="kontakt">
			<div class="cta-banner">
				<h2><?php esc_html_e( 'Ein ähnliches Projekt im Kopf?', 'letsdoo' ); ?></h2>
				<p><?php esc_html_e( 'Erzählen Sie uns von Ihren Prozessen – wir zeigen Ihnen, wie Odoo sie abbilden kann.', 'letsdoo' ); ?></p>
				<div class="cta-banner__actions">
					<?php letsdoo_button( __( 'Kontakt aufnehmen', 'letsdoo' ), home_url( '/kontakt/' ) ); ?>
					<?php letsdoo_button( __( 'Alle Referenzen', 'letsdoo' ), $referenzen_url . '#referenzen' ); ?>
				</div>
			</div>
		</section>

	</main>

	<?php
endwhile;

get_footer();
