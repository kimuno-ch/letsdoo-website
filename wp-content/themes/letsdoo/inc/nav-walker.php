<?php
/**
 * Mega-menu walker for the primary navigation (header.php).
 *
 * Maps native WP menu nesting straight onto a grouped dropdown — no separate
 * menu-builder UI, just Appearance → Menüs with items dragged into place:
 *
 *   depth 0  the trigger ("Leistungen", …) — a plain link if it has no
 *            children, a hover/focus dropdown if it does.
 *   depth 1  a column. Left childless it renders as one flat link straight
 *            in the dropdown; given its own children it renders as an
 *            uppercase eyebrow label heading a little list instead — so a
 *            single-level submenu and a full grouped mega-menu come out of
 *            the same markup, the editor just decides by nesting.
 *   depth 2  the links under a depth-1 group's eyebrow.
 *
 * The optional promo tile on the right (badge + heading + text + Kontakt
 * button) is letsdoo_nav_promo_card() (inc/template-helpers.php), reading ACF
 * fields off the depth-0 item itself (inc/acf-fields.php, "nav_menu_item"
 * location) — skipped entirely when no heading is set.
 *
 * depth 3+ falls back to core's own Walker_Nav_Menu output; nothing in this
 * theme's menu needs to nest that far.
 */
class Letsdoo_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * Depth-0 items currently being walked, so end_lvl() — which core only
	 * ever hands a depth, not the parent item — can still look up which
	 * item's promo-card fields to render once its columns are done.
	 *
	 * @var WP_Post[]
	 */
	private $parent_stack = array();

	public function start_lvl( &$output, $depth = 0, $args = null ) {
		if ( 0 === $depth ) {
			$output .= '<div class="mega-menu__columns">';
			return;
		}

		if ( 1 === $depth ) {
			$output .= '<ul class="mega-menu__links">';
			return;
		}

		parent::start_lvl( $output, $depth, $args );
	}

	public function end_lvl( &$output, $depth = 0, $args = null ) {
		if ( 0 === $depth ) {
			$output .= '</div>'; // .mega-menu__columns

			$parent = end( $this->parent_stack );
			if ( $parent ) {
				$output .= letsdoo_nav_promo_card( $parent );
			}
			return;
		}

		if ( 1 === $depth ) {
			$output .= '</ul>';
			return;
		}

		parent::end_lvl( $output, $depth, $args );
	}

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		if ( $depth >= 3 ) {
			parent::start_el( $output, $item, $depth, $args, $id );
			return;
		}

		$has_children = $this->item_has_children( $item );
		$link         = $this->build_link( $item, $depth );

		if ( 0 === $depth ) {
			$this->parent_stack[] = $item;

			$classes = array_filter( array_merge(
				array( 'main-navigation__item' ),
				(array) $item->classes
			) );

			$output .= '<li class="' . esc_attr( implode( ' ', $classes ) ) . '">' . $link;

			if ( $has_children ) {
				$output .= '<button type="button" class="mega-menu__toggle" aria-expanded="false"><span class="screen-reader-text">' . esc_html__( 'Untermenü öffnen', 'letsdoo' ) . '</span></button>';
				$output .= '<div class="mega-menu"><div class="mega-menu__panel">';
			}
			return;
		}

		if ( 1 === $depth ) {
			$output .= '<div class="mega-menu__col">';
			$output .= $has_children
				? '<p class="mega-menu__eyebrow">' . esc_html( $item->title ) . '</p>'
				: $link;
			return;
		}

		// depth === 2
		$output .= '<li class="mega-menu__link-item">' . $link;
	}

	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		if ( $depth >= 3 ) {
			parent::end_el( $output, $item, $depth, $args );
			return;
		}

		if ( 0 === $depth ) {
			if ( $this->item_has_children( $item ) ) {
				$output .= '</div></div>'; // .mega-menu__panel, .mega-menu
			}
			$output .= '</li>';
			array_pop( $this->parent_stack );
			return;
		}

		if ( 1 === $depth ) {
			$output .= '</div>'; // .mega-menu__col
			return;
		}

		// depth === 2
		$output .= '</li>';
	}

	/**
	 * Whether $item has children, read off the "menu-item-has-children" class
	 * WP core already stamps onto every item with a child before the walk
	 * starts ( _wp_menu_item_classes_by_context() ). Deliberately not
	 * $args->has_children — core does set that, but on the single args object
	 * shared across the whole walk, mutated fresh for whatever element is
	 * currently being processed; by the time end_el() runs for a depth-0 item
	 * — after its children, grandchildren and their siblings have all taken
	 * their own turns overwriting it — it no longer reflects that item at
	 * all. The class, baked onto $item->classes once, doesn't have that
	 * problem.
	 */
	private function item_has_children( $item ) {
		return in_array( 'menu-item-has-children', (array) $item->classes, true );
	}

	/**
	 * One <a>, carrying the href/target/rel/title core's own start_el() would
	 * — this walker never needs the rest of what core builds (page-ID
	 * classes, the nav_menu_link_attributes filter chain, …), since every
	 * item here only ever prints as plain text inside its own wrapper.
	 */
	private function build_link( $item, $depth ) {
		$attrs = array( 'href' => ! empty( $item->url ) ? $item->url : '' );

		if ( ! empty( $item->target ) ) {
			$attrs['target'] = $item->target;
			if ( '_blank' === $item->target && empty( $item->xfn ) ) {
				$attrs['rel'] = 'noopener';
			}
		}
		if ( ! empty( $item->xfn ) ) {
			$attrs['rel'] = $item->xfn;
		}
		if ( ! empty( $item->attr_title ) ) {
			$attrs['title'] = $item->attr_title;
		}

		$attrs['class'] = 0 === $depth ? 'nav-link' : 'mega-menu__link';
		if ( in_array( 'current-menu-item', (array) $item->classes, true ) ) {
			$attrs['aria-current'] = 'page';
		}

		$attributes = '';
		foreach ( $attrs as $attr => $value ) {
			if ( '' === $value ) {
				continue;
			}
			$attributes .= ' ' . $attr . '="' . ( 'href' === $attr ? esc_url( $value ) : esc_attr( $value ) ) . '"';
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );

		return '<a' . $attributes . '>' . esc_html( $title ) . '</a>';
	}
}
