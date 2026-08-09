<?php
/**
 * Standort — local SEO landing page ("Odoo <Ort>").
 *
 * Built from the same sections as the rest of the site rather than a stripped
 * layout of its own: a page that looks like a landing page reads as one to
 * visitors and to Google. The Leistungen and Vorgehen blocks are the real ones
 * pulled from their post types, so they stay in step with the home page
 * automatically; everything above and below them is per-town copy.
 *
 * Nothing links here — see the post type registration in inc/cpt.php.
 */

get_header();

while ( have_posts() ) :
	the_post();

	$ort           = get_field( 'ort' );
	$kanton        = get_field( 'kanton' );
	$hero_image    = get_field( 'hero_image' );
	$hero_heading  = get_field( 'hero_heading' ) ?: sprintf( 'Odoo %s', $ort );
	$hero_text     = get_field( 'hero_text' );
	$lokal_heading = get_field( 'lokal_heading' ) ?: sprintf( 'Odoo-Partner in %s', $ort );
	$lokal_text    = get_field( 'lokal_text' );
	$referenz_id   = get_field( 'referenz' );
	$faq           = letsdoo_faq_liste( get_field( 'faq' ) );
	$leistungen    = letsdoo_get_leistungen();
	$schritte      = letsdoo_get_vorgehen_schritte();
	?>

	<main id="main" class="site-main">

		<section class="hero hero--sub hero--standort" style="background-image:url('<?php echo esc_url( letsdoo_image_url( $hero_image, 'placeholder-photo.svg', 'full' ) ); ?>');">
			<div class="hero__panel">
				<?php if ( $kanton ) : ?>
					<span class="hero__badge"><?php echo esc_html( $kanton ); ?></span>
				<?php endif; ?>
				<h1><?php echo esc_html( $hero_heading ); ?></h1>
				<?php if ( $hero_text ) : ?>
					<p><?php echo esc_html( $hero_text ); ?></p>
				<?php endif; ?>
				<?php letsdoo_button( 'Kontakt aufnehmen', letsdoo_page_url_by_template( 'page-templates/template-contact.php', '/kontakt/' ) ); ?>
			</div>
		</section>

		<?php if ( $lokal_text ) : ?>
			<section class="section section--standort-lokal">
				<div class="section__content">
					<h2><?php echo esc_html( $lokal_heading ); ?></h2>
					<div class="standort-lokal__text">
						<?php foreach ( letsdoo_lines( $lokal_text ) as $absatz ) : ?>
							<p><?php echo esc_html( $absatz ); ?></p>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( $leistungen ) : ?>
			<section class="section section--leistungen">
				<div class="section__bg-photo"></div>
				<div class="section__content">
					<h2><?php printf( esc_html__( 'Unsere Leistungen für %s', 'letsdoo' ), esc_html( $ort ) ); ?></h2>
					<div class="leistungen-grid">
						<?php
						/*
						 * Small cards only. The home page alternates wide cards
						 * carrying images and feature lists, but here the section
						 * is supporting material under the local copy — the wide
						 * variant would outweigh it.
						 */
						foreach ( $leistungen as $leistung_id ) :
							$icon = get_field( 'icon', $leistung_id );
							$text = get_field( 'text', $leistung_id );
							?>
							<div class="leistung-card">
								<div class="leistung-card__body">
									<div class="leistung-card__icon"><?php echo letsdoo_icon( $icon ); ?></div>
									<h3><?php echo esc_html( get_the_title( $leistung_id ) ); ?></h3>
									<?php if ( $text ) : ?>
										<p><?php echo esc_html( $text ); ?></p>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( $referenz_id ) : ?>
			<section class="section section--standort-referenz">
				<div class="section__content">
					<h2><?php printf( esc_html__( 'Aus der Region %s', 'letsdoo' ), esc_html( $ort ) ); ?></h2>
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
				</div>
			</section>
		<?php endif; ?>

		<?php if ( $faq ) : ?>
			<section class="section section--standort-faq">
				<div class="section__content">
					<h2><?php esc_html_e( 'Häufige Fragen', 'letsdoo' ); ?></h2>
					<dl class="standort-faq">
						<?php foreach ( $faq as $item ) : ?>
							<div class="standort-faq__item">
								<dt><?php echo esc_html( $item['frage'] ); ?></dt>
								<?php if ( $item['antwort'] ) : ?>
									<dd><?php echo esc_html( $item['antwort'] ); ?></dd>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</dl>
				</div>
			</section>
		<?php endif; ?>

		<section class="section section--standort-cta">
			<div class="section__bg-photo"></div>
			<div class="section__content">
				<h2><?php printf( esc_html__( 'Odoo-Projekt in %s geplant?', 'letsdoo' ), esc_html( $ort ) ); ?></h2>
				<p class="section__subheading"><?php esc_html_e( 'Erstgespräch unverbindlich und kostenlos.', 'letsdoo' ); ?></p>
				<?php letsdoo_button( 'Kontakt aufnehmen', letsdoo_page_url_by_template( 'page-templates/template-contact.php', '/kontakt/' ) ); ?>
			</div>
		</section>

	</main>

	<?php
endwhile;

get_footer();
