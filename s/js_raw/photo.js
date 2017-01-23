function initJcrop() {
	if(window.js_crop) {
		var cropCommentTool = __$('crop2comment');
		var isCropCommentTool = cropCommentTool.length;
		var options = {
			onInit : function() {
				__$('tool_crop').addClass('shadow');
			},
			onDestroy : function() {
				__$('tool_crop').removeClass('shadow');
				if(isCropCommentTool) {
					cropCommentTool.hide();
				}
			},
			afterSelect : function() {
				if(isCropCommentTool) {
					cropCommentTool.show();
				}
			},
			afterRelease : function() {
				if(isCropCommentTool) {
					cropCommentTool.hide();
				}
			}
		};
		js_crop.setTarget('preview');
		js_crop.setOptions(options);
		js_crop.highlight();
		__$('tool_crop').unbind().click(function(){
			window.js_crop.toggle();
		});
		if(isCropCommentTool) {
			cropCommentTool.unbind().click(function(){
				if(typeof js_com=='object') {
					js_com.addText(js_crop.getCustomStringCoords()+' ');
				}
				js_crop.destroy();
			});
		}
	}
}

function initGrayscale() {
	__$('tool_grayscale').unbind().bind('click', function(){
		if(typeof this.image=='undefined') {
			var preview = __$('preview');
			this.image = {
				'id':preview.attr('rel'),
				'self':preview,
				'orig_src':preview.attr('src'),
				'grayscale_src':null
			};
		}
		if($(this).hasClass('shadow')) {
			if($.browser.msie) {
				setPreviewImageCss('filter','progid:DXImageTransform.Microsoft.BasicImage(grayscale=0)');
			}
			else {
				setPreviewImageSrc(this.image.orig_src);
			}
			$(this).removeClass('shadow');
		}
		else {
			if($.browser.msie) {
				setPreviewImageCss('filter','progid:DXImageTransform.Microsoft.BasicImage(grayscale=1)');
				$(this).addClass('shadow');
			}
			else {
				if(!this.image.grayscale_src) {
					var this_image = this.image;
					var self = this;
					$.ajax({
						url : '/json/photo_grayscale/',
						dataType : 'json',
						beforeSend : function() {
							__$('grayscaling').html(js_html.ajaxloader());
						},
						afterSend : function() {
							__$('grayscaling').html(null);
						},
						error : function() {
							this.afterSend();
						},
						success : function(json_response) {
							if(json_response && json_response.image) {
								this_image.grayscale_src = json_response.image;
								setPreviewImageSrc(this_image.grayscale_src);
								this.afterSend();
							}
						},
						data : {
							id : this_image.id,
							rand : Math.random(),
							dbg : 0
						}
					});
				}
				else {
					setPreviewImageSrc(this.image.grayscale_src);
				}
				$(this).addClass('shadow');
			}
		}
	});
}

function setPreviewImageSrc(src) {
	var images = $('img', __$('preview_placeholder'));
	images.each(function(i,e){
		$(e).attr('src', src);
	});
	return false;
}
function setPreviewImageCss(name,value) {
	var images = $('img', __$('preview_placeholder'));
	images.each(function(i,e){
		$(e).css(name,value);
	});
	return false;
}