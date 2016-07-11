<?php

session_start();

include 'functions.php';

$ip = $_POST['ip'];
$domain = $_POST['domain'];

if ($ip === '') {
    echo "Error in the ip";
    exit();
}

if ($domain != '')
    if (strpos($domain, 'www.') === false || $pos != 0) {
        $domain2 = 'www.' . $domain;
    } else {
        $domain2 = substr($domain, 4 - strlen($domain));
    }

if (strstr($ip, ':'))
    $ip = '[' . $ip . ']';
$extendedValidationOids = array(
    "1.3.6.1.4.1.34697.2.1",
    "1.3.6.1.4.1.34697.2.2",
    "1.3.6.1.4.1.34697.2.1",
    "1.3.6.1.4.1.34697.2.3",
    "1.3.6.1.4.1.34697.2.4",
    "1.2.40.0.17.1.22",
    "2.16.578.1.26.1.3.3",
    "1.3.6.1.4.1.17326.10.14.2.1.2",
    "1.3.6.1.4.1.17326.10.8.12.1.2",
    "1.3.6.1.4.1.6449.1.2.1.5.1",
    "2.16.840.1.114412.2.1",
    "2.16.528.1.1001.1.1.1.12.6.1.1.1",
    "2.16.840.1.114028.10.1.2",
    "1.3.6.1.4.1.14370.1.6",
    "1.3.6.1.4.1.4146.1.1",
    "2.16.840.1.114413.1.7.23.3",
    "1.3.6.1.4.1.14777.6.1.1",
    "1.3.6.1.4.1.14777.6.1.2",
    "1.3.6.1.4.1.22234.2.5.2.3.1",
    "1.3.6.1.4.1.782.1.2.1.8.1",
    "1.3.6.1.4.1.8024.0.2.100.1.2",
    "1.2.392.200091.100.721.1",
    "2.16.840.1.114414.1.7.23.3",
    "1.3.6.1.4.1.23223.2",
    "1.3.6.1.4.1.23223.1.1.1",
    "1.3.6.1.5.5.7.1.1",
    "2.16.756.1.89.1.2.1.1",
    "2.16.840.1.113733.1.7.48.1",
    "2.16.840.1.114404.1.1.2.4.1",
    "2.16.840.1.113733.1.7.23.6",
    "1.3.6.1.4.1.6334.1.100.1",
);

//Check if SSL is enable
$fip = @fsockopen($ip, 443, $errno, $errstr, 10);
if (!$fip) {
    echo 'Error:Impossible to establish SSL connection.';
    @fclose($fip);
    exit();
}

