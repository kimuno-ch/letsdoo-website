<?php
/**
 * Small helpers shared across the page templates: image fallbacks,
 * button markup, and ordered CPT queries.
 */

/**
 * Resolve an ACF image field (array|ID) to a URL, falling back to a
 * bundled placeholder while real photography hasn't been uploaded yet.
 */
function letsdoo_image_url( $image, $fallback_file, $size = 'large' ) {
	if ( is_array( $image ) && ! empty( $image['sizes'][ $size ] ) ) {
		return $image['sizes'][ $size ];
	}
	if ( is_array( $image ) && ! empty( $image['url'] ) ) {
		return $image['url'];
	}
	if ( is_numeric( $image ) ) {
		$src = wp_get_attachment_image_src( $image, $size );
		if ( $src ) {
			return $src[0];
		}
	}
	return get_theme_file_uri( '/assets/images/' . $fallback_file );
}

function letsdoo_image_alt( $image, $fallback_alt = '' ) {
	if ( is_array( $image ) && ! empty( $image['alt'] ) ) {
		return $image['alt'];
	}
	return $fallback_alt;
}

/**
 * Gradient pill button used throughout the design. Buttons that point at the
 * Kontakt page open the site-wide Kontakt modal (template-parts/kontakt-modal.php)
 * instead of navigating there, so the href is kept as a working fallback and
 * as the target for anyone who opens the link in a new tab.
 */
function letsdoo_button( $label, $link, $classes = '' ) {
	if ( empty( $label ) ) {
		return;
	}
	$link = $link ? $link : '#kontakt';

	$opens_modal = ! letsdoo_is_kontakt_page() && untrailingslashit( $link ) === untrailingslashit( letsdoo_kontakt_page_url() );

	printf(
		'<a class="btn %1$s" href="%2$s"%3$s>%4$s</a>',
		esc_attr( $classes ),
		esc_url( $link ),
		$opens_modal ? ' data-kontakt-modal="open"' : '',
		esc_html( $label )
	);
}

/**
 * The optional promo tile on the right of a primary-nav dropdown (see
 * inc/nav-walker.php) — badge, heading, text and a fixed "Kontakt aufnehmen"
 * button that opens the Kontakt modal, same brand gradient as .cta-banner
 * (19-angebote-cta.css) and .plattform-card--cta (28-plattform.css). Fields
 * live on the menu item itself (inc/acf-fields.php, "nav_menu_item"
 * location). The button isn't editable per item, same call the Plattform
 * block's own CTA card made: one fixed action, not a link an editor could
 * point somewhere odd.
 *
 * Presence-triggered like the rest of the theme's optional content — no
 * heading, no tile, so a plain dropdown with no promo fields filled in stays
 * exactly that.
 *
 * @param WP_Post $item The depth-0 nav menu item object.
 */
