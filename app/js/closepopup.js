/* Author: Fredrik Larsson | svartselet.se
––––––––––––––––––––––––––––––––––––––––––––––––*/

// Functions to fade out and remove error messages from DOM onclick-event
function closeThis() {
	$("#edit-alternatives").animate({ "opacity": 0 }, function(){
		$(this).remove();
	});
}

function closeErrorMsg() {
	$("#error-msg").animate({ "opacity": 0 }, function(){
		$(this).remove();
	});
}