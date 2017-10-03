function getLocation() {
    if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition);
    }
}

function showPosition(position) {
    var lat = position.coords.latitude;
    var lon = position.coords.longitude;
    console.log(lat, lon);

    jQuery.ajax({
        url: 'location.php',
        type: 'POST',
        data: {
            lat: lat,
            lon: lon,
        },
        dataType: 'json'
    });
}

function scrollBottom() {
    var div = document.getElementById("chat-window");
    setTimeout(function() {
        div.scrollTop = div.scrollHeight;
    }, 500);
}