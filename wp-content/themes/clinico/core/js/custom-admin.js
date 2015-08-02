"use strict";
jQuery(document).ready(function() {
//	simple_select_2_init();
	invokeSelect2();
	pinterest_layout_select();
	jQuery(".redux-image-select#cws-mb-sb_layout .redux-image-select").live('click',pinterest_layout_select);
});

function format(item){
	return item.text;
}

function simple_select_2_init (){
	jQuery('select[id^="widget-cws"]:not(.icons)').each(function(){
		var id = jQuery(this).attr('id');
		if (id.indexOf("__i__") == -1) {
			jQuery(this).select2({
				allowClear: true,
				placeholder: "",
				formatResult: format,
				formatSelection: format,
				escapeMarkup: function(m) { return m; }
			});
		}
	});
}

function pinterest_layout_select () {		/* dependencies between sb_layout and pinterest cols count */
	jQuery(".redux-image-select#cws-mb-sb_layout").each( function (){
		var choice, sb_layout;
		jQuery(this).find("input[type='radio']").each(function (){
			if(jQuery(this).attr('checked') != undefined){
				choice = jQuery(this).attr('value');
			}
		});
		if ( ['left','right'].indexOf(choice)!=-1 ){
			 sb_layout = "single";
		}
		else if ( choice == 'both' ){
			sb_layout = 'double';
		}
		else{
			sb_layout = choice;
		}
		blog_layout_controller( choice );
		var data = { "single":{restricted_cols_count : [4], show_siblings : true} , "double":{restricted_cols_count : [3,4], show_siblings : false} };
		pinterest_layout_controller( data, sb_layout );
	});
}

function pinterest_layout_controller (data, sb_layout){
	var data = data[sb_layout];
	var pinterest_options = jQuery(".redux-image-select#cws-mb-pinterest_layout>.redux-image-select");
		if (data == undefined){
			pinterest_options.each(function (){
					jQuery(this).show();
			});
		}
		else{
			var restricted = data['restricted_cols_count'];
			var min = restricted[0];
			var restricted_values_selector = "";
			for (var i=0;i<restricted.length;i++){
				if (restricted[i]<min){
					min = restricted[i];
				}
				restricted_values_selector += "input[type='radio'][value='" + restricted[i] + "']";
				if (i<restricted.length-1){
					restricted_values_selector += ",";
				}
			}
			var max_allowed = min - 1;
			if (max_allowed<2){
				return;
			}
			var show_siblings = data['show_siblings'];
			pinterest_options.each(function (){
				var processed_options = jQuery(this).find(restricted_values_selector);
				if (processed_options.length>0){
					if (show_siblings){
						jQuery(this).hide().siblings().show();
					}
					else{
						jQuery(this).hide();
					}
					for (var i=0; i<processed_options.length; i++){
						if (jQuery(processed_options[i]).attr("checked")){
							var val = parseInt(jQuery(processed_options[i]).attr("value"));
							if (val>max_allowed){
								jQuery(processed_options[i]).removeAttr("checked").parents("label").removeClass("redux-image-select-selected");
								pinterest_options.find("input[type='radio'][value='" + max_allowed + "']").attr("checked","checked").parents("label").addClass("redux-image-select-selected");
							}
						}
					}
				}
			});
		}
}

function blog_layout_controller (choice){
	var is_blog = false;
	if ( choice == 'default' ){
		jQuery(".redux-image-select#cws-mb-pinterest_layout").parents("tr").hide();
		jQuery(".redux-image-select#cws-mb-blogtype").find("[name='cws-mb-blogtype']").removeAttr("checked").parent().removeClass("redux-image-select-selected");
		jQuery(".redux-image-select#cws-mb-blogtype").find("[name='cws-mb-blogtype'][value='medium']").attr("checked","checked").parent().addClass("redux-image-select-selected");
		jQuery(".redux-image-select#cws-mb-blogtype").parents("tr").hide();
	}
	else{
		if ( jQuery("[name='cws-mb-sb_override']").attr("checked") != undefined ){
			is_blog = true;
			if (is_blog){
				jQuery(".redux-image-select#cws-mb-blogtype").parents("tr").show();
				var selected = jQuery(".redux-image-select#cws-mb-blogtype").find("[name='cws-mb-blogtype'][checked='checked']");
				if ( ( selected.attr('value') == 'pinterest' ) && ( is_blog ) ){
					jQuery(".redux-image-select#cws-mb-pinterest_layout").parents("tr").show();
				}
			}
		}
	}
}

function addIconToSelectFa(icon) {
	if ( icon.hasOwnProperty( 'id' ) ) {
		return "<span><i class='fa fa-" + icon.id + " fa-2x'></i>" + "&nbsp;&nbsp;" + icon.id.toUpperCase() + "</span>";
	}
}

