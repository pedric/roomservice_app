
<?php

// Redirect if NOT logged in
if (!isset($_SESSION['loggedin'])) { 
	
	if (headers_sent()) { 
		die('<script>window.location.href = "index.php";</script>');
	} else {
		header("Location: index.php");
		die();
	}
}

?>

<?php

// Set icon and color scheme ( "whistles color" default as last else option )
$mood = "";

$user = $_SESSION['username'];

$get_mood = new User();

$mood = $get_mood->getMoodToday( $user );

if ( $mood == "sad" ) {
	$color = "#58b450";
} else if ( $mood == "whistles" ) {
	$color = "#1eaedb";
} else if ( $mood == "happy" ) {
	$color = "#d25c96";
} else {
	$color = "#1eaedb";
}

// Overwrite CSS if "mood image" is chosen
if ( $color ) { 

?>

<style>
	
	header { background-color: <?= $color; ?>; }

	a { color: <?= $color; ?>; }

	.hello { color: <?= $color; ?>; }

	.admin-add-button { background-color: <?= $color; ?>; }

	ul.room-list li > div:first-child { background-color: <?= $color; ?>; }

	.button.button-primary, button.button-primary, input[type="submit"].button-primary, input[type="reset"].button-primary, input[type="button"].button-primary {
	    color: #FFF;
	    background-color: <?= $color; ?>;
	    border-color: <?= $color; ?>;
	}

</style>

<?php } ?>
<!-- Draggable menu -->
<header class="draggable">
	
	<div class="row">
		<div class="ten columns offset-by-one column">
			<a href="includes/logout.php" style="color: #fff;">Logout</a>
			<a href="about.php" style="color: #fff; float: right;">About</a>
		</div>
	</div>

	<div id="menu-anchor">
		<!-- Main menu -->
		<div class="row">
			<nav id="main-menu">
				<ul>
					<li><a href="edit_hotel.php">Hotel info</a></li>
					<li><a href="profile.php">Profile</a></li>
					<li><a href="list_users.php">Colleagues</a></li>
					<li><a href="list_rooms.php">Rooms</a></li>
					<li><a href="dirtify.php">Dirtify</a></li>
					<li><a href="to_do.php">To do</a></li>
				</ul>
			</nav>
		</div>

		<!-- Time and date -->
		<div class="row">
			<p id="clock" style="margin: 0;text-align: center;color: #fff;font-size: 4.4rem;font-family: 'Courier new', 'Courier'; line-height: 1;"></p>
		</div>
		<div class="row">
			<p id="date" style="margin: 0;text-align: center;color: #fff;font-size: 1.9rem;font-family: 'Courier new', 'Courier';"></p>
		</div>
	</div>
	<div id="dragdown-container">
		<div>
			<i id="arrow" class="fa fa-angle-down" aria-hidden="true"></i>
		</div>
	</div>
</header>