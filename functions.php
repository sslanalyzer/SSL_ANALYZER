<?php

function countVul($exploit, $date) {

//Conection to database
    $conn = pg_connect("host=localhost dbname=SSL_ANALYZER user=alumnodb password=alumnodb") or die('No pudo conectarse: ' . pg_last_error());

    if ($date != '')
        $consulta = "SELECT count(*) FROM statistics WHERE " . $exploit . " = '1' and created >= '" . $date . "';";
    else
        $consulta = "SELECT count(*) FROM statistics WHERE " . $exploit . " = '1';";
    $resultado = pg_query($conn, $consulta) or die('Consulta fallida2: ' . pg_last_error());
    $linea = pg_fetch_row($resultado, null, PGSQL_ASSOC);
    return $linea['count'];
}

function countNotVul($exploit, $date) {

//Conection to database
    $conn = pg_connect("host=localhost dbname=SSL_ANALYZER user=alumnodb password=alumnodb") or die('No pudo conectarse: ' . pg_last_error());

    if ($date != '')
        $consulta = "SELECT count(*) FROM statistics WHERE " . $exploit . " = '0' and created >= '" . $date . "';";
    else
        $consulta = "SELECT count(*) FROM statistics WHERE " . $exploit . " = '0';";
    $resultado = pg_query($conn, $consulta) or die('Consulta fallida3: ' . pg_last_error());
    $linea = pg_fetch_row($resultado, null, PGSQL_ASSOC);

    return $linea['count'];
}

function countTot($exploit, $date) {

//Conection to database
    $conn = pg_connect("host=localhost dbname=SSL_ANALYZER user=alumnodb password=alumnodb") or die('No pudo conectarse: ' . pg_last_error());

    if ($date != '')
        $consulta = "SELECT count(*) FROM statistics WHERE created >= '" . $date . "';";
    else
        $consulta = "SELECT count(*) FROM statistics WHERE " . $exploit . " = '0' OR " . $exploit . " = '1';";
    $resultado = pg_query($conn, $consulta) or die('Consulta fallida1: ' . pg_last_error());
    $linea = pg_fetch_row($resultado, null, PGSQL_ASSOC);

    return $linea['count'];
}

function bcdechex($dec) {
    $last = bcmod($dec, 16);
    $remain = bcdiv(bcsub($dec, $last), 16);
    if ($remain == 0) {
        return dechex($last);
    } else {
        return bcdechex($remain) . dechex($last);
    }
}

function getSubject($ip, $index) {
    $subject = exec('openssl x509 -noout -text -in ./ssl/pem/' . $ip . '_' . $index . '.pem | grep "Subject:" | uniq');
    $subject = explode('CN=', $subject);
    return $subject[1];
}

function getFingerprint($ip, $index) {
    $fingerprint = explode('=', exec('openssl x509 -noout -in ./ssl/pem/' . $ip . '_' . $index . '.pem -fingerprint'));
    $fingerprint = str_replace(":", '', $fingerprint[1]);
    $fingerprint = strtolower($fingerprint);
    return $fingerprint;
}

