<?php get_header(); ?>

<main id="main">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<?php the_excerpt(); ?>
			</article>
			<?php
		endwhile;
		the_posts_navigation();
	else :
		?>
		<p><?php esc_html_e( 'Nothing found.', 'letsdoo' ); ?></p>
		<?php
	endif;
	?>
</main>

<?php get_footer(); ?>
