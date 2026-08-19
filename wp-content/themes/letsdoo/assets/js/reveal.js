( function () {
	var targets = document.querySelectorAll( '.section__intro, .section__content' );
	if ( ! targets.length ) {
		return;
	}

	var reduceMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	// No animation to run: reveal everything immediately rather than leaving
	// it pinned at opacity 0 by the .js-reveal CSS.
	if ( reduceMotion || ! ( 'IntersectionObserver' in window ) ) {
		targets.forEach( function ( el ) {
			el.classList.add( 'is-visible' );
		} );
		return;
	}

	// rootMargin trims the bottom of the viewport so a section doesn't reveal
	// the instant its top pixel appears — it has to actually be readable.
	var observer = new IntersectionObserver( function ( entries, obs ) {
		entries.forEach( function ( entry ) {
			if ( ! entry.isIntersecting ) {
				return;
			}
			entry.target.classList.add( 'is-visible' );
			obs.unobserve( entry.target );
		} );
	}, { threshold: 0.15, rootMargin: '0px 0px -10% 0px' } );

	targets.forEach( function ( el ) {
		observer.observe( el );
	} );
} )();
