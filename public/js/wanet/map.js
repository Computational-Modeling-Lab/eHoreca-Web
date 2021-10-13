var Api = new (function() {
    this.URL = "http://51.15.85.46/api.php/";
    this.post = function(endpoint, params, handler) {
        $.post(this.URL + endpoint, params, function(data, status, request) {
            var response = new ApiResponse(data);
            if (typeof handler == "function") {
                handler(response);
            }
        });
    };
})();

var map = null;
var markers = {
    mixed: [],
    recyclable: [],
    compost: []
};

var paths = [];
var polygons = [];
var points = [];
var lastPopUp;

function initMap() {
    $(window).resize(onResize);
    map = new google.maps.Map(document.getElementById("main-map"), {
        center: {
            lat: 39.625795,
            lng: 19.922098
        },
        zoom: 14,
        mapTypeControlOptions: {
            style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
            position: google.maps.ControlPosition.TOP_RIGHT
        },
        styles: [
            {
                featureType: "poi.attraction",
                stylers: [
                    {
                        visibility: "off"
                    }
                ]
            },
            {
                featureType: "poi.business",
                stylers: [
                    {
                        visibility: "off"
                    }
                ]
            },
            {
                featureType: "poi.government",
                stylers: [
                    {
                        visibility: "off"
                    }
                ]
            },
            {
                featureType: "poi.place_of_worship",
                stylers: [
                    {
                        visibility: "off"
                    }
                ]
            },
            {
                featureType: "poi.school",
                stylers: [
                    {
                        visibility: "off"
                    }
                ]
            },
            {
                featureType: "poi.park",
                elementType: "labels",
                stylers: [
                    {
                        visibility: "off"
                    }
                ]
            },
            {
                featureType: "road.local",
                elementType: "geometry.fill",
                stylers: [
                    {
                        color: "#ffffff"
                    }
                ]
            }
        ]
    });
    getGeoData();
    onResize();
}

function onResize() {
    $("#main-map").height(
        $(window).height() - $("#topbar").height() - $("#footer").height() + 20
    );
}

function addPoint(map, position, title, label, icon, type) {
    // Info windows are used for popups of markers.
    var popUp = new google.maps.InfoWindow();

    // var position = {lat: lat, lng: lon};
    tmp = {
        map: map,
        position: {
            lat: parseFloat(position.lat),
            lng: parseFloat(position.lng)
        },
        title: title.toString()
    };
    if (label != null) {
        tmp.label = label;
    }
    if (icon != null) {
        tmp.icon = icon;
    }

    marker = new google.maps.Marker(tmp);

    // On click handler for icons. A tooltip could show up that gives options of actions to the user.
    google.maps.event.addListener(
        marker,
        "click",
        (function(marker, title) {
            return function() {
                let content =
                    '<ol id="' +
                    title +
                    '"><li class="tooltip-item"><a href="/report?bin=' +
                    title +
                    "&lat=" +
                    position.lat +
                    "&lng=" +
                    position.lng +
                    '">Report</a></li><li class="tooltip-item"><a onclick="window.location=\'list.php?show=reports&id=' +
                    localStorage.getItem("user_id") +
                    "&bin_id=" +
                    title +
                    "'\">See reports</a></li></ol>";
                // If at least one pop up is opened, close it before showing the next one.
                if (lastPopUp !== undefined) {
                    lastPopUp.close();
                }

                // Can be any valid html markup
                popUp.setContent(content);
                popUp.open(map, marker);
                lastPopUp = popUp;
            };
        })(marker, title)
    );

    points.push(position);
    if (typeof markers[type] !== "undefined") markers[type].push(marker);
}

function addPath(map, points, color, width, id) {
    if (color == null) {
        color = "#000000";
    }
    if (width == null) {
        width = 2;
    }
    var path = new google.maps.Polyline({
        map: map,
        path: points,
        strokeWeight: width,
        strokeColor: color,
        clickable: true,
        _objectID: id
    });
    google.maps.event.addListener(path, "click", function() {
        console.log("Path ID: " + this._objectID);
    });
    paths.push(path);
}

