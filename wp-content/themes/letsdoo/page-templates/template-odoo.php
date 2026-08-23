<?php
/**
 * Template Name: Odoo
 *
 * Dedicated "what is Odoo" landing page — the kind of page a prospect lands
 * on from a Google search for "Odoo" itself, before they know Let's Doo.
 * Same shape as single-standort.php: only the hero and the title after it
 * are fixed here (the H1 lives in the latter — see letsdoo_page_title(),
 * inc/template-helpers.php), everything below is the_content(), built from
 * the theme's blocks, so the page can be reordered and extended without
 * touching PHP.
 */

get_header();
while ( have_posts() ) :
	the_post();

	$hero_image      = get_field( 'hero_image' );
	$hero_heading    = get_field( 'hero_heading' ) ?: 'Odoo';
	$hero_subheading = get_field( 'hero_subheading' );
	$hero_text       = get_field( 'hero_text' );
	?>

	<main id="main" class="site-main">

		<section class="hero hero--sub hero--odoo"<?php echo letsdoo_hero_bg_style( $hero_image ); ?>></section>

		<?php letsdoo_page_title( $hero_heading, $hero_subheading, $hero_text ); ?>

		<?php
		/*
		 * The body of the page. Every section below the hero is a block (see
		 * inc/blocks.php / blocks/*), so the mix and running order can change
		 * without a template edit — same reasoning as single-standort.php,
		 * including the "no wrapper div" part: the wave dividers and
		 * background blends in 05-sections.css key off `section:nth-of-type`
		 * among siblings of <main>.
		 */
		the_content();
		?>

	</main>

	<?php
endwhile;

get_footer();
