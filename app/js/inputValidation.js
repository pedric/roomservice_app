/* Author: Fredrik Larsson | svartselet.se
––––––––––––––––––––––––––––––––––––––––––––––––*/
$(function(){

	// Shows pink background on inputfields if it is under 4 chars and hide submit-button til validation is true
	$("body").on("keyup", ".validate input", function(){

		valid = false;

		var val = $(this).val().length;
		
		if ( val <= 3 ) { 
			$(this).css( "background", "pink" );
			$(this).parent().next( ".validate-to-submit" ).animate({ "height": "0px" }, 200);
			valid = false;

		} else {

			$(this).css( "background", "#fff" );
			valid = true;
		}

		if ( valid ) {

			$(this).parent().next( ".validate-to-submit" ).animate({ "height": "38px" }, 200);
		}

	});
});