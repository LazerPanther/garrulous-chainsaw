/*global jQuery, document*/

jQuery(document).ready(function () {

	jQuery('.ace-editor').each(function(index, element){
		/*var editor = jQuery(element).attr('data-editor');

		var aceeditor = ace.edit(editor);

		aceeditor.setTheme("ace/theme/"  + jQuery(element).attr('data-theme'));
		aceeditor.getSession().setMode("ace/mode/" + jQuery(element).attr('data-mode'));

		aceeditor.on('change', function(e){
			jQuery('#'+element.id).val(aceeditor.getSession().getValue());
			redux_change(jQuery(element));
		});*/
	});
});
/*
	Field Color (color)
 */

/*global jQuery, document, redux_change */
(function($){
	'use strict';

	$.redux = $.redux || {};

	var tcolour;

	$(document).ready(function(){
		$.redux.color();
	});

	$.redux.color = function(){
		$('.redux-color-init').wpColorPicker({
			change: function(u) {
				redux_change($(this));
				$('#' + u.target.id + '-transparency').removeAttr('checked');
			},
			clear: function() {
				redux_change($(this).parent().find('.redux-color-init'));
			}
		});

		$('.redux-color').on('focus', function() {
			$(this).data('oldcolor', $(this).val());
		});

		$('.redux-color').on('keyup', function() {
			var value = $(this).val();
			var color = redux_color_validate(this);
			var id = '#' + $(this).attr('id');
			if (value === "transparent") {
				$(this).parent().parent().find('.wp-color-result').css('background-color', 'transparent');
				$(id + '-transparency').attr('checked', 'checked');
			} else {
				$(id + '-transparency').removeAttr('checked');
				if (color && color !== $(this).val()) {
					$(this).val(color);
				}
			}
		});

		// Replace and validate field on blur
		$('.redux-color').on('blur', function() {
			var value = $(this).val();
			var id = '#' + $(this).attr('id');
			if (value === "transparent") {
				$(this).parent().parent().find('.wp-color-result').css('background-color', 'transparent');
				$(id + '-transparency').attr('checked', 'checked');
			} else {
				if (redux_color_validate(this) === value) {
					if (value.indexOf("#") !== 0) {
						$(this).val($(this).data('oldcolor'));
					}
				}
				$(id + '-transparency').removeAttr('checked');
			}
		});

		// Store the old valid color on keydown
		$('.redux-color').on('keydown', function() {
			$(this).data('oldkeypress', $(this).val());
		});

		// When transparency checkbox is clicked
		$('.color-transparency').on('click', function() {
			if ($(this).is(":checked")) {
				$('#' + $(this).data('id')).val('transparent');
				$('#' + $(this).data('id')).parent().parent().find('.wp-color-result').css('background-color', 'transparent');
			} else {
				if ($('#' + $(this).data('id')).val() === 'transparent') {
					$('#' + $(this).data('id')).val('');
				}
			}
		});
	};

})(jQuery);

// Name check, converts name to hex
function colourNameToHex(colour) {
	tcolour = colour.replace(/^\s\s*/, '').replace(/\s\s*$/, '').replace("#", "");
	var colours = {
		"aliceblue": "#f0f8ff",
		"antiquewhite": "#faebd7",
		"aqua": "#00ffff",
		"aquamarine": "#7fffd4",
		"azure": "#f0ffff",
		"beige": "#f5f5dc",
		"bisque": "#ffe4c4",
		"black": "#000000",
		"blanchedalmond": "#ffebcd",
		"blue": "#0000ff",
		"blueviolet": "#8a2be2",
		"brown": "#a52a2a",
		"burlywood": "#deb887",
		"cadetblue": "#5f9ea0",
		"chartreuse": "#7fff00",
		"chocolate": "#d2691e",
		"coral": "#ff7f50",
		"cornflowerblue": "#6495ed",
		"cornsilk": "#fff8dc",
		"crimson": "#dc143c",
		"cyan": "#00ffff",
		"darkblue": "#00008b",
		"darkcyan": "#008b8b",
		"darkgoldenrod": "#b8860b",
		"darkgray": "#a9a9a9",
		"darkgreen": "#006400",
		"darkkhaki": "#bdb76b",
		"darkmagenta": "#8b008b",
		"darkolivegreen": "#556b2f",
		"darkorange": "#ff8c00",
		"darkorchid": "#9932cc",
		"darkred": "#8b0000",
		"darksalmon": "#e9967a",
		"darkseagreen": "#8fbc8f",
		"darkslateblue": "#483d8b",
		"darkslategray": "#2f4f4f",
		"darkturquoise": "#00ced1",
		"darkviolet": "#9400d3",
		"deeppink": "#ff1493",
		"deepskyblue": "#00bfff",
		"dimgray": "#696969",
		"dodgerblue": "#1e90ff",
		"firebrick": "#b22222",
		"floralwhite": "#fffaf0",
		"forestgreen": "#228b22",
		"fuchsia": "#ff00ff",
		"gainsboro": "#dcdcdc",
		"ghostwhite": "#f8f8ff",
		"gold": "#ffd700",
		"goldenrod": "#daa520",
		"gray": "#808080",
		"green": "#008000",
		"greenyellow": "#adff2f",
		"honeydew": "#f0fff0",
		"hotpink": "#ff69b4",
		"indianred ": "#cd5c5c",
		"indigo ": "#4b0082",
		"ivory": "#fffff0",
		"khaki": "#f0e68c",
		"lavender": "#e6e6fa",
		"lavenderblush": "#fff0f5",
		"lawngreen": "#7cfc00",
		"lemonchiffon": "#fffacd",
		"lightblue": "#add8e6",
		"lightcoral": "#f08080",
		"lightcyan": "#e0ffff",
		"lightgoldenrodyellow": "#fafad2",
		"lightgrey": "#d3d3d3",
		"lightgreen": "#90ee90",
		"lightpink": "#ffb6c1",
		"lightsalmon": "#ffa07a",
		"lightseagreen": "#20b2aa",
		"lightskyblue": "#87cefa",
		"lightslategray": "#778899",
		"lightsteelblue": "#b0c4de",
		"lightyellow": "#ffffe0",
		"lime": "#00ff00",
		"limegreen": "#32cd32",
		"linen": "#faf0e6",
		"magenta": "#ff00ff",
		"maroon": "#800000",
		"mediumaquamarine": "#66cdaa",
		"mediumblue": "#0000cd",
		"mediumorchid": "#ba55d3",
		"mediumpurple": "#9370d8",
		"mediumseagreen": "#3cb371",
		"mediumslateblue": "#7b68ee",
		"mediumspringgreen": "#00fa9a",
		"mediumturquoise": "#48d1cc",
		"mediumvioletred": "#c71585",
		"midnightblue": "#191970",
		"mintcream": "#f5fffa",
		"mistyrose": "#ffe4e1",
		"moccasin": "#ffe4b5",
		"navajowhite": "#ffdead",
		"navy": "#000080",
		"oldlace": "#fdf5e6",
		"olive": "#808000",
		"olivedrab": "#6b8e23",
		"orange": "#ffa500",
		"orangered": "#ff4500",
		"orchid": "#da70d6",
		"palegoldenrod": "#eee8aa",
		"palegreen": "#98fb98",
		"paleturquoise": "#afeeee",
		"palevioletred": "#d87093",
		"papayawhip": "#ffefd5",
		"peachpuff": "#ffdab9",
		"peru": "#cd853f",
		"pink": "#ffc0cb",
		"plum": "#dda0dd",
		"powderblue": "#b0e0e6",
		"purple": "#800080",
		"red": "#ff0000",
		"redux": "#01a3e3",
		"rosybrown": "#bc8f8f",
		"royalblue": "#4169e1",
		"saddlebrown": "#8b4513",
		"salmon": "#fa8072",
		"sandybrown": "#f4a460",
		"seagreen": "#2e8b57",
		"seashell": "#fff5ee",
		"sienna": "#a0522d",
		"silver": "#c0c0c0",
		"skyblue": "#87ceeb",
		"slateblue": "#6a5acd",
		"slategray": "#708090",
		"snow": "#fffafa",
		"springgreen": "#00ff7f",
		"steelblue": "#4682b4",
		"tan": "#d2b48c",
		"teal": "#008080",
		"thistle": "#d8bfd8",
		"tomato": "#ff6347",
		"turquoise": "#40e0d0",
		"violet": "#ee82ee",
		"wheat": "#f5deb3",
		"white": "#ffffff",
		"whitesmoke": "#f5f5f5",
		"yellow": "#ffff00",
		"yellowgreen": "#9acd32"
	};
	if (colours[tcolour.toLowerCase()] !== 'undefined') {
		return colours[tcolour.toLowerCase()];
	}
	return colour;
}

