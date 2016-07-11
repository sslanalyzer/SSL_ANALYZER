function sslInfo(ip, domain) {

    $.ajax({
        data: {ip: ip, domain: domain},
        url: 'ssl2.php',
        type: 'post',
        beforeSend: function() {
            //("section").append('<div><img src="fonts/load.gif"></div>');
        },
        success: function(response) {

            $('.error_title').html(response);
            if ($('.error_title').text().indexOf("Error:") >= 0) {
                $('.error_title').show();
                //Create the AmCharts
            } else {
                $("section").append(response);
                ciphers(ip);
                protocols(ip);
                exploitRun(ip);
                $('h2.title_two').html('<b style="color: green;">Analyze ' + ip + ' completed</b>');
            }
        }
    });
}