/**
 * The hotspot placer (editor only). Select a spot, click the scene's image: its Across and
 * Down fields fill in, and markers show every spot on the preview. The numbers stay editable,
 * so a spot can still be nudged by hand and this is never the only way to set one.
 */
( () => {
	const SPOTS = '.acf-field[data-name="spots"]';
	const IMAGE = '.acf-field[data-name="image"] .image-wrap';

	const style = document.createElement( 'style' );
	style.textContent = `
		.mbf-spot-preview { position: relative; display: inline-block; cursor: crosshair; }
		.mbf-spot-preview img { display: block; }
		.mbf-spot-mark { position: absolute; translate: -50% -50%; inline-size: 26px; block-size: 26px; display: grid; place-items: center; border-radius: 50%; background: #1d2327; color: #fff; font: 600 12px/1 sans-serif; box-shadow: 0 0 0 2px #fff; pointer-events: none; }
		.mbf-spot-mark.is-active { background: #d63638; }
		${ SPOTS } .acf-row.mbf-spot-active > .acf-row-handle { background: #2271b1; color: #fff; }
	`;
	document.head.append( style );

	const sceneOf = ( node ) => node.closest( `.acf-field[data-name="scenes"] .acf-row` );
	const spotRows = ( scene ) => [ ...scene.querySelectorAll( `${ SPOTS } .acf-row:not(.acf-clone)` ) ];
	const input = ( row, name ) => row.querySelector( `.acf-field[data-name="${ name }"] input` );

	const draw = ( scene ) => {
		const wrap = scene.querySelector( IMAGE );

		if ( ! wrap ) {
			return;
		}

		wrap.classList.add( 'mbf-spot-preview' );
		wrap.querySelectorAll( '.mbf-spot-mark' ).forEach( ( mark ) => mark.remove() );

		spotRows( scene ).forEach( ( row, i ) => {
			const x = input( row, 'x' )?.value;
			const y = input( row, 'y' )?.value;

			if ( x === '' || y === '' || x === undefined || y === undefined ) {
				return;
			}

			const mark = document.createElement( 'span' );
			mark.className = 'mbf-spot-mark' + ( row.classList.contains( 'mbf-spot-active' ) ? ' is-active' : '' );
			mark.textContent = String( i + 1 );
			mark.style.left = `${ x }%`;
			mark.style.top = `${ y }%`;
			wrap.append( mark );
		} );
	};

	const drawAll = () => document.querySelectorAll( '.acf-field[data-name="scenes"] .acf-row:not(.acf-clone)' ).forEach( ( row ) => {
		if ( row.querySelector( SPOTS ) ) {
			draw( row );
		}
	} );

	const activate = ( scene, row ) => {
		spotRows( scene ).forEach( ( other ) => other.classList.toggle( 'mbf-spot-active', other === row ) );
		draw( scene );
	};

	document.addEventListener( 'pointerdown', ( event ) => {
		const row = event.target.closest( `${ SPOTS } .acf-row` );

		if ( row ) {
			activate( sceneOf( row.parentElement ), row );
		}
	} );

	document.addEventListener( 'focusin', ( event ) => {
		const row = event.target.closest( `${ SPOTS } .acf-row` );

		if ( row ) {
			activate( sceneOf( row.parentElement ), row );
		}
	} );

	document.addEventListener( 'click', ( event ) => {
		const image = event.target.closest( `${ IMAGE } img` );
		const scene = image && sceneOf( image );

		if ( ! scene || ! scene.querySelector( SPOTS ) ) {
			return;
		}

		const rows = spotRows( scene );
		const row = rows.find( ( item ) => item.classList.contains( 'mbf-spot-active' ) ) ?? rows.at( -1 );

		if ( ! row ) {
			return;
		}

		const box = image.getBoundingClientRect();

		[ [ 'x', event.clientX - box.left, box.width ], [ 'y', event.clientY - box.top, box.height ] ].forEach( ( [ name, offset, size ] ) => {
			const field = input( row, name );
			field.value = ( Math.round( ( offset / size ) * 1000 ) / 10 ).toString();
			field.dispatchEvent( new Event( 'change', { bubbles: true } ) );
		} );

		activate( scene, row );
	} );

	document.addEventListener( 'input', ( event ) => {
		if ( event.target.closest( `${ SPOTS }` ) ) {
			drawAll();
		}
	} );

	if ( window.acf ) {
		window.acf.addAction( 'ready', drawAll );
		window.acf.addAction( 'append', drawAll );
		window.acf.addAction( 'remove', () => setTimeout( drawAll ) );
	}
} )();
