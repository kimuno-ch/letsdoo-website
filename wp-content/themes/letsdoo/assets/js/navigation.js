( function () {
	var header = document.getElementById( 'masthead' );
	var toggle = document.querySelector( '.menu-toggle' );
	if ( ! header || ! toggle ) {
		return;
	}

	toggle.addEventListener( 'click', function () {
		var isOpen = header.classList.toggle( 'is-open' );
		toggle.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
	} );
} )();
