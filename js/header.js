$(document).ready(function () {

    /*   $(window).scroll(function() {
     if ($(this).scrollTop() > 0) {
     $('header').addClass('header2');
     } else {
     $('header').removeClass('header2');
     }
     
     });*/

    var pag = $('a.active').attr('href');
    var url = 'user.php';

    $.ajax({
        data: {active: pag
        },
        url: 'header.php',
        type: 'post',
        beforeSend: function () {
            //$("h2.title_two").html("Procesando, espere por favor...");
        },
        success: function (response) {
            $('.error_title').html(response)
            if ($('.error_title').text().indexOf("Erro") < 0) {
                $('header.header2').html(response);
                $('body').append('<img class="user" src="fonts/user.png" onclick="location.href=\'user.php\'";/>');
                $('body').append('<img class="info" src="fonts/info.png" onclick="location.href=\'info.html\'";/>');
            } else
                $('body').append('<img class="info2" src="fonts/info.png" onclick="location.href=\'info.html\'";/>');
        }});

});



function sign_off() {
    $.ajax({
        data: {active: 'cerrar'
        },
        url: 'sign_off.php',
        type: 'post',
        beforeSend: function () {
            //$("h2.title_two").html("Procesando, espere por favor...");
        },
        success: function (response) {
            $('.error_title').html(response)
            if ($('.error_title').text().indexOf("Error") < 0) {
                $('header.header2').html(response);
            }
        }});
}