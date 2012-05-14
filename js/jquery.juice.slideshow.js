(function($) {
	$.fn.slideshow = function(callerSettings) {
		var settings = $.extend(true, {}, $.fn.slideshow.settings, callerSettings);
		return this.each(function() {
			var n = $(this), current = animating = false, list, slides, width, height, wrap, caption;
			var setup = function() {
				n.addClass('juice-slideshow')
					.addClass('juice-slideshow-active')
					.css({
						overflow: 'hidden',
						position: 'relative'
					});
				slides = $(settings.slides, n)
					.wrap($('<div />')
						.addClass('juice-slideshow-slide')
						.css({'position': 'relative', cssFloat: 'left', overflow: 'hidden', width: n.width() }
					));
				width = n.width();
				height = slides.height();
				n.wrapInner($('<div />')
						.css({
							width: width * slides.length,
							height: height
						})
					.addClass('juice-slideshow-slides'))
					.wrapInner($('<div />')
						.css({
							overflow: 'hidden',
							position: 'relative',
							width: width
						})
						.addClass('juice-slideshow-viewport'));
				wrap = $('.juice-slideshow-viewport', n);
				if (slides.length > 1 && (settings.paging || settings.prevNext)) {
					list = $('<ul />')
						.addClass('juice-slideshow-menu')
						.appendTo(n);
				}
				slides.each(function(i, n) {
					if (settings.paging) {
						$('<li />')
							.text(i+1)
							.appendTo(list)
							.wrapInner('<a />')
							.mousedown(function(e) {
								e.preventDefault();
								viewSlide(i);
							});
					}
					if (settings.caption) {
						if ($(this).attr('alt')) {
							var text = '';
							if ($(this).attr('title')) {
								text = '<div class="juice-slideshow-title">'+$(this).attr('title')+'</div>';
							}
							text += $(this).attr('alt');
							if (text) {
								$(this).data('caption', text).removeAttr('title').removeAttr('alt');
								var c = $('<div />')
									.addClass('juice-slideshow-caption')
									.html(text)
									.wrapInner($('<div />').addClass('juice-slideshow-caption-inner'))
									.css('opacity', settings.captionOpacity)
									.hide()
									.appendTo($(this).parent());
								c.data('height', c.height());
							}
						}
					}
				})
				//.css('overflow', 'auto')
				.width(n.innerWidth())
				.height(n.innerHeight());
				if (list && settings.prevNext == true) {
					prev(); next();
				}
				if (settings.hide) {
					n.hover(function() {
						if (caption) {
							n.addClass('juice-slideshow-active');
							caption.stop().animate({ height: caption.data('height') }, { queue: false });
						}
					}, function() {
						if (caption) {
							n.removeClass('juice-slideshow-active');
							caption.stop().animate({ height: 0 }, { queue: false });
						}
					});
					if (settings.hideOnLoad) {
						setSlide(0);
						caption.height(0);
					} else {
						viewSlide(0);
					}
				} else {
					viewSlide(0);
				}
			};
			var prev = function() {
				$('<li />')
					.html('&laquo;')
					.addClass('juice-slideshow-previous')
					.wrapInner('<a />')
					.prependTo(list)
					.mousedown(function(e) {
						e.preventDefault();
						viewSlide(current > 0 ? current-1 : slides.length-1);
					});
			};
			var next = function() {
				$('<li />')
					.html('&raquo;')
					.addClass('juice-slideshow-next')
					.wrapInner('<a />')
					.appendTo(list)
					.mousedown(function(e) {
						e.preventDefault();
						viewSlide(current == slides.length-1 ? 0 : current+1);
					});
			};
			var viewSlide = function(i) {
				var slide = slides.eq(i);
				if (current !== i && animating == false) {
					//slides.css('overflow', 'hidden');
					animating = true;
					var offset = (i>current?'+':'-')+'='+(width*Math.abs(current - i))+'px';
					if (settings.caption) {
						var c = $('.juice-slideshow-caption', n);
						c.slideUp('slow');
						wrap.animate({scrollLeft: offset}, 'medium', 'swing', function() {
							if (slide.data('caption')) {
								$('.juice-slideshow-caption', slide.parent()).slideDown('fast', function() {
									if (!n.hasClass('juice-slideshow-active')) {
										c.slideUp();
									}
									setSlide(i);
								});
							} else {
								setSlide(i);
							}
						});
					} else {
						wrap.animate({scrollLeft: offset}, 'medium', 'swing', function() {
							setSlide(i);
						});
					}
				}
			};
			var setSlide = function(i) {
				caption = $('.juice-slideshow-caption', n).eq(i);
				if (settings.paging) {
					$('li', list)
						.removeClass('juice-slideshow-current')
						.eq(i+(settings.prevNext ? 1 : 0))
							.addClass('juice-slideshow-current');
				}
				current = i;
				animating = false;
				//slides.css('overflow', 'auto');
			};
			setup();
		});
	};
	$.fn.slideshow.settings = {
		slides: 'img',
		prevNext: true,
		paging: true,
		hide: false,
		hideOnLoad: false,
		caption: true,
		captionOpacity: 0.7,
		prevText: '&laquo;',
		nextText: '&raquo;'
	};
})(jQuery);