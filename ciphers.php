<?php

session_start();

include 'functions.php';

$ip = $_POST['ip'];

if ($ip === '') {
    echo "Error in the ip";
    exit();
}

$ciphLow = getCiphers($ip, 'MEDIUM', 'HIGH');
$ciphMedium = getCiphers($ip, 'LOW', 'HIGH');
$cipHigh = getCiphers($ip, 'LOW', 'MEDIUM');

$_SESSION['rc4'] = $ciphMedium;
$_SESSION['fs'] = joinArrays($cipHigh, $ciphMedium);
$_SESSION['freak'] = joinArrays($cipHigh, $ciphMedium, $ciphLow);

echo '<div id="tab_ip" class="form-content"><div id="tab" class="ssl wrap1">';
echo "<h2 class='title'>Ciphers supported:</h2><table>";
echo "<tr class='grey'><td class='left first'>Name</td><td class='right first'>Protocol</td><td class='right first'>Key</td>"
 . "<td class='left first'>Authentication</td><td class='left first'>Encryption</td><td class='left first'>Bits</td>"
 . "<td class='left first'>MAC</td></tr>";
for ($i = 0; $i < count($cipHigh); $i++) {
    $info = exec('openssl ciphers -v \'ALL:!ADH:@STRENGTH\' | grep "' . $cipHigh[$i] . '" | uniq ');
    $info = getInfoCorrect($info);
    if ($info['enc'] == 'RC4' || $info['mac'] == 'MD5')
        echo "<tr class='grey'><td class='left second' style='color: red;'>" . $info['name'] . "</td><td class='right second'>" . $info['protocol'] . "</td><td class='right second'>" . $info['key'] . "</td>"
        . "<td class='left second'>" . $info['authentication'] . "</td><td class='left second'>" . $info['enc'] . "</td><td class='left second'>" . $info['bits'] . "</td>"
        . "<td class='left second'>" . $info['mac'] . "</td></tr>";
    else
        echo "<tr class='grey'><td class='left second' style='color: green;'>" . $info['name'] . "</td><td class='right second'>" . $info['protocol'] . "</td><td class='right second'>" . $info['key'] . "</td>"
        . "<td class='left second'>" . $info['authentication'] . "</td><td class='left second'>" . $info['enc'] . "</td><td class='left second'>" . $info['bits'] . "</td>"
        . "<td class='left second'>" . $info['mac'] . "</td></tr>";
}
for ($i = 0; $i < count($ciphMedium); $i++) {
    $info = exec('openssl ciphers -v \'ALL:!ADH:@STRENGTH\' | grep "' . $ciphMedium[$i] . '" | uniq ');
    $info = getInfoCorrect($info);
    if ($info['enc'] == 'RC4' || $info['mac'] == 'MD5')
        echo "<tr class='grey'><td class='left second' style='color: red;'>" . $info['name'] . "</td><td class='right second'>" . $info['protocol'] . "</td><td class='right second'>" . $info['key'] . "</td>"
        . "<td class='left second'>" . $info['authentication'] . "</td><td class='left second'>" . $info['enc'] . "</td><td class='left second'>" . $info['bits'] . "</td>"
        . "<td class='left second'>" . $info['mac'] . "</td></tr>";
    else
        echo "<tr class='grey'><td class='left second' style='color: orange;'>" . $info['name'] . "</td><td class='right second'>" . $info['protocol'] . "</td><td class='right second'>" . $info['key'] . "</td>"
        . "<td class='left second'>" . $info['authentication'] . "</td><td class='left second'>" . $info['enc'] . "</td><td class='left second'>" . $info['bits'] . "</td>"
        . "<td class='left second'>" . $info['mac'] . "</td></tr>";
}
for ($i = 0; $i < count($ciphLow); $i++) {
    $info = exec('openssl ciphers -v \'ALL:!ADH:@STRENGTH\' | grep "' . $ciphLow[$i] . '" | uniq ');
    $info = getInfoCorrect($info);
    echo "<tr class='grey'><td class='left second' style='color: red;'>" . $info['name'] . "</td><td class='right second'>" . $info['protocol'] . "</td><td class='right second'>" . $info['key'] . "</td>"
    . "<td class='left second'>" . $info['authentication'] . "</td><td class='left second'>" . $info['enc'] . "</td><td class='left second'>" . $info['bits'] . "</td>"
    . "<td class='left second'>" . $info['mac'] . "</td></tr>";
    //echo "<tr class='grey'><td class='left second'>Name:</td><td class='right second'><b>" . $cip[$i] . "</b></td></tr>";
}
echo '</table></div></div>';
?>
