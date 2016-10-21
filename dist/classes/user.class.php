<?php
/*********************************************************************/
/* class User (extends SQLite3)                                      */
/* Handles user queries                                              */
/* Author: Fredrik Larsson                                           */
/*                                                                   */
/*********************************************************************/
class User extends SQLite3
{

	/**
	* User (email)
	*/
	private $firstname;
	/**
	* User (email)
	*/
	private $lastname;
	/**
	* User (email)
	*/
	private $phone;
	/**
	* User (email)
	*/
	private $image;
	/**
	* User (email)
	*/
	private $role;
	/**
	* User (email)
	*/
	private $active;
	/**
	* User (email)
	*/
	private $start_date;
	/**
	* User (email)
	*/
	private $user;
	/**
	* Password
	*/
	private $password;
	/**
	* Password
	*/
	private $user_id;

	

	/**
	* Constructor
	* Opens SQLite3 database "data.DB"
	*/
	function __construct() {

		$flags = SQLITE3_OPEN_READWRITE;
		
		$this->open("db/data.db", $flags);
	}



	/**
	* Create new user-account
	* @param string $user
	* @param string $password
	* @param string $hotel
	* @return send to login-function or bool
	*/
	public function register( $user, $password, $hotel ) {

		// Prevent SQL-injections
    	$user = SQLite3::escapeString($user);
    	$password = SQLite3::escapeString($password);
    	$hotel = SQLite3::escapeString($hotel);

    	// Save uncrypted password-input for login below
    	$uncrypted_password = $password;

		//crypt password
		$salt = "S4ltA" . $user . "p3Ppr4";
    	$password = crypt($password, $salt);

    	// Lowercase strings
    	$hotel = strtolower($hotel);
    	$user = strtolower($user);

    	// Set values
    	$this->setUser($user);
    	$this->setPassword($password);
    	$this->setHotel($hotel);

    	// Check if hotelname and/or username already exists
    	if ( !$this->hotelExists( $this->hotel ) && !$this->userExists( $this->user ) ) {

			$result = true;

			// Insert new hotel to hotels-table
			if ( !$this->exec("INSERT INTO hotels (hotel_name) VALUES ('$this->hotel')") ) 
			{ 
				$result = false; 
			}

			// Get hotel_id from recently added hotel as foreign key for new user
			$get_hotel_id = $this->query( "SELECT hotel_id FROM hotels WHERE hotel_name = '$this->hotel'" );
			
			$row = $get_hotel_id->fetchArray(SQLITE3_ASSOC);

			$hotel_id = $row['hotel_id'];

			// Insert new user to users-table
			if ( !$this->exec("INSERT INTO users (hotel_id, mail, password, role, active) VALUES ('$hotel_id', '$this->user', '$this->password', 'owner', 1)") )
			{ 
				$result = false; 
			}

			if ( $result === true ) {
				
				// Login new user
				$this->login($this->user, $uncrypted_password, true);

			}

		} else {

			return false;
		}
	}



	/**
	* Login user
	* @param string $user
	* @param string $password
	* @param boolean $first_login
	* @return redirects or bool
	*/
	public function login( $user, $password, $first_login ) {

		// Prevent SQL-injections
    	$user = SQLite3::escapeString($user);
    	$password = SQLite3::escapeString($password);

		// Lowercase input
		$user = strtolower($user);

		//crypt password
		$salt = "S4ltA" . $user . "p3Ppr4";
    	$password = crypt($password, $salt);

    	// Set values
    	$this->setUser($user);
    	$this->setPassword($password);
    	$this->setHotel($hotel);

    	// validate if there is any hits on username + password
    	if ( $this->validateLogin( $this->user, $this->password ) ) {

	    	// Set to active user (if ignored reset password mail)
	    	$this->activateUser( $user );

	    	// Search user data for sessionvariables
	    	$sql = "SELECT user_id, hotel_id, firstname, mail FROM users WHERE mail = '$this->user' AND password = '$this->password'";

	    	$result = $this->query($sql);

    		// Set session-variables
    		while ( $row = $result->fetchArray(SQLITE3_ASSOC)) {
	    		$_SESSION["user_id"] = $row["user_id"];
	    		$_SESSION["loggedin"] = true;
	        	$_SESSION["username"] = $row["mail"];
	        	$_SESSION["hotel_id"] = $row["hotel_id"];

	        	// First time user?	        	
	        	if ( $first_login ) {
	        		$location = "about.php"; 
	        	} else {
	        		$location = "profile.php";
	        	}

	        	// Redirect
				header("Location: " . $location);
			}

    	} else {

    		return false;
    	}    	  	

    }



