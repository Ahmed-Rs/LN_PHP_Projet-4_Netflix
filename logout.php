<?php
	
	session_start();
	session_unset();
	session_destroy();
	setcookie('auth', '', time()-1, '/', null, false, true); // DETRUIT LE COOKIE

	header('location: index.php');
	exit();
?>