/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For complete reference see:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
		{ name: 'styles',      groups : [ 'Font','FontSize' ] },
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'forms' },
		{ name: 'tools' },
		{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'others' },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'about' },
		 { name: 'ckwebspeech'},
		 { name: 'imagepaste'}
		 //{ name: 'colorbutton'}
		 
	];

	// Remove some buttons provided by the standard plugins, which are
	// not needed in the Standard(s) toolbar.
	config.removeButtons = 'Underline,Subscript,Superscript';

	// Set the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';

	// Simplify the dialog windows.
	//config.removeDialogTabs = 'image:advanced;link:advanced';
	config.removePlugins = 'elementspath';
	config.scayt_autoStartup = false;
	config.uiColor = '#EEEEEE';//D5114F , AADC6E
	//config.colorButton_colors = '00923E,F8C100,28166F';
	config.extraPlugins = 'colorbutton';
	
	config.filebrowserBrowseUrl = site_base_url+'assets/ckeditor/kcfinder/browse.php?type=files';
	config.filebrowserImageBrowseUrl = site_base_url+'assets/ckeditor/kcfinder/browse.php?type=images';
	config.filebrowserUploadUrl = site_base_url+'assets/ckeditor/kcfinder/upload.php?type=files';
	config.filebrowserImageUploadUrl = site_base_url+'assets/ckeditor/kcfinder/upload.php?type=images';
        
                //config.fillEmptyBlocks = false;
                //config.fullPage = false;
                config.autoParagraph = false;
};
