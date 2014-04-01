!function($) {
    var cover;
    var holder;
    var container;
    var both;
    var body = $('body');
    $.popup = function(r) {
        if (!cover) {
            cover = $('<div id="popup-cover" style="display:none;"></div>');
            holder = $('<div id="popup-holder"></div>');
            container = $('<div id="popup-container" style="display:none;"></div>');
            container.append(holder);
            both = $('#popup-cover,#popup-container');
            body.append(cover)
                .append(holder);
        }
        var content;
        if (typeof r === 'object' && typeof r.content !== 'undefined') {
            content = r.content;
        }
        else {
            content = r;
        }
        holder.html(content);
        cover.css({
            height: $(document).height() + 'px'
        });
        both.fadeIn('fast');
    };
    $.popup.close = function() {
        both.fadeOut('fast', function() {
            holder.html('');
        });
        return false;
    };
    $('#popup-cover,#popup-container .closer').live('click', $.popup.close);
}(jQuery);