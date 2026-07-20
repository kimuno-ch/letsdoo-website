( function () {
	var nav = document.getElementById( 'site-navigation' );
	if ( ! nav ) {
		return;
	}

	var toggle = nav.querySelector( '.menu-toggle' );
	if ( ! toggle ) {
		return;
	}

	toggle.addEventListener( 'click', function () {
		var isOpen = nav.classList.toggle( 'is-open' );
		toggle.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
	} );
} )();