function letsdoo_nav_promo_card( $item ) {
	$heading = get_field( 'promo_heading', $item );

	if ( ! $heading ) {
		return '';
	}

	$badge = get_field( 'promo_badge', $item );
	$text  = get_field( 'promo_text', $item );
	$image = get_field( 'promo_image', $item );
	/* A custom property, not background-image directly: 03-header.css layers
	   a readability scrim UNDER this in the same background-image list, and
	   an inline style="background-image:…" would win outright over that
	   whole stylesheet rule (inline beats any non-!important cascade layer),
	   leaving the photo with no scrim under the title/text/button. */
	$style = $image ? ' style="--promo-photo:url(\'' . esc_url( letsdoo_image_url( $image, '', 'medium' ) ) . '\')"' : '';

	ob_start();
	?>
	<div class="mega-menu__promo<?php echo $image ? ' mega-menu__promo--photo' : ''; ?>"<?php echo $style; ?>>
		<?php if ( $badge ) : ?>
			<span class="mega-menu__promo-badge"><?php echo esc_html( $badge ); ?></span>
		<?php endif; ?>
		<h3 class="mega-menu__promo-heading"><?php echo esc_html( $heading ); ?></h3>
		<?php if ( $text ) : ?>
			<p class="mega-menu__promo-text"><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>
		<?php letsdoo_button( __( 'Kontakt aufnehmen', 'letsdoo' ), letsdoo_page_url_by_template( 'page-templates/template-contact.php', '/kontakt/' ), 'btn--sm' ); ?>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * ID of the page using the Kontakt template, cached per request. Used to
 * recognise "Kontakt aufnehmen" links (so they can open the modal instead of
 * navigating) and to pull the Kontakt page's form shortcode into the modal.
 */
function letsdoo_kontakt_page_id() {
	static $page_id = null;

	if ( null === $page_id ) {
		$pages   = get_posts( array(
			'post_type'      => 'page',
			'posts_per_page' => 1,
			'post_status'    => 'publish',
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'page-templates/template-contact.php',
			'fields'         => 'ids',
		) );
		$page_id = $pages ? $pages[0] : 0;
	}

	return $page_id;
}

function letsdoo_kontakt_page_url() {
	$page_id = letsdoo_kontakt_page_id();
	return $page_id ? get_permalink( $page_id ) : home_url( '/kontakt/' );
}

function letsdoo_is_kontakt_page() {
	return is_page_template( 'page-templates/template-contact.php' );
}

/**
 * Resolves a letsdoo_post_selector_field() (inc/acf-fields.php) value to the
 * posts it should render: the chosen ones, in the order they were chosen, or
 * every post of that type in menu_order when nothing was picked — the
 * behaviour every grid on the site had before that field existed, so an
 * empty selector is not a broken page.
 *
 * @param string        $post_type    The CPT to query when nothing is selected.
 * @param int[]|int|null $selected_ids A post_selector field's value (already IDs).
 */
function letsdoo_selected_or_all( $post_type, $selected_ids ) {
	if ( ! $selected_ids ) {
		return get_posts( array(
			'post_type'      => $post_type,
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		) );
	}

	return array_values( array_filter( array_map( 'get_post', (array) $selected_ids ) ) );
}

function letsdoo_get_leistungen( $selected_ids = null ) {
	return letsdoo_selected_or_all( 'leistung', $selected_ids );
}

function letsdoo_get_vorgehen_schritte() {
	return get_posts( array(
		'post_type'      => 'vorgehen_schritt',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	) );
}

function letsdoo_get_team() {
	return get_posts( array(
		'post_type'      => 'team_mitglied',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	) );
}

function letsdoo_get_referenzen( $selected_ids = null ) {
	return letsdoo_selected_or_all( 'referenz', $selected_ids );
}

/**
 * The "Warum Odoo?" platform diagram (assets/images/odoo-map.svg), inlined
 * rather than left as a plain <img src> like every other image on the site.
 *
 * The Kunde chip on the diagram — originally a static person icon — cycles
 * through real Referenzen logos when any are entered, falling back to that
 * original icon when there aren't any yet. An externally referenced .svg
 * file can't reach into the database to build that, and even a same-origin
 * fetch wouldn't help: an <img>-embedded SVG runs under stricter rules than
 * one that's actually part of the page (scripting disabled, and in at least
 * this browser's case CSS `d`-property animation silently did nothing — see
 * the two galleries of the git history around the hub-ripple work for the
 * before/after). Inlining sidesteps all of that, as a side effect of solving
 * the actual problem, which is that the chip needs live data.
 *
 * The file is used as-is otherwise — this only ever touches the single
 * marked region (see the "omap:kunde-chip" comments in the SVG itself), via
 * letsdoo_odoo_map_inject_logo_cycle(), so the rest of the diagram (and its
 * own animations) is exactly the hand-authored file on disk.
 */
function letsdoo_render_odoo_map() {
	$svg = file_get_contents( get_theme_file_path( '/assets/images/odoo-map.svg' ) );

	if ( false === $svg ) {
		return '';
	}

	$logos = array();

	foreach ( letsdoo_get_referenzen() as $referenz ) {
		$thumb_id = get_post_thumbnail_id( $referenz );

		if ( ! $thumb_id ) {
			continue; // No real logo to show -- cycling a placeholder in would look like a bug, not a feature.
		}

		$logos[] = array(
			'src' => letsdoo_image_url( $thumb_id, 'placeholder-logo.svg' ),
			'alt' => get_the_title( $referenz ),
		);
	}

	return $logos ? letsdoo_odoo_map_inject_logo_cycle( $svg, $logos ) : $svg;
}

/**
 * Swaps the Kunde chip's marked region for a stack of client-logo <image>
 * elements, cross-fading through them via CSS. Only called with at least one
 * logo — letsdoo_render_odoo_map() leaves the file's own fallback content
 * (person icon + "Kunde") in place otherwise.
 *
 * Same box for every logo (290×86, centred where the chip used to be —
 * bigger than the chip itself since there's no card padding to leave room
 * for any more) regardless of each one's own aspect ratio —
 * preserveAspectRatio="xMidYMid meet" (the SVG default, stated here for
 * clarity) scales each to fit inside it without distorting or cropping, the
 * same guarantee object-fit: contain gives a bitmap <img>.
 */
function letsdoo_odoo_map_inject_logo_cycle( $svg, $logos ) {
	$count = count( $logos );

	if ( 1 === $count ) {
		// Nothing to cycle with -- animating a single logo would just flicker
		// it off once per loop for no reason.
		$markup = sprintf(
			'<image href="%s" x="305" y="79" width="290" height="86" preserveAspectRatio="xMidYMid meet"><title>%s</title></image>',
			esc_url( $logos[0]['src'] ),
			esc_html( $logos[0]['alt'] )
		);
	} else {
		/*
		 * Every logo shares one keyframe animation and differs only by
		 * animation-delay — the same staggering technique the rest of the
		 * map already uses for its dashed connectors and dots (see
		 * odoo-map.svg). A negative delay of -(i × its share of the shared
		 * duration) starts logo i that far into the cycle, so each takes its
		 * turn in the visible window rather than all showing at once.
		 */
		$duration    = $count * 2.6; // seconds for the whole cycle to visit every logo once
		$slice       = 100 / $count; // % of the cycle each logo gets
		$holdUntil   = round( $slice * 0.72, 2 ); // still fully visible up to here
		$hiddenAfter = round( $slice, 2 ); // fully faded out by here

		$markup = sprintf(
			'<style>
				.omap-kunde-logo { opacity: 0; animation: omap-kunde-cycle %1$ss ease-in-out infinite; }
				@media (prefers-reduced-motion: reduce) {
					.omap-kunde-logo { animation: none; }
					.omap-kunde-logo--first { opacity: 1; }
				}
				@keyframes omap-kunde-cycle {
					0%%    { opacity: 1; }
					%2$s%% { opacity: 1; }
					%3$s%% { opacity: 0; }
					100%%  { opacity: 0; }
				}
			</style>',
			$duration,
			$holdUntil,
			$hiddenAfter
		);

		foreach ( $logos as $i => $logo ) {
			$classes = 'omap-kunde-logo' . ( 0 === $i ? ' omap-kunde-logo--first' : '' );

			$markup .= sprintf(
				'<image class="%1$s" style="animation-delay:-%2$ss" href="%3$s" x="305" y="79" width="290" height="86" preserveAspectRatio="xMidYMid meet"><title>%4$s</title></image>',
				esc_attr( $classes ),
				round( $i * ( $duration / $count ), 2 ),
				esc_url( $logo['src'] ),
				esc_html( $logo['alt'] )
			);
		}
	}

	return preg_replace(
		'/<!-- omap:kunde-chip:start.*?-->.*?<!-- omap:kunde-chip:end -->/s',
		$markup,
		$svg,
		1
	);
}

function letsdoo_get_pakete() {
	return get_posts( array(
		'post_type'      => 'angebot_paket',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	) );
}

/**
 * Numbered pagination for the blog index and archives, marked up with the
 * theme's pill styling instead of the default prev/next links.
 */
function letsdoo_pagination() {
	$links = paginate_links( array(
		'type'      => 'array',
		'mid_size'  => 1,
		'prev_text' => __( 'Zurück', 'letsdoo' ),
		'next_text' => __( 'Weiter', 'letsdoo' ),
	) );

	if ( empty( $links ) ) {
		return;
	}

	printf(
		'<nav class="pagination" aria-label="%s">%s</nav>',
		esc_attr__( 'Beitragsnavigation', 'letsdoo' ),
		implode( '', $links )
	);
}

/**
 * Rough reading time in minutes, shown next to the date on blog posts.
 * 200 wpm is the usual rule of thumb; always at least 1 so a short post
 * doesn't read "0 Min.".
 */
function letsdoo_reading_time( $post = null ) {
	$words = str_word_count( wp_strip_all_tags( strip_shortcodes( get_the_content( null, false, $post ) ) ) );
	return max( 1, (int) round( $words / 200 ) );
}

/**
 * Permalink of the page using a given page template, so cross-page links
 * (e.g. a Referenz detail page pointing back at the Referenzen list on
 * "Über uns") survive the client renaming or re-slugging that page.
 */
function letsdoo_page_url_by_template( $template, $fallback_path = '/' ) {
	$pages = get_posts( array(
		'post_type'      => 'page',
		'posts_per_page' => 1,
		'post_status'    => 'publish',
		'meta_key'       => '_wp_page_template',
		'meta_value'     => $template,
	) );

	return $pages ? get_permalink( $pages[0] ) : home_url( $fallback_path );
}

/**
 * Splits a textarea field into trimmed, non-empty lines — used for the
 * Paket "Merkmale" field and the Standort "Lokaler Inhalt" paragraphs.
 *
 * Dates from the free ACF build, where repeater sub-fields were unavailable.
 * PRO is installed now, so these textareas are a pending migration; they still
 * hold live content, and converting one moves its meta keys.
 */
function letsdoo_lines( $text ) {
	if ( ! $text ) {
		return array();
	}
	$lines = preg_split( '/\r\n|\r|\n/', $text );
	$lines = array_map( 'trim', $lines );
	return array_values( array_filter( $lines, function ( $line ) {
		return '' !== $line;
	} ) );
}

/**
 * Parses a Paket "Merkmale" textarea into a list of
 * [ 'text' => ..., 'enthalten' => bool ], where a leading "-" marks a
 * feature as not included (rendered greyed out / crossed off).
 */
function letsdoo_merkmale_liste( $text ) {
	$items = array();
	foreach ( letsdoo_lines( $text ) as $line ) {
		if ( '-' === substr( $line, 0, 1 ) ) {
			$items[] = array( 'text' => trim( substr( $line, 1 ) ), 'enthalten' => false );
		} else {
			$items[] = array( 'text' => $line, 'enthalten' => true );
		}
	}
	return $items;
}

/*
 * letsdoo_faq_liste() used to live here — it split the Standort "FAQ" textarea
 * on "|" into question/answer pairs. The FAQ is a block with a proper repeater
 * now (blocks/faq/), so nothing calls it. The one-off conversion carries its
 * own copy of the parser: tools/migrate-standorte-to-blocks.php.
 */

/**
 * Small named icon set for spots that aren't backed by an ACF Font Awesome
 * field (fixed UI chrome, and the PHP-level fallback content that stands in
 * before a client has entered anything). ACF font-awesome fields render
 * their own markup directly via get_field() and don't go through this.
 */
function letsdoo_icon( $key ) {
	/*
	 * Font Awesome Free's Regular (outline) style only covers a narrow
	 * ~270-icon subset — house/gear/headset/graduation-cap/check/
	 * magnifying-glass/rocket/location-dot/phone/xmark aren't in it (Pro
	 * has full regular coverage; Free doesn't). Where the literal icon
	 * has no regular drawing, this swaps in the closest available regular
	 * icon instead of falling back to solid, so the whole set stays one
	 * consistent weight.
	 */
	$icons = array(
		'architecture' => 'fa-regular fa-house',
		'settings'     => 'fa-regular fa-pen-to-square',
		'support'      => 'fa-regular fa-headphones-simple',
		'training'     => 'fa-regular fa-id-badge',
		'check'        => 'fa-regular fa-circle-check',
		'search'       => 'fa-regular fa-eye',
		'rocket'       => 'fa-regular fa-lightbulb',
		'pin'          => 'fa-regular fa-map',
		'mail'         => 'fa-solid fa-envelope',
		'phone'        => 'fa-solid fa-phone',
		'cross'        => 'fa-regular fa-circle-xmark',
		'linkedin'     => 'fa-brands fa-linkedin-in',
		'instagram'    => 'fa-brands fa-instagram',
	);
	$class = isset( $icons[ $key ] ) ? $icons[ $key ] : $icons['check'];
	return '<i class="' . esc_attr( $class ) . '" aria-hidden="true"></i>';
}

/**
 * Odoo's own official app icons (assets/images/odoo-icons/, vendored from
 * Odoo's icon pack), keyed by filename without extension. Used as the
 * `choices` for the Plattform block's per-app icon field and to resolve a
 * chosen icon to a URL at render time.
 */
function letsdoo_odoo_icon_choices() {
	static $choices = null;

	if ( null !== $choices ) {
		return $choices;
	}

	$choices = array();
	$files   = glob( get_theme_file_path( '/assets/images/odoo-icons/*.svg' ) );

	foreach ( $files as $file ) {
		$slug             = basename( $file, '.svg' );
		$label            = ucwords( str_replace( '_', ' ', $slug ) );
		$choices[ $slug ] = $label;
	}

	asort( $choices );

	return $choices;
}

function letsdoo_odoo_icon_url( $slug ) {
	if ( ! $slug ) {
		return '';
	}

	return get_theme_file_uri( '/assets/images/odoo-icons/' . $slug . '.svg' );
}

/**
 * Company contact details from the Firmenangaben settings page
 * (inc/settings-page.php), with sane fallbacks before the client fills
 * them in via Einstellungen → Firmenangaben.
 */
function letsdoo_company_field( $field, $fallback = '' ) {
	$values = get_option( LETSDOO_SETTINGS_OPTION, array() );
	if ( ! empty( $values[ $field ] ) ) {
		return $values[ $field ];
	}
	if ( $fallback ) {
		return $fallback;
	}
	$fields = letsdoo_settings_fields();
	return isset( $fields[ $field ]['default'] ) ? $fields[ $field ]['default'] : '';
}
