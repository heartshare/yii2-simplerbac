(function ($) {
	var actions = {
		init: function (selector, event, func, funcargs,prevent ) {
           if(!prevent){
	           prevent=true;
           }
			if(!funcargs){
				funcargs=false;
			}

			if(!actions[func]){
				console.error('Call unsupported method '+func);
			}else{
				if(typeof selector =='string'){
					selector=[selector];
				}
				selector.forEach(function(sel){
					$(document).on(event, sel, function(e){
						console.log('run binding '+sel+ 'on ' +event+' with funcargs as '+(typeof funcargs));
						if(prevent){
							e.preventDefault();
						}
						(typeof funcargs !='object')?actions[func].apply(this, [funcargs]):actions[func].apply(this, funcargs);
						return false;
					});
				});

            }
            return false;
		},
		test: function (argone, argtwo){
			console.log('Calling test method with param '+argone + '&& '+ argtwo);
			console.log('Calling test method for element '+$(this).attr('id')+' '+$(this).attr('class') +' '+$(this).prop('tagName'));

		},
		send: function (loader_id, method) {
			console.log('Calling send method with param '+loader_id + '&& '+ method);
			if (loader_id) {
				$(loader_id).show();
			}
            if(!method){
	            method='post';
            }
			var ok_target = $(this).data('oktarget');
			var err_target = $(this).data('errtarget');
			var trigger = $(this).data('trigger');
			var errtrigger = $(this).data('errtrigger');
			var scroll = $(this).data('scroll');
			var is_form=($(this).prop('tagName')=='FORM');
			if (is_form && $(this).find('.has-error').length) {
				return false;
			}
			$.ajax({
				url: is_form?$(this).attr('action'):$(this).data('action'),
				type: method,
				dataType: "json",
				data: is_form?$(this).serialize():$(this).data(),
				async:true
			}).success(function(response){
				if (response.state == 'success' || response.state == 'ok' || response.state == 1) {
					$(err_target).hide();
					(ok_target && response.result) ? $(ok_target).html(response.result) : '';
					if (trigger) {
						console.log('fire trigger '+trigger);
						$(document).trigger(trigger);
					}
					//if(is_form){var f=document.getElementById(id);if(f){f.reset()};}
					if (scroll && ok_target) {
						actions.scroll(ok_target);
					}
				} else{
					if (errtrigger) {
						console.log('fire errtrigger '+errtrigger);
						$(document).trigger(errtrigger,[response.error]);
					}
					if (err_target) {
						$(err_target).show();
						$(err_target).html(response.error);
						if (scroll) {
							actions.scroll(err_target);
						}
					}
				}
			}).error(function(response){
				if(typeof(response) == 'object' && response.responseJSON){
					err='Server Error '+response.responseJSON.code+ response.responseJSON.message;
				}else{
					err=response;
				}
				if (errtrigger) {
					$(document).trigger(errtrigger,[err]);
				}
				if (err_target) {
					$(err_target).show();
					$(err_target).html(err);
					if (scroll) {
						actions.scroll(err_target);
					}
				}
			}).complete(function(response){
				if (loader_id) {
					$(loader_id).hide();
				}
			});
			return false;
		},
		scroll: function (selector) {
			var scrollTop = $(selector).offset().top;
			$(document).scrollTop(scrollTop);
		}

	};
	$.ajaxHelper = function () {
		return actions.init.apply(this, arguments);
	};
	$.fn.ajaxHelper = function (action) {
		if (actions[action]) {
			return actions[action].apply(this, Array.prototype.slice.call(arguments, 1));
		} else {
			$.error('Ашибка метода ' + action);
		}
	};
})(jQuery);