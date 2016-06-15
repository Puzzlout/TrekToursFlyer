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

$(document).ready(function () {
	showCookieConsent();

	$(".cookie-consent-button").click(function(){
		var cc_val;
		if($(this).hasClass("cc-button-positive")) {
			cc_val = "1";
		} else {
			cc_val = "0";
		}
		createCookie("usr_cc", cc_val, 4745);
		$(".cookie-consent").slideUp("fast");
	})
});

function showCookieConsent() {
	var usr_cc = readCookie("usr_cc");
	if(!usr_cc) {
		$(".cookie-consent").slideDown("slow");
	}
}

