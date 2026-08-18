( function () {
	var grids = document.querySelectorAll( '.zahlen-grid' );
	if ( ! grids.length ) {
		return;
	}

	var reduceMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	/**
	 * "Zahl" fields are free text (ACF, letsdoo_merkmale-style), so a value
	 * can be "23", "09", "500+", "98%" or "1'200" — anything an editor typed.
	 * Split it into the parts that matter for counting: a numeric target to
	 * animate towards, and the prefix/suffix/leading-zero-width/thousands
	 * separator needed to redraw it looking the same as the original at every
	 * frame, not just the last one.
	 */
	function parseZahl( raw ) {
		var match = raw.match( /^(\D*)([\d.,'\s]*\d)(\D*)$/ );
		if ( ! match ) {
			return null;
		}

		var digitsOnly = match[ 2 ].replace( /\D/g, '' );
		var target = parseInt( digitsOnly, 10 );
		if ( isNaN( target ) ) {
			return null;
		}

		var separatorMatch = match[ 2 ].match( /[.,'\s]/ );

		return {
			prefix: match[ 1 ],
			suffix: match[ 3 ],
			target: target,
			padLength: digitsOnly.length,
			separator: separatorMatch ? separatorMatch[ 0 ] : ''
		};
	}

	function formatZahl( value, info ) {
		var str = String( value );
		while ( str.length < info.padLength ) {
			str = '0' + str;
		}
		if ( info.separator ) {
			str = str.replace( /\B(?=(\d{3})+(?!\d))/g, info.separator );
		}
		return info.prefix + str + info.suffix;
	}

	function animate( el, info ) {
		var duration = 1600;
		var start = null;

		function step( timestamp ) {
			if ( null === start ) {
				start = timestamp;
			}
			var progress = Math.min( ( timestamp - start ) / duration, 1 );
			// Ease-out cubic: fast start, gentle settle on the final value.
			var eased = 1 - Math.pow( 1 - progress, 3 );

			el.textContent = formatZahl( Math.round( info.target * eased ), info );

			if ( progress < 1 ) {
				requestAnimationFrame( step );
			}
		}

		requestAnimationFrame( step );
	}

	grids.forEach( function ( grid ) {
		var items = [];

		grid.querySelectorAll( '.zahl-card__wert' ).forEach( function ( el ) {
			var info = parseZahl( el.textContent.trim() );
			if ( ! info ) {
				return;
			}
			items.push( { el: el, info: info } );
			if ( ! reduceMotion ) {
				el.textContent = formatZahl( 0, info );
			}
		} );

		if ( reduceMotion || ! items.length || ! ( 'IntersectionObserver' in window ) ) {
			return;
		}

		// Fires once per grid: when it first scrolls into view, every number
		// in it counts up together (lightly staggered), then the observer
		// disconnects — these are stats, not a repeating decoration.
		var observer = new IntersectionObserver( function ( entries, obs ) {
			entries.forEach( function ( entry ) {
				if ( ! entry.isIntersecting ) {
					return;
				}
				items.forEach( function ( item, index ) {
					setTimeout( function () {
						animate( item.el, item.info );
					}, index * 120 );
				} );
				obs.unobserve( entry.target );
			} );
		}, { threshold: 0.35 } );

		observer.observe( grid );
	} );
} )();
