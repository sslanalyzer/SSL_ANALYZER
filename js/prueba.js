
var map;
function initialize(lat, lon, country, city) {
    var mapOptions = {
        zoom: 14,
        center: {lat: lat, lng: lon}
    };
    map = new google.maps.Map(document.getElementById('map'),
        mapOptions);

    var marker = new google.maps.Marker({
        position: {lat: lat, lng: lon},
        map: map,
        title: city + '<br>' + country

    });

    var infowindow = new google.maps.InfoWindow({
        content: '<p>' + marker.getTitle() + '</p><br><p>Location:' + marker.getPosition() + '</p><br>'
    });

    google.maps.event.addListener(marker, 'click', function() {
        infowindow.open(map, marker);
    });
}

google.maps.event.addDomListener(window, 'load', initialize);


