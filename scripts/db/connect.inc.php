<?php

	$host = "localhost";
	$username = "zillwcco_passwd";
	$password = "Tested101!";
	$db_name = "zillwcco_passwd";
	
	mysql_connect($host, $username, $password) or die("Cannot establish a connection to mysql!");
	mysql_select_db($db_name) or die("Cannot connect to database!");
?>