function addPolygon(map, points, color) {
    if (color == null) {
        color = "#000000";
    }
    var polygon = new google.maps.Polygon({
        map: map,
        paths: points,
        strokeWeight: 2,
        strokeColor: color,
        fillColor: color,
        fillOpacity: 0.3
    });
    polygons.push(polygon);
}

function clearMap(type) {
    if (typeof markers[type] !== "undefined") {
        for (var i = 0; i < markers[type].length; i++) {
            markers[type][i].setMap(null);
        }
        markers[type] = [];
    }
    for (var i = 0; i < paths.length; i++) {
        paths[i].setMap(null);
    }
    paths = [];
    for (var i = 0; i < polygons.length; i++) {
        polygons[i].setMap(null);
    }
    polygons = [];
}

function getQueryValue(key) {
    var queryString = window.location.search.substring(1);
    var params = queryString.split("&");
    for (var i = 0; i < params.length; i++) {
        param = params[i].split("=");
        if (param[0] == key) {
            return param[1];
        }
    }
    return null;
}

function getEnabledLayers() {
    var enabledLayers = getQueryValue("layers");
    if (enabledLayers != null) {
        return enabledLayers.split(",");
    }
    return null;
}

function getGeoData() {
    clearMap();
    var enabledLayers = getEnabledLayers();
    if (enabledLayers == null) {
        return;
    }
    //console.log(enabledLayers);
    for (var i = 0; i < enabledLayers.length; i++) {
        Api.post("getlayer/" + enabledLayers[i], null, receiveLayer);
    }
}

function receiveLayer(response) {
    bounds = new google.maps.LatLngBounds();

    var layer = response.getWhole();
    var icon = layer.point_marker;
    if (icon != null) {
        icon = "http://51.15.85.46/images/markers/" + icon;
    }
    //console.log(layer);
    for (var i = 0; i < layer.points.length; i++) {
        var temp = layer.points[i].latlng;
        loc = new google.maps.LatLng(temp["lat"], temp["lng"]);
        bounds.extend(loc);

        // addPoint(map, layer.points[i].latlng, layer.title, layer.points[i].label, icon);
    }
    for (var i = 0; i < layer.paths.length; i++) {
        addPath(
            map,
            layer.paths[i].points,
            layer.path_color,
            layer.path_width,
            layer.paths[i].id
        );
    }
    for (var i = 0; i < layer.polygons.length; i++) {
        addPolygon(map, layer.polygons[i].points, layer.polygon_color);
    }

    map.fitBounds(bounds); //auto-zoom
    map.panToBounds(bounds); //auto-center
}

function toggleLayer(layerId) {
    var newURL = "http://51.15.85.46";
    var enabledLayers = getEnabledLayers();
    if (enabledLayers == null) {
        $("#layer-" + layerId).addClass("active");
        enabledLayers = ["" + layerId];
    } else {
        if (enabledLayers.includes("" + layerId)) {
            $("#layer-" + layerId).removeClass("active");
            var removeIndex = enabledLayers.indexOf("" + layerId);
            if (removeIndex > -1) {
                enabledLayers.splice(removeIndex, 1);
            }
        } else {
            $("#layer-" + layerId).addClass("active");
            enabledLayers.push("" + layerId);
        }
    }
    var query = "";
    enabledLayers.sort();
    for (var i = 0; i < enabledLayers.length; i++) {
        query += enabledLayers[i];
        if (i + 1 < enabledLayers.length) {
            query += ",";
        }
    }
    if (enabledLayers.length > 0) {
        newURL += "?layers=" + query;
    }

    if (history.pushState) {
        window.history.pushState(
            {
                path: newURL
            },
            "",
            newURL
        );
    } else {
        window.location = newURL;
    }
    getGeoData();
}

function initReverseGeocoding() {
    var geocoder = new google.maps.Geocoder();

    $(".location").each(function() {
        const coordinates = $(this).html();
        const lat = coordinates.split("-")[0];
        const lng = coordinates.split("-")[1];
        const location = { lat: parseFloat(lat), lng: parseFloat(lng) };

        geocoder.geocode({ location: location }, function(results, status) {
            console.log(results);
        });
    });
}
