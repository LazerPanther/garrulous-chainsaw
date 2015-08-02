(function() {
	var DOM = tinymce.DOM;

	tinymce.create('tinymce.plugins.cws_tmce', {
		init : function(ed, url) {
			var disabled = true;
			var t = this;
			var selection;

			ed.addButton('mark', {
				title : 'Mark selection',
				onclick : function() {
					selection = ed.selection.getContent();
					id = 'mark';
					tb_show("Insert Shortcode: " + id, url +
						'/popup.php?shortcode=' + id + '&sel=' + encodeURIComponent(selection) + '&width=600&height=640' );
				}
			});

			ed.onNodeChange.add(function(ed, cm, n, co) {
				disabled = co;
				selection = ed.selection.getContent();
				var sellen = selection.length;
				if (disabled) {
					cm.setDisabled('mark', true);
					cm.setDisabled('dropcap', true);
					cm.setDisabled('shortcode_carousel', true);
				}
				else {
					cm.setDisabled('mark', false);
					cm.setDisabled('dropcap', (1 == sellen) ? false : true);
					cm.setDisabled('shortcode_carousel', false);
				}
			});

			ed.addButton('cws_button', {
				title : 'Button',
				onclick : function (){
					selection = ed.selection.getContent();
					id = 'cws_button',
					tb_show("Insert Shortcode: " + id, url +
						'/popup.php?shortcode=' + id + '&width=600&height=600' )
				}
			});

			ed.addButton('services', {
				title : 'Services',
				//image : url+'/button-progress.png',
				onclick : function() {
					//ed.selection.setContent('[progress width="50%"]');
					id = 'services';
					tb_show("Insert Shortcode: " + id, url +
						'/popup.php?shortcode=' + id + '&width=600&height=600' );
				}
			});

			ed.addButton('progress', {
				title : 'Percent bar',
				onclick : function() {
					//ed.selection.setContent('[progress width="50%"]');
					id = 'progress';
					tb_show("Insert Shortcode: " + id, url +
						'/popup.php?shortcode=' + id + '&width=600&height=650' );
				}
			});

			ed.addButton('milestone', {
				title : 'Milestone',
				onclick : function() {
					//ed.selection.setContent('[progress width="50%"]');
					id = 'milestone';
					tb_show("Insert Shortcode: " + id, url +
						'/popup.php?shortcode=' + id + '&width=600&height=650' );
				}
			});

			ed.addButton('tweets', {
				title : 'Tweets',
				onclick : function() {
					id = 'tweets';
					tb_show("Insert Shortcode: " + id, url +
						'/popup.php?shortcode=' + id + '&width=600&height=400' );
				}
			});

			ed.addButton('embed', {
				title : 'Embed audio/video file',
				onclick : function() {
					//ed.selection.setContent('[youtube id="Video ID (eg. Abc14deFghI5)" width="730" height="350"]');
					id = 'embed';
					tb_show("Insert Shortcode: " + id, url +
						'/popup.php?shortcode=' + id + '&width=600&height=400' );
				}
			});

			ed.addButton('quote', {
				title : 'Quote',
				onclick : function() {
					selection = encodeURIComponent( ed.selection.getContent() );
					var rng = ed.selection.getRng(true);
					id = 'quote';
					source = url + '/popup.php?shortcode=' + id + '&sel=' + selection;
					title = "Insert Shortcode: " + id;
					cws_tb_modal_show( source, "get", title , 600, 500 );
/*					tb_show("Insert Shortcode: " + id, url +
						'/popup.php?shortcode=' + id + '&sel=' + selection + '&width=600&height=600' );*/

				}
			});

			ed.addButton('fa', {
				title : 'FontAwesome icon',
				onclick : function() {
					selection = encodeURIComponent( ed.selection.getContent() );
					//console.log( getWordAt( rng.startContainer.textContent, rng.startOffset ) );
					//console.log(document.styleSheets);
					id = 'fa';
					tb_show("Insert Shortcode: " + id, url +
						'/popup.php?shortcode=' + id + '&sel=' + selection + '&width=600&height=850' );
				}
			});

			ed.addButton('featured_fa', {
				title : 'Featured icon',
				onclick : function() {
					selection = encodeURIComponent( ed.selection.getContent() );
					//console.log( getWordAt( rng.startContainer.textContent, rng.startOffset ) );
					//console.log(document.styleSheets);
					id = 'featured_fa';
					tb_show("Insert Shortcode: " + id, url +
						'/popup.php?shortcode=' + id + '&sel=' + selection + '&width=600&height=850' );
				}
			});

			ed.addButton('dropcap', {
				title : 'Drop cap',
				onclick : function() {
					selection = encodeURIComponent( ed.selection.getContent() );
					if(window.tinyMCE) {
						window.tinyMCE.activeEditor.selection.setContent("[dropcap]" + selection + "[/dropcap]");

					}
				}
			});

			ed.addButton('alert', {
				title : 'Info box',
				onclick : function() {
					selection = encodeURIComponent( ed.selection.getContent() );
					id = 'alert';
					tb_show("Insert Shortcode: " + id, url +
						'/popup.php?shortcode=' + id + '&sel=' + selection + '&width=600&height=450' );

					//ed.selection.setContent('[alert type="information"]Information[/alert]');
				}
			});

			ed.addButton('cws_cta', {
				title : 'Call to action button',
				onclick : function() {
					selection = encodeURIComponent( ed.selection.getContent() );
					id = 'cws_cta';
					tb_show("Insert Shortcode: " + id, url +
						'/popup.php?shortcode=' + id + '&sel=' + selection + '&width=600&height=500' );
					jQuery("#TB_WINDOW").attr("id","#CWS_TB_WINDOW");
					jQuery("#TB_imageOff").attr("id","#CWS_TB_imageOff");
					jQuery("#TB_closeWindowButton").attr("id","#CWS_TB_closeWindowButton");

					//ed.selection.setContent('[alert type="information"]Information[/alert]');
				}
			});


			ed.addButton('price-table', {
				title : 'Pricing table',
				onclick : function() {
					id = 'price-table';
					tb_show("Insert Shortcode: " + id, url +
						'/popup.php?shortcode=' + id + '&width=600&height=600' );

					//ed.selection.setContent('[alert type="information"]Information[/alert]');
				}
			});

			ed.addButton('ourteam', {
				title : 'Our team',
				onclick : function() {
					id = 'ourteam';
					tb_show("Insert Shortcode: " + id, url +
						'/popup.php?shortcode=' + id + '&width=600&height=600' );
				}
			});

			ed.addButton('portfolio', {
				title : 'Portfolio',
				onclick : function() {
					id = 'portfolio';
					tb_show("Insert Shortcode: " + id, url +
						'/popup.php?shortcode=' + id + '&width=800&height=600' );
				}
			});

			ed.addButton('shortcode_blog', {
				title : 'Blog',
				onclick : function() {
					id = 'shortcode_blog';
					tb_show("Insert Shortcode: " + id, url +
						'/popup.php?shortcode=' + id + '&width=800&height=600' );
				}
			});

			ed.addButton('shortcode_carousel', {
				title : 'Carousel',
				onclick : function() {
					selection = ed.selection.getContent();
					id = 'shortcode_carousel';
					tb_show("Insert Shortcode: " + id, url +
						'/popup.php?shortcode=' + id + '&sel=' + encodeURIComponent(selection) + '&width=600&height=300' );
				}
			});

		},
	});
	tinymce.PluginManager.add('cws_tmce', tinymce.plugins.cws_tmce);

})();
