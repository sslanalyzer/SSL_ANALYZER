var map;
var lat = "";
var lon = "";
var country = "";
var city = "";
var error = "";

function info(ip) {
    map = "";
    //var ip2 = $('tr#' + ip).find('th')[0].textContent;
    //var host = $('tr#' + ip).find('th')[1].textContent;
    $('#tab').removeClass('wrap').addClass('wrap1');

    //ip2 = ip2.split(' ');$('#map').show();
    $('#map').show();

    $.getJSON("http://ip-api.com/json/" + ip, function(data) {
        var table_body = "";
        table_body += "<h2 class='title'>Basic info:</h2><table>";
        var i = 0;
        $.each(data, function(k, v) {
            if ((i % 2) == 0)
                table_body += "<tr class='white'><td class='left second'>" + k + "</td><td class='right second'><b>" + v + "</b></td></tr>";
            else
                table_body += "<tr class='grey'><td class='left second'>" + k + "</td><td class='right second'><b>" + v + "</b></td></tr>";
            if (k == 'lat')
                lat = v;
            if (k == 'lon')
                lon = v;
            if (k == 'country')
                country = v;
            if (k == 'city')
                city = v;
            if (k == 'message')
                error = v;
            if (city != '' && country != '' && lat != '' && lon != '')
                initialize(lat, lon, country, city);
            i++;
        });
        table_body += '</table>';
        if (error == '') {
            $("div.wrap1").html(table_body);
            $('.error_title').hide();
            $("div.separator").show();
        }
        else {
            $('.error_title').html(error);
            $('.error_title').show();
            $("div.separator").hide();
            $('#map').hide();
        }
    });

    if (error == '') {
        $('#map').show();
    }
}