    /**
	* Sets new password
	* @param string $user
	* @param string $password
	* @return redirects or bool
	*/
	public function newUserSetPassword( $user, $password ) {

		// Prevent SQL-injections
    	$user = SQLite3::escapeString($user);
    	$password = SQLite3::escapeString($password);

    	// Save uncrypted password for direct login below
    	$uncrypted_password = $password;

		// Lowercase input
		$user = strtolower($user);

		//crypt password
		$salt = "S4ltA" . $user . "p3Ppr4";
    	$password = crypt($password, $salt);

    	// Set values
    	$this->setUser($user);
    	$this->setPassword($password);

    	// Insert new password and set active to 1 (only 0 can enter reset-page, forgot passwordlink sets to 0)
    	$sql = "UPDATE users SET password = '$this->password', active = 1 WHERE mail = '$this->user'";

    	if ( $this->exec($sql) ) {

    		// Login user
    		$this->login($this->user, $uncrypted_password, true);
    	
    	} else {

    		return false;
    	}
    }



    /**
	* Mail account details to user
	* @param string $user
	* @return bool
	*/
	public function mailAccountDetails( $user ) {

		// Lowercase input
		$user = strtolower($user);

    	// Prevent SQL-injections
    	$user = SQLite3::escapeString($user);

    	// Check if user exists
    	if ( $this->userExists( $user ) ) {

    		// Set active to 0, (resetpage cant be reached with active = 1)
    		if ( !$this->exec("UPDATE users SET active = 0 WHERE mail = '$user'") ) {

    			return false;
    		
    		} else {

    			// Set mail with headers
    			$to = $user;
	    		$subject = "Request to change password at Clean Rooms.";
	    		$message = "Follow this link to set a new password.\nhttp://svartselet.se/skola/dt148g/new_user_login.php?user=$user\nIf you have not requested a new password, log in with your former details to reactivate your account.\n";
	    		$message .= "Your details.\nUsername( e-mail ): $to";
	    		$headers = array("From: epost.larsson@gmail.com",
				    "Reply-To: epost.larsson@gmail.com",
				    "X-Mailer: PHP/" . PHP_VERSION
				);
				$headers = implode("\r\n", $headers);
	        	
	        	// Send mail with reset-link
	        	mail($to, $subject, $message, $headers);

	        	return true;
			}
    	
    	} else {
    		
    		return false;
    	}
    }



