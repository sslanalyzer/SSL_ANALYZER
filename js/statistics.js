$(document).ready(function() {

    listStatistics();
});

function listStatistics() {
    $.ajax({
        data: {},
        url: 'listStatistics.php',
        type: 'post',
        beforeSend: function() {
            //$("h2.title_two").html("Procesando, espere por favor...");
        },
        success: function(response) {
            //$('.error').show();

            $('.error_title').html(response)
            if ($('.error_title').text().indexOf("Error: The name") >= 0) {
                elementos.name.value = "";
                elementos.name.className = elementos.name.className + " error";
                $('.error_title').show();
            } else if ($('.error_title').text().indexOf("Error:") >= 0) {
                $('.error_title').show();
            } else {
                $('.error_title').hide();
                $('div.btn-login').hide();
                $('ul#ul.head').html(response);
            }
        }});
}

function getInfo() {
    $('div.emergent').show();
}

function close_aside() {
    $('div.emergent').hide();
}

function redirect() {
    location.href = 'index.html';
}

function infoStadistic(id) {

    var name = $('li#li' + id + ' a').children().context.activeElement.innerText;
    var name = $('li#li' + id + ' a')[0].innerHTML;
    $.ajax({
        data: {name: name},
        url: 'chart.php',
        type: 'post',
        beforeSend: function() {
            //$("h2.title_two").html("Procesando, espere por favor...");
        },
        success: function(response) {
            //$('.error').show();

            $('.error_title').html(response)

            if ($('.error_title').text().indexOf("Consulta fallida") >= 0) {
                $('.error_title').html('The exploit: ' + name + ' doesnt have statistics');
                $('.error_title').show();
            }
            if ($('.error_title').text().indexOf("Error:") >= 0) {
                $('.error_title').show();
                //Create the AmCharts
            } else {
                var data = jQuery.parseJSON(response);
                $('.error_title').hide();
                $('div.new-exploit').hide();
                var html_code = '';
                //$('div.list-exploits').html('<h2>' + data.name + '</h2><br>');
                html_code += '<h2>' + data.name + '</h2><br>';

                if (data.des !== '')
                    html_code += '<h1>Description:</h1><br><div class="description">' + data.des + '</div><br>';
                html_code += '<h2>Stadistic:</h2><br>';
                $('div.list-exploits').html(html_code);

                if (data[0] !== '0' || data[1] !== '0') {

                    $('div.list-exploits').append('<br> <h1>Last Week:</h1><br><br>\n\
<div id="chart"></div>');

                    var chart = AmCharts.makeChart("chart", {
                        "type": "pie",
                        "theme": "light",
                        "dataProvider": [{
                                "Vulnerable": "Yes",
                                "value": data[0]
                            }, {
                                "Vulnerable": "No",
                                "value": data[1]
                            }
                        ],
                        "valueField": "value",
                        "titleField": "Vulnerable",
                        "outlineAlpha": 0.4,
                        "depth3D": 15,
                        "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
                        "angle": 30,
                        "export": {
                            "enabled": true
                        }
                    });
                    $('div.amcharts-chart-div a').hide();
                }
                if (data[2] !== '0' || data[3] !== '0') {
                    $('div.list-exploits').append('<br> <h1>Last Month:</h1><br><br><div id="chart1"></div>');
                    var chart1 = AmCharts.makeChart("chart1", {
                        "type": "pie",
                        "theme": "light",
                        "dataProvider": [{
                                "Vulnerable": "Yes",
                                "value": data[2]
                            }, {
                                "Vulnerable": "No",
                                "value": data[3]
                            }
                        ],
                        "valueField": "value",
                        "titleField": "Vulnerable",
                        "outlineAlpha": 0.4,
                        "depth3D": 15,
                        "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
                        "angle": 30,
                        "export": {
                            "enabled": true
                        }
                    });

                    $('div.amcharts-chart-div a').hide();
                }
                if (data[4] !== '0' || data[5] !== '0') {
                    $('div.list-exploits').append('<br> <h1>All:</h1><br><br><div id="chart2"></div>');
                    var chart2 = AmCharts.makeChart("chart2", {
                        "type": "pie",
                        "theme": "light",
                        "dataProvider": [{
                                "Vulnerable": "Yes",
                                "value": data[4]
                            }, {
                                "Vulnerable": "No",
                                "value": data[5]
                            }
                        ],
                        "valueField": "value",
                        "titleField": "Vulnerable",
                        "outlineAlpha": 0.4,
                        "depth3D": 15,
                        "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
                        "angle": 30,
                        "export": {
                            "enabled": true
                        }
                    });
                    $('div.amcharts-chart-div a').hide();
                }
            }
        }});
}

