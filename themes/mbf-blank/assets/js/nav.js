/**
 * Inline nav dropdowns: <details name="mbf-nav">, so only one is open at a time with no
 * JS. This adds what <details> doesn't do on its own for a dropdown: Esc closes it and
 * returns focus to its summary, and it closes when a click or focus moves elsewhere.
 */
( () => {
	const nav = document.querySelector( '.mbf-nav' );
	const groups = nav ? nav.querySelectorAll( 'details' ) : [];

	if ( ! groups.length ) {
		return;
	}

	const closeAllExcept = ( keep ) => {
		groups.forEach( ( group ) => {
			if ( group !== keep && ! group.contains( keep ) ) {
				group.open = false;
			}
		} );
	};

	nav.addEventListener( 'keydown', ( event ) => {
		const open = nav.querySelector( 'details[open]' );

		if ( 'Escape' !== event.key || ! open ) {
			return;
		}

		open.open = false;
		open.querySelector( 'summary' ).focus();
	} );

	// relatedTarget is null when focus leaves the page entirely; leave it open then.
	nav.addEventListener( 'focusout', ( event ) => {
		if ( event.relatedTarget ) {
			closeAllExcept( event.relatedTarget );
		}
	} );

	document.addEventListener( 'click', ( event ) => {
		if ( ! nav.contains( event.target ) ) {
			closeAllExcept( null );
		}
	} );
} )();
