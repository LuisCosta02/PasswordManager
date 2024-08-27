<?php
	define("USER_BD",""); // PostgreSQL username
	define("PASS_BD",""); // PostgreSQL password
	define("NOME_BD",""); // PostgreSQL database name
	$hostname_conn = "localhost"; // Hostname (usually 'localhost' for local server)
	$port = "5432"; // Default PostgreSQL port

	// Connection string
	$conn_string = "host=$hostname_conn port=$port dbname=".NOME_BD." user=".USER_BD." password=".PASS_BD;

	// Conectamos ao nosso servidor PostgreSQL
	$conn = pg_connect($conn_string);

	
?>