    /**
	* Create new (employee or administrator) user-account
	* @param string $user
	* @param string $hotel_id
	* @param string $role
	* @return sends mail or bool
	*/
	public function createNewEmployee( $user, $hotel_id, $role ) {

		// LOwercase input
		$user = strtolower($user);
		$role = strtolower($role);

		// Prevent SQL-injections
    	$user = SQLite3::escapeString($user);

    	// Check if username is taken
    	if ( !$this->userExists( $user ) ) {

			$result = true;

			// Execute query
			if ( !$this->exec("INSERT INTO users (hotel_id, mail, role, active) VALUES ($hotel_id, '$user', '$role', 0)") ) { 
	
				$result = false; 
			}

			if ( $result ) {

				// Set instructions-mail with headers sent to new user
				$to = $user;
				$subject = "Your account details at Clean Rooms.";
	    		$message = "You have been added to the Clean Rooms service by your employeer. Follow this link to activate your account: http://svartselet.se/skola/dt148g/new_user_login.php?user=$user\n\n";
	    		$message .= "Your details.\nUsername( e-mail ): $to";
	    		$headers = array("From: epost.larsson@gmail.com",
				    "Reply-To: epost.larsson@gmail.com",
				    "X-Mailer: PHP/" . PHP_VERSION
				);
				$headers = implode("\r\n", $headers);
	        	
	        	// Send mail
	        	mail($to, $subject, $message, $headers);

	        	return true;

			} else { 

				return false; }
    	
    	} else {

    		return false;
    	}   

	}



	/**
	* Update user-account
	* @param string $user
	* @param string $firstname
	* @param string $lastname
	* @param string $phone
	* @return bool
	*/
	public function updateUser( $user, $firstname, $lastname, $phone ) {

		// Prevent SQL-injections
    	$user = SQLite3::escapeString($user);
    	$firstname = SQLite3::escapeString($firstname);
    	$lastname = SQLite3::escapeString($lastname);
    	$phone = SQLite3::escapeString($phone);

    	// Set values
    	$this->setUser($user);
    	$this->setFirstname($firstname);
    	$this->setLastname($lastname);
    	$this->setPhone($phone);

		// Execute query
		if ($this->exec("UPDATE users SET firstname = '$this->firstname', lastname = '$this->lastname', phone = '$this->phone' WHERE mail = '$this->user'")) 
		{ 
			return true;

		} else {

    		return false;
    	}   

	}



	/**
	* Change password
	* @param int $user_id
	* @param string $password
	* @return bool
	*/
	public function changePassword( $user_id, $password ) {

		// Prevent SQL-injections
    	$password = SQLite3::escapeString($password);

    	//crypt password
		$salt = "S4ltA" . $user . "p3Ppr4";
    	$password = crypt($password, $salt);

    	// Set value
    	$this->setPassword($password);

		// Execute query
		if ( $this->exec("UPDATE users SET password = '$this->password' WHERE user_id = $user_id") ) 
		{ 
			return true;

		} else {

    		return false;
    	}   

	}



	/**
	* Sets user to active (after first login or reset password)
	* @param string $user
	* @return bool
	*/
	public function activateUser( $user ) {

		// Prevent SQL-injections
    	$user = SQLite3::escapeString($user);

		// Execute query
		if ( $this->exec("UPDATE users SET active = 1 WHERE mail = '$user'") ) 
		{ 
			return true;

		} else {

    		return false;
    	}   

	}



	/**
	* Show user profile
	* @param string $user
	* @return string or bool
	*/
	public function showUserProfile( $user ) {

    	// Set values
    	$this->setUser($user);

    	$sql = "SELECT * FROM users WHERE mail = '$this->user'";

    	$result = $this->query($sql);

    	$rows = count($result);
    	
    	if ( $rows > 0 ) {

    		// Echoes user-profile
    		$row = $result->fetchArray(SQLITE3_ASSOC);
    		$user_mail = $row['mail'];
    		$firstname = $row['firstname'];
    		$lastname = $row['lastname'];
    		$phone = $row['phone'];
    		$image = $row['image'];
    		$role = $row['role'];
    		
    		$output = "<div>";
    		$output .= "<p>$user_mail</p>";
    		$output .= "<p>$firstname</p>";
    		$output .= "<p>$lastname</p>";
    		$output .= "<p>$phone</p>";
    		$output .= "<p>$image</p>";
    		$output .= "<p>$role</p>";
    		$output .= "<div>";

    		echo $output;
    	
    	} else {
    		return false;
    	}    	  	

    }



