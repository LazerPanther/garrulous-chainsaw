"use strict";
(function() {
	var DOM = tinymce.DOM;

	tinymce.create('tinymce.plugins.mark', {
		init : function(ed, url) {
			var disabled = true;
			var selection;
			ed.addButton('mark', {
				title : 'Mark selection',
				onclick : function() {
					selection = ed.selection.getContent();
					id = 'mark';
					tb_show("Insert Shortcode: " + id, url.substring(0, url.lastIndexOf("/")) +
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
				}
				else {
					cm.setDisabled('mark', false);
					cm.setDisabled('dropcap', (1 == sellen) ? false : true);
				}
			});
		},
	});
	tinymce.PluginManager.add('mark', tinymce.plugins.mark);

	tinymce.create('tinymce.plugins.enable_cws_bar', {
		init : function(ed, url) {
				var t = this;
				//var id = '';
				var selection = '';
				var tb3Id = ed.getParam('wordpress_adv_toolbar', 'toolbar2');

				ed.onDblClick.add(function(ed, e) {
					/*
					var r = ed.selection.getRng(true);
					var rn = DOM.createRng();
					//var textNode = ed.getDoc().getElementsByTagName('p')[0].firstChild;
					//var textNode = ed.selection.getNode().firstChild;
					var nStart = ((r.startOffset - 5) > 0) ? r.startOffset - 5 : 0;
					var nEnd = ((r.endOffset + 5) < r.commonAncestorContainer.length) ? r.endOffset + 5 : r.commonAncestorContainer.length;
					rn.setStart(r.commonAncestorContainer, nStart);
					rn.setEnd(r.commonAncestorContainer, nEnd);
					ed.selection.setRng(rn);
					ed.selection.setContent('sample');
					*/

					/*
					var sc_body = getShortcodeAt1( r.startContainer.textContent, r.startOffset );
					if (sc_body) {
						alert(sc_body);
					}
					*/
				});

				if ( getUserSetting('hidetb3', '0') == '1' )
					ed.settings.cws_tb3_hidden = 0;

				ed.onPostRender.add(function() {
					var cws_toolbar = ed.controlManager.get(tb3Id);
					if ( ed.getParam('cws_tb3_hidden', 1) && cws_toolbar ) {
						DOM.hide(cws_toolbar.id);
						//t._resizeIframe(ed, tb3Id, 28);
					}
				});
			if (tinymce.majorVersion < 4) {
				ed.addButton('enable_cws_bar', {
					title : 'enable cws bar',
					onclick : function() {
						var toolbars = ed.theme.panel.find('.toolbar:not(.menubar)');
						var cm = ed.controlManager;
						var id = cm.get(tb3Id).id;
						if ( 'undefined' == id )
							return;
						if ( DOM.isHidden(id) ) {
							cm.setActive('enable_cws_bar', 1);
							DOM.show(id);
							//t._resizeIframe(ed, tb3Id, -28);
							ed.settings.cws_tb3_hidden = 0;
							setUserSetting('hidetb3', '1');
						} else {
							cm.setActive('enable_cws_bar', 0);
							DOM.hide(id);
							//t._resizeIframe(ed, tb3Id, 28);
							ed.settings.cws_tb3_hidden = 1;
							setUserSetting('hidetb3', '0');
						}
					}
				});
			}
			ed.onNodeChange.add(function(ed, cm, node) { cm.setDisabled('cws_logo', true)	});
			ed.addButton('cws_logo', {
				label : 'CWS Buttons:',
				icon : false,
				//onclick : function() { return; }
			});

			ed.addButton('progress', {
				title : 'Add percent bar',
				image : url+'/button-progress.png',
				onclick : function() {
					//ed.selection.setContent('[progress width="50%"]');
					id = 'progress';
					tb_show("Insert Shortcode: " + id, url.substring(0, url.lastIndexOf("/")) +
						'/popup.php?shortcode=' + id + '&width=600&height=300' );
				}
			});

			ed.addButton('news', {
				title : 'Add news shortcode',
				onclick : function() {
					//ed.selection.setContent('[progress width="50%"]');
					id = 'news';
					tb_show("Insert Shortcode: " + id, url.substring(0, url.lastIndexOf("/")) +
						'/popup.php?shortcode=' + id + '&width=600&height=400' );
				}
			});

			ed.addButton('tweets', {
				title : 'Add tweets',
				onclick : function() {
					id = 'tweets';
					tb_show("Insert Shortcode: " + id, url.substring(0, url.lastIndexOf("/")) +
						'/popup.php?shortcode=' + id + '&width=600&height=400' );
				}
			});

			ed.addButton('embed', {
				title : 'Add audio/video clip here',
				onclick : function() {
					//ed.selection.setContent('[youtube id="Video ID (eg. Abc14deFghI5)" width="730" height="350"]');
					id = 'embed';
					tb_show("Insert Shortcode: " + id, url.substring(0, url.lastIndexOf("/")) +
						'/popup.php?shortcode=' + id + '&width=600&height=400' );
				}
			});

			ed.addButton('quote', {
				title : 'Add quote',
				onclick : function() {
					selection = encodeURIComponent( ed.selection.getContent() );
					var rng = ed.selection.getRng(true);
					//console.log( getWordAt( rng.startContainer.textContent, rng.startOffset ) );
					//console.log(document.styleSheets);
					id = 'quote';
					tb_show("Insert Shortcode: " + id, url.substring(0, url.lastIndexOf("/")) +
						'/popup.php?shortcode=' + id + '&sel=' + selection + '&width=600&height=500' );
				}
			});

			ed.addButton('fa', {
				title : 'Add Font Awesome',
				onclick : function() {
					selection = encodeURIComponent( ed.selection.getContent() );
					//console.log( getWordAt( rng.startContainer.textContent, rng.startOffset ) );
					//console.log(document.styleSheets);
					id = 'fa';
					tb_show("Insert Shortcode: " + id, url.substring(0, url.lastIndexOf("/")) +
						'/popup.php?shortcode=' + id + '&sel=' + selection + '&width=600&height=900' );
				}
			});

			ed.addButton('dropcap', {
				title : 'Add Drop Cap',
				onclick : function() {
					selection = encodeURIComponent( ed.selection.getContent() );
					id = 'dropcap';
					tb_show("Insert Shortcode: " + id, url.substring(0, url.lastIndexOf("/")) +
						'/popup.php?shortcode=' + id + '&sel=' + selection + '&width=600&height=460' );
				}
			});

			ed.addButton('alert', {
				title : 'Add an Alert',
				onclick : function() {
					selection = encodeURIComponent( ed.selection.getContent() );
					id = 'alert';
					tb_show("Insert Shortcode: " + id, url.substring(0, url.lastIndexOf("/")) +
						'/popup.php?shortcode=' + id + '&sel=' + selection + '&width=600&height=300' );

					//ed.selection.setContent('[alert type="information"]Information[/alert]');
				}
			});

			ed.addButton('cws_cta', {
				title : 'Add Call to action',
				onclick : function() {
					selection = encodeURIComponent( ed.selection.getContent() );
					id = 'cws_cta';
					tb_show("Insert Shortcode: " + id, url.substring(0, url.lastIndexOf("/")) +
						'/popup.php?shortcode=' + id + '&sel=' + selection + '&width=600&height=400' );

					//ed.selection.setContent('[alert type="information"]Information[/alert]');
				}
			});

			ed.addButton('price-table', {
				title : 'Insert pricing table',
				onclick : function() {
					id = 'price-table';
					tb_show("Insert Shortcode: " + id, url.substring(0, url.lastIndexOf("/")) +
						'/popup.php?shortcode=' + id + '&width=600&height=600' );

					//ed.selection.setContent('[alert type="information"]Information[/alert]');
				}
			});

			ed.addButton('ourteam', {
				title : 'Insert Our Team',
				onclick : function() {
					id = 'ourteam';
					tb_show("Insert Shortcode: " + id, url.substring(0, url.lastIndexOf("/")) +
						'/popup.php?shortcode=' + id + '&width=600&height=600' );
				}
			});

			ed.addButton('portfolio', {
				title : 'Insert portfolio',
				onclick : function() {
					id = 'portfolio';
					tb_show("Insert Shortcode: " + id, url.substring(0, url.lastIndexOf("/")) +
						'/popup.php?shortcode=' + id + '&width=800&height=600' );
				}
			});
		},
		createControl : function(n, cm) { return null; },
	});
	tinymce.PluginManager.add('enable_cws_bar', tinymce.plugins.enable_cws_bar);
})();
