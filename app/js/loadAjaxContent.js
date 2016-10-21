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