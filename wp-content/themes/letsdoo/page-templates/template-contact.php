<?php
/**
 * Template Name: Kontakt
 */

get_header();
while ( have_posts() ) :
	the_post();

	$hero_image = get_field( 'hero_image' );
	$side_image = get_field( 'side_image' );
	$shortcode  = get_field( 'form_shortcode' );
	?>

	<main id="main" class="site-main">

		<section class="hero hero--sub" style="background-image:url('<?php echo esc_url( letsdoo_image_url( $hero_image, 'placeholder-photo.svg', 'full' ) ); ?>');">
			<div class="hero__panel hero_inner">
				<h1><?php echo esc_html( get_field( 'hero_heading' ) ?: 'Kontakt' ); ?></h1>
				<p class="hero__eyebrow"><?php echo esc_html( get_field( 'hero_subheading' ) ?: 'Kontaktiere uns' ); ?></p>
				<p><?php echo esc_html( get_field( 'hero_text' ) ?: 'Wir geben unser Bestes, dir baldmöglichst eine Antwort zu geben und mit dir in Verbindung zu treten.' ); ?></p>
			</div>
		</section>

		<section class="section section--kontaktformular">
			<div class="kontakt-grid">
				<div class="kontakt-form">
					<?php if ( $shortcode ) : ?>
						<?php echo do_shortcode( $shortcode ); ?>
					<?php else : ?>
						<?php
						$cf7_forms = get_posts( array( 'post_type' => 'wpcf7_contact_form', 'posts_per_page' => 1 ) );
						if ( $cf7_forms ) {
							echo do_shortcode( '[contact-form-7 id="' . $cf7_forms[0]->ID . '"]' );
						} else {
							echo '<p class="admin-hint">' . esc_html__( 'Kein Kontaktformular ausgewählt – bitte unter Seite bearbeiten → Kontakt Inhalte den Contact Form 7 Shortcode eintragen.', 'letsdoo' ) . '</p>';
						}
						?>
					<?php endif; ?>
				</div>

				<div class="kontakt-side" style="background-image:url('<?php echo esc_url( letsdoo_image_url( $side_image, 'placeholder-photo.svg', 'full' ) ); ?>');">
					<div class="kontakt-card">
						<img class="kontakt-card__logo" src="<?php echo esc_url( get_theme_file_uri( '/assets/images/logo-mark.png' ) ); ?>" width="40" height="30" alt="">
						<strong><?php echo esc_html( letsdoo_company_field( 'company_name', "Let's Doo GmbH" ) ); ?></strong>
						<p><?php echo nl2br( esc_html( letsdoo_company_field( 'company_address', "Schiltmatthalde 1\n6048 Horw" ) ) ); ?></p>
						<?php $email = letsdoo_company_field( 'company_email', 'contact@letsdoo.it' ); ?>
						<p><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></p>
						<?php $phone = letsdoo_company_field( 'company_phone', '+41 43 243 43 39' ); ?>
						<p><a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></p>
					</div>
				</div>
			</div>
		</section>

	</main>

	<?php
endwhile;
get_footer();
