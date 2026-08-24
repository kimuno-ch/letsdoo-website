<?php
/**
 * Company-wide details (address, email, phone) used by the footer and the
 * Kontakt page. Social links moved out to their own ACF Options Page
 * (group_ld_social_media, inc/acf-fields.php) — this file used to also hold
 * linkedin_url/instagram_url, but that only fit one fixed pair of networks.
 *
 * Plain Settings API, written when this site ran ACF free and an Options Page
 * was out of reach. PRO is installed now, so acf_add_options_page() would
 * replace this whole file — but the values live in the letsdoo_firmenangaben
 * option rather than ACF's own storage, so the swap needs a data migration and
 * a rewrite of letsdoo_company_field(). Until then this stays.
 */

define( 'LETSDOO_SETTINGS_OPTION', 'letsdoo_firmenangaben' );

function letsdoo_settings_fields() {
	return array(
		'company_name'    => array( 'label' => 'Firmenname', 'type' => 'text', 'default' => "Let's Doo GmbH" ),
		'company_address' => array( 'label' => 'Adresse', 'type' => 'textarea', 'default' => "Schiltmatthalde 1\n6048 Horw" ),
		'company_email'   => array( 'label' => 'E-Mail', 'type' => 'email', 'default' => 'contact@letsdoo.it' ),
		'company_phone'   => array( 'label' => 'Telefon', 'type' => 'text', 'default' => '+41 43 243 43 39' ),
	);
}

function letsdoo_register_settings_page() {
	add_options_page(
		__( 'Firmenangaben', 'letsdoo' ),
		__( 'Firmenangaben', 'letsdoo' ),
		'manage_options',
		'firmenangaben',
		'letsdoo_render_settings_page'
	);
}
add_action( 'admin_menu', 'letsdoo_register_settings_page' );

function letsdoo_sanitize_settings( $input ) {
	$sanitized = array();
	foreach ( letsdoo_settings_fields() as $key => $field ) {
		$value = isset( $input[ $key ] ) ? wp_unslash( $input[ $key ] ) : '';
		$sanitized[ $key ] = 'textarea' === $field['type'] ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );
	}
	return $sanitized;
}

function letsdoo_register_settings() {
	register_setting( 'letsdoo_firmenangaben_group', LETSDOO_SETTINGS_OPTION, array(
		'sanitize_callback' => 'letsdoo_sanitize_settings',
	) );
}
add_action( 'admin_init', 'letsdoo_register_settings' );

function letsdoo_render_settings_page() {
	$values = get_option( LETSDOO_SETTINGS_OPTION, array() );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Firmenangaben', 'letsdoo' ); ?></h1>
		<p><?php esc_html_e( 'Diese Angaben werden im Footer und auf der Kontaktseite angezeigt.', 'letsdoo' ); ?></p>
		<form method="post" action="options.php">
			<?php settings_fields( 'letsdoo_firmenangaben_group' ); ?>
			<table class="form-table" role="presentation">
				<?php foreach ( letsdoo_settings_fields() as $key => $field ) : ?>
					<tr>
						<th scope="row"><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
						<td>
							<?php $value = isset( $values[ $key ] ) ? $values[ $key ] : ''; ?>
							<?php if ( 'textarea' === $field['type'] ) : ?>
								<textarea class="large-text" rows="3" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( LETSDOO_SETTINGS_OPTION ); ?>[<?php echo esc_attr( $key ); ?>]"><?php echo esc_textarea( $value ); ?></textarea>
							<?php else : ?>
								<input class="regular-text" type="<?php echo esc_attr( $field['type'] ); ?>" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( LETSDOO_SETTINGS_OPTION ); ?>[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $value ); ?>">
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
