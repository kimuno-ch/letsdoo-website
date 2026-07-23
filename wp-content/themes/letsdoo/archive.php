<?php
/**
 * Category / tag / date / author archives for blog posts. Same card grid
 * as the blog overview, with the archive title in the hero.
 */

get_header();
?>

<main id="main" class="site-main">

	<section class="hero hero--sub" style="background-image:url('<?php echo esc_url( get_theme_file_uri( '/assets/images/placeholder-photo.svg' ) ); ?>');">
		<div class="hero__panel">
			<span class="hero__badge"><?php esc_html_e( 'Blog', 'letsdoo' ); ?></span>
			<h1><?php echo esc_html( wp_strip_all_tags( get_the_archive_title() ) ); ?></h1>
			<?php $letsdoo_archiv_text = get_the_archive_description(); ?>
			<?php if ( $letsdoo_archiv_text ) : ?>
				<p><?php echo esc_html( wp_strip_all_tags( $letsdoo_archiv_text ) ); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<section class="section section--blog">
		<div class="section__content">
			<?php if ( have_posts() ) : ?>

				<div class="blog-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/content', 'card' );
					endwhile;
					?>
				</div>

				<?php letsdoo_pagination(); ?>

			<?php else : ?>
				<p class="admin-hint"><?php esc_html_e( 'Keine Beiträge gefunden.', 'letsdoo' ); ?></p>
			<?php endif; ?>
		</div>
	</section>

</main>

<?php
get_footer();