// Run the validation
function redux_color_validate(field) {
	var value = jQuery(field).val();
	if (colourNameToHex(value) !== value.replace('#', '')) {
		return colourNameToHex(value);
	}
	return value;
}

jQuery(document).ready(function() {

	jQuery(".redux-dimensions-units").select2({
		width: 'resolve',
		triggerChange: true,
		allowClear: true
	});

	jQuery('.redux-dimensions-input').on('change', function() {
		var units = jQuery(this).parents('.redux-field:first').find('.field-units').val();
		if ( jQuery(this).parents('.redux-field:first').find('.redux-dimensions-units').length !== 0 ) {
			units = jQuery(this).parents('.redux-field:first').find('.redux-dimensions-units option:selected').val();
		}
		if( typeof units !== 'undefined' ) {
			jQuery('#'+jQuery(this).attr('rel')).val(jQuery(this).val()+units);
		} else {
			jQuery('#'+jQuery(this).attr('rel')).val(jQuery(this).val());
		}
	});

	jQuery('.redux-dimensions-units').on('change', function() {
		jQuery(this).parents('.redux-field:first').find('.redux-dimensions-input').change();
	});

});/* global redux_change */
(function($){
	"use strict";

	$.redux.group = $.group || {};

	$(document).ready(function () {
		//Group functionality
		$.redux.group();
	});

	$.redux.group = function(){
		$("#redux-groups-accordion")
		.accordion({
			header: "> div > h3",
			collapsible: true,
			active: false,
			heightStyle: "content",
			icons: {
				"header": "ui-icon-plus",
				"activeHeader": "ui-icon-minus"
			}
		})
		.sortable({
			axis: "y",
			handle: "h3",
			stop: function (event, ui) {
				// IE doesn't register the blur when sorting
				// so trigger focusout handlers to remove .ui-state-focus
				ui.item.children("h3").triggerHandler("focusout");
				var inputs = $('input.slide-sort');
				inputs.each(function(idx) {
					$(this).val(idx);
				});
			}
		});

		$('.redux-groups-accordion-group:not(.redux-dummy) input[id^="color-"]').each( function(idx) {
			$(this).wpColorPicker();
		});

		$('.redux-groups-accordion-group input[data-title="true"]').on('keyup',function(event) {
			$(this).closest('.redux-groups-accordion-group').find('.redux-groups-header').text(event.target.value);
			$(this).closest('.redux-groups-accordion-group').find('.slide-title').val(event.target.value);
		});

		$('.redux-groups-remove').live('click', function () {
			redux_change($(this));
			$(this).parent().find('input[type="text"]').val('');
			$(this).parent().find('input[type="hidden"]').val('');
			$(this).parent().parent().slideUp('medium', function () {
				$(this).remove();
			});
		});

		$('.redux-groups-add').click(function () {
			var newSlide = $(this).prev().find('.redux-dummy').clone(true).show();
			var slideCounter = $(this).parent().find('.redux-dummy-slide-count');
			// Count # of slides
			var slideCount = slideCounter.val();
			// Update the slideCounter
			slideCounter.val(parseInt(slideCount)+1 );
			// REMOVE var slideCount1 = slideCount*1 + 1;

			$(newSlide).find('input[id^="color-"]').each( function(idx) {
				$(this).wpColorPicker(); // mas: apply wp color picker here
			});

			$(this).prev().append(newSlide);

			// Remove dummy classes from newSlide
			$(newSlide).removeClass("redux-dummy");
			$(newSlide).find('input[type="text"], input[type="hidden"], textarea , select').each(function(){
				var attr_name = $(this).data('name');
				var attr_id = $(this).attr('id');
				var def_val = $(this).attr('value');
				// For some browsers, `attr` is undefined; for others,
				// `attr` is false.  Check for both.
				if (typeof attr_id !== 'undefined' && attr_id !== false) {
					$(this).attr("id", $(this).attr("id").replace("@", slideCount) );
				}
				if (typeof attr_name !== 'undefined' && attr_name !== false) {
					$(this).attr("name", $(this).data("name").replace("@", slideCount) );
					$(this).removeAttr("data-name"); // mas
				}
				if ('undefined' !== def_val) {
					$(this).removeAttr('value');
					$(this).val(def_val); // mas
				}

				if($(this).prop("tagName") == 'SELECT') {
					console.log('fuck2');
					//we clean select2 first
					$(newSlide).find('.select2-container').remove();
					$(newSlide).find('select').removeClass('select2-offscreen');
					$.redux.select(); // mas: attach select2 to a new group
				}

				if ($(this).hasClass('slide-sort')){
					$(this).val(slideCount);
				}
			});
		});
	};
})(jQuery);
/* global confirm, redux, redux_change */

