<?php

$name = $_POST['name'];
$pass = $_POST['pass'];
session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = $name;
}
//Conection to database
$conn = pg_connect("host=localhost dbname=SSL_ANALYZER user=alumnodb password=alumnodb") or die('No pudo conectarse: ' . pg_last_error());

// Obtain the pass if exist
$consulta = "SELECT pass FROM users WHERE name = '" . $name . "';";
$resultado = pg_query($conn, $consulta) or die('Failed: ' . pg_last_error());
$linea = pg_fetch_row($resultado, null, PGSQL_ASSOC);

//Case if the user doesnt exist
if (!strlen($linea['pass'])) {
    echo 'Error: The user ' . $name . ' doesnt exist';
}
//Case if the user exist
else {
    //Check if the pass is the same
    $passCrypt = sha1($pass);
    //Subcase if the pass doesnt match
    if ($linea['pass'] != $passCrypt) {
        echo 'Error: The pass doesnt match';
    }
    //Subcase if the pass is correct
    else {
        echo '<div class="correct_register"><h2>Sign successfully with the name: ' . $name . ' </h2><br>Redirecting...</div>';
    }
}
?>