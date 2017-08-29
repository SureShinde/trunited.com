$(function() {
	var COOKIE_NAME = 'landing-page-cookie';
	$go = $.cookie(COOKIE_NAME);
	if ($go == null) {
		$.cookie(COOKIE_NAME, 'test', { path: '/', expires: 7 });
		window.location = "/landingPage.html"
	}
	else {
	}
});//This script must be within the main page so user will be redirected to the landing page if he hasnt already gone there