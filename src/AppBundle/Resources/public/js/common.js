/*
 $(document).ready(function(){
 //add scroll effect to the menu navbar
 var navbarFixedTop = $("#header-menu");
 navbarFixedTop.css('background-color', 'rgba(0,0,0,0.75)');
 var transparency = 0;
 $(window).scroll(function() {
 if ($(document).scrollTop() > 70) {
 transparency = ($(document).scrollTop()/$(window).height())+0.75;
 navbarFixedTop.css('background-color', 'rgba(0,0,0,'+transparency+')');
 } else {
 navbarFixedTop.css('background-color', 'rgba(0,0,0,0.75)');
 }
 });
 });*/
var menuopen=0;
var what = (/(iPhone|iPod|iPad).*AppleWebKit/i.test(navigator.userAgent)) ? 'touchstart' : 'click';
$(document).ready(function () {
    if(menuopen==0){
        $('#upnavbar').show();
        $( "#navbar" ).show().animate({left: "40px"}, 200, function() {});menuopen=1;
    }
    else if(menuopen==1){
        $( "#navbar" ).animate({left: "300px"}, 200, function() {$('#upnavbar').hide();});menuopen=0;
    }


    showCookieConsent();

    $(".cookie-consent-button").click(function () {
        var ccVal;
        if ($(this).hasClass("cc-button-positive")) {
            ccVal = "1";
        } else {
            ccVal = "0";
        }
        createCookie("usr_cc", ccVal, 4745);
        $(".cookie-consent").slideUp("fast");
    });
});

function showCookieConsent() {
    var usrCc = readCookie("usr_cc");
    if (!usrCc) {
        $(".cookie-consent").slideDown("slow");
    }
}

