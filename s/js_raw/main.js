/**
 * Emulate object's inheritance
 * @param function Child
 * @param function Parent
 * @returns function
 */
function ClassExtend(Child, Parent) {
	var F = function(){};
	F.prototype = Parent.prototype;
	Child.prototype = new F();
	Child.prototype.constructor = Child;
	Child.superclass = Parent.prototype;
	return Child;
}

/**
 * Overriding original jQuery methods & define new custom methods
 */
(function($){
	var __show, __hide;
	__show = jQuery.fn.show;
	jQuery.fn.show = function(){
		$(this).removeClass('hidden');
		return __show.apply(this, arguments);
	};
	__hide = jQuery.fn.hide;
	jQuery.fn.hide = function(){
		$(this).removeClass('visible');
		return __hide.apply(this, arguments);
	};
	jQuery.fn.toggle = function(){
		return $(this).is(':visible') ? jQuery.fn.hide.apply(this, arguments) : jQuery.fn.show.apply(this, arguments);
	};

	jQuery.fn.bindDoublePress = function(firstKeyCode, secondKeyCode, callback, context) {
		var isFirstKeyPressed=false, that=this;
		$(this).keyup(function(e){
			if(e.which == firstKeyCode) isFirstKeyPressed=false;
		}).keydown(function(e) {
			if(e.which == firstKeyCode) isFirstKeyPressed=true;
			if(e.which == secondKeyCode && isFirstKeyPressed == true) {
				callback = callback || function(){};
				callback.apply(that, [context]);
			}
		});
		return this;
	};
	jQuery.fn.bindCtrlPress = function(keyCode, callback, context) {
		var that=this;
		$(this).keydown(function(e) {
			if(e.ctrlKey && e.which==keyCode) {
				callback = callback || function(){};
				callback.apply(that, [context]);
			}
		});
	};
	jQuery.fn.bindCtrlEnter = function(callback, context){
		return jQuery.fn.bindCtrlPress.call(this, 13, callback, context);
		//return jQuery.fn.bindDoublePress.call(this, 17, 13, callback, context);
	};
	jQuery.fn.bindCtrlLeft = function(callback, context){
		return jQuery.fn.bindCtrlPress.call(this, 37, callback, context);
		//return jQuery.fn.bindDoublePress.call(this, 17, 37, callback, context);
	};
	jQuery.fn.bindCtrlRight = function(callback, context){
		return jQuery.fn.bindCtrlPress.call(this, 39, callback, context);
		//return jQuery.fn.bindDoublePress.call(this, 17, 39, callback, context);
	};



	/**
	 * sprintf and vsprintf for jQuery
	 * somewhat based on http://jan.moesen.nu/code/javascript/sprintf-and-printf-in-javascript/
	 *
	 * Copyright (c) 2008 Sabin Iacob (m0n5t3r) <iacobs@m0n5t3r.info>
	 * This program is free software: you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation, either version 3 of the License, or
	 * (at your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * @license http://www.gnu.org/licenses/gpl.html
	 * @project jquery.sprintf
	 */
	var formats = {
		'b': function(val) {return parseInt(val, 10).toString(2);},
		'c': function(val) {return String.fromCharCode(parseInt(val, 10));},
		'd': function(val) {return parseInt(val, 10);},
		'u': function(val) {return Math.abs(val);},
		'f': function(val, p) {
			p = parseInt(p, 10);
			val = parseFloat(val);
			if(isNaN(p && val)) {
				return NaN;
			}
			return p && val.toFixed(p) || val;
		},
		'o': function(val) {return parseInt(val, 10).toString(8);},
		's': function(val) {return val;},
		'x': function(val) {return ('' + parseInt(val, 10).toString(16)).toLowerCase();},
		'X': function(val) {return ('' + parseInt(val, 10).toString(16)).toUpperCase();}
	};

	var re = /%(?:(\d+)?(?:\.(\d+))?|\(([^)]+)\))([%bcdufosxX])/g;

	var dispatch = function(data){
		if(data.length == 1 && typeof data[0] == 'object') { //python-style printf
			data = data[0];
			return function(match, w, p, lbl, fmt, off, str) {
				return formats[fmt](data[lbl]);
			};
		} else { // regular, somewhat incomplete, printf
			var idx = 0;
			return function(match, w, p, lbl, fmt, off, str) {
				if(fmt == '%') {
					return '%';
				}
				return formats[fmt](data[idx++], p);
			};
		}
	};

	$.extend({
		sprintf: function(format) {
			var argv = Array.apply(null, arguments).slice(1);
			return format.replace(re, dispatch(argv));
		},
		vsprintf: function(format, data) {
			return format.replace(re, dispatch(data));
		}
	});

	$.extend({
		inherit: function(child, parent) {
			return ClassExtend(child, parent);
		}
	});
})(jQuery);

