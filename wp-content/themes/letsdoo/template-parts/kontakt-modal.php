<?php
/**
 * Site-wide Kontakt modal: the same form + "Direkt erreichen" content as
 * page-templates/template-contact.php, opened by any "Kontakt aufnehmen"
 * button instead of navigating away. Included once from footer.php on every
 * page except the Kontakt page itself (see letsdoo_is_kontakt_page()).
 */

$kontakt_page_id = letsdoo_kontakt_page_id();
$shortcode        = $kontakt_page_id ? get_field( 'form_shortcode', $kontakt_page_id ) : '';
$email            = letsdoo_company_field( 'company_email', 'contact@letsdoo.it' );
$phone            = letsdoo_company_field( 'company_phone', '+41 43 243 43 39' );
?>
<div class="kontakt-modal" id="kontakt-modal" hidden>
	<div class="kontakt-modal__backdrop" data-kontakt-modal="close"></div>
	<div class="kontakt-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="kontakt-modal-title">
		<button type="button" class="kontakt-modal__close" data-kontakt-modal="close" aria-label="<?php esc_attr_e( 'Schliessen', 'letsdoo' ); ?>">
			<?php echo letsdoo_icon( 'cross' ); ?>
		</button>
		<div class="kontakt-modal__content">
			<div class="kontakt-layout">
				<div class="kontakt-form">
					<h2 id="kontakt-modal-title"><?php esc_html_e( 'Schreib uns', 'letsdoo' ); ?></h2>
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

				<aside class="kontakt-infos">
					<div class="kontakt-infos__mark"><?php echo letsdoo_render_hub_mark(); ?></div>
					<h2><?php esc_html_e( 'Direkt erreichen', 'letsdoo' ); ?></h2>
					<p class="kontakt-infos__firma"><?php echo esc_html( letsdoo_company_field( 'company_name', "Let's Doo GmbH" ) ); ?></p>

					<ul class="kontakt-info-liste">
						<li class="kontakt-info">
							<span class="kontakt-info__icon" aria-hidden="true"><?php echo letsdoo_icon( 'pin' ); ?></span>
							<span class="kontakt-info__label"><?php esc_html_e( 'Adresse', 'letsdoo' ); ?></span>
							<span class="kontakt-info__wert"><?php echo nl2br( esc_html( letsdoo_company_field( 'company_address', "Schiltmatthalde 1\n6048 Horw" ) ) ); ?></span>
						</li>
						<li class="kontakt-info">
							<span class="kontakt-info__icon" aria-hidden="true"><?php echo letsdoo_icon( 'mail' ); ?></span>
							<span class="kontakt-info__label"><?php esc_html_e( 'E-Mail', 'letsdoo' ); ?></span>
							<a class="kontakt-info__wert" href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
						</li>
						<li class="kontakt-info">
							<span class="kontakt-info__icon" aria-hidden="true"><?php echo letsdoo_icon( 'phone' ); ?></span>
							<span class="kontakt-info__label"><?php esc_html_e( 'Telefon', 'letsdoo' ); ?></span>
							<a class="kontakt-info__wert" href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
						</li>
					</ul>
				</aside>
			</div>
		</div>
	</div>
</div>
