;!function($){$.fn.center=function(){return this.each(function(){var d=$(window),h=d.height(),w=d.width(),r=$(this),m=r.height(),n=r.width(),s=d.scrollTop(),l=d.scrollLeft(),t=(((h-m)/2)+s);var t=t<0?0:t;var l=(((w-n)/2)+l);r.css({position:'absolute',left:l+'px',top:t+'px'});});};}(jQuery);