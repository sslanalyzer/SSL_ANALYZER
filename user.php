<?php
session_start();
$name = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en"><head> 
        <meta charset="UTF-8">
        <title>SSL Analyzer</title> 
        <link rel="shorcut icon" href="fonts/logo.png">
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css">
        <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <link rel="stylesheet" type="text/css" href="css/login.css">
        <link rel="stylesheet" type="text/css" href="css/header.css">
        <link rel="stylesheet" type="text/css" href="css/footer.css">
        <link rel="stylesheet" type="text/css" href="css/fonts.css">
        <script src="http://code.jquery.com/jquery-latest.js" type="text/javascript"></script>
        <script src="js/up.js" type="text/javascript"></script>
        <script src="js/header.js" type="text/javascript"></script>

    </head>

    <body> 
        <header class="header2">            <div class="wrapper">
                <div class="logo">
                    <div><img src="fonts/logo7.png" width="80px" ;="" height="85px">
                        SSL Analyzer</div></div>
                <nav><a href="index.html">Home</a>
                    <a href="exploits.html">Exploits</a>
                    <a href="statistics.html">Statistics</a>
                    <a href="sign_off.php">Sign off: zenk4276</a></nav>
            </div>

        </header>


        <section class="contenido wrapper">

            <h1 class="title_one"><?php echo $name; ?></h1>

            <?php
            $conn = pg_connect("host=localhost dbname=SSL_ANALYZER user=alumnodb password=alumnodb") or die('No pudo conectarse: ' . pg_last_error());

            //Check if the name exist
            $consulta = "SELECT * FROM exploit WHERE status = '1' AND idparent = '" . $name . "';";
            $resultado = pg_query($conn, $consulta) or die('Error: SQL --> ' . pg_last_error());

            if (pg_num_rows($resultado) > 0) {
                ?>

                <h3 class="title_three">My exploits:</h3>       

                <div>
                    <table class="ip_tabs">
                        <tbody>
                            <tr>
                                <th class="left first">NAME</th>
                                <th class="right first">CREATED</th>
                            </tr>

                            <?php
                            $i = 0;
                            while ($row = pg_fetch_row($resultado, null, PGSQL_ASSOC)) {
                                //echo '<li id="li' . $i . '"><a href="#" onclick="infoExploit(' . $i . ')">' . $row["name"] . '</a></li>';
                                echo '<tr id="' . $i . '"><th class="left second"><a id="' . $i . '">' . $row["name"] . '</a></th>';
                                echo '<th class="right second">' . $row["created"] . '</th></tr>';
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <?php } ?>
            <h3 class="title_three pass">Change password:</h3>   
            <div id="tab" class="form-content">
                <h3 class="error_title">Error</h3>
                <div class="wrap">
                    <form id="form_pass" action="#" class="form" method="post">
                        <div class="address">
                            <div class="input-group">
                                <input type="password" id="passOld" class="passOld" name="passOld" required="”required”">
                                <label class="label" for="passOld">Old password:</label>
                            </div>
                            <div class="input-group">
                                <input type="password" id="pass" class="pass" name="pass" required="”required”">
                                <label class="label" for="pass">New password:</label>
                            </div>
                            <div class="input-group">
                                <input type="password" id="pass2" class="pass2" name="pass2" required="”required”">
                                <label class="label" for="pass2">Confirm the new password:</label>
                            </div>
                            <input type="submit" id="submit" value="Change" onclick="changePass();
                                    return false;">
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <footer class="footer">
            <p>All rights reserved © 2016. Designed By <a href="#">Iago Sánchez</a></p>
        </footer>
        <script src="js/user.js" type="text/javascript"></script>

        <img class="user" src="fonts/user.png" onclick="location.href = 'user.php'" ;=""></body></html>