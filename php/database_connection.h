<?php
	define("USER_BD","postgres"); // PostgreSQL username
	define("PASS_BD","Naonaosei21."); // PostgreSQL password
	define("NOME_BD","PasswordManager"); // PostgreSQL database name
	$hostname_conn = "localhost"; // Hostname (usually 'localhost' for local server)
	$port = "5432"; // Default PostgreSQL port

	// Connection string
	$conn_string = "host=$hostname_conn port=$port dbname=".NOME_BD." user=".USER_BD." password=".PASS_BD;

	// Conectamos ao nosso servidor PostgreSQL
	$conn = pg_connect($conn_string);

	
?>