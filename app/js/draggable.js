/* Author: Fredrik Larsson | svartselet.se
––––––––––––––––––––––––––––––––––––––––––––––––*/

/* Header */

$(function(){

	// Get window height and set minus margin to that value minus 50px (visible dragdown part)
	var height = $(window).height();

	var marginTop = height + 50 - (2 * height);

	$("header").css({ "margin-top": marginTop, "height": height });

	
	$( ".draggable" ).draggable({ axis: "y", containment: 'window', stop: function() {
			snapToTopBottom();
		}
	});


	// Change direction on arrow icon depending if header is down or up
	function flipArrow() {

		var headerTop = $("header").css("top");

		headerTop = parseInt(headerTop, "10");

		var flippingPoint = 30;

		if ( headerTop > flippingPoint ) {
			$("#arrow").removeClass("fa-angle-down").addClass("fa-angle-up");
		} else {
			$("#arrow").removeClass("fa-angle-up").addClass("fa-angle-down");
		}
	}


	// Make header animate to top or bottom of window depending on drop position, calls function flipArrow
	function snapToTopBottom() {

		var height = $(window).height();

		var breakingPoint = height / 2;

		var headerTop = $("header").css("top");

		headerTop = parseInt(headerTop, "10");

		if (breakingPoint >= headerTop) {
			$("header").animate({ "top": 0 }, function(){ 
				flipArrow(); 
			});
		} else {
			$("header").animate({ "top": height-50 }, function(){ 
				flipArrow(); 
			});
		}
	}
});

/* To do */

$(function(){

	$( ".draggable-todo-item" ).draggable({ axis: "x", containment: 'parent', revert: "invalid", snap: ".droppable", snapMode: "inner", snapTolerance: 50 });

	// Dropevent triggers ajax-call to change status in db, on success item fades out
	$( ".droppable-to-do" ).droppable({
		drop: function( event, ui ) {

			// Data to db-queries saved in data-attributes
			var no = parseInt($(this).attr("data-roomnumber"));

			var hotel_id = $(".todo-list").attr("data-hotel");

			var thisEl = $(this).parent("li");

			$.ajax({ 
				url: "update_room_status.php",
				data: { room_number: no, status: 1, hotel: hotel_id },
				type: "GET",
				success: function ( data ) {
					
					$(thisEl).animate({"opacity": 0}, function(){
						$(thisEl).animate({"height": 0, "marginBottom": 0}, function(){
							$(thisEl).remove();
						});
					});	
				}
			}); // end ajax

		} 
	});

	/* Dirtify */

	$( ".draggable-dirtify-item" ).draggable({ axis: "x", containment: 'parent', revert: "invalid", snap: ".droppable", snapMode: "inner", snapTolerance: 50 });

	// Dropevent triggers ajax-call to change status in db, on success item fades out
	$( ".droppable-dirtify" ).droppable({
		drop: function( event, ui ) {

			// Data to db-queries saved in data-attributes
			var no = parseInt($(this).attr("data-roomnumber"));

			var hotel_id = $(".dirtify-list").attr("data-hotel");

			var thisEl = $(this).parent("li");

			$.ajax({ 
				url: "update_room_status.php",
				data: { room_number: no, status: 0, hotel: hotel_id },
				type: "GET",
				success: function ( data ) {

					$(thisEl).animate({"opacity": 0}, function(){
						$(thisEl).animate({"height": 0, "marginBottom": 0}, function(){
							$(thisEl).remove();
						});
					});	
				}
			}); // end ajax

		} 
	});

});