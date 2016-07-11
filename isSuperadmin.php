<?php

session_start();
//Conection to database
$conn = pg_connect("host=localhost dbname=SSL_ANALYZER user=alumnodb password=alumnodb") or die('No pudo conectarse: ' . pg_last_error());

//Check if the user is superadmin
$consulta = "SELECT * FROM users WHERE name = '" . $_SESSION['username'] . "';";
$resultado = pg_query($conn, $consulta) or die('Error: SQL --> ' . pg_last_error());
$linea = pg_fetch_row($resultado, null, PGSQL_ASSOC);

if ($linea['superadmin'] == '1') {
    // output data of each row
    echo '1';
} else {
    echo "0";
}
?>