//Get the context
$g = stream_context_create(array("ssl" => array("capture_peer_cert" => true)));
$r = stream_socket_client("ssl://" . $ip . ":443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $g);
$cont = stream_context_get_params($r);
$info = openssl_x509_parse($cont["options"]["ssl"]["peer_certificate"]);

//Get all the certs
$certs = shell_exec("openssl s_client -connect " . $ip . ":443 -showcerts 2>&1 < /dev/null");
$count = substr_count($certs, '-----BEGIN CERTIFICATE-----');
$certRoot = explode('-----END CERTIFICATE-----', $certs);
$certs = array();

for ($i = 0; $i < $count; $i++) {
    $cert[$i] = explode('-----BEGIN CERTIFICATE-----', $certRoot[$i]);
    $certs[$i] = '-----BEGIN CERTIFICATE-----' . $cert[$i][1] . '-----END CERTIFICATE-----';
    createFile('./ssl/pem/' . $ip . '_' . $i . '.pem');
    file_put_contents('./ssl/pem/' . $ip . '_' . $i . '.pem', $certs[$i]);
}

if ($count > 1) {
    //Get the size
    $file_root = file_get_contents("./ssl/pem/" . $ip . "_0.pem");
    //$file_root .= PHP_EOL . PHP_EOL;
    for ($i = 1; $i < $count; $i++) {
        $file_root .= PHP_EOL . file_get_contents("./ssl/pem/" . $ip . "_" . $i . ".pem");
    }
    createFile('./ssl/pem/' . $ip . '_chain.txt');
    file_put_contents('./ssl/pem/' . $ip . '_chain.txt', $file_root);
}

//Parser for the table
$alternativeNames = explode(",", $info['extensions']['subjectAltName']);

//Caught the .crt file
$aux = explode("URI:", $info['extensions']['authorityInfoAccess']);
for ($i = 0; $i < count($aux); $i++) {
    if (strpos($aux[$i], '.crt') > 0) {
        $crt = $aux[$i];
    }
}
$crt = explode("OCSP", $crt);
$crt2 = explode("/", $crt[0]);
shell_exec("wget -O ./ssl/crt/" . $crt2[(count($crt2) - 1)] . ".crt " . $crt[0]);
exec("openssl x509 -inform der -in ./ssl/crt/" . $crt2[(count($crt2) - 1)] . ".crt -out ./ssl/pem/" . $crt2[(count($crt2) - 1)] . ".pem");

$file_pem = file_get_contents("./ssl/pem/" . $ip . "_0.pem");
$pub_key = openssl_pkey_get_public($file_pem);
if ($pub_key == FALSE) {
    echo '<br>Error getting the publickey<br>';
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
file_put_contents('./ssl/key/pubkey_' . $ip . '_0.txt', $key['key']);

//Get the fingerprint
$fingerprint = str_replace("SHA1 Fingerprint=", '', exec('openssl x509 -noout -in ./ssl/pem/' . $ip . '_0.pem -fingerprint'));
$fingerprint = str_replace(":", '', $fingerprint);
$fingerprint = strtolower($fingerprint);

//Get the pin
$pin = exec('openssl x509 -noout -in ./ssl/pem/' . $ip . '_0.pem -pubkey | \
       openssl asn1parse -noout -inform pem -out ./ssl/key/' . $ip . '_0.key
   openssl dgst -sha256 -binary ./ssl/key/' . $ip . '_0.key | openssl enc -base64');

//Get the signature
$sign = exec('openssl x509 -noout -text -in ./ssl/pem/' . $ip . '_0.pem | grep "Signature Algorithm" | uniq');
$sign = str_replace("Signature Algorithm:", '', $sign);
$sign = str_replace("Encryption", '', $sign);

//Get the exponent
$exponent = exec('openssl x509 -noout -text -in ./ssl/pem/' . $ip . '_0.pem | grep "Exponent" | uniq');
$exponent = str_replace("Exponent:", '', $exponent);
$exponent = explode('(', $exponent);

$oids = exec('openssl x509 -noout -text -in ../ssl/pem/' . $ip . '_0.pem | grep "Policy" | uniq');
$oids = str_replace("Policy: ", '', $oids);
echo '<br>';
if (in_array($oids, $extendedValidationOids)) {
    $oids = 'Yes';
}
else
    $oids = 'No';

$crl = explode("URI:", $info['extensions']['crlDistributionPoints']);
$crl = $crl[1];
$crl = explode('Full', $crl);
$crl = $crl[0];

$crl_ocsp = '';
$ocsp = explode("OCSP - URI:", $info['extensions']['authorityInfoAccess']);
$ocsp = explode("CA", $ocsp[1]);
$ocsp = $ocsp[0];

//$today = date("Y-m-d H:i:s");
//$check = date("Y-m-d H:i:s", strtotime(getValidTo($ip, 0)));
$today = time();
$check = strtotime(getValidTo($ip, 0));
//**********GET THE FIRST CERTIFICATE **************************************
//Create the div to the SSL content
echo '<div class="separator" style="width: 100%; height: 620px;"></div>';
echo '<div id="tab_ip" class="form-content"><div id="tab" class="ssl wrap1">';
echo "<h2 class='title'>SSL info:</h2><table>";
if ($domain == $info['subject']['CN'] || $domain2 == $info['subject']['CN'] || $domain == '' || correctCN($domain, $info['subject']['CN']) === 'Yes' || correctCN($domain2, $info['subject']['CN']) === 'Yes')
    echo "<tr class='white'><td class='left second'>Subject:</td><td class='right second'><b>" . $info['subject']['CN'] . "<br><div class='small'> Fingerprint: "
    . $fingerprint . "<br> PinSHA256: " . $pin . "</b></div></td></tr>";
else
    echo "<tr class='white'><td class='left second'>Subject:</td><td class='right second' style='color: red;'><b>" . $info['subject']['CN'] . " MISTMATCH<br><div class='small'> Fingerprint: "
    . $fingerprint . "<br> PinSHA256: " . $pin . "</b></div></td></tr>";
echo "<tr class='grey'><td class='left second'>Common Names:</td><td class='right second'><b>" . $info['subject']['CN'] . "</b></td></tr>";
echo "<tr class='white'><td class='left second'>Alternative Names:</td><td class='right second'><b>";
for ($i = 0; $i < count($alternativeNames); $i++) {
    $alternativeNames2[$i] = str_replace("DNS:", "", $alternativeNames[$i]);
    echo $alternativeNames2[$i] . " ";
}
echo "</b></td></tr>";
echo "<tr class='grey'><td class='left second'>Valid From:</td><td class='right second'><b>" . gmdate("D, d M Y H:i:s", $info['validFrom_time_t']) . "</b></td></tr>";
if ($today > $check)
    echo "<tr class='white'><td class='left second' style='color: red;'>Valid To:</td><td class='right second' style='color: red;'><b>" . gmdate("D, d M Y H:i:s", $info['validTo_time_t']) . " (EXPIRED)</b></td></tr>";
else
    echo "<tr class='white'><td class='left second'>Valid To:</td><td class='right second'><b>" . gmdate("D, d M Y H:i:s", $info['validTo_time_t']) . "</b></td></tr>";

echo "<tr class='grey'><td class='left second'>Key:</td><td class='right second'><b>" . $key ['type'] . " " . $key ['tam'] . " (e " . $exponent [0] . ")</b></td></tr>";
echo "<tr class='white'><td class='left second'>Public Key:</td><td class='right second'><b><a href='./ssl/key/pubkey_" . $ip . "_0.txt' target='_blank'>Click here</a></b></td></tr>";
echo "<tr class='grey'><td class='left second'>Issuer:</td><td class='right second'><b>" . $info ['issuer']['CN'] . "<br>";
if ($crt[0] != '')
    echo
    "<div class='small'> AIA: " . $crt [0] . "</div>";
echo "</b></td></tr>";
if (strstr($sign, 'md5') || strstr($sign, 'md5'))
    echo "<tr class='white'><td class='left second' style='color: red;'>Signature Algorithm: </td><td class='right second'  style='color: red;'><b>" . $sign . " INSECURE </b></td></tr>";
else
    echo "<tr class='white'><td class='left second'>Signature Algorithm: </td><td class='right second'><b>" . $sign . "</b></td></tr>";
echo "<tr class='grey'><td class='left second'>Extended Validation:</td><td class='right second'><b>" . $oids . "</b></td></tr>";
echo "<tr class='white'><td class='left second'>Revocation information:</td><td class='right second'><b>";
if ($crl != '')
    $crl_ocsp .= "CRL,";
if ($ocsp != '')
    $crl_ocsp .= "OCSP,";
$crl_ocsp = trim($crl_ocsp, ",");
echo $crl_ocsp;
if ($crl_ocsp != '') {
    echo "<div class='small'>";
    if ($crl != '')
        echo
        "<br>CRL: " . $crl;
    if ($ocsp != '')
        echo
        "<br>OCSP: " . $ocsp;
    echo "</div>";
}
echo "</b></td></tr>";
$rev = getRevocation($ip, $crl);
if (strlen($rev) == 3) {
    echo "<tr class='grey'><td class='left second' style='color: green;'>Revocation Status:</td><td class='right second' style='color: green;'><b>Good (Not revoked)</b></td></tr>";
} else {
    echo "<tr class='grey'><td class='left second' style='color: red;'>Revocation Status:</td><td class='right second' style='color: red;'><b>Revoked</b></td></tr>";
}
if (strlen($rev) == 3) {
    echo "<tr class='white'><td class='left second' style='color: green;'>Trusted: </td><td class='right second' style='color: green;'><b>Yes</b></td></tr>";
} else {
    echo "<tr class='white'><td class='left second' style='color: red;'>Trusted: </td><td class='right second' style='color: red;'><b>No</b></td></tr>";
}
echo '</table></div></div>';

//*************GET THE REST CERTIFICATES*********************************
if ($count > 1) {

//Get the size
    $file_root = file_get_contents("./ssl/pem/" . $ip . "_0.pem");
    $file_root .= PHP_EOL . PHP_EOL;
    for ($i = 1; $i < $count; $i++) {
        $file = file_get_contents("./ssl/pem/" . $ip . "_" . $i . ".pem");
        $file_root .= $file;
    }
    $file_root .= PHP_EOL . PHP_EOL;
    file_put_contents('./ssl/pem/' . $ip . '_chain.txt', $file_root);

    $size = shell_exec('stat -c %s ./ssl/pem/' . $ip . '_chain.txt');

    echo '<div id="tab_ip" class="form-content"><div id="tab" class="ssl wrap1">';
    echo "<h2 class='title'>Additional certificartes:</h2><table>";
    echo "<tr class='grey'><td class='left second'>Certificates Provided:</td><td class='right second'><b>" . $count . "</b></td></tr>";
    echo "<tr class='white'><td class='left second'>Size:</td><td class='right second'><b>" . $size . "(bytes)</div></b></td></tr>";
    echo "<tr class='grey'><td class='left second'>Download:</td><td class='right second'><b><a href='./ssl/pem/" . $ip . "_chain.txt' target='_blank'>Click here</a></b></td></tr>";
    echo '</table></div></div>';
    for ($i = 1; $i < $count; $i++) {
        $key = getKey($ip, $i);
        echo '<div id="tab_ip" class="form-content" style="margin-top: -100px;"><div id="tab" class="ssl wrap1">';
        echo "<h2 class='title'>Certificate # " . $i . ":</h2><table>";
        echo "<tr class='grey'><td class='left second'>Subject:</td><td class='right second'><b>" . getSubject($ip, $i) . "<br><div class='small'> Fingerprint:"
        . getFingerprint($ip, $i) . "<br> PinSHA256: " . getPin($ip, $i) . "</b></div></td></tr>";
        echo "<tr class = 'white'><td class = 'left second'>Valid From:</td><td class = 'right second'><b>" . getValidFrom($ip, $i) . "</div></b></td></tr>";
        $check = strtotime(getValidTo($ip, $i));
        if ($today > $check)
            echo "<tr class = 'grey'><td class = 'left second' style='color: red;'>Valid To:</td><td class = 'right second' style='color: red;'><b>" . getValidTo($ip, $i) . " (EXPIRED)</b></td></tr>";
        else
            echo "<tr class = 'grey'><td class = 'left second'>Valid To:</td><td class = 'right second'><b>" . getValidTo($ip, $i) . "</b></td></tr>";
        echo "<tr class='white'><td class='left second'>Key:</td><td class='right second'><b>" . $key ['type'] . " " . $key ['tam'] . " (e " . getExponent($ip, $i) . ")</b></td></tr>";
        echo "<tr class='grey'><td class='left second'>Public Key:</td><td class='right second'><b><a href='./ssl/key/pubkey_" . $ip . "_" . $i . ".txt' target='_blank'>Click here</a></b></td></tr>";
        echo "<tr class='white'><td class='left second'>Issuer:</td><td class='right second'><b>" . getIssuer($ip, $i) . "</b></td></tr>";
        if (strstr($sign, 'md5') || strstr($sign, 'md5'))
            echo "<tr class='grey'><td class='left second' style='color: red;'>Signature Algorithm:</td><td class='right second' style='color: red;'><b>" . getSignature($ip, $i) . " INSECURE</b></td></tr>";
        else
            echo "<tr class='grey'><td class='left second'>Signature Algorithm:</td><td class='right second'><b>" . getSignature($ip, $i) . "</b></td></tr>";
        echo '</table></div></div>';
    }
}

//src: http://php.net/manual/en/ref.bc.php
?>