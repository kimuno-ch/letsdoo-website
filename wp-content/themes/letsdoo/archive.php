<?php
/**
 * Category / tag / date / author archives for blog posts. Same card grid
 * as the blog overview, with the archive title in the hero.
 */

get_header();
?>

<main id="main" class="site-main">

	<section class="hero hero--sub"<?php echo letsdoo_hero_bg_style( null ); ?>></section>

	<?php
	$letsdoo_archiv_text = wp_strip_all_tags( get_the_archive_description() );
	letsdoo_page_title( wp_strip_all_tags( get_the_archive_title() ), __( 'Blog', 'letsdoo' ), $letsdoo_archiv_text );
	?>

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
