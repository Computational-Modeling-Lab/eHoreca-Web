var refs = [];
var retry = 0;

window.initMaps = () => {
    refs = document.getElementsByClassName("map-td");

    if (!refs.length) {
        retry = setInterval(findElements, 100);
    }
    if (typeof google !== "undefined") {
        for (var i = 0; i < refs.length; i++) {
            if (refs[i] === "mainContent") return;
            const id = refs[i].id;
            const coordinates = refs[i].innerHTML;

            const lat = parseFloat(coordinates.split("-")[0]);
            const lng = parseFloat(coordinates.split("-")[1]);

            const mapCenter = new google.maps.LatLng(lat, lng);

            const mapOptions = {
                zoom: 11,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                center: mapCenter,
                streetViewControl: false
            };

            var map = new google.maps.Map(
                document.getElementById(id),
                mapOptions
            );

            // Add the marker to show where exactly the bin is.

            var marker = new google.maps.Marker({
                position: mapCenter,
                map: map,
                title: "Location",
                // icon: "/images/markers/waste_container_green.vsmall.png"
            });

            if (this.isEditing) {
                marker.draggable = true;

                google.maps.event.addListener(marker, "dragend", function(evt) {
                    // console.log('Marker dropped: Current Lat: ' + evt.latLng.lat().toFixed(3) + ' Current Lng: ' + evt.latLng.lng().toFixed(3));
                    // console.log(marker.getPosition().lat() + ' - ' + marker.getPosition().lng());
                });
            }
        }
    }
};

var findElements = () => {
    refs = document.getElementsByClassName("map-td");
    if (refs.length) {
        clearInterval(retry);
        retry = 0;
        window.initMaps();
    }
};
