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
	<div class="site-branding">
		<?php if ( has_custom_logo() ) : ?>
			<?php the_custom_logo(); ?>
		<?php else : ?>
			<p class="site-title">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
			</p>
			<?php $description = get_bloginfo( 'description', 'display' ); ?>
			<?php if ( $description ) : ?>
				<p class="site-description"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		<?php endif; ?>
	</div>

	<nav id="site-navigation" class="main-navigation">
		<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
			<span class="menu-toggle-bar"></span>
			<span class="menu-toggle-bar"></span>
			<span class="menu-toggle-bar"></span>
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'letsdoo' ); ?></span>
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
</header>
