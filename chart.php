<?php

session_start();

include 'functions.php';

$exploit = $_POST['name'];
$result = array();

if (isset($_SESSION['username'])) {

    //Conection to database
    $conn = pg_connect("host=localhost dbname=SSL_ANALYZER user=alumnodb password=alumnodb") or die('No pudo conectarse: ' . pg_last_error());

    //Check if the name exist
    $consulta = "SELECT * FROM exploit WHERE status = '1';";
    $resultado = pg_query($conn, $consulta) or die('Consulta fallida: ' . pg_last_error());

    while ($row = pg_fetch_row($resultado, null, PGSQL_ASSOC)) {
        if (strnatcasecmp($exploit, $row['name']) == 0) {
            $result['des'] = $row['description'];
            $result['name'] = $row['name'];
        }
    }

    $time = mktime(0, 0, 0, date("m"), date("d") - 7, date("Y"));
    $prevWeek = date("Y-m-d H:i:s", $time);
    $time2 = mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"));
    $prevMonth = date("Y-m-d H:i:s", $time2);

    //Create the array
    if (strlen($result['des']) === 0) {
        $result['des'] = '';
        $result['name'] = str_replace('_', ' ', $exploit);
    }
    $result[0] = countVul($exploit, $prevWeek);
    $result[1] = countNotVul($exploit, $prevWeek);
    $result[2] = countVul($exploit, $prevMonth);
    $result[3] = countNotVul($exploit, $prevMonth);
    $result[4] = countVul($exploit, '');
    $result[5] = countNotVul($exploit, '');
    echo json_encode($result);
    //}
} else {
    echo 'Error: you must be logged';
}
?>