;
!function ($) {
    $.ajaxSetup({
        url: '/rest.php',
        dataFilter: function (data, dataType) {
            switch (dataType) {
                case 'json':
                    data = data.replace('while(1);', '');
                    break;
            }
            return data;
        },
        error: function (e, t, s, c) {
            var m = $.parseJSON(t.responseText);
            $.popup({
                content: m.response || 'There was an ajax problem.'
            });
        }
    });
    $.ajaxPrefilter(function (options, originalOptions, jqXHR) {
        if (!$.isPlainObject(originalOptions.data)) {
            originalOptions.data = $.deParam(originalOptions.data);
        }
        options.data = $.param($.extend(originalOptions.data, {
            _ajax: 1,
            xsrf_id: $.user.xsrf_id
        }));
    });
    var loading = false;
    $(document).ajaxStart(function () {
        if (!loading) {
            $('body')
                .append(
                    $('<div id="ajax-loading"></div>')
                        .html($('<p>Loading...</p>'))
                );
            loading = $('#ajax-loading');
        }
        loading.center().show();
    });
    $(document).ajaxStop(function () {
        if (loading) {
            loading.hide();
        }
    });
}(jQuery);