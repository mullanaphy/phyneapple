;
!function($){
    $.popup = function(r) {
        if(!$('#popup-cover').length) {
            $('body')
            .append($('<div id="popup-cover" style="display:none;"></div>'))
            .append(
                $('<div id="popup-container" style="display:none;"></div>')
                .html($('<div id="popup-holder"></div>'))
                )
        }
        if(typeof r === 'object' && typeof r.content !== 'undefined') {
            var content = r.content;
        }
        else {
            var content = r;
        }
        $('#popup-holder').html(content);
        $('#popup-cover').css({
            height:$(document).height()+'px'
        });
        $('#popup-cover,#popup-container').fadeIn('fast');
    };
    $.popup.close = function() {
        $('#popup-cover,#popup-container').fadeOut('fast',function(){
            $('#popup-holder').html('');
        });
        return false;
    }
    $('#popup-cover,#popup-container .closer').live('click',$.popup.close);
}(jQuery);