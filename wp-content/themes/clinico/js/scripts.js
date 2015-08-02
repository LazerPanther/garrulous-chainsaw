"use strict";
jQuery(document).ready(function (){
	jQuery(".search-form [type='submit']").on("mouseover", function(){
		jQuery(this).prev().addClass("button-hover");
	})
	jQuery(".search-form [type='submit']").on("mouseleave", function(){
		jQuery(this).prev().removeClass("button-hover");
	})
	touch_devices_hover_fix();
	widget_carousel_init();
	wp_gallery_init();
	fancy_box_init();
	load_more_init();
	scrollup_init();
	resize_controller_init();
	lang_sel_click_toggle();
});
jQuery(window).load(function (){
	find_a_doctor_init();
	sticky_init();
	gallery_post_carousel_init();
	isotope_init();
	shortcode_carousel_init();
	carousel_init();
	rev_slider_bullets_init ();
});
function touch_devices_hover_fix(){
	if ( jQuery("html").hasClass("touch") && ( is_safari_ios() ) ){
		jQuery(".page-content").on( "hover", ".news .pic, .news .pic .links, .news .pic img, .woocommerce .products *, .product .pic, .product .pic .links,  .product .pic .img", function(){});
		jQuery(".page-content").on( "hover", ".fancy", function(){
			if (jQuery(this).is(":hover")){
				jQuery(this).trigger("click");
			}
		});
		jQuery(document).on("hover",".fancybox-close, .fancybox-next, .fancybox-prev",function(){
			jQuery(this).trigger("click");
		});
	}
}
function resize_controller_init (){
	window.is_mobile_prev = is_mobile();
	jQuery(window).resize( function (){
		window.is_mobile_next = is_mobile();
		if ( window.is_mobile_prev && !window.is_mobile_next ){
			find_a_doctor_init();
		}
	});
}
function find_a_doctor_init(){
	var switcher = jQuery(".toggle_sidebar .switcher");
	var rtl = cws_is_rtl();
	if (switcher.length){
		var switcher_w = switcher.outerWidth();
		var side_frame_w = switcher_w / 2;

		var switcher_shadow = jQuery(".toggle_sidebar .switcher_shadow");
		var switcher_shadow_w = switcher_shadow.outerWidth();

		var language_bar = jQuery('.toggle_sidebar .cws_language_bar');

		var crumbs = jQuery('.page-title .bread-crumbs');

		jQuery("head").append("<style id='toggle_sidebar_switcher_style' type='text/css'>.toggle_sidebar .switcher:after{ border-left-width:" + side_frame_w + "px; border-right-width:" + side_frame_w + "px; border-top-width: 15px; }</style>");
		if ( switcher_w > switcher_shadow_w ){
			switcher_shadow.css( ( rtl ? "left" : "right" ), side_frame_w + "px" );
		}
		else{
			switcher_shadow.css("display", "none");
		}
		if ( crumbs.length ){
			crumbs.css( ( rtl ? "margin-left" : "margin-right" ), ( switcher_w + 30 ) + "px" );
		}

		var toggle_item = jQuery(".toggle_sidebar #toggle_sidebar_area");
		jQuery(".toggle_sidebar .switcher").on("click", function (e) {
			toggle_item.slideToggle('300');
		});
	}
}

function rev_slider_bullets_init (){
	var bullets = jQuery(".rev_slider_wrapper .tp-bullets");
	var set_bullets = false;
	if (bullets.length){
		if ( is_mobile_device() || is_mobile() ){
			attach_bullets();
		}
		else if ( !( is_mobile_device() || is_mobile() ) ){
			detach_bullets();
		}
		jQuery(window).resize( rev_slider_bullets_controller );
	}
	function attach_bullets(){
		bullets.css( "display", "block" );
		set_bullets = true;
	}
	function detach_bullets(){
		bullets.css( "display", "none" );
		set_bullets = false;
	}
	function rev_slider_bullets_controller (){
		if ( ( is_mobile_device() || is_mobile() ) && !set_bullets ){
			attach_bullets();
		}
		else if ( !( is_mobile_device() || is_mobile() ) && set_bullets ){
			detach_bullets();
		}
	}
}

