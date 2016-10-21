<?php
/*********************************************************************/
/* class Room (extends SQLite3)                                      */
/* Handles room queries                                              */
/* Author: Fredrik Larsson                                           */
/*                                                                   */
/*********************************************************************/
class Room extends SQLite3
{

	/**
	* Room number
	*/
	private $room_number;
	/**
	* hotel name
	*/
	private $hotel_name;
	/**
	* Phone number
	*/
	private $phone;
	/**
	* E-mail (username)
	*/
	private $mail;
	/**
	* Start date (account registration time)
	*/
	private $start_date;



	/**
	* Constructor
	* Opens SQLite3 database "data.DB"
	*/
	function __construct() {

		$flags = SQLITE3_OPEN_READWRITE;
		
		$this->open("db/data.db", $flags);
	}



	/**
	* Create new room
	* @param int $room_number
	* @param int $hotel_id
	* @param int $room_type
	* @param int $no_beds
	* @return bool
	*/
	public function createNewRoom( $room_number, $hotel_id, $room_type, $no_beds ) {

		// Prevent SQL-injections
    	$room_number = SQLite3::escapeString($room_number);
    	$room_type = SQLite3::escapeString($room_type);
    	$no_beds = SQLite3::escapeString($no_beds);

    	// Set values
    	$this->setRoom($room_number);

    	// Check if the roomnumber already exists
    	if ( !$this->roomExists( $room_number, $hotel_id ) ) {

			// Execute queries and insert new room to tables: rooms and room_status
			$result = true;

			if (!$this->exec("INSERT INTO rooms (room_number, hotel_id, no_beds, room_type) VALUES ('$this->room_number', '$hotel_id', '$no_beds', '$room_type')")) { 
				
				$result = false; 
			}

			if (!$this->exec("INSERT INTO room_status (room_number, hotel_id, clean) VALUES ('$this->room_number', $hotel_id, 0)")) { 
				
				$result = false; 
			}

			if ($result) {

				return true;

			} else {

				return false;
			}
    	
    	} else {

    		return false;
    	}   

	}



	/**
	* List all rooms
	* @return string
	*/
	public function listRooms() {

    	// Select all rooms
    	$sql = "SELECT * FROM rooms";

    	$result = $this->query($sql);

    	$rows = count($result);
    	
    	if ( $rows > 0 ) {

    		// Echo heading and <ul>
    		echo "<h1 style='text-align:center;'>Rooms.</h1><ul class='room-list'>";

    		// Loop result and as echo as with markup (html, inline CSS and JS-eventhandlers)
    		while ($row = $result->fetchArray(SQLITE3_ASSOC)) {

    			$delete_button = "";
    		
	    		$room_number = $row['room_number'];
	    		$no_beds = $row['no_beds'];
	    		$room_type = ucfirst($row['room_type']);

	    		if ( $this->isClean($room_number) ) { 
	    			$room_status = "style='background-color:#74a950;'"; 
	    		} else {
	    			$room_status = "style='background-color:#dc4c4c;'";
	    		}

	    		$obj = new User();

	    		$role = $obj->isAdmin($_SESSION['username']);

	    		if ($role) { 

	    			$id = (int)$room_number;

	    			$deletebutton = "<div><p><i class='fa fa-pencil edit-user-button' aria-hidden='true' onclick='editRoomPopup($id)'></i></p></div>"; 
	    		}
	    		
	    		$output = "<li>";
	    		$output .= "<div $room_status><h3><strong>$room_number</strong></h3></div>";
	    		$output .= "<div>";
	    		$output .= "<p><strong>$room_type</strong>-unit with <strong>$no_beds beds</strong>.</p>";
	    		$output .= "</div>";
	    		$output .= $deletebutton;
	    		$output .= "</li>";

	    		echo $output;

    		}

    		// End <ul>
    		echo "</ul>";
    	
    	} else {

    		return false;
    	}    	  	

    }



