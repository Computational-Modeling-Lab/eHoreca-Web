$(document).ready(function() {
    $("#signup-content").height(
        $(window).height() -
            $(".navbar-fixed-top").height() -
            $("#footer").height()
    );
    $("#signup-content").css("margin-top", $(".navbar-fixed-top").height());
});

$(document).ready(function() {
    $("#login-content").height(
        $(window).height() -
            $(".navbar-fixed-top").height() -
            $("#footer").height()
    );
    $("#login-content").css("margin-top", $(".navbar-fixed-top").height());
});

function wanetNavigate(toShow) {
    window.location.href =
        "./list.php?show=" + toShow + "&id=" + localStorage.getItem("user_id");
}

function addMarks2Map(data) {
    for (let i = 0; i < data.length; i++) {
        loc = new google.maps.LatLng(
            parseFloat(data[i].location.lat),
            parseFloat(data[i].location.lng)
        );
        bounds.extend(loc);
        addPoint(
            map,
            data[i].location,
            data[i].id,
            null,
            "http://51.15.85.46/images/markers/waste_container_" +
                icon +
                ".vsmall.png",
            data[i].type
        );
    }
    map.fitBounds(bounds); //auto-zoom
    map.panToBounds(bounds);
}

function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split("&"),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split("=");

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined
                ? true
                : decodeURIComponent(sParameterName[1]);
        }
    }
}
