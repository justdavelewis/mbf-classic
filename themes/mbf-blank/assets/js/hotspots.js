/**
 * Hotspots: adds what the server's markup can't do alone.
 *
 *  - Tabs when there are several scenes: built here, so without JS there's no tablist that
 *    doesn't work, only every scene under its own heading. Arrow keys, Home and End move
 *    between tabs (roving tabindex).
 *  - Escape hides a popup until the pointer or focus moves (1.4.13).
 *  - The marker ↔ list highlight, both ways: hovering or focusing an entry marks its marker;
 *    hovering a marker marks its entry. Clicking a marker follows its entry's link, or marks
 *    the entry and brings it into view when it has none.
 */
( () => {
	const reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' );

	const pair = ( scene ) => {
		const parts = scene.querySelectorAll( '[data-mbf-spot]' );

		const mark = ( spot ) => {
			parts.forEach( ( part ) => part.classList.toggle( 'is-active', part.dataset.mbfSpot === spot ) );
		};

		scene.querySelectorAll( '.mbf-hotspots__item' ).forEach( ( item ) => {
			item.addEventListener( 'pointerenter', () => mark( item.dataset.mbfSpot ) );
			item.addEventListener( 'focusin', () => mark( item.dataset.mbfSpot ) );
			item.addEventListener( 'pointerleave', () => mark( null ) );
			item.addEventListener( 'focusout', () => mark( null ) );
		} );

		/*
		 * Markers pair on hover too (David, 2026-11-10: hovering a spot did nothing, though
		 * hovering its row did). A tap follows the card's link, as on FA: the marker looks
		 * tappable. A spot with no page marks its card and brings it into view instead.
		 */
		scene.querySelectorAll( '.mbf-hotspots__marker' ).forEach( ( marker ) => {
			const spot = marker.dataset.mbfSpot;
			const item = scene.querySelector( `.mbf-hotspots__item[data-mbf-spot="${ spot }"]` );

			marker.addEventListener( 'pointerenter', () => mark( spot ) );
			marker.addEventListener( 'pointerleave', () => mark( null ) );
			marker.addEventListener( 'click', () => {
				const link = item?.querySelector( 'a.mbf-hotspots__link' );

				if ( link ) {
					link.click();
					return;
				}

				mark( spot );
				item?.scrollIntoView( { block: 'nearest', behavior: reduceMotion.matches ? 'auto' : 'smooth' } );
			} );
		} );
	};

	const tabs = ( root, scenes, index ) => {
		const list = document.createElement( 'div' );
		list.className = 'mbf-hotspots__tabs';
		list.setAttribute( 'role', 'tablist' );

		const buttons = scenes.map( ( scene, i ) => {
			const button = document.createElement( 'button' );
			button.type = 'button';
			button.className = 'mbf-hotspots__tab';
			button.id = `mbf-hotspots-${ index }-tab-${ i }`;
			button.setAttribute( 'role', 'tab' );
			button.textContent = scene.dataset.mbfHotspotsScene;

			scene.id = `mbf-hotspots-${ index }-panel-${ i }`;
			scene.setAttribute( 'role', 'tabpanel' );
			scene.setAttribute( 'aria-labelledby', button.id );
			button.setAttribute( 'aria-controls', scene.id );
			scene.querySelector( '.mbf-hotspots__label' )?.setAttribute( 'hidden', '' ); // The tab names it now.

			list.append( button );
			return button;
		} );

		const select = ( chosen, moveFocus = true ) => {
			buttons.forEach( ( button, i ) => {
				const on = i === chosen;
				button.setAttribute( 'aria-selected', String( on ) );
				button.tabIndex = on ? 0 : -1;
				scenes[ i ].hidden = ! on;
			} );

			if ( moveFocus ) {
				buttons[ chosen ].focus();
			}
		};

		list.addEventListener( 'click', ( event ) => {
			const i = buttons.indexOf( event.target.closest( '[role="tab"]' ) );

			if ( i >= 0 ) {
				select( i );
			}
		} );

		list.addEventListener( 'keydown', ( event ) => {
			const current = buttons.indexOf( document.activeElement );
			const next = {
				ArrowRight: current + 1,
				ArrowLeft: current - 1,
				Home: 0,
				End: buttons.length - 1,
			}[ event.key ];

			if ( undefined !== next && current >= 0 ) {
				event.preventDefault();
				select( ( next + buttons.length ) % buttons.length );
			}
		} );

		root.prepend( list );
		select( 0, false );
	};

	// Escape dismisses a popup shown by hover or focus (1.4.13), without moving focus.
	document.addEventListener( 'keydown', ( event ) => {
		if ( 'Escape' === event.key ) {
			document.querySelectorAll( '.mbf-hotspots .is-active' ).forEach( ( part ) => part.classList.remove( 'is-active' ) );
			document.querySelectorAll( '.mbf-hotspots__media' ).forEach( ( media ) => media.classList.add( 'is-dismissed' ) );
		}
	} );

	// A dismissed popup stays away until the pointer or focus moves on.
	document.addEventListener( 'pointermove', () => document.querySelectorAll( '.mbf-hotspots__media.is-dismissed' ).forEach( ( media ) => media.classList.remove( 'is-dismissed' ) ) );
	document.addEventListener( 'focusin', () => document.querySelectorAll( '.mbf-hotspots__media.is-dismissed' ).forEach( ( media ) => media.classList.remove( 'is-dismissed' ) ) );

	document.querySelectorAll( '[data-mbf-hotspots]' ).forEach( ( root, index ) => {
		const scenes = [ ...root.querySelectorAll( '[data-mbf-hotspots-scene]' ) ];

		scenes.forEach( pair );

		// Tabs need a name for every scene. Otherwise they all stay visible.
		if ( scenes.length > 1 && scenes.every( ( scene ) => scene.dataset.mbfHotspotsScene ) ) {
			tabs( root, scenes, index );
		}
	} );
} )();