/**
 * @param s
 * @returns
 */
function __id(s) {
	if(typeof s == 'string' && s!=='') {
		var tags,l,c,x,v,i;
		tags = [
			'html','head','body','document',
			'a','p','div','span','em','b','u','i','iframe',
			'img','ul','ol','li','pre','center','hr',
			'table','tbody','tr','th','td','dd','dl','dt',
			'form','input','button','select','option','textarea',
			':', '['
		].sort(function (a,b) {if(a.length<b.length)return -1;else if(a.length>b.length)return 1;else return 0;}).reverse();
		s = $.trim(s);
		l = s.toLowerCase();
		for(x in tags) {
			v = tags[x];
			if(l.indexOf(v)===0) {
				c = l.charCodeAt(v.length);
				if((c>=95&&c<=122)) {
					s = '#'+s;
				}
				else {
					i=1;
				}
				break;
			}
		}
		c = s.charCodeAt(0);
		if(c>=95&&c<=122 && !i) {
			s = '#'+s;
		}
	}
	return s;
}

/**
 * Extended version of jQuery $
 * @param o
 */
function __$(o, context) {
	return context ? $(__id(o), $(__id(context))) : $(__id(o));
}

/**
 * Get JavaScript Configuration parameter value
 * @param what
 * @returns mixed
 */
function __jsc(what) {
	return window.JSC ? window.JSC[what] : null;
}

/**
 * Trace input variable in javascript debug console
 * @param what
 */
function _e(what) {
	var PRO = __jsc('PRO');
	if(PRO!==null && PRO==0) {
		if(window.console && window.console.log) {
			console.log(what);
		}
	}
}

/**
 * Predicate methods
 * Return {Boolean}
 */
var js_is = {
	/**
	 * Check if object defined and not empty
	 * @param o
	 * @returns {Boolean}
	 */
	defined : function(o) {
		return __$(o).length>0;
	},
	/**
	 * Check if object defined and hidden
	 * @param o
	 * @returns {Boolean}
	 */
	hidden : function(o) {
		return js_is.defined(o) && __$(o).is(':hidden');
	},
	/**
	 * Check if object defined and visible
	 * @param o
	 * @returns {Boolean}
	 */
	visible : function(o) {
		return !js_is.hidden(o);
	},
	/**
	 * check if browser supports canvas
	 * @returns
	 */
	canvasCompatible : function() {
		var result = false;
		try{
			result = !!document.createElement('canvas').getContext('2d');
		} catch(a){
			result = !!document.createElement('canvas').getContext;
		}
		return result;
	}
}

/**
 * Data type casting
 */
var js_cast = {
	/**
	 * To integer
	 */
	toInt : function(n) {
		return parseInt(n);
	}
}

/**
 * Get html samples
 */
var js_html = {
	/**
	 * Get Ajax Loader Image
	 */
	ajaxloader : function(options) {
		var defaults={}, attrs={}, a=[], n='';
		defaults = {
			src:__jsc('S_URL')+'img/ajaxloader.gif',
			width:16,
			height:11,
			alt:'loading...',
			title:'loading...',
			border:0
		};
		attrs = $.extend(defaults, options);
		for(n in attrs) {
			a.push(n+'="'+attrs[n]+'"');
		}
		return '<img %s />'.replace('%s', a.join(' '));
	}
};
/**
 * Helper utils
 */
