<?php
/**
 * Template Name: Startseite
 */

get_header();
while ( have_posts() ) :
	the_post();

	$hero_image = get_field( 'hero_image' );
	$letsdoo_image = get_field( 'warum_letsdoo_image' );
	$leistungen = letsdoo_get_leistungen();
	$schritte   = get_field( 'vorgehen_schritte' );
	?>

	<main id="main" class="site-main">

		<section class="hero" id="start" style="background-image:url('<?php echo esc_url( letsdoo_image_url( $hero_image, 'placeholder-photo.svg' ) ); ?>');">
			<div class="hero__blob" aria-hidden="true">
				<img src="<?php echo esc_url( get_theme_file_uri( '/assets/images/logo-mark.png' ) ); ?>" alt="">
			</div>
			<div class="hero__panel">
				<h1><?php echo esc_html( get_field( 'hero_heading' ) ?: "Odoo einfach gemacht" ); ?></h1>
				<p><?php echo esc_html( get_field( 'hero_text' ) ?: 'Suchst du nach einer flexiblen ERP- oder CRM-Lösung? Oder bist du mit deiner aktuellen Odoo-Umgebung unzufrieden? Als Odoo-Partner aus Luzern unterstützen wir KMU dabei, ihre Geschäftsprozesse zu digitalisieren – persönlich, effizient und auf ihre Bedürfnisse abgestimmt.' ); ?></p>
				<?php letsdoo_button( get_field( 'hero_button_label' ) ?: 'Kontakt aufnehmen', get_field( 'hero_button_link' ) ?: home_url( '/kontakt/' ) ); ?>
			</div>
		</section>

		<section class="section section--warum-odoo" id="odoo">
			<div class="section__intro">
				<h2><?php echo esc_html( get_field( 'warum_odoo_heading' ) ?: 'Warum Odoo?' ); ?></h2>
				<p class="section__subheading"><?php echo esc_html( get_field( 'warum_odoo_subheading' ) ?: 'Eine Software für Ihr gesamtes Unternehmen.' ); ?></p>
				<p><?php echo esc_html( get_field( 'warum_odoo_text' ) ?: 'Odoo vereint CRM, Verkauf, Einkauf, Lager, Projekte, Buchhaltung und vieles mehr in einer einzigen Lösung. Dank des modularen Aufbaus wächst das System mit Ihrem Unternehmen mit – genau so, wie Sie es brauchen.' ); ?></p>
				<?php letsdoo_button( get_field( 'warum_odoo_button_label' ) ?: 'Kontakt aufnehmen', get_field( 'warum_odoo_button_link' ) ?: home_url( '/kontakt/' ) ); ?>
			</div>
		</section>

		<section class="section section--leistungen" id="leistungen">
			<div class="section__bg-photo" style="background-image:url('<?php echo esc_url( get_theme_file_uri( '/assets/images/placeholder-photo.svg' ) ); ?>');"></div>
			<div class="section__content">
				<h2><?php echo esc_html( get_field( 'leistungen_heading' ) ?: 'Unsere Leistungen' ); ?></h2>
				<div class="leistungen-grid">
					<?php if ( $leistungen ) : ?>
						<?php foreach ( $leistungen as $leistung ) : ?>
							<div class="leistung-card">
								<h3><?php echo esc_html( get_the_title( $leistung ) ); ?></h3>
								<p><?php echo esc_html( get_field( 'beschreibung', $leistung->ID ) ); ?></p>
							</div>
						<?php endforeach; ?>
					<?php else : ?>
						<p class="admin-hint"><?php esc_html_e( 'Noch keine Leistungen erfasst – unter „Leistungen“ im Menü hinzufügen.', 'letsdoo' ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<section class="section section--warum-letsdoo" id="warum-letsdoo">
			<div class="warum-letsdoo__panel">
				<h2><?php echo esc_html( get_field( 'warum_letsdoo_heading' ) ?: "Warum Let's Doo?" ); ?></h2>
				<p class="section__subheading"><?php echo esc_html( get_field( 'warum_letsdoo_subheading' ) ?: 'Persönlich. Transparent. Lösungsorientiert.' ); ?></p>
				<p><?php echo esc_html( get_field( 'warum_letsdoo_text' ) ?: 'Wir glauben, dass eine erfolgreiche Digitalisierung mehr braucht als nur Software. Deshalb begleiten wir unsere Kundinnen und Kunden persönlich, kommunizieren offen und entwickeln Lösungen, die im Arbeitsalltag wirklich funktionieren.' ); ?></p>
				<?php letsdoo_button( get_field( 'warum_letsdoo_button_label' ) ?: 'Kontakt aufnehmen', get_field( 'warum_letsdoo_button_link' ) ?: home_url( '/kontakt/' ) ); ?>
			</div>
			<div class="warum-letsdoo__image" style="background-image:url('<?php echo esc_url( letsdoo_image_url( $letsdoo_image, 'placeholder-photo.svg' ) ); ?>');"></div>
		</section>

		<section class="section section--vorgehen" id="vorgehen">
			<div class="section__bg-photo" style="background-image:url('<?php echo esc_url( get_theme_file_uri( '/assets/images/placeholder-photo.svg' ) ); ?>');"></div>
			<div class="vorgehen__panel">
				<h2><?php echo esc_html( get_field( 'vorgehen_heading' ) ?: 'Unser Vorgehen' ); ?></h2>
				<p class="section__subheading"><?php echo esc_html( get_field( 'vorgehen_subheading' ) ?: 'Schritt für Schritt zur passenden Lösung.' ); ?></p>
				<ol class="vorgehen-list">
					<?php if ( $schritte ) : ?>
						<?php foreach ( $schritte as $schritt ) : ?>
							<li><?php echo esc_html( $schritt['schritt_text'] ); ?></li>
						<?php endforeach; ?>
					<?php else : ?>
						<?php foreach ( array( 'Kennenlernen und Bedürfnisse verstehen', 'Prozesse analysieren und planen', 'Odoo implementieren und anpassen', 'Mitarbeitende schulen', 'Langfristig begleiten und unterstützen' ) as $default_schritt ) : ?>
							<li><?php echo esc_html( $default_schritt ); ?></li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ol>
			</div>
		</section>

		<section class="section section--cta" id="kontakt">
			<h2><?php echo esc_html( get_field( 'cta_heading' ) ?: 'Bereit für den nächsten Schritt?' ); ?></h2>
			<p><?php echo esc_html( get_field( 'cta_text' ) ?: 'Lassen Sie uns gemeinsam herausfinden, wie Odoo Ihr Unternehmen unterstützen kann. Vereinbaren Sie ein unverbindliches Erstgespräch – wir freuen uns darauf, Sie kennenzulernen.' ); ?></p>
			<?php letsdoo_button( get_field( 'cta_button_label' ) ?: 'Jetzt kontaktieren', get_field( 'cta_button_link' ) ?: home_url( '/kontakt/' ) ); ?>
		</section>

	</main>

	<?php
endwhile;
get_footer();
