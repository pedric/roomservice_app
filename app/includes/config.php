<?php
/*********************************************************************/
/* configurationfile Clean Rooms                                     */
/* Author: Fredrik Larsson                                           */
/*                                                                   */
/*********************************************************************/

// Include classes
require_once("classes/room.class.php");
require_once("classes/user.class.php");

// start session
session_start();


/* Form responders
************************************************************************/

// Register new organisation
/* ******************************************************************* */
if (isset($_REQUEST["submitRegister"])) {

	// Request input data
	$user = strtolower($_REQUEST["registerEmailInput"]);
	$password = $_REQUEST["registerPasswordInput"];
	$hotel = strtolower($_REQUEST["registerHotelInput"]);

	// Min 4 char each field. Different error messages on different errors ( if errors occurs )
	if ( strlen($hotel) >= 4 && strlen($user) >= 4 && strlen($password) >= 4 ) {

		$new_user = new User();

		if ( !$new_user->hotelExists( $hotel ) && !$new_user->userExists( $user ) ) {

			if ( !$new_user->register($user, $password, $hotel) ) {

				echo "<div id='error-msg'>
						<div class='container'>
							<div id='close-button-wrapper'><img src='images/close.png' onclick='closeErrorMsg()' alt='Close button' /></div>
							<h2>Sorry...</h2>
							<p>
							...but something went wrong, please try again later.
							</p>
						</div>
					</div>";
			}

		} else {

			echo "<div id='error-msg'>
				<div class='container'>
					<div id='close-button-wrapper'><img src='images/close.png' onclick='closeErrorMsg()' alt='Close button' /></div>
					<h2>Sorry...</h2>
					<p>
					...but the hotelname $hotel and/or mailadress $user is already in use.
					</p>
				</div>
			</div>";
		}

	} else {

		echo "<div id='error-msg'>
				<div class='container'>
					<div id='close-button-wrapper'><img src='images/close.png' onclick='closeErrorMsg()' alt='Close button' /></div>
					<h2>Try again.</h2>
					<p>
					4 Charachters is minimum for all fields.
					</p>
				</div>
			</div>";

	}

	unset($_REQUEST["submitRegister"]);

}

// Login user
/* ******************************************************************* */
if (isset($_REQUEST["submitLogin"])) {

	// Request input data
	$user = $_REQUEST["loginEmailInput"];
	$password = $_REQUEST["loginPasswordInput"];

	$login_user = new User();

	// If login fails, echo error msg
	if ( !$login_user->login($user, $password, false) ) {

		echo "<div id='error-msg'>
				<div class='container'>
					<div id='close-button-wrapper'><img src='images/close.png' onclick='closeErrorMsg()' alt='Close button' /></div>
					<h2>Incorrect details.</h2>
					<p>
					Forgot your password? Click <a href='includes/forgot_password.php' class='ajaxLoader white' onclick='closeErrorMsg()'>here to reset</a>.
					</p>
					<p>If you lost the required information, please contact an administrator at your organisation.
					</p>
				</div>
			</div>";
	}

	unset($_REQUEST["submitLogin"]);

}

// Forgotten password
/* ******************************************************************* */
if (isset($_REQUEST["forgotLogin"])) {

	// Request input data
	$user = $_REQUEST["forgotEmailInput"];

	$obj = new User();

	// If details is incorrect, echo error msg
	if ( !$obj->mailAccountDetails( $user ) ) {

		echo "<div id='error-msg'>
				<div class='container'>
					<div id='close-button-wrapper'><img src='images/close.png' onclick='closeErrorMsg()' alt='Close button' /></div>
					<h2>Incorrect details.</h2>
					<p>If you lost the required information, please contact an administrator at your organisation.
					</p>
				</div>
			</div>";
	}

	unset($_REQUEST["forgotLogin"]);

}

// Create new employee
/* ******************************************************************* */
if (isset($_REQUEST["submitAddUser"])) {

	// Request input data and session vars
	$user = $_REQUEST["addUserEmailInput"];
	$hotel_id = $_SESSION["hotel_id"];
	$role = $_REQUEST["role"];

	$add_user = new User();

	// Class-method sends mail with logindetails
	if ( !$add_user->createNewEmployee($user, $hotel_id, $role) ) {

		echo "<div id='error-msg'>
				<div class='container'>
					<div id='close-button-wrapper'><img src='images/close.png' onclick='closeErrorMsg()' alt='Close button' /></div>
					<h2>Error.</h2>
					<p>
						There is already a user with that mailaddress.
					</p>
				</div>
			</div>";
	
	}

	unset($_REQUEST["submitAddUser"]);

}


