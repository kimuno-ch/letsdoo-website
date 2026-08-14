<?php
/**
 * Plattform block — categorised grid of Odoo apps (Finanzen, Verkauf,
 * Lager, ...), each a card with an icon, a label and a short list of app
 * names. Built for the Odoo page (page-templates/template-odoo.php) but not
 * tied to it, same as the theme's other section blocks.
 *
 * @var array $block
 * @var bool  $is_preview
 */

$heading    = get_field( 'heading' ) ?: __( 'Eine Plattform für jeden Bereich', 'letsdoo' );
$text       = get_field( 'text' );
$kategorien = get_field( 'kategorien' );
$blend      = (bool) get_field( 'bg_blend' );

if ( ! $kategorien ) {
	if ( $is_preview ) {
		echo '<p class="admin-hint">' . esc_html__( 'Plattform: noch keine Kategorien erfasst.', 'letsdoo' ) . '</p>';
	}
	return;
}

letsdoo_block_section_open( $block, 'section--plattform', $blend );
?>
	<div class="section__content">
		<h2><?php echo esc_html( $heading ); ?></h2>

		<?php if ( $text ) : ?>
			<p class="plattform__intro"><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>

		<div class="plattform-grid">
			<?php foreach ( $kategorien as $kategorie ) : ?>
				<?php
				$apps = $kategorie['apps'] ?? array();
				if ( empty( $kategorie['label'] ) || ! $apps ) {
					continue;
				}
				?>
				<div class="plattform-card">
					<h3><?php echo esc_html( $kategorie['label'] ); ?></h3>
					<ul class="plattform-card__apps">
						<?php foreach ( $apps as $app ) : ?>
							<?php
							if ( empty( $app['label'] ) ) {
								continue;
							}
							$icon_url = letsdoo_odoo_icon_url( $app['icon'] ?? '' );
							?>
							<li>
								<?php if ( ! empty( $app['url'] ) ) : ?>
									<a href="<?php echo esc_url( $app['url'] ); ?>" target="_blank" rel="noopener">
								<?php else : ?>
									<span>
								<?php endif; ?>

								<?php if ( $icon_url ) : ?>
									<img src="<?php echo esc_url( $icon_url ); ?>" alt="" loading="lazy">
								<?php endif; ?>
								<span class="plattform-card__app-label"><?php echo esc_html( $app['label'] ); ?></span>

								<?php echo ! empty( $app['url'] ) ? '</a>' : '</span>'; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
