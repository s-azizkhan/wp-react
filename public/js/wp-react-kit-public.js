(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	/**
	 * Load imgs from the build folder with lazy loading
	 */
	document.addEventListener('DOMContentLoaded', function() {
		var lazyImages = document.querySelectorAll('img[src]');
		lazyImages.forEach(function(img) {
			const imgPath = img.getAttribute('src');
			let imgSrc = imgPath;
			if(imgSrc.startsWith('./')){
				imgSrc = imgSrc.substr(1);
				imgSrc = `${wpReactKit.pluginUrl}resources/js/dist/build${imgSrc}`;
				img.setAttribute('src', imgSrc);
			}
			img.onload = function() {
				img.setAttribute('data-src', imgPath);
			};
		});
	});

})( jQuery );
