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

// Back to Top Navigation
	
	// Show or hide the sticky footer button
			$(window).scroll(function() {
				if ($(this).scrollTop() > 200) {
					$('.go_top').fadeIn(200);
				} else {
					$('.go_top').fadeOut(200);
				}
			});
	
	// Animate the scroll to top
			$('.go_top').click(function(event) {
				event.preventDefault();
				
				$('html, body').animate({scrollTop: 0}, 300);
			})
