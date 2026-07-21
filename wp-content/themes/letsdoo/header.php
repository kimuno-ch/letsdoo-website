<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
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
			<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
				<span class="menu-toggle-bar"></span>
				<span class="menu-toggle-bar"></span>
				<span class="menu-toggle-bar"></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Menü', 'letsdoo' ); ?></span>
			</button>
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'menu_id'        => 'primary-menu',
				'container'      => false,
				'fallback_cb'    => false,
			) );
			?>
		</nav>

		<span class="nav-divider" aria-hidden="true"></span>

		<div class="site-header__actions">
			<?php $phone = letsdoo_company_field( 'company_phone' ); ?>
			<?php $email = letsdoo_company_field( 'company_email' ); ?>
			<?php if ( $email ) : ?>
				<a class="icon-link" href="mailto:<?php echo esc_attr( $email ); ?>" aria-label="<?php esc_attr_e( 'E-Mail schreiben', 'letsdoo' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 6l10 7 10-7"/></svg>
				</a>
			<?php endif; ?>
			<?php if ( $phone ) : ?>
				<a class="icon-link" href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $phone ) ); ?>" aria-label="<?php esc_attr_e( 'Anrufen', 'letsdoo' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.1-8.7A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .3 2 .6 3a2 2 0 0 1-.5 2.1L8 10a16 16 0 0 0 6 6l1.2-1.2a2 2 0 0 1 2.1-.5c1 .3 2 .5 3 .6a2 2 0 0 1 1.7 2z"/></svg>
				</a>
			<?php endif; ?>
			<a class="btn btn--sm" href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>"><?php esc_html_e( 'Kontakt aufnehmen', 'letsdoo' ); ?></a>
		</div>
	</div>
</header>