    /**
	* Sets all params
	* @param string $user
	* @return bool
	*/
	public function thisUserInfo( $user ) {

		// Set values
    	$this->setUser($user);

    	$sql = "SELECT * FROM users WHERE mail = '$this->user'";

    	$result = $this->query($sql);

    	$rows = count($result);
    	
    	if ( $rows > 0 ) {

    		$row = $result->fetchArray(SQLITE3_ASSOC);

    		$this->setUser($row['mail']);
    		$this->setFirstname($row['firstname']);
    		$this->setLastname($row['lastname']);
    		$this->setPhone($row['phone']);
    		$this->setActive($row['active']);
    		$this->setRole($row['role']);
    		$this->setImage($row['image']);
    		$this->setStartDate($row['start_date']);

    		return true;
    	
    	} else {

    		return false;
    	}    	  	

    }



    /**
	* Get role from user_id
	* @param string $user
	* @return string or bool
	*/
	public function userRole( $user_id ) {

    	$sql = "SELECT role FROM users WHERE user_id = '$user_id'";

    	$result = $this->query($sql);

    	$rows = count($result);
    	
    	if ( $rows > 0 ) {

    		while ( $row = $result->fetchArray(SQLITE3_ASSOC) ) {

	    		echo ($row['role']);
    	}

    	} else {

    		return false;
    	}    	  	

    }



    /**
	* List users
	* @param $hotel_id
	* @return string or bool
	*/
	public function listUsers( $hotel_id ) {

    	// Select organisations all users
    	$sql = "SELECT * FROM users WHERE hotel_id = '$hotel_id'";

    	$result = $this->query($sql);

    	$rows = count($result);

    	if ( $rows > 0 ) {

    		// Echoes start of <ul> output
    		echo "<h1 style='text-align:center;'>Colleagues.</h1><ul class='user-list'>";

    		while ($row = $result->fetchArray(SQLITE3_ASSOC)) {

    			// Loop result-set and echo with markup, different depending on role
    			$deletebutton = "";
    		
	    		$id = $row['user_id'];
	    		$id = (int)$id;
	    		$user_mail = $row['mail'];
	    		$firstname = ucfirst($row['firstname']);
	    		$lastname = ucfirst($row['lastname']);
	    		$phone = $row['phone'];
	    		if (strlen($row['image']) > 0) { $image = $row['image']; } else { $image = "whistles"; }
	    		$role = $row['role'];

	    		if ($this->isAdmin($_SESSION['username'])) { $deletebutton = "<div><p><i class='fa fa-minus remove-user-button' aria-hidden='true' onclick='editUserPopup($id)'></i></p></div>"; }
	    		
	    		$output = "<li>";
	    		$output .= "<div><img src='images/$image.png' alt='#' /></div>";
	    		$output .= "<div>";
	    		$output .= "<div><p><strong>$firstname $lastname</strong> <span>($role)</span></p></div>";
	    		$output .= "<div><i class='fa fa-envelope' aria-hidden='true'></i> <a href='mailto:$user_mail'>$user_mail</a></div>";
	    		$output .= "<div><p><i class='fa fa-mobile' aria-hidden='true'></i> $phone</p></div>";
	    		if ($role != "owner") { $output .= $deletebutton; }
	    		$output .= "</div>";
	    		$output .= "</li>";

	    		echo $output;

	    	}

	    	echo "</ul>";
    	
    	} else {

    		return false;
    	}    	  	

    }



    /**
	* Validates admin or owner role
	* @param string $user
	* @return bool
	*/
	public function isAdmin( $user ) {

    	// Set values
    	$this->setUser($user);

    	$sql = "SELECT role FROM users WHERE mail = '$this->user'";

    	$result = $this->query($sql);

    	$rows = count($result);

    	$row = $result->fetchArray(SQLITE3_ASSOC);
    	
    	$role = $row['role'];
    	
    	// If user is administartor or owner -> true
    	if ( $role === 'administrator' || $role === 'owner' ) {

    		return true;
    	
    	} else {

    		return false;
    	}    	  	

    }



