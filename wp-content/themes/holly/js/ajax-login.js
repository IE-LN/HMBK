
// regardless of the existence of the modal, we want a visual indicator that the info from facebook has been recieved
// and that we are now just trying to load the account to which it is linked. the reason for this is so that the user
// does not get impatient with no visual confirmation of a login, and so that the user does not take for granted that 
// is already done, if it is not. ths block will handle that.
(function($) {
	$.BM = $.BM || {};
	$.BM.fbCommMsg = function(msg) {
		var comm = $('#facebook-communication-div').css({display:'none'});
		if ($(comm).length == 0) {
			var comm = $('<div id="facebook-communication-div"></div>').css({display:'none',position:'absolute',zIndex:1500}).appendTo('body');
			$('<div id="facebook-communication-backdrop"></div>').css({position:'absolute'}).appendTo(comm);
			$('<div id="facebook-communication-message"></div>').css({position:'absolute'}).appendTo(comm);
			$(window).unbind('closemsg.fbcomm').bind('closemsg.fbcomm', function() { comm.hide(); });
			$(window).unbind('openmsg.fbcomm').bind('openmsg.fbcomm', function() {
				comm.show();
				$(window).trigger('scroll');
			});
			$(window).unbind('changemsg.fbcomm').bind('changemsg.fbcomm', function(e,msg) {
				var msgdiv = $('#facebook-communication-message', comm);
				$('<div>'+msg+'</div>').appendTo(msgdiv.empty());
			});
			$(window).unbind('resize.fbauthmsg').bind('resize.fbauthmsg', function() {
				var hw = {height:parseInt($(document).height())+'px', width:parseInt($(document).width())+'px','top':0,'left':0};
				comm.css(hw);
				$('#facebook-communication-backdrop', comm).css(hw);
				$(this).trigger('scroll');
			});
			$(window).unbind('scroll.fbauthmsg').bind('scroll.fbauthmsg', function() {
				var msgdiv = $('#facebook-communication-message', comm);
				var newtop = $(window).scrollTop() + (($(window).height() - msgdiv.outerHeight())/2);
				var newleft = $(window).scrollLeft() + (($(window).width() - msgdiv.outerWidth())/2);
				msgdiv.css({'top':newtop,'left':newleft});
			});
			$(window).trigger('resize').trigger('scroll');
		}
		$(window).trigger('changemsg.fbcomm', [msg]);
		$(window).trigger('openmsg.fbcomm');
	}
	$(function() {
		$(window).bind('startauth.bmfbauth', function(ev, ele) {
			ele.each(function() {
				var extra = ($(this).attr('fb-extra')||'').split('|');
				var o = {};
				for (i in extra) {
					var attr = extra[i];
					var attr = attr.split(':');
					var name = attr.shift().replace(/[^\w]+/g, '-');
					if (attr.length) var val = attr.join(':');
					else var val = true;
					o[name] = val;
				}
				o = $.extend({rplc:false, w:0, h:0}, o);
				if (o.rplc) {
					$(this).empty();
					$('<div class="facebook-loading-account"><span>Loading Your Account</span></div>').css({color:'#b2b2b2', 'float':'right'}).appendTo(this);
				} else {
					var off = $(this).position();
					$('<div> </div>').css({
						position:'absolute',
						backgroundColor:'white',
						opacity:.5,
						'top':off.top,
						'left':off.left,
						height:$(this).innerHeight()+parseInt(o.h),
						width:$(this).innerWidth()+parseInt(o.w)
					}).appendTo(this);
				}
			});
		});
		// $.BM.fbCommMsg('<div class="loading-facebook-account">Loading your account...<div>'); });
		$(window).bind('successafterreplace.bmfbauth', function() { $(window).trigger('closemsg.fbcomm'); });
		$(window).bind('unsuccessafterreplace.bmfbauth', function() { window.location.reload(true); });
	});
})(jQuery);
