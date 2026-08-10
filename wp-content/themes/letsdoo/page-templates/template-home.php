<?php
/**
 * Template Name: Startseite
 */

get_header();
while ( have_posts() ) :
	the_post();

	$hero_image        = get_field( 'hero_image' );
	$leistungen        = letsdoo_get_leistungen();
	$schritte          = letsdoo_get_vorgehen_schritte();
	?>

	<main id="main" class="site-main">

		<section class="hero" id="start" style="background-image:url('<?php echo esc_url( letsdoo_image_url( $hero_image, 'placeholder-photo.svg', 'full' ) ); ?>');">
			<div class="hero__inner">
				<div class="hero__blob" aria-hidden="true">
					<img src="<?php echo esc_url( get_theme_file_uri( '/assets/images/logo-mark.png' ) ); ?>" alt="">
				</div>
				<div class="hero__panel">
					<h1><?php echo esc_html( get_field( 'hero_heading' ) ?: "Odoo einfach gemacht" ); ?></h1>
					<p><?php echo esc_html( get_field( 'hero_text' ) ?: 'Suchst du nach einer flexiblen ERP- oder CRM-Lösung? Oder bist du mit deiner aktuellen Odoo-Umgebung unzufrieden? Als Odoo-Partner aus Luzern unterstützen wir KMU dabei, ihre Geschäftsprozesse zu digitalisieren – persönlich, effizient und auf ihre Bedürfnisse abgestimmt.' ); ?></p>
					<?php letsdoo_button( get_field( 'hero_button_label' ) ?: 'Kontakt aufnehmen', get_field( 'hero_button_link' ) ?: home_url( '/kontakt/' ) ); ?>
				</div>
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
			<div class="section__bg-photo"></div>
			<div class="section__content">
				<h2><?php echo esc_html( get_field( 'leistungen_heading' ) ?: 'Unsere Leistungen' ); ?></h2>
				<div class="leistungen-grid">
					<?php if ( $leistungen ) : ?>
						<?php foreach ( $leistungen as $index => $leistung ) : ?>
							<?php
							$leistung_id = $leistung->ID;
							$bild        = get_field( 'bild', $leistung_id );
							$is_wide     = ! empty( $bild );
							$is_reverse  = $is_wide && 1 === $index % 2;
							$icon        = get_field( 'icon', $leistung_id ) ?: 'check';
							$text        = get_field( 'beschreibung', $leistung_id );
							$merkmale    = letsdoo_lines( get_field( 'merkmale', $leistung_id ) );
							?>
							<div class="leistung-card <?php echo $is_wide ? 'leistung-card--wide' : ''; ?> <?php echo $is_reverse ? 'is-reverse' : ''; ?>">
								<?php if ( $is_wide ) : ?>
									<div class="leistung-card__image">
										<img src="<?php echo esc_url( letsdoo_image_url( $bild, 'placeholder-photo.svg', 'large' ) ); ?>" alt="<?php echo esc_attr( letsdoo_image_alt( $bild, get_the_title( $leistung_id ) ) ); ?>">
									</div>
								<?php endif; ?>
								<div class="leistung-card__body">
									<div class="leistung-card__icon"><?php echo letsdoo_icon( $icon ); ?></div>
									<h3><?php echo esc_html( get_the_title( $leistung_id ) ); ?></h3>
									<?php if ( $text ) : ?>
										<p><?php echo esc_html( $text ); ?></p>
									<?php endif; ?>
									<?php if ( $is_wide && $merkmale ) : ?>
										<ul class="leistung-card__merkmale">
											<?php foreach ( $merkmale as $merkmal ) : ?>
												<li><?php echo letsdoo_icon( 'check' ); ?> <?php echo esc_html( $merkmal ); ?></li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					<?php else : ?>
						<p class="admin-hint"><?php esc_html_e( 'Noch keine Leistungen erfasst – unter „Leistungen“ im Menü hinzufügen.', 'letsdoo' ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<section class="section section--warum-letsdoo" id="warum-letsdoo">
			<div class="section__content">
				<h2><?php echo esc_html( get_field( 'warum_letsdoo_heading' ) ?: "Warum Let's Doo?" ); ?></h2>
				<p class="section__subheading"><?php echo esc_html( get_field( 'warum_letsdoo_subheading' ) ?: 'Was uns von anderen Odoo-Partnern unterscheidet.' ); ?></p>
				<?php
				/*
				 * The three claims that used to sit compressed into the subheading
				 * as "Persönlich. Transparent. Lösungsorientiert." Read as three
				 * flat field groups rather than a repeater, dating from the free
				 * ACF build; PRO is installed now, so this is a pending migration
				 * rather than a limitation (see inc/acf-fields.php). Each falls
				 * back to real copy the same way the Vorgehen steps below do, so
				 * the section stands up before the client has filled anything in;
				 * a pillar with its Titel cleared drops out of the row.
				 */
				$punkte_defaults = array(
					1 => array( 'icon' => 'support', 'titel' => 'Persönlich', 'text' => 'Ihr habt feste Ansprechpersonen, die eure Prozesse kennen – keine Warteschleife, kein wechselndes Team.' ),
					2 => array( 'icon' => 'search', 'titel' => 'Transparent', 'text' => 'Aufwand, Zeitplan und Kosten liegen von Anfang an offen auf dem Tisch – auch wenn etwas länger dauert.' ),
					3 => array( 'icon' => 'rocket', 'titel' => 'Lösungsorientiert', 'text' => 'Wir bauen, was im Arbeitsalltag wirklich funktioniert, statt was auf dem Papier gut aussieht.' ),
				);

				$punkte = array();
				foreach ( $punkte_defaults as $i => $default ) {
					$titel = get_field( "warum_letsdoo_punkt_{$i}_titel" );
					$saved = null !== $titel && '' !== $titel;
					if ( $saved && ! trim( $titel ) ) {
						continue;
					}
					$punkte[] = array(
						'icon'  => get_field( "warum_letsdoo_punkt_{$i}_icon" ) ?: $default['icon'],
						'titel' => $saved ? $titel : $default['titel'],
						'text'  => get_field( "warum_letsdoo_punkt_{$i}_text" ) ?: ( $saved ? '' : $default['text'] ),
					);
				}
				?>
				<ul class="warum-letsdoo__punkte">
					<?php foreach ( $punkte as $punkt ) : ?>
						<li class="warum-letsdoo-punkt">
							<div class="warum-letsdoo-punkt__icon"><?php echo letsdoo_icon( $punkt['icon'] ?? 'check' ); ?></div>
							<h3><?php echo esc_html( $punkt['titel'] ?? '' ); ?></h3>
							<?php if ( ! empty( $punkt['text'] ) ) : ?>
								<p><?php echo esc_html( $punkt['text'] ); ?></p>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php letsdoo_button( get_field( 'warum_letsdoo_button_label' ) ?: 'Kontakt aufnehmen', get_field( 'warum_letsdoo_button_link' ) ?: home_url( '/kontakt/' ) ); ?>
			</div>
		</section>

		<section class="section section--vorgehen" id="vorgehen">
			<div class="section__bg-photo"></div>
			<div class="section__content">
				<h2><?php echo esc_html( get_field( 'vorgehen_heading' ) ?: 'Unser Vorgehen' ); ?></h2>
				<p class="section__subheading"><?php echo esc_html( get_field( 'vorgehen_subheading' ) ?: 'Schritt für Schritt zur passenden Lösung.' ); ?></p>
				<?php
				/*
				 * Steps come from the Vorgehen post type. Until any are entered
				 * the section would stand empty, so it falls back to the five
				 * steps that used to be hard-coded here — same behaviour as
				 * before, just rendered as the timeline.
				 */
				$schritte_fallback = array(
					array( 'titel' => 'Kennenlernen', 'text' => 'Wir hören zu und verstehen, wo ihr steht und was ihr braucht.', 'icon' => 'search' ),
					array( 'titel' => 'Analysieren & planen', 'text' => 'Wir schauen uns eure Prozesse an und legen den Weg fest.', 'icon' => 'settings' ),
					array( 'titel' => 'Umsetzen', 'text' => 'Odoo wird eingerichtet und auf euren Alltag zugeschnitten.', 'icon' => 'architecture' ),
					array( 'titel' => 'Schulen', 'text' => 'Euer Team lernt das System an konkreten Beispielen kennen.', 'icon' => 'training' ),
					array( 'titel' => 'Begleiten', 'text' => 'Nach dem Go-live bleiben wir ansprechbar und entwickeln weiter.', 'icon' => 'support' ),
				);

				$timeline = array();
				if ( $schritte ) {
					foreach ( $schritte as $schritt ) {
						$timeline[] = array(
							'titel' => get_the_title( $schritt->ID ),
							'text'  => get_field( 'beschreibung', $schritt->ID ),
							'icon'  => get_field( 'icon', $schritt->ID ) ?: 'check',
						);
					}
				} else {
					$timeline = $schritte_fallback;
				}
				?>
				<ol class="vorgehen-timeline">
					<?php foreach ( $timeline as $index => $schritt ) : ?>
						<li class="vorgehen-step">
							<div class="vorgehen-step__marker" aria-hidden="true">
								<?php echo letsdoo_icon( $schritt['icon'] ); ?>
							</div>
							<span class="vorgehen-step__nummer">
								<?php
								/* translators: %s: step number. */
								printf( esc_html__( 'Schritt %s', 'letsdoo' ), esc_html( $index + 1 ) );
								?>
							</span>
							<h3 class="vorgehen-step__titel"><?php echo esc_html( $schritt['titel'] ); ?></h3>
							<?php if ( $schritt['text'] ) : ?>
								<p class="vorgehen-step__text"><?php echo esc_html( $schritt['text'] ); ?></p>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
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