function carousel_init (){
	jQuery(".carousel").each(function (){
		jQuery(this).owlCarousel({
		itemsCustom : [
			[0,1],
			[479,2],
			[980,3],
			[1170,4]
		],
		navigation: false,
		});
		var owl = jQuery(this);
		jQuery(this).parents(".cws_widget").find(".carousel_nav").each(function (){
			jQuery(this).children(".next").click(function(){
				owl.trigger('owl.next');
			})
			jQuery(this).children(".prev").click(function (){
				owl.trigger('owl.prev');
			});
		});
	});
}
function cuniq() {
	var c, d, m, u;
	c = Math.random();
    d = new Date();
    m = d.getMilliseconds() + "";
    u = Math.floor( ++d + m + (++c === 10000 ? (c = 1) : c) );
    return u;
}
function wp_gallery_init (){
	jQuery(document).on( 'click', '.gallery-icon>a', function (e){
		e.preventDefault();
	});
	jQuery(".gallery").each(function (){
		var items = jQuery(this).find(".gallery-item");
		var unique_id = cuniq();
		items.each( function (){
			var image_container = jQuery(this).find("img").parent();
			image_container.append("<div class='hover-effect'></div>");
			var link = jQuery(this).find("a");
			if (link.length){
				var fancy = false;
				var href = link.attr('href');
				if (/\/uploads\//.test(href)){
					fancy = true;
				}
				image_container.append("<div class='links'><a class='fa " + ( fancy ? "fa-eye fancy" : "fa-link" ) + "' href='" + href + "' data-fancybox-group='fancybox_group_" + unique_id + "'></a></div>");
			}
		});
	});
}
function shortcode_carousel_init (){
	jQuery(".shortcode_carousel").each( function (){
		var gallery = Boolean(jQuery(this).find(".gallery").length);
		var blog = Boolean(jQuery(this).find(".blog").length);
		var woo = Boolean(jQuery(this).find(".woocommerce").length);
		var owl = "";
		if (gallery){
			owl = jQuery(this).find(".gallery");
			var cols =/gallery-columns-([0-9]+)/.exec(owl.attr("class"))[1];
			owl.children(":not(.gallery-item)").remove();
			owl.owlCarousel({
				itemsCustom : [
					[0,1],
					[479,2],
					[980,3],
					[1170,cols]
				]
			});
		}
		else if (blog){
			try{
				jQuery(this).find(".isotope").isotope('destroy');
			}
			catch(e){
				;
			}
			owl = jQuery(this).find(".grid");
			var cols =/news-([0-9]+)/.exec(owl.parent().attr("class"))[1];
			owl.children(":not(.item)").remove();
			var blog_wrapper = jQuery(this).find(".news");
			if ( blog_wrapper.length ) blog_wrapper.removeClass("news-pinterest news-4 news-3 news-2");
			owl.owlCarousel({
				itemsCustom : [
					[0,1],
					[479,2],
					[980,3],
					[1170,cols]
				]
			});
		}
		else if (woo){
			owl = jQuery(this).find(".woocommerce .products");
			owl.owlCarousel({
				itemsCustom : [
					[0,1],
					[479,2],
					[980,3],
					[1170,4]
				]
			});
		}
		if (owl){
			jQuery(this).find(".carousel_nav").each(function (){
				jQuery(this).children(".next").click(function(){
					owl.trigger('owl.next');
				})
				jQuery(this).children(".prev").click(function (){
					owl.trigger('owl.prev');
				});
			});
		}
	});
}
function widget_carousel_init (){
	jQuery(".widget_carousel").each(function(){
		jQuery(this).owlCarousel({
			singleItem:true,
			slideSpeed:300,
			navigation: false,
			pagination: false
		});
		var owl = jQuery(this);
		jQuery(this).parents(".cws-widget").find(".widget_carousel_nav").each(function (){
			jQuery(this).children(".next").click(function(){
				owl.trigger('owl.next');
			})
			jQuery(this).children(".prev").click(function (){
				owl.trigger('owl.prev');
			});
		});
	});
}
function gallery_post_carousel_init (){
	jQuery(".gallery_post_carousel").each(function(){
		jQuery(this).owlCarousel({
			singleItem:true,
			slideSpeed:300,
			navigation: false,
			pagination: false
		});
		var owl = jQuery(this);
		jQuery(this).parent().find(".gallery_carousel_nav").each(function (){
			jQuery(this).children(".next").click(function(){
				owl.trigger('owl.next');
			})
			jQuery(this).children(".prev").click(function (){
				owl.trigger('owl.prev');
			});
		});
	});
}
function fancy_box_init (){
		jQuery(".fancy:not(.fancy_gallery)").fancybox({
			openEffect:'elastic',
			closeEffect:'elastic',
			openSpeed:400,
			closeSpeed:400
		})
		jQuery(".fancy.fancy_gallery").fancybox({
			openEffect : 'elastic',
			closeEffect : 'elastic',
			openSpeed : 400,
			closeSpeed : 400,
			prevEffect : 'fade',
			nextEffect : 'fade',
			prevSpeed : 400,
			nextSpeed: 400
		});
}

