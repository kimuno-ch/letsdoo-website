<?php
/**
 * One post teaser card. Shared by the blog index (home.php), the archives
 * (archive.php) and search results (index.php) so the listing looks the
 * same wherever posts are listed.
 */

$letsdoo_kategorien = get_the_category();
?>

<article <?php post_class( 'blog-card' ); ?>>

	<a class="blog-card__image" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<img
			src="<?php echo esc_url( letsdoo_image_url( get_post_thumbnail_id(), 'placeholder-photo.svg', 'large' ) ); ?>"
			alt=""
			loading="lazy"
		>
	</a>

	<div class="blog-card__body">
		<div class="blog-card__meta">
			<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
			<?php if ( $letsdoo_kategorien ) : ?>
				<span class="blog-card__kategorie"><?php echo esc_html( $letsdoo_kategorien[0]->name ); ?></span>
			<?php endif; ?>
		</div>

		<h3 class="blog-card__titel">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>

		<p class="blog-card__text"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>

		<a class="blog-card__more" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true" data-label="<?php echo esc_attr__( 'Weiterlesen', 'letsdoo' ); ?>"><?php esc_html_e( 'Weiterlesen', 'letsdoo' ); ?><span class="blog-card__arrow">→</span></a>
	</div>

</article>
