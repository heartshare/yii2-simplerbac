(function ($) {
	var settings = {};
	var actions = {
		init: function (options) {
			settings = $.extend({}, options);

            return this;
		},
		send: function (loader_id) {
			if (loader_id) {
				$(loader_id).show();
			}

			var ok_target = $(this).data('oktarget');
			var err_target = $(this).data('errtarget');
			var trigger = $(this).data('trigger');
			var scroll = $(this).data('scroll');
			$.post($(this).data('action'), $(this).data(), function (response) {
				if (response.state == 'success' || response.state == 'ok' || response.state == 1) {
					$(err_target).hide();
					(ok_target && response.result) ? $(ok_target).html(response.result) : '';
					if (trigger) {
						$(document).trigger(trigger);
					}
					if (scroll && ok_target) {
						actions.scroll(ok_target);
					}
				} else if (err_target) {
					$(err_target).show();
					$(err_target).html(response.error);
					if (scroll) {
						actions.scroll(err_target);
					}
				}

			}).error(function (response) {
				if(typeof(response) == 'object' && response.responseJSON){
					err='Server Error '+response.responseJSON.code+ response.responseJSON.message;
				}else{
					err=response;
				}
				if (err_target) {
					$(err_target).show();
					$(err_target).html(err);
					if (scroll) {
						actions.scroll(err_target);
					}
				} else {
					alert(err);
				}
			}).complete(function () {
				if (loader_id) {
					$(loader_id).hide();
				}
			});
			return false;
		},
		sendform: function (loader_id) {
			if (loader_id) {
				$(loader_id).show();
			}
			var ok_target = $(this).data('oktarget');
			var err_target = $(this).data('errtarget');
			var trigger = $(this).data('trigger');
			var id=$(this).attr('id');
			if ($(this).find('.has-error').length) {
				return false;
			}
			console.log('run sendform for '+$(this).attr('id') +'/'+ $(this).attr('class'));

			$.ajax({
				url: $(this).attr('action'),
				type: "POST",
				dataType: "json",
				data: $(this).serialize(),
				success: function (response) {
					if (response.state == 'success' || response.state == 'ok' || response.state == 1) {
						$(err_target).hide();
						(ok_target && response.result) ? $(ok_target).html(response.result) : '';
						if (trigger) {
							console.log('fire trigger '+trigger);
							$(document).trigger(trigger);
						}
						var f=document.getElementById(id);
						if(f){f.reset()};
					} else if (err_target) {
						$(err_target).show();
						$(err_target).html(response.error);
						if (scroll) {
							actions.scroll(err_target);
						}
					}
				},
				error: function (response) {
					if(typeof(response) == 'object' && response.responseJSON){
						err='Server Error '+response.responseJSON.code+ response.responseJSON.message;
					}else{
						err=response;
					}
					if (err_target) {
						$(err_target).show();
						$(err_target).html(err);
						if (scroll) {
							actions.scroll(err_target);
						}
					} else {
						alert(err);
					}
				},
				complete: function () {
					if (loader_id) {
						$(loader_id).hide();
					}
				}
			});

			return false;

		},
		scroll: function (selector) {
			var scrollTop = $(selector).offset().top;
			$(document).scrollTop(scrollTop);
		}

	};
	$.fn.rbacManage = function (action) {
		if (actions[action]) {
			return actions[action].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof action === 'object' || !action) {
			return actions.init.apply(this, arguments);
		} else {
			$.error('Ашибка метода ' + action);
		}
	};
})(jQuery);