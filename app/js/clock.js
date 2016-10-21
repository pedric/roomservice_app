/* Author: Fredrik Larsson | svartselet.se
––––––––––––––––––––––––––––––––––––––––––––––––*/
function time() {

	// Clock to header
	// Set clock
	var time = new Date();
	var hours = time.getHours();
	var minutes = time.getMinutes();

	if (minutes <= 9) { minutes = "0" + minutes; }

	// Set date
	var year = time.getFullYear();
	var month = time.getMonth() + 1;
	var day = time.getUTCDate();

	if (month <= 9) { month = "0" + month; }
	if (day <= 9) { day = "0" + day; }

	// Output
	$("#clock").html(hours + ":" + minutes);
	$("#date").html(year + "/" + month + "/" + day);
}

// Update clock every 10 seconds
$(function(){
	time();
	setInterval('time()', 10000);
});