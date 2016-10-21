<?php

	// Called when item is dropped in dirtify and to do list. Changes status for room.
	require_once("includes/config.php");

	$hotel_id = $_GET["hotel"];
	$room_number = $_GET["room_number"];
	$status = $_GET["status"];
	$user_id = $_SESSION["user_id"];

	$obj = new Room();

	$obj->updateToDoList( $room_number, $status, $hotel_id, $user_id );

?>