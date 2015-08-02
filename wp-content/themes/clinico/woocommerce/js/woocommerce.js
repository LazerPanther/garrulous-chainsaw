"use strict";

/**/
	/* MARK */
	/**/
	jQuery(document).ready(function ($){
		$(".stars").ready(function (){
			var rtl = typeof cws_is_rtl == 'function' ? cws_is_rtl() : false;
			var stars_active = false;
			$(".woocommerce .stars").on("mouseover", function(){
				if (!stars_active){
					$(this).find("span:not(.stars-active)").append("<span class='stars-active' data-set='no'>&#xf005;&#xf005;&#xf005;&#xf005;&#xf005;</span>");
					stars_active = true;
				}
			});
			$(".woocommerce .stars").on("mousemove", function (e){
				var width = $(this).width();
				var cursor = e.pageX;
				var ofs = $(this).offset().left;
				var fill = rtl ? width - ( cursor - ofs ) : cursor - ofs;
				var persent = Math.round(100*fill/width);
				$(".woocommerce .stars .stars-active").css("width",String(persent)+"%");
			});
			$(".woocommerce .stars").on("click", function (e){
				var width = $(this).width();
				var cursor = e.pageX;
				var ofs = $(this).offset().left;
				var fill = rtl ? width - ( cursor - ofs ) : cursor - ofs;
				var persent = Math.ceil( Math.round( 100 * ( fill/width ) ) / 20 ) * 20;
				var mark = $(this).find(".stars-active");
				mark.css('width',String(persent)+"%");
				mark.attr("data-set",String(persent));
			});
			$(".woocommerce .stars").on("mouseleave", function (e){
				var mark = $(this).find(".stars-active");
				if (mark.attr("data-set") == "no"){
					mark.css("width","0");
				}
				else{
					var persent = mark.attr("data-set");
					mark.css("width",String(persent)+"%");
					$(".stars-active").addClass("fixed-mark");
				}
			});
		});
	})


/* Search icon hover */
jQuery(document).ready(function ($){
	$( "#searchform #searchsubmit" ).mouseover(function() {
  		$("#searchform div").addClass( "hover-search" );
	});
	$( "#searchform #searchsubmit" ).mouseout(function() {
  		$("#searchform div").removeClass( "hover-search" );
	});
});

/* Search icon hover */

/****************** \PB ********************/

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