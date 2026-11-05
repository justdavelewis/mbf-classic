/**
 * Drawer: <dialog id="mbf-drawer">, opened with showModal().
 *
 * Opening and closing are declarative (commandfor/command, closedby="any"). This file
 * only fills the gaps: a fallback where those aren't supported, and closing the
 * drawer when the viewport widens past the nav breakpoint, because a modal left open
 * there would keep the inline nav inert behind it.
 */
( () => {
	const dialog = document.getElementById( 'mbf-drawer' );

	if ( ! dialog || typeof dialog.showModal !== 'function' ) {
		return;
	}

	if ( ! ( 'command' in HTMLButtonElement.prototype ) ) {
		document.querySelectorAll( '[commandfor="mbf-drawer"]' ).forEach( ( button ) => {
			button.addEventListener( 'click', () => {
				if ( 'close' === button.getAttribute( 'command' ) ) {
					dialog.close();
				} else {
					dialog.showModal();
				}
			} );
		} );
	}

	// Light dismiss: a backdrop click targets the dialog itself (the panel fills it).
	if ( ! ( 'closedBy' in HTMLDialogElement.prototype ) ) {
		dialog.addEventListener( 'click', ( event ) => {
			if ( event.target === dialog ) {
				dialog.close();
			}
		} );
	}

	// The nav breakpoint. Keep in step with src/components/site-header.css (its one @media).
	window.matchMedia( '(min-width: 64rem)' ).addEventListener( 'change', ( event ) => {
		if ( event.matches && dialog.open ) {
			dialog.close();
		}
	} );
} )();
