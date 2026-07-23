<?php
/**
 * Blog post detail.
 *
 * The body is authored with core blocks, so this template only frames it
 * and shares the .entry-content / .entry-nav styling with the Referenz
 * case-study pages.
 */

get_header();

while ( have_posts() ) :
	the_post();

	$thumb_id   = get_post_thumbnail_id();
	$kategorien = get_the_category();
	$schlagwoerter = get_the_tags();
	$blog_url   = get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/' );
	?>

	<main id="main" class="site-main">

		<article <?php post_class(); ?>>

			<section class="hero hero--sub hero--post" style="background-image:url('<?php echo esc_url( letsdoo_image_url( $thumb_id, 'placeholder-photo.svg', 'full' ) ); ?>');">
				<div class="hero__panel">
					<?php if ( $kategorien ) : ?>
						<a class="hero__badge" href="<?php echo esc_url( get_category_link( $kategorien[0] ) ); ?>">
							<?php echo esc_html( $kategorien[0]->name ); ?>
						</a>
					<?php endif; ?>

					<h1><?php the_title(); ?></h1>

					<p class="post-meta">
						<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
						<span class="post-meta__sep" aria-hidden="true">·</span>
						<?php
						printf(
							/* translators: %d: reading time in minutes */
							esc_html__( '%d Min. Lesezeit', 'letsdoo' ),
							(int) letsdoo_reading_time()
						);
						?>
					</p>
				</div>
			</section>

			<section class="section section--blog-inhalt">
				<div class="section__content">

					<div class="entry-content">
						<?php the_content(); ?>
					</div>

					<?php if ( $schlagwoerter ) : ?>
						<div class="entry-tags">
							<?php foreach ( $schlagwoerter as $tag ) : ?>
								<a class="entry-tag" href="<?php echo esc_url( get_tag_link( $tag ) ); ?>">#<?php echo esc_html( $tag->name ); ?></a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php
					$prev_post = get_previous_post();
					$next_post = get_next_post();
					?>
					<?php if ( $prev_post || $next_post ) : ?>
						<nav class="entry-nav" aria-label="<?php esc_attr_e( 'Weitere Beiträge', 'letsdoo' ); ?>">
							<?php if ( $prev_post ) : ?>
								<a class="entry-nav__link" href="<?php echo esc_url( get_permalink( $prev_post ) ); ?>">
									<span class="entry-nav__label"><?php esc_html_e( 'Älterer Beitrag', 'letsdoo' ); ?></span>
									<span class="entry-nav__titel"><?php echo esc_html( get_the_title( $prev_post ) ); ?></span>
								</a>
							<?php endif; ?>
							<?php if ( $next_post ) : ?>
								<a class="entry-nav__link entry-nav__link--next" href="<?php echo esc_url( get_permalink( $next_post ) ); ?>">
									<span class="entry-nav__label"><?php esc_html_e( 'Neuerer Beitrag', 'letsdoo' ); ?></span>
									<span class="entry-nav__titel"><?php echo esc_html( get_the_title( $next_post ) ); ?></span>
								</a>
							<?php endif; ?>
						</nav>
					<?php endif; ?>

				</div>
			</section>

		</article>

		<section class="section section--cta-banner" id="kontakt">
			<div class="cta-banner">
				<h2><?php esc_html_e( 'Bereit für den nächsten Schritt?', 'letsdoo' ); ?></h2>
				<p><?php esc_html_e( 'Lassen Sie uns gemeinsam herausfinden, wie Odoo Ihr Unternehmen unterstützen kann.', 'letsdoo' ); ?></p>
				<div class="cta-banner__actions">
					<?php letsdoo_button( __( 'Kontakt aufnehmen', 'letsdoo' ), home_url( '/kontakt/' ), 'btn--white' ); ?>
					<?php letsdoo_button( __( 'Alle Beiträge', 'letsdoo' ), $blog_url, 'btn--ghost-white' ); ?>
				</div>
			</div>
		</section>

	</main>

	<?php
endwhile;

get_footer();
