<?php
/**
 * Fallback template — in practice this serves search results, since the
 * blog index has home.php and the archives have archive.php. Uses the
 * same card grid so listings look consistent everywhere.
 */

get_header();

$title = is_search()
	/* translators: %s: search term */
	? sprintf( __( 'Suchergebnisse für „%s“', 'letsdoo' ), get_search_query() )
	: __( 'Beiträge', 'letsdoo' );
?>

<main id="main" class="site-main">

	<section class="hero hero--sub"<?php echo letsdoo_hero_bg_style( null ); ?>></section>

	<?php letsdoo_page_title( $title ); ?>

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
				<p class="admin-hint"><?php esc_html_e( 'Nichts gefunden.', 'letsdoo' ); ?></p>
			<?php endif; ?>
		</div>
	</section>

</main>

<?php
get_footer();
