<?php
/**
 * Blog overview.
 *
 * Rendered for the page selected as "Beitragsseite" under
 * Einstellungen → Lesen. The hero texts are ACF fields on that page (see
 * the "Blog Inhalte" group in inc/acf-fields.php); because the main loop
 * here holds posts rather than the page, they're read with the page ID.
 */

get_header();

$blog_page_id = (int) get_option( 'page_for_posts' );
$hero_image   = $blog_page_id ? get_field( 'hero_image', $blog_page_id ) : null;
$hero_heading = $blog_page_id ? get_field( 'hero_heading', $blog_page_id ) : '';
$hero_text    = $blog_page_id ? get_field( 'hero_text', $blog_page_id ) : '';

if ( ! $hero_heading ) {
	$hero_heading = $blog_page_id ? get_the_title( $blog_page_id ) : __( 'Blog', 'letsdoo' );
}
?>

<main id="main" class="site-main">

	<section class="hero hero--sub" style="background-image:url('<?php echo esc_url( letsdoo_image_url( $hero_image, 'placeholder-photo.svg', 'full' ) ); ?>');">
		<div class="hero__panel">
			<span class="hero__badge"><?php esc_html_e( 'Blog', 'letsdoo' ); ?></span>
			<h1><?php echo esc_html( $hero_heading ); ?></h1>
			<p><?php echo esc_html( $hero_text ?: __( 'Praxisnahe Beiträge rund um Odoo, Digitalisierung und effiziente Geschäftsprozesse – aus unserem Alltag als Odoo-Partner.', 'letsdoo' ) ); ?></p>
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
				<p class="admin-hint"><?php esc_html_e( 'Noch keine Beiträge veröffentlicht – unter „Beiträge“ im Menü erfassen.', 'letsdoo' ); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<section class="section section--cta-banner" id="kontakt">
		<div class="cta-banner">
			<h2><?php esc_html_e( 'Fragen zu Ihrem Odoo-Projekt?', 'letsdoo' ); ?></h2>
			<p><?php esc_html_e( 'Wir beantworten sie gerne – unverbindlich und ohne Verkaufsdruck.', 'letsdoo' ); ?></p>
			<div class="cta-banner__actions">
				<?php letsdoo_button( __( 'Kontakt aufnehmen', 'letsdoo' ), home_url( '/kontakt/' ), 'btn--white' ); ?>
			</div>
		</div>
	</section>

</main>

<?php
get_footer();
