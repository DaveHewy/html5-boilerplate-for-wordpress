/*
 * jquery.futube.js
 *
 * FuTube Video Player v0.1.0
 * Date: 2009-08-13
 * Requires: jQuery v1.3 or later
 *
 * Copyright 2009 Fubra Limited (http://www.fubra.com/video)
 */

(function($) {
	$.fn.futube = function(callerSettings) {
		var settings = $.extend(true, {}, $.fn.futube.settings, callerSettings);
		return this.each(function() {
			if (!settings.video) { return; }
			var n = $(this);
			var id = n.attr("id") + "_futube";
			$.getScript(settings.path + "js/controls.futube.js", function() {					
				$.getScript(settings.path + "js/swfobject.js", function() {
					swfobject.embedSWF(
						settings.path + settings.skin,
						n.attr("id"),
						settings.width,
						settings.height,
						"9",
						null,
						{
							align: settings.align,
							author: settings.author,
							color: settings.color,
							hd: settings.hd,
							id: id,
							image: settings.image,
							title: settings.title, 
							video: settings.video,
							autoplay: settings.autoplay
						},
						{ 
							allowFullScreen: "true",
							allowScriptAccess: "always",
							menu: "false",
							scale: "noscale",
							wmode: "window",
							bgcolor: settings.bgcolor
						},
						{ name: id, id: id }
					);
				});
			});
		});
	};
	$.fn.futube.settings = {
		align: "left",
		author: "by Fubra",
		bgcolor: "auto",
		color: false,
		height: 300,
		hd: true,
		image: false,
		skin: "skins/default.swf?nocache=" + new Date().getTime(),
		title: "FuTube",
		video: false,
		width: 400,
		path: "http://video.fubra.com/",
		autoplay: false
	};
})(jQuery);