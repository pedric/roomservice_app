<?php

			require_once('includes/config.php');

			$hotel_id = $_SESSION['hotel_id'];
			$room_number = $_GET['room_number'];

			$obj = new Room();

		?>

<style>

	button {
		margin: 0 auto;
    	display: block;
	}

	.button.button-primary, button.button-primary, input[type="submit"].button-primary, input[type="reset"].button-primary, input[type="button"].button-primary {
	    color: #FFF;
	    background-color: #dc4c4c;
	    border-color: #dc4c4c;
	}

</style>

<div id="edit-alternatives">
	<div class="container">
		<div id="close-button-wrapper"><img src="images/close.png" onclick="closeThis()" alt="Close button" /></div>

		<?php $obj->listRoomInfo( $hotel_id, $room_number ); ?>

		<h4 class="red" style="text-align: center;">Delete room.</h4>
		<p>If you delete a room it can't be reset. Are you sure?</p>
		<form action="#">
			<input type="hidden" name="room_number" value="<?= $room_number; ?>">
			<input id="submitDeleteRoom" class="u-full-width button-primary red-button" name="submitDeleteRoom" type="submit" value="delete">
		</form>
	</div>
</div>