function getPin($ip, $index) {
    $pin = exec('openssl x509 -noout -in ./ssl/pem/' . $ip . '_' . $index . '.pem -pubkey | \
       openssl asn1parse -noout -inform pem -out ./ssl/key/' . $ip . '_' . $index . '.key
   openssl dgst -sha256 -binary ./ssl/key/' . $ip . '_' . $index . '.key | openssl enc -base64');
    return $pin;
}

function getValidFrom($ip, $index) {
    $validFrom = exec('openssl x509 -noout -text -in ./ssl/pem/' . $ip . '_' . $index . '.pem | grep "Not Before:" | uniq');
    $validFrom = explode('Not Before:', $validFrom);
    return $validFrom[1];
}

function getValidTo($ip, $index) {
    $validTo = exec('openssl x509 -noout -text -in ./ssl/pem/' . $ip . '_' . $index . '.pem | grep "Not After :" | uniq');
    $validTo = explode('Not After :', $validTo);
    return $validTo[1];
}

function getKey($ip, $index) {
    $file_pem = file_get_contents("./ssl/pem/" . $ip . "_" . $index . ".pem");
    $pub_key = openssl_pkey_get_public($file_pem);
    if ($pub_key == FALSE) {
        echo '<br>ERRORRRR<br>';
    }
    $keyData = openssl_pkey_get_details($pub_key);

    $key['tam'] = $keyData['bits'];
    $key['key'] = $keyData['key'];

//RSA KEY
    if ($keyData['type'] == 0) {
        $key['type'] = 'RSA';
    }//DSA KEY
    elseif ($keyData['type'] == 1) {
        $key['type'] = 'DSA';
    }//DH KEY
    elseif ($keyData['type'] == 2) {
        $key['type'] = 'DH';
    }//EC KEY
    elseif ($keyData['type'] == 3) {
        $key['type'] = 'EC';
    }
    file_put_contents('./ssl/key/pubkey_' . $ip . '_' . $index . '.txt', $key['key']);

    return $key;
}

function getExponent($ip, $index) {
    $exponent = exec('openssl x509 -noout -text -in ./ssl/pem/' . $ip . '_0.pem | grep "Exponent" | uniq');
    $exponent = str_replace("Exponent:", '', $exponent);
    $exponent = explode('(', $exponent);
    return $exponent[0];
}

function getIssuer($ip, $index) {
    $issuer = exec('openssl x509 -noout -text -in ./ssl/pem/' . $ip . '_' . $index . '.pem | grep "Issuer:" | uniq');
    $issuer1 = explode('CN=', $issuer);
    if ($issuer1[1] == '') {
        $o = explode('O=', $issuer1[0]);
        $ou = explode(', OU=', $o[1]);
        return $ou[0] . '/ ' . $ou[1];
    }
    return $issuer1[1];
}

function getSignature($ip, $index) {
    $signature = exec('openssl x509 -noout -text -in ./ssl/pem/' . $ip . '_' . $index . '.pem | grep "Signature Algorithm:" | uniq');
    $signature = explode('Signature Algorithm:', $signature);
    $signature = str_replace("Encryption", '', $signature[1]);
    return $signature;
}

function getRevocation($ip, $url) {

    if ($url == '')
        return "No-None";

    $today = time();
    $check = strtotime(getValidTo($ip, 0));

    if ($today > $check) {
        return 'No-None';
    }
//createFile("./ssl/der/" . $ip . ".der");
//createFile("./ssl/der/" . $ip . ".pem");
//createFile("./ssl/pem/" . $ip . "_crl_chain.pem");
    exec("wget -O ./ssl/der/" . $ip . ".der " . $url);
    exec("openssl crl -inform DER -in ./ssl/der/" . $ip . ".der -outform PEM -out ./ssl/der/" . $ip . ".pem");
    //Check if exist the file with more than one certificate
    if (file_exists("./ssl/pem/" . $ip . "_chain.txt"))
        exec("cat ./ssl/der/" . $ip . ".pem ./ssl/pem/" . $ip . "_chain.txt > ./ssl/pem/" . $ip . "_crl_chain.pem");
    else
        exec("cat ./ssl/der/" . $ip . ".pem ./ssl/pem/" . $ip . "_0.pem > ./ssl/pem/" . $ip . "_crl_chain.pem");
    $prueba = exec('openssl verify -crl_check -CAfile ./ssl/pem/' . $ip . '_crl_chain.pem ./ssl/pem/' . $ip . '_0.pem');
    $prueba = str_replace('./ssl/pem/' . $ip . '_0.pem', " ", $prueba);
    if (strpos($prueba, ' OK') > 0) {
        return 'Yes';
    } else {
        if (strpos($prueba, ' revoked') >= 0) {
            return 'No-revoked';
        }
        return 'No';
    }
}

function isTrust($date) {
    $today = date("Y-m-d H:i:s");
    $dteStart = new DateTime($today);
    $dteEnd = new DateTime($linea['date']);
    $dteDiff = $dteStart->diff($dteEnd);

    echo '<br>' . $dateStart . '<br>' . $dteEnd . '<br>' . $dteDiff;
}

function createFile($file) {
    $fp = fopen($file, 'w ');
    fclose($fp);
    chmod($file, 0777);
}

function getCiphers($ip, $level1, $level2) {
    $ciphers = exec("openssl ciphers 'ALL:eNULL:!" . $level1 . ":!" . $level2 . "'");
    $ciphers = explode(":", $ciphers);
    $cipherAllow = array();

    for ($i = 0; $i < count($ciphers); $i++) {
        $support = exec("echo -n | openssl s_client -connect " . $ip . ":443 -cipher " . $ciphers[$i] . ' 2>&1');
        usleep(50);
        if ($support === 'DONE')
            $ciphersAllow[] = $ciphers[$i];
    }
    return $ciphersAllow;
}

function getInfoCorrect($info) {
    $info = explode(" ", $info);
    $pos = 0;
    $infoRight = array();
    foreach ($info as $i => $value) {
        if ($info[$i] == '')
            unset($info[$i]);
        else {
            switch ($pos) {
                case '0':
                    $infoRigh['name'] = $info[$i];
                    $pos++;
                    break;
                case '1':
                    $infoRigh['protocol'] = $info[$i];
                    $pos++;
                    break;
                case '2':
                    $info[$i] = str_replace("Kx=", "", $info[$i]);
                    $infoRigh['key'] = $info[$i];
                    $pos++;
                    break;
                case '3':
                    $info[$i] = str_replace("Au=", "", $info[$i]);
                    $infoRigh['authentication'] = $info[$i];
                    $pos++;
                    break;
                case '4':
                    $info[$i] = str_replace("Enc=", "", $info[$i]);
                    $aux = explode("(", $info[$i]);
                    $infoRigh['enc'] = $aux[0];
                    $infoRigh['bits'] = str_replace(")", "", $aux[1]);
                    $pos++;
                    break;
                case '5':
                    $info[$i] = str_replace("Mac=", "", $info[$i]);
                    $infoRigh['mac'] = $info[$i];
                    $pos++;
                    break;
            }
        }
    }
    return $infoRigh;
}

function isCorrect($content) {

    if ('' == $content) {
        return 'UNKNOWN';
    }

    if (stristr($content, 'New, (NONE), Cipher is (NONE)') != '') {
        return 'NO';
    } else {
        return 'OK';
    }
}

function checkExploit($file) {

    if ($file === '') {
        return "No file";
    }
    $content = file_get_contents($file);

    if ($content == '') {
        return "No content";
    }

    if (stristr($content, 'No') != '') {
        return 'No';
    } else if (stristr($content, 'Yes') != '') {
        return 'Yes';
    } else {
        return 'Unknown';
    }
}

function secureRenegotation($ip) {

    if ($ip === '') {
        return 'Unknown';
    }
    createFile("./exploit/txt/secure_" . $ip . ".txt");
    exec("openssl s_client -connect " . $ip . ":443 > ./exploit/txt/secure_" . $ip . ".txt");
    $content = file_get_contents("./exploit/txt/secure_" . $ip . ".txt");
    if ($content === '')
        return 'Unknown';
    if (stristr($content, 'Secure Renegotiation IS supported') != '')
        return 'Yes';
    else
        return 'No';
}

function saveResult($ip, $name, $res) {

    if ($ip == '' || $name == '') {
        echo 'ERROR';
        exit();
    }

    if ($res === 'Yes' || $res === 'Yes-new')
        $res = '1';
    else
        $res = '0';

    //Connect to the database
    $conn = pg_connect("host=localhost dbname=SSL_ANALYZER user=alumnodb password=alumnodb") or die('No pudo conectarse: ' . pg_last_error());

    //Check if the name exist
    $consulta = "SELECT count(*) FROM statistics WHERE ip = '" . $ip . "';";
    $resultado = pg_query($conn, $consulta) or die('Consulta fallida: ' . pg_last_error());
    $linea = pg_fetch_row($resultado, null, PGSQL_ASSOC);

    $consulta2 = "SELECT count(*) FROM exploit WHERE status = '1';";
    $resultado2 = pg_query($conn, $consulta2) or die('Consulta fallida: ' . pg_last_error());
    $linea2 = pg_fetch_row($resultado2, null, PGSQL_ASSOC);

    //check if the ip exist
    if ($linea['count'] === '0') {
        //The ip doesnt exist
        $insert = "INSERT INTO statistics VALUES ('" . $ip . "', '" . date("Y-m-d H:i:s") . "', ";
        for ($i = 1; $i < ($linea2['count'] + 8); $i++) {
            $insert .= "'0', ";
        }
        $insert .= "'0');";
        //echo '<br>' . $insert . '<br>';
        $consulta = $insert;
        $resultado = pg_query($conn, $consulta) or die('Consulta fallida: ' . pg_last_error());
    }

    //if ($res == '1') {
    // The ip exist, refresh the result or refresh the exploit
    $consulta = "UPDATE statistics SET " . strtolower($name) . " = '" . $res . "' WHERE ip = '" . $ip . "';";
    $resultado = pg_query($conn, $consulta) or die('Consulta fallida: ' . pg_last_error());
    //}
}

function rc4($array) {
    if (empty($array)) {
        return 'No';
    }

    for ($i = 0; $i < count($array); $i++) {
        if (stristr($array[$i], 'RC4') != '')
            return 'Yes';
    }
    return 'No';
}

function fowardSecrecy($array) {

    $new = 'No';
    $have = 'No';
    if (empty($array)) {
        return 'No';
    }

    for ($i = 0; $i < count($array); $i++) {
        if (stristr($array[$i], 'ECDHE') != '')
            $new = 'Yes';
        if (stristr($array[$i], 'DHE') != '' && stristr($array[$i], 'ECDHE') == '')
            $have = 'Yes';
    }
    if ($have === 'Yes')
        return 'Yes';
    else if ($new === 'Yes')
        return 'Yes-new';
    else
        return 'No';
}

function joinArrays($arr1, $arr2) {
    if (empty($arr1) && empty($arr2)) {
        return '';
    }

    for ($i = 0; $i < count($arr2); $i++) {
        $arr1[count($arr1) + $i] = $arr2[$i];
    }
    return $arr1;
}

function downgradeAttack($ip) {

    if ($ip === '')
        return 'Unknown';
    createFile('./exploit/txt/scsv_' . $ip . '.txt');
    exec('openssl s_client -connect ' . $ip . ':443 -fallback_scsv -no_tls1_2 > ./exploit/txt/scsv_' . $ip . '.txt');
    usleep(50);

    $content = file_get_contents("./exploit/txt/scsv_" . $ip . ".txt");
    if ($content === '')
        return 'Unknown';

    if (stristr($content, 'New, (NONE), Cipher is (NONE)') != '')
        return 'Yes';
    else
        return 'No';
}

function freakAttack($array) {
    if (empty($array)) {
        return 'No';
    }

    for ($i = 0; $i < count($array); $i++) {
        if (stristr($array[$i], 'EXP') != '')
            return 'Yes';
    }
    return 'No';
}

function getExploits() {

    $i = 0;

    //Connect to the database
    $conn = pg_connect("host=localhost dbname=SSL_ANALYZER user=alumnodb password=alumnodb") or die('No pudo conectarse: ' . pg_last_error());

    //Check if the name exist
    $consulta = "SELECT name, idparent FROM exploit WHERE status = '1';";
    $resultado = pg_query($conn, $consulta) or die('Consulta fallida: ' . pg_last_error());
    while ($row = pg_fetch_row($resultado, null, PGSQL_ASSOC)) {
        $res[$i]['name'] = $row['name'];
        $res[$i]['idparent'] = $row['idparent'];
        $i++;
    }
    return $res;
}

function checkIp($array1, $array2) {
    $pos = 0;

    for ($j = 0; $j < count($array1); $j++) {
        for ($i = 0; $i < count($array2); $i++) {
            if ($array1[$j]['ip'] === $array2[$i]['ip'])
                $pos++;
        }
        if ($pos === 0)
            $array2[$i] = $array1[$j];
        $pos = 0;
    }
    return $array2;
}

function poodleSSL3($ciphers, $protocols) {
    if ($ciphers === '' || $protocols !== 'OK')
        return 'No';

    for ($i = 0; $i < count($ciphers); $i++) {
        if (stristr($ciphers[$i], 'CBC') != '' && (stristr($ciphers[$i], 'AES-128') != '' || stristr($ciphers[$i], 'AES-256') != '' || stristr($ciphers[$i], 'DES') != ''))
            return 'Yes';
    }
    return 'No';
}

function poodleTLS($ciphers, $tls, $tls1_1, $tls1_2) {
    if ($ciphers === '' || ($tls !== 'OK' && $tls1_1 !== 'OK' && $tls1_2 !== 'OK'))
        return 'No';

    for ($i = 0; $i < count($ciphers); $i++) {
        if (stristr($ciphers[$i], 'CBC-') != '' && (stristr($ciphers[$i], 'AES-128') != '' || stristr($ciphers[$i], 'AES-256') != '' || stristr($ciphers[$i], 'DES') != ''))
            return 'Yes';
    }
    return 'No';
}

function BeastAttack($ciphers, $tls) {
    if ($ciphers === '' || $tls !== 'OK')
        return 'No';
    for ($i = 0; $i < count($ciphers); $i++) {
        if (stristr($ciphers[$i], 'CBC-') != '' && (stristr($ciphers[$i], 'AES-128') != '' || stristr($ciphers[$i], 'AES-256') != '' || stristr($ciphers[$i], 'DES') != '') || stristr($ciphers[$i], 'RC4') != '')
            return 'Yes';
    }
}

function DrownAttack($ip) {
    //echo '<br>entra en drown attack ' . $ip . '<br>';
    if ($ip === '')
        return 'No';

    $ips = dns_get_record($ip, DNS_ALL);
    //echo '<br>Obtiene las ips<br>';
    //echo '<pre>';
    //print_r($ips);
    //echo '</pre><br>';
    for ($i = 0; $i < count($ips); $i++) {
        //Get the address through types
        if ($ips[$i]['type'] == 'A') {
            $addr = $ips[$i]['ip'];
        } else if ($ips[$i]['type'] == 'MX' || $ips[$i]['type'] == 'NS') {
            $addr = $ips[$i]['target'];
        } else if ($ips[$i]['type'] == 'SOA') {
            $addr = $ips[$i]['rname'];
        }
        else
            $addr = '';


        if ($addr !== '') {
            //echo '<br>Obteniendo direccion: ' . $addr . '<br>';
            //echo '<br>La ip es: ' . gethostname($addr) . '<br>';
            if ($addr != $ip) {
                if (checkDrown($ip) == 'Yes') {
                    echo "<td style='color: red;'>' . $ip . ' - Vulnerable</td>";
                } else {
                    echo "<td style='color: green;'>' . $ip . ' - Not Vulnerable</td>";
                }
            }
            else
                echo "<td>' . $ip . ' (Same host)</td>";
        }
    }
}

function checkDrown($ip) {
    //Ssl 2
    createFile("./ssl/Drown_ssl2_" . $ip . ".txt");
    exec("openssl s_client -connect " . $ip . ":443 -no_ssl3 -no_tls1 -no_tls1_1 -no_tls1_2 > ./ssl/Drown_ssl2_" . $ip . ".txt");
    $ssl_2 = file_get_contents("./ssl/Drown_ssl2_" . $ip . ".txt");

    if (isCorrect($ssl_2) != 'OK')
        return 'No';

    //echo '<br>Si tiene SSLv2<br>';
    //get the ciphers of the ips
    $ciphLow = getCiphers($ip, 'MEDIUM', 'HIGH');
    $ciphMedium = getCiphers($ip, 'LOW', 'HIGH');
    $cipHigh = getCiphers($ip, 'LOW', 'MEDIUM');
    $allCiphers = joinArrays($cipHigh, $ciphMedium, $ciphLow);
    //echo '<br>Obtiene todos los cifrados<br>';
    for ($i = 0; $i < count($allCiphers); $i++) {
        if (stristr($allCiphers[$i], 'EXP') != '' && (stristr($allCiphers[$i], 'RC2') != '' || stristr($allCiphers[$i], 'RC4') != '' || stristr($allCiphers[$i], 'DES') != ''))
            return 'Yes';
    }
}

function heartbeatExtension($ip) {

    if ($ip === '')
        return 'Unknown';
    createFile('./exploit/txt/heartbeatExtension_' . $ip . '.txt');
    exec('openssl s_client -connect ' . $ip . ':443 -tlsextdebug > ./exploit/txt/heartbeatExtension_' . $ip . '.txt');
    usleep(50);

    $content = file_get_contents("./exploit/txt/heartbeatExtension_" . $ip . ".txt");
    if ($content === '')
        return 'Unknown';

    if (stristr($content, 'heartbeat') != '')
        return 'Yes';
    else
        return 'No';
}

function correctCN($domain, $name) {
    $domain = explode('.', $domain);
    $name = explode('.', $name);

    if (count($domain) != count($name))
        return 'No';
    for ($i = 0; $i < count($domain); $i++) {
        if ($domain[$i] != $name[$i] && $domain[$i] != '*' && $name[$i] != '*') {
            return 'No';
            exit();
        }
    }
    return 'Yes';
}