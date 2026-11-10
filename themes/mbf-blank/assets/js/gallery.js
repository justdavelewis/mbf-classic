/**
 * Photo gallery (template-parts/gallery.php): changes which photo shows. Every photo is
 * already in the markup, so this adds nothing a reader needs without JS.
 *
 *  - Thumbnails are buttons with aria-pressed: the keyboard and screen-reader path.
 *  - The arrows wrap (prev from the first shows the last) and are pointer-only.
 *  - The counter is a polite live region, so a change is announced. Only its number is
 *    rewritten: replacing the whole text dropped the word "Photo" on Mantech.
 *  - The fade is CSS, off under prefers-reduced-motion.
 */
( () => {
	document.querySelectorAll( '[data-mbf-gallery]' ).forEach( ( root ) => {
		const images  = root.querySelectorAll( '.mbf-gallery__image' );
		const thumbs  = root.querySelectorAll( '[data-mbf-gallery-index]' );
		const current = root.querySelector( '[data-mbf-gallery-current]' );
		const caption = root.querySelector( '[data-mbf-gallery-caption]' );
		let shown     = 0;

		const show = ( next ) => {
			shown = ( next + images.length ) % images.length;

			images.forEach( ( image, i ) => {
				image.classList.toggle( 'is-on', i === shown );
				// Not toggleAttribute: that writes aria-hidden="", which hides nothing.
				if ( i === shown ) {
					image.removeAttribute( 'aria-hidden' );
				} else {
					image.setAttribute( 'aria-hidden', 'true' );
				}
			} );
			thumbs.forEach( ( thumb, i ) => thumb.setAttribute( 'aria-pressed', String( i === shown ) ) );

			if ( current ) {
				current.textContent = String( shown + 1 );
			}

			if ( caption ) {
				caption.textContent = images[ shown ].dataset.caption || '';
				caption.hidden      = '' === caption.textContent;
			}
		};

		thumbs.forEach( ( thumb ) => thumb.addEventListener( 'click', () => show( Number( thumb.dataset.mbfGalleryIndex ) ) ) );
		root.querySelectorAll( '[data-mbf-gallery-step]' ).forEach( ( button ) => {
			button.addEventListener( 'click', () => show( shown + Number( button.dataset.mbfGalleryStep ) ) );
		} );
	} );
} )();
