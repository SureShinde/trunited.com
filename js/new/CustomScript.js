$j = jQuery.noConflict();
$j(document).ready(function() {
	// For Menu Button -----
	$j('.navbar-toggle').click(function(e) {
		$j('body').toggleClass('mobo_menu');
	});
});

// For Sticky footer -----
function sticky_ftr(){
	var _footerH = $j('footer').height();
	$j('.wrapper').css({'margin-bottom': -_footerH });
	$j('.push').height(_footerH);
}
sticky_ftr();