function is_mobile (){
	return window.innerWidth < 768;
}

function is_mobile_device (){
	return jQuery("html").hasClass("touch");
}

function is_safari_ios (){
	var browser =  navigator.userAgent;
	return ( /(iPod|iPhone|iPad)/.test(browser) && /AppleWebKit/.test(browser) && /OS 7/.test(browser) );
}

/* sticky */

function get_logo_position(){
	return /logo-\w+/.exec(jQuery(".page-header").attr("class"))[0];
}
function sticky_vars (){
	window.sticky_menu = {'page_header':jQuery(".page-header"),
							'logo_position':get_logo_position(),
							'menu_container':jQuery('.page-header .main-nav-container'),
							'is_set':false,
							'logo_init_height':jQuery(".page-header .logo>img").height(),
							'first_level_menu_item':jQuery(".page-header .main-menu>.menu-item>a"),
							'menu_item_padding_top':24,
							'menu_item_padding_bottom':14,
							'logo_indent':10,
							'logo_top_init_margin':parseInt(jQuery(".page-header .logo>img").css("margin-top")),
							'logo_bottom_init_margin':parseInt(jQuery(".page-header .logo>img").css("margin-bottom"))};
	window.sticky_menu.menu_offset = window.sticky_menu.logo_position == "logo-center" ? jQuery(".main-nav-container").offset().top : window.sticky_menu.page_header.find(".container").offset().top;
}
function sticky_init (){
	sticky_vars();
	if ( window.stick_menu == 1 && !is_mobile_device() ){
		sticky();
		jQuery(window).scroll(sticky);
		jQuery(window).resize( function (){
			if ( (is_mobile()) && (window.sticky_menu.is_set) ){
				reset_sticky();
			}
			else if ( !is_mobile() ){
				if ( (jQuery(document).scrollTop()>window.sticky_menu.menu_offset) && (!window.sticky_menu.is_set) ){
					set_sticky();
					window.sticky_menu.menu_container.find(".menu-item.active").removeClass("active").find(".sub-menu").hide("1");
				}
			}
		} );
	}
	else if ( is_mobile_device() && window.sticky_menu.logo_top_init_margin ){
		window.sticky_menu.menu_container.css( "margin-top", window.sticky_menu.logo_top_init_margin + "px" );
	}
}
function sticky (){
	if ( !is_mobile() ){
		if ( (jQuery(document).scrollTop()>window.sticky_menu.menu_offset) && (!window.sticky_menu.is_set) ){
			set_sticky();
		}
		else if ( (jQuery(document).scrollTop()<=window.sticky_menu.menu_offset) && (window.sticky_menu.is_set) ){
			reset_sticky();
		}
	}
}
function set_sticky (){
		window.sticky_menu.page_header.css("height",window.sticky_menu.page_header.outerHeight());
		window.sticky_menu.page_header.addClass("sticky");
		var width = Boolean( jQuery(".page_boxed").length ) ? jQuery(".page_boxed").outerWidth() + "px" : "100%";
		var left = Boolean( jQuery(".page_boxed").length ) ? jQuery(".page_boxed").offset().left + "px" : "0";
		if (window.sticky_menu.logo_position!="logo-center"){
			window.sticky_menu.page_header.find(".container").css({"position":"fixed",
				"width":width,
				top:0,
				"left":left,
				'margin-top':"0px"});
			window.sticky_menu.page_header.find(".logo>img").css({"height":String(window.sticky_menu.menu_container.find(".main-menu>.menu-item>a").eq(0).height()+window.sticky_menu.menu_item_padding_top+window.sticky_menu.menu_item_padding_bottom-(window.sticky_menu.logo_indent*2))+"px",
				'margin-top':window.sticky_menu.logo_indent+'px',
				'margin-bottom':window.sticky_menu.logo_indent+'px'});
		}
		else{
			window.sticky_menu.menu_container.css({"position":"fixed",
				"width":width,
				top:0,
				"left":left});
			}
		window.sticky_menu.first_level_menu_item.css({'padding-top':window.sticky_menu.menu_item_padding_top+'px','padding-bottom':window.sticky_menu.menu_item_padding_bottom+'px'});
		window.sticky_menu.is_set = true;
}
function reset_sticky (){
		window.sticky_menu.page_header.removeClass("sticky");
		if (window.sticky_menu.logo_position!="logo-center"){
			window.sticky_menu.page_header.find(".container").removeAttr("style");
			window.sticky_menu.page_header.find(".logo>img").css({"height":window.sticky_menu.logo_init_height+"px",
				"margin-top":window.sticky_menu.logo_top_init_margin+"px",
				"margin-bottom":window.sticky_menu.logo_bottom_init_margin+"px"});
		}
		else{
			window.sticky_menu.menu_container.removeAttr("style");
		}
		window.sticky_menu.page_header.removeAttr("style");
		window.sticky_menu.first_level_menu_item.removeAttr("style");
		window.sticky_menu.is_set = false;
}

