"use strict";
jQuery(document).ready(function () {

function getPageId() {
	var a = document.getElementsByTagName('body')[0].className;
	var addendum = 0;
	var ind = a.indexOf('page-id');
	if ( ind != -1 ){
		addendum = 8;
	}
	else{
		 ind = a.indexOf('postid');
		 addendum = 7;
	}
	return a.substr(ind+addendum, a.indexOf(' ', ind)-ind-addendum);
}

function update_grid ( old_items, new_items, div_grid ){
	var new_rows = Math.floor( ( new_items.length / div_grid.data('cols') ) + (0 !== new_items.length % div_grid.data('cols')) );
	var g_height = old_items[0].clientHeight * new_rows;

	div_grid.css('min-height', g_height);

	div_grid.append( new_items );
	div_grid.isotope( 'remove', old_items ).isotope('layout');
	var img_loader = imagesLoaded( div_grid );
	img_loader.on ('always', function (){
		div_grid.isotope( 'appended', new_items);
		div_grid.isotope('layout');
	});
	//div_grid.isotope( { sortBy:'cat' } );
	div_grid.isotope('updateSortData').isotope();
}

function PortfolioPage( container, el, e ) {
	e.preventDefault();
	e.stopPropagation();
	var pid = getPageId();
	var div_grid = container.find(".grid");
	var link = el.attr("href");
	var filter = div_grid.attr('data-filter');
	if ( filter == undefined ) filter = "";
	var ppp = div_grid.attr('data-ppp');
	var use_filter = div_grid.attr('data-use-filter');
	var aurl = div_grid.attr('data-aurl');
	var blogtype = div_grid.attr('data-blogtype');
	var col = div_grid.attr('data-cols');
	var cp_type = div_grid.attr('data-cp-type');
	var select = container.find('.filter');
	var selbox = "";
	var all = "";
	if (Boolean(select.length)){
		selbox = select.find(":selected")[0].value;
		all = select.find("option")[0].value;
	}
	var dta = new Array( link, col, all, use_filter, ppp, selbox, pid, blogtype, cp_type );

	if (all === filter || filter.length == 0 ) {
		//var dta = new Array(link, col, all, flags, ppp);
		jQuery.ajax({ url: aurl,
			data: {	action: dta	},
			type: 'post',
			'cache': 'true',
			success: function(data) {
				var new_items = jQuery('.item', jQuery(data));
				var old_items = div_grid.isotope('getItemElements');
				var old_pagination = container.find(".pagination");
				var new_pagination = jQuery('.pagination', jQuery(data));
				if (Boolean(old_pagination.length)) {
					old_pagination.fadeOut('300', function () {
						old_pagination.remove();
						if (Boolean(new_pagination.length)) {
							new_pagination.fadeOut('1');
							container.append(new_pagination);
							container.find(".pagination").fadeIn("300");
						}
					});
				}
				else {
					if (Boolean(new_pagination.length)){
						new_pagination.fadeOut('1');
						container.append(new_pagination);
						container.find(".pagination").fadeIn("600");
					}
				}
				update_grid( old_items, new_items, div_grid );
				if (Retina.isRetina()) {
					jQuery(window.retina.root).trigger("load");
				}
				jQuery('html, body').animate({
					scrollTop: jQuery(".photo_tour_section").offset().top - 60
				}, 1000);
			}
		});
	}
	else {
		//var dta = new Array(link, col, all, flags, ppp, filter);
		jQuery.ajax({ url: aurl,
			data: {	filter: dta, link: link	},
			type: 'post',
			'cache': 'true',
			success: function(data) {
				var new_items = jQuery('.item',jQuery(data));
				var old_items = div_grid.isotope('getItemElements');
				var old_pagination = container.find(".pagination");
				var new_pagination = jQuery('.pagination',jQuery(data));
				if (Boolean(old_pagination.length)){
					old_pagination.fadeOut('300', function (){
						old_pagination.remove();
						if (Boolean(new_pagination.length)){
							new_pagination.fadeOut('1');
							container.append(new_pagination);
							container.find(".pagination").fadeIn("300");
						}
					});
				}
				else{
					if (Boolean(new_pagination.length)){
						new_pagination.fadeOut('1');
						container.append(new_pagination);
						container.find(".pagination").fadeIn("600");
					}
				}
				update_grid( old_items, new_items, div_grid );
				if (Retina.isRetina()) {
					jQuery(window.retina.root).trigger("load");
				}
				jQuery('html, body').animate({
					scrollTop: jQuery(".photo_tour_section").offset().top - 60
				}, 1000);
			}
		});
	}
}
//jQuery('.photo_tour').on('click', '.pagination a', PortfolioPage(event) );
/*jQuery('.photo_tour_section .pagination a').on('click', function(e) {
	PortfolioPage(e);
} );*/

var userAgent = window.navigator.userAgent.toLowerCase(),
	ios = /iphone|ipod|ipad/.test( userAgent );

jQuery('.photo_tour_section').each( function (){
	var section = jQuery(this);
	section.find(".pagination a").live('click', function (e){
		if (ios) {
			jQuery(".toggle_sidebar .switcher").off("click");
		}
		PortfolioPage( section, jQuery(this), e );
	});
});

function PortfolioFilter(container, el) {
	var pid = getPageId();
	//var div_grid = container.find('.grid');
	var div_grid = container.parent().find('.grid');
	var filter = el.find(":selected")[0].value;
	var ppp = div_grid.attr('data-ppp');
	var use_filter = div_grid.attr('data-use-filter');
	var aurl = div_grid.attr('data-aurl');
	var blogtype = div_grid.attr('data-blogtype');
	var col = div_grid.attr('data-cols');
	var cp_type = div_grid.attr('data-cp-type');
	var all = el.find("option")[0].value;
	var dta = new Array(filter, col, all, use_filter, ppp, filter, pid, blogtype, cp_type );

	jQuery.ajax({ url: aurl,
		data: {	filter: dta	},
		type: 'post',
		success: function(data) {
			var new_items = jQuery('.item',jQuery(data));
			var old_items = div_grid.isotope('getItemElements');
			var old_pagination = container.find(".pagination");
			var new_pagination = jQuery('.pagination',jQuery(data));
			if (Boolean(old_pagination.length)){
				old_pagination.fadeOut('300', function (){
					old_pagination.remove();
					if (Boolean(new_pagination.length)){
						new_pagination.fadeOut('1');
						container.append(new_pagination);
						container.find(".pagination").fadeIn("300");
					}
				});
			}
			else{
				if (Boolean(new_pagination.length)){
					new_pagination.fadeOut('1');
					container.append(new_pagination);
					container.find(".pagination").fadeIn("600");
				}
			}
			update_grid( old_items, new_items, div_grid );
			if (Retina.isRetina()) {
				jQuery(window.retina.root).trigger("load");
			}
		}
	});
}

jQuery('.photo_tour_section,.photo_tour_section_header').each( function (){
	var section = jQuery(this);
	section.find(".filter").live("change", function (e){
		e.stopPropagation();
		if (ios) {
			jQuery(".toggle_sidebar .switcher").off("click");
		}
		PortfolioFilter( section, jQuery(this) );
	});
});

});


