<?php

session_start();

include 'functions.php';

$name = $_POST['name'];

if (isset($_SESSION['username'])) {

    //Conection to database
    $conn = pg_connect("host=localhost dbname=SSL_ANALYZER user=alumnodb password=alumnodb") or die('No pudo conectarse: ' . pg_last_error());

    //Check if the name exist
    $consulta = "SELECT * FROM exploit WHERE name = '" . $name . "';";
    $resultado = pg_query($conn, $consulta) or die('Consulta fallida: ' . pg_last_error());
    $linea = pg_fetch_row($resultado, null, PGSQL_ASSOC);

    if ($linea['name'] == '') {
        echo 'Error: Exploit ' . $name . ' doesnt exist';
        exit();
    } else {
        //Put the name of the exploit as title
        echo '<h2>' . $name . '</h2>';

        //Check if the exploit has description
        if ($linea['description'] != '') {

            echo '<br><h1>Description:</h1><br>';
            echo '<div class="description">' . $linea['description'] . '</div>';
        }
        echo '<br><h1>Stadistic:</h1><br>';

        //Get the names of the eploits
        $consulta = "SELECT * FROM exploit WHERE status = '1';";
        $resultado = pg_query($conn, $consulta) or die('Error: SQL --> ' . pg_last_error());
        while ($row = pg_fetch_row($resultado, null, PGSQL_ASSOC)) {
            if ($name == $row['name'])
                $exploit = strtolower($row['name']);
        }

        $time = mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"));
        $prevWeek = date("Y-m-d H:i:s", $time);
        $time2 = mktime(0, 0, 0, date("m"), date("d") - 7, date("Y"));
        $prevMonth = date("Y-m-d H:i:s", $time2);
        echo '<br> Last Week:<br><br>';
        echo '<div id="tab" class="wrap stadistics">
        <table class="stadistics">
            <tbody>
            <tr>
                <th class="left first">Number of ips vulnerable</th>
                <th class="right first">Number of ips not vulnerable</th>
                <th class="right first">Total of ips</th>
            </tr>
            <tr id="1">
                <th class="right second">' . countVul($exploit, $prevWeek) . ' (' . (countVul($exploit, $prevWeek) / countTot($exploit, $prevWeek)) * 100 . '%) <a>more info</a></th>
                <th class="right second">' . countNotVul($exploit, $prevWeek) . ' (' . (countNotVul($exploit, $prevWeek) / countTot($exploit, $prevWeek)) * 100 . '%) <a>more info</a></th>
                <th class="right second">' . countTot($exploit, $prevWeek) . '</th></tr>
        </table>
    </div>
    </div>
    </div>';
        echo '<br> Last Month:<br><br>';
        echo '<div id="tab" class="wrap stadistics">
        <table class="stadistics">
            <tbody>
            <tr>
                <th class="left first">Number of ips vulnerable</th>
                <th class="right first">Number of ips not vulnerable</th>
                <th class="right first">Total of ips</th>
            </tr>
            <tr id="1">
                <th class="right second">' . countVul($exploit, $prevMonth) . ' (' . (countVul($exploit, $prevMonth) / countTot($exploit, $prevMonth)) * 100 . '%) <a>more info</a></th>
                <th class="right second">' . countNotVul($exploit, $prevMonth) . ' (' . (countNotVul($exploit, $prevMonth) / countTot($exploit, $prevMonth)) * 100 . '%) <a>more info</a></th>
                <th class="right second">' . countTot($exploit, $prevMonth) . '</th></tr>
        </table>
    </div>
    </div>
    </div>';
        echo '<br> All:<br><br>';
        echo '<div id="tab" class="wrap stadistics">
        <table class="stadistics">
            <tbody>
            <tr>
                <th class="left first">Number of ips vulnerable</th>
                <th class="right first">Number of ips not vulnerable</th>
                <th class="right first">Total of ips</th>
            </tr>
            <tr id="1">
                <th class="right second">' . countVul($exploit, '') . ' (' . (countVul($exploit, '') / countTot($exploit, '')) * 100 . '%) <a>more info</a></th>
                <th class="right second">' . countNotVul($exploit, '') . ' (' . (countNotVul($exploit, '') / countTot($exploit, '')) * 100 . '%) <a>more info</a></th>
                <th class="right second">' . countTot($exploit, '') . '</th></tr>
        </table>
    </div>
    </div>
    </div>';
    }
} else {
    echo 'Error: you must be logged';
}
?>