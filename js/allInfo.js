//Create all the divs to show the information

function allInfo(ip) {

    var ip2 = $('tr#' + ip).find('th')[0].textContent;
    ip2 = ip2.split(' ');
    var domain = $('tr#' + ip).find('th')[1].textContent;

    $('h2.title_two').html('Testing ' + ip2[0] +'...');
    $('h2.title_two').append("<h3 class='error_title'></h3>");

//Basic Info
    info(ip2[0]);

//SSL Info
    //$('section').append('<div id="tab_ip" class="form-content"><div id="tab" class="ssl wrap1">/div>');

//Ciphers
    //$('section').append('<div id="tab_ip" class="form-content"><div id="tab" class="ssl wrap1">/div>');
//Protocols
    //$('section').append('<div id="tab_ip" class="form-content"><div id="tab" class="ssl wrap1">/div>');

    sslInfo(ip2[0], domain);
}

function allInfoIP(ip) {
    $('h2.title_two').html('Testing ' + ip + '...');
    $('h2.title_two').append("<h3 class='error_title'></h3>");

//Basic Info
    info(ip);

    sslInfo(ip, '');
}