var js_util = {
	/**
	 * Animated page scrolling to element's position.
	 * Callback function used in context of $.animate function
	 * @param o
	 * @param callback
	 * @returns {Boolean}
	 */
	scrollto : function(o, callback, speed) {
		if(js_is.defined(o)) {
			speed = speed || 600;
			var offset = (function(o){
				o = __$(o);
				var o_hidden = js_is.hidden(o);
				if(o_hidden) o.show();
				var offset = o.offset();
				if(o_hidden) o.hide();
				return offset;
			})(o);
			if(typeof callback != 'function') {
				callback = function(){};
			}
			$('html,body').animate({scrollTop: offset.top}, speed, callback);
		}
		return false;
	},
	/**
	 * Make element blinking a bit
	 */
	blink : function(o, ms) {
		var ms = ms || 120;
		return __$(o).show()
			.addClass('blink')
			.fadeOut(ms)
			.fadeIn(ms)
			.fadeOut(ms)
			.fadeIn(ms)
			.fadeOut(ms)
			.fadeIn(ms)
			.removeClass('blink')
		;
	},
	/**
	 * Control Input (Textarea) allowed max length
	 */
	controlLength : function(o, ctrl) {
		if(js_is.defined(o) && js_is.defined(ctrl)) {
			o = __$(o), ctrl = __$(ctrl);
			var maxLength = o.attr('maxlength');
			if(maxLength) {
				var text, leftLength;
				text = $.trim(o.val());
				leftLength = maxLength-text.length;
				if(leftLength<0) {
					o.val(text.substr(0, maxLength));
					leftLength = 0;
					js_util.blink(ctrl);
				}
				ctrl.html(leftLength);
			}
		}
	},
	/**
	 * Highlight text element given in hash-tag
	 */
	highlight : function(hash) {
		if (typeof hash == 'string' && hash!=='') {
			hash = hash.substring(hash.lastIndexOf('#')+1);
			var o_hash = __$(hash);
			if(js_is.defined(o_hash)) {
				$(function() {js_util.blink(o_hash, 200);}).delay(50);
			}
		}
		return false;
	}
}

/**
 * Retrieve captcha object from the server
 */
var js_captcha = {
	imgsrc_reload : function(img_id) {
		img_id = img_id || 'captcha_img';
		__$(img_id).attr('src', '/captcha/image.jpeg?dbg=0&rand='+Math.random());
		return false;
	}
};

/**
 * Location: country & city
 */
var js_loc= {

	source_id: null,

	target_select: null,
	target_status: 1,
	target_status_arr: [0,1,2], // 2 = 0 and 1

	get_country_states : function(country_id, state_select, state_status) {
		return this.get_target('country_states', country_id, state_select, state_status);
	},

	get_country_cities : function(country_id, city_select, city_status) {
		return this.get_target('country_cities', country_id, city_select, city_status);
	},

	get_state_cities : function(state_id, city_select, city_status) {
		return this.get_target('state_cities', state_id, city_select, city_status);
	},

	get_target : function(target_name, source_id, target_select, target_status) {

		if(!$.inArray(target_name, ['country_states', 'country_cities', 'state_cities'])) {
			return false;
		}

		this.source_id = js_cast.toInt(source_id);

		if(!this.source_id) {
			return false;
		}

		if(target_select) {
			this.target_select = target_select;
		}

		if(target_status && $.inArray(target_status, this.target_status_arr)) {
			this.target_status = target_status;
		}

		var that = this;

		$.ajax({
			url : '/json/location/',
			dataType : 'json',
			beforeSend : function() {
				__$(that.target_select).attr('disabled', true);
				__$(that.target_select).empty();
			},
			success : function(json_response) {
				if(json_response) {
					var id, data, name, is_capital, is_main, option;
					for(id in json_response) {
						data = json_response[id];
						name = (data.n) ? data.n : null;
						is_capital = (data.c) && js_cast.toInt(data.c);
						is_main = (data.m) && js_cast.toInt(data.m);
						if(name && id) {
							option = new Option(name, id);
							if(is_capital) {
								$(option).addClass('bold underline');
							}
							else if(is_main) {
								$(option).addClass('bold');
							}
							__$(that.target_select).append(option);
						}
					}
					if(__$(that.target_select).children().length!==0) {
						__$(that.target_select).removeAttr('disabled');
					}
					else {
						__$(that.target_select).append(new Option('---', 0));
					}
				}
			},
			data : {
				target : target_name,
				id : that.source_id,
				status : that.target_status,
				rand : Math.random(), dbg : 0
			}
		});

		return false;
	}
};

