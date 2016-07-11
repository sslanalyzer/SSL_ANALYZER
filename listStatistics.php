<?php

//Conection to database
$conn = pg_connect("host=localhost dbname=SSL_ANALYZER user=alumnodb password=alumnodb") or die('No pudo conectarse: ' . pg_last_error());

//Check if the name exist
$consulta = "SELECT * FROM information_schema.columns WHERE table_name = 'statistics' and data_type = 'bit'";
$resultado = pg_query($conn, $consulta) or die('Error: SQL --> ' . pg_last_error());

if (pg_num_rows($resultado) > 0) {
    // output data of each row
    $i = 0;
    while ($row = pg_fetch_row($resultado, null, PGSQL_ASSOC)) {
        echo '<li id="li' . $i . '"><a href="#" onclick="infoStadistic(' . $i . ')">' . $row["column_name"] . '</a></li>';
        $i++;
    }
} else {
    echo "No data";
}
?>
