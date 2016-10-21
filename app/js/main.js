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
/* Author: Fredrik Larsson | svartselet.se
––––––––––––––––––––––––––––––––––––––––––––––––*/

$(function(){

	 $('body').on('click','.ajaxLoader',function( e ){

		e.preventDefault();

		var link = $(this).attr('href');

		targetElement = $(".container");

		$.ajax({
			url: link,
			datatype: 'php',
			success: function( data ) {
				targetElement.fadeOut(0).html(data).fadeIn(200);
			}
		});
	});
});


// Open edit user popup
function editUserPopup( id, hotel ) {

	$.ajax({ 
			url: "edit_user_popup.php",
			data: { user_id: id, hotel: hotel },
			type: "GET",
			success: function ( data ) {
				
				$("body").append( data );
				$("#edit-alternatives").animate({ "opacity": 1 });
			}
		}); // end ajax
		
	}

// Open edit room popup
function editRoomPopup( id, hotel ) {

	$.ajax({ 
			url: "edit_room_popup.php",
			data: { room_number: id, hotel: hotel },
			type: "GET",
			success: function ( data ) {
				
				$("body").append( data );
				$("#edit-alternatives").animate({ "opacity": 1 });
			}
		}); // end ajax
		
	}

// Open add room popup
function addRoomPopup() {

	$.ajax({ 
			url: "add_room.php",
			type: "GET",
			success: function ( data ) {
				
				$("body").append( data );
				$("#edit-alternatives").animate({ "opacity": 1 });
			}
		}); // end ajax
		
	}

// Open add user popup
function addUserPopup() {

	$.ajax({ 
			url: "add_user.php",
			type: "GET",
			success: function ( data ) {
				
				$("body").append( data );
				$("#edit-alternatives").animate({ "opacity": 1 });
			}
		}); // end ajax
		
	}
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
/*!
 * jQuery UI Touch Punch 0.2.3
 *
 * Copyright 2011–2014, Dave Furfero
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * Depends:
 *  jquery.ui.widget.js
 *  jquery.ui.mouse.js
 */
!function(a){function f(a,b){if(!(a.originalEvent.touches.length>1)){a.preventDefault();var c=a.originalEvent.changedTouches[0],d=document.createEvent("MouseEvents");d.initMouseEvent(b,!0,!0,window,1,c.screenX,c.screenY,c.clientX,c.clientY,!1,!1,!1,!1,0,null),a.target.dispatchEvent(d)}}if(a.support.touch="ontouchend"in document,a.support.touch){var e,b=a.ui.mouse.prototype,c=b._mouseInit,d=b._mouseDestroy;b._touchStart=function(a){var b=this;!e&&b._mouseCapture(a.originalEvent.changedTouches[0])&&(e=!0,b._touchMoved=!1,f(a,"mouseover"),f(a,"mousemove"),f(a,"mousedown"))},b._touchMove=function(a){e&&(this._touchMoved=!0,f(a,"mousemove"))},b._touchEnd=function(a){e&&(f(a,"mouseup"),f(a,"mouseout"),this._touchMoved||f(a,"click"),e=!1)},b._mouseInit=function(){var b=this;b.element.bind({touchstart:a.proxy(b,"_touchStart"),touchmove:a.proxy(b,"_touchMove"),touchend:a.proxy(b,"_touchEnd")}),c.call(b)},b._mouseDestroy=function(){var b=this;b.element.unbind({touchstart:a.proxy(b,"_touchStart"),touchmove:a.proxy(b,"_touchMove"),touchend:a.proxy(b,"_touchEnd")}),d.call(b)}}}(jQuery);