/**
 * User occupation & experience
 */
var js_occ = {
	cbx_limit : 10,
	toggle : function(el) {
		var parent = __$($(el).attr('id'));
		var child = __$($(el).attr('rel'));
		if(!parent.attr('checked')) {
			__$(':checkbox', child).removeAttr('checked');
		}
		return parent.attr('checked') ? child.show() : child.hide();
	},
	check_limit : function(parent_id, error_id) {
		var i = 0;
		var that = this;
		__$(parent_id+' :checkbox').each(function(index, item){
			if($(item).attr('checked')) {
				if(i >= that.cbx_limit) {
					i--;
					$(item).removeAttr('checked');
					__$(error_id).show().fadeOut(2000);
				}
				else {
					i++;
				}
			}
		});
		return false;
	}
};

/**
 * Comments
 */
var js_com = {
	o : [],
	auth : false,
	item_id : 0,
	item_type : null,
	url_add : '/comment/add/',
	url_get : '/comment/get/',
	url_upd : '/comment/upd/',
	url_clr : '/comment/clr/',
	url_del : '/comment/del/',
	init : function(item_type, item_id, max_len) {
		js_com.reinit();
		this.auth = js_is.defined(js_com.o.c_form);
		this.item_id = item_id || (js_is.defined(js_com.o.c_item_id) ? js_com.o.c_item_id.val() : 0);
		this.item_type = item_type || (js_is.defined(js_com.o.c_item_type) ? js_com.o.c_item_type.val() : null);
		js_com.o.c_toggle.unbind().bind('click', function(){return js_com.toggle();});
		js_com.close();
		js_com.reinit();
		if(js_com.auth) {
			this.url_get = js_is.defined(js_com.o.c_url_get) ? js_com.o.c_url_get.val() : this.url_get;
			this.url_add = js_is.defined(js_com.o.c_url_add) ? js_com.o.c_url_add.val() : this.url_add;
			this.url_clr = js_is.defined(js_com.o.c_url_clr) ? js_com.o.c_url_clr.val() : this.url_clr;
			this.url_del = js_is.defined(js_com.o.c_url_del) ? js_com.o.c_url_del.val() : this.url_del;
			js_com.o.c_form.unbind().bind('submit', function(){return js_com.add()});
		}
		js_com.highlight();
	},
	reinit : function() {
		var id_arr = [
			'cc_context','cf_context','ci_context',
			'c_toggle', 'c_open', 'c_close',
			'ce_context', 'c_form', 'c_text',
			'c_item_id', 'c_item_type', 'c_submit', 'c_submit_tip',
			'cf_ajaxloader', 'cf_length',
			'c_url_get', 'c_url_add', 'c_url_clr', 'c_url_del'
		];
		$.each(id_arr, function(index, id){
			js_com.o[id] = __$(id);
		});
		$('a[rel=c_url]', js_com.o.ci_context).unbind().bind('click', function(){return js_com.highlight($(this).attr('href'))});
		$('a[rel=c_clr]', js_com.o.ci_context).unbind().bind('click', function(){return js_com.clr($(this).attr('value'))});
		$('a[rel=c_del]', js_com.o.ci_context).unbind().bind('click', function(){return js_com.del($(this).attr('value'))});
		$('a[rel=c_crop]', js_com.o.ci_context).unbind().bind('click', function(){return js_crop.highlight($(this).attr('href'))});
		if(js_com.auth) {
			js_com.o.c_text.unbind().bind({
				'keyup' : function(){return js_com.left()},
				'focus' : function() {js_com.o.c_submit_tip.show()},
				'blur' : function() {js_com.o.c_submit_tip.hide()}
			}).bindCtrlEnter(function(){return js_com.add()});
			js_com.left();
		}
	},
	open : function() {
		js_com.o.c_open.hide();
		js_com.o.c_close.show();
		js_com.o.cf_context.show();
		js_util.scrollto(js_com.o.cc_context);
		if(js_com.auth) {
			js_com.o.c_text.focus();
		}
	},
	close : function() {
		js_com.o.c_open.show();
		js_com.o.c_close.hide();
		js_com.o.cf_context.hide();
	},
	toggle : function() {
		js_is.hidden(js_com.o.cf_context) ? js_com.open() : js_com.close();
		js_com.o.c_toggle.blur()
	},
	left : function() {
		if(js_com.auth) {
			js_util.controlLength(js_com.o.c_text, js_com.o.cf_length);
		}
	},
	addText : function(text) {
		if(js_com.auth) {
			js_com.o.c_text.val(js_com.o.c_text.val()+text);
			js_com.left();
			js_com.open();
		}
	},
	setText : function(text) {
		if(js_com.auth) {
			js_com.o.c_text.val(text);
			js_com.left();
			js_com.open();
		}
	},
	add : function() {
		return js_com.request({
			url : js_com.url_add,
			type : 'POST',
			data : {
				text : js_com.o.c_text.val()
			}
		});
	},
	get : function() {
		return js_com.request({
			url : js_com.url_get
		});
	},
	upd : function(id) {
		return js_com.request({
			url : js_com.url_upd,
			type : 'POST',
			data : {
				'id' : id,
				text : js_com.o.c_text.val()
			}
		});
	},
	clr : function(id) {
		return js_com.request({
			url : js_com.url_clr,
			data : {
				'id' : id
			}
		});
	},
	del : function(id) {
		return js_com.request({
			url : js_com.url_del,
			data : {
				'id' : id
			}
		});
	},
	request : function(options) {
		var defaults = {
			url : js_com.url_get,
			type : 'GET',
			dataType : 'json',
			data : {
				'item_id' : js_com.item_id,
				'item_type' : js_com.item_type,
				'json' : 1,
				'dbg' : 0, 'rand' : Math.random()
			},
			beforeSend : function() {
				js_com.o.cf_ajaxloader.html(js_html.ajaxloader());
			},
			afterSend : function() {
				js_com.o.cf_ajaxloader.html(null);
			},
			error : function() {
				this.afterSend();
			},
			success : function(data) {
				if(data.error && data.error.length) {
					js_com.showError(data.error);
				}
				else {
					if(typeof data.html != 'undefined' && data.html) {
						js_com.paste(data.html);
					}
					var count = 0;
					if(typeof data.count != 'undefined') {
						count = data.count;
						$($.sprintf('span[rel=cc-%s-%u]',js_com.item_type, js_com.item_id)).html(data.count);
					}
					if(typeof data.id != 'undefined') {}
					js_com.clearText();
					js_com.clearError();
					js_com.close();
					if(typeof data.url != 'undefined') {
						count>5 ? js_com.highlight(data.url) : js_util.highlight(data.url);
					}
				}
				this.afterSend();
			}
		};
		$.ajax($.extend(true, defaults, options));
		return false;
	},
	paste : function(html) {
		js_com.o.ci_context.html(html);
		js_com.reinit();
	},
	highlight : function(hash, pattern) {
		var hash = hash || window.location.hash;
		pattern = pattern || /c\d+/;
		if (typeof hash == 'string' && hash!=='') {
			hash = hash.substring(hash.lastIndexOf('#')+1);
			var o_hash = __$(hash);
			if(js_is.defined(o_hash)) {
				if(hash.match(pattern)) {
					callback = function() {
						window.location.hash = '#'+hash;
						js_util.blink(o_hash);
					};
					js_util.scrollto(o_hash, callback);
				}
				else if(hash=='c_open') {
					js_com.open();
				}
				else {
					js_util.scrollto(o_hash);
				}
			}
		}
		return false;
	},
	showError : function(error) {
		var html = '';
		var replace_func = function(string){return $.sprintf('<li class="red"> - %s</li>', string);}
		switch(typeof error) {
			case 'string':
			case 'number':
				html = replace_func(error);
				break;
			case 'object':
				$.each(error, function(index, e){
					html += replace_func(e);
				});
				break;
		}
		js_com.clearError();
		if(html) {
			$('ul:first', js_com.o.ce_context.show()).html(html);
			js_com.open();
		}
	},
	clearError : function() {
		$('ul', js_com.o.ce_context.hide()).html(null);
	},
	clearText : function() {
		js_com.o.c_text.val(null);
		js_com.left();
	},
	isAuth : function() {
		return js_com.auth;
	}
};