// Register new user password from mailing link
/* ******************************************************************* */
if (isset($_REQUEST["submitNewUserFromMail"])) {

	// Request input data
	$user = $_REQUEST["newMailEmailInput"];
	$password = $_REQUEST["newMailPasswordInput"];

	$add_user = new User();

	// Class-method sends mail with logindetails
	if ( !$add_user->newUserSetPassword( $user, $password ) ) {

		echo "<div id='error-msg'>
				<div class='container'>
					<div id='close-button-wrapper'><img src='images/close.png' onclick='closeErrorMsg()' alt='Close button' /></div>
					<h2>Error.</h2>
					<p>
						Have you typed at least 4 charachters?
					</p>
				</div>
			</div>";
	
	}

	unset($_REQUEST["submitNewUserFromMail"]);

}

// Change password from profile page
/* ******************************************************************* */
if (isset($_REQUEST["changePasswordSubmit"])) {

	// Request input data and session var
	$user_id = $_SESSION["user_id"];
	$password = $_REQUEST["changePasswordInput"];

	$obj = new User();

	// Class-method sends mail with logindetails
	if ( !$obj->changePassword( $user_id, $password ) ) {

		echo "<div id='error-msg'>
				<div class='container'>
					<div id='close-button-wrapper'><img src='images/close.png' onclick='closeErrorMsg()' alt='Close button' /></div>
					<h2>Error.</h2>
					<p>
						Have you typed at least 4 charachters?
					</p>
				</div>
			</div>";
	
	}

	unset($_REQUEST["changePasswordSubmit"]);

}

// Create new room
/* ******************************************************************* */
if (isset($_REQUEST["submitAddRoom"])) {

	$room_number = "";
	$room_type = "";
	$no_beds = "";

	// Request input data and session var
	$room_number = $_REQUEST["addRoomNumberInput"];
	$room_type = $_REQUEST["type"];
	$no_beds = $_REQUEST["addNoBedsInput"];

	$hotel_id = $_SESSION["hotel_id"];

	$add_room = new Room();

	// If function fails echo error-msg
	if ( !$add_room->createNewRoom( $room_number, $hotel_id, $room_type, $no_beds )) {

		echo "<div id='error-msg'>
				<div class='container'>
					<div id='close-button-wrapper'><img src='images/close.png' onclick='closeErrorMsg()' alt='Close button' /></div>
					<h2>Error.</h2>
					<p>
						This room already exists.
					</p>
				</div>
			</div>";
	}

	unset($_REQUEST["submitAddRoom"]);

}


// Edit hotel info
/* ******************************************************************* */
if (isset($_REQUEST["submitEditHotel"])) {

	$address = "";
	$phone = "";
	$mail = "";

	// Request input data
	$phone = $_REQUEST["editHotelPhoneInput"];
	$mail = $_REQUEST["editHotelMailInput"];

	$hotel_id = $_SESSION["hotel_id"];

	$add_room = new Room();

	$add_room->updateHotel($phone, $mail, $hotel_id);

	unset($_REQUEST["submitEditHotel"]);

}


// Edit profile info
/* ******************************************************************* */
if (isset($_REQUEST["submitEditProfile"])) {

	$mail = "";
	$phone = "";
	$firstname = "";
	$lastname = "";

	// Request input data and session vars
	$mail = $_SESSION["username"];
	$phone = $_REQUEST["editProfilePhoneInput"];
	$firstname = $_REQUEST["editProfileFirstnameInput"];
	$lastname = $_REQUEST["editProfileLastnameInput"];

	$hotel = $_SESSION["hotel"];

	$edit_user = new User();

	$edit_user->updateUser($mail, $firstname, $lastname, $phone );

	unset($_REQUEST["submitEditProfile"]);

}


// Update mood
/* ******************************************************************* */
if (isset($_REQUEST["mood"])) {

	$user = $_SESSION['username'];

	$mood = $_REQUEST["mood"];

	$user_mood = new User();

	$user_mood->setMoodToday( $user, $mood );

	unset($_REQUEST["mood"]);

}



// Delete user
/* ******************************************************************* */
if (isset($_REQUEST["submitDeleteUser"])) {

	$user_id = "";

	// Request input data and session var
	$user_id = $_REQUEST["userId"];

	$hotel = $_SESSION["hotel"];

	$obj = new User();

	$obj->deleteUser($user_id );

	unset($_REQUEST["submitDeleteUser"]);

}


// Delete room
/* ******************************************************************* */
if (isset($_REQUEST["submitDeleteRoom"])) {

	$room_number = "";

	// Request input data and session var
	$room_number = $_REQUEST["room_number"];

	$hotel = $_SESSION["hotel"];

	$obj = new Room();

	$obj->deleteRoom($room_number);

	$obj->deleteRoomFromStatus($room_number);

	unset($_REQUEST["submitDeleteRoom"]);

}


?>