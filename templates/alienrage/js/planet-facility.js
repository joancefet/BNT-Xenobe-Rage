$(function(){
    $('.planet-facility').mouseenter(function(){
        $(this).children('.planet-facility-child').fadeIn();
    }).mouseleave(function(){
        $(this).children('.planet-facility-child').fadeOut();
    });
});