    /**
	* List (individual) room info
	* @return string
	*/
	public function listRoomInfo( $hotel_id, $room_number ) {

		// No injection-filter, data from non input (or already filtered inputs)
		$hotel_id = (int)$hotel_id;
		$room_number = (int)$room_number;

		// Select the rooms cleaning history
    	$sql = "SELECT * FROM cleaning_history WHERE room_number = $room_number AND hotel_id = $hotel_id";

    	$result = $this->query($sql);

    	$rows = count($result);
    	
    	if ( $rows > 0 ) {

    		// Echo <ul>
    		echo 	"<h2 style='text-align:center;'>Room $room_number</h2>
    				<h4 style='text-align:center;'>Cleaning history.</h4>
    				<ul class='room-cleaning-list'>";

    		// Loop result and echo markedup
    		while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    		
	    		$action = "Unregistered action ";
	    		if ( $row['action'] == 0 ){ $action = "Cleaned"; }
	    		if ( $row['action'] == 1 ){ $action = "Dirtified"; }

	    		$name = $this->getFullNameFromId( $row['user_id'] );

	    		$time = $row['cleaning_time'];
	    		
	    		$output = "<li>";
	    		$output .= "$action by $name, $time";
	    		$output .= "</li>";

	    		echo $output;

    		}

    		// End output </ul>
    		echo "</ul>";
    	
    	} else {

    		return false;
    	}    	  	

    }



    /**
	* Adds data to cleaning history
	* @param int $room_number
	* @param int $hotel_id
	* @param int $user_id
	* @param int $action
	* @return bool
	*/
	public function cleaningHistory( $room_number, $hotel_id, $user_id, $action ) {

		// Set new row on "dirtify"- or "to do"-events with: room, hotel, who and what action
    	$sql = "INSERT INTO cleaning_history ( room_number, hotel_id, user_id, action ) VALUES ( $room_number, $hotel_id, $user_id, $action )";
    	
    	if ( $result = $this->exec($sql) ) {

    		return true;
    	
    	} else {

    		return false;
    	}    	  	
    }



    /**
	* Get firstname and lastname as one string from user_id
	* @return string
	*/
	public function getFullNameFromId( $user_id ) {

		$user_id = (int)$user_id;

    	$sql = "SELECT firstname, lastname FROM users WHERE user_id = $user_id";

    	$result = $this->query($sql);

    	$rows = count($result);
    	
    	if ( $rows > 0 ) {

    		$row = $result->fetchArray(SQLITE3_ASSOC);
    		$name = ucfirst( $row['firstname'] ) . " " . ucfirst( $row['lastname'] );

    		return $name;
    	
    	} else {

    		return false;
    	}    	  	

    }



    /**
	* Sets all hotel info
	* @param $hotel_id
	* @return strings
	*/
	public function thisHotelInfo( $hotel_id ) {

    	$sql = "SELECT * FROM hotels WHERE hotel_id = '$hotel_id'";

    	$result = $this->query($sql);

    	$rows = count($result);
    	
    	if ( $rows > 0 ) {

    		$row = $result->fetchArray(SQLITE3_ASSOC);
    		$this->hotel_name = $row['hotel_name'];
    		$this->phone = $row['phone'];
    		$this->mail = $row['mail'];
    		$this->start_date = $row['start_date'];
    	
    	} else {

    		return false;
    	}    	  	

    }



    /**
	* Update hotel info
	* @param int $phone
	* @param int $mail
	* @param int $hotel_id
	* @return bool
	*/
	public function updateHotel( $phone, $mail, $hotel_id ) {

		// Prevent SQL-injections
    	$address = SQLite3::escapeString($address);
    	$phone = SQLite3::escapeString($phone);
    	$mail = SQLite3::escapeString($mail);

		// Execute query
		$result = true;
		if (!$this->exec("UPDATE hotels SET phone = '$phone', mail = '$mail' WHERE hotel_id = $hotel_id")) {

			$result = false; 
		}

		if ($result) {

			return true;
    	
    	} else {
    		
    		return false;
    	}   

	}



	/**
	* Delete room from table rooms
	* @param int $room_number
	* @return bool
	*/
	public function deleteRoom( $room_number ) {

    	$sql = "DELETE FROM rooms WHERE room_number = '$room_number'";
    	
    	if ( $result = $this->query($sql) ) {

    		return true;
    	
    	} else {

    		return false;
    	}    	  	
    }



    /**
	* Delete room from table room_status
	* @param int $room_number
	* @return bool
	*/
	public function deleteRoomFromStatus( $room_number ) {

    	$sql = "DELETE FROM room_status WHERE room_number = '$room_number'";
    	
    	if ( $result = $this->query($sql) ) {

    		return true;
    	
    	} else {

    		return false;
    	}    	  	
    }



	/**
	* Echoes To Do list
	* @return string
	*/
	public function toDoList( $hotel_id ) {

    	// If there is no dirty rooms ( 0 ), echo after else below
    	$sql = "SELECT count(*) AS 'needle' FROM room_status WHERE clean = 0 AND hotel_id = $hotel_id";

    	$amount_rooms = $this->query($sql);

    	$amount_rooms = $amount_rooms->fetchArray(SQLITE3_ASSOC);

    	$amount_rooms = $amount_rooms["needle"];

    	if ( $amount_rooms > 0 ) {

    		$sql = "SELECT * FROM room_status WHERE clean = 0 AND hotel_id = $hotel_id ORDER BY room_number ASC";

	    	$result = $this->query($sql);

    		// Echo start of <ul>
    		echo "<h1 style='text-align:center;''>To do.</h1><ul class='todo-list' data-hotel='$hotel_id'>";

    		// Loop through result-set and echo with markup
    		while ($row = $result->fetchArray(SQLITE3_ASSOC)) {

	    		$room_number = $row['room_number'];

	    		$output = "<li>";
	    		$output .= "<div class='draggable-todo-item'>";
	    		$output .= "<p>$room_number</p>";
	    		$output .= "</div>";
	    		$output .= "<div class='dropzone droppable-to-do' data-roomnumber='$room_number'><i class='fa fa-check-circle' aria-hidden='true'></i></div>";
	    		$output .= "</li>";

	    		echo $output;

    		}

			echo "</ul>";
    	
    	} else {
    
    		echo "<h4>There is nothing to do here but change and go to the beach!</h4>";
    	}    	  	
    }



    /**
	* Echoes To Do list
	* @return string
	*/
	public function dirtifyList( $hotel_id ) {
    	
    	// If there is no clean rooms ( 1 ), echo after else below
    	$sql = "SELECT count(*) AS 'needle' FROM room_status WHERE clean = 1 AND hotel_id = $hotel_id";

    	$amount_rooms = $this->query($sql);

    	$amount_rooms = $amount_rooms->fetchArray(SQLITE3_ASSOC);

    	$amount_rooms = $amount_rooms["needle"];

    	if ( $amount_rooms > 0 ) {

    		$sql = "SELECT * FROM room_status WHERE clean = 1 AND hotel_id = $hotel_id ORDER BY room_number ASC";

	    	$result = $this->query($sql);

	    	// Echo start of <ul>
    		echo "<h1 style='text-align:center;''>Dirtify.</h1><ul class='dirtify-list' data-hotel='$hotel_id'>";

    		// Loop through result-set and echo with markup
    		while ($row = $result->fetchArray(SQLITE3_ASSOC)) {

	    		$room_number = $row['room_number'];

	    		$output = "<li>";
	    		$output .= "<div class='draggable-dirtify-item'>";
	    		$output .= "<p>$room_number</p>";
	    		$output .= "</div>";
	    		$output .= "<div class='dropzone droppable-dirtify' data-roomnumber='$room_number'><i class='fa fa-check-circle' aria-hidden='true'></i></div>";
	    		$output .= "</li>";

	    		echo $output;

    		}

			echo "</ul>";
    	
    	} else {
    		
    		echo "<h4>It seems like all the rooms are dirty already, time to work...</h4>";
    	}    	  	
    }



    /**
	* Change status clean/dirty room depending on $status ( 0 or 1 )
	* @param int $room_number
	* @param int $status
	* @param int $hotel_id
	* @param int $user_id
	* @return bool
	*/
	public function updateToDoList( $room_number, $status, $hotel_id, $user_id ) {

		$status = (int)$status;
		$room_number = (int)$room_number;
		$hotel_id = (int)$hotel_id;
		$user_id = (int)$user_id;

		// Add action to cleaning_history table
		$this->cleaningHistory( $room_number, $hotel_id, $user_id, $status );

		// Execute query
		if ($this->exec("UPDATE room_status SET clean = $status WHERE room_number = $room_number")) { 
			
			return true; 
    	
    	} else {
    		
    		return false;
    	}   

	}



	/**
	* Check room status (Dirty / Clean)
	* @param int $room_number
	* @return bool
	*/
	public function isClean( $room_number ) {

    	$sql = "SELECT clean FROM room_status WHERE room_number = $room_number";

    	$result = $this->query($sql);

    	$rows = count($result);
    	
    	if ( $rows > 0 ) {

    		$row = $result->fetchArray(SQLITE3_ASSOC);
    		$status = $row['clean'];

    		if ($status) { return true; } else { return false; }
    	
    	} else {

    		return false;
    	}    	  	

    }



    /**
	* Check if room number already exists
	* @param int $room_number
	* @param int $hotel_id
	* @return bool
	*/
	public function roomExists( $room_number, $hotel_id ) {

    	$sql = "SELECT count(*) AS 'needle' FROM rooms WHERE room_number = $room_number AND hotel_id = $hotel_id";

    	$result = $this->query($sql);

    	$result = $result->fetchArray(SQLITE3_ASSOC);

    	$result = $result["needle"];

    	if ( $result > 0 ) {

    		return true;
    	
    	} else {

    		return false;
    	}    	  	

    }



    /**
	* Get Room
	* @return string
	*/
	public function getRoom() {
		return $this->room_number;
	}


    /**
	* Get Name
	* @return string
	*/
	public function getName() {
		return $this->hotel_name;
	}


    /**
	* Get Phone
	* @return string
	*/
	public function getPhone() {
		return $this->phone;
	}


    /**
	* Get Mail
	* @return string
	*/
	public function getMail() {
		return $this->mail;
	}

	 /**
	* Get Start date
	* @return string
	*/
	public function getStartDate() {
		return $this->start_date;
	}

	/**
	* Set Room
	* @param $room_number
	*/
	public function setRoom($room_number) {
		if ($room_number != "") {
			$room_number = (int)$room_number;
			return $this->room_number = $room_number;
		} else {
			return false;
		}
	}

	/**
	* Set Name
	* @param $address
	*/
	public function setName($hotel_name) {
		if ($hotel_name != "") {
			$hotel_name = strtolower($hotel_name);
			return $this->hotel_name = $hotel_name;
		} else {
			return false;
		}
	}

	/**
	* Set Phone
	* @param $phone
	*/
	public function setPhone($phone) {
		if ($phone != "") {
			$phone = strtolower($phone);
			return $this->phone = $phone;
		} else {
			return false;
		}
	}

	/**
	* Set Mail
	* @param $mail
	*/
	public function setMail($mail) {
		if ($mail != "") {
			$mail = strtolower($mail);
			return $this->mail = $mail;
		} else {
			return false;
		}
	}

	/**
	* Set Start date
	* @param $start_date
	*/
	public function setStartDate($start_date) {
		if ($start_date != "") {
			$start_date = strtolower($start_date);
			return $this->start_date = $start_date;
		} else {
			return false;
		}
	}

}