<?php

session_start();
$pag = $_POST['active'];
if (isset($_SESSION['username'])) {
    echo '            <div class="wrapper">
                                <div class="logo">
                    <div><img src="fonts/logo7.png" width="80px"; height="85px";>
                    SSL Analyzer</div></div>
                <nav>';

    if ($pag == 'index.html')
        echo '<a class="active" href="index.html">Home</a>';
    else
        echo '<a href="index.html">Home</a>';

    if ($pag == 'exploits.html')
        echo '<a class="active" href="exploits.html">Exploits</a>';
    else
        echo '<a href="exploits.html">Exploits</a>';

    if ($pag == 'statistics.html')
        echo '<a class="active" href="statistics.html">Statistics</a>';
    else
        echo '<a href="statistics.html">Statistics</a>';
    echo '     <a href="sign_off.php">Sign off: ' . $_SESSION['username'] . '</a></nav>
            </div>';
}
else
    echo 'Error';
?>


