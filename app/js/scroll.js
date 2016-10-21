/* Animate scroll on href with class .scroll-this */
$(function() {
	
	var scrollToElementOnLinkClick = function( e ) {
		
		e.preventDefault();

		var link = $(this).attr('href');
		var linkEl = $(link);
		var height = 60;

			$('html, body').animate({ scrollTop: linkEl.offset().top-height }, 750);
	};

	/*Add event listener*/
	$(".scroll-this").click(scrollToElementOnLinkClick);

});