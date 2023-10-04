/*!
* App.js
* Version 1.0 - built Fri, Feb 1st 2019, 05:05 pm
* https://simcycreative.com
* Simcy Creative - <hello@simcycreative.com>
* Private License
*/

/*
 * Start jQuery
 */
$(document).ready(function() {

    /*
     * Tooltip
     */
    $('[data-toggle="tooltip"]').tooltip();


    /*
     * humbager
     */
    $(".humbager, .close-menu").click(function(event) {
        event.preventDefault();
        var menu = $("header nav");
        if (menu.hasClass("open")) {
            menu.removeClass("open");
        } else {
            menu.addClass("open");
        }
    });

});



/*
 * auth page switch pages
 */
$(".auth-switch").click(function(event){
	event.preventDefault();
	$(".register, .forgot, .reset, .login").hide();
	$($(this).attr("show")).show();
});

/**
 * Active Links
 */
$('nav.navigation a[href="'+window.location.pathname+'"]').parent().addClass('active');

/**
 * Disable values
 */
$('option[value="0"]').attr('disabled',true);


/**
 * Adjust budget
 */
$("input[name=monthly_spending]").on('keyup change',function(){
    var value = $(this).val();
    $("input[name=annual_spending]").val((value*12).toFixed(2));
    $(".total-budget").text(value);
    $('.budget-slider').slider({'max':value});
});

$("input[name=annual_spending]").on('keyup change',function(){
    var monthly_spending = parseFloat($(this).val()/12);
    $("input[name=monthly_spending]").val(monthly_spending.toFixed(2));
    $(".total-budget").text(monthly_spending);
    $('.budget-slider').slider({'max':monthly_spending});
}).change();


$("input[type=number]").change(function(){
    var value = parseFloat($(this).val());
    log(value);
    $(this).val(value.toFixed(2));
});
