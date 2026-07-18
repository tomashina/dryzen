/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

/**
 * @fileOverview Captioned Image plugin by Clicker (https://opencart.click).
 */

( function() {
	var imagecaptionedCmd = {
		canUndo: false, // The undo snapshot will be handled by 'insertElement'.
		exec: function( editor ) {
			editor.insertHtml('<figure class="image"><img alt="" src="view/image/logo.png" style="width: 180px; height: 35px;" /><figcaption>Figure caption</figcaption></figure>');
		}
	};

	var pluginName = 'imagecaptioned';

	// Register a plugin named "imagecaptioned".
	CKEDITOR.plugins.add( pluginName, {
		// jscs:disable maximumLineLength
		lang: 'en', // %REMOVE_LINE_CORE%
		// jscs:enable maximumLineLength
		icons: 'imagecaptioned', // %REMOVE_LINE_CORE%
		hidpi: true, // %REMOVE_LINE_CORE%
		init: function( editor ) {
			if ( editor.blockless )
				return;

			editor.addCommand( pluginName, imagecaptionedCmd );
			editor.ui.addButton && editor.ui.addButton( 'ImageCaptioned', {
				label: editor.lang.imagecaptioned.toolbar,
				command: pluginName,
				toolbar: 'insert,10'
			} );
		}
	} );
} )();
