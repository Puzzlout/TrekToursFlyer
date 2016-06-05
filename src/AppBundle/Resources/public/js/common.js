$(document).ready(function(){
	//add scroll effect to the menu navbar
	var navbarFixedTop = $("#header-menu");
	var transparency = 0;
	$(window).scroll(function() {
		if ($(document).scrollTop() > 70) {
			transparency = $(document).scrollTop()/$(window).height();
			navbarFixedTop.css('background-color', 'rgba(255,255,255,'+transparency+')');
		} else {
			navbarFixedTop.css('background-color', 'transparent');
		}
	});
});