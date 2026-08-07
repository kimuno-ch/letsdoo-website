<?php
/**
 * Template Name: Über uns
 */

get_header();
while ( have_posts() ) :
	the_post();

	$hero_image  = get_field( 'hero_image' );
	$referenzen  = letsdoo_get_referenzen();
	$team        = letsdoo_get_team();
	$zahlen      = get_field( 'zahlen_liste' );
	?>

	<main id="main" class="site-main">

		<section class="hero hero--sub" style="background-image:url('<?php echo esc_url( letsdoo_image_url( $hero_image, 'placeholder-photo.svg', 'full' ) ); ?>');">
			<div class="hero__panel">
				<h1><?php echo esc_html( get_field( 'hero_heading' ) ?: 'Über uns' ); ?></h1>
				<p><?php echo esc_html( get_field( 'hero_text' ) ?: "Wir sind Let'sDoo – Ihr Odoo-Partner aus Luzern. Wir setzen auf persönliche Beratung, pragmatische Lösungen und langfristige Zusammenarbeit." ); ?></p>
			</div>
		</section>

		<section class="section section--referenzen" id="referenzen">
			<div class="section__content">
				<h2><?php echo esc_html( get_field( 'referenzen_heading' ) ?: 'Referenzen' ); ?></h2>
				<div class="referenzen-grid">
					<?php if ( $referenzen ) : ?>
						<?php foreach ( $referenzen as $referenz ) : ?>
							<div class="referenz-card">
								<div class="referenz-card__logo">
									<img src="<?php echo esc_url( letsdoo_image_url( get_post_thumbnail_id( $referenz ), 'placeholder-logo.svg' ) ); ?>" alt="<?php echo esc_attr( get_the_title( $referenz ) ); ?>">
								</div>
								<div class="referenz-card__body">
									<h3><?php echo esc_html( get_the_title( $referenz ) ); ?></h3>
									<p><?php echo esc_html( get_field( 'beschreibung', $referenz->ID ) ); ?></p>
									<a class="btn btn--sm referenz-card__link" href="<?php echo esc_url( get_permalink( $referenz ) ); ?>">
										<?php esc_html_e( 'Referenz ansehen', 'letsdoo' ); ?>
										<span class="screen-reader-text">: <?php echo esc_html( get_the_title( $referenz ) ); ?></span>
									</a>
								</div>
							</div>
						<?php endforeach; ?>
					<?php else : ?>
						<p class="admin-hint"><?php esc_html_e( 'Noch keine Referenzen erfasst – unter „Referenzen“ im Menü hinzufügen.', 'letsdoo' ); ?></p>
					<?php endif; ?>
				</div>
				<?php letsdoo_button( 'Kontakt aufnehmen', home_url( '/kontakt/' ) ); ?>
			</div>
		</section>

		<section class="section section--zahlen" id="zahlen">
			<div class="section__bg-photo"></div>
			<div class="section__content">
				<h2><?php echo esc_html( get_field( 'zahlen_heading' ) ?: 'Du glaubst an Zahlen?' ); ?></h2>
				<p class="zahlen__intro"><?php echo esc_html( get_field( 'zahlen_text' ) ?: 'Wir haben zusammen mehr Jahre in Code, Projektmanagement und Datenanalyse gesteckt, als so mancher Dinosaurier alt wurde – genau deshalb wissen wir, wie wir Odoo zum Laufen bringen, und zwar für dein Business.' ); ?></p>
				<div class="zahlen-grid">
					<?php
					$default_zahlen = array(
						array( 'zahl_wert' => '23', 'zahl_label' => 'Jahre Analysieren von Businessprozessen' ),
						array( 'zahl_wert' => '09', 'zahl_label' => 'Jahre Projektmanagement im Kundenauftrag' ),
						array( 'zahl_wert' => '14', 'zahl_label' => 'Jahre SEO / Marketing' ),
						array( 'zahl_wert' => '42', 'zahl_label' => 'Jahre Programmieren' ),
					);
					$zahlen_liste = $zahlen ? $zahlen : $default_zahlen;
					foreach ( $zahlen_liste as $zahl ) :
						?>
						<div class="zahl-card">
							<span class="zahl-card__wert"><?php echo esc_html( $zahl['zahl_wert'] ); ?></span>
							<span class="zahl-card__label"><?php echo esc_html( $zahl['zahl_label'] ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="section section--team" id="team">
			<div class="section__content">
				<h2><?php echo esc_html( get_field( 'team_heading' ) ?: 'Unser Team' ); ?></h2>
				<div class="team-grid">
					<?php if ( $team ) : ?>
						<?php foreach ( $team as $mitglied ) : ?>
							<div class="team-card">
								<div class="team-card__photo">
									<img src="<?php echo esc_url( letsdoo_image_url( get_post_thumbnail_id( $mitglied ), 'placeholder-portrait.svg' ) ); ?>" alt="<?php echo esc_attr( get_the_title( $mitglied ) ); ?>">
								</div>
								<h3><?php echo esc_html( get_the_title( $mitglied ) ); ?></h3>
								<p><?php echo esc_html( get_field( 'position', $mitglied->ID ) ); ?></p>
							</div>
						<?php endforeach; ?>
					<?php else : ?>
						<p class="admin-hint"><?php esc_html_e( 'Noch keine Teammitglieder erfasst – unter „Team“ im Menü hinzufügen.', 'letsdoo' ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</section>

	</main>

	<?php
endwhile;
get_footer();
