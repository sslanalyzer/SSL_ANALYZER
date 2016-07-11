<?php

session_start();
//Conection to database
$conn = pg_connect("host=localhost dbname=SSL_ANALYZER user=alumnodb password=alumnodb") or die('No pudo conectarse: ' . pg_last_error());

//Check if have some exploits
$consulta = "SELECT * FROM exploit WHERE status = '0';";
$resultado = pg_query($conn, $consulta) or die('Error: SQL --> ' . pg_last_error());

if (pg_num_rows($resultado) > 0) {
    // output data of each row
    echo '<div id="tab1" class="form-content tab1 active">';
    echo '<h2 class="title" style="text-align: center; padding-bottom: 40px;
">Update exploits in database</h2><h3 class="error_title">Error</h3>';
    echo '<div class="wrap2"><form id="form1" action="" class="form" name="form_register" method="post">
      <div>';

    while ($row = pg_fetch_row($resultado, null, PGSQL_ASSOC)) {
        echo '      <div class="input-group checkbox">
      <input type="checkbox" name="' . $row['name'] . '" id="' . $row['name'] . '" value="true">
      <label for="' . $row['name'] . '">' . $row['name'] . '</label> <div class="more-info"><a href="#" onclick="infoExploit(\'' . $row['name'] . '\')">more info about: ' . $row['name'] . '</a></div>
      </div>';
    }

    echo '<div class="input-group"><input type="submit" id="btn-submit" value="Update" onclick="updateExploit();
      return false;">
      </div></div>
      </form></div></div>';
} else {
    echo "No data";
}
?>