    /**
	* Validates owner role
	* @param string $user
	* @return bool
	*/
	public function isOwner( $user ) {

    	// Set values
    	$this->setUser($user);

    	$sql = "SELECT role FROM users WHERE mail = '$this->user'";

    	$result = $this->query($sql);

    	$rows = count($result);

    	$row = $result->fetchArray(SQLITE3_ASSOC);
    	
    	$role = $row['role'];
    	
    	// If user is owner -> true
    	if ( $role === 'owner' ) {

    		return true;
    	
    	} else {

    		return false;
    	}    	  	

    }



    /**
	* Check if user(mail) exists
	* @param int $user
	* @return bool
	*/
	public function userExists( $user ) {

    	$sql = "SELECT count(*) AS 'needle' FROM users WHERE mail = '$user'";

    	$result = $this->query($sql);

    	$result = $result->fetchArray(SQLITE3_ASSOC);

    	$result = $result["needle"];

    	// True if any hits made on username
    	if ( $result > 0 ) {

    		return true;
    	
    	} else {

    		return false;
    	}    	  	

    }



    /**
	* Check if hotel_name exists
	* @param int $hotel_name
	* @return bool
	*/
	public function hotelExists( $hotel_name ) {

    	$sql = "SELECT count(*) AS 'needle' FROM hotels WHERE hotel_name = '$hotel_name'";

    	$result = $this->query($sql);

    	$result = $result->fetchArray(SQLITE3_ASSOC);

    	$result = $result["needle"];

    	// True if any hits made on hotelname
    	if ( $result > 0 ) {

    		return true;
    	
    	} else {

    		return false;
    	}    	  	

    }



    /**
	* Check if user(mail) and password exists ( validation for login )
	* @param int $user
	* @return bool
	*/
	public function validateLogin( $user, $password ) {

    	$sql = "SELECT count(*) AS 'needle' FROM users WHERE mail = '$user' AND password = '$password'";

    	$result = $this->query($sql);

    	$result = $result->fetchArray(SQLITE3_ASSOC);

    	$result = $result["needle"];

    	// If hits are made -> true
    	if ( $result > 0 ) {

    		return true;
    	
    	} else {

    		return false;
    	}    	  	

    }



