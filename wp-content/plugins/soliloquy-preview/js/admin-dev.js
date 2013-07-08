/**
 * jQuery to power preview customizations.
 *
 * The object passed to this script file via wp_localize_script is
 * soliloquy_preview. 
 *
 * @package   Tgmsp-Preview
 * @version   1.0.0
 * @author    Thomas Griffin <thomas@thomasgriffinmedia.com>
 * @copyright Copyright (c) 2012, Thomas Griffin
 */
 
/** Define a global var for other scripts to access to append their settings ID selector so the Ajax script will grab the current data when going to generate a preview */
if ( 'undefined' == typeof soliloquyPreviewSettingsID || false == soliloquyPreviewSettingsID )
	soliloquyPreviewSettingsID = '';

jQuery(document).ready(function($) {

	$('#soliloquy_preview_settings').on('click.soliloquyPreview', '#soliloquy-preview', function(e) {
		/** Prevent the default click action */
		e.preventDefault();
		$('.soliloquy-waiting').remove();
		
		/** Set the height of the container to avoid jumping */
		$('#soliloquy_preview_settings').css({ 'height' : $('#soliloquy_preview_settings').height() });
		
		/** Output loading icon and message */
		$(this).after('<span class="soliloquy-waiting"><img class="soliloquy-spinner" src="' + soliloquy.spinner + '" width="16px" height="16px" style="margin: 0 5px; vertical-align: text-bottom;" />' + soliloquy_preview.process + '</span>');
		
		/** Setup the current_settings object */
		var current_settings = {
			action: 'soliloquy_update_preview',
			id: soliloquy_preview.id,
			nonce: soliloquy_preview.nonce
		}
		var table = {};
		soliloquyPreviewSettingsID += '#soliloquy-slider-type,#soliloquy_settings .form-table td,';
		
		/** Start populating the object with current settings */
		$(soliloquyPreviewSettingsID.slice(0, -1)).each(function() {
			var children = $(this).find('*');
			
			$.each(children, function() {
				var field_id 	= $(this).attr('id');
				var field_val 	= $(this).val();
			
				if ( 'checkbox' == $(this).attr('type') || 'radio' == $(this).attr('type') )
					var field_val = $(this).is(':checked') ? 'true' : 'false';
				
				/** Store all data in the current_settings object */
				current_settings[field_id] = field_val;
			});
		});
		
		/** Get the attachment table that holds the metadata */
		$('#soliloquy-area .soliloquy-meta-table').each(function(i) {
			/** Get the current attachment ID that we are looping through */
			table[i] = $(this).attr('id');
		});
		
		/** Loop through each image attachment's metadata table and get values */
		$.each(table, function(i, id){
			var table_item 	= id;
			var attach_id 	= id.split('-');
			attach_id 		= attach_id[3];
			current_settings['soliloquymeta-' + attach_id] = {};
			$('#' + table_item + ' td').each(function() {
				/** Grab all the items within each td element */
				var children = $(this).find('*');
			
				/** Loop through each child element */
				$.each(children, function() {
					var field_class = $(this).attr('class');
					var field_val 	= $(this).val();
				
					if ( 'checkbox' == $(this).attr('type') )
						var field_val = $(this).is(':checked') ? 'true' : 'false';
				
					/** Store all data in the current_settings object */
					current_settings['soliloquymeta-' + attach_id][field_class] = field_val;
				});
			});
		});
		
		/** Now send out all the data via an ajax request */
		var opts = {
			url: soliloquy.ajaxurl,
			type: 'post',
			async: true,
			cache: false,
			data: current_settings,
			dataType: 'json',
			success: function(json) {
				/** Change the loading response message to let users know that the preview is now generating */
				$('.soliloquy-waiting').html('<img class="soliloquy-spinner" src="' + soliloquy.spinner + '" width="16px" height="16px" style="margin: 0 5px; vertical-align: text-bottom;" />' + soliloquy_preview.generate);
				
				/** If there is an error, output the error message and fade out */
				if (typeof json.error !== 'undefined') {
					$('.soliloquy-waiting').html('<span style="font-weight: bold; margin-left: 10px;">' + json.error + '</span>');
					$('#soliloquy-preview-wrap').slideUp('slow', function() {
						$(this).html('');
					});
					$('#soliloquy_preview_settings').css({ 'height' : 'auto' });
					setTimeout(function() {
						$('.soliloquy-waiting').fadeOut();
					}, 3000);
					return;
				}
				
				/** If a user has filtered the slider using tgmsp_pre_load_slider, output the data here and return */
				if (typeof json.pre !== 'undefined') {
					$('.soliloquy-waiting').html('<span style="font-weight: bold; margin-left: 10px;">' + soliloquy_preview.success + '</span>');
					/** Output the slider */
					$('#soliloquy-preview-wrap').show();
					$('#soliloquy-preview-wrap').html(json.pre);
					$('#soliloquy_preview_settings').css({ 'height' : 'auto' });
					setTimeout(function() {
						$('.soliloquy-waiting').fadeOut();
					}, 3000);
					return;
				}
				
				/** If there are any scripts to load and execute, do so here */
				if ( ! $.isEmptyObject(json.scripts) ) {
					$.each(json.scripts, function(i, script){
						$.ajax({
  							url: script,
  							dataType: 'script',
  							cache: false,
 							success: function(){
 								if ( i == (json.scripts.length - 1) ) {
 									/** If a user has filtered and added extra items to output for their addon, apply it here */
									if ( 0 !== json.extra.length )
										json.slider += json.extra;
										
 									/** Output the slider */
									$('#soliloquy-preview-wrap').show();
									$('#soliloquy-preview-wrap').html(json.slider);
				
									/** Let the user know that the preview has been generated successfully and fade out the message */
									$('.soliloquy-waiting').html('<span style="font-weight: bold; margin-left: 10px;">' + soliloquy_preview.success + '</span>');
									$('#soliloquy_preview_settings').css({ 'height' : 'auto' });
									setTimeout(function() {
										$('.soliloquy-waiting').fadeOut();
									}, 3000);
 								}
 							}
						});
					});
				} else {
					/** If a user has filtered and added extra items to output for their addon, apply it here */
					if ( 0 !== json.extra.length )
						json.slider += json.extra;
						
					/** Output the slider */
					$('#soliloquy-preview-wrap').show();
					$('#soliloquy-preview-wrap').html(json.slider);
				
					/** Let the user know that the preview has been generated successfully and fade out the message */
					$('.soliloquy-waiting').html('<span style="font-weight: bold; margin-left: 10px;">' + soliloquy_preview.success + '</span>');
					$('#soliloquy_preview_settings').css({ 'height' : 'auto' });
					setTimeout(function() {
						$('.soliloquy-waiting').fadeOut();
					}, 3000);
				}	
			},
			error: function(xhr, textStatus, e) { 
                $('.soliloquy-waiting').html('<span style="font-weight: bold; margin-left: 10px;">' + soliloquy_preview.error + '</span>');
                $('#soliloquy_preview_settings').css({ 'height' : 'auto' });
				setTimeout(function() {
					$('.soliloquy-waiting').fadeOut();
				}, 3000);
            	return; 
            }
		}
		
		$.ajax(opts);
	});

});