<?php
	
	define("host","localhost");
	define("user", "root");
	define("password", "password");
	define("database", "goalifyoz");
	/*
	define("host","166.62.8.50");
	define("user", "settlein");
	define("password", "ThisIsThePasswordForTheDB1!");
	define("database", "settlein");
	*/
	function connect () {
		$conn = mysql_connect(constant("host"), constant("user"), constant("password")) or die (mysql_error());
	}
	
	function executeSql ($sql) {
		$connection = mysql_connect(constant("host"), constant("user"), constant("password")) or die (mysql_error());
		mysql_select_db(constant("database"), $connection);
		$result = mysql_query($sql, $connection) or die(mysql_error());

		mysql_close($connection);
		
		return $result;
	}
?>