function allStadistics() {

    $.ajax({
        data: {},
        url: 'allStatistics.php',
        type: 'post',
        beforeSend: function() {
            //$("h2.title_two").html("Procesando, espere por favor...");
        },
        success: function(response) {
            //$('.error').show();

            $('.error_title').html(response);

            if ($('.error_title').text().indexOf("Error:") >= 0) {
                $('.error_title').show();
                //Create the AmCharts
            } else {
                $('.error_title').hide();
                $('div.new-exploit').hide();
                var data = jQuery.parseJSON(response);
                var week = 0;
                var month = 0;
                var all = 0;
                //check the content
                for (i = 0; i < data.length; i++) {
                    week += parseInt(data[i].VALUE) + parseInt(data[i].NOTVALUE);
                    month += parseInt(data[i].VALUE2) + parseInt(data[i].NOTVALUE2);
                    all += parseInt(data[i].VALUE3) + parseInt(data[i].NOTVALUE3);
                }

                var html_text = '';
                html_text += '<br><h2>All stadistic:</h2><br>';
                $('div.list-exploits').html(html_text);
                if (week !== 0) {
                    html_text += '<br> <h1>Last Week:</h1><br><br><h1 id="sub">Number of IPs with: </h1>\n\
<div id="container"><div id="chartdiv"></div>\n\
<div id="tab" class="wrap stadistics">\n\
<table class="stadistics">\n\
<tbody>\n\
 <tr>\n\
<th class="left first">Name of the exploits</th>\n\
<th class="right first">Number of IPs with</th>\n\
<th class="right first">Total</th>\n\
</tr>';
                    for (i = 0; i < data.length; i++) {
                        html_text += '<tr id="' + i + '">';
                        html_text += '<th class="left second">' + data[i].NAME + '</th>';
                        html_text += '<th class="right second">' + data[i].VALUE + '</th>';
                        html_text += '<th class="right second">' + (parseInt(data[i].VALUE) + parseInt(data[i].NOTVALUE)) + '</th>';
                        html_text += '</tr>';
                    }
                    html_text += '</table></div></div><br><br>';
                    $('div.list-exploits').html(html_text);

                    var chart = AmCharts.makeChart("chartdiv", {
                        "type": "pie",
                        "theme": "light",
                        "dataProvider": data, "valueField": "VALUE",
                        "titleField": "NAME",
                        "outlineAlpha": 0.4,
                        "depth3D": 15,
                        "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
                        "angle": 30,
                        "export": {
                            "enabled": true
                        }
                    });
                    $('div.amcharts-chart-div a').hide();
                    html_text = '';
                    html_text += '<br><h1 id="sub">Number of IPs not with: </h1>\n\
                <div id="container"><div id="chartdiv1"></div>\n\
                    <div id="tab" class="wrap stadistics">\n\
<table class="stadistics">\n\
<tbody>\n\
 <tr>\n\
<th class="left first">Name of the exploits</th>\n\
<th class="right first">Number of IPs not with</th>\n\
<th class="right first">Total</th>\n\
</tr>';
                    for (i = 0; i < data.length; i++) {
                        html_text += '<tr id="' + i + '">';
                        html_text += '<th class="left second">' + data[i].NAME + '</th>';
                        html_text += '<th class="right second">' + data[i].NOTVALUE + '</th>';
                        html_text += '<th class="right second">' + (parseInt(data[i].VALUE) + parseInt(data[i].NOTVALUE)) + '</th>';
                        html_text += '</tr>';
                    }
                    html_text += '</table></div></div><br><br>';
                    $('div.list-exploits').append(html_text);

                    var chart1 = AmCharts.makeChart("chartdiv1", {
                        "type": "pie",
                        "theme": "light",
                        "dataProvider": data,
                        "valueField": "NOTVALUE",
                        "titleField": "NAME",
                        "outlineAlpha": 0.4,
                        "depth3D": 15,
                        "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
                        "angle": 30,
                        "export": {
                            "enabled": true
                        }
                    });
                    $('div.amcharts-chart-div a').hide();
                }
                html_text = '';
                if (month !== 0) {
                    html_text += '<br> <h1>Last Month:</h1><br><br><h1 id="sub">Number of IPs with: </h1>\n\
                    <div id="container"><div id="chartdiv2"></div>\n\
                <div id="tab" class="wrap stadistics">\n\
                <table class="stadistics">\n\
<tbody>\n\
 <tr>\n\
<th class="left first">Name of the exploits</th>\n\
<th class="right first">Number of IPs not with</th>\n\
<th class="right first">Total</th>\n\
</tr>';
                    for (i = 0; i < data.length; i++) {
                        html_text += '<tr id="' + i + '">';
                        html_text += '<th class="left second">' + data[i].NAME + '</th>';
                        html_text += '<th class="right second">' + data[i].VALUE2 + '</th>';
                        html_text += '<th class="right second">' + (parseInt(data[i].VALUE2) + parseInt(data[i].NOTVALUE2)) + '</th>';
                        html_text += '</tr>';
                    }
                    html_text += '</table></div></div><br><br>';
                    $('div.list-exploits').append(html_text);
                    var chart2 = AmCharts.makeChart("chartdiv2", {
                        "type": "pie",
                        "theme": "light",
                        "dataProvider": data,
                        "valueField": "VALUE2",
                        "titleField": "NAME",
                        "outlineAlpha": 0.4,
                        "depth3D": 15,
                        "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
                        "angle": 30,
                        "export": {
                            "enabled": true
                        }
                    });
                    $('div.amcharts-chart-div a').hide();
                    html_text = '';
                    html_text += '<br><h1 id="sub">Number of IPs not with: </h1>\n\
                <div id="container"><div id="chartdiv3"></div>\n\
<div id="tab" class="wrap stadistics">\n\
                <table class="stadistics">\n\
<tbody>\n\
                <tr>\n\
<th class="left first">Name of the exploits</th>\n\
<th class="right first">Number of IPs not with</th>\n\
<th class="right first">Total</th>\n\
</tr>';
                    for (i = 0; i < data.length; i++) {
                        html_text += '<tr id="' + i + '">';
                        html_text += '<th class="left second">' + data[i].NAME + '</th>';
                        html_text += '<th class="right second">' + data[i].NOTVALUE2 + '</th>';
                        html_text += '<th class="right second">' + (parseInt(data[i].VALUE2) + parseInt(data[i].NOTVALUE2)) + '</th>';
                        html_text += '</tr>';
                    }
                    html_text += '</table></div></div><br><br>';
                    $('div.list-exploits').append(html_text);
                    var chart3 = AmCharts.makeChart("chartdiv3", {
                        "type": "pie",
                        "theme": "light",
                        "dataProvider": data,
                        "valueField": "NOTVALUE2",
                        "titleField": "NAME",
                        "outlineAlpha": 0.4,
                        "depth3D": 15,
                        "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
                        "angle": 30,
                        "export": {
                            "enabled": true
                        }
                    });
                    $('div.amcharts-chart-div a').hide();
                }
                html_text = '';
                if (all !== 0) {
                    html_text += '<br> <h1>All:</h1><br><br><h1 id="sub">Number of IPs with: </h1>\n\
                <div id="container"><div id="chartdiv4"></div>\n\
                <div id="tab" class="wrap stadistics">\n\
<table class="stadistics">\n\
<tbody>\n\
 <tr>\n\
<th class="left first">Name of the exploits</th>\n\
<th class="right first">Number of IPs not with</th>\n\
<th class="right first">Total</th>\n\
</tr>';
                    for (i = 0; i < data.length; i++) {
                        html_text += '<tr id="' + i + '">';
                        html_text += '<th class="left second">' + data[i].NAME + '</th>';
                        html_text += '<th class="right second">' + data[i].VALUE3 + '</th>';
                        html_text += '<th class="right second">' + (parseInt(data[i].VALUE3) + parseInt(data[i].NOTVALUE3)) + '</th>';
                        html_text += '</tr>';
                    }
                    html_text += '</table></div></div><br><br>';
                    $('div.list-exploits').append(html_text);

                    var chart2 = AmCharts.makeChart("chartdiv4", {
                        "type": "pie",
                        "theme": "light",
                        "dataProvider": data,
                        "valueField": "VALUE3",
                        "titleField": "NAME",
                        "outlineAlpha": 0.4,
                        "depth3D": 15,
                        "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
                        "angle": 30,
                        "export": {
                            "enabled": true
                        }
                    });
                    $('div.amcharts-chart-div a').hide();
                    html_text = '';
                    html_text += '<br><h1 id="sub">Number of IPs not with: </h1>\n\
                <div id="container"><div id="chartdiv5"></div>\n\
                <div id="tab" class="wrap stadistics">\n\
<table class="stadistics">\n\
<tbody>\n\
 <tr>\n\
<th class="left first">Name of the exploits</th>\n\
<th class="right first">Number of IPs not with</th>\n\
<th class="right first">Total</th>\n\
</tr>';
                    for (i = 0; i < data.length; i++) {
                        html_text += '<tr id="' + i + '">';
                        html_text += '<th class="left second">' + data[i].NAME + '</th>';
                        html_text += '<th class="right second">' + data[i].NOTVALUE3 + '</th>';
                        html_text += '<th class="right second">' + (parseInt(data[i].VALUE3) + parseInt(data[i].NOTVALUE3)) + '</th>';
                        html_text += '</tr>';
                    }
                    html_text += '</table></div></div><br><br>';
                    $('div.list-exploits').append(html_text);

                    var chart3 = AmCharts.makeChart("chartdiv5", {
                        "type": "pie",
                        "theme": "light",
                        "dataProvider": data,
                        "valueField": "NOTVALUE3",
                        "titleField": "NAME", "outlineAlpha": 0.4,
                        "depth3D": 15,
                        "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
                        "angle": 30,
                        "export": {
                            "enabled": true
                        }
                    });
                    $('div.amcharts-chart-div a').hide();
                }
            }
        }});
}
