<?php
/**
 * Standort — local SEO landing page ("Odoo <Ort>").
 *
 * Built from the same sections as the rest of the site rather than a stripped
 * layout of its own: a page that looks like a landing page reads as one to
 * visitors and to Google.
 *
 * Only the hero is rendered here. Everything below it is composed from the
 * theme's blocks (inc/blocks.php) so each town can carry a different mix of
 * sections in a different order — which is also the point: a set of pages that
 * differ only in the town name is what Google filters out as doorway pages.
 * inc/cpt.php gives a new Standort a starting skeleton.
 *
 * The hero and the title after it stay in the template deliberately. Between
 * them they carry the H1, and its "Odoo <Ort>" fallback is the guarantee that
 * a Standort can never be published with a heading that doesn't name the town
 * it exists to rank for — not something to leave deletable in the editor.
 *
 * Nothing links here — see the post type registration in inc/cpt.php.
 */

get_header();

while ( have_posts() ) :
	the_post();

	$ort          = get_field( 'ort' );
	$kanton       = get_field( 'kanton' );
	$hero_image   = get_field( 'hero_image' );
	$hero_heading = get_field( 'hero_heading' ) ?: sprintf( 'Odoo %s', $ort );
	$hero_text    = get_field( 'hero_text' );
	?>

	<main id="main" class="site-main">

		<section class="hero hero--sub hero--standort"<?php echo letsdoo_hero_bg_style( $hero_image ); ?>></section>

		<?php letsdoo_page_title( $hero_heading, $kanton, $hero_text ); ?>

		<?php
		/*
		 * The body of the page. Every section below the hero is a block, so the
		 * running order and the mix differ per town.
		 *
		 * the_content() is emitted straight into <main> with no wrapper, which
		 * matters: the wave dividers and background blends in 05-sections.css
		 * are keyed off `section:nth-of-type(3n+k)` among siblings, so the
		 * blocks have to land as direct children of <main> alongside the hero.
		 * Wrapping them in a div would restart the count and desynchronise the
		 * waves. (nth-of-type counts only <section>, so an interleaved
		 * paragraph or image is harmless.)
		 */
		the_content();
		?>

	</main>

	<?php
endwhile;

get_footer();
