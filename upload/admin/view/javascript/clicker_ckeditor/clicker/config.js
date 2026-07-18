/**
 * @license Copyright (c) 2003-2021, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';


	// config.skin = 'bootstrapck';

	// config.height = 350;
	// config.resize_enabled = 1;
	// config.resize_dir = 'vertical';

	config.embed_provider = '//ckeditor.iframe.ly/api/oembed?url={url}&callback={callback}'; // for ckeditor 4.7

	config.toolbar = 'full';

	config.toolbar_basic = [
		['Source'],
		['Maximize'],
		['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
		['NumberedList','BulletedList','-','Outdent','Indent'],
		['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
		['SpecialChar'],
		'/',
		['Undo','Redo'],
		['Font','FontSize'],
		['TextColor','BGColor'],
		['Link','Unlink','Anchor'],
		['Image','cl_bs_grid','Table','HorizontalRule']
	];

	// FontAwesome configs
	config.fontawesomePath = 'view/javascript/font-awesome/css/font-awesome.min.css';

	CKEDITOR.on('instanceLoaded', function(evt) {
		ck_clicker_on_ready(evt);
	});

	// Emoji default list url
	config.emoji_emojiListUrl = CKEDITOR.basePath + 'clicker/plugins/emoji/emoji.json';

	config = c_ckeSetConfig(config);

	// console.log(config);
};

var ck_clicker_on_ready_to = {};
var cl_cke;

function ck_clicker_on_ready(evt) {
	if (typeof ck_clicker_on_ready_to[evt.editor.name] != 'undefined') {
		clearTimeout(ck_clicker_on_ready_to[evt.editor.name]);
	}
	ck_clicker_on_ready_to[evt.editor.name] = setTimeout(function(evt) {
		// if (typeof replace_instanceLoaded == 'function') {
			// replace_instanceLoaded(evt);
		// }
		if (/*typeof c_cke != 'undefined' && */typeof cl_cke == 'undefined') {
			cl_cke = new c_cke({
				debug: cke_settings.debug,
				user_token: cke_settings.user_token,
				token: cke_settings.token,
			});
		}

		for (instance in CKEDITOR.instances) {
			// Store editor data to textarea when focus lost
			CKEDITOR.instances[instance].on('change', function(e) {
				for (var idx in CKEDITOR.instances) {
					CKEDITOR.instances[idx].updateElement();
				}
			});

		}

		var toolbar_before = {
			'a.cke_button.cke_button__image': 'a.cke_button__cl_bs_grid',
			'a.cke_button.cke_button__video': 'a.cke_button__cl_bs_grid',
			'a.cke_button.cke_button__html5video': 'a.cke_button__cl_bs_grid',
			'a.cke_button.cke_button__html5audio': 'a.cke_button__cl_bs_grid',
			'a.cke_button.cke_button__youtube': 'a.cke_button__cl_bs_grid',
		};
		var toolbar_after = {};
		var toolbar_hide = {};

		setTimeout(function(evt) {
			for (instance in CKEDITOR.instances) {
						// setTimeout(function(evt) {
				//var toolbar_class = '#cke_' + instance + ' .cke_toolbox ';
				var toolbar_class = '#cke_' + evt.editor.name + ' .cke_toolbox ';
				for (idx in toolbar_before) {
					// console.log(toolbar_class, $(toolbar_class + idx));
					if ($(toolbar_class + toolbar_before[idx]).length) {
						let ckebtn = $(toolbar_class + idx).detach();
						if (ckebtn.length) {
							$(toolbar_class + toolbar_before[idx]).before(ckebtn);
						}
					}
				}
						// }, 50, evt);
			}
		}, 50, evt);
	}, 80, evt);
	// console.log(CKEDITOR);
}