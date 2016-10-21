<?php include('includes/config.php'); ?>
<?php include('includes/head.php'); ?>
<?php include('includes/header.php'); ?>

<!-- Primary Page Layout
–––––––––––––––––––––––––––––––––––––––––––––––––– -->
<div class="container">

<?php 

if (isset($_REQUEST['userProfile'])) {

	$user = $_REQUEST['userProfile'];

	$profile = new User($_SESSION['hotel']);

	$profile->showUserProfile( $user );
}

?>

</div><!-- !end.Primary Page Layout -->

<?php include('includes/footer.php'); ?>