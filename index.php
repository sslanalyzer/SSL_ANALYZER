<?php

//Obtain the ip o the address
$ip = $_POST['name'];
$flag = 0;
$linea = '';

//get the client ip
$ip_user = $_SERVER['REMOTE_ADDR'];

//Start session
session_start();

include 'functions.php';

//Conection to database
$conn = pg_connect("host=localhost dbname=SSL_ANALYZER user=alumnodb password=alumnodb") or die('No pudo conectarse: ' . pg_last_error());



// NEW -------------------
if (!isset($_SESSION['username'])) {
    //Check if the ip is on the DB
    // Realizar una consulta SQL 
    $consulta = "SELECT date FROM ip WHERE ip = '" . $ip_user . "';";
    $resultado = pg_query($conn, $consulta) or die('Consulta fallida: ' . pg_last_error());
    $linea = pg_fetch_row($resultado, null, PGSQL_ASSOC);



//check if the ip exists in the DB
    if ($linea != '') {
        $today = date("Y-m-d H:i:s");
        $dteStart = new DateTime($today);
        $dteEnd = new DateTime($linea['date']);
        $dteDiff = $dteStart->diff($dteEnd);

        $day = 1 - $dteDiff->format("%D");
        if ($day <= 0) {
            $consulta = "UPDATE ip SET date = '" . $today . "' WHERE ip = '" . $ip_user . "';";
            $resultado = pg_query($conn, $consulta) or die('Consulta fallida: ' . pg_last_error());
            searchIps($ip);
            exit();
        } else {
            $hour = 23 - $dteDiff->format("%H");
            $min = 59 - $dteDiff->format("%I");
            $sec = 60 - $dteDiff->format("%S");

            if ($hour < 0 || ($hour <= 0 && $min < 0) || ($hour <= 0 && $min <= 0 && $sec < 0)) {
                $consulta = "UPDATE ip SET date = ' . $today . ' WHERE ip = ' . $ip_user . ';";
                $resultado = pg_query($conn, $consulta) or die('Consulta fallida: ' . pg_last_error());
                //HACER BUSQUEDA
                searchIps($ip);
                exit();
            } else {
                echo 'Error, you must wait: ' . $hour . ' h ' . $min . ' min ' . $sec . ' sec<br>';
                exit();
            }
        }
    } else {
        $consulta = "INSERT INTO ip VALUES ('" . $ip_user . "', '" . date("Y-m-d H:i:s") . "')";
        $resultado = pg_query($conn, $consulta) or die('Consulta fallida: ' . pg_last_error());
        //HACER BUSQUEDA
        searchIps($ip);
        exit();
    }
} else {
    //HACER BUSQUEDA
    searchIps($ip);
    exit();
}

function searchIps($ip) {


    //Check if the ip or the host is localhost/127.0.0.1
    if ($ip == 'localhost' || $ip == '127.0.0.1') {
        echo 'Error, cant analyze: ' . $ip;
        exit();
    }

//Look if its ip
    if (preg_match('/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/', $ip)) {
        //echo 'ES IP';
        if (ping($ip) == false) {
            echo 'Error, dont connect to: ' . $ip;
            $flag = 1;
        } else {
            $domain = gethostbyaddr($ip);
            echo 'Sending ip ' . $domain;
        }
//IF non ip
    } else {
        $pos = strpos($ip, 'www.');
        //Check if the domain have 'www.'
        if (strpos($ip, 'www.') === false || $pos != 0) {

            //Add www. to get the ips
            $ip2 = 'www.' . $ip;
            $ipsv6 = dns_get_record($ip, DNS_AAAA);
            $ipsv4 = dns_get_record($ip, DNS_A);
            $ipsv6_2 = dns_get_record($ip2, DNS_AAAA);
            $ipsv4_2 = dns_get_record($ip2, DNS_A);

            $ipsv6 = array_merge($ipsv6, $ipsv6_2);
            $ipsv4 = checkIp($ipsv4_2, $ipsv4);

            $res4 = '';
            $res6 = '';
        } else {
            $ip2 = substr($ip, 4 - strlen($ip));
            $ipsv6 = dns_get_record($ip, DNS_AAAA);
            $ipsv4 = dns_get_record($ip, DNS_A);
            $ipsv6_2 = dns_get_record($ip2, DNS_AAAA);
            $ipsv4_2 = dns_get_record($ip2, DNS_A);

            $ipsv6 = array_merge($ipsv6, $ipsv6_2);
            $ipsv4 = checkIp($ipsv4_2, $ipsv4);

            $res4 = '';
            $res6 = '';
        }

        if (count($ipsv6) == 0 && count($ipsv4) == 0) {
            echo 'Error, dont connect to: ' . $ip;
            $flag = 1;
        } else {
            if ($flag == 0) {
                if ((count($res6) + count($res4)) >= 2) {
                    //When have more than one option
                    echo'
    <h2 class="title_two">Select IP to analyze:</h2>

 <div id="tab_ip" class="form-content">
    <div id="tab" class="wrap address">
        <table class="ip_tabs">
            <tbody>
            <tr>
                <th class="left first">SERVER</th>
                <th class="right first">DOMAIN</th>
            </tr>';
                    for ($i = 0; $i < $ct = count($ipsv4); $i++) {//$ipsv4[$i]['ip'];
                        echo '<tr id="' . $i . '"><th class="left second"><a id="' . $i . '" href="#" onclick="allInfo(this.id); return false;">' . $ipsv4[$i]['ip'] . ' (ipv4)</a></th>';
                        echo '<th class="right second">' . $ipsv4[$i]['host'] . '</th></tr>';
                    }
                    for ($j = 0; $j < $ct = count($ipsv6); $j++) {//$ipsv6[$i]['ip'];
                        echo '<tr id="' . ($i + $j) . '"><th class="left second"><a id="' . ($i + $j) . '"  href="#" onclick="allInfo(this.id); return false;">' . $ipsv6[$j]['ipv6'] . ' (ipv6)</a></th>';
                        echo '<th class="right second">' . $ipsv6[$j]['host'] . '</th></tr>';
                    }
                    echo '</tbody>
        </table>
    </div>
    <div id="map" class="wrap2" style="display: none;">
    </div>
    </div>';
                } else {
                    echo '<pre>';
                    echo print_r($res4);
                    echo print_r($res6);
                    echo '</pre>';
                }
            }
        }
    }
}

function ping($host) {
    $fp = fsockopen($host, 80, $errno, $errstr);
    if (!$fp) {
        echo "ERROR: $errno - $errstr<br />\n";
        return false;
    } else {
        return true;
    }
}
?>