/* isotope */

function isotope_init (){
	jQuery(".news .isotope").isotope({
	itemSelector: '.item',
	});
	jQuery(".photo_tour .isotope, .our_team .isotope").isotope({
	itemSelector: '.item',
	getSortData: {
		cat: "[data-category]",
	},
	});
}

function load_more_init (){
	jQuery(".load_more").each(function (){
		jQuery(this).on("click", function (){
			jQuery('>i', this).toggleClass('fa-spin');
			var selector = jQuery(this).attr("data-selector");
			var paged = jQuery(this).attr("data-paged");
			var max_paged = jQuery(this).attr("data-max-paged");
			var post_id = jQuery(this).attr("data-post-id");
			var template_dir = jQuery(this).attr("data-template-directory");
			var that = this;
			if ( (!selector) || (!paged) || (!max_paged) || (!parseInt(paged)) || (!post_id) || (!template_dir) ){
				return;
			}
			paged ++;
			jQuery(this).attr("data-paged",String(paged));
			jQuery.post( template_dir + "/blog.php", { paged : paged, ajax : true, post_id : post_id } ).done( function( data ) {
				var new_items = jQuery( ".item", jQuery(data) );
				new_items.css('display','none');
				jQuery(selector).append( new_items );
				var img_loader = imagesLoaded( jQuery(selector) );
				img_loader.on ('always', function (){
					jQuery(selector).isotope( 'appended', new_items);
					reload_scripts();
					jQuery(selector).isotope('layout');
					if (Retina.isRetina()) {
					jQuery(window.retina.root).trigger("load");
					}
					});
					jQuery('>i', that).toggleClass('fa-spin');
			});
			if (max_paged==paged) {
				var that = this;
				setTimeout(function() {
						jQuery(that).hide(500);
					}, 1500);
			}
		});
	});
}

var userAgent = window.navigator.userAgent.toLowerCase(),
	ios = /iphone|ipod|ipad/.test( userAgent );

function reload_scripts (){
	if (ios) {
		jQuery(".toggle_sidebar .switcher").off("click");
	}
	fancy_box_init();
	gallery_post_carousel_init();
}

/****************** PB ********************/

function cws_tabs_init (){
	jQuery.fn.cws_tabs = function (){
		jQuery(this).each(function (){
			var parent = jQuery(this);
			var tabs = parent.find("[role='tab']");
			var tab_items_container = parent.find("[role='tabpanel']").parent();
			tabs.each(function(){
				jQuery(this).on("click", function (){
					var active_ind = jQuery(this).siblings(".active").eq(0).attr("tabindex");
					jQuery(this).addClass("active").siblings().removeClass("active");
					var item = tab_items_container.find("[tabindex='"+this.tabIndex+"']");
					item.siblings("[tabindex='"+active_ind+"']").eq(0).fadeToggle("150",'swing',function(){
						item.fadeToggle("150");
					});
				});
			});
		});
	}
}

