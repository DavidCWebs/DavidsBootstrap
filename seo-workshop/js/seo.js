//jQuery for page scrolling feature - requires jQuery Easing plugin
$(function() {
    $('.page-scroll a').bind('click', function(event) {
        var $anchor = $(this);
        $('html, body').stop().animate({
            scrollTop: $($anchor.attr('href')).offset().top
        }, 1500, 'easeInOutExpo');
        event.preventDefault();
    });
});

// mobile nav toggle - close the menu when an item is clicked
	$('.nav a').on('click', function(){
	if ($(document).width() <= 767){
	$(".navbar-toggle").click();
	}

});