/**
 * Voting
 */
var js_vote = {
	o : [],
	auth : false,
	action : null,
	item_id : 0,
	item_type: null,
	url_get: '/vote/get/',
	url_add: '/vote/add/',
	url_del: '/vote/del/',
	loaded : 0,
	init : function(item_type, item_id) {
		js_vote.reinit();
		this.item_id = item_id || this.item_id;
		this.item_type = item_type || this.item_type;

		this.auth = js_is.defined(js_vote.o.va_context);

		if(this.auth) {
			__$('a[rel=v-add-ctrl]', this.o.va_context).unbind().bind('click', function(){$(this).blur();return js_vote.add($(this).attr('value'));});
		}
		__$('v-get-ctrl').unbind().bind('click', function(){$(this).blur();return js_vote.toggle();});
	},
	reinit : function() {
		var id_arr = [
			'va_context','vv_context', 've_context', 'vi_context',
			'vf_ajaxloader', 'vv_ajaxloader'
		];
		$.each(id_arr, function(index, id){
			js_vote.o[id] = __$(id);
		});
		__$($.sprintf('a[rel=v-%s-del]', js_vote.item_type), js_vote.o.vi_context).unbind().bind('click', function(){return js_vote.del($(this).attr('value'));});
	},
	add : function(vote_type) {
		this.action = 'add';
		return js_vote.request({
			url : js_vote.url_add,
			type : 'POST',
			beforeSend : function() {
				js_vote.o.vf_ajaxloader.html(js_html.ajaxloader());
			},
			afterSend : function() {
				js_vote.o.vf_ajaxloader.html(null);
			},
			data : {'vote_type':vote_type}
		});
	},
	get : function() {
		this.action = 'get';
		return js_vote.request({
			url : js_vote.url_get
		});
	},
	del : function(id) {
		this.action = 'del';
		return js_vote.request({
			url : js_vote.url_del,
			data : {
				'id' : id
			}
		});
	},
	request : function(options) {
		var defaults = {
			url : js_vote.url_get,
			type : 'GET',
			dataType : 'json',
			data : {
				'item_id' : js_vote.item_id,
				'item_type' : js_vote.item_type,
				'json' : 1,
				'dbg' : 0, 'rand' : Math.random()
			},
			beforeSend : function() {
				js_vote.o.vv_ajaxloader.html(js_html.ajaxloader());
			},
			afterSend : function() {
				js_vote.o.vv_ajaxloader.html(null);
			},
			error : function() {
				this.afterSend();
			},
			success: function(data){
				if(data.error && data.error.length) {
					js_vote.showError(data.error);
				}
				else {
					if(typeof data.html != 'undefined' && data.html) {
						js_vote.insert(data.html);
					}
					if(typeof data.votes != 'undefined') {
						var votes, rel, x;
						votes = data.votes;
						for(x in data.votes) {
							rel = $.sprintf('vc-%s-%u-%s', js_vote.item_type, js_vote.item_id, x);
							__$($.sprintf('span[rel=%s]', rel)).html(votes[x]);
						}
					}
				}
				this.afterSend();
			}
		};
		if(js_vote.item_id>0 && js_vote.item_type!='') {
			$.ajax($.extend(true, defaults, options));
		}
		return false;
	},
	insert : function(html) {
		if(js_vote.action=='add') {
			js_vote.o.va_context.remove();
		}
		js_util.scrollto(js_vote.o.vv_context.html(html).show(), function(){
			js_vote.clearError();
			js_vote.reinit();
		});
		js_vote.loaded = 1;
	},
	toggle : function() {
		if(js_vote.loaded) {
			js_vote.o.vv_context.toggle();
		}
		else {
			js_vote.get();
		}
	},
	showError : function(error) {
		var html = '';
		var replace_func = function(string){return $.sprintf('<li class="red"> - %s</li>', string);}
		switch(typeof error) {
			case 'string':
			case 'number':
				html = replace_func(error);
				break;
			case 'object':
				$.each(error, function(index, e){
					html += replace_func(e);
				});
				break;
		}
		js_vote.clearError();
		if(html) {
			$('ul:first', js_vote.o.ve_context.show()).html(html);
		}
	},
	clearError : function() {
		$('ul', js_vote.o.ve_context.hide()).html(null);
	},
	isAuth : function() {
		return js_vote.auth;
	}
};