function cws_accordion_init (){
	jQuery.fn.cws_accordion = function () {
		jQuery(this).each(function (){
			var sections = jQuery(this).find(".accordion_section");
			sections.each( function (index, value){
				var section_index = index;
				jQuery(this).find(".accordion_title").on("click", function (){
					jQuery(this).siblings(".accordion_content").slideDown("300");
					sections.eq(section_index).addClass("active");
					sections.eq(section_index).siblings().removeClass("active").find(".accordion_content").slideUp("300");
				});
			});
		});
	}
}

function cws_toggle_init (){
	jQuery.fn.cws_toggle = function ( item_class, opener_class, toggle_section_class ){
		var i=0;
		jQuery(this).each( function (){
			i++;
			var sections = jQuery(this).find("."+item_class);
			var j=0;
			sections.each( function (index, value){
				j++;
				var section_index = index;
				jQuery(this).find("."+opener_class).eq(0).on("click", function (){
					if (!sections.eq(section_index).hasClass("active")){
						sections.eq(section_index).addClass("active");
						sections.eq(section_index).find("."+toggle_section_class).eq(0).slideDown("300");
					}
					else{
						sections.eq(section_index).removeClass("active");
						sections.eq(section_index).find("."+toggle_section_class).eq(0).slideUp("300");
					}
				});
			});
		});
	}
}

jQuery(document).ready(function (){
	/* init plugins */
	is_visible_init ();
	cws_tabs_init ();
	cws_accordion_init ();
	cws_toggle_init ();
	cws_progress_bar_init ();
	cws_milestone_init ();
	/* \init plugins */
	jQuery(".cws_widget_content.tab_widget").cws_tabs();
	jQuery(".cws_widget_content.accordion_widget").cws_accordion();
	jQuery(".cws_widget_content.toggle_widget,.services").cws_toggle("accordion_section","accordion_title","accordion_content");
	jQuery(".main-menu").cws_toggle("menu-item","button_open","sub-menu");
	jQuery(".page-header").cws_toggle("main-nav-container","mobile_menu_header","main-menu");
	jQuery(".single_bar").cws_progress_bar();
	jQuery(".milestone").cws_milestone();
	message_box_deactivating ();
	custom_colors_init ();
});

function message_box_deactivating (){
	jQuery(document).on("click",".message_box",function (){
		var el = jQuery(this);
		el.fadeToggle("300","swing",function(){
			el.remove();
		});
	});
}

function custom_colors_init (){
	jQuery(".custom_color").each(function (){
		var button_color = jQuery(this).attr("data-bg-color");
		var text_color = jQuery(this).attr("data-font-color");
		var border_color = jQuery(this).attr("data-border-color");
		jQuery(this).css({"background-color":button_color,"color":text_color,"border-color":border_color});
		jQuery(this).on("mouseover", function (){
			jQuery(this).css({"background-color":text_color,"color":button_color,"border-color":text_color});
		});
		jQuery(this).on("mouseout", function (){
			jQuery(this).css({"background-color":button_color,"color":text_color,"border-color":border_color});
		});
	});
}

/* PROGRESS BAR */

function cws_progress_bar_init (){
	jQuery.fn.cws_progress_bar = function (){
		jQuery(this).each( function (){
			var el = jQuery(this);
			var done = false;
			if (!done) done = progress_bar_controller(el);
			jQuery(window).scroll(function (){
				if (!done) done = progress_bar_controller(el);
			});
		});
	}
}

function progress_bar_controller (el){
	if (el.is_visible()){
		var progress = el.find(".progress");
		var value = parseInt( progress.attr("data-value") );
		var width = parseInt(progress.css('width').replace(/%|(px)|(pt)/,""));
		var ind = el.find(".indicator");
		var progress_interval = setInterval( function(){
			width ++;
			progress.css("width", width+"%");
			ind.text(width);
			if (width == value){
				clearInterval(progress_interval);
			}
		}, 5);
		return true;
	}
	return false;
}

function is_visible_init (){
	jQuery.fn.is_visible = function (){
		return ( jQuery(this).offset().top >= jQuery(window).scrollTop() ) && ( jQuery(this).offset().top <= jQuery(window).scrollTop() + jQuery(window).height() );
	}
}

/* MILESTONE */

function cws_milestone_init (){
	
	jQuery.fn.cws_milestone = function (){
		jQuery(this).each( function (){
			
			var el = jQuery(this);
			var number_container = el.find(".number");
			var done = false;
			if (number_container.length){
				if ( !done ) done = milestone_controller (el, number_container);
				jQuery(window).scroll(function (){
					if ( !done ) done = milestone_controller (el, number_container);
				});
			}
		});
	}
}

