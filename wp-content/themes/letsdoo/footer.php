	<footer id="colophon" class="site-footer">
		<div class="site-footer__columns">
			<div class="footer-col footer-col--brand">
				<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php if ( has_custom_logo() ) : ?>
						<?php the_custom_logo(); ?>
					<?php else : ?>
						<img src="<?php echo esc_url( get_theme_file_uri( '/assets/images/logo-mark.png' ) ); ?>" width="69" height="52" alt="<?php bloginfo( 'name' ); ?>">
					<?php endif; ?>
				</a>
			</div>

			<div class="footer-col">
				<h3 class="footer-col__title"><?php esc_html_e( 'Anschrift', 'letsdoo' ); ?></h3>
				<p><?php echo nl2br( esc_html( letsdoo_company_field( 'company_address', "Schiltmatthalde 1\n6048 Horw" ) ) ); ?></p>
				<?php $email = letsdoo_company_field( 'company_email', 'contact@letsdoo.it' ); ?>
				<p><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></p>
				<?php $phone = letsdoo_company_field( 'company_phone', '+41 43 243 43 39' ); ?>
				<p><a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></p>
			</div>

			<div class="footer-col">
				<h3 class="footer-col__title"><?php esc_html_e( 'Social', 'letsdoo' ); ?></h3>
				<div class="footer-col__social">
					<?php $linkedin = letsdoo_company_field( 'linkedin_url' ); ?>
					<?php if ( $linkedin ) : ?>
						<a class="social-icon" href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener" aria-label="LinkedIn">
							<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5zM3 9h4v12H3zM9 9h3.8v1.7h.1c.5-1 1.8-2 3.7-2 4 0 4.7 2.6 4.7 6V21h-4v-5.5c0-1.3 0-3-1.8-3s-2.1 1.4-2.1 2.9V21H9z"/></svg>
						</a>
					<?php endif; ?>
					<?php $instagram = letsdoo_company_field( 'instagram_url' ); ?>
					<?php if ( $instagram ) : ?>
						<a class="social-icon" href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener" aria-label="Instagram">
							<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.5" fill="currentColor"/></svg>
						</a>
					<?php endif; ?>
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
			<div class="legal-links">
				<span><?php esc_html_e( 'Impressum', 'letsdoo' ); ?></span>
				<span><?php esc_html_e( 'Datenschutz', 'letsdoo' ); ?></span>
			</div>
		</div>
	</footer>

<?php wp_footer(); ?>
</body>
</html>
