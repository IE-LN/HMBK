(function($) {
	var _defs = {
		tar:'ztarget',
		full:'full-url',
		xdim:'xdim',
		ydim:'ydim'
	};
	$.BM = $.BM || {};
	$.BM.zoomPhoto = function(e,o) {
		this.e = {};
		this.e.btn = e;
		e.data('bm-zoom-photo', this);
		this.o = $.extend({}, _defs, {author:'loushou', version:'1.0-lou'}, o);
		this.init();
	}
	$.BM.zoomPhoto.prototype = {
		init: function() {
			this.zooming = false;
			this.e.where = $($(this.e.btn).attr(this.o.tar));
			this.fullurl = $(this.e.btn).attr(this.o.full);
			this.fullx = $(this.e.btn).attr(this.o.xdim);
			this.fully = $(this.e.btn).attr(this.o.ydim);
			this._setup_events();
		},
		_setup_events: function() {
			var self = this;
			this.e.btn.unbind('click.bm-photo-zoom').bind('click.bm-photo-zoom', function(e) { self._click.apply(self, [e]); });
		},
		_click: function(e) {
			e.preventDefault();
			if (this.zooming) this._stop_zooming();
			else this._start_zooming();
		},
		_start_zooming: function() {
			this.zooming = true;
			this.e.btn.siblings('.helper').show();
			this.e.btn.closest('.zoom-in-feature').siblings('.zoom-in-feature').find('.helper').show();
			this._create_zoom_cover();
		},
		_stop_zooming: function() {
			this.zooming = false;
			this.e.btn.siblings('.helper').hide();
			this.e.btn.closest('.zoom-in-feature').siblings('.zoom-in-feature').find('.helper').hide();
			this._destroy_zoom_cover();
		},
		_destroy_zoom_cover: function() {
			this.e.where.removeClass('zoom-helper').unbind('mouseenter.bm-photo-zoom');
			this.e.zc.unbind('mouseleave.bm-photo-zoom');
			this.e.zc.unbind('mousemove.bm-photo-zoom');
			this.e.zc.remove();
			this.e.zc = undefined;
		},
		_create_zoom_cover: function() {
			if (this.e.zc) return;
			var self = this;
			this.e.zc = $('<div class="bm-photo-zoom-cover zoom-helper"></div>').appendTo('body').hide();
			this.e.where.addClass('zoom-helper');
			var p = this.e.where.offset();
			var d = {w:this.e.where.outerWidth(), h:this.e.where.outerHeight()};
			this.e.zc.css({
				position:'absolute',
				'top':p['top'],
				'left':p['left'],
				background:'transparent url("'+this.fullurl+'") no-repeat left top',
				width:d.w,
				height:d.h,
				zIndex:9
			});
			this.r = {x:((this.fullx-d.w)/d.w), y:((this.fully-d.h)/d.h)};
			this.zcoff = this.e.where.offset();
			this.e.where.unbind('mouseenter.bm-photo-zoom').bind('mouseenter.bm-photo-zoom', function(e) { self._begin_zoom_action.apply(self, [e]) });
			this.e.zc.unbind('mouseout.bm-photo-zoom').bind('mouseout.bm-photo-zoom', function(e) { self._end_zoom_action.apply(self, [e]) });
			this.e.zc.unbind('mousemove.bm-photo-zoom').bind('mousemove.bm-photo-zoom', function(e) { self._move_zoom_action.apply(self, [e]) });
		},
		_move_zoom_action:function(e) {
			var off = {x:e.pageX - this.zcoff.left, y:e.pageY - this.zcoff.top};
			off.x = off.x * this.r.x;
			off.y = off.y * this.r.y;
			this.e.zc.css({backgroundPosition:'-'+off.x+'px -'+off.y+'px'});
		},
		_end_zoom_action:function(e) {
			this.e.zc.hide();
		},
		_begin_zoom_action: function(e) {
			this.e.zc.show();
		}
	}
	$.fn.bmZoomPhoto = function(o){ return this.each(function(){(new $.BM.zoomPhoto($(this), o))}) }
})(jQuery);

(function($){
	$(function() {
		$('.activate-zoom').bmZoomPhoto();
	});
})(jQuery);
