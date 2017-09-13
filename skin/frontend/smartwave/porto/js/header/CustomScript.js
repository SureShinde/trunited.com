$(document).ready(function() {
	// For Menu Button -----
	$('.navbar-toggle').click(function(e) {
		$('body').toggleClass('mobo_menu');
	});
});

// For Sticky footer -----
function sticky_ftr(){
	var _footerH = $('footer').height();
	$('.wrapper').css({'margin-bottom': -_footerH });
	$('.push').height(_footerH);
}
sticky_ftr();