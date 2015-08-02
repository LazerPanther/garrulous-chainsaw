"use strict";
jQuery(document).ready(function() {

	if (jQuery('.cws-page-section').length) {
		jQuery('.cws-page-section select').each( function() {
			jQuery(this).select2({
				allowClear: true,
				placeholder: " ",
			});
		});
	}

	jQuery('.redux-image-select label .cws_img_select_wrap, .redux-image-select label .cws_img_select_wrap').click(function(e) {
		var id = jQuery(this).closest('label').attr('for');
		jQuery(this).parents("td:first").find('.redux-image-select-selected').removeClass('redux-image-select-selected');
		jQuery(this).closest('label').find('input[type="radio"]').prop('checked');
		jQuery('label[for="' + id + '"]').addClass('redux-image-select-selected').find("input[type='radio']").attr("checked", true);
	});

	if ( 0 == jQuery('#post-formats-select input#post-format-table').length ) {
		jQuery('#post-formats-select').append('<input type="radio" name="post_format" class="post-format" id="post-format-table" value="table"></input><label for="post-format-table" class="post-format-icon post-format-table">Price Table</label>');
	}
	if ( !jQuery('label.post-format-table').html() ) {
		jQuery('label.post-format-table').text("Price Table");
	}

	var old_type = 0;
	var bIsParentHidden = false;

	jQuery('#post-formats-select input').change(function(){
		//hideAllMetaBox();
		var type = jQuery(this).val();
		jQuery('#cws-post-metabox-id h3').html( type.substring(0, 1).toUpperCase() + type.substring(1) + " Settings" );
		if (0 != old_type) {
			jQuery('#post-' + old_type).hide();
		}
		if ( 0 == jQuery('#post-' + type).length ) {
			jQuery('#cws-post-metabox-id').hide();
			bIsParentHidden = true;
		} else {
			if (bIsParentHidden) {
				jQuery('#cws-post-metabox-id').show();
				bIsParentHidden = false;
			}
			jQuery('#post-' + type).show();
		}
		old_type = type;
	});

	jQuery('#post-formats-select input:checked').change();

	var cws_frame;

	jQuery('#cws-mb-media-button').click( function( event ) {
		//var $el = $(this);
		event.preventDefault();

		// If the media frame already exists, reopen it.
		// Create the media frame.
		//var selection = 'a:2:{i:998,i:999}'; //this.select();
		var content = jQuery('#cws-mb-gallery').attr("value");// '[gallery ids="999,530"]';
		var shortcode = wp.shortcode.next( 'gallery', content ),
				defaultPostId = wp.media.gallery.defaults.id,
				attachments, selection;

			// Bail if we didn't match the shortcode or all of the content.
		if ( shortcode) {
			shortcode = shortcode.shortcode;

			if ( _.isUndefined( shortcode.get('id') ) && ! _.isUndefined( defaultPostId ) )
				shortcode.set( 'id', defaultPostId );

			attachments = wp.media.gallery.attachments( shortcode );

			selection = new wp.media.model.Selection( attachments.models, {
				props:    attachments.props.toJSON(),
				multiple: true
			});

			selection.gallery = attachments.gallery;
		}

		cws_frame = wp.media({
			// Set the title of the modal.
			id:				'cws-frame',
			frame:		'post',
			state:		'gallery-edit',
			title:		wp.media.view.l10n.editGalleryTitle,
			editing:	true,
			multiple:	true,
			selection: selection,

			// Tell the modal to show only images.
			library: { type: 'image' },

			// Customize the submit button.
			button: {	text: 'update',
				close: false
			}
		});

		// When an image is selected, run a callback.
		cws_frame.on( 'update', function( selection ) {
			jQuery('#cws-mb-gallery').attr("value", wp.media.gallery.shortcode( selection ).string() );
		});

		cws_frame.open();
	});

	jQuery(function($) {
		var tb_position = function() {
			var tbWindow = $('#TB_window');
			var width = $(window).width();
			var H = window.ajaxContentH;
			var W = window.ajaxContentW;

			if ( tbWindow.size() ) {
				tbWindow.width(W).height(H);
				//$('#TB_iframeContent').width( W - 50 ).height( H - 75 );
				tbWindow.css({'margin-left': '-' + parseInt((W/2),10) + 'px'});
				tbWindow.css({'margin-top': '-' + parseInt((H/2),10) + 'px'});
			};
		};
		$(window).resize( function() { tb_position() } );
	});
});