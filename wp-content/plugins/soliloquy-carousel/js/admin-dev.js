/**
 * jQuery to power the carousel preview.
 * 
 * @package   Tgmsp-Carousel
 * @version   1.0.0
 * @author    Thomas Griffin <thomas@thomasgriffinmedia.com>
 * @copyright Copyright (c) 2012, Thomas Griffin
 */
jQuery(document).ready(function($) {

	/** Append information to the global settings ID var */
	if ( 'undefined' == typeof soliloquyPreviewSettingsID || false == soliloquyPreviewSettingsID )
		soliloquyPreviewSettingsID = '#soliloquy_carousel_settings .form-table td,';
	else
		soliloquyPreviewSettingsID += '#soliloquy_carousel_settings .form-table td,';
		
	/** Refresh the carousel settings input fields */
	$('#soliloquy-carousel-reset').on('click.soliloquyCarouselReset', function(e){
		e.preventDefault();
		$('#soliloquy-carousel-width, #soliloquy-carousel-margin, #soliloquy-carousel-minimum, #soliloquy-carousel-maximum, #soliloquy-carousel-move').val('');
	});

});