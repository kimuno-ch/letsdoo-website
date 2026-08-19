( function () {
	var modal = document.getElementById( 'kontakt-modal' );
	if ( ! modal ) {
		return;
	}

	var openTriggers  = document.querySelectorAll( '[data-kontakt-modal="open"]' );
	var closeTriggers = modal.querySelectorAll( '[data-kontakt-modal="close"]' );
	var lastFocused   = null;

	function onKeydown( event ) {
		if ( 'Escape' === event.key ) {
			closeModal();
		}
	}

	function openModal( event ) {
		/*
		 * The header's email icon is a mailto: link wearing this same trigger
		 * — clicking it should still hand off to the user's mail client, not
		 * just pop the form instead, so it's the one case left to navigate
		 * normally. mailto:/tel: never unload the page, so nothing about the
		 * modal opening below is at odds with letting that through.
		 */
		var href = event.currentTarget.getAttribute( 'href' ) || '';
		if ( ! /^(mailto|tel):/i.test( href ) ) {
			event.preventDefault();
		}
		lastFocused = document.activeElement;

		modal.hidden = false;
		document.body.classList.add( 'kontakt-modal-open' );
		document.addEventListener( 'keydown', onKeydown );

		var closeButton = modal.querySelector( '.kontakt-modal__close' );
		if ( closeButton ) {
			closeButton.focus();
		}
	}

	function closeModal() {
		modal.hidden = true;
		document.body.classList.remove( 'kontakt-modal-open' );
		document.removeEventListener( 'keydown', onKeydown );

		if ( lastFocused && 'function' === typeof lastFocused.focus ) {
			lastFocused.focus();
		}
	}

	openTriggers.forEach( function ( trigger ) {
		trigger.addEventListener( 'click', openModal );
	} );

	closeTriggers.forEach( function ( trigger ) {
		trigger.addEventListener( 'click', closeModal );
	} );
} )();
