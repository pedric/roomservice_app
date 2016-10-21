<style>
	
	#edit-alternatives {
		position: fixed;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		z-index: 999;
		background: #fff;
		opacity: 0;
	}

	button {
		margin: 0 auto;
    	display: block;
	}

	#close-button-wrapper {
		height: 60px;
	}

	#close-button-wrapper img {
		height: 30px;
		width: 30px;
	}

	.red {
		color: #dc4c4c;
	}

	.button.button-primary, button.button-primary, input[type="submit"].button-primary, input[type="reset"].button-primary, input[type="button"].button-primary {
    color: #FFF;
    background-color: #dc4c4c;
    border-color: #dc4c4c;
}


</style>

<?php

	require_once('classes/user.class.php');

	$user_id = $_GET['user_id'];

?>

<div id="edit-alternatives">
	<div class="container">
		<div id="close-button-wrapper"><img src="images/close.png" onclick="closeThis()" alt="Close button" /></div>
		<h2 class="red">Dangerzone!</h2>
		<p>If you delete a user the account can't be reset. Are you sure?</p>
		<form action="#">
			<input type="hidden" name="userId" value="<?= $user_id; ?>">
			<input id="submitDeleteUser" class="u-full-width button-primary red_bg" name="submitDeleteUser" type="submit" value="delete">
		</form>
	</div><!-- !end.Primary Page Layout -->
</div>