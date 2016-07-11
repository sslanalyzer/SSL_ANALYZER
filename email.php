<?php

/* $email = $_POST['email1'];

  $to = $email;
  $subject = "SSL_ANALYZER - Lost pass";
  $txt = "The pass has been changed, the new pass is:". "\r\n";
  $headers = "From: ssl_analyzer@gmail.com" . "\r\n";

  mail($to,$subject,$txt,$headers); */

    ini_set("SMTP", "aspmx.l.google.com");
    ini_set("sendmail_from", "iago.sanchezorieto@gmail.com");

    $message = "The mail message was sent with the following mail setting:\r\nSMTP = aspmx.l.google.com\r\nsmtp_port = 25\r\nsendmail_from = YourMail@address.com";

    $headers = "From: iago.sanchezorieto@gmail.com";


    mail("atest@gmail.com", "Testing", $message, $headers);
    echo "Check your email now....<BR/>";

// Enviarlo
//mail($name, 'Pass changed - SSL ANALYZER', $mensaje);
?>

