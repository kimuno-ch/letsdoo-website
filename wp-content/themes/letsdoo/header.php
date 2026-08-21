<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Runs before body paints, so 05-sections.css's .js-reveal opacity:0 rule
	     never applies to a visitor whose JS didn't load (reveal.js is what
	     removes it again, section by section, as they scroll into view). -->
	<script>document.documentElement.classList.add('js-reveal');</script>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header id="masthead" class="site-header">
	<div class="site-header__inner">
		<div class="site-branding">
			<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php if ( has_custom_logo() ) : ?>
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<img src="<?php echo esc_url( get_theme_file_uri( '/assets/images/logo-mark.png' ) ); ?>" width="79" height="60" alt="<?php bloginfo( 'name' ); ?>">
				<?php endif; ?>
			</a>
		</div>

		<nav id="site-navigation" class="main-navigation">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'menu_id'        => 'primary-menu',
				'container'      => false,
				'fallback_cb'    => false,
				'walker'         => new Letsdoo_Nav_Walker(),
			) );
			?>
		</nav>

		<span class="nav-divider" aria-hidden="true"></span>

		<div class="site-header__actions">
			<?php $phone = letsdoo_company_field( 'company_phone' ); ?>
			<?php $email = letsdoo_company_field( 'company_email' ); ?>
			<?php if ( $email ) : ?>
				<a class="icon-link" href="mailto:<?php echo esc_attr( $email ); ?>" aria-label="<?php esc_attr_e( 'E-Mail schreiben', 'letsdoo' ); ?>" data-kontakt-modal="open">
					<?php echo letsdoo_icon( 'mail' ); ?>
				</a>
			<?php endif; ?>
			<?php if ( $phone ) : ?>
				<a class="icon-link" href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $phone ) ); ?>" aria-label="<?php esc_attr_e( 'Anrufen', 'letsdoo' ); ?>">
					<?php echo letsdoo_icon( 'phone' ); ?>
				</a>
			<?php endif; ?>
			<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
				<span class="menu-toggle-bar"></span>
				<span class="menu-toggle-bar"></span>
				<span class="menu-toggle-bar"></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Menü', 'letsdoo' ); ?></span>
			</button>
			<!-- <a class="btn btn--sm" href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>"><?php esc_html_e( 'Kontakt aufnehmen', 'letsdoo' ); ?></a> -->
		</div>
	</div>
</header>
