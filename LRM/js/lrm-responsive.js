$(window).resize(function () {
    var responsive_viewport = $(window).width();
    
    //  console.log(responsive_viewport);
    if (responsive_viewport < 570) {

        // Change the order of the main container divs
        $("#main-image").insertBefore("#text_intro");
        
    } else {
        
        // Revert order of container divs
        $("#text_intro").insertBefore("#main_image");
    }

});

$(document).ready(
    
    function () {
        $(window).trigger('resize');
    });