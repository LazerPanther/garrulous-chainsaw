"use strict";
jQuery(document).ready(function() {
	jQuery(document).on("click", ".elements_panel .tabs a", function() {
		jQuery(this).addClass("active").siblings("a").removeClass("active");
	});

});

var makeCRCTable = function() {
	var c;
	var crcTable = [];
	for (var n = 0; n < 256; n++) {
		c = n;
		for (var k = 0; k < 8; k++) {
			c = ((c & 1) ? (0xEDB88320 ^ (c >>> 1)) : (c >>> 1));
		}
		crcTable[n] = c;
	}
	return crcTable;
}

var crc32 = function(str) {
	var crcTable = window.crcTable || (window.crcTable = makeCRCTable());
	var crc = 0 ^ (-1);
	for (var i = 0; i < str.length; i++) {
		crc = (crc >>> 8) ^ crcTable[(crc ^ str.charCodeAt(i)) & 0xFF];
	}
	return (crc ^ (-1)) >>> 0;
};

window.onscroll = function (event) {
  if ('pref' === event.target.activeElement.getAttribute('class')) {
		event.stopPropagation;
		event.preventDefault;
  }
}

/* ===== YUI portal ===== */
/*var YUI_config = {
	filter: 'debug', // request -debug versions of modules for log statements
	//gallery: 'gallery-2014.02.20-23-55',
	debug: true,
	logExclude: {
		base: true, // Don't broadcast log messages from the event module
		attribute: true, // or the attribute module
		widget: true // or the widget module
	},
	logLevel: 'error',
	useBrowserConsole: true,
};
*/
//Use loader to grab the modules needed
YUI().use(
	'dd', 'event-base',	'gallery-accordion-horiz-vert', 'tabview', 'node', 'panel', 'dd-delegate',
	'dd-plugin', 'dd-drop-plugin', 'dd-proxy',
	'anim',	'anim-base',
	'cookie',
	'json',
	function(Y) {
		//Make this an Event Target so we can bubble to it
		var Portal = function() {
			Portal.superclass.constructor.apply(this, arguments);
		};
		Portal.NAME = 'portal';
		Y.extend(Portal, Y.Base);
		// This is our new bubble target.
		Y.Portal = new Portal();

		Y.DD.Drag.prototype._handleMouseDownEvent = function(ev) {
			this.fire('drag:mouseDown', {	ev: ev });
		};

		var old_content, old_content_hash, new_content_hash, nestedPanel = null;
		var ajurl = '';
		var cws_cont_wrap_id = document.getElementById('cws_content_wrap');

		Y.on('domready', function(e) {
			Y.one('.wp-editor-tabs').prepend(
				'<a id="content-cws" class="wp-switch-editor switch-cws">CWS Builder</a>'
			);
			Y.all('input#publish,input#save-post,a#post-preview').on('mousedown', function(e) {
				if (typeof tinymce != 'undefined') {
					tinymce.remove('textarea#content');
				}
				onClickTmce_or_Publish(e, true);
			});
			Y.one('#content-cws').on('click', function(e) {
				e.preventDefault();
				onClickorDomReady(e);
			});
			if ('cwspb' == getUserSetting('editor')) {
				onClickorDomReady(e);
			}
		});

		var onClickorDomReady = function(e) {
			var content_wrap = Y.one('#wp-content-wrap');
			if (!content_wrap.hasClass('cws-active')) {
				var source = content_wrap.hasClass('html-active') ? 'html' : 'tmce';
				var id = 'content';
				var tmce_i = 0;
				if (typeof tinymce != 'undefined' && !tinymce.editors.length) {
					//switchEditors.switchto(document.getElementById('content-tmce'));
					setTimeout(function() {
						tinymce.init(tinyMCEPreInit.mceInit['content']);
					}, 1000);
				}
				if (typeof tinymce == 'undefined' || !tinymce.editors.length) {
					old_content = document.getElementById('cws-pb-cont').innerHTML;
					source = '';
				}
				setUserSetting('editor', 'cwspb');

				// var panxy = getUserSetting('cwspanxy');
				var panxy = 0;
				if (panxy) {
					panx = panxy & 0xffff;
					pany = panxy >> 16;
				} else {
					pany = document.getElementById('wpadminbar').clientHeight + 5;
					panx = document.getElementById('adminmenuwrap').clientWidth + 5;
				}

				if ('tmce' === source) {
					// tmce
					Y.all('#wp-content-editor-container, #post-status-info').hide();
					Y.one('#wp-content-wrap').removeClass('tmce-active');
					Y.one('#wp-content-wrap').addClass('cws-active');
					Y.one('#cws_content_wrap').show();

					var ed = tinyMCE.get(id);
					var dom = tinymce.DOM;
					old_content = ed.getContent({format: 'html'});
					old_content = old_content.replace(/<p>(\[\/?cws-.*?\])<\/p>/g, "$1");
					old_content = old_content.replace(/<p>(\[\/?cws-.*?\])<br \/>/g, "$1");
					old_content = old_content.replace(/<p>(\[\/?item.*?\])<\/p>/g, "$1");
					old_content = old_content.replace(/<p>(\[\/?item.*?\])<br \/>/g, "$1");
				} else if ('html' === source) {
					// text
					Y.one('#post-status-info').hide();
					Y.one('#wp-content-editor-container').hide();
					Y.one('#wp-content-wrap').removeClass('html-active');
					Y.one('#wp-content-wrap').addClass('cws-active');
					Y.one('#cws_content_wrap').show();
					//old_content = Y.one('#' + id).get('value');
					old_content = Y.one('#' + id)._node.value;
					/*
					old_content = window.switchEditors.wpautop( Y.one('#' + id).get('value') );
					old_content = old_content.replace(/<p>(\[\/?cws-.*?\])<\/p>/g, "$1");
					old_content = old_content.replace(/<p>(\[\/?cws-.*?\])<br \/>/g, "$1");
					old_content = old_content.replace(/<p>(\[\/?item.*?\])<\/p>/g, "$1");
					old_content = old_content.replace(/<p>(\[\/?item.*?\])<br \/>/g, "$1");
					*/
					//old_content = Y.one('#' + id).get('value');
				} else {
					Y.one('#post-status-info').hide();
					Y.one('#wp-content-editor-container').hide();
					Y.one('#wp-content-wrap').removeClass('html-active');
					Y.one('#wp-content-wrap').addClass('cws-active');
					Y.one('#cws_content_wrap').show();
				}
				var bNeedUpdate = false;
				var tmce_hash = crc32(old_content);
				if (!old_content_hash || tmce_hash != new_content_hash) {
					old_content_hash = tmce_hash;
					bNeedUpdate = true;
				}
				var li_rows = Y.all('#cws_row li');
				if (typeof preprocessContent == 'function') {
					old_content = preprocessContent(old_content);
				}
				var first8chars = old_content.substring(0, 8);
				if (old_content.length && '[cws-row' === first8chars && bNeedUpdate) {
					// we should build new content and compare it with the old one
					// if they don't match (for example at the first run, or after
					//
					// first we should clear old content if there's any
					//document.getElementById('pb_overlay').style.display = 'block';
					cleanAll();
					//window.setTimeout(buildModFromContent(old_content), 1000);
					buildModFromContent(old_content);
					//document.getElementById('pb_overlay').style.display = 'none';
				} else if (old_content.length && '[cws-row' !== first8chars) {
					// should insert old content into our builder
					// as text block
					cleanAll();
					var row = createMod(feeds['cols1']);
					var mod = createMod(widgets['w_widget1']);
					Y.one('#cws_row').appendChild(row);
					Y.one('.cwspb_widgets>ul').appendChild(mod);
					Y.one('#cws_row .cwspb_widgets li .inner').empty().append(old_content);
					initClonedDD(row);
					initClonedDD(mod);
				}
			}
		};

		var buildModFromContent0 = function(cont) {
			var i = 0;
			setTimeout(function() {
				buildModFromContent(cont);
				if (i < 1000000)
					i++, window.setTimeout(arguments.callee, 10);
			}, 10);
		};

		var cleanAll = function() {
			Y.all('#cws_row ul.item li.item').each(function(el) {
				var id = el.get('id');
				var dd = Y.DD.DDM.getDrag('#' + id);
				dd.destroy();
				el.get('parentNode').removeChild(el);
			});
			Y.all('#cws_row li.item').each(function(el) {
				var id = el.get('id');
				var dd = Y.DD.DDM.getDrag('#' + id);
				dd.destroy();
				el.get('parentNode').removeChild(el);
			});
			return;
		};

		var trim = Y.Lang.trim;

		var process_sc = function(content, node) {
			node.append(content);
			if (!ajurl.length) {
				var contwr = document.getElementById('cws_content_wrap');
				if (contwr) {
					ajurl = contwr.getAttribute('data-cws-ajurl');
				}
			}
			if (ajurl.length > 0) {
				var sc_parts = content.split(/\[(?=[^\/])/ig); // !!! no sc in sc !!!
				node.empty();
				node._node.setAttribute('data-cws-raw', content);
				if (sc_parts.length > 1) {
					jQuery.ajax({
						url: ajurl + '/pbaj.php',
						data: {
							cont: content
						},
						//async: false,
						type: 'post',
						error: function() {
							node.append(content);
						},
						success: function(data) {
							//bAjComplete = true;
							node.append(data);
						},
						//timeout: 50
					});
				} else {
					node.append(content);
				}
			}
		}

		// restore
		var buildModFromContent = function(content) {
			var fromidx = 0,
				curr_row = 0;
			while (8 == 8) {
				var row_start = content.indexOf('[cws-row', fromidx);
				if (-1 == row_start) {
					break;
				}
				curr_row++;
				var row_open_end = content.indexOf(']', fromidx + 8);
				var params = evalparam(trim(content.substring(fromidx + 9, row_open_end)));
				var col_nums = params.cols.substring(1).length;
				var feed_name = 'cols'; // by default
				col_nums = col_nums ? col_nums : parseInt(params.cols.substring(0));
				if (undefined !== params['render']) {
					switch (params['render']) {
						case 'portfolio':
						case 'portfolio_fw':
							feed_name = 'port';
							break;
						case 'ourteam':
							feed_name = 'ourt';
							break;
						case 'blog':
							feed_name = 'blog';
							break;
					}
					if (feed_name.length) {
						col_nums = 0;
						// grab this shortcode or whatever inside that row
						// var row = createMod(feeds[feed_id + params.cols], true, content.substring(row_open_end+1, content.indexOf('[/cws-row]', row_open_end+1)));
						if (undefined !== params['atts']) {
							var restoredAtts = restoreShortcodeArg(params['atts']);
							var row = createMod(feeds[feed_name + params.cols], true, '[pb_' + params['render'] + ' ' + restoredAtts + ' /]');
							// add p for every argument
							row._data['params'] = evalparam(restoredAtts.replace(/(\w+)=/gm, 'p_$1='));
						} else {
							var row = createMod(feeds[feed_name + params.cols]);
						}
						row.one('h4>strong').set('textContent', params.title);
					} // otherwise there'll be error, as it is an error
				} else {
					if (undefined !== params['atts']) {
						var restoredAtts = restoreShortcodeArg(params['atts']);
					}
					var row = createMod(feeds[feed_name + params.cols]);
					if (undefined !== restoredAtts && restoredAtts.length > 0) {
						row['_data'] = [];
						row._data['params'] = evalparam(restoredAtts.replace(/(\w+)=/gm, 'p_$1='));
					}
				}
				restoreShortcodeArgs(params, row, ['render', 'data', 'cols', 'atts']);
				if (undefined !== restoredAtts && restoredAtts.length > 0) {
					var restoredAttsParam = evalparam(restoredAtts);
					for (var n in restoredAttsParam) {
						if (restoredAttsParam.hasOwnProperty(n) ) {
							switch (n) {
								case 'row_img':
									row._node.setAttribute('cws-row-img', restoredAttsParam[n]);
									row.one('.inner').setStyle('background', 'url(' + restoredAttsParam[n] + ')');
									break;
								case 'cust_color':
									row.one('.inner').setStyle('background', restoredAttsParam[n]);
									//row.setData(n, restoredAttsParam[n]);
									break;
							}
						}
					}
				}
				Y.one('#cws_row').appendChild(row);
				fromidx = row_open_end + 1;
				var row_end = content.indexOf('[/cws-row]', fromidx) + 10; // sizeof [/cws-row]
				// parse cols
				for (var i = 0; i < col_nums; i++) {
					fromidx = content.indexOf('[col', fromidx) + 4;
					var col_open_end = content.indexOf(']', fromidx);
					var col_params = evalparam(trim(content.substring(fromidx, col_open_end)));
					var col_end = content.indexOf('[/col]', fromidx);
					while (7 == 7) {
						var w_start = content.indexOf('[cws-widget', fromidx);
						if (-1 == w_start || w_start > col_end) {
							break;
						}
						var w_open_end = content.indexOf(']', w_start + 11);
						var w_params = evalparam(trim(content.substring(w_start + 11, w_open_end)));
						// we can use underscore here as it's already included into wp
						var cur_widget = _.find(widgets, function(obj) {
							return obj.type == w_params.type
						});
						var curr_pos = w_open_end + 1,
							item_open_end, item_end;
						var mod = createMod(cur_widget, true);

						if (undefined !== w_params['atts']) {
							var restoredAtts = restoreShortcodeArg(w_params['atts']);
							if (restoredAtts.length > 0) {
								if (undefined === mod._data) {
									mod._data = {};
								}
								mod._data['params'] = evalparam(restoredAtts.replace(/(\w+)=/gm, 'p_$1='));
							}
						}
						Y.one('#cws_row>li:nth-child(' + curr_row + ') .cwspb_widgets>ul:nth-child(' + (i + 1) + ')').appendChild(mod);
						var textblock = content.substring(curr_pos, content.indexOf('[/cws-widget]', fromidx));
						var mod_inner = mod.one('.inner');
						switch (w_params.type) {
							case 'text':
								process_sc(textblock, mod_inner);
								break;
							case 'tcol':
								process_sc(textblock, mod_inner.one('.content_part'));
								mod_inner._node.setAttribute('data-cws-ishilited', w_params.ishilited ? '1' : '');
								mod_inner.one('a.button_text').set('href', w_params.order_url);
								Y.all('#cws-pb-tcol .row_options input[type="text"]').each(function(k) {
									if ( mod_inner.one('.' + k._node.name) ) {
										mod_inner.one('.' + k._node.name).set('textContent', w_params[k._node.name]);
									}
								});
								//for (var k = 0; k < tcol_a.length; k++) {
								//	mod_inner.one('.' + tcol_a[k]).set('textContent', w_params[tcol_a[k]]);
								//}
								break;
							case 'callout':
								var textblock = content.substring(curr_pos, content.indexOf('[/cws-widget]', fromidx));
								var mod_inner = mod.one('.inner');
								process_sc(textblock, mod_inner.one('.content_part'));
								fillIconOptions(mod_inner, w_params);
								break;
							case 'tabs':
								var tabs = mod.getData('tabs');
								var tab_item = {};
								tabs.plug(Removeable);
								for (var y = 0; y < w_params.items; y++) {
									curr_pos = content.indexOf('[item', curr_pos) + 5;
									item_open_end = content.indexOf(']', curr_pos);
									item_end = content.indexOf('[/item]', item_open_end);
									var item_params = evalparam(trim(content.substring(curr_pos, item_open_end)));
									// build icon_option if
									var icon_option_part = buildAccIconOption(item_params);
									tab_item['label'] = item_params.title;
									var textblock = trim(content.substring(item_open_end + 1, item_end));
									tab_item['content'] = '';
									tabs.add(tab_item, tabs.size());
									var curr_tab = tabs.item(tabs.size() - 1);

									process_sc(textblock, curr_tab.get('panelNode'));
									curr_tab.get('panelNode').addClass('clearfix');
									var newBoudingContent = icon_option_part + curr_tab.get('boundingBox')._node.innerHTML;
									curr_tab.get('boundingBox')._node.innerHTML = newBoudingContent;

									if (undefined !== item_params['iconfa']) {
										curr_tab.get('boundingBox').setData('sa_fa_icon', item_params['iconfa']);
									}
									if (undefined !== item_params['iconimg']) {
										curr_tab.get('boundingBox').setData('sa_img_icon', item_params['iconimg']);
									}
									//param.item(i).get('boundingBox')
									/*if ( crc32(textblock) != crc32(textblock_c) ) {
										tabs.item(tabs.size()-1).get('panelNode')._node.setAttribute('data-cws-raw', textblock);
									}*/
									curr_pos = item_end + 7;
								}
								tabs.selectChild(0);
								tabs.plug(Addable);
								//tabs.render(mod.one('.inner'));
								break;
							case 'accs':
								var accs = mod.getData('accs');
								var accs_item = {};
								if (1 == w_params.toggle) {
									mod.setData('istoggle', true);
									accs.set('allowMultipleOpen', true);
								} else {
									mod.setData('istoggle', false);
									accs.set('allowMultipleOpen', false);
								}

								if (1 == w_params.alt_style) {
									mod.setData('alt_style', true);
								} else {
									mod.setData('alt_style', false);
								}
								//var curr_pos = w_open_end+1, item_open_end, item_end;
								accs.plug(RemoveableAcc);
								for (var y = 0; y < w_params.items; y++) {
									curr_pos = content.indexOf('[item', curr_pos) + 5;
									item_open_end = content.indexOf(']', curr_pos);
									item_end = content.indexOf('[/item]', item_open_end);
									var item_params = evalparam(trim(content.substring(curr_pos, item_open_end)));
									var icon_option_part = buildAccIconOption(item_params);
									accs_item['label'] = item_params.title;
									var textblock = trim(content.substring(item_open_end + 1, item_end));
									accs_item['section'] = '';
									accs.insertSection(accs.section_list.length - 1,
										'<div>' + icon_option_part + '<a href="javascript:void(0);">' + accs_item['label'] + '</a>\
										<div class="control_panel">\
										<a class="yui3-acc-remove" title="remove slide"></a>' +
										'<a class="yui3-pref" title="Edit slide"></a></div></div>',
										'<div>' + accs_item['section'] + '</div>');
									process_sc(textblock, accs.section_list[accs.section_list.length - 2].content);
									if (item_params.open == '1') {
										accs.openSection(accs.section_list.length - 2);
									}
									// save icon options
									if (undefined !== item_params['iconfa']) {
										accs.section_list[accs.section_list.length - 2].title.setData('sa_fa_icon', item_params['iconfa']);
									}
									if (undefined !== item_params['iconimg']) {
										accs.section_list[accs.section_list.length - 2].title.setData('sa_img_icon', item_params['iconimg']);
									}

									/*if ( crc32(textblock) != crc32(textblock_c) ) {
										// save original content
										accs.section_list[accs.section_list.length - 2].content._node.setAttribute('data-cws-raw', textblock);
									}*/

									curr_pos = item_end + 7;
								}
								accs.plug(AddableAcc);
								break;
							default:
								var dr = mod_inner.one('div[data-render]');
								if (undefined !== w_params['atts']) {
									var w_sc_params = evalparam(restoreShortcodeArg(w_params['atts']));
									process_sc('[pb_' + w_params['type'] + ' ' + restoreShortcodeArg(w_params['atts']) + ' /]', dr);
									restoreParamShortcodeArgs(w_sc_params, mod);
								} else {
									if (null !== dr) {
										// internal shortcodes will be converted too
										textblock = textblock.replace(/\[(\/|)(.*?\])/i, '[$1pb_$2')
										process_sc(textblock, dr);
										var w_sc_params = evalparam(textblock.substring(textblock.indexOf(' ')+1, textblock.length-1));
										restoreParamShortcodeArgs(w_sc_params, mod);
									}
								}
								break;
						}
						fromidx = content.indexOf('[/cws-widget]', fromidx) + 13; // sizeof [/cws-widget]
						initClonedDD(mod, true);
						restoreParamShortcodeArgs(w_params, mod);
						var mod_title = undefined !== w_params.title ? w_params.title : '';
						mod.one('h4 strong').set('textContent', mod_title);
						var new_data = mod.getData('data');
						new_data.title = mod_title;
						mod.setAttribute('cws-title', mod_title);
						mod.setData('data', new_data);
						mod.setData('extra_style', w_params.e_style);
						restoredAtts = '';
					}
				}
				restoredAtts = '';
				fromidx = row_end;
				initClonedDD(row);
			}
		};

		var fillIconOptions = function(inner, item) {
			if (undefined !== item['iconfa']) {
				var span_fa  = inner.one('span.fa i');
				span_fa._node.removeAttribute('class');
				inner.one('span.fa')._node.removeAttribute('style');
				span_fa.addClass('fa fa-2x fa-' + item['iconfa']);
				inner.setData('sa_img_icon', '');
				inner.setData('sa_fa_icon', item['iconfa']);
			}
			if (undefined !== item['iconimg']) {
				inner.one('img.icon-options').set('src', item.iconimg)._node.removeAttribute('style');
				inner.one('span.fa')._node.style.display = 'none';
				inner.setData('sa_img_icon', item.iconimg);
				inner.setData('sa_fa_icon', '');
			}
			// fill
			Y.each(item, function(v, k) {
				var name_parts = k.split('_');
				if ('c' === name_parts[0]) {
					var module_node = inner.one('.' + name_parts[1]);
					if ('text' === name_parts[2]) {
						module_node.set('textContent', v);
					} else {
						module_node._node.setAttribute(name_parts[2], v);
					}
				}
			} );
		}

		var buildAccIconOption = function(item) {
			var out = '<img class="icon-options" src style="display:none"><span class="fa" style="display:none"><i class="fa fa-2x"></i></span>';
			if (undefined !== item['iconfa']) {
				out = '<img class="icon-options" src style="display:none"><span class="fa"><i class="fa fa-2x fa-' + item['iconfa'] + '"></i></span>';
			}
			if (undefined !== item['iconimg']) {
				out = '<img class="icon-options" src="' + item['iconimg'] + '"><span class="fa" style="display:none"><i class="fa fa-2x"></i></span>';
			}
			return out;
		}

		var evalparam = function(str) {
			var obj = {};
			var spos = 0,
				epos = 0;
			var is_quote = 0;
			var name = '',
				value = '';
			while (true) {
				spos = str.indexOf('=', epos);
				if (spos == -1) break;
				name = str.substring(epos, spos);
				is_quote = str.substring(spos + 1, spos + 2) == '"' ? 1 : 0;
				var space = str.indexOf(is_quote == 1 ? '"' : ' ', spos + is_quote + 1);
				value = space !== -1 ? str.substring(spos + is_quote + 1, space) : str.substring(spos + is_quote + 1);
				obj[name] = value;
				epos = space + is_quote + 1;
				if (!epos) break;
			};
			return obj;
		};

		Y.all('#content-tmce, #content-html').on('click', function(e) {
			onClickTmce_or_Publish(e, false);
			Y.all('#wp-content-editor-container,#post-status-info').show();
		});

		var onClickTmce_or_Publish = function(e, is_update) {
			var target_tab;
			var panxy = panx || pany << 16;
			setUserSetting('cwspanxy', panxy);
			if (e.type == 'click') {
				target_tab = e.target.getAttribute('id').substring(8);
				setUserSetting('editor', target_tab);
			} else {
				target_tab = 'tmce';
			}

			if (Y.one('#wp-content-wrap').hasClass('cws-active')) {
				var id = 'content';
				//var ed = tinyMCE.get(id);
				//var dom = tinymce.DOM;
				var new_content = buildContent();
				if (!is_update) {
					//delete data;
					var data = {};
					new_content_hash = crc32(new_content);
					if (new_content_hash != old_content_hash) {
						old_content_hash = new_content_hash;
					}
				}
				//ed.setContent(window.switchEditors.wpautop(new_content), {format: 'html'});
				new_content = new_content.replace(/\sdata-mce-(src|href)=".*?"/g, "");
				//ed.setContent(new_content, { format: 'raw' });
				document.getElementById('content').value = new_content;
				if ('html' === target_tab || 'view' === target_tab) {
					//Y.one('#content').set('value', window.switchEditors.pre_wpautop(new_content) );
					new_content = new_content.replace(/<p>(.*?)<\/p>/g, "\n\r$1\n\r");
					//Y.one('#content').set('value', new_content);
					document.getElementById('content').value = new_content;
				} else {
					//Y.one('#content').set('value', new_content);
				}
				if (!is_update) {
					Y.one('#wp-content-wrap').removeClass('cws-active');
					Y.one('#cws_content_wrap').hide();
					if (typeof tinymce !== 'undefined') {
						var dom = tinymce.DOM;
						dom.addClass('wp-content-wrap', target_tab + '-active');
					}
				}
			}
		};

		var cleanupYuid = function(node) {
			if (node) {
				node.all('*[id^="yui"]').removeAttribute('id');
			}
		};

		var tcol_a = ['title', 'button_text', 'currency', 'price', 'price_description', 'encouragement'];

		// save
		var buildContent = function() {
			var ret = '';
			Y.all('#cws_row>li').each(function(el0) {
				var col_id = el0.getData('data').id;
				ret += '[cws-row cols=' + col_id.substring(4);
				var col4id = col_id.substring(0, 4);
				var row_mod_obj = [];
				if ('cols' !== col4id) {
					// full width "row" modules
					row_mod_obj = el0.one('.inner>div').getData();
					var inc_sc = row_mod_obj['cws-raw'];
					var int_sc_params = evalparam(inc_sc.substring(inc_sc.indexOf(' ')+1, inc_sc.indexOf(']')));
					// !!! between rows there can't be anything at this point
					// only [cws-row some params][/cws-row]
					// var atts = saveShortcodeArg(int_sc_params); // got these from above
					ret += ' render="' + row_mod_obj.render + '"';
					var row_title = el0.getAttribute('cws-title');
					if (row_title.length > 0) {
						ret += ' title="' + el0.getAttribute('cws-title') + '"';
					}
					ret += ' atts="' + saveShortcodeArg(el0.getData('params')) + '"';
					//ret += atts.length > 0 ? ' atts="' + atts + '"' : '';
				} else {
					ret += saveShortcodeArgs(el0._data, ['bTitlePref', 'data', 'render', 'atts']);
					var atts = saveShortcodeArg(el0.getData('params'));
					if (atts.length > 0) {
						ret += ' atts="' + atts + '"';
					}
				}

				//var row_img = el0._node.getAttribute('cws-row-img');
				//ret += (row_img != null && row_img.length) ? ' row_img="' + row_img + '"' : '';
				// find out if there're at least one table column (w_widget4)
				// in this case we need to set a specific flag
				var a = el0.all('ul.item li.item[data-id="w_widget4"]');
				var bIsTabRow = (a._nodes.length > 0);

				ret += bIsTabRow ? ' flags=1' : '';
				ret += ']';
				/*if (undefined !== row_mod_obj['cws-raw']){
					ret += row_mod_obj['cws-raw'];
				}*/
				var col = 0;
				// columns
				var colnum = parseInt(col_id.substring(4, 5));
				el0.all('.cwspb_widgets>ul').each(function(el1, c) {
					var spn = isNaN(parseInt(col_id.substring(5 + col, 6 + col))) ? 1 : parseInt(col_id.substring(5 + col, 6 + col));
					ret += '[col span=' + 12 * spn / colnum;
					var ab = el1.all('li.item[data-id="w_widget4"]');
					var bTabColFlags = (ab._nodes.length > 0) ? bTabColFlags | 1 : bTabColFlags;
					ab = el1.all('li.item .mod .inner[data-cws-ishilited="1"]');
					bTabColFlags = (ab._nodes.length > 0) ? bTabColFlags | 3 : bTabColFlags;
					ret += bTabColFlags ? ' flags=' + bTabColFlags : '';
					ret += ']';
					// widgets
					var param;
					el1.all('>li').each(function(el2, c) {
						var data = el2.getData('data');
						ret += '[cws-widget type=' + data.type;
						param = el2.getData('extra_style');
						ret += (param !== undefined && param.length) ? ' e_style="' + param + '"' : '';
						param = el2.getData('alt_style');
						ret += (param !== undefined && param === true) ? ' alt_style=1' : '';
						// save atts if any
						var atts = saveShortcodeArg(el2.getData('params'));
						if (atts.length > 0) {
							ret += ' atts="' + atts + '"';
						}
						var mod_inner;
						switch (data.type) {
							case 'text':
								// <i class="fa fa-twitter"></i>
								ret += saveShortcodeArgsP(el2.getData('params'));
								ret += ' title="' + el2.getAttribute('cws-title') + '"]';
								mod_inner = el2.one('.inner');
								cleanupYuid(mod_inner);
								var text_block = (null !== mod_inner._node.getAttribute('data-cws-raw')) ? mod_inner._node.getAttribute('data-cws-raw') : mod_inner.get('innerHTML');
								ret += text_block;
								break;
							case 'tcol':
								mod_inner = el2.one('.inner');
								var text_block = (null !== mod_inner._node.getAttribute('data-cws-raw')) ? mod_inner._node.getAttribute('data-cws-raw') : mod_inner.one('.content_part').get('innerHTML');
								//ret += ' title="' + el2.getAttribute('cws-title') + '"';
								ret += ' order_url="' + mod_inner.one('.button_text')._node.href + '"';
								var ishi = mod_inner._node.getAttribute('data-cws-ishilited');
								ret += (null !== ishi && ishi.length === 1) ? ' ishilited=1' : '';
								// the rest can be extracted in a similar manner
								Y.all('#cws-pb-tcol .row_options input[type="text"]').each(function(k) {
									if ( mod_inner.one('.' + k._node.name) ) {
										ret += ' ' + k._node.name + '="' + mod_inner.one('.' + k._node.name).get('textContent') + '"';
									}
								});
								//for (var i = 0; i < tcol_a.length; i++) {
								//	ret += ' ' + tcol_a[i] + '="' + mod_inner.one('.' + tcol_a[i]).get('textContent') + '"';
								//}
								ret += ']';
								ret += text_block;
								break;
							case 'callout':
								mod_inner = el2.one('.inner');
								var text_block = (null !== mod_inner._node.getAttribute('data-cws-raw')) ? mod_inner._node.getAttribute('data-cws-raw') : mod_inner.one('.content_part').get('innerHTML');
								text_block = text_block.trim();
								var icon_option = getAccSectionIcon(mod_inner);
								var a_button = mod_inner.one('.btn');
								ret += ' title="' + el2.getAttribute('cws-title') + '"';
								ret += icon_option;
								ret += ' c_btn_href="' + a_button._node.getAttribute('href') + '"';
								ret += ' c_btn_text="' + a_button._node.textContent + '"';
								ret += ']';
								ret += text_block;
								break;
							case 'tabs':
								ret += ' title="' + el2.getAttribute('cws-title') + '"';
								param = el2.getData(data.type);
								var num_tabs = param.size();
								ret += ' items=' + num_tabs + ']';
								for (var i = 0; i < num_tabs; i++) {
									var open = param.item(i).get('selected') == '1' ? ' open=1' : '';
									var icon_option = getAccSectionIcon(param.item(i).get('boundingBox'));
									ret += '[item' + open + icon_option + ' type=' + data.type + ' title="' + param.item(i).get('srcNode').get('innerHTML') + '"]';
									mod_inner = param.item(i);
									//cleanupYuid(mod_inner);
									//ret += mod_inner.get('content');
									var text_block = (null !== mod_inner.get('panelNode')._node.getAttribute('data-cws-raw')) ?
										mod_inner.get('panelNode')._node.getAttribute('data-cws-raw') : mod_inner.get('content');
									ret += text_block;
									ret += '[/item]';
								}
								break;
							case 'accs':
								var istoggle = el2.getData('istoggle');
								if (istoggle) {
									ret += ' toggle=1';
								}
								param = el2.getData(data.type);
								ret += ' title="' + el2.getAttribute('cws-title') + '"';
								var num_tabs = param.section_list.length - 1;
								if (-1 != num_tabs) {
									ret += ' items=' + num_tabs + ']';
									for (var i = 0; i < num_tabs; i++) {
										var open = param.section_list[i].open ? ' open=1' : '';
										var icon_option = getAccSectionIcon(param.section_list[i].title);
										ret += '[item' + open + icon_option + ' type=' + data.type + ' title="' + param.section_list[i].title.one('>div>a').get('textContent') + '"]';
										mod_inner = param.section_list[i].content.one('>div');
										cleanupYuid(mod_inner);
										var text_block = (null !== param.section_list[i].content._node.getAttribute('data-cws-raw')) ?
											param.section_list[i].content._node.getAttribute('data-cws-raw') : mod_inner.get('innerHTML');
										ret += text_block;
										ret += '[/item]';
									}
								}
								break;
							default:
								ret += ' title="' + el2.getAttribute('cws-title') + '"';
								var options = el2.getData('options');
								var el2_options = null;
								if (options) {
									el2_options = processModuleOptions(options);
								}
								var sc_args = saveShortcodeArgsP(el2.getData('params'));
								if (el2_options && '1' == el2_options['atts']) {
									ret += ' atts="' + saveShortcodeArg(evalparam(sc_args.trim())) + '"]';
								} else {
									mod_inner = el2.one('.inner');
									var dr = mod_inner.one('div[data-render]');
									if (null !== dr) {
										ret += ']';
										// replace [pb_ourteam... to [ourteam...
										// wouldn't work with opening and closing shortcodes, like [a]text[/a]
										var real_sc = dr._node.getAttribute('data-cws-raw').replace(/(\[)pb_(.*?)\s.*((\/|)\])/i, '$1$2 ' + sc_args + '$3');
										ret += real_sc;
									} else {
										ret += sc_args;
										ret += ']';
									}
								}
								break;
						}
						ret += '[/cws-widget]';
					});
					col++;
					ret += '[/col]';
				});
				ret += '[/cws-row]';
			});
			return ret;
		};

		var processModuleOptions = function(str) {
			var options_pairs = str.split(';');
			var out = {};
			for (var i=0; i<options_pairs.length; i++) {
				var pair = options_pairs[i].split(':');
				out[pair[0]] = pair[1];
			}
			return out;
		}

		var restoreParamShortcodeArgs = function(data, mod) {
			//var params = mod.getData('params') ? mod.getData('params') : new Object;
			var params = mod.getData('params');
			if (undefined !== params) {
				for (var key in data) {
					if (data.hasOwnProperty(key) && key !== 'title' && key !== 'type' && key !== 'atts') {
						params['p_'+key] = data[key];
					}
				}
				mod.setData('params', params);
			}
		}

		var saveShortcodeArgsP = function(data) {
			var out = '';
			for (var key in data) {
				if (data.hasOwnProperty(key) && 'p_' === key.substring(0,2)) {
					if ("boolean" === typeof data[key]) {
						var val = data[key] ? '1' : '';
					} else {
						var val = data[key];
					}
					if (val.length) {
						var k = key.indexOf('_', 2);// p_render_t
						k = (k === -1) ? key.substring(2) : key.substring(2, k);
						out += ' ' + k + '="' + val + '"';
					}
				}
			}
			return out;
		}

		var restoreShortcodeArgs = function(data, mod, aSkip) {
			var params = mod.getData();
			for (var key in data) {
				if (data.hasOwnProperty(key) && ('undefined' === typeof aSkip || aSkip.indexOf(key) === -1) ) {
					params[key] = data[key];
				}
			}
			mod.setData(params);
		}

		var saveShortcodeArgs = function(data, aSkip) {
			var out = '';
			for (var key in data) {
				if ('undefined' === typeof aSkip || aSkip.indexOf(key) === -1) {
					if (data.hasOwnProperty(key)) {
						if ("boolean" === typeof data[key]) {
							var val = data[key] ? '1' : '';
						} else {
							var val = data[key];
						}
						if (val.length > 0 && 'p_' !== key.substring(0,2)) { // p_ are service controls
							out += ' ' + key + '="' + val + '"';
						}
					}
				}
			}
			return out;
		}

		// for string like key:param;key1:param2;
		var saveShortcodeArg = function(data, aSkip) {
			var out = '';
			for (var key in data) {
				if ('undefined' === typeof aSkip || aSkip.indexOf(key) === -1) {
					if (data.hasOwnProperty(key)) {
						if ("boolean" === typeof data[key]) {
							var val = data[key] ? '1' : '';
						} else {
							var val = data[key];
						}
						if (val.length > 0) { //
							out += key.substring(key.indexOf('_')+1) + ':' + val + ';';
						}
					}
				}
			}
			return out;
		}

		// turn key:param;key1:param2; to key="param"
		var restoreShortcodeArg = function(data) {
			return data.replace(/(\w+):(.*?);/gm,'$1="$2" ').trim();
		}


		var getAccSectionIcon = function(inner) {
			var img_icon_src = inner.getData('sa_img_icon');
			var fa_icon = inner.getData('sa_fa_icon');
			var out = '';
			if (undefined !== fa_icon && fa_icon.length) {
				out = ' iconfa="' + fa_icon + '"';
			}
			if (undefined !== img_icon_src && img_icon_src.length) {
				out = ' iconimg="' + img_icon_src + '"';
			}
			return out;
		}

		var Addable = function(config) {
			Addable.superclass.constructor.apply(this, arguments);
		};
		var AddableAcc = function(config) {
			AddableAcc.superclass.constructor.apply(this, arguments);
		};

		Addable.NAME = 'addableTabs';
		Addable.NS = 'addable';
		AddableAcc.NAME = 'addableAccN';
		AddableAcc.NS = 'addableAcc';

		Y.extend(Addable, Y.Plugin.Base, {
			ADD_TEMPLATE: '<li class="yui3-tab" title="add a tab">' +
				'<a class="yui3-tab-label yui3-tab-add"></a></li>',

			initializer: function(config) {
				var tabview = this.get('host');
				tabview.after('render', this.afterRender, this);
				tabview.get('contentBox')
					.delegate('click', this.onAddClick, '.yui3-tab-add', this);
			},

			getTabInput: function() {
				var tabview = this.get('host');
				return {
					label: 'New Tab',
					content: 'New Tab Content.',
				}
			},

			afterRender: function(e) {
				var tabview = this.get('host');
				tabview.get('contentBox').one('> ul').append(this.ADD_TEMPLATE);
			},

			onAddClick: function(e) {
				e.stopPropagation();
				var tabview = this.get('host'),
					input = this.getTabInput();
				tabview.add(input, tabview.size());
				tabview.selectChild(tabview.size() - 1);
				var curr_tab = tabview.item(tabview.size() - 1);
				var newtabhtml = curr_tab.get('boundingBox')._node.innerHTML;
				curr_tab.get('panelNode').addClass('clearfix');
				curr_tab.get('boundingBox')._node.innerHTML = '<img class="icon-options" src style="display:none"><span class="fa"><i class="fa fa-2x"></i></span>' + newtabhtml;

			}
		});

		Y.extend(AddableAcc, Y.Plugin.Base, {
			ADD_TEMPLATE: '<div>' +
				'<a class="yui3-tab-label yui3-acc-add"></a></div>',

			initializer: function(config) {
				var acc = this.get('host');
				acc.after('render', this.afterRender, this);
				acc.get('contentBox').delegate('click', this.onAddClick, '.yui3-acc-add', this);
				acc.get('contentBox').delegate('click', this.onAddClick0, '.yui3-accordion-title', this);
			},

			getTabInput: function() {
				var acc = this.get('host');
				return {
					label: 'New Tab',
					section: 'New Slide Content.',
				}
			},

			afterRender: function(e) {
				var acc = this.get('host');
				acc.appendSection(this.ADD_TEMPLATE, '<div class="yui3-accordion-section-clip"></div>');
			},

			onAddClick0: function(e) {
				var one_a = e.target.one("a");
				var classname = null !== one_a ? one_a.get('className') : '';
				if (-1 !== classname.indexOf('yui3-acc-add')) {
					// prevent + slide from shaking
					e.stopPropagation();
				} else {}
			},

			onAddClick: function(e) {
				e.stopPropagation();
				var acc = this.get('host');
				acc.insertSection(acc.section_list.length - 1,
					'<div><img class="icon-options" src style="display:none">\
						<span class="fa"><i class="fa fa-2x"></i></span>\
						<a href="javascript:void(0);">New Slide</a>\
					<div class="control_panel">\
					<a class="yui3-acc-remove" title="remove slide"></a>' +
					'<a class="yui3-pref" title="Edit slide"></a></div></div>',
					'<div>New Slide Content</div>');
				//acc.openSection(acc.section_list.length - 1);
			}
		});

		var Removeable = function(config) {
			Removeable.superclass.constructor.apply(this, arguments);
		};
		var RemoveableAcc = function(config) {
			RemoveableAcc.superclass.constructor.apply(this, arguments);
		};

		Removeable.NAME = 'removeableTabs';
		Removeable.NS = 'removeable';
		RemoveableAcc.NAME = 'removeableAccN';
		RemoveableAcc.NS = 'removeableAcc';

		Y.extend(Removeable, Y.Plugin.Base, {
			REMOVE_TEMPLATE: '<div class="control_panel"><a class="yui3-tab-remove" title="remove tab"></a>' +
				'<a class="yui3-tab-edit" title="Edit tab"></a></div>',

			initializer: function(config) {
				var tabview = this.get('host'),
					cb = tabview.get('contentBox');

				cb.addClass('yui3-tabview-removeable');
				cb.delegate('click', this.onRemoveClick, '.yui3-tab-remove', this);
				cb.delegate('click', this.onEditClick, '.yui3-tab-edit', this);

				// Tab events bubble to TabView
				tabview.after('tab:render', this.afterTabRender, this);
			},

			afterTabRender: function(e) {
				// boundingBox is the Tab's LI
				e.target.get('boundingBox').append(this.REMOVE_TEMPLATE);
			},

			onRemoveClick: function(e) {
				e.stopPropagation();
				var tab = Y.Widget.getByNode(e.target);
				tab.remove();
			},

			onEditClick: function(e) {
				e.stopPropagation();
				current_tab = Y.Widget.getByNode(e.target);
				g_li = current_tab._parentNode.ancestor('li.item');
				//var curr_idx = acc.findSection(e.target);
				nestedPanel = showpanel(current_tab.get('label'));
				nestedPanel.show();
				nestedPanel.on('init', onPanelInit, {
					cfg: current_tab,
					bTitlePref: false
				});
			}
		});

		Y.extend(RemoveableAcc, Y.Plugin.Base, {
			REMOVE_TEMPLATE: '<div class="control_panel"><a class="yui3-acc-remove" title="remove slide"></a>' +
				'<a class="yui3-pref" title="Edit slide"></a></div>',

			initializer: function(config) {
				var acc = this.get('host'),
					cb = acc.get('contentBox');

				cb.addClass('yui3-acc-removeable');
				cb.delegate('click', this.onRemoveClick, '.yui3-acc-remove', this);
				cb.delegate('click', this.onPrefClick, '.yui3-pref', this);

				// Tab events bubble to TabView
				acc.after('accordion:render', this.afterAccRender, this);
				acc.allow_all_closed = true;
			},

			afterAccRender: function(e) {
				// boundingBox is the Tab's LI
				e.target.get('titles').append(this.REMOVE_TEMPLATE);
			},

			onPrefClick: function(e) {
				e.stopPropagation();
				var acc = Y.Widget.getByNode(e.target);
				g_li = acc._parentNode.get('parentNode').get('parentNode').get('parentNode');
				var curr_idx = acc.findSection(e.target);
				nestedPanel = showpanel(acc.section_list[curr_idx].title.one('a:first-child').get('textContent'));
				nestedPanel.show();
				nestedPanel.on('init', onPanelInit, {
					cfg: curr_idx,
					bTitlePref: false
				});
			},

			onRemoveClick: function(e) {
				e.stopPropagation();
				var acc = Y.Widget.getByNode(e.target);
				acc.removeSection(acc.findSection(e.target));
			}
		});


		//Setup some private variables..
		var goingUp = false,
			lastY = 0,
			trans = {},
			g_li = null;

		//The list of feeds that we are going to use
		var feeds = {
			'cols1': {
				id: 'cols1',
				title: '1/1',
				type: 'col'
			},
			'cols2': {
				id: 'cols2',
				title: '2/2',
				type: 'col'
			},
			'cols3': {
				id: 'cols3',
				title: '3/3',
				type: 'col'
			},
			'cols4': {
				id: 'cols4',
				title: '4/4',
				type: 'col'
			},
			'cols321': {
				id: 'cols321',
				title: '2/3 + 1/3',
				type: 'col'
			},
			'cols312': {
				id: 'cols312',
				title: '1/3 + 2/3',
				type: 'col'
			},
			'cols413': {
				id: 'cols413',
				title: '1/4 + 3/4',
				type: 'col'
			},
			'cols431': {
				id: 'cols431',
				title: '3/4 + 1/4',
				type: 'col'
			},
			'cols4112': {
				id: 'cols4112',
				title: '1/4 + 1/4 + 2/4',
				type: 'col'
			},
			'cols4211': {
				id: 'cols4211',
				title: '2/4 + 1/4 + 1/4',
				type: 'col'
			},
			'cols4121': {
				id: 'cols4121',
				title: '1/4 + 2/4 + 1/4',
				type: 'col'
			},
			'port1': {
				id: 'port1',
				title: 'Full width Portfolio',
				type: 'col'
			},
			'port2': {
				id: 'port2',
				title: 'Row Portfolio',
				type: 'col'
			},
			'ourt1': {
				id: 'ourt1',
				title: 'Row Our Team',
				type: 'col'
			},
			'blog1': {
				id: 'blog1',
				title: 'Row Blog',
				type: 'col'
			},
		};

		var widgets = {
			'w_widget1': {
				id: 'w_widget1',
				type: 'text',
				wtitle: true,
				dtitle: 'Text',
			},
			'w_widget2': {
				id: 'w_widget2',
				type: 'tabs',
				wtitle: true,
				dtitle: 'Tabs',
			},
			'w_widget3': {
				id: 'w_widget3',
				type: 'accs',
				wtitle: true,
				eheight: 300,
				dtitle: 'Accordion/Toggle',
			},
			'w_widget4': {
				id: 'w_widget4',
				type: 'tcol',
				wtitle: true,
				eheight: 300,
				dtitle: 'Pricing Table Column',
			},
			'w_widget5': {
				id: 'w_widget5',
				type: 'callout',
				wtitle: true,
				eheight: 300,
				dtitle: 'Callout',
			},
			'w_widget6': {
				id: 'w_widget6',
				type: 'ourteam',
				wtitle: true,
				norender: true,
				dtitle: 'Our Team',
			},
			'w_widget7': {
				id: 'w_widget7',
				type: 'portfolio',
				wtitle: true,
				norender: true,
				dtitle: 'Portfolio',
			},
			'w_widget8': {
				id: 'w_widget8',
				type: 'blog',
				wtitle: true,
				norender: true,
				dtitle: 'Blog',
			},
			'w_widget9': {
				id: 'w_widget9',
				type: 'tweet',
				wtitle: true,
				norender: true,
				dtitle: 'Tweets',
			},
		};

		// http://jafl.github.io/yui-modules/accordion-horiz-vert/

		//Simple method for stopping event propagation
		//Using this so we can detach it later
		var stopper = function(e) {
			e.stopPropagation();
		};

		//Helper method for creating the feed DD on the left
		var _createFeedDD = function(node) {
			//Create the DD instance
			var id = node.getAttribute('id') !== undefined ? node.getAttribute('id') : node.getData('id');
			var data = 'w_' == id.substring(0, 2) ? widgets[id] : feeds[id];
			node.setData('data', data);
			//var groups = 'cols' == data.id.substring(0, 4) ? ['cols'] : ['widgets']
			var groups = ('col' === data.type) ? ['cols'] : ['widgets']

			if (false == Y.DD.DDM.getDrag('#' + data.id)) {
				var dd = new Y.DD.Drag({
					node: node,
					data: data,
					groups: groups,
					bubbleTargets: Y.Portal
				});
				dd.plug(Y.Plugin.DDProxy, {
					moveOnEnd: false,
					cloneNode: true,
					borderStyle: 'none'
				});
				//Setup some stopper events
				dd.on('drag:start', _handleStart);
				dd.on('drag:end', stopper);
				dd.on('drag:drophit', stopper);
			}
		};

		var _nodeSelect = function(e) {
			var a = e.target,
				div = a.ancestor('li.item');
			updateMod(feeds['cols' + e.target.get('value')], div);
		}


		//Handle the node:click event
		// click on the dropped item
		var w_node;
		var current_acc, current_tab;

		var _nodeClick = function(e) {
			//Is the target an href?
			if (e.target.test('a')) {
				var a = e.target,
					anim = null,
					div = a.get('parentNode').get('parentNode').get('parentNode');
				switch (a.getAttribute('class')) {
					case 'min':
						//Get some node references
						//debugger
						var div_inner = div.one('div.inner'),
							ul = div.one('div.inner>*'),
							h4 = div.one('h4'),
							h = h4.get('offsetHeight'),
							hUL = div_inner.get('clientHeight'),
							inner = div.one('div.inner');

						//Create an anim instance on this node.
						anim = new Y.Anim({	node: div_inner	});
						//Is it expanded?
						if (!div.hasClass('minned')) {
							div.toggleClass('minned');
							div_inner._node.style.display = 'none';

							/*
							//Set the vars for collapsing it
							anim.setAttrs({
								to: {
									height: 0,
									padding: 0,
								},
								duration: '.25',
								easing: Y.Easing.easeOut,
								iteration: 1
							});
							//On the end, toggle the minned class
							//Then set the cookies for state
							anim.on('end', function() {
								div.toggleClass('minned');
								div.setAttribute('cws-h', hUL);
								//_setCookies();
							});
*/
						} else {
							//Set the vars for expanding it
							hUL = div.getAttribute('cws-h') - hUL;
							/*anim.setAttrs({
								to: {
									height: (hUL),
								},
								duration: '.25',
								easing: Y.Easing.easeOut,
								iteration: 1
							});
							//Toggle the minned class
							anim.on('end', function() {
								div.toggleClass('minned');
								div.setAttribute('cws-h', 0);
								div_inner._node.removeAttribute('style');
							});*/
							div.toggleClass('minned');
							div_inner._node.removeAttribute('style');
						}
						//Run the animation
						//anim.run();
						break;
					case 'close':
						//Get some Node references..
						var li = div.get('parentNode'),
							id = li.get('id'),
							dd = Y.DD.DDM.getDrag('#' + id);
						//Destroy the DD instance.
						dd.destroy();
						//Setup the animation for making it disappear
						anim = new Y.Anim({
							node: div,
							to: {
								opacity: 0
							},
							duration: '.25',
							easing: Y.Easing.easeOut
						});
						anim.on('end', function() {
							//On end of the first anim, setup another to make it collapse
							var anim = new Y.Anim({
								node: div,
								to: {
									height: 0
								},
								duration: '.25',
								easing: Y.Easing.easeOut
							});
							anim.on('end', function() {
								li.get('parentNode').removeChild(li);
							});
							anim.run();
						});
						//Run the animation
						anim.run();
						break;
					case 'pref':
						e.preventDefault();
						e.stopPropagation();
						var li = div.get('parentNode'),
							id = li.get('id'),
							dd = Y.DD.DDM.getDrag('#' + id),
							data = dd.get('data');
						g_li = li;
						var content = getContent(data);
						var title = g_li.getData('data').dtitle;
						title = (undefined !== title) ? title : g_li.getData('data').title;
						nestedPanel = showpanel(title, true);
						nestedPanel.show();
						nestedPanel.on('init', onPanelInit, {
							bTitlePref: true
						});
						break;
					case 'clone':
						var li = div.get('parentNode'),
							ul = li.get('parentNode'),
							id = li.get('id'),
							dd = Y.DD.DDM.getDrag('#' + id),
							data = dd.get('data');
						a = li.insert(li.cloneNode(true), 'after');
						var newli = li.get('nextSibling');

						if ('tabs' === data.type || 'accs' === data.type) {
							newli.all('*[role]').each(function(b) {
								b.removeAttribute('role');
								b.removeAttribute('aria-labeledby');
								b.removeAttribute('aria-controls');
								b.removeAttribute('aria-hidden');
							});
						}
						newli.all('*[id^="yui"]').removeAttribute('id');

						newli.all('.cwspb_widgets li.item').each(function(b) {
							// id cloned too
							initClonedDD(b, true);
						});
						initClonedDD(newli, false, li);
						break;
				}
				//Stop the click
				e.halt();
			}
		};

		var _switchTextEditor = function(e, text) {
			if (e) {
				e.stopPropagation();
				e.preventDefault();
			}
			var qttb = w_node.one('.cws-pb-tmce>.quicktags-toolbar');
			var textarea = w_node.one('.wp-editor-area');
			if (!e || 'tmce' == e.target.getData('mode')) {
				var textareaid = textarea.get('id');
				if (!qttb) {
					var qt = quicktags(window.tinyMCEPreInit.qtInit[textareaid]);
					quicktags({
						id: textareaid,
						buttons: "strong,em,link,block,del,ins,img,ul,ol,li,code,more,close"
					});
					QTags._buttonsInit();
				} else {
					qttb.show();
				}

				if (e) {
					var iframe = tinymce.DOM.get(nestedPanel.bodyNode.one('iframe').get('id'));
					var editorHeight = iframe ? parseInt(iframe.style.height, 10) : 0;

					if (editorHeight) {
						var toolbarHeight = nestedPanel.bodyNode.one('.mce-toolbar-grp').get('clientHeight');
						editorHeight = editorHeight + toolbarHeight - 14;

						// height cannot be under 50 or over 5000
						if (editorHeight > 50 && editorHeight < 5000) {
							textarea.setStyle('height', editorHeight);
						}
					}
					w_node.one('.cws-pb-tmce>div').hide();
					w_node.one('.cws-pb-tmce>textarea').show();
					var html = window.switchEditors.pre_wpautop(tinyMCE.activeEditor.getContent({
						format: 'html'
					}));
				} else {
					html = text;
					w_node.one('#cws-switch-text').setData('mode', 'html');
				}

				textarea.set('value', html);
				if (e) {
					e.target.set('innerHTML', 'Switch to Visual');
					e.target.setData('mode', 'html');
				}
			} else {
				w_node.one('.cws-pb-tmce>div').show();
				w_node.one('.cws-pb-tmce>textarea').hide();
				qttb.hide();
				var tmce_content = window.switchEditors.wpautop(textarea.get('value'));
				tinyMCE.activeEditor.setContent(tmce_content, {
					format: 'html'
				});

				e.target.set('innerHTML', 'Switch to Text');
				e.target.setData('mode', 'tmce');
				setTimeout(function() {
					if (window.scrollY !== scry) {
						window.scrollTo(scrx, scry);
					}
				}, 5);
			}
		};

		var onPanelInit = function(e) {
			var data = g_li.getData('data');
			var type = data.type;
			g_li.setData('bTitlePref', this.bTitlePref);
			var template = (false === this.bTitlePref || ('tabs' !== type && 'accs' !== type) ) ? type : (data.wtitle) ? 'col-title' : 'col';
			if ('col-title' == template && 'accs' == data.type) {
				template = 'accs-title';
			} else if (type === 'col' && type !== data.id.substring(0,3)) {
				template = data.id;
			}

			var wb = Y.one('.yui3-widget-bd');
			w_node = wb.append(Y.one('#cws-pb-' + template).cloneNode(true).show());

			if (null !== wb.one('#cws-switch-text')) {
				if (typeof tinymce != 'undefined') {
					wb.one('#cws-switch-text').on('click', _switchTextEditor);
				}
			}

			if (w_node.one('textarea')) {
				w_node.one('textarea').set('id', w_node._yuid);
			}
			var dataStorage = g_li.one('.inner'); // icons options for now
			var data_cws_raw = g_li.one('.inner')._node.getAttribute('data-cws-raw');
			switch (template) {
				case 'text':
					var text_block = (null !== data_cws_raw) ? data_cws_raw : g_li.one('.inner').get('innerHTML');
					_inittmce(w_node, text_block);
					wb.one('input[name="title"]').set('value', g_li.getAttribute('cws-title'));
					assignFormElements(wb, g_li);
					break;
				case 'tcol':
					wb.all('.row_options input').each(function(el) {
						switch (el._node.name) {
							case 'order_url':
								// pathname is to get href as is
								wb.one('input[name="order_url"]').set('value', g_li.one('.button_text')._node.href);
								wb.one('input[name="button_text"]').set('value', g_li.one('.button_text').get('textContent'));
								break;
							case 'button_text':
								// omit this one because we've filled all we need in order_url
								break;
							case 'ishilited':
								var ishi = g_li.one('.inner')._node.getAttribute('data-cws-ishilited');
								if (null !== ishi && ishi.length === 1) {
									wb.one('input[name="' + el._node.name + '"]').set('checked', ishi);
								} else {
									wb.one('input[name="' + el._node.name + '"]').set('checked', null);
								}
								break;
							default:
								wb.one('input[name="' + el._node.name + '"]').set('value', g_li.one('.' + el._node.name).get('textContent'));
								break;
						}
					});
					//var text_block = (null !== data_cws_raw) ? data_cws_raw : g_li.one('.inner .content_part').get('innerHTML');
					//_inittmce(w_node, text_block);
					break;
				case 'tabs':
					text_block = (null !== this.cfg.get('panelNode')._node.getAttribute('data-cws-raw')) ?
						this.cfg.get('panelNode')._node.getAttribute('data-cws-raw') : this.cfg.get('content');
					_inittmce(w_node, text_block);
					wb.one('input[name="title"]').set('value', this.cfg.get('label'));
					dataStorage = this.cfg.get('boundingBox');
					break;
				case 'accs':
					current_acc = g_li.getData('accs').section_list[this.cfg];
					text_block = (null !== current_acc.content._node.getAttribute('data-cws-raw')) ?
						current_acc.content._node.getAttribute('data-cws-raw') : current_acc.content.one('>div').get('innerHTML');
					_inittmce(w_node, text_block);
					wb.one('input[name="title"]').set('value', current_acc.title.one('a:first-of-type').get('textContent'));
					dataStorage = current_acc.title;
					break;
				default:
					var dtitle = g_li.getAttribute('cws-title');
					if (!dtitle.length && data.type === 'col' && data.id.substring(0, 4) !== 'cols') {
						dtitle = (g_li.one('h4>strong').get('textContent') !== data.title) ? g_li.one('h4>strong').get('textContent') : '';
					}
					var row_img = g_li.getAttribute('cws-row-img');
					var e_style = wb.one('select[name^="extra_style"]');
					if (e_style && undefined !== g_li.getData('extra_style')) {
						e_style.set('value', g_li.getData('extra_style'));
					}

					if (dtitle.length && wb.one('input[name="title"]')) {
						wb.one('input[name="title"]').set('value', dtitle);
					}
					if (row_img.length) {
						wb.one('input[name="p_cws-pb-row-img"]').set('value', row_img);
						wb.one('img#img-cws-pb').set('src', row_img);
						wb.one('a#pb-media-cws-pb').hide();
						wb.one('a#pb-remov-cws-pb').show();
					}
					assignFormElements(wb, g_li);
					break;
			}

			if (typeof tinymce == 'undefined') {
				_switchTextEditor(null, text_block);
			}

			// initialize tmce window if needed
			if ( typeof tinymce != 'undefined' && g_li.one('.inner .content_part') ) {
				var text_block = (null !== data_cws_raw) ? data_cws_raw : g_li.one('.inner .content_part').get('innerHTML');
				_inittmce(w_node, text_block);
			}

			if ('tcol' !== template) {
				// this should be reworked to avoid overbloating
				wb.all('input').each(function(v, k) {
					if (undefined !== g_li.getData(v.get('name')) && 'title' !== v.get('name')) {
						if ('checkbox' == v.getAttribute('type')) {
							v.set('checked', g_li.getData(v.get('name')));
							if (undefined !== v.getData('options')) {
								processInputOptions(v._node);
							}
						} else {
							v.set('value', g_li.getData(v.get('name')));
						}
					} else {
						var name_parts = v.get('name').split('_');
						if ('c' === name_parts[0]) {
							if ('text' === name_parts[2]) {
								v.set('value', g_li.one('.inner .' + name_parts[1]).get('textContent'));
							} else {
								v.set('value', g_li.one('.inner .' + name_parts[1])._node.getAttribute(name_parts[2]));
							}
						}
					}
				});
			}
			//jQuery('select.sel2').each(function() {	jQuery(this).select2({allowClear: true});	});
			cws_pb_select();
			if ('col' !== template) { // no fa-icons for row !!!
				// probably need to rework later in case we need it
				initIconOptions(wb, dataStorage);
			}
			document.getElementById('pb_overlay').style.display = 'block';
		}

		var saveFormElements = function(gli, v) {
			var param = gli.getData('params');
			if (undefined === param) {
				param = new Object;
				gli.setData('params', param);
			}
			var value = null;
			if (undefined !== param) {
				switch (v._node.type) {
					case 'radio':
						if (v.get('checked')) {
							value = v.get('value');
						}
						break;
					case 'checkbox':
						value = v.get('checked');
						break;
					case 'text':
					case 'textarea':
					case 'select-one':
						value = v.get('value');
						break;
					case 'select-multiple':
						var i = 0;
						value = '';
						Y.each(v._node.options, function(v) {
							if (v.selected == true) {
								value += i>0 ? ',' : '';
								value += v.value;
								i++;
							}
						});
						//value = value.length > 0 ? value : null;
						break;
				}
				if ( value !== null && value.toString().length>0 ) {
					param[v.get('name')] = value;
				} else if (undefined !== param[v.get('name')]) {
					delete param[v.get('name')];
				}
			}
		}

		var assignFormElements = function(wb_form, gli) {
			var ot = gli.one('div[data-render]');
			if ( ot ) {
				var raw = ot.getData('cws-raw');
				var item_params = evalparam(trim(raw.substring(raw.indexOf(' '), raw.length-1)));
				// copy special params for the form for easy parsing in the next for (a lot of fors here)
				copyParamsFromData(gli.getData('params'), item_params);
				var item_params_size = 0;
				for (var key in item_params) {
					if ( item_params.hasOwnProperty(key) ) {
						item_params_size++;
						var cur_input = wb_form.one('input[name="' + key + '"],select[name="' + key + '"]');
						if (!cur_input) {
							// let's try to add p_ to input names
							cur_input = wb_form.one('input[name^="p_' + key + '"],select[name="p_' + key + '"],textarea[name="p_' + key + '"]');
							// check if data-options are there in order to trigger onchange event
							if (cur_input && undefined !== cur_input._node.attributes['data-options']) {
								Y.each(cur_input._node.options, function(v) {
									if (v.value === item_params[key]) {
										v.selected = true;
									} else {
										v.selected = false;
									}
								});
								// update options depending on sel_by value
								processInputOptions(cur_input._node);
							}
						}
						if (cur_input) {
							AssignElement(wb_form, cur_input, item_params, key);
						}
					}
				}
				if (!item_params_size) {
					wb_form.all('input[name^="p_sel"],select[name^="p_sel"]').each(function(el) {
						processInputOptions(el._node);
					});
				}
			} else {
				var item_params = new Object;
				copyParamsFromData(gli.getData('params'), item_params);
				var item_params_size = 0;
				for (var key in item_params) {
					if ( item_params.hasOwnProperty(key) ) {
						item_params_size++;
						cur_input = wb_form.one('input[name^="' + key + '"],select[name^="' + key + '"]');
						if ('p_sel' === key.substring(0,5) && cur_input) {
							Y.each(cur_input._node.options, function(v) {
								if (v.value === item_params[key]) {
									v.selected = true;
								} else {
									v.selected = false;
								}
							});
							// update options depending on sel_by value
							processInputOptions(cur_input._node);
						} else if (cur_input) {
							AssignElement(wb_form, cur_input, item_params, key);
						}
					}
				}
				if (!item_params_size) {
					wb_form.all('input[name^="p_sel"],select[name^="p_sel"]').each(function(el) {
						processInputOptions(el._node);
					});
				}
			}
		}

		var AssignElement = function (wb_form, cur_input, item_params, key) {
			var input_type = cur_input.get('type');
			switch (input_type) {
				case 'radio':
					cur_input = wb_form.one('input[name="'+key+'"][value="'+item_params[key]+'"]');
					if (!cur_input) {
						cur_input = wb_form.one('input[name^="p_'+key+'"][value="'+item_params[key]+'"]');
					}
					cur_input._node.checked = true;
					break;
				case 'checkbox':
					cur_input._node.checked = item_params[key];
					break;
				case 'text':
				case 'select-one':
				case 'textarea':
					cur_input.set('value', item_params[key]);
					break;
				case 'select-multiple':
					var sel_options = item_params[key].split(',');
					if (sel_options.length && sel_options[0].length > 0 ) {
						Y.each(cur_input._node.options, function(v) {
							if (-1 !== sel_options.indexOf(v.value)) {
								v.selected = true;
							}
						});
					}
					break;
			}
		}

		var copyParamsFromData = function (dataFrom, dataTo) {
			for (var key in dataFrom) {
				if (dataFrom.hasOwnProperty(key) && 'p_' === key.substring(0,2)) {
					dataTo[key] = dataFrom[key];
				}
			}
		}

		var getFilteredTmceContent = function(textmode) {
			var obj = '';
			if ('html' === textmode) {
				obj = window.switchEditors.wpautop(nestedPanel.bodyNode.one('.wp-editor-area').get('value'));
			} else {
				//obj = tinyMCE.activeEditor.getContent({format: 'html'});
				obj = tinyMCE.activeEditor.getContent();
				//obj = obj.replace(/<div class="wpview-wrap".*?>.*(<iframe.*\/iframe>).*&nbsp;<\/p><\/div>/i, "$1");
			}
			/*obj = obj.replace(/<p><\/p>/g, '<br>');
			obj = obj.replace(/<div>&nbsp;<\/div>/g, '<br>');*/
			return obj;
		}

		var collectPanelParams = function(gli, w_type) {
			var cat_select = Y.one('.yui3-widget-bd').one('select[name="p_cats"]') ? Y.one('.yui3-widget-bd').one('select[name="p_cats"]')._node : null;
			var cats = '';
			if (cat_select) {
				Y.each(cat_select.options, function(v) {
					cats += v.selected === true ? v.value + ',' : '';
				});
				cats = cats.length > 0 ? ' cats="' + cats.substring(0,cats.length-1) +  '"' : '';
			}
			var items = '';
			Y.all('.yui3-widget-bd *[name^="p_"]:not(select[name="p_cats"])').each(function(el) {
				if ( el._node.type === 'radio' && el._node.checked === false || el._node.type !== 'radio') {
					var val = '';
					if (el._node.type === 'select-multiple') {
						Y.each(el._node.options, function(v) {
							val += v.selected === true ? v.value + ',' : '';
						});
						val = val.length > 0 ? val.substring(0,val.length-1) : '';
					}	else {
						val = el._node.value;
					}
					if (val.length) {
						items += ' ' + el._node.name.substring(2) + '="' + val + '"';
					}
				}
			});
			var obj = '[pb_' + w_type + cats + items + '/]';
			// compare it in case only title was changed, for example
			if (gli.one('div[data-render]').getData('cws-raw') !== obj) {
				process_sc(obj, gli.one('div[data-render]'));
			}
		}

		var panx, pany;
		var scrx, scry;

		var showpanel = function(title) {
			if (null === nestedPanel) {
				scrx = (window.scrollX || window.pageXOffset);
				scry = (window.scrollY || window.pageYOffset);
				//console.log('X: ' + panx);
				//console.log('Y: ' + pany);

				nestedPanel = new Y.Panel({
					headerContent: title,
					bodyContent: '',
					width: 800,
					zIndex: 1000,
					x: panx + scrx,
					y: pany + scry + 130,
					visible: false,
					modal: true,
					render: '#wpwrap',
					buttons: [{
						value: 'Apply',
						section: Y.WidgetStdMod.FOOTER,
						action: function(e) {
							e.preventDefault();
							var wb;
							var bSkipTitle = false;
							var w_type = g_li.getData('data').type;
							var w_id = g_li.getData('data').id.substring(0,2);
							var switch_text = this.bodyNode.one('#cws-switch-text');
							var textmode = switch_text ? switch_text.getData('mode') : null;
							var btp = g_li.getData('bTitlePref');
							if ( (true === btp && 'w_' === w_id && 'tabs' !== w_type && 'accs' !== w_type) || (false === btp && 'w_' === w_id) || (true === btp && 'co' !== w_id && w_type === 'col') ) {
								var dataStorage = g_li.one('.inner');
								switch (w_type) {
									case 'col':
										if ('cols' !== g_li.getData('data').id.substring(0,3)) {
											var w_type_row = '';
											switch (g_li.getData('data').id.substring(0,4)) {
												case 'port':
													w_type_row = 'portfolio';
													break;
												case 'ourt':
													w_type_row = 'ourteam';
													break;
												case 'blog':
													w_type_row = 'blog';
													break;
											}
											collectPanelParams(g_li, w_type_row);
										}
										break;
									case 'text':
										var obj = getFilteredTmceContent(textmode);
										process_sc(obj, g_li.one('.inner'));
										//var mod_inner = g_li.one('.inner');
										//mod_inner.empty().append(textblock_c);
										//if ( crc32(obj) != crc32(textblock_c) ) {
										// save raw content as data attribute
										//	mod_inner._node.setAttribute('data-cws-raw', obj);
										//}

										//g_li.one('.inner').empty().append(obj);
										//data.title = wb.one('input[name="title"]').get('value');
										break;
									case 'ourteam':
									case 'portfolio':
									case 'blog':
									case 'tweet':
										collectPanelParams(g_li, w_type);
										break;
									case 'tcol':
									case 'callout':
										var obj = getFilteredTmceContent(textmode);
										process_sc(obj, g_li.one('.inner .content_part'));
										break;
									case 'tabs':
										wb = Y.one('.yui3-widget-bd');
										obj = getFilteredTmceContent(textmode);
										process_sc(obj, current_tab.get('panelNode'));
										/*current_tab.set('content', textblock_c);
										if ( crc32(obj) != crc32(textblock_c) ) {
											current_tab.get('panelNode')._node.setAttribute('data-cws-raw', obj);
										}*/
										current_tab.set('label', wb.one('input[name="title"]').get('value'));
										bSkipTitle = true;
										dataStorage = current_tab.get('boundingBox');
										break;
									case 'accs':
										wb = Y.one('.yui3-widget-bd');
										obj = getFilteredTmceContent(textmode);
										process_sc(obj, current_acc.content);
										/*current_acc.content.one('>div').set('innerHTML', obj);
										if ( crc32(obj) != crc32(textblock_c) ) {
											current_acc.content._node.setAttribute('data-cws-raw', obj);
										}*/
										current_acc.content.get('parentNode').get('parentNode')
											.one('#' + current_acc.content.get('parentNode').get('aria-labeledby') + ' a')
											.set('textContent', wb.one('input[name="title"]').get('value'));
										bSkipTitle = true;
										dataStorage = current_acc.title;
										break;
								}
							}
							// save icon or image on a designated place
							wb = Y.one('.yui3-widget-bd section.icon-options');
							if (undefined !== wb && undefined !== dataStorage) {
								processIconOptions( wb, dataStorage );
							}

							var bIsBgChanged = false;
							// save all extra features (except for icon-options section)
							this.bodyNode.all('input:not(section input),select:not(section select),textarea[name^="p_"]').each(function(v, k) {
								//g_li.setData(v.get('name'), v.get('value'));
								var name_parts = v.get('name').split('_');
								switch (name_parts[0]) {
									case 'title':
										if (!bSkipTitle) {
											var data = g_li.getData('data');
											var title_old = data.title;
											data.title = v.get('value');
											g_li.setAttribute('cws-title', v.get('value'));
											var h4_title = g_li.one('h4>strong') || g_li.one('h4'); // for cases with row modules
											h4_title.set('textContent', v.get('value'));
											g_li.setData('data', data);
											if (w_type === 'tcol') {
												g_li.one('.' + v.get('name')).set('textContent', v.get('value'));
											}
										}
										break;
									case 'c': // compound name, [1] class name, [2] attribute
										var module_node = g_li.one('.inner .' + name_parts[1]);
										if ('text' === name_parts[2]) {
											module_node.set('textContent', v.get('value'));
										} else {
											module_node._node.setAttribute(name_parts[2], v.get('value'));
										}
										break;
									case 'ishilited':
										g_li.one('.inner')._node.setAttribute('data-cws-ishilited', v.get('checked') ? '1' : '');
										break;
									case 'order':
										if ('url' === name_parts[1]) {
											g_li.one('a.button_text').set('href', v.get('value'));
										}
										break;
									case 'extra':
										// extra_style_col
										g_li.setData('extra_style', v.get('value'));
										break;
									default:
										if ( undefined === v.getData('pb-skip') ) {
											var inp = g_li.one('.' + v.get('name'));
											if (inp) {
												inp.set('textContent', v.get('value'));
											}
											if ('p' === name_parts[0].substring(0,2)) {
												switch (name_parts[1]) {
													case 'cws-pb-row-img-id':
														if (undefined !== v.getData('dim') && v.getData('dim').length > 0) {
															var dims = v.getData('dim').split(':');
															if (undefined !== v._node.dataset['key']) {
																// in case where data-key is present (which should be for all media types)
																if (undefined === g_li.getData('params')[v._node.dataset['key']]) {
																	g_li.getData('params')[v._node.dataset['key']] = {};
																}
																g_li.getData('params')[v._node.dataset['key']]['p_row_img_id'] = v.get('value');
																g_li.getData('params')[v._node.dataset['key']]['p_row_img_w'] = dims[0];
																g_li.getData('params')[v._node.dataset['key']]['p_row_img_h'] = dims[1];
															}	else {
																g_li.getData('params')['p_row_img_id'] = v.get('value');
																g_li.getData('params')['p_row_img_w'] = dims[0];
																g_li.getData('params')['p_row_img_h'] = dims[1];
															}
														}
														break;
													case 'cws-pb-row-img':
														bIsBgChanged = true;
														var row_img = v.get('value');
														g_li.setAttribute('cws-row-img', row_img);
														if (row_img.length > 0) {
															if (undefined !== g_li.getData('params')) {
																if (undefined !== v._node.dataset['key']) {
																	if (undefined === g_li.getData('params')[v._node.dataset['key']]) {
																		g_li.getData('params')[v._node.dataset['key']] = {};
																	}
																	g_li.getData('params')[v._node.dataset['key']]['p_row_img'] = row_img;
																} else {
																	g_li.getData('params')['p_row_img'] = row_img;
																}
															}
															row_img = row_img.length ? 'url(' + row_img + ')' : '';
															g_li.one('.inner').setStyle('background', row_img);
														} else {
															bIsBgChanged = false;
														}
														break;
													case 'c': // compound name, [1] class name, [2] attribute
														var module_node = g_li.one('.inner .' + name_parts[2]);
														if ('text' === name_parts[3]) {
															module_node.set('textContent', v.get('value'));
														} else {
															module_node._node.setAttribute(name_parts[3], v.get('value'));
														}
														break;
													case 'cust':
														if (name_parts[2] === 'color') {
															bIsBgChanged = true;
															var cust_color = v.get('value');
															//g_li.setData(v.get('name'), v.get('value'));
															g_li.one('.inner').setStyle('background', cust_color);
															var param = g_li.getData('params');
															if (undefined !== g_li.getData('params')) {
																delete g_li.getData('params')['p_row_img'];
															}
														}
														break;
												}
												saveFormElements(g_li, v)
											} else if (v.get('name').length > 0)  {
												if (v._node.type === 'radio' && v.get('checked') === true || v._node.type !== 'checkbox') {
													g_li.setData(v.get('name'), v.get('value'));
												}
												if (v._node.type === 'checkbox') {
													g_li.setData(v.get('name'), v.get('checked'));
												}
											}
										}
										break;
								}
							});
							if (!bIsBgChanged) {
								//debugger
								if ( g_li.one('.inner').getStyle('background').length > 0 ) {
									g_li.one('.inner').setStyle('background', '');
								}
								var param = g_li.getData('params');
								if (undefined !== param) {
									delete param['p_row_img'];
									delete param['p_row_img_h'];
									delete param['p_row_img_w'];
									delete param['p_row_img_id'];
								}
							}
							//removeItems();
						}
					}, {
						value: 'Close',
						section: Y.WidgetStdMod.FOOTER,
						action: function(e) {
							e.preventDefault();
							//tinymce.execCommand('mceRemoveControl', true, 'cws-pb-content');
							if (editor !== undefined && editor.getContentAreaContainer() != null) {
								tmceh = editor.getContentAreaContainer().firstChild.clientHeight;
							}
							if (w_node.one('textarea#' + w_node._yuid)) {
								if (typeof tinymce != 'undefined') {
									tinymce.remove('textarea#' + w_node._yuid);
								}
							}
							nestedPanel.hide();
							document.getElementById('pb_overlay').style.display = 'none';
							//w_node.remove();
							//nestedPanel = null;
						}
					}]
				});
				nestedPanel.plug(Y.Plugin.Drag, { handles: ['.yui3-widget-hd'] });
			} else {
				//nestedPanel.set('srcNode', content);
				nestedPanel.set('width', 500);
				nestedPanel.set('headerContent', title);
			}
			nestedPanel.on('visibleChange', function(e) {
				if (false === e.newVal) {
					setTimeout(function() {
						nestedPanel.destroy(false);
						nestedPanel = null;
						// here we should also de-init all select2 on nested panel if we use any
					}, 15);
				}
			});
			/*
			nestedPanel.on('click', function(e, el) {
				//e.target.bodyNode.delegate('click', onPanClick, 'div#cws-pb-callout' );
				//debugger
				// jQuery('select.sel2').each(function() {jQuery(this).select2({});});
				return false;
			});
			*/
			nestedPanel.on('focusedChange', function(e, el) {
				// otherwise shortcode dialogs inputs wouldn't get focus
				e.preventDefault();
			});
			return nestedPanel;
		}

		var initIconOptions = function(src, inner) {
			var gli_param = g_li.getData('params');
			if (undefined !== gli_param) {
				var img_icon_src = inner.getData('sa_img_icon') !== undefined ? inner.getData('sa_img_icon') : gli_param['p_iconimg'];
				var fa_icon = inner.getData('sa_fa_icon') !== undefined ? inner.getData('sa_fa_icon') : gli_param['p_iconfa'];
			} else {
				var img_icon_src = inner.getData('sa_img_icon');
				var fa_icon = inner.getData('sa_fa_icon');
			}
			if (undefined !== fa_icon && fa_icon.length) {
				//src.one('select#cws-pb-icons').set('value', fa_icon); // after select2 init this doesn't work
				jQuery('.yui3-widget-bd .icon-options select#cws-pb-icons').select2('val', fa_icon);
				src.one('input[name="fa"]')._node.checked = true;
				src.one('input[name="img"]')._node.checked = false;
			}
			if (undefined !== img_icon_src && img_icon_src.length) {
				src.one('img#img-cws-pb').set('src', img_icon_src);
				src.one('.img-wrapper:nth-child(2)')._node.removeAttribute('style');
				// remove Select image and enable remove image
				src.one('.img-wrapper a#pb-remov-cws-pb')._node.removeAttribute('style');
				src.one('.img-wrapper a#pb-media-cws-pb')._node.style.display = 'none';

				src.one('section.icon-options .image-part input#cws-pb-row-img').set('value', img_icon_src);
				// now we have to hide fa parts
				src.one('section.icon-options .image-part .img-wrapper:first-of-type')._node.style.display = 'none';
				// now we need to remove selected class from fa and add it to img
				src.all('section.icon-options li.redux-image-select').each(function(v){ v.toggleClass('selected'); });
				src.one('input[name="img"]')._node.checked = true;
				src.one('input[name="fa"]')._node.checked = false;
			} else {
				if (src.one('section.icon-options img#img-cws-pb')) {
					src.one('section.icon-options img#img-cws-pb').set('src', '');
					src.one('section.icon-options .img-wrapper a#pb-remov-cws-pb')._node.style.display = 'none';
				}
			}
		}

		var processIconOptions = function(src, gli) {
			if ( src ) {
				var which = src.one('input[name="fa"]')._node.checked ? 1 : src.one('input[name="img"]')._node.checked ? 2 : 0;
				switch (which) {
					case 2:
						// img
						var img_icon_src = '';
						if (null !== src.one('input#cws-pb-row-img-thumb')) {
							img_icon_src = src.one('input#cws-pb-row-img-thumb')._node.value;
						} else {
							// compatibility
							img_icon_src = src.one('img#img-cws-pb')._node.getAttribute('src');
						}
						gli.one('img.icon-options').set('src', img_icon_src)._node.removeAttribute('style');
						gli.one('span.fa')._node.style.display = 'none';
						gli.setData('sa_img_icon', img_icon_src);
						gli.setData('sa_fa_icon', '');
						if (!img_icon_src.length) {
							gli.one('img.icon-options')._node.style.display = 'none';
						}
					break;
					case 1:
						// fa
						var fa_icon = src.one('select#cws-pb-icons').get('value');
						if (fa_icon.length) {
							var span_fa = gli.one('span.fa i');
							span_fa._node.removeAttribute('class');
							gli.one('span.fa')._node.removeAttribute('style');
							span_fa.addClass('fa fa-2x fa-' + fa_icon);
							gli.one('img.icon-options')._node.style.display = 'none';
							gli.setData('sa_img_icon', '');
							gli.setData('sa_fa_icon', fa_icon);
						} else {
							gli.one('span.fa')._node.style.display = 'none';
							gli.setData('sa_fa_icon', '');
						}
					break;
				}
			}
			//src._node.parentNode.removeChild(src._node); // delete the whole section
		}

		var tmceh = 500;
		var editor;

		var onPanClick = function(e) {
		}

		var _inittmce = function(w_node, content) {
			if (typeof tinymce != 'undefined') {
				if (tinymce.editors.length == 0) {
					switchEditors.switchto(document.getElementById('content-tmce'));
				}
				var href = window.location.href;
				if ('#' === href.substr(href.length-1, 1)) {
					// there's a bug in tinymce if there's # at the end of the url
					window.history.pushState("tmce_bug", "tmce_bug", href.substr(0, href.length-1));
				}
				var tmce_h = (undefined !== g_li.getData('data').eheight) ? g_li.getData('data').eheight : tmceh;
				if (typeof tinymce != 'undefined') {
					tinymce.init({
						selector: 'textarea#' + w_node._yuid,
						auto_focus: w_node._yuid,
						content_css: tinyMCE.editors[0].settings.content_css,
						resize: true,
						height: tmce_h,
						convert_urls: false,
						ie7_compat: false,
						//inline: true,
						external_plugins: tinyMCE.editors[0].settings.external_plugins,
						plugins: tinyMCE.editors[0].settings.plugins,
						toolbar1: tinyMCE.editors[0].settings.toolbar1,
						toolbar2: tinyMCE.editors[0].settings.toolbar2,
						toolbar3: tinyMCE.editors[0].settings.toolbar3,
						setup: function(editor) {
							editor.on('init', function(e) {
								editor.setContent(content);
							});
							/*editor.on('focus', function(e) {
								//if (window.scrollY !== scry) {
								//	window.scrollTo(0, 135);
								//}
							});*/
						}
					});
					editor = tinymce.editors[tinymce.editors.length - 1];
				}
			}
		}

		var getContent = function(data) {
			//var id_name =
			return 'w_' == data.id.substring(0, 2) ? '#cws-pb-' + data.id : '#cws-pb-cols';
		}

		var from_acc;

		var initClonedDD = function(node, isnew, from) {
			isnew = typeof isnew !== 'undefined' ? isnew : false;
			var id = node.getData('id');
			var data = 'w_' == id.substring(0, 2) ? widgets[id] : feeds[id];
			node.removeAttribute('id');
			node.removeAttribute('class').addClass('item');
			//node.setData('data', data);
			if (!isnew) {
				if ('tabs' == data.type) {
					// need to remove + tab first
					var plus_tab = node.one('li[title="add a tab"]');
					plus_tab.get('parentNode').removeChild(plus_tab);
					node.all('li.yui3-tab div.control_panel').remove();
					var tabview = new Y.TabView({
						srcNode: node.one('.cws-pb-tabs'),
						plugins: [Addable, Removeable]
					});
					tabview.render(node.one('.inner'));
					node.setData('tabs', tabview);
				} else if ('accs' === data.type) {
					node.one('.cws-pb-accs-content>div:last-of-type').remove();
					node.one('.cws-pb-accs-content>div:last-of-type').remove();
					node.all('.yui3-acc-remove').remove();
					node.all('.yui3-pref').remove();
					var acc = node.one('.cws-pb-accs').get('parentNode');
					node.one('.yui3-accordion>ul').appendTo(acc);
					node.one('.yui3-accordion').remove();
					var ul_acc = node.one('ul.cws-pb-accs-content');
					var ul_li;
					var ul_li_div;
					var cws_raw;
					var cws_raws = []; // need to return raw data too
					var raws_i = 0;
					var that = this;
					ul_acc.all('>div[class^="yui3"]').each(function(a, k, t) {
						if (this.hasClass('yui3-accordion-title')) {
							ul_li = Y.Node.create('<li></li>');
							ul_acc.insert(ul_li);
						}
						if (this.hasClass('yui3-accordion-section-clip')) {
							cws_raw = this._node.getElementsByClassName('yui3-accordion-section')[0].getAttribute('data-cws-raw');
							cws_raws[raws_i] = cws_raw;
							raws_i++;
							//ul_acc.all('>div[class^="yui3"]').item(0).one('>div').appendTo(ul_li);
							ul_acc.all('>div[class^="yui3"]').item(0).all('>div *').each( function () {
								this.appendTo(ul_li_div);
							});
						} else {
							//var item_params = from_acc.all('.inner .yui3-accordion-title').item(k).getData();
							// !!!
							//a.setData(item_params);
							ul_acc.all('>div[class^="yui3"]').item(0).one('>div').appendTo(ul_li);
							ul_li.append('<div></div>'); // these are necessary, all content data should be inside div
							ul_li_div = ul_li.get('lastChild');
						}
						this.remove();
					});
					var srcNode = node.one('.cws-pb-accs');
					var vm = new Y.Accordion({
						srcNode: node.one('.cws-pb-accs-content'),
						replaceTitleContainer: false,
						animateOpenClose: false,
						animateInsertRemove: false,
						replaceSectionContainer: false,
						plugins: [AddableAcc, RemoveableAcc]
					});
					Y.delegate('click', onTitleClicked, srcNode, '.yui3-accordion-title', null, vm);
					//accordions.push(vm);
					node.setData('accs', vm);
					vm.render(srcNode);
					// return row data back
					from_acc = from.getData('accs'); // global variable
					for (var i = 0; i < vm.section_list.length - 1; i++) {
						var item_params = from_acc.section_list[i].title.getData();
						vm.section_list[i].title.setData(item_params);
						if (cws_raws[i] && cws_raws[i].length) {
							vm.section_list[i].clip._node.setAttribute('data-cws-raw', cws_raws[i]);
						}
					}
				}
			}

			var groups = 'cols' == data.id.substring(0, 4) ? ['cols'] : ['widgets']
			var dd = new Y.DD.Drag({
				node: node,
				data: data,
				groups: groups,
				bubbleTargets: Y.Portal
			});
			dd.plug(Y.Plugin.DDProxy, {
				moveOnEnd: false,
				cloneNode: true,
				borderStyle: 'none'
			});

			uls = node.all('.cwspb_widgets>ul');
			uls.each(function(v, k) {
				var tar = new Y.DD.Drop({
					node: v,
					padding: '5',
					groups: ['widgets'],
					bubbles: Y.Portal
				});
			});
			//Setup some stopper events
			dd.on('drag:start', _handleStart);
			dd.on('drag:end', stopper);
			dd.on('drag:drophit', stopper);

			//dd.set('node', node);
			dd.set('dragNode', Y.DD.DDM._proxy);
			setupModDD(node, data, dd);
			if ('object' === typeof from) {
				var params = new Object;
				node.setData('params', params);
				copyData(from.getData('params'), node.getData('params'));
				// icons data is here
				var icon_params = new Object;
				copyData(from.one('.inner').getData(), icon_params);
				node.one('.inner').setData(icon_params);
			}
		};

		var copyData = function (dataFrom, dataTo) {
			if ('object' === typeof dataFrom && 'object' === typeof dataTo) {
				for (var key in dataFrom) {
					if (dataFrom.hasOwnProperty(key) ) {
						dataTo[key] = dataFrom[key];
					}
				}
			}
		}

		//This creates the module, either from a drag event or from the cookie load
		var setupModDD = function(mod, data, dd) {
			var node = mod;
			//Listen for the click so we can react to the buttons
			node.setData('data', data);
			node.one('h4').on('click', _nodeClick);
			node.one('h4').on('change', _nodeSelect);

			//Remove the event's on the original drag instance
			dd.detachAll('drag:start');
			dd.detachAll('drag:end');
			dd.detachAll('drag:drophit');

			//It's a target
			dd.set('target', true);
			//Setup the handles
			dd.addHandle('h4').addInvalid('a');
			//Remove the mouse listeners on this node
			dd._unprep();
			//Update a new node
			dd.set('node', mod);
			//Reset the mouse handlers
			dd._prep();
		};

		function onTitleClicked(e, a) {
			var i = a.findSection(e.target);
			if (i >= 0) {
				a.toggleSection(i);
			}
		}

		var updateMod = function(new_data, old_li) {
			// first we need to determine
			// should we delete some cols or add them
			// if delete - check if there're any modules inside
			// and move them to the first one
			// if we add, then update spans if necessary
			// and just add some columns
			var id = old_li.get('id'),
				dd = Y.DD.DDM.getDrag('#' + id),
				old_data = dd.get('data'),
				n_colnum = parseInt(new_data.id.substring(4, 5)),
				colconf = new_data.id.substring(5),
				o_colnum = parseInt(old_data.id.substring(4, 5)),
				first_col = old_li.one('.cwspb_widgets>ul');
			n_colnum = (colconf.length) || parseInt(new_data.id.substring(4, 5));
			o_colnum = (old_data.id.substring(5).length) || parseInt(old_data.id.substring(4, 5));
			if (n_colnum < o_colnum) {
				// need to check for modules and move
				// them all to first column
				var k = n_colnum + 1;
				for (var i = k; i <= o_colnum; i++) {
					var curr_col = old_li.one('.cwspb_widgets>ul:nth-child(' + k + ')');
					curr_col.all('>li').each(function(el2) {
						first_col.appendChild(el2);
					});
					curr_col.get('parentNode').removeChild(curr_col);
				}
			} else if (n_colnum > o_colnum) {
				// we should just add columns
				var k = n_colnum - o_colnum;
				var str = ''
				for (var i = 0; i < k; i++) {
					str += '<ul class="item span12"></ul>'
				}
				var a = Y.Node.create(str);
				old_li.one('.cwspb_widgets').appendChild(a);
				var uls = old_li.all('.cwspb_widgets>ul');
				uls.each(function(v, k) {
					var tar = new Y.DD.Drop({
						node: v,
						padding: '5',
						groups: ['widgets'],
						bubbles: Y.Portal
					});
				});
			}
			// now update spans
			old_li.all('.cwspb_widgets ul.item').each(function(el1, c) {
				var span = colconf.length ? 12 * parseInt(colconf[c], 16) / (n_colnum + 1) : 12 / n_colnum;
				el1._node.className = el1._node.className.replace(/(span[0-9]+)/g, 'span' + span);
			});

			dd.set('data', new_data);
			old_li.setData('data', new_data);
			//old_li.one('h4 strong').set('textContent', new_data.title);
			old_li.setData('id', new_data.id);

			//Resync all the targets because something moved..
			Y.Lang.later(50, Y, function() {
				Y.DD.DDM.syncActiveShims(true);
			});
		}

		var accordions = [];

		//Helper method to create the markup for the module..
		var createMod = function(data, isnew, new_data) {
			var shortcode = '';
			isnew = typeof isnew !== 'undefined' ? isnew : false;
			var type = data.type;
			if ('col' == type) {
				var colnum = parseInt(data.id.substring(4, 5));
				var colid4 = data.id.substring(0, 4);
				var colconf = data.id.substring(5);
				var span = '';
				var str_inner = '<div class="cwspb_widgets">'
				if ('cols' === colid4) {
					if (colconf.length) {
						for (var i = 0; i < colconf.length; i++) {
							span = 12 * parseInt(colconf[i], 16) / colnum;
							str_inner += '<ul class="item span' + span + '"></ul>';
						}
					} else {
						span = 12 / colnum;
						for (var i = 0; i < colnum; i++) {
							str_inner += '<ul class="item span' + span + '"></ul>';
						}
					}
				} else {
					// if there're more than port needed, switch would be more appropriate
					switch (data.id) {
						case 'port1':
							str_inner = '<div class="portfolio" data-render="portfolio_fw"></div>';
							shortcode = !isnew ? '[pb_portfolio_fw /]' : new_data;
							break;
						case 'port2':
							str_inner = '<div class="portfolio" data-render="portfolio"></div>';
							shortcode = !isnew ? '[pb_portfolio /]' : new_data;
							break;
						case 'ourt1':
							str_inner = '<div class="ourteam" data-render="ourteam"></div>';
							shortcode = !isnew ? '[pb_ourteam /]' : new_data;
							break;
						case 'blog1':
							str_inner = '<div class="blog" data-render="blog"></div>';
							shortcode = !isnew ? '[pb_blog /]' : new_data;
							break;
					}
				}
				str_inner += '</div>';
			}
			else
			{
				switch (data.type) {
					case 'text':
						str_inner = isnew ? '<div></div>' : '<p>Some content here depending on data type</p>';
						break;
					case 'tabs':
						str_inner = isnew ? '<div class="cws-pb-tabs"><ul></ul><div></div></div>' : '<div class="cws-pb-tabs">\
							<ul>\
								<li><img class="icon-options" src style="display:none">\
									<span class="fa" style="display:none"><i class="fa fa-2x"></i></span>\
									<a href="#tab1">Tab 1</a></li>\
								<li><img class="icon-options" src style="display:none">\
									<span class="fa" style="display:none"><i class="fa fa-2x"></i></span>\
									<a href="#tab2">Tab 2</a></li>\
							</ul>\
							<div>\
							<div id="tab1" class="clearfix"><p>Tab 1 content</p></div>\
							<div id="tab2" class="clearfix"><p>Tab 2 content</p></div>\
							</div>\
						</div>';
						break;
					case 'accs':
						str_inner = '<div class="cws-pb-accs"></div><ul class="cws-pb-accs-content">'
						str_inner += isnew ? '</ul>' : '<li>\
								<div><img class="icon-options" src style="display:none">\
									<span class="fa" style="display:none"><i class="fa fa-2x"></i></span>\
								<a href="javascript:void(0);">#1</a></div>\
								<div><p>Slide #1 Content</p></div>\
							</li>\
							<li>\
								<div><img class="icon-options" src style="display:none">\
									<span class="fa" style="display:none"><i class="fa fa-2x"></i></span>\
								<a href="javascript:void(0);">#2</a></div>\
								<div><p>Slide #2 Content</p></div>\
							</li>\
						</ul>';
						break;
					case 'tcol':
						str_inner = '<div class="pricing_table_column">\
							<div>\
								<div class="pricing_table_header">\
									<div class="title">START</div>\'';
						// check if we need encouragement
						str_inner +=  ( Y.one('#cws-pb-tcol .row_options input[name="encouragement"]') ) ? '<div class="encouragement">Great for small business</div>' : '';
						str_inner += '</div>\
								<div class="price_part">\
									<span class="price_container">\
										<span class="currency">$</span>\
										<span class="price">6</span>\
										<span class="price_description">monthly</span>\
									</span>\
								</div>\
								<div class="content_part">\
								<p>Content</p>\
								</div>\
								<a href="/" class="cws_button button_text">buy now</a>\
							</div>\
						</div>';
						break;
					case 'callout':
						str_inner = '<div class="callout">\
							<img class="icon-options" src style="display:none">\
							<span class="fa" style="display:none"><i class="fa fa-2x"></i></span>\
							<div class="content_part">\
							<p>Nullam augue orci, luctus sed rutrum amet.</p>\
							</div>\
							<a href="#" class="btn button button-xlarge">Purchase Now</a>\
							</div>';
						break;
					case 'ourteam':
					case 'portfolio':
					case 'blog':
					case 'tweet':
						str_inner = '<div class="' + data.type + '" data-render="' + data.type + '">\
							</div>';
						shortcode = !isnew ? '[pb_' + data.type + ' /]' : '';
						break;
				}
			}

			if (undefined === data.title) {
				data.title = '';
			}
			var dtitle = data.dtitle !== undefined ? data.dtitle + ':' : '';
			if ('col' == type) {
				var sel_col = '';
				if ('cols' === colid4) {
					sel_col = '<label><select>';
					Y.each(feeds, function(v, k) {
						var issel = (data.id === v.id) ? ' selected' : '';
						sel_col += '<option value="' + v.id.substring(4) + '"' + issel + '>' + v.title + ' Column</option>'
					});
					sel_col += '</select></label>';
				} else {
					dtitle = data.title;
				}

				var str = '<li class="item" data-id="' + data.id + '">' +
					'<div class="mod">' +
					'<h4>' +
					'<div class="control_panel">' +
					'<a title="close module" class="close" href="#"></a>' +
					'<a title="minimize module" class="min" href="#"></a>' +
					'<a title="pref module" class="pref" href="#"></a>' +
					'<a title="clone module" class="clone" href="#"></a>' +
					'</div><strong>' +
					dtitle + sel_col +
					'</strong></h4><div class="inner">';
			}
			else {
				var div_options = Y.one('#cws-pb-' + data.type + ' div[data-options]');
				var module_options = '';
				if (div_options) {
					module_options = ' data-options="' + div_options.getData('options') + '"';
				}
				var str = '<li class="item" data-id="' + data.id + '"' + module_options + '>' +
					'<div class="mod">' +
					'<h4>' +
					'<div class="control_panel">' +
					'<a title="close module" class="close" href="#"></a>' +
					'<a title="minimize module" class="min" href="#"></a>' +
					'<a title="pref module" class="pref" href="#"></a>' +
					'<a title="clone module" class="clone" href="#"></a>' +
					'</div>' +
					dtitle + '<strong></strong>' +
					'</h4><div class="inner">';
			}

			str += str_inner;
			str += '</div></div></li>';
			var a = Y.Node.create(str);
			if ('tabs' == data.type) {
				if (!isnew) {
					var tabview = new Y.TabView({
						srcNode: a.one('.cws-pb-tabs'),
						plugins: [Addable, Removeable]
					});
				} else {
					var tabview = new Y.TabView({
						srcNode: a.one('.cws-pb-tabs')
					});
				}
				tabview.render(a.one('.inner'));
				a.setData('tabs', tabview);
			} else if ('accs' == data.type) {
				var srcNode = a.one('.cws-pb-accs');
				var vm = new Y.Accordion({
					srcNode: a.one('.cws-pb-accs-content'),
					replaceTitleContainer: false,
					animateOpenClose: false,
					animateInsertRemove: false,
					replaceSectionContainer: false,
					plugins: [AddableAcc, RemoveableAcc]
				});
				Y.delegate('click', onTitleClicked, srcNode, '.yui3-accordion-title', null, vm);
				//accordions.push(vm);
				a.setData('accs', vm);
				vm.render(srcNode);
			}
			if (shortcode.length > 0) {
				process_sc(shortcode, a.one('div[data-render]'));
				a.setData('params', new Object);
			}
			return a;
		};

		//Handle the start Drag event on the left side
		var _handleStart = function(e) {
			//Stop the event
			//console.log('handleStart');
			stopper(e);
			//Some private vars
			var drag = this,
				column_1st = null;
			/*		debugger
					delete data;
					data = {};*/
			//drag.get('data').title = '';
			var mod = createMod(drag.get('data'), false);
			//if ('cols' == drag.get('data').id.substring(0, 4)) {
			if ('col' == drag.get('data').type) {
				column_1st = Y.one('#cws_row');
			} else {
				column_1st = Y.one('.cwspb_widgets>ul');
			}
			if (!column_1st) {
				// Empty row placeholder, should prolly add one
				var row = createMod(feeds['cols1']);
				Y.one('#cws_row').appendChild(row);
				initClonedDD(row);
				column_1st = Y.one('.cwspb_widgets>ul');
			}
			if (column_1st) {
				//Add it to the first list
				//column_1st.appendChild(mod);
				column_1st.insertBefore(mod, column_1st._node.firstChild);
				//Set the item on the left column disabled.
				//drag.get('node').addClass('disabled');
				//Set the node on the instance
				drag.set('node', mod);
				//Add some styles to the proxy node.
				drag.get('dragNode').setStyles({
					opacity: '.5',
					borderStyle: 'none',
					zIndex: '100000000',
					width: '320px',
					height: '61px'
				});
				//Update the innerHTML of the proxy with the innerHTML of the module
				drag.get('dragNode').set('innerHTML', drag.get('node').get('innerHTML'));
				//set the inner module to hidden
				drag.get('node').one('div.mod').setStyle('visibility', 'hidden');
				//add a class for styling
				drag.get('node').addClass('moving');
				//Setup the DD instance
				setupModDD(mod, drag.get('data'), drag);

				//Remove the listener
				this.detach('drag:start', _handleStart);
			}
		};

		//Walk through the feeds list and create the list on the left
		var feedList = Y.one('#feeds ul#feeds-cols');
		Y.each(feeds, function(v, k) {
			var colid4 = v.id.substring(0, 4);
			var module_id = ('cols' !== colid4) ? document.getElementById('cws-pb-'+v.id) : 'cols';
			if (module_id) {
				var li = Y.Node.create('<li id="' + k + '">' + v.title + '</li>');
				feedList.appendChild(li);
				//Create the DD instance for this item
				_createFeedDD(li);
			}
		});

		feedList = Y.one('#feeds ul#feeds-modules');
		Y.each(widgets, function(v, k) {
			var module_id = document.getElementById('cws-pb-'+v.type);
			if (module_id) {
				var li = Y.Node.create('<li id="' + k + '">' + v.dtitle + '</li>');
				feedList.appendChild(li);
				//Create the DD instance for this item
				_createFeedDD(li);
			}
		});

		//This does the calculations for when and where to move a module
		var _moveMod = function(drag, drop) {
			if (drag.get('node').hasClass('item')) {
				var dragNode = drag.get('node'),
					dropNode = drop.get('node');

				//console.log('Previous: ' + dragNode.get('previousSibling'));
				var n_parent = dropNode.get('parentNode');
				if (dropNode && n_parent) {
					if (goingUp) {
						n_parent.insertBefore(dragNode, dropNode);
					} else {
						//n_parent.appendChild(dragNode);
						var actives = dragNode.siblings('li.yui3-dd-drop-active-valid');
						if (dragNode.get('nextSibling') && dragNode.get('nextSibling')._node == actives._nodes[actives._nodes.length-1]) {
							n_parent.appendChild(dragNode);
						} else {
							n_parent.insertBefore(dragNode, dropNode);
						}
						//n_parent.insert(dragNode, dragNode.get('previousSibling'));
					}
				}
				//Resync all the targets because something moved
				Y.Lang.later(50, Y, function() {
					Y.DD.DDM.syncActiveShims(true);
				});
			}
		};

	/*
	Handle the drop:enter event
	Now when we get a drop enter event, we check to see if the target is an LI, then we know it's our module.
	Here is where we move the module around in the DOM.
	*/
		Y.Portal.on('drop:enter', function(e) {
			if (!e.drag || !e.drop || (e.drop !== e.target)) {
				return false;
			}
			var node = e.drop.get('node');
			console.log('drop-enter: ' + node.get('tagName').toLowerCase());
			console.log('drop-enter: Has class item: ' + node.hasClass('item'));
			if (node.get('tagName').toLowerCase() === 'li' && node.hasClass('item') ) {
				_moveMod(e.drag, e.drop);
			}
		});

		//Handle the drag:drag event
		//On drag we need to know if they are moved up or down so we can place the module in the proper DOM location.
		Y.Portal.on('drag:drag', function(e) {
			var y = e.target.mouseXY[1];
			if (y < lastY) {
				goingUp = true;
			} else {
				goingUp = false;
			}
			lastY = y;
		});

	/*
	Handle the drop:hit event
	Now that we have a drop on the target, we check to see if the drop was not on a LI.
	This means they dropped on the empty space of the UL.
	*/
		Y.Portal.on('drag:drophit', function(e) {
			var drop = e.drop.get('node'),
				drag = e.drag.get('node');
			if (drop.get('tagName').toLowerCase() !== 'li') {
				if (drag.get('nextSibling')) {
					drop.insert(drag, drag.get('nextSibling'));
				} else {
					drop.appendChild(drag);
				}
			}
		});

		//Handle the drag:start event
		//Use some CSS here to make our dragging better looking.
		Y.Portal.on('drag:start', function(e) {
			stopper(e);
			var drag = e.target;
			if (drag.target) {
				drag.target.set('locked', true);
			}
			drag.get('dragNode').set('innerHTML', drag.get('node').get('innerHTML'));
			drag.get('dragNode').setStyles({
				opacity: '.5',
				borderStyle: 'none',
				zIndex: '100000000',
			});
			drag.get('node').one('div.mod').setStyle('visibility', 'hidden');
			drag.get('node').addClass('moving');
		});

		//Handle the drag:end event
		//Replace some of the styles we changed on start drag.
		Y.Portal.on('drag:end', function(e) {
			var drag = e.target,
				drop = e.target.target.get('node'),
				drag_node = drag.get('node');
			if ( !drop.ancestor('.mod') || !drop.ancestor('.mod').hasClass('minned') ) {
				if (drag.target) {
					drag.target.set('locked', false);
				}
				drag_node.setStyle('visibility', '');
				drag_node.one('div.mod').setStyle('visibility', '');
				drag_node.removeClass('moving');
				drag.get('dragNode').set('innerHTML', '');

				// mas: make a left item draggable again
				var dd = Y.DD.DDM.getDrag('#' + drag_node.get('id')),
					data = dd.get('data'),
					item = Y.one('#' + data.id);
				_createFeedDD(item);

				uls = drag_node.all('.cwspb_widgets>ul');
				uls.each(function(v, k) {
					var tar = new Y.DD.Drop({
						node: v,
						padding: '5',
						groups: ['widgets'],
						bubbles: Y.Portal
					});
				});
				if (null !== last_over) {
					//last_over.removeClass('over');
					last_over = null;
				}
			} else {
				var dd = Y.DD.DDM.getDrag('#' + drag_node.get('id')),
					data = dd.get('data'),
					item = Y.one('#' + data.id);
				_createFeedDD(item);

				drag_node.get('parentNode').removeChild(drag_node);
				dd.destroy();

				uls = drag_node.all('.cwspb_widgets>ul');
				uls.each(function(v, k) {
					var tar = new Y.DD.Drop({
						node: v,
						padding: '5',
						groups: ['widgets'],
						bubbles: Y.Portal
					});
				});
				if (null !== last_over) {
					last_over = null;
				}

			}
		});

		var last_over = null;

		//Handle going over a UL, for empty lists
		Y.Portal.on('drop:over', function(e) {
			var drop = e.drop.get('node'),
				drag = e.drag.get('node');

			if (drop.get('tagName').toLowerCase() !== 'li') {
				if (!drop.contains(drag)) {
					if (null !== last_over && last_over != drop) {
						//last_over.removeClass('over');
						last_over = null;
					}
					last_over = drop;
					//last_over.addClass('over');
					drop.appendChild(drag);
					//drop.insertBefore(drag, drop.firstChild);
					Y.Lang.later(50, Y, function() {
						Y.DD.DDM.syncActiveShims(true);
					});
				}
			}
		});

		//Create simple targets for the main lists..
		var uls = Y.all('#cws_row ul.list');
		uls.each(function(v, k) {
			var tar = new Y.DD.Drop({
				node: v,
				groups: ['cols'],
				padding: '20 0',
				bubbles: Y.Portal
			});
		});
	});

function processInputOptions (el) {
	// we'll use pure js here, no jquery
	var options_pairs = el.dataset['options'].split(';');
	var parent = document.querySelectorAll('.yui3-widget-bd')[0]; // this one should be the only one
	for (var i=0; i<options_pairs.length; i++) {
		var pair = options_pairs[i].split(':');
		switch (pair[0]) {
			case 'toggle':
				var bElEnabled = (parent.getElementsByClassName('row '+pair[1])[0].style.display === '');
				parent.getElementsByClassName('row '+pair[1])[0].style.display = el.checked ? (bElEnabled ? 'none' : '') : (bElEnabled ? 'none': '');
				//jQuery(el).closest('.yui3-widget-bd').find('div.row.'+pair[1]).toggle(300);
			break;
			case 'select':
				var op_options = (undefined !== el.options[el.selectedIndex] && undefined !== el.options[el.selectedIndex].dataset.options) ? el.options[el.selectedIndex].dataset.options : '';
				if (op_options.length) {
					options_pairs = op_options.split(';');
					for (var i=0; i<options_pairs.length; i++) {
						pair = options_pairs[i].split(':');
						switch (pair[0]) {
							case 'enable':
							parent.getElementsByClassName('row '+ pair[1])[0].className = parent.getElementsByClassName('row '+pair[1])[0].className.replace(/\s+disable/gm,'');
							break;
							case 'disable':
							//parent.querySelectorAll('select[name^="p_'+pair[1]+'"]')[0].value = [];
							jQuery('.yui3-widget-bd div.row[class*="'+pair[1]+'"] select,'+
								'.yui3-widget-bd div.row[class*="'+pair[1]+'"] input[type="text"],'+
								'.yui3-widget-bd div.row[class*="'+pair[1]+'"] input[type="hidden"],'+
								'.yui3-widget-bd div.row[class*="'+pair[1]+'"] .img-wrapper img'
								).each( function() {
								//debugger
								if ('text' === this.type || 'hidden' === this.type) {
									jQuery(this).val('');
								} else if ('select' === this.type) {
									jQuery(this).select2('val', '');
								} else if (undefined === this.type) {
									jQuery(this).attr("src","");
								}
							});
							//var a = parent.querySelectorAll('select[name^="p_'+pair[1]+'"]')[0].options;
							//jQuery.each(a, function(v, e) {e.selected=false;});
							parent.getElementsByClassName('row '+pair[1])[0].className = parent.getElementsByClassName('row '+pair[1])[0].className.replace(/\s+disable/gm,'') + ' disable';
							break;
						}
					}
				}
			break;
		}
	}
}

function cws_pb_select() {
	jQuery('.yui3-widget-bd input[data-options],.yui3-widget-bd select[data-options]').on('change', function(e) {
		processInputOptions(this);
	});

	jQuery('a[id="pb-media-cws-pb"]').on('click', function() {
		var that = this;
		var media_editor_attachment_backup = wp.media.editor.send.attachment;
		wp.media.editor.send.attachment = function(props, attachment) {
			var url = attachment.sizes.full.url;
			var thumb0 = attachment.sizes.thumbnail.url;
			var thumb = (attachment.sizes[props['size']].url || url);
			jQuery('img#img-cws-pb').attr('src', thumb);
			if (jQuery('input#img-cws-pb').length) {
				jQuery('input#img-cws-pb').attr('value', url);
			} else if (that.parentNode.getElementsByTagName('input').length) {
				// assign this image to the sibling input
				that.parentNode.querySelector('#cws-pb-row-img').value = url;
				// that.parentNode.querySelector('#cws-pb-row-img-thumb').value = thumb0;
				that.parentNode.querySelector('#cws-pb-row-img-id').value = attachment.id;
				that.parentNode.querySelector('#cws-pb-row-img-id').setAttribute('data-dim', String(attachment.width + ':' + attachment.height) );
			}
			jQuery(that).toggle(300);
			jQuery('a#pb-remov-cws-pb').toggle(300);
			wp.media.editor.send.attachment = media_editor_attachment_backup;
			return;
		}
		wp.media.editor.open(this);
	});

	jQuery('a[id="pb-remov-cws-pb"]').on('click', function(el) {
		jQuery(el.target).parent().find('input[id="cws-pb-row-img"]').attr('value', '');
		//jQuery('.yui3-widget-bd .img-wrapper input.image').attr('value', '');
		jQuery(el.target).parent().find('img#img-cws-pb').attr('src', '');
		//jQuery('.yui3-widget-bd .img-wrapper img#img-cws-pb').attr('src', '');
		jQuery(this).hide(300);
		jQuery(el.target).parent().find('a#pb-media-cws-pb').show(300);
		//jQuery('.yui3-widget-bd .img-wrapper a#pb-media-cws-pb').show(300);
	});

	jQuery('.icon-options li.redux-image-select').on('click', function(e) {
		var _this = jQuery(this);
		_this.addClass('selected');
		_this.siblings().removeClass('selected');
		_this.children('input').prop('checked',true)
		_this.siblings().children('input').prop('checked',false);
		var ind = _this.index();
		_this.parents('.icon-options').find('.image-part').children('.img-wrapper').eq(ind).fadeIn(300).siblings().fadeOut(300);
	});

	jQuery(".yui3-widget-bd input[data-default-color]").each(function(){
		jQuery(this).wpColorPicker();
	});

/*	jQuery('.reset_icon_options').on('click', function (e) {
		e.preventDefault();
		var icon_parents = jQuery(this).parents(".icon-options");
		initIconOptions();
		icon_parents.find('li.redux-image-select').eq(0).addClass('selected'); // default is fa
		icon_parents.find('li.redux-image-select').eq(1).removeClass('selected');
		icon_parents.find('#cws-pb-icons').select2("val","");
		icon_parents.find('img#img-cws-pb').attr('src','');
		icon_parents.find("a[id^='remov']").hide(300);
		icon_parents.find("a[id^='media']").show(300);
	});*/

	jinitIconOptions();

	jQuery('.yui3-widget-bd select').each(function() {
		jQuery(this).select2();
	});

	jQuery('.yui3-widget-bd .icon-options select#cws-pb-icons').each(function() {
		jQuery(this).select2({
			allowClear: true,
			placeholder: " ",
			formatResult: addIconToSelectFa,
			formatSelection: addIconToSelectFa,
			escapeMarkup: function(m) { return m; }
		});
	});

	function addIconToSelectFa(icon) {
		if ( icon.hasOwnProperty( 'id' ) ) {
			return "<span><i class='fa fa-" + icon.id + "'></i>" + "&nbsp;&nbsp;" + icon.id.toUpperCase() + "</span>";
		}
	}
}

function clone(obj) {
	var copy;
	// Handle the 3 simple types, and null or undefined
	if (null == obj || "object" != typeof obj) return obj;
	// Handle Date
	if (obj instanceof Date) {
		copy = new Date();
		copy.setTime(obj.getTime());
		return copy;
	}
	// Handle Array
	if (obj instanceof Array) {
		copy = [];
		for (var i = 0, len = obj.length; i < len; i++) {
			copy[i] = clone(obj[i]);
		}
		return copy;
	}
	// Handle Object
	if (obj instanceof Object) {
		copy = {};
		for (var attr in obj) {
			if (obj.hasOwnProperty(attr)) copy[attr] = clone(obj[attr]);
		}
		return copy;
	}
	throw new Error("Unable to copy obj! Its type isn't supported.");
}

function jinitIconOptions() {
	var curr_icon_o = jQuery('.yui3-widget-bd .icon-options');
	curr_icon_o.find('li.redux-image-select input[name="fa"]').prop('checked',true);
	curr_icon_o.find('li.redux-image-select input[name="img"]').prop('checked',false);
}
