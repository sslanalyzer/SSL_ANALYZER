<?php

session_start();

$name = $_POST['name'];

echo $name;
//Conection to database
$conn = pg_connect("host=localhost dbname=SSL_ANALYZER user=alumnodb password=alumnodb") or die('No pudo conectarse: ' . pg_last_error());

//Check if have some exploits
$consulta = "UPDATE exploit SET status = '1' WHERE name = '" . $name . "';";
$resultado = pg_query($conn, $consulta) or die('Error: SQL --> ' . pg_last_error());
?>