/**
 * Image crop tool
 */
var js_crop = {

	/**
	 * init times
	 */
	init_count : 0,

	/**
	 * Jcrop instance
	 */
	api : null,

	/**
	 * image target
	 */
	target : null,

	/**
	 *
	 */
	coords : {},

	/**
	 *
	 */
	width : 0,

	/**
	 *
	 */
	height : 0,

	/**
	 * Options passed while first init
	 */
	options : {},

	/**
	 *
	 */
	check : function() {
		return this.api!==null;
	},

	/**
	 * @param string|jquery_object target (e.g. "target", $('#target'))
	 * @param object options (e.g. {})
	 */
	init : function(target, options) {
		var result = false;
		if(!this.check()) {
			target = target || this.getTarget();
			options = options || this.getOptions();
			var defaults = {
				// standard handlers
				onSelect : function(c){
					if(c) js_crop.coords=c;
					if(typeof js_crop.options.afterSelect=='function') {
						js_crop.options.afterSelect(this);
					}
				},
				onChange : function(c){
					if(c) js_crop.coords=c;
					if(typeof js_crop.options.afterChange=='function') {
						js_crop.options.afterChange(this);
					}
				},
				onRelease : function() {
					js_crop.destroy();
					js_crop.setCoords(0,0,0,0);
					if(typeof js_crop.options.afterRelease=='function') {
						js_crop.options.afterRelease(this);
					}
				},
				// custom handlers
				onInit : function(){},
				onDestroy : function(){},
				afterSelect : function(){},
				afterChange : function(){},
				afterRelease : function(){},
				//aspectRatio: 16/9,
				//minSize : [100, 100],
				//maxSize : [200, 200],
				bgColor : 'black',
				bgOpacity : 0.4
			}
			this.setOptions($.extend(defaults, options));
			this.setTarget(target);
			if(typeof $.Jcrop!='undefined') {
				this.api = $.Jcrop(this.getTarget(), this.getOptions());
			}
			result = this.check();
			if(result) {
				this.init_cnt++;
				this.getDimension(); // init max width && height
				if(this.isCoordsInited()) {
					this.animateSelect(this.coords.x, this.coords.y, this.coords.x2, this.coords.y2);
				}
				this.options.onInit(this);
			}
		}
		return result;
	},

	/**
	 *
	 */
	destroy : function() {
		if(this.check()) {
			this.api.destroy();
			this.api = null;
			this.options.onDestroy(this)
		}
	},

	/**
	 * init/destroy
	 */
	toggle : function(target, options) {
		return this.check() ? this.destroy() : this.init(target, options);
	},

	/**
	 *
	 */
	disable : function() {
		return this.check() ? this.api.disable() : false;
	},

	/**
	 *
	 */
	enable : function() {
		return this.check() ? this.api.enable() : false;
	},

	/**
	 *
	 */
	setTarget : function(target) {
		if(!target) {
			target = 'preview';
		}
		if(typeof target=='string') {
			target = __$(target);
		}
		if(typeof target=='object') {
			this.target = target;
		}
		return this;
	},

	/**
	 *
	 */
	getTarget : function() {
		return this.target;
	},

	/**
	 *
	 */
	setOptions : function(options) {
		if(typeof options=='object') {
			this.options = options;
		}
		return this;
	},

	/**
	 *
	 */
	getOptions : function() {
		return this.options;
	},

	/**
	 *
	 */
	setCoords : function(x,y,x2,y2) {
		x=x||0;y=y||0;x2=x2||0;y2=y2||0;
		this.coords = {'x':x,'y':y,'x2':x2,'y2':y2,'w':x2-x,'h':y2-y};
	},

	/**
	 *
	 */
	getCoords : function() {
		return this.coords;
	},

	/**
	 * @return [x,y,x2,y2]
	 */
	getCustomFormatCoords : function() {
		return this.coords ? [this.coords.x, this.coords.y, this.coords.x2, this.coords.y2] : [0,0,0,0];
	},

	/**
	 * @return "[x,y,x2,y2]"
	 */
	getCustomStringCoords : function() {
		return this.coords ? $.sprintf('[%d,%d,%d,%d]', this.coords.x, this.coords.y, this.coords.x2, this.coords.y2) : '[0,0,0,0]';
	},

	/**
	 *
	 */
	checkCoords : function(x,y,x2,y2) {
		_e(x);
		_e(y);
		_e(x2);
		_e(y2);
		_e(this.getWidth());
		_e(this.getHeight());
		return this.validateCoords(x, y, x2, y2, this.getWidth(), this.getHeight());
	},

	/**
	 *
	 */
	validateCoords : function(x,y,x2,y2,w,h) {
		var bool = true;
		bool = bool && x>=0 && x<w;
		bool = bool && y>=0 && y<h;
		bool = bool && x2>0 && x2<=w;
		bool = bool && y2>0 && y2<=h;
		return bool;
	},

	/**
	 *
	 */
	isCoordsInited : function() {
		var bool = true;
		bool = bool && typeof(this.coords.x2)!='undefined' && this.coords.x2>0;
		bool = bool && typeof(this.coords.y2)!='undefined' && this.coords.y2>0;
		return bool;
	},

	/**
	 *
	 */
	select : function(x,y,x2,y2) {
		this.selectImpl('setSelect', x,y,x2,y2);
	},

	/**
	 *
	 */
	animateSelect : function(x,y,x2,y2) {
		this.selectImpl('animateTo', x,y,x2,y2);
	},

	/**
	 * @private
	 */
	selectImpl : function(impl, x,y,x2,y2) {
		if(this.checkCoords(x,y,x2,y2)) {
			if(this.check() || this.init()) {
				this.setCoords(x,y,x2,y2);
				this.api[impl]([x,y,x2,y2]);
				this.options.onSelect(this.getCoords());
			}
		}
	},

	/**
	 *
	 */
	getWidth : function() {
		var dimension = this.getDimension();
		return dimension[0];
	},

	/**
	 *
	 */
	getHeight : function() {
		var dimension = this.getDimension();
		return dimension[1];
	},

	/**
	 *
	 */
	getDimension : function() {
		var dimension = [0,0];
		if(this.width>0 && this.height>0) {
			dimension = [this.width, this.height];
		}
		else if(this.check()) {
			dimension = this.api.getBounds();
			this.width = dimension[0];
			this.height = dimension[1];
		}
		else {
			this.width = this.target.width();
			this.height = this.target.height();
			dimension = [this.width, this.height];
		}
		return dimension;
	},

	/**
	 *
	 */
	highlight : function(hash, pattern) {
		var hash = hash || window.location.hash;
		if (typeof hash == 'string' && hash!=='') {
			pattern = pattern || /c\[(\d+),(\d+),(\d+),(\d+)\]/;
			hash = hash.substring(hash.lastIndexOf('#')+1);
			if(hash.search(pattern)!= -1) {
				var x=RegExp.$1, y=RegExp.$2, x2=RegExp.$3, y2=RegExp.$4;
				if(js_crop.checkCoords(x,y,x2,y2)) {
					callback = function() {
						window.location.hash = '#'+hash;
						js_crop.animateSelect(x,y,x2,y2);
					};
					js_util.scrollto(js_crop.getTarget(), callback);
				}
			}
		}
		return false;
	},

	/**
	 *
	 */
	dummy : function() {}
};