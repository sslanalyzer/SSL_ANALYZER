<?php

session_start();

include 'functions.php';

$ip = $_POST['ip'];

if ($ip === '') {
    echo "Error in the ip";
    exit();
}

//Tls 1.0
createFile("./ssl/tls1_" . $ip . ".txt");
exec("openssl s_client -connect " . $ip . ":443 -tls1 > ./ssl/tls1_" . $ip . ".txt");
$tls = file_get_contents("./ssl/tls1_" . $ip . ".txt");

//Tls 1.1
createFile("./ssl/tls1_1_" . $ip . ".txt");
exec("openssl s_client -connect " . $ip . ":443 -tls1_1 > ./ssl/tls1_1_" . $ip . ".txt");
$tls1_1 = file_get_contents("./ssl/tls1_1_" . $ip . ".txt");

//Tls 1.2
createFile("./ssl/tls1_2_" . $ip . ".txt");
exec("openssl s_client -connect " . $ip . ":443 -tls1_2 > ./ssl/tls1_2_" . $ip . ".txt");
$tls1_2 = file_get_contents("./ssl/tls1_2_" . $ip . ".txt");

//Ssl 3
createFile("./ssl/ssl3_" . $ip . ".txt");
exec("openssl s_client -connect " . $ip . ":443 -ssl3 > ./ssl/ssl3_" . $ip . ".txt");
$ssl_3 = file_get_contents("./ssl/ssl3_" . $ip . ".txt");

//Ssl 2
createFile("./ssl/ssl2_" . $ip . ".txt");
exec("openssl s_client -connect " . $ip . ":443 -no_ssl3 -no_tls1 -no_tls1_1 -no_tls1_2 > ./ssl/ssl2_" . $ip . ".txt");
$ssl_2 = file_get_contents("./ssl/ssl2_" . $ip . ".txt");

echo '<div id="tab_ip" class="form-content"><div id="tab" class="ssl wrap1">';
echo "<h2 class='title'>Protocols supported:</h2><table>";
echo "<tr class='grey'><td class='left first'>Name</td><td class='right first'>Support</td></tr>";

//Save all the protocols
$_SESSION['tls1.2'] = isCorrect($tls1_2);
$_SESSION['tls1.1'] = isCorrect($tls1_1);
$_SESSION['tls'] = isCorrect($tls);
$_SESSION['ssl3'] = isCorrect($ssl_3);
$_SESSION['ssl2'] = isCorrect($ssl_2);

if (isCorrect($tls1_2) == 'OK') {
    echo "<tr class='grey'><td class='left second' style='color: green;'>TLS 1.2</td><td class='right second' style='color: green;'>Yes</td></tr>";
} else if (isCorrect($tls1_2) == 'UNKNOWN') {
    echo "<tr class='grey'><td class='left second' style='color: orange;'>TLS 1.2</td><td class='right second' style='color: orange;'>Unknown</td></tr>";
} else {
    echo "<tr class='grey'><td class='left second' style='color: red;'>TLS 1.2</td><td class='right second' style='color: red;'>No</td></tr>";
}

if (isCorrect($tls1_1) == 'OK') {
    echo "<tr class='grey'><td class='left second'>TLS 1.1</td><td class='right second'>Yes</td></tr>";
} else if (isCorrect($tls1_1) == 'UNKNOWN') {
    echo "<tr class='grey'><td class='left second' style='color: orange;'>TLS 1.1</td><td class='right second' style='color: orange;'>Unknown</td></tr>";
} else {
    echo "<tr class='grey'><td class='left second'>TLS 1.1</td><td class='right second'>No</td></tr>";
}

if (isCorrect($tls) == 'OK') {
    echo "<tr class='grey'><td class='left second'>TLS 1.0</td><td class='right second'>Yes</td></tr>";
} else if (isCorrect($tls) == 'UNKNOWN') {
    echo "<tr class='grey'><td class='left second' style='color: orange;'>TLS 1.0</td><td class='right second' style='color: orange;'>Unknown</td></tr>";
} else {
    echo "<tr class='grey'><td class='left second'>TLS 1.0</td><td class='right second'>No</td></tr>";
}

if (isCorrect($ssl_3) == 'OK') {
    echo "<tr class='grey'><td class='left second' style='color: red;'>SSL 3</td><td class='right second' style='color: red;'>Yes</td></tr>";
} else if (isCorrect($ssl_3) == 'UNKNOWN') {
    echo "<tr class='grey'><td class='left second' style='color: orange;'>SSL 3</td><td class='right second' style='color: orange;'>Unknown</td></tr>";
} else {
    echo "<tr class='grey'><td class='left second'>SSL 3</td><td class='right second'>No</td></tr>";
}

if (isCorrect($ssl_2) == 'OK') {
    echo "<tr class='grey'><td class='left second' style='color: red;'>SSL 2</td><td class='right second' style='color: red;'>Yes</td></tr>";
} else if (isCorrect($ssl_2) == 'UNKNOWN') {
    $openTimeout = 5;
    echo '<br><br>SSL2: ' . isCorrect($ssl_2) . '<br>';
    $stream = @fsockopen("ssl2://" . $ip, 443, $errno, $errstr, $openTimeout);
    echo '<br><br>SSL2 Stream: ' . $stream . '<br>';
    if (!$stream) {
        echo "<tr class='grey'><td class='left second' style='color: orange;'>SSL 2</td><td class='right second' style='color: orange;'>Unknown</td></tr>";
    } else {
        echo "<tr class='grey'><td class='left second' style='color: red;'>SSL 2</td><td class='right second' style='color: red;'>Yes</td></tr>";
    }
} else {
    echo "<tr class='grey'><td class='left second'>SSL 2</td><td class='right second'>No</td></tr>";
}

echo '</table></div></div>';
?>
