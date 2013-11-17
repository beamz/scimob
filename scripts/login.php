<?php
	session_start();
	include("db/connect.inc.php");
	
	$username = "";
	$password = "";
	
	// Checking for all parameters
	if (!isset($_POST['username']) || !isset($_POST['password'])) {
		$_SESSION['username'] = (isset($_SESSION['username'])) ? $_SESSION['username'] : $username;
		$_SESSION['error'] = "Please fill out all fields";
		header("Location: ../");
		exit();
	}
	
	// Pulling all variables
	$username = mysql_real_escape_string($_POST['username']);
	$password = mysql_real_escape_string($_POST['password']);
	$_SESSION['username'] = $username;
	
	// Getting the system salt
	$SYSTEM_SALT = "";
	$salt_query = mysql_query("SELECT salt FROM passwd_system WHERE id=1");
	$SYSTEM_SALT = mysql_result($salt_query, 0);
	
	// Grabbing rows
	$result = mysql_query("SELECT * FROM passwd_users WHERE username=AES_ENCRYPT('".$username."', '".$SYSTEM_SALT."') AND passwd=AES_ENCRYPT('".$password."', '".$SYSTEM_SALT."');");
	$nums = mysql_num_rows($result);

	if ($nums < 1) {
		$_SESSION['error'] = "Incorrect username and password combination..";
		header("Location: ../");
		exit();
	}

	while ($row = mysql_fetch_array($result)) {
		$id = $row['id'];
		$username = $row['username'];
		$phone = $row['phone'];
	}

	$_SESSION['id'] = $id;
	$_SESSION['username'] = $username;
	$_SESSION['phone'] = $phone;
	$_SESSION['expiretime'] = time() + 600;

	unset($_SESSION['error']);
	header("Location: ../");
?>