jQuery(document).ready(function() {

	// On label click, change the input and class
	jQuery('.redux-image-select label .cws_img_select_wrap, .redux-image-select label .cws_img_select_wrap').click(function(e) {
		var id = jQuery(this).closest('label').attr('for');
		jQuery(this).parents("fieldset:first").find('.redux-image-select-selected').removeClass('redux-image-select-selected');
		jQuery(this).closest('label').find('input[type="radio"]').prop('checked');
		if (jQuery(this).closest('label').hasClass('redux-image-select-preset-' + id)) { // If they clicked on a preset, import!
			e.preventDefault();
			var presets = jQuery(this).closest('label').find('input');
			var data = presets.data('presets');
			if (presets !== undefined && presets !== null) {
				var answer = confirm(redux.args.preset_confirm);
				if (answer) {
					jQuery('label[for="' + id + '"]').addClass('redux-image-select-selected').find("input[type='radio']").attr("checked", true);
					window.onbeforeunload = null;
					jQuery('#import-code-value').val(JSON.stringify(data));
					jQuery('#redux-import').click();
				}
			} else {
			}
			return false;
		} else {
			redux_change(jQuery(this).closest('label').find('input[type="radio"]'));
			jQuery('label[for="' + id + '"]').addClass('redux-image-select-selected').find("input[type='radio']").attr("checked", true);
		}
	});

	// Used to display a full image preview of a tile/pattern
	/*jQuery('.tiles').qtip({
		content: {
			text: function(event, api) {
				return "<img src='" + jQuery(this).attr('rel') + "' style='max-width:150px;' alt='' />";
			},
		},
		style: 'qtip-tipsy',
		position: {
			my: 'top center', // Position my top left...
			at: 'bottom center', // at the bottom right of...
		}
	});*/
});
(function($) {
	"use strict";

function handleFileSelect(e) {
	var files = e.target.files; // FileList object
	var reader = new FileReader();
	reader.onload = function(event) {
		var content = event.target.result;
		document.getElementById('import-code-value').value = content;
		document.getElementById('redux-import-code-button').click();
	};
	reader.readAsText(files[0]);
}

document.getElementById('reduxbackupjson').addEventListener('change', handleFileSelect, false);

	$(document).ready(function() {
		$('#redux-import').click(function(e) {
			if ($('#import-code-value').val() === "" && $('#import-link-value').val() === "") {
				e.preventDefault();
				return false;
			}
		});

		$('#redux-import-code-button').click(function() {
			if ($('#redux-import-link-wrapper').is(':visible')) {
				$('#redux-import-link-wrapper').hide();
				$('#import-link-value').val('');
			}
			$('#redux-import-code-wrapper').fadeIn('fast');
		});

		$('#redux-import-file-button').click(function() {
			$("#reduxbackupjson").trigger('click');
		});

		$('#redux-import-link-button').click(function() {
			if ($('#redux-import-code-wrapper').is(':visible')) {
				$('#redux-import-code-wrapper').hide();
				$('#import-code-value').val('');
			}
			$('#redux-import-link-wrapper').fadeIn('fast');
		});

		$('#redux-export-code-copy').click(function() {
			if ($('#redux-export-link-value').is(':visible')) {
				$('#redux-export-link-value').hide();
			}
			$('#redux-export-code').fadeIn('fast');
		});

		$('#redux-export-link').click(function() {
			if ($('#redux-export-code').is(':visible')) {
				$('#redux-export-code').hide();
			}
			$('#redux-export-link-value').fadeIn('fast');
		});

	});
})(jQuery);
/* global redux_change, wp */


// Add a file via the wp.media function
function redux_add_file(event, selector) {

	event.preventDefault();

	var frame;
	var jQueryel = jQuery(this);



	// If the media frame already exists, reopen it.
	if ( frame ) {
		frame.open();
		return;
	}

	// Create the media frame.
	frame = wp.media({
		multiple: false,
		library: {
			//type: 'image' //Only allow images
		},
		// Set the title of the modal.
		title: jQueryel.data('choose'),

		// Customize the submit button.
		button: {
			// Set the text of the button.
			text: jQueryel.data('update')
			// Tell the button not to close the modal, since we're
			// going to refresh the page when the image is selected.

		}
	});

	// When an image is selected, run a callback.
	frame.on( 'select', function() {

		// Grab the selected attachment.
		var attachment = frame.state().get('selection').first();
		frame.close();

		if ( typeof redux.media[jQuery(selector).attr('data-id')] === 'undefined' ) {
			redux.media[jQuery(selector).attr('data-id')] = {};
			redux.media[jQuery(selector).attr('data-id')].mode = "image";
		}

		if ( redux.media[jQuery(selector).attr('data-id')].mode !== false && attachment.attributes.type !== redux.media[jQuery(selector).attr('data-id')].mode) {
			return;
		}

		selector.find('.upload').val(attachment.attributes.url);
		selector.find('.upload-id').val(attachment.attributes.id);
		selector.find('.upload-height').val(attachment.attributes.height);
		selector.find('.upload-width').val(attachment.attributes.width);
		redux_change( jQuery(selector).find( '.upload-id' ) );
		var thumbSrc = attachment.attributes.url;
		if (typeof attachment.attributes.sizes !== 'undefined' && typeof attachment.attributes.sizes.thumbnail !== 'undefined') {
			thumbSrc = attachment.attributes.sizes.thumbnail.url;
		} else if ( typeof attachment.attributes.sizes !== 'undefined' ) {
			var height = attachment.attributes.height;
			for (var key in attachment.attributes.sizes) {
				var object = attachment.attributes.sizes[key];
				if (object.height < height) {
					height = object.height;
					thumbSrc = object.url;
				}
			}
		} else {
			thumbSrc = attachment.attributes.icon;
		}
		selector.find('.upload-thumbnail').val(thumbSrc);
		if ( !selector.find('.upload').hasClass('noPreview') ) {
			selector.find('.screenshot').empty().hide().append('<img class="redux-option-image" src="' + thumbSrc + '">').slideDown('fast');
		}
		//selector.find('.media_upload_button').unbind();
		selector.find('.remove-image').removeClass('hide');//show "Remove" button
		selector.find('.redux-background-properties').slideDown();
	});

	// Finally, open the modal.
	frame.open();
}


// Function to remove the image on click. Still requires a save
function redux_remove_file(selector) {

	// This shouldn't have been run...
	if (!selector.find('.remove-image').addClass('hide')) {
		return;
	}
	selector.find('.remove-image').addClass('hide');//hide "Remove" button
	selector.find('.upload').val('');
	selector.find('.upload-id').val('');
	selector.find('.upload-height').val('');
	selector.find('.upload-width').val('');
	selector.find('.upload-thumbnail').val('');
	redux_change( jQuery(selector).find( '.upload-id' ) );
	selector.find('.redux-background-properties').hide();
	var screenshot = selector.find('.screenshot');

	// Hide the screenshot
	screenshot.slideUp();

	selector.find('.remove-file').unbind();
	// We don't display the upload button if .upload-notice is present
	// This means the user doesn't have the WordPress 3.5 Media Library Support
	if ( jQuery('.section-upload .upload-notice').length > 0 ) {
		jQuery('.media_upload_button').remove();
	}

}

