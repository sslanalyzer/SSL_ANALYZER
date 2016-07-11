<?php

session_start();
$name = $_SESSION['username'];

$passOld = $_POST['passOld'];
$pass = $_POST['pass'];
$pass2 = $_POST['pass2'];
$passCrypt = sha1($passOld);

if ($name === '') {
    echo 'Error: the user is empty';
    exit();
}

if ($passOld === $pass) {
    echo 'Error: the new pass and the old is the same';
    exit();
}

if ($pass !== $pass2) {
    echo "Error: the confirmation pass is wrong";
    exit();
}

$conn = pg_connect("host=localhost dbname=SSL_ANALYZER user=alumnodb password=alumnodb") or die('No pudo conectarse: ' . pg_last_error());

//Check if the old pass is alright
$consulta = "SELECT * FROM users WHERE name = '" . $name . "';";
$resultado = pg_query($conn, $consulta) or die('Error: SQL --> ' . pg_last_error());
$linea = pg_fetch_row($resultado, null, PGSQL_ASSOC);
if ($linea['pass'] !== $passCrypt) {
    echo 'Error: the old password is wrong';
    exit();
}
$passCrypt2 = sha1($pass);
//Change the pawssword
$consulta = "UPDATE users SET pass = '" . $passCrypt2 . "' WHERE name = '" . $name . "';";
pg_query($conn, $consulta) or die('Error: SQL --> ' . pg_last_error());
?>