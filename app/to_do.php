<?php include('includes/config.php'); ?>
<?php include('includes/head.php'); ?>
<?php include('includes/header.php'); ?>

<!-- Primary Page Layout
–––––––––––––––––––––––––––––––––––––––––––––––––– -->
<div class="container">

<?php

	$list = new Room();

	$list->toDoList($_SESSION['hotel_id']);

?>

</div><!-- !end.Primary Page Layout -->

<?php include('includes/footer.php'); ?>