(function($){
	"use strict";

	$.redux = $.redux || {};

	$(document).ready(function () {
		 $.redux.media();
	});

	/**
	* Media Uploader
	* Dependencies		: jquery, wp media uploader
	* Feature added by	: Smartik - http://smartik.ws/
	* Date				: 05.28.2013
	*/
	$.redux.media = function(){
		// Remove the image button
		$('.remove-image, .remove-file').unbind('click').on('click', function() {
			redux_remove_file( $(this).parents('fieldset.redux-field:first') );
		});

		// Upload media button
		$('.media_upload_button').unbind().on('click', function( event ) {
			redux_add_file( event, $(this).parents('fieldset.redux-field:first') );
		});
	};

})(jQuery);
/* global redux_change */
(function($){
	"use strict";

	$.redux = $.redux || {};

	$(document).ready(function () {
		//multi text functionality
		$.redux.multi_text();
	});

	$.redux.multi_text = function(){
		$('.redux-multi-text-remove').live('click', function() {
			redux_change($(this));
			$(this).prev('input[type="text"]').val('');
			$(this).parent().slideUp('medium', function(){
				$(this).remove();
			});
		});

		$('.redux-multi-text-add').click(function(){
			var number = parseInt($(this).attr('data-add_number'));
			var id = $(this).attr('data-id');
			var name = $(this).attr('data-name');
			for (var i = 0; i < number; i++) {
				var new_input = $('#'+id+' li:last-child').clone();
				$('#'+id).append(new_input);
				$('#'+id+' li:last-child').removeAttr('style');
				$('#'+id+' li:last-child input[type="text"]').val('');
				$('#'+id+' li:last-child input[type="text"]').attr('name' , name);
			}
		});
	};
})(jQuery);/* global redux_change */
(function($){
	"use strict";

		$.redux = $.redux || {};

		$(document).ready(function () {
				 $.redux.select();
		});

		$.redux.select = function() {
		$('select.redux-select-item').each(function() {

			var default_params = {
				width: 'resolve',
				triggerChange: true,
				allowClear: true
			};

			if ( $(this).siblings('.select2_params').size() > 0 ) {
				var select2_params = $(this).siblings('.select2_params').val();
				select2_params = JSON.parse( select2_params );
				default_params = $.extend({}, default_params, select2_params);
			}

			if ( $(this).hasClass('font-icons') ) {
				default_params = $.extend({}, {formatResult: addIconToSelect, formatSelection: addIconToSelect, escapeMarkup: function(m) { return m; } }, default_params);
			}
			if ( $(this).hasClass('font-icons-fa') ) {
				default_params = $.extend({}, {formatResult: addIconToSelectFa, formatSelection: addIconToSelectFa, escapeMarkup: function(m) { return m; } }, default_params);
			}
			$(this).select2(default_params);
			if ($(this).hasClass('select2-sortable')) {
				default_params = {};
				default_params.bindOrder = 'sortableStop';
				default_params.sortableOptions = { placeholder : 'ui-state-highlight' };
				$(this).select2Sortable(default_params);
			}

			$(this).on("change", function() {
				redux_change($($(this)));
				$(this).select2SortableOrder();
			});

		});
	};

	function addIconToSelect(icon) {
		if ( icon.hasOwnProperty( 'id' ) ) {
			return "<span class='elusive'><i class='" + icon.id + "'></i>" + "&nbsp;&nbsp;" + icon.id.toUpperCase() + "</span>";
		}
	}
	function addIconToSelectFa(icon) {
		if ( icon.hasOwnProperty( 'id' ) ) {
			return "<span class='elusive'><i class='fa fa-" + icon.id + " fa-2x'></i>" + "&nbsp;&nbsp;" + icon.id.toUpperCase() + "</span>";
		}
	}
})(jQuery);(function($){

	$('.redux-select-image-item').on('change', function() {
		var preview = $(this).parents('.redux-field:first').find('.redux-preview-image');
		if ($(this).val() === "") {
			preview.fadeOut('medium', function() {
				preview.attr('src', '');
			});
		} else {
			preview.attr('src', $(this).val());
			preview.fadeIn().css('visibility', 'visible');
		}
	});

})(jQuery);


jQuery(document).ready(function() {

	jQuery(".redux-spacing-units").select2({
		width: 'resolve',
		triggerChange: true,
		allowClear: true
	});

	jQuery('.redux-spacing-input').on('change', function() {
		var units = jQuery(this).parents('.redux-field:first').find('.field-units').val();
		if ( jQuery(this).parents('.redux-field:first').find('.redux-spacing-units').length !== 0 ) {
			units = jQuery(this).parents('.redux-field:first').find('.redux-spacing-units option:selected').val();
		}
		var value = jQuery(this).val();
		if( typeof units !== 'undefined' && value ) {
			value += units;
		}
		if ( jQuery(this).hasClass( 'redux-spacing-all' ) ) {
			jQuery(this).parents('.redux-field:first').find('.redux-spacing-value').each(function() {
				jQuery(this).val(value);
			});
		} else {
			jQuery('#'+jQuery(this).attr('rel')).val(value);
		}
	});
	jQuery('.redux-spacing-units').on('change', function() {
		jQuery(this).parents('.redux-field:first').find('.redux-spacing-input').change();
	});

});

