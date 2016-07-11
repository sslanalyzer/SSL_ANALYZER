function ciphers(ip) {

    $.ajax({
        data: {ip: ip},
        url: 'ciphers.php',
        type: 'post',
        async: false,
        beforeSend: function () {
            //$("section").append('<div><img src="fonts/load.png"</div>');
        },
        success: function (response) {
            $("section").append(response);
        }
    });

}