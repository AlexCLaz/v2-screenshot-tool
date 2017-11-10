<?php	
	$server = 'localhost';
	$username = 'root';
	$password = '';
	$database = 'screenshots';

	$main_con = mysql_connect($server, $username, $password) or die('Could not establish a connection to the database.</br>');
	mysql_select_db($database) or die('Could not establish a connection to the database.</br>');
?>