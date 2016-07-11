
<?php

session_start();
$i = 0;
$result = array();

include 'functions.php';
if (isset($_SESSION['username'])) {

//Conection to database
    $conn = pg_connect("host=localhost dbname=SSL_ANALYZER user=alumnodb password=alumnodb") or die('No pudo conectarse: ' . pg_last_error());

    $time = mktime(0, 0, 0, date("m"), date("d") - 7, date("Y"));
    $prevWeek = date("Y-m-d H:i:s", $time);
    $time2 = mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"));
    $prevMonth = date("Y-m-d H:i:s", $time2);

    //Get the names of the exploits
    $consulta3 = "SELECT * FROM information_schema.columns WHERE table_name = 'statistics' and data_type = 'bit'";
    $resultado3 = pg_query($conn, $consulta3) or die('Error: SQL --> ' . pg_last_error());
    while ($row = pg_fetch_row($resultado3, null, PGSQL_ASSOC)) {
        $exploit = $row['column_name'];
        $result[$i]['NAME'] = str_replace('_', ' ', $exploit);
        $result[$i]['VALUE'] = countVul($exploit, $prevWeek);
        $result[$i]['NOTVALUE'] = countNotVul($exploit, $prevWeek);
        $result[$i]['VALUE2'] = countVul($exploit, $prevMonth);
        $result[$i]['NOTVALUE2'] = countNotVul($exploit, $prevMonth);
        $result[$i]['VALUE3'] = countVul($exploit, '');
        $result[$i]['NOTVALUE3'] = countNotVul($exploit, '');
        $i++;
    }
    echo json_encode($result);
} else {
    echo 'Error: you must be logged';
}