function milestone_controller (el, number_container){
	
if (el.is_visible()){
	var number = parseInt(number_container.attr("data-number"));
	var n = number;
	var digits = [];
	var k = 0;
	while ( n>=1 ){
		n/=10;
		
		digits[k] = Math.round(n % 1 * 10);
		n = Math.floor(n);
		k++;
	}
	var n = 0;
	var interval = setInterval ( function (){

		var digit = digits.length;
		if (digits[digit-1] == 0) {
			digit--;
			while (digits[digit-1] === 0) digit--;
		}
		var digit_number = get_digit(n, digit);
		n += Math.pow(10, digit-1);
		if (n<=number) {
			number_container.text( String(n) );
		}
		if (get_digit(n, digit) == digits[digit-1]){
			digits.splice(digit-1,1);
		}
		if (!digits.length) clearInterval( interval );
	}, 30);
	return true;
	}
return false;
}

function get_digit (number, digit){
	
	var exp = Math.pow(10, digit);
	return Math.round(number/exp%1*10);
}

/****************** \PB ********************/

/* WPML */

function lang_sel_click_toggle(){
	jQuery("#lang_sel_click.lang_sel_click").removeAttr("onclick");
	jQuery("#lang_sel_click.lang_sel_click .lang_sel_sel").on( "click",function (){
		jQuery(this).next("ul").slideToggle();
	});
	if (is_mobile_device()){
		jQuery("#lang_sel .lang_sel_sel").on( "click",function (e){
			e.preventDefault();
			jQuery(this).next("ul").slideToggle();
		});
	}
}

/* SCROLLUP */

function scrollup_init (){
	jQuery("#scrollup").on("click", function (){
	  jQuery('html, body').stop().animate({
		 scrollTop: 0
	  }, 750);
	})
}

/* LAVALAMP */

function cws_lavalamp_init (){
	jQuery.fn.cws_lavalamp = function ( args ){
		args = args ? args : {};
		args['skin'] = args['skin'] ? args['skin'] : { 'border' : '1px solid #e3e3e3', 'padding' : '10px', 'margin-left' : '-11px', 'margin-top' : '-11px' };
		args['speed'] = args['speed'] ? args['speed'] : 300;
		args['easing'] = args['easing'] ? args['easing'] : 'swing';
		args['skin']['position'] = 'absolute';
		args['skin']['z-index'] = '0';
		cont_obj = jQuery(args['cont_sel']);
		cont_obj = cont_obj.length ? cont_obj :  jQuery(this);
		if ( !cont_obj ) return;
		function lavalamp_controller (){
			var items, active, lava;
			var cont = jQuery(this);
			items = cont.find(args['items_sel']);
			items = items.length ? items : cont.children();
			if (!items)	return;
			active = items.filter(args['active_sel']);
			active = active.length ? active : items.eq(0);
			if(!active)	return;
			cont.css( "position", "relative" );
			items.css( { "position" : "relative", "z-index" : "1" } );
			cont.append("<div class='cws_lava'></div>");
			lava = cont.children(".cws_lava").eq(0);
			lava.css( args['skin'] );
			set_lava( active, false );
			jQuery(items).on( "hover", function (){
				set_lava(jQuery(this));
			});
			function set_lava ( item, animate ){
				var animate = animate == undefined ? true : animate;
				var width = item.outerWidth();
				var height = item.outerHeight();
				var left = item.position().left;
				var top = item.position().top;
				var params = { left: left, width: width, top: top, height: height };
				if ( animate ){
					jQuery(lava).stop().animate( params, args['speed'], args['easing'] );
				}
				else{
					jQuery(lava).css( params );
				}
			}
			cont.on('mouseleave', function(){
				set_lava( active );
			});
		}
		cont_obj.each( lavalamp_controller );
	}
}

/* RTL */

function cws_is_rtl(){
	return jQuery("body").hasClass("rtl");
}

/***********************************************/

/* jQuery(document).ready(function (){
	setTimeout(function (){
		jQuery("#tribe-bar-collapse-toggle").live("click",function (){
			jQuery(this).addClass("class-1 class-2");
			jQuery(".tribe-bar-filters").toggleClass("active");
			jQuery(this).live("click",function(){
				jQuery(".tribe-bar-filters").slideToggle(300);
			})
		});
	}, 2000);
}); */