// spinner_custom.js
(function(a,b){var c="ui-state-active",d="ui-state-hover",e="ui-state-disabled",f=a.ui.keyCode,g=f.UP,h=f.DOWN,i=f.RIGHT,j=f.LEFT,k=f.PAGE_UP,l=f.PAGE_DOWN,m=f.HOME,n=f.END,o=a.browser.msie,p=a.browser.mozilla?"DOMMouseScroll":"mousewheel",q=".uispinner",r=[g,h,i,j,k,l,m,n,f.BACKSPACE,f.DELETE,f.TAB],s;a.widget("ui.spinner",{options:{min:null,max:null,allowNull:false,group:"",point:".",prefix:"",suffix:"",places:null,defaultStep:1,largeStep:10,mouseWheel:true,increment:"slow",className:null,showOn:"always",width:95,upIconClass:"ui-icon-triangle-1-n",downIconClass:"ui-icon-triangle-1-s",format:function(a,b){var c=this,d=/(\d+)(\d{3})/,e=(isNaN(a)?0:Math.abs(a)).toFixed(b)+"";for(e=e.replace(".",c.point);d.test(e)&&c.group;e=e.replace(d,"$1"+c.group+"$2")){}return(a<0?"-":"")+c.prefix+e+c.suffix},parse:function(a){var b=this;if(b.group==".")a=a.replace(".","");if(b.point!=".")a=a.replace(b.point,".");return parseFloat(a.replace(/[^0-9\-\.]/g,""))}},_create:function(){var a=this,b=a.element,c=b.attr("type");if(!b.is("input")||c!="text"&&c!="number"){console.error("Invalid target for ui.spinner");return}a._procOptions(true);a._createButtons(b);if(!b.is(":enabled"))a.disable()},_createButtons:function(b){function R(){if(L){a(this).removeClass(c);p._stopSpin();L=false}return false}function Q(){if(!t.disabled){var b=p.element[0],d=this===C?1:-1;b.focus();b.select();a(this).addClass(c);L=true;p._startSpin(d)}return false}function P(a){function b(){G=0;a()}if(G){if(a===H)return;clearTimeout(G)}H=a;G=setTimeout(b,100)}function O(a,b){if(K)return false;var c=String.fromCharCode(b||a),d=p.options;if(c>="0"&&c<="9"||c=="-")return false;if(p.places>0&&c==d.point||c==d.group)return false;return true}function N(a){for(var b=0;b<r.length;b++)if(r[b]==a)return true;return false}function e(a){return a=="auto"?0:parseInt(a)}var p=this,t=p.options,u=t.className,v=t.width,w=t.showOn,x=a.support.boxModel,y=b.outerHeight(),z=p.oMargin=e(b.css("margin-right")),A=p.wrapper=b.wrap('<span class="spinner-wrpr" />').css({width:(p.oWidth=x?b.width():b.outerWidth())-v,marginRight:"30px",marginLeft:"30px",textAlign:"center","float":"none",marginTop:0}).after('<span class="ui-spinner ui-widget"></span>').next(),B=p.btnContainer=a('<div class="ui-spinner-buttons">'+'<div class="ui-spinner-up ui-spinner-button ui-state-default ui-corner-tr"><span class="ui-icon '+t.upIconClass+'"> </span></div>'+'<div class="ui-spinner-down ui-spinner-button ui-state-default ui-corner-br"><span class="ui-icon '+t.downIconClass+'"> </span></div>'+"</div>"),C,D,E,F,G,H,I,J,K,L,M=b[0].dir=="rtl";if(u)A.addClass(u);A.append(B.css({height:y,left:0,top:0}));E=p.buttons=B.find(".ui-spinner-button");E.css({width:"30px",height:y-(x?E.outerHeight()-E.height():0)});E.eq(0).css({right:"0"});E.eq(1).css({left:"0"});C=E[0];D=E[1];F=E.find(".ui-icon");B.width("105px");if(w!="always")B.css("opacity",0);if(w=="hover"||w=="both")E.add(b).bind("mouseenter"+q,function(){P(function(){I=true;if(!p.focused||w=="hover")p.showButtons()})}).bind("mouseleave"+q,function S(){P(function(){I=false;if(!p.focused||w=="hover")p.hideButtons()})});E.hover(function(){p.buttons.removeClass(d);if(!t.disabled)a(this).addClass(d)},function(){a(this).removeClass(d)}).mousedown(Q).mouseup(R).mouseout(R);if(o)E.dblclick(function(){if(!t.disabled){p._change();p._doSpin((this===C?1:-1)*t.step)}return false}).bind("selectstart",function(){return false});b.bind("keydown"+q,function(b){var d,e,f,o=b.keyCode;if(b.ctrl||b.alt)return true;if(N(o))K=true;if(J)return false;switch(o){case g:case k:d=1;e=o==k;break;case h:case l:d=-1;e=o==l;break;case i:case j:d=o==i^M?1:-1;break;case m:f=p.options.min;if(f!=null)p._setValue(f);return false;case n:f=p.options.max;f=p.options.max;if(f!=null)p._setValue(f);return false}if(d){if(!J&&!t.disabled){keyDir=d;a(d>0?C:D).addClass(c);J=true;p._startSpin(d,e)}return false}}).bind("keyup"+q,function(a){if(a.ctrl||a.alt)return true;if(N(f))K=false;switch(a.keyCode){case g:case i:case k:case h:case j:case l:E.removeClass(c);p._stopSpin();J=false;return false}}).bind("keypress"+q,function(a){if(O(a.keyCode,a.charCode))return false}).bind("change"+q,function(){p._change()}).bind("focus"+q,function(){function a(){p.element.select()}o?a():setTimeout(a,0);p.focused=true;s=p;if(!I&&(w=="focus"||w=="both"))p.showButtons()}).bind("blur"+q,function(){p.focused=false;if(!I&&(w=="focus"||w=="both"))p.hideButtons()})},_procOptions:function(a){var b=this,c=b.element,d=b.options,e=d.min,f=d.max,g=d.step,h=d.places,i=-1,j;if(d.increment=="slow")d.increment=[{count:1,mult:1,delay:250},{count:3,mult:1,delay:100},{count:0,mult:1,delay:50}];else if(d.increment=="fast")d.increment=[{count:1,mult:1,delay:250},{count:19,mult:1,delay:100},{count:80,mult:1,delay:20},{count:100,mult:10,delay:20},{count:0,mult:100,delay:20}];if(e==null&&(j=c.attr("min"))!=null)e=parseFloat(j);if(f==null&&(j=c.attr("max"))!=null)f=parseFloat(j);if(!g&&(j=c.attr("step"))!=null)if(j!="any"){g=parseFloat(j);d.largeStep*=g}d.step=g=g||d.defaultStep;if(h==null&&(j=g+"").indexOf(".")!=-1)h=j.length-j.indexOf(".")-1;b.places=h;if(f!=null&&e!=null){if(e>f)e=f;i=Math.max(Math.max(i,d.format(f,h,c).length),d.format(e,h,c).length)}if(a)b.inputMaxLength=c[0].maxLength;j=b.inputMaxLength;if(j>0){i=i>0?Math.min(j,i):j;j=Math.pow(10,i)-1;if(f==null||f>j)f=j;j=-(j+1)/10+1;if(e==null||e<j)e=j}if(i>0)c.attr("maxlength",i);d.min=e;d.max=f;b._change();c.unbind(p+q);if(d.mouseWheel)c.bind(p+q,b._mouseWheel)},_mouseWheel:function(b){var c=a.data(this,"spinner");if(!c.options.disabled&&c.focused&&s===c){c._change();c._doSpin(((b.wheelDelta||-b.detail)>0?1:-1)*c.options.step);return false}},_setTimer:function(a,b,c){function e(){d._spin(b,c)}var d=this;d._stopSpin();d.timer=setInterval(e,a)},_stopSpin:function(){if(this.timer){clearInterval(this.timer);this.timer=0}},_startSpin:function(a,b){var c=this,d=c.options,e=d.increment;c._change();c._doSpin(a*(b?c.options.largeStep:c.options.step));if(e&&e.length>0){c.counter=0;c.incCounter=0;c._setTimer(e[0].delay,a,b)}},_spin:function(a,b){var c=this,d=c.options.increment,e=d[c.incCounter];c._doSpin(a*e.mult*(b?c.options.largeStep:c.options.step));c.counter++;if(c.counter>e.count&&c.incCounter<d.length-1){c.counter=0;e=d[++c.incCounter];c._setTimer(e.delay,a,b)}},_doSpin:function(a){var b=this,c=b.curvalue;if(c==null)c=(a>0?b.options.min:b.options.max)||0;b._setValue(c+a)},_parseValue:function(){var a=this.element.val();return a?this.options.parse(a,this.element):null},_validate:function(a){var b=this.options,c=b.min,d=b.max;if(a==null&&!b.allowNull)a=this.curvalue!=null?this.curvalue:c||d||0;if(d!=null&&a>d)return d;else if(c!=null&&a<c)return c;else return a},_change:function(){var a=this,b=a._parseValue(),c=a.options.min,d=a.options.max;if(!a.selfChange){if(isNaN(b))b=a.curvalue;a._setValue(b,true)}},_setOption:function(b,c){a.Widget.prototype._setOption.call(this,b,c);this._procOptions()},increment:function(){this._doSpin(this.options.step)},decrement:function(){this._doSpin(-this.options.step)},showButtons:function(a){var b=this.btnContainer.stop();if(a)b.css("opacity",1);else b.fadeTo("fast",1)},hideButtons:function(a){var b=this.btnContainer.stop();if(a)b.css("opacity",0);else b.fadeTo("fast",0);this.buttons.removeClass(d)},_setValue:function(a,b){var c=this;c.curvalue=a=c._validate(a);c.element.val(a!=null?c.options.format(a,c.places,c.element):"");if(!b){c.selfChange=true;c.element.change();c.selfChange=false}},value:function(a){if(arguments.length){this._setValue(a);return this.element}return this.curvalue},enable:function(){this.buttons.removeClass(e);this.element[0].disabled=false;a.Widget.prototype.enable.call(this)},disable:function(){this.buttons.addClass(e).removeClass(d);this.element[0].disabled=true;a.Widget.prototype.disable.call(this)},destroy:function(b){this.wrapper.remove();this.element.unbind(q).css({width:this.oWidth,marginRight:this.oMargin});a.Widget.prototype.destroy.call(this)}})})(jQuery)

