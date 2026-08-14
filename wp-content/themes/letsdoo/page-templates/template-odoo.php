<?php
/**
 * Template Name: Odoo
 *
 * Dedicated "what is Odoo" landing page — the kind of page a prospect lands
 * on from a Google search for "Odoo" itself, before they know Let's Doo.
 * Same shape as single-standort.php: only the hero is fixed here (it carries
 * the H1), everything below is the_content(), built from the theme's blocks,
 * so the page can be reordered and extended without touching PHP.
 */

get_header();
while ( have_posts() ) :
	the_post();

	$hero_image        = get_field( 'hero_image' );
	$hero_heading      = get_field( 'hero_heading' ) ?: 'Odoo';
	$hero_subheading   = get_field( 'hero_subheading' );
	$hero_text         = get_field( 'hero_text' );
	$hero_button_label = get_field( 'hero_button_label' ) ?: 'Kontakt aufnehmen';
	$hero_button_link  = get_field( 'hero_button_link' ) ?: home_url( '/kontakt/' );
	?>

	<main id="main" class="site-main">

		<section class="hero hero--sub hero--odoo" style="background-image:url('<?php echo esc_url( letsdoo_image_url( $hero_image, 'placeholder-photo.svg', 'full' ) ); ?>');">
			<div class="hero__panel hero_inner">
				<h1><?php echo esc_html( $hero_heading ); ?></h1>
				<?php if ( $hero_subheading ) : ?>
					<p class="hero__eyebrow"><?php echo esc_html( $hero_subheading ); ?></p>
				<?php endif; ?>
				<?php if ( $hero_text ) : ?>
					<p><?php echo esc_html( $hero_text ); ?></p>
				<?php endif; ?>
				<?php letsdoo_button( $hero_button_label, $hero_button_link ); ?>
			</div>
		</section>

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
