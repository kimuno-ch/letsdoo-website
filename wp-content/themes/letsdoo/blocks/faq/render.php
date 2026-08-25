<?php
/**
 * FAQ block.
 *
 * A proper Frage/Antwort repeater, replacing the textarea that had to be typed
 * as "Frage | Antwort" one per line and parsed by letsdoo_faq_liste(). A line
 * with a missing pipe used to produce a question with no answer and no warning.
 *
 * The FAQPage structured data is NOT emitted here — inc/seo.php reads these
 * blocks back out of post_content and writes one combined script into <head>,
 * so a page carrying two FAQ blocks still produces a single valid FAQPage.
 * letsdoo_faq_block_items() is the shared reader.
 *
 * @var array $block
 * @var bool  $is_preview
 */

$heading = get_field( 'heading' ) ?: __( 'Häufige Fragen', 'letsdoo' );
$blend   = (bool) get_field( 'bg_blend' );
$fragen  = get_field( 'fragen' );

if ( ! $fragen ) {
	if ( $is_preview ) {
		echo '<p class="admin-hint">' . esc_html__( 'FAQ: noch keine Fragen erfasst.', 'letsdoo' ) . '</p>';
	}
	return;
}

letsdoo_block_section_open( $block, 'section--standort-faq', $blend );
?>
	<div class="section__content">
		<h2><?php echo esc_html( $heading ); ?></h2>

		<div class="standort-faq">
			<?php foreach ( $fragen as $item ) : ?>
				<?php if ( empty( $item['frage'] ) ) : ?>
					<?php continue; ?>
				<?php endif; ?>
				<details class="standort-faq__item">
					<summary class="standort-faq__question">
						<span><?php echo esc_html( $item['frage'] ); ?></span>
						<span class="standort-faq__icon"><?php echo letsdoo_icon( 'chevron-down' ); ?></span>
					</summary>
					<?php if ( ! empty( $item['antwort'] ) ) : ?>
						<div class="standort-faq__answer"><?php echo esc_html( $item['antwort'] ); ?></div>
					<?php endif; ?>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>