/* global redux_change */
jQuery(document).ready(function() {

	jQuery('.redux_spinner').each(function() {
		//slider init
		var spinner = redux.spinner[jQuery(this).attr('rel')];

		jQuery("#" + spinner.id).spinner({
			value: parseInt(spinner.val, null),
			min: parseInt(spinner.min, null),
			max: parseInt(spinner.max, null),
			step: parseInt(spinner.step, null),
			range: "min",
			slide: function(event, ui) {
				var input = jQuery("#" + spinner.id);
				input.val(ui.value);
				redux_change(input);
			}
		});

		// Limit input for negative
		var neg = false;
		if (parseInt(spinner.min, null) < 0) {
			neg = true;
		}

		/*jQuery("#" + spinner.id).numeric({
			allowMinus: neg,
			min: spinner.min,
			max: spinner.max
		});*/

	});

	// Update the slider from the input and vice versa
	jQuery(".spinner-input").keyup(function() {

		jQuery(this).addClass('spinnerInputChange');

	});

	function cleanSpinnerValue(value, selector, spinner) {

		if ( !selector.hasClass('spinnerInputChange') ) {
			return;
		}
		selector.removeClass('spinnerInputChange');

		if (value === "" || value === null) {
			value = spinner.min;
		} else if (value >= parseInt(spinner.max)) {
			value = spinner.max;
		} else if (value <= parseInt(spinner.min)) {
			value = spinner.min;
		} else {
			value = Math.round(value / spinner.step) * spinner.step;
		}

		jQuery("#" + spinner.id).val(value);

	}

	// Update the spinner from the input and vice versa
	jQuery(".spinner-input").blur(function() {
//        cleanSpinnerValue(jQuery(this).val(), jQuery(this), redux.spinner[jQuery(this).attr('id')]);
	});
	jQuery(".spinner-input").focus(function() {
		cleanSpinnerValue(jQuery(this).val(), jQuery(this), redux.spinner[jQuery(this).attr('id')]);
	});

	/*jQuery('.spinner-input').typeWatch({
		callback:function(value){
			cleanSpinnerValue(value, jQuery(this), redux.spinner[jQuery(this).attr('id')]);
		},
		wait:500,
		highlight:false,
		captureLength:1
	});*/

});
/* global redux_change */
(function($){
	"use strict";

	$.redux = $.redux || {};

	$(document).ready(function () {
		 $.redux.switch();
	});

	/**
	 * Switch
	 * Dependencies		: jquery
	 * Feature added by	: Smartik - http://smartik.ws/
	 * Date				: 03.17.2013
	 */
	$.redux.switch = function(){
		$(".cb-enable").click(function() {
			if ($(this).hasClass('selected')) {
				return;
			}
			var parent = $(this).parents('.switch-options');
			$('.cb-disable', parent).removeClass('selected');
			$(this).addClass('selected');
			$('.checkbox-input', parent).val(1);
			redux_change($('.checkbox-input', parent));
			//fold/unfold related options
			var obj = $(this);
			var $fold = '.f_' + obj.data('id');
			$($fold).slideDown('normal', "swing");
		});
		$(".cb-disable").click(function() {
			if ($(this).hasClass('selected')) {
				return;
			}
			var parent = $(this).parents('.switch-options');
			$('.cb-enable', parent).removeClass('selected');
			$(this).addClass('selected');
			$('.checkbox-input', parent).val(0);
			redux_change($('.checkbox-input', parent));
			//fold/unfold related options
			var obj = $(this);
			var $fold = '.f_' + obj.data('id');
			$($fold).slideUp('normal', "swing");
		});
		//disable text select(for modern chrome, safari and firefox is done via CSS)
		//if (($.browser.msie && $.browser.version < 10) || $.browser.opera) {
		$('.cb-enable span, .cb-disable span').find().attr('unselectable', 'on');
		//}
	};
})(jQuery);/* global redux_change */
/**
 * Typography
 * Dependencies		: google.com, jquery
 * Feature added by : Dovy Paukstys - http://simplerain.com/
 * Date				: 06.14.2013
 */
jQuery.noConflict();
/** Fire up jQuery - let's dance!
 */
