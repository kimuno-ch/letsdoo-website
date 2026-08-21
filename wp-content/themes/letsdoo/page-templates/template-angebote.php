<?php
/**
 * Template Name: Angebote
 */

get_header();
while ( have_posts() ) :
	the_post();

	$hero_image = get_field( 'hero_image' );

	$default_vorteile = array(
		array( 'titel' => 'Zertifizierte Sicherheit', 'text' => 'Sichere Cloud-Infrastruktur und DSGVO-konforme Datenhaltung.' ),
		array( 'titel' => 'Effizienz-Boost', 'text' => 'Automatisierung von Routineaufgaben spart bis zu 40% Ihrer Zeit.' ),
	);
	$vorteile = get_field( 'vertrauen_vorteile' );
	$vorteile = $vorteile ? $vorteile : $default_vorteile;
	$vertrauen_image = get_field( 'vertrauen_image' );

	$pakete = letsdoo_get_pakete();
	?>

	<main id="main" class="site-main">

		<section class="hero hero--sub" style="background-image:url('<?php echo esc_url( letsdoo_image_url( $hero_image, 'placeholder-photo.svg', 'full' ) ); ?>');"></section>

		<?php
		letsdoo_page_title(
			get_field( 'hero_heading' ) ?: 'Angebote & Pakete',
			get_field( 'hero_badge' ) ?: 'Offizieller Odoo Partner',
			get_field( 'hero_text' ) ?: 'Transparente Pakete für jede Unternehmensgrösse – von der ersten Odoo-Einrichtung bis zur individuellen Grossprojekt-Betreuung.'
		);
		?>

		<?php
		/*
		 * "Unsere Leistungen" used to be a fixed section here, reading its own
		 * heading + Leistungen selection from ACF (the old .leistung-card--wide
		 * / is-reverse alternating photo layout, .leistungen-grid). It's block
		 * content now — the_content() renders whatever's in the editor at this
		 * exact spot, same shift single-standort.php and template-odoo.php
		 * already made for their own bodies. Add the Leistungen block (small
		 * cards; the old wide/photo variant isn't something it supports) or any
		 * other block here. No wrapper div around it, same reasoning as those
		 * two templates: the wave dividers and background blends in
		 * 05-sections.css key off `section:nth-of-type` among siblings of
		 * <main>, and a wrapper would break that count.
		 */
		the_content();
		?>

		<section class="section section--vertrauen" id="vertrauen">
			<div class="vertrauen-grid">
				<div class="vertrauen__image">
					<img src="<?php echo esc_url( letsdoo_image_url( $vertrauen_image, 'placeholder-photo.svg', 'large' ) ); ?>" alt="<?php echo esc_attr( letsdoo_image_alt( $vertrauen_image, 'Let\'s Doo Team bei der Arbeit' ) ); ?>">
				</div>
				<div class="vertrauen__content">
					<h2><?php echo esc_html( get_field( 'vertrauen_heading' ) ?: 'Expertise aus Leidenschaft' ); ?></h2>
					<p><?php echo esc_html( get_field( 'vertrauen_text' ) ?: 'Als zertifizierter Odoo-Partner kombinieren wir technologisches Know-how mit tiefem betriebswirtschaftlichem Verständnis.' ); ?></p>
					<div class="vertrauen__vorteile">
						<?php foreach ( $vorteile as $vorteil ) : ?>
							<div class="vorteil">
								<span class="vorteil__icon"><?php echo letsdoo_icon( 'check' ); ?></span>
								<div>
									<h4><?php echo esc_html( $vorteil['titel'] ); ?></h4>
									<p><?php echo esc_html( $vorteil['text'] ); ?></p>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>

		<?php if ( $pakete ) : ?>
		<section class="section section--pakete" id="pakete">
			<div class="section__intro">
				<h2><?php echo esc_html( get_field( 'pakete_heading' ) ?: 'Wählen Sie Ihr Paket' ); ?></h2>
				<p class="section__subheading"><?php echo esc_html( get_field( 'pakete_subheading' ) ?: 'Transparente Preise für jedes Unternehmensstadium.' ); ?></p>
			</div>
				<div class="pakete-grid">
					<?php foreach ( $pakete as $paket ) : ?>
						<?php
						$paket_id      = $paket->ID;
						$hervorgehoben = ! empty( get_field( 'hervorgehoben', $paket_id ) );
						$badge         = get_field( 'badge', $paket_id );
						$preis         = get_field( 'preis', $paket_id );
						$preis_suffix  = get_field( 'preis_suffix', $paket_id );
						$preis_hinweis = get_field( 'preis_hinweis', $paket_id );
						$merkmale      = letsdoo_merkmale_liste( get_field( 'merkmale', $paket_id ) );
						$button_label  = get_field( 'button_label', $paket_id ) ?: 'Jetzt anfragen';
						$button_link   = get_field( 'button_link', $paket_id ) ?: home_url( '/kontakt/' );
						?>
						<div class="paket-card <?php echo $hervorgehoben ? 'paket-card--hervorgehoben' : ''; ?>">
							<?php if ( $hervorgehoben && $badge ) : ?>
								<span class="paket-card__badge"><?php echo esc_html( $badge ); ?></span>
							<?php endif; ?>
							<h3 class="paket-card__name"><?php echo esc_html( get_the_title( $paket_id ) ); ?></h3>
							<?php if ( get_field( 'untertitel', $paket_id ) ) : ?>
								<p class="paket-card__untertitel"><?php echo esc_html( get_field( 'untertitel', $paket_id ) ); ?></p>
							<?php endif; ?>
							<?php if ( $preis ) : ?>
								<div class="paket-card__preis-wrap">
									<span class="paket-card__preis"><?php echo esc_html( $preis ); ?></span>
									<?php if ( $preis_suffix ) : ?>
										<span class="paket-card__preis-suffix"><?php echo esc_html( $preis_suffix ); ?></span>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							<?php if ( $preis_hinweis ) : ?>
								<p class="paket-card__hinweis"><?php echo esc_html( $preis_hinweis ); ?></p>
							<?php endif; ?>
							<?php if ( $merkmale ) : ?>
								<ul class="paket-card__merkmale">
									<?php foreach ( $merkmale as $merkmal ) : ?>
										<li class="<?php echo $merkmal['enthalten'] ? '' : 'is-disabled'; ?>">
											<?php echo $merkmal['enthalten'] ? letsdoo_icon( 'check' ) : letsdoo_icon( 'cross' ); ?>
											<?php echo esc_html( $merkmal['text'] ); ?>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
							<?php letsdoo_button( $button_label, $button_link, $hervorgehoben ? '' : 'btn--outline' ); ?>
						</div>
					<?php endforeach; ?>
				</div>
		</section>
		<?php endif; ?>

		<section class="section section--cta-banner" id="kontakt">
			<div class="cta-banner">
				<h2><?php echo esc_html( get_field( 'cta_heading' ) ?: 'Bereit für den nächsten Schritt?' ); ?></h2>
				<p><?php echo esc_html( get_field( 'cta_text' ) ?: 'Lassen Sie uns gemeinsam herausfinden, wie Odoo Ihre Geschäftsprozesse revolutionieren kann.' ); ?></p>
				<div class="cta-banner__actions">
					<?php letsdoo_button( get_field( 'cta_button2_label' ) ?: 'Kontakt aufnehmen', get_field( 'cta_button2_link' ) ?: home_url( '/kontakt/' ) ); ?>
				</div>
			</div>
		</section>

	</main>

	<?php
endwhile;
get_footer();
