	<footer id="colophon" class="site-footer">
		<div class="site-footer__columns">
			<div class="footer-col footer-col--brand">
				<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php if ( has_custom_logo() ) : ?>
						<?php the_custom_logo(); ?>
					<?php else : ?>
						<?php echo letsdoo_render_hub_mark(); ?>
					<?php endif; ?>
				</a>
			</div>

			<div class="footer-col">
				<h3 class="footer-col__title"><?php esc_html_e( 'Anschrift', 'letsdoo' ); ?></h3>
				<p><?php echo nl2br( esc_html( letsdoo_company_field( 'company_address', "Schiltmatthalde 1\n6048 Horw" ) ) ); ?></p>
				<?php $email = letsdoo_company_field( 'company_email', 'contact@letsdoo.it' ); ?>
				<p><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo letsdoo_icon( 'mail' ); ?> <?php echo esc_html( $email ); ?></a></p>
				<?php $phone = letsdoo_company_field( 'company_phone', '+41 43 243 43 39' ); ?>
				<p><a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $phone ) ); ?>"><?php echo letsdoo_icon( 'phone' ); ?> <?php echo esc_html( $phone ); ?></a></p>
			</div>

			<div class="footer-col">
				<h3 class="footer-col__title"><?php esc_html_e( 'Social', 'letsdoo' ); ?></h3>
				<div class="footer-col__social">
					<?php $socials = get_field( 'socials', 'option' ); ?>
					<?php foreach ( (array) $socials as $social ) : ?>
						<?php if ( empty( $social['url'] ) ) { continue; } ?>
						<a class="social-icon" href="<?php echo esc_url( $social['url'] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $social['label'] ); ?>">
							<?php echo $social['icon'] ?: letsdoo_icon( 'check' ); ?>
						</a>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="footer-col">
				<h3 class="footer-col__title"><?php esc_html_e( 'Navigation', 'letsdoo' ); ?></h3>
				<?php
				wp_nav_menu( array(
					'theme_location' => 'footer',
					'menu_id'        => 'footer-menu',
					'container'      => false,
					'fallback_cb'    => false,
				) );
				?>
			</div>
		</div>

		<div class="site-footer__bottom">
			<p class="site-info">
				&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php echo esc_html( letsdoo_company_field( 'company_name', get_bloginfo( 'name' ) ) ); ?>
			</p>
			<div class="footer-badge">
				<img src="<?php echo esc_url( get_theme_file_uri( '/assets/images/odoo-ready-partners.svg' ) ); ?>" width="190" height="95" alt="Odoo Ready Partner" loading="lazy">
			</div>
			<div class="legal-links">
				<a href="<?php echo esc_url( home_url( '/impressum/' ) ); ?>"><?php esc_html_e( 'Impressum', 'letsdoo' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/datenschutz/' ) ); ?>"><?php esc_html_e( 'Datenschutz', 'letsdoo' ); ?></a>
			</div>
		</div>
	</footer>

	<?php if ( ! letsdoo_is_kontakt_page() ) : ?>
		<?php get_template_part( 'template-parts/kontakt-modal' ); ?>
	<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
