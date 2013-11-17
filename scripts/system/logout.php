<?php
	session_start();

	$error = "";

	// Collect any errors
	if (isset($_SESSION['error'])) {
		$error = $_SESSION['error'];
	}

	// Destroy session
	session_destroy();

	// Save the error and redirect
	$_SESSION['error'] = $error;
	header("Location: ../../");
?>