jQuery(document).ready(function($) {

	Object.size = function(obj) {
		var size = 0,
		key;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				size++;
			}
		}
		return size;
	};

	function typographySelect(selector) {
		var mainID = jQuery(selector).parents('.redux-container-typography:first').attr('data-id');
		if ($(selector).hasClass('redux-typography-family')) {
			//$('#' + mainID + ' .typography-style span').text('');
			//$('#' + mainID + ' .typography-script span').text('');
		}
		// Set all the variables to be checked against
		var family = $('#' + mainID + ' select.redux-typography-family').val();
		if (!family) {
			family = null; //"inherit";
		}
		var familyBackup = $('#' + mainID + ' select.redux-typography-family-backup').val();
		var size = $('#' + mainID + ' .redux-typography-size').val();
		var height = $('#' + mainID + ' .redux-typography-height').val();
		var word = $('#' + mainID + ' .redux-typography-word').val(); // New Word-Spacing
		var letter = $('#' + mainID + ' .redux-typography-letter').val(); // New Letter-Spacing
		var align = $('#' + mainID + ' select.redux-typography-align').val(); // text-align
		var transform = $('#' + mainID + ' select.redux-typography-transform').val();
		var style = $('#' + mainID + ' select.redux-typography-style').val();
		var script = $('#' + mainID + ' select.redux-typography-subsets').val();
		var color = $('#' + mainID + ' .redux-typography-color').val();
		var units = $('#' + mainID).data('units');
		var option = $('#' + mainID + ' .redux-typography-family option:selected');
		var output = family;
		//$('#' + mainID + ' select.redux-typography-style').val('');
		//$('#' + mainID + ' select.redux-typography-subsets').val('');
		var google = option.data('google'); // Check if font is a google font
		// Page load. Speeds things up memory wise to offload to client
		if (!$('#' + mainID).hasClass('typography-initialized')) {
			style = $('#' + mainID + ' select.redux-typography-style').data('value');
			script = $('#' + mainID + ' select.redux-typography-subsets').data('value');
			if (style !== "") {
				style = String(style);
			}
			if (typeof (script) !== undefined) {
				script = String(script);
			}
			$('#' + mainID).addClass('typography-initialized');
		}
		// Get the styles and such from the font
		var details = undefined !== option.data('details') ? jQuery.parseJSON(decodeURIComponent(option.data('details'))) : '';
		$('#' + mainID + ' .redux-typography-font-options').val(decodeURIComponent(option.data('details')));
		// If we changed the font
		if ($(selector).hasClass('redux-typography-family')) {
			var html = '<option value=""></option>';
			if (google) { // Google specific stuff
				var selected = "";
				$.each(details.variants, function(index, variant) {
					if (variant.id === style || Object.size(details.variants) === 1) {
						selected = ' selected="selected"';
						style = variant.id;
					} else {
						selected = "";
					}
					html += '<option value="' + variant.id + '"' + selected + '>' + variant.name.replace(/\+/g, " ") + '</option>';
				});
				$('#' + mainID + ' .redux-typography-style').html(html);
				selected = "";
				html = '<option value=""></option>';
				$.each(details.subsets, function(index, subset) {
					if (subset.id === script || Object.size(details.subsets) === 1) {
						selected = ' selected="selected"';
						script = subset.id;
					} else {
						selected = "";
					}
					html += '<option value="' + subset.id + '"' + selected + '>' + subset.name.replace(/\+/g, " ") + '</option>';
				});
				if (typeof (familyBackup) !== "undefined" && familyBackup !== "") {
					output += ', ' + familyBackup;
				}

				$('#' + mainID + ' .redux-typography-subsets').html(html);
				$('#' + mainID + ' .redux-typography-subsets').fadeIn('fast');
				$('#' + mainID + ' .typography-family-backup').fadeIn('fast');
			} else {
				if (details) {
					$.each(details, function(index, value) {
						if (index === style || index === "normal") {
							selected = ' selected="selected"';
							$('#' + mainID + ' .typography-style .select2-chosen').text(value);
						} else {
							selected = "";
						}
						html += '<option value="' + index + '"' + selected + '>' + value.replace('+', ' ') + '</option>';
					});
					$('#' + mainID + ' .redux-typography-style').html(html);
					$('#' + mainID + ' .redux-typography-subsets').fadeOut('fast');
					$('#' + mainID + ' .typography-family-backup').fadeOut('fast');
				}
			}
		} else if ($(selector).hasClass('redux-typography-family-backup') && familyBackup !== "") {
			$('#' + mainID + ' .redux-typography-font-family').val(output);
		}

		// Check if the selected value exists. If not, empty it. Else, apply it.
		if ($('#' + mainID + " select.redux-typography-style option[value='" + style + "']").length === 0) {
			style = "";
			$('#' + mainID + ' select.redux-typography-style').val('');
		} else if (style === "400") {
			$('#' + mainID + ' select.redux-typography-style').val(style);
		}
		if ($('#' + mainID + " select.redux-typography-subsets option[value='" + script + "']").length === 0) {
			script = "";
			$('#' + mainID + ' select.redux-typography-subsets').val('');
		}

		var _linkclass = 'style_link_' + mainID;

		//remove other elements crested in <head>
		$('.' + _linkclass).remove();
		if (family !== null && family !== "inherit") {
			//replace spaces with "+" sign
			var the_font = family.replace(/\s+/g, '+');
			if (google) {
				//add reference to google font family
				var link = the_font;
				if (style) {
					link += ':' + style.replace(/\-/g, " ");
				}
				if (script) {
					link += '&subset=' + script;
				}

				if (WebFont) {
					WebFont.load({google: {families: [link]}});
				}
				//link = 'http://fonts.googleapis.com/css?family=' + link;
				//$('head').append('<link href="' + link + '" rel="stylesheet" type="text/css" class="' + _linkclass + '">');
				$('#' + mainID + ' .redux-typography-google').val(true);
			} else {
				$('#' + mainID + ' .redux-typography-google').val(false);
			}
		}

		$('#' + mainID + ' .typography-preview').css('font-size', size + units);
		$('#' + mainID + ' .typography-preview').css('font-style', "normal");

		// Weight and italic
		if (style.indexOf("italic") !== -1) {
			$('#' + mainID + ' .typography-preview').css('font-style', 'italic');
			$('#' + mainID + ' .typography-font-style').val('italic');
			style = style.replace('italic', '');
		} else {
			$('#' + mainID + ' .typography-font-style').val('');
		}
		$('#' + mainID + ' .typography-font-weight').val(style);
		$('#' + mainID + ' .typography-preview').css('font-weight', style);

		//show in the preview box the font
		$('#' + mainID + ' .typography-preview').css('font-family', family + ', sans-serif');

		if (family === 'none' && family === '') {
			//if selected is not a font remove style "font-family" at preview box
			$('#' + mainID + ' .typography-preview').css('font-family', 'inherit');
		}
		if (!height) {
			height = size;
		}

		$('#' + mainID + ' .typography-preview').css('line-height', height + units);
		$('#' + mainID + ' .typography-preview').css('word-spacing', word + units);
		$('#' + mainID + ' .typography-preview').css('letter-spacing', letter + units);
		if (size === '') {
			$('#' + mainID + ' .typography-font-size').val('');
		} else {
			//console.log('here-font-size');
			$('#' + mainID + ' .typography-font-size').val(size + units);
		}
		if (height === '') {
			$('#' + mainID + ' .typography-line-height').val('');
		} else {
			$('#' + mainID + ' .typography-line-height').val(height + units);
		}
		$('#' + mainID + ' .typography-word-spacing').val(word + units);
		$('#' + mainID + ' .typography-letter-spacing').val(letter + units);

		if (color) {
			$('#' + mainID + ' .typography-preview').css('color', color);
			$('#' + mainID + ' .typography-preview').css('background-color', getContrastColour(color));
		}

		$('#' + mainID + ' .redux-typography-font-family').val(output);
		$('#' + mainID + ' .typography-style .select2-chosen').text($('#' + mainID + ' .redux-typography-style option:selected').text());
		$('#' + mainID + ' .typography-script .select2-chosen').text($('#' + mainID + ' .redux-typography-subsets option:selected').text());

		if (align) {
			$('#' + mainID + ' .typography-preview').css('text-align', align);
		}

		if (transform) {
			$('#' + mainID + ' .typography-preview').css('text-transform', transform);
		}

	}
	//init for each element
	jQuery('.redux-typography-container').each(function() {
		var family = jQuery(this).find('.redux-typography-family');
		if (family.data('value') !== "") {
			jQuery(family).val(family.data('value'));
		}
		typographySelect(family);
	});
	//init when value is changed
	jQuery('.redux-typography').on('change', function() {
		typographySelect(this);
	});
	//init when value is changed
	jQuery('.redux-typography-size, .redux-typography-height, .redux-typography-word, .redux-typography-letter, .redux-typography-align, .redux-typography-transform').keyup(function() {
		typographySelect(this);
	});
	// Have to redeclare the wpColorPicker to get a callback function
	$('.redux-typography-color').wpColorPicker({
		change: function(event, ui) {
			redux_change(jQuery(this));
			jQuery(this).val(ui.color.toString());
			typographySelect(jQuery(this));
		}
	});
