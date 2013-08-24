;!function($){
	var _callback = function(response) {};
	$.ajaxDispatcher('default', function(event) {
		event.caller.success = event.callback || _callback;
		$.ajax(event.caller);
	});
	$.ajaxDispatcher('confirm', function(event) {
		var message = event.caller.dataset.message||'Are you sure you want to do this action?';
		if(window.confirm(message)) {
			event.type='ajax:dispatcher:default';
			$('body').trigger(event);
		}
	});
	$.ajaxDispatcher('popup', function(event) {
		event.type = 'ajax:dispatcher:default';
		event.callback = $.popup;
		$('body').trigger(event);
	});
}(jQuery);