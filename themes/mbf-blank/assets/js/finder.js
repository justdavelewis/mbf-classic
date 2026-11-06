/**
 * Solution finder: turns the server's list of links into steps.
 *
 *   1. Which option? An option with no recommended page stays a link to the form.
 *   2. Which site type? Skipped when the site has no question 2.
 *   3. The recommendation, and a button to the form carrying ?need=…&site=…, which WS Form
 *      fields read with #query_var("need").
 *
 * Options are real <button>s. Each change moves focus to the new question (never on load,
 * which would scroll the page to the finder), and the step line announces itself.
 */
( () => {
	const element = ( tag, className, text ) => {
		const node = document.createElement( tag );

		if ( className ) {
			node.className = className;
		}

		if ( text ) {
			node.textContent = text;
		}

		return node;
	};

	const format = ( template, ...values ) => values.reduce( ( out, value, i ) => out.replace( `%${ i + 1 }$s`, value ).replace( '%s', value ), template );

	document.querySelectorAll( '[data-mbf-finder]' ).forEach( ( root ) => {
		let config;

		try {
			config = JSON.parse( root.dataset.mbfFinder );
		} catch {
			return; // Leave the links in place.
		}

		const stage = root.querySelector( '[data-mbf-finder-stage]' );
		const stepLine = root.querySelector( '[data-mbf-finder-step]' );

		if ( ! config?.options?.length || ! stage || ! stepLine ) {
			return;
		}

		const hasSites = Boolean( config.question2 && config.sites?.length );
		const total = hasSites ? 3 : 2;

		const question = ( text ) => {
			const heading = element( `h${ config.level }`, 'h4 mbf-finder__question', text );
			heading.tabIndex = -1; // Takes focus when the step changes, without becoming a tab stop.
			return heading;
		};

		const paint = ( step, nodes, moveFocus = true ) => {
			stage.replaceChildren( ...nodes );
			stepLine.textContent = format( config.strings.step, String( step ), String( total ) );

			if ( moveFocus ) {
				( stage.querySelector( '.mbf-finder__question' ) ?? stage.querySelector( 'button, a' ) )?.focus();
			}
		};

		const optionList = ( items, onPick ) => {
			const list = element( 'ul', 'mbf-finder__options' );
			list.setAttribute( 'role', 'list' );

			items.forEach( ( item ) => {
				const row = element( 'li' );
				// A link when there's nothing to ask about ("Not sure yet" goes straight to the form).
				const control = item.page === '' ? element( 'a', 'mbf-finder__option' ) : element( 'button', 'mbf-finder__option' );

				if ( item.page === '' ) {
					control.href = item.href;
				} else {
					control.type = 'button';
					control.addEventListener( 'click', () => onPick( item ) );
				}

				if ( item.icon ) {
					// Built by mbf_icon() on the server from a filtered name.
					control.insertAdjacentHTML( 'beforeend', item.icon );
				}

				control.append( item.label );
				row.append( control );
				list.append( row );
			} );

			return list;
		};

		const textButton = ( label, onClick ) => {
			const button = element( 'button', 'mbf-finder__back', label );
			button.type = 'button';
			button.addEventListener( 'click', onClick );
			return button;
		};

		const showOptions = ( moveFocus = true ) => {
			const nodes = config.showQuestion ? [ question( config.question ) ] : [];
			nodes.push( optionList( config.options, ( option ) => ( hasSites ? showSites( option ) : showResult( option, null ) ) ) );
			paint( 1, nodes, moveFocus );
		};

		const showSites = ( option ) => {
			paint( 2, [
				question( config.question2 ),
				optionList( config.sites, ( site ) => showResult( option, site ) ),
				textButton( config.strings.back, () => showOptions() ),
			] );
		};

		const showResult = ( option, site ) => {
			const nodes = [
				element( 'p', 'mbf-finder__intro', site?.phrase ? format( config.strings.for, site.phrase ) : config.strings.forAny ),
				question( option.title ),
			];

			if ( option.points.length ) {
				const ticks = element( 'ul', 'mbf-ticks' );
				ticks.setAttribute( 'role', 'list' );
				option.points.forEach( ( point ) => ticks.append( element( 'li', 'mbf-ticks__item', point ) ) );
				nodes.push( ticks );
			}

			const actions = element( 'div', 'mbf-buttons' );

			if ( config.target ) {
				const url = new URL( config.target, window.location.href );
				url.searchParams.set( 'need', option.key );

				if ( site ) {
					url.searchParams.set( 'site', site.key );
				}

				const go = element( 'a', 'mbf-button', config.button );
				go.href = url.href;
				actions.append( go );
			}

			// The same page the no-JS link goes to. Filled only if it's the one button.
			const more = element( 'a', config.target ? 'mbf-button mbf-button--secondary' : 'mbf-button', config.strings.readMore );
			more.href = option.page;
			actions.append( more );

			nodes.push( actions, textButton( config.strings.restart, () => showOptions() ) );
			paint( total, nodes );
		};

		showOptions( false );
	} );
} )();
