<?php

$name = $_POST['name'];
$pass = $_POST['pass'];
$email = $_POST['email'];
session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = $name;
}
$passCrypt = sha1($pass);

//Conection to database
$conn = pg_connect("host=localhost dbname=SSL_ANALYZER user=alumnodb password=alumnodb") or die('No pudo conectarse: ' . pg_last_error());

// Realizar una consulta SQL 
$consulta = "SELECT * FROM users WHERE name = '" . $name . "';";
$resultado = pg_query($conn, $consulta) or die('Consulta fallida: ' . pg_last_error());
$linea = pg_fetch_row($resultado, null, PGSQL_ASSOC);

$consulta2 = "SELECT count(*) FROM users WHERE email = '" . $email . "';";
$resultado2 = pg_query($conn, $consulta2) or die('Consulta fallida: ' . pg_last_error());
$linea2 = pg_fetch_row($resultado2, null, PGSQL_ASSOC);

if (!strlen($linea['pass']) && !strlen($linea['email'])) {
    $consulta = "INSERT INTO users VALUES ('" . $name . "', '" . $email . "', '" . $passCrypt . "', '" . date("Y-m-d H:i:s") . "', '0');";
    $resultado = pg_query($conn, $consulta) or die('Consulta fallida: ' . pg_last_error());
    $_SESSION['username'] = $name;
    echo '<div class="correct_register"><h2>Registered successfully with the name: ' . $name . ' </h2><br>Redirecting...</div>';
    exit();
} else {
    //Check if the pass is hte same
    if ($linea2['count'] > 0)
        echo 'Error: the email "' . $email . '" already exists';
    else
        echo 'Error: the user "' . $name . '" already exists';
}
?>