    /**
	* Check if user(mail) exists and is inactive ( if not inactive reset password-page cant be reached )
	* @param int $user
	* @return bool
	*/
	public function validateSetPassword( $user ) {

    	$sql = "SELECT count(*) AS 'needle' FROM users WHERE mail = '$user' AND active = 0";

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
	* Gets "mood-image" and it's color scheme for user
	* @param string $user
	* @return bool
	*/
	public function getMoodToday( $user ) {

		// Set values
    	$this->setUser($user);

    	$sql = "SELECT image FROM users WHERE mail = '$this->user'";

    	$result = $this->query($sql);
    	
		$row = $result->fetchArray(SQLITE3_ASSOC);
    		
		$output = $row['image'];

		if ( $output ) {
			
			return $output;
    	
    	} else {

    		return false;
    	}    	  	

    }



    /**
	* Sets "mood-image" and it's color scheme for user
	* @param string $user
	* @param string $image
	* @return bool
	*/
	public function setMoodToday( $user, $image ) {

		// Prevent SQL-injections
    	$user = SQLite3::escapeString($user);
    	$image = SQLite3::escapeString($image);

    	// Set values
    	$this->setUser($user);

		// Execute query
		if ($this->exec("UPDATE users SET image = '$image' WHERE mail = '$this->user'")) 
		{ 
			return true;

		} else {

    		return false;
    	}   

	}



	/**
	* Delete user
	* @param string $user
	* @return bool
	*/
	public function deleteUser( $user_id ) {

    	$sql = "DELETE FROM users WHERE user_id = '$user_id'";
    	
    	if ( $result = $this->query($sql) ) {

    		return true;
    	
    	} else {

    		return false;
    	}    	  	

    }



    /**
	* Change role ( not possible in app yet, for future development )
	* @param string $user
	* @return bool
	*/
	public function updateRole( $user, $role ) {

    	// Set values
    	$this->setUser($user);
    	$this->setRole($role);

		// Execute query
		if ($this->exec("UPDATE users SET role = '$this->role' WHERE mail = '$this->user'")) 
		{ 
			return true;

		} else {

    		return false;
    	}   

	}



	/* GET and SET */

	/**
	* Get User
	* @return string
	*/
	public function getUser() {
		return $this->user;
	}

	/**
	* Get Password
	* @return string
	*/
	public function getPassword() {
		return $this->password;
	}

	/**
	* Get Hotel
	* @return string
	*/
	public function getHotel() {
		return $this->hotel;
	}

	/**
	* Get firstname
	* @return string
	*/
	public function getFirstname() {
		return $this->firstname;
	}

	/**
	* Get lastname
	* @return string
	*/
	public function getLastname() {
		return $this->lastname;
	}

	/**
	* Get phone
	* @return string
	*/
	public function getPhone() {
		return $this->phone;
	}

	/**
	* Get active
	* @return string
	*/
	public function getActive() {
		return $this->active;
	}

	/**
	* Get image
	* @return string
	*/
	public function getImage() {
		return $this->image;
	}

	/**
	* Get role
	* @return string
	*/
	public function getRole() {
		return $this->role;
	}

	/**
	* Get start_date
	* @return string
	*/
	public function getStartDate() {
		return $this->start_date;
	}

	/**
	* Get user_id
	* @return int
	*/
	public function getUser_id() {
		return $this->user_id;
	}

	/**
	* Set User
	* @param $user
	*/
	public function setUser($user) {
		if ($user != "") {
			$user = strtolower($user);
			return $this->user = $user;
		} else {
			return false;
		}
	}

	/**
	* Set Password
	* @param $password
	*/
	public function setPassword($password) {
		if ($password != "") {
			$password = strtolower($password);
			return $this->password = $password;
		} else {
			return false;
		}
	}

	/**
	* Set Hotel
	* @param $hotel
	*/
	public function setHotel($hotel) {
		if ($hotel != "") {
			$hotel = strtolower($hotel);
			return $this->hotel = $hotel;
		} else {
			return false;
		}
	}

	/**
	* Set firstname
	* @param $firstname
	*/
	public function setFirstname($firstname) {
		if ($firstname != "") {
			$firstname = strtolower($firstname);
			return $this->firstname = $firstname;
		} else {
			return false;
		}
	}

	/**
	* Set lastname
	* @param $lastname
	*/
	public function setLastname($lastname) {
		if ($lastname != "") {
			$lastname = strtolower($lastname);
			return $this->lastname = $lastname;
		} else {
			return false;
		}
	}

	/**
	* Set phone
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
	* Set active
	* @param $active
	*/
	public function setActive($active) {
		if ($active != "") {
			$active = strtolower($active);
			return $this->active = $active;
		} else {
			return false;
		}
	}

	/**
	* Set image
	* @param $image
	*/
	public function setImage($image) {
		if ($image != "") {
			$image = strtolower($image);
			return $this->image = $image;
		} else {
			return false;
		}
	}

	/**
	* Set role
	* @param $role
	*/
	public function setRole($role) {
		if ($role != "") {
			$role = strtolower($role);
			return $this->role = $role;
		} else {
			return false;
		}
	}

	/**
	* Set start_date
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

	/**
	* Set user_id
	* @param $user_id
	*/
	public function setUser_id($user_id) {
		if ($user_id != "") {
			return $this->user_id = $user_id;
		} else {
			return false;
		}
	}

}

?>