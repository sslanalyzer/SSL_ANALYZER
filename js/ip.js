function realizaProceso() {
    var name = $('input#name').val();

    if (name.length === 0) {
        $('.error_title').hide();
        alert('You must intriduce a domain or ip');
    } else {
        $.ajax({
            data: {name: name},
            url: 'index.php',
            type: 'post',
            error: function(xhr) {
                $(".alert").html(xhr.responseText);
            },
            beforeSend: function() {
                //$("h2.title_two").html("Procesando, espere por favor...");
            },
            success: function(response) {
                //$('.error').show();

                $('.error_title').html(response)
                if ($('.error_title').text().indexOf("Error,") >= 0) {
                    $('.error_title').show();
                } else if ($('.error_title').text().indexOf("Sending ip") >= 0) {
                    $('.error_title').hide();
                    var domain = $('.error_title').text().split(' ');
                    html_ip = '<h2 class="title_two">Select IP to analyze:</h2>';
                    html_ip += '<div id="tab_ip" class="form-content">';
                    html_ip += '<div id="tab" class="wrap address">';
                    html_ip += '<table class="ip_tabs">';
                    html_ip += '<tbody><tr>';
                    html_ip += '<th class="left first">SERVER</th>';
                    html_ip += '<th class="right first">DOMAIN</th>';
                    html_ip += '</tr><tr id="0"><th class="left second"><a id="0" href="#" onclick="info(this.id); return false;">' + name + ' (ipv4)</a></th>';
                    html_ip += '<th class="right second">' + name + '</th></tr>';
                    html_ip += '</tbody></table></div>';
                    html_ip += '<div id="map" class="wrap2" style="display: none;"></div></div>';
                    $('section.contenido.wrapper').html(html_ip);
                    allInfoIP(name, '');
                } else {
                    $('.error_title').hide();
                    $('section.contenido.wrapper').html(response);
                }

            }
        });
    }
}
