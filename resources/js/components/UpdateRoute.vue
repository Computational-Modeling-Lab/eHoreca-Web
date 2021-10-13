<template>
  <div class="mt-5 px-1">
    <h1>New Route</h1>
    <table class="data_table">
      <tr>
        <th>Title</th>
        <td>
          <input type="text" name="title" id="title" v-model="route.title" />
        </td>
      </tr>
      <tr>
        <th>Description</th>
        <td>
          <textarea
            name="description"
            id="description"
            v-model="route.description"
          />
        </td>
      </tr>
      <tr>
        <th>Vehicle</th>
        <td>
          <select v-model="route.targetVehicleId" v-if="vehicles.length > 0">
            <option disabled value="-1">Please select one</option>
            <option
              v-for="vehicle in vehicles"
              :value="vehicle.id"
              :key="vehicle.id"
              :disabled="vehicle['in service'].toLowerCase() === 'no'"
            >
              {{ vehicle.municipality }} <b>|</b> {{ vehicle.plates }} <b>|</b>
              {{ vehicle.type }}
            </option>
          </select>
          <span v-else>
            No vehicles available, please create a new vehicle
            <a href="/create?entity=vehicles">here</a>!
          </span>
        </td>
      </tr>
      <tr class="tr-map">
        <th>Map</th>
        <td>
          <div id="main-map" ref="main-map" class="map-td"></div>
        </td>
      </tr>
      <tr>
        <th>Bin IDs</th>
        <td>
          <p v-if="route.bins.length > 0">{{ route.bins.join(", ") }}</p>
        </td>
      </tr>
    </table>
  </div>
</template>

<script>
export default {
  data() {
    return {
      vehicles: [],
      bins: [],
      markers: [],
      map: null,
      polyline: null,
    };
  },
  props: {
    route: {
      type: Object | Array,
      required: true,
    },
  },
  methods: {
    async initMap() {
      const mapElement = document.getElementById("main-map");
      var latitude = 39.625795;
      var longitude = 19.922098;

      if (navigator.geolocation)
        navigator.geolocation.getCurrentPosition((position) => {
          latitude = position.coords.latitude;
          longitude = position.coords.longitude;
        });

      const latLng = new google.maps.LatLng(latitude, longitude);
      const map = new google.maps.Map(mapElement, {
        center: latLng,
        zoom: 10,
        mapTypeControlOptions: {
          style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
          position: google.maps.ControlPosition.TOP_LEFT,
        },
        streetViewControl: false,
        fullscreenControl: false,
        rotateControl: false,
        scaleControl: false,
        styles: [
          {
            featureType: "poi.attraction",
            stylers: [
              {
                visibility: "off",
              },
            ],
          },
          {
            featureType: "poi.business",
            stylers: [
              {
                visibility: "off",
              },
            ],
          },
          {
            featureType: "poi.government",
            stylers: [
              {
                visibility: "off",
              },
            ],
          },
          {
            featureType: "poi.place_of_worship",
            stylers: [
              {
                visibility: "off",
              },
            ],
          },
          {
            featureType: "poi.school",
            stylers: [
              {
                visibility: "off",
              },
            ],
          },
          {
            featureType: "poi.park",
            elementType: "labels",
            stylers: [
              {
                visibility: "off",
              },
            ],
          },
          {
            featureType: "road.local",
            elementType: "geometry.fill",
            stylers: [
              {
                color: "#ffffff",
              },
            ],
          },
        ],
      });

      this.map = map;

      this.polyline = new google.maps.Polyline({
        strokeColor: "#000000",
        strokeOpacity: 1.0,
        strokeWeight: 3,
      });
      this.polyline.setMap(map);

      this.initMarkersAndPath();
    },
    initMarkersAndPath() {
      const selectedBinIds = this.route.bins;
      this.binMarkerController();
      selectedBinIds.forEach((binId) => {
        this.route.bins.push(parseInt(binId));
      });

      const path = this.polyline.getPath();

      this.route.bins.forEach((binId, index) => {
        const bin = this.bins.find((binObj) => {
          return binObj.id === binId;
        });

        path.setAt(
          index,
          new google.maps.LatLng(bin.location.lat, bin.location.lng)
        );
      });
    },
    binMarkerController() {
      this.polyline.getPath().clear();
      this.route.bins = [];

      this.markers.forEach((marker) => {
        marker.setMap(null);
      });

      this.bins.forEach((bin, index) => {
        let icon = "/images/markers/";

        switch (bin.type) {
          case "compost":
            icon += "waste_container_yellow.vsmall.png";
            break;
          case "glass":
            icon += "waste_container_blue.vsmall.png";
            break;
          case "recyclable":
            icon += "waste_container_blue.vsmall.png";
            break;
          case "mixed":
            icon += "waste_container_green.vsmall.png";
            break;
          case "metal":
            icon += "waste_container_blue.vsmall.png";
            break;
          case "paper":
            icon += "waste_container_yellow.vsmall.png";
            break;
          case "plastic":
            icon += "waste_container_blue.vsmall.png";
            break;
        }

        const targetId = this.bins[index].id;
        const marker = new google.maps.Marker({
          position: new google.maps.LatLng(bin.location.lat, bin.location.lng),
          icon: icon,
          map: this.map,
        });

        const infowindow = new google.maps.InfoWindow();
        infowindow.setContent(`Bin #${targetId} added!`);
        marker["infowindow"] = infowindow;

        marker.addListener("click", () => {
          const path = this.polyline.getPath();

          if (this.route.bins.includes(targetId)) {
            path.removeAt(this.route.bins.indexOf(targetId));
            marker.infowindow.close();
            this.route.bins.splice(this.route.bins.indexOf(targetId), 1);
          } else {
            this.route.bins.push(targetId);
            path.setAt(
              this.route.bins.length - 1,
              new google.maps.LatLng(bin.location.lat, bin.location.lng)
            );
            infowindow.open(this.map, marker);
          }
        });

        this.markers.push(marker);
      });
    },
    changeType() {
      this.bins = [];
      var url = `api/bins`;
      if (this.route.type !== "all") url += `?type=${this.route.type}`;

      $.ajax({
        url: url,
        method: "GET",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
        success: (res) => {
          this.bins = res;
          if (res.hasOwnProperty("data")) this.bins = res.data;
          if (res.hasOwnProperty("results")) this.bins = res.results;

          if (this.map === null)
            setTimeout(() => {
              this.initMap();
            }, 250);
          else this.binMarkerController();
        },
      });
    },
  },
  created() {
    this.map = null;

    setTimeout(() => {
      $.ajax({
        url: `api/vehicles?order=municipality`,
        method: "GET",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
        success: (res) => {
          this.vehicles = res;
          if (res.hasOwnProperty("data")) this.vehicles = res.data;
          if (res.hasOwnProperty("results")) this.vehicles = res.results;
        },
      });

      var binUrl = `api/bins`;
      if (this.route.type !== "all") binUrl += `?type=${this.route.type}`;

      $.ajax({
        url: binUrl,
        method: "GET",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
        success: (res) => {
          this.bins = res;
          if (res.hasOwnProperty("data")) this.bins = res.data;
          if (res.hasOwnProperty("results")) this.bins = res.results;

          setTimeout(() => {
            this.initMap();
          }, 250);
        },
      });
    }, 500);
  },
};
</script>

<style scoped>
.tr-map {
  height: 40vh;
}
.map-td {
  height: 50vh;
}
tr:hover {
  cursor: default;
}
</style>