/*
	jQuery(".redux-typography-size").numeric({
		allowMinus: false,
	});

	jQuery(".redux-typography-height, .redux-typography-word, .redux-typography-letter").numeric({
		allowMinus: true,
	});
*/
	//jQuery(".redux-typography-family, .redux-typography-style, .redux-typography-subsets").select2({
	jQuery(".redux-typography-family, .redux-typography-family-backup, .redux-typography-align, .redux-typography-transform").select2({
		width: 'resolve',
		triggerChange: true,
		allowClear: true
	});

	jQuery('.redux-typography-qtip').each(function() {
		/*$(this).qtip({
			style: 'qtip-tipsy black',
			position: {
				my: 'bottom center',
				at: 'top center',
			}
		});*/
	});
});

/*!
 * jQuery Cookie Plugin v1.3.1
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2013 Klaus Hartl
 * Released under the MIT license
 */
(function (factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as anonymous module.
		define(['jquery'], factory);
	} else {
		// Browser globals.
		factory(jQuery);
	}
}(function ($) {

	var pluses = /\+/g;

	function raw(s) {
		return s;
	}

	function decoded(s) {
		return decodeURIComponent(s.replace(pluses, ' '));
	}

	function converted(s) {
		if (s.indexOf('"') === 0) {
			// This is a quoted cookie as according to RFC2068, unescape
			s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
		}
		try {
			return config.json ? JSON.parse(s) : s;
		} catch(er) {}
	}

	var config = $.cookie = function (key, value, options) {

		// write
		if (value !== undefined) {
			options = $.extend({}, config.defaults, options);

			if (typeof options.expires === 'number') {
				var days = options.expires, t = options.expires = new Date();
				t.setDate(t.getDate() + days);
			}

			value = config.json ? JSON.stringify(value) : String(value);

			return (document.cookie = [
				config.raw ? key : encodeURIComponent(key),
				'=',
				config.raw ? value : encodeURIComponent(value),
				options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
				options.path    ? '; path=' + options.path : '',
				options.domain  ? '; domain=' + options.domain : '',
				options.secure  ? '; secure' : ''
			].join(''));
		}

		// read
		var decode = config.raw ? raw : decoded;
		var cookies = document.cookie.split('; ');
		var result = key ? undefined : {};
		for (var i = 0, l = cookies.length; i < l; i++) {
			var parts = cookies[i].split('=');
			var name = decode(parts.shift());
			var cookie = decode(parts.join('='));

			if (key && key === name) {
				result = converted(cookie);
				break;
			}

			if (!key) {
				result[name] = converted(cookie);
			}
		}

		return result;
	};

	config.defaults = {};

	$.removeCookie = function (key, options) {
		if ($.cookie(key) !== undefined) {
			// Must not alter options, thus extending a fresh object...
			$.cookie(key, '', $.extend({}, options, { expires: -1 }));
			return true;
		}
		return false;
	};

}));

// select2.sortable
(function(a){a.fn.extend({select2SortableOrder:function(){var b=this.filter("[multiple]");b.each(function(){var e=a(this);if(typeof(e.data("select2"))!=="object"){return false}var d=e.siblings(".select2-container"),f=[],c;e.find("option").each(function(){!this.selected&&f.push(this)});c=a(d.find('.select2-choices li[class!="select2-search-field"]').map(function(){if(!this){return undefined}var g=a(this).data("select2Data").id;return e.find('option[value="'+g+'"]')[0]}));c.push.apply(c,f);e.children().remove();e.append(c)});return b},select2Sortable:function(){var d=Array.prototype.slice.call(arguments,0);$this=this.filter("[multiple]"),validMethods=["destroy"];if(d.length===0||typeof(d[0])==="object"){var b={bindOrder:"formSubmit",sortableOptions:{placeholder:"ui-state-highlight",items:"li:not(.select2-search-field)",tolerance:"pointer"}};var c=a.extend(b,d[0]);if(typeof($this.data("select2"))!=="object"){$this.select2()}$this.each(function(){var e=a(this),f=e.siblings(".select2-container").find(".select2-choices");f.sortable(c.sortableOptions);switch(c.bindOrder){case"sortableStop":f.on("sortstop.select2sortable",function(g,h){e.select2SortableOrder()});e.on("change",function(g){a(this).select2SortableOrder()});break;default:e.closest("form").unbind("submit.select2sortable").on("submit.select2sortable",function(){e.select2SortableOrder()})}})}else{if(typeof(d[0]==="string")){if(a.inArray(d[0],validMethods)==-1){throw"Unknown method: "+d[0]}if(d[0]==="destroy"){$this.select2SortableDestroy()}}}return $this},select2SortableDestroy:function(){var b=this.filter("[multiple]");b.each(function(){var c=a(this),d=c.parent().find(".select2-choices");c.closest("form").unbind("submit.select2sortable");d.unbind("sortstop.select2sortable");d.sortable("destroy")});return b}})}(jQuery));