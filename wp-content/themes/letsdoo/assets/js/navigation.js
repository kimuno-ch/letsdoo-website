( function () {
	var header = document.getElementById( 'masthead' );
	var toggle = document.querySelector( '.menu-toggle' );
	if ( ! header || ! toggle ) {
		return;
	}

	toggle.addEventListener( 'click', function () {
		var isOpen = header.classList.toggle( 'is-open' );
		toggle.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
		/*
		 * Without this the dropdown (position: absolute, 23-mobile.css)
		 * just overlays the page — nothing stops the page underneath from
		 * scrolling too, so a touch-scroll meant for the menu scrolled the
		 * background behind it instead. body.nav-open (23-mobile.css) locks
		 * that; the menu's own list gets its own bounded, scrollable area
		 * there so long menus/open mega-menu accordions stay reachable
		 * instead of being trapped below the now-locked viewport.
		 */
		document.body.classList.toggle( 'nav-open', isOpen );
	} );

	/*
	 * Below the mobile breakpoint hover doesn't exist, so 23-mobile.css turns
	 * the mega menu into an accordion instead; this is the tap handler for
	 * its .mega-menu__toggle button.
	 */
	document.querySelectorAll( '.mega-menu__toggle' ).forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			var item = btn.closest( '.main-navigation__item' );
			if ( ! item ) {
				return;
			}
			var isOpen = item.classList.toggle( 'is-open' );
			btn.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
		} );
	} );

	/*
	 * Desktop opens/closes a mega menu on hover — .is-hover-open, not plain
	 * CSS :hover (03-header.css). The panel is centred on the whole page
	 * (see .mega-menu's own comment there), not under whichever narrow
	 * trigger opened it, so reaching it can mean a diagonal or otherwise
	 * imperfect mouse path rather than a straight line down. A pure-CSS
	 * :hover has no memory — the instant the cursor is over neither the
	 * trigger nor the (not-yet-reached) panel, it reads as "closed" and the
	 * fade-out starts before the cursor arrives. This keeps the class on for
	 * a short delay after the cursor actually leaves, long enough to reach
	 * the panel from a real trigger position; a fresh mouseenter within that
	 * window (including one on the panel itself, since it's the same
	 * .main-navigation__item ancestor) cancels the pending close outright.
	 */
	document.querySelectorAll( '.mega-menu' ).forEach( function ( menu ) {
		var item = menu.closest( '.main-navigation__item' );
		if ( ! item ) {
			return;
		}

		var closeTimer = null;

		item.addEventListener( 'mouseenter', function () {
			clearTimeout( closeTimer );
			item.classList.add( 'is-hover-open' );
		} );

		item.addEventListener( 'mouseleave', function () {
			clearTimeout( closeTimer );
			closeTimer = setTimeout( function () {
				item.classList.remove( 'is-hover-open' );
			}, 300 );
		} );
	} );
} )();
