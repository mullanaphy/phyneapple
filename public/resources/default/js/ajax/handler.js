;
!function ($) {
    var d = document,
        h = d.documentElement,
        l = null,
        n = function (e, t) {
            while (e && e.nodeName != t)e = e.parentNode;
            return e;
        },
        p = function (c, t) {
            var _ = {
                url: '/rest.php',
                caller: c,
                type: 'get',
                data: {},
                dataset: $(c).data()
            };
            if (!_.dataset.called)_.dataset.called = 0;
            $.data(_.caller, 'called', ++_.dataset.called);
            var url;
            if (t === 'f') {
                if (_.dataset.method)_.type = _.dataset.method;
                else if (c.getAttribute('method'))_.type = c.getAttribute('method');
                else _.type = 'post';
                url = [c.getAttributeNode('action') ? c.getAttributeNode('action').value : '/rest.php', $(c).serialize()];
                if (!_.dataset.method)_.method = 'post';
                if (url[1])url[1]['_called'] = _.dataset.called;
                else url[1] = {_called: _.dataset.called};
                if (_.dataset.hash)url[1]['hash'] = _.dataset.hash;
            }
            else {
                if (_.dataset.method)_.type = _.dataset.method;
                url = c.href.split('?');
                if (url[1])url[1] += '&_called=' + _.dataset.called;
                else url[1] = '_called=' + _.dataset.called;
                if (_.dataset.hash)url[1] += '&hash=' + _.dataset.hash;
            }
            _.url = url[0];
            _.data = url[1];
            if (!_.dataset.type) {
                _.dataset.type = 'default';
            }
            var dispatcher = $.ajaxDispatcher(_.dataset.type);
            var event = $.Event(dispatcher);
            event.caller = _;
            if ($(document).data('events')[dispatcher] && $(document).data('events')[dispatcher].length) {
                $(document).trigger(event);
            }
            else {
                try {
                    if (typeof console !== 'undefined' && console.log) {
                        console.log('Event "' + dispatcher + '" does not exist.');
                    }
                } catch (e) {
                }
                event.type = $.ajaxDispatcher('default');
                $(document).trigger(event);
            }
            return false;
        };
    h.onclick = function (e) {
        e = e || window.event;
        l = e.target || e.srcElement;
        var m = n(l, 'A') || h;
        if (m.nodeName !== 'A' || !m.className) {
            return;
        } else if (m.className.match('ajax'))return p(m, 'a');
    };
    h.onsubmit = function (e) {
        e = e || window.event;
        var m = e.target || e.srcElement;
        if (!m || m.nodeName !== 'FORM') {
            return;
        }
        else if (m.className.match('ajax')) {
            return p(m, 'f');
        }
    };
}(jQuery);