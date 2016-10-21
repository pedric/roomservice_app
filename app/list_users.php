<?php include('includes/config.php'); ?>
<?php include('includes/head.php'); ?>
<?php include('includes/header.php'); ?>

<!-- Primary Page Layout
–––––––––––––––––––––––––––––––––––––––––––––––––– -->
<div class="container">

<?php 

	$profile = new User();

	$profile->listUsers($_SESSION['hotel_id']);

	$user = $_SESSION["username"];

	// Echo admin-button
	if ( $profile->isAdmin( $user ) ) {

		$add_user_button = "";
		$add_user_button .= "<div class='admin-add-button' onclick='addUserPopup()'>";
		$add_user_button .= "<p>";
		$add_user_button .= "<i class='fa fa-plus' aria-hidden='true'></i>";
		$add_user_button .= "</p>";
		$add_user_button .= "</div>";

		echo $add_user_button;

	}

?>

</div><!-- !end.Primary Page Layout -->

<?php include('includes/footer.php'); ?>