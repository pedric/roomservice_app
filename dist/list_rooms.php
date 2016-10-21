<?php include('includes/config.php'); ?>
<?php include('includes/head.php'); ?>
<?php include('includes/header.php'); ?>

<!-- Primary Page Layout
–––––––––––––––––––––––––––––––––––––––––––––––––– -->
<div class="container">

<?php 

	$room = new Room();

	$room->listRooms();

	$role = new User();

	$user = $_SESSION["username"];

	// Echo admin-button
	if ( $role->isAdmin( $user ) ) {
		
		$add_user_button = "";
		$add_user_button .= "<div class='admin-add-button' onclick='addRoomPopup()'>";
		$add_user_button .= "<p>";
		$add_user_button .= "<i class='fa fa-plus' aria-hidden='true'></i>";
		$add_user_button .= "</p>";
		$add_user_button .= "</div>";

		echo $add_user_button;

	}

?>

</div><!-- !end.Primary Page Layout -->

<?php include('includes/footer.php'); ?>