function invokeSelect2() {
	jQuery('.show_icon_options').live('change', function (){
		if (jQuery(this).prop("checked")){
			jQuery(this).parents(".widget-content").find(".icon-options").show(300);
		}
		else{
			jQuery(this).parents(".widget-content").find(".icon-options").hide(300);
		}
	});

	jQuery('select[id*="dept"],select[id^="cws-pb"]').each(function() {
		var id = jQuery(this).attr('id');
		var wp_content_len = jQuery(this).parents('#wp-content-wrap').length;
		if (id.indexOf("__i__") == -1 && !wp_content_len) {
			jQuery(this).select2();
		}
	});

	jQuery('.reset_icon_options').live('click',function (e){
		e.preventDefault();
		var icon_parents = jQuery(this).parents(".icon-options");
		icon_parents.find(".icons,.image").attr("value","");
		icon_parents.find(".icons").select2("val","");
		icon_parents.find("img[id*='title_img']").attr('src','');
		icon_parents.find("a[id^='remov']").hide(300);
		icon_parents.find("a[id^='media']").show(300);
	})

	jQuery('select[id^="cws-clinico"],select.icons').each(function() {
		var id = jQuery(this).attr('id');
		if (id.indexOf("__i__") == -1) {
			if (-1 !== id.indexOf("_fa")) {
				jQuery(this).select2({
					allowClear: true,
					placeholder: " ",
					formatResult: addIconToSelectFa,
					formatSelection: addIconToSelectFa,
					escapeMarkup: function(m) { return m; }
				});
			} else {
				jQuery(this).select2();
			}
		}
	});

	jQuery('.widget-content li.redux-image-select').click(function()
	{
		var _this = jQuery(this);
		_this.addClass("selected").siblings().removeClass("selected");
		_this.children("input").prop('checked',true).siblings().prop('checked',false);
		var ind = _this.index();
		var opt_group = _this.parents(".widget-content").find(".image-part").children(".img-wrapper");
		var current_opt = opt_group.eq(ind);
		var other_opts = current_opt.siblings();
		other_opts.fadeOut( 150, function (){
			current_opt.fadeIn( 150 );
		});
	});

	jQuery('a[id^="media"]').live("click", function() {
		var _this = jQuery(this).attr('id').substring(6);
		var media_editor_attachment_backup = wp.media.editor.send.attachment;
		wp.media.editor.send.attachment = function(props, attachment) {
			jQuery('#'+_this).attr("value", attachment.id);
			var url= '';
			if (attachment.sizes.thumbnail == undefined){
				url=attachment.sizes.full.url;
			}
			else{
				url=attachment.sizes.thumbnail.url;
			}
			jQuery('img#img-'+_this).attr("src", url).toggle();
			jQuery('a#media-' + _this).hide(300);
			jQuery('a#remov-' + _this).show(300);
			wp.media.editor.send.attachment = media_editor_attachment_backup;
			return;
		}
		wp.media.editor.open(this);
	});

	jQuery('a[id^="remov"]').live("click",function()
	{
		var _this = jQuery(this).attr('id').substring(6);
		jQuery("#"+_this).attr("value", '');
		jQuery('img#img-'+_this).toggle();
		jQuery('a#remov-' + _this).hide(300);
		jQuery('a#media-' + _this).show(300);
	});
}

jQuery(document).ajaxSuccess(function(e, xhr, settings) {
	var widget_id = 'cws';
	if( settings.data !== undefined) {
		if (settings.data.search('widget-id=' + widget_id) != -1 ) {
			invokeSelect2();
		}
		if (settings.data.search('action=add-tag') != -1) {
			if (settings.data.search('taxonomy=cws-staff-dept') != -1) {
				// just added tags to dept
				jQuery('img#img-dept-img').attr("src",'');
				jQuery('#dept-img').attr("value", '');
				jQuery('a#remov-dept-img').hide(300);
				jQuery('a#media-dept-img').show(300);
				jQuery('select[id^=cws-clinico]').select2('val', 'All');
			}
			if (settings.data.search('taxonomy=cws-staff-procedures') != -1) {
				jQuery('select[id^="cws-clinico"]').select2('val', 'All');
				jQuery('input[id^="cws-clinico"]').val('');
			}
		}
	}
});

/********************************* CWS_TB_MODAL *********************************/

window.cws_tb = [];

document.onkeydown = function(evt) {
    evt = evt || window.event;
    if ( evt.keyCode == 27 && window.cws_tb.length ) {
       cws_last_tb_modal_close();
    }
};

function cws_tb_modal_show ( source, method, title, width, height ){
	var modal_index = jQuery("cws_tb_modal").length;
	var id = "cws_tb_modal_" + String(modal_index);
	jQuery("body").append("<section class='cws_tb_modal' id='" + id + "' style='z-index:" + String(100050+modal_index) + "'><div class='cws_tb_modal_overlay'></div><div class='cws_tb_modal_window' style='width:" + width + "px;height:" + height + "px;'><div class='cws_tb_modal_header'>" + title + "<div class='cws_tb_modal_close'></div></div><div class='cws_tb_modal_content'></div></div></section>");
	var container = jQuery("#"+id);
	if ( method=="get" ){
		jQuery.get( source, function ( data ){
			container.find(".cws_tb_modal_content").append(data);
			window.cws_tb[modal_index] = id;
		});
	}
	else if ( method=="post" ){
		jQuery.post( source, function ( data ){
			container.find(".cws_tb_modal_content").append(data);
			window.cws_tb[modal_index] = id;
		});
	}
	else{
		container.remove();
	}
}

function cws_tb_close (){
	jQuery(this).fadeOut('fast', function (){
		jQuery(this).remove();
	});
}

function cws_tb_modal_close (){
	jQuery(this).parents(".cws_tb_modal").cws_tb_close();
}

function cws_last_tb_modal_close (){
	jQuery( '#' + cws_tb[ cws_tb.length - 1 ] ).cws_tb_close();
}

function is_cws_tb_modal (){
	return Boolean(jQuery(this).parents(".cws_tb_modal").length);
}

jQuery.fn.cws_tb_close = cws_tb_close;
jQuery.fn.is_cws_tb_modal = is_cws_tb_modal;
jQuery.fn.cws_tb_modal_close = cws_tb_modal_close;
jQuery(".cws_tb_modal_close").live("click",cws_tb_modal_close);