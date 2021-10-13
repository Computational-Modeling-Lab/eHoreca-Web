<template>
  <div class="mt-5 px-1">
    <h1>New Route</h1>
    <table class="data_table">
      <tr>
        <th>Title</th>
        <td>
          <input type="text" name="title" id="title" v-model="title" />
        </td>
      </tr>
      <tr>
        <th>Description</th>
        <td>
          <textarea name="description" id="description" v-model="description" />
        </td>
      </tr>
      <tr>
        <th>Vehicle</th>
        <td>
          <select v-model="targetVehicle" v-if="vehicles.length > 0">
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
      <tr v-if="targetVehicle >= 0">
        <th>Type</th>
        <td>
          <select v-model="type" @change="changeType()">
            <option disabled value="">Please select one</option>
            <option value="all">All</option>
            <option value="compost">Compost</option>
            <option value="glass">Glass</option>
            <option value="metal">Metal</option>
            <option value="mixed">Mixed</option>
            <option value="paper">Paper</option>
            <option value="plastic">Plastic</option>
            <option value="recyclable">Recyclable</option>
          </select>
        </td>
      </tr>
      <tr class="tr-map" v-if="type !== ''">
        <th>Map</th>
        <td>
          <div>
            Click the bins in the order you want the driver to go through. Click
            on the same point a second time to remove it from the route. The
            points before and after a removed point will be connected.
          </div>
          <div id="main-map" ref="main-map" class="map-td"></div>
        </td>
      </tr>
      <tr v-if="type !== ''">
        <th>Bin IDs</th>
        <td>
          <p v-if="selectedBinIDs.length > 0">
            {{ selectedBinIDs.join(", ") }}
          </p>
        </td>
      </tr>
    </table>

    <br />

    <button
      class="button text-white"
      @click="submitBtn"
      :disabled="selectedBinIDs.length < 1"
    >
      Create
    </button>
    <button class="button text-white" @click="cancelBtn">Cancel</button>
  </div>
</template>

<script>
export default {
  data: function () {
    return {
      title: "",
      description: "",
      type: "",
      targetVehicle: -1,
      vehicles: [],
      bins: [],
      selectedBinIDs: [],
      markers: [],
      map: null,
      polyline: null,
    };
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

      this.binMarkerController();
    },
    binMarkerController() {
      this.polyline.getPath().clear();
      this.selectedBinIDs = [];
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

          if (this.selectedBinIDs.includes(targetId)) {
            path.removeAt(this.selectedBinIDs.indexOf(targetId));
            marker.infowindow.close();
            this.selectedBinIDs.splice(
              this.selectedBinIDs.indexOf(targetId),
              1
            );
          } else {
            this.selectedBinIDs.push(targetId);
            path.setAt(
              this.selectedBinIDs.length - 1,
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
      if (this.type !== "all") url += `?type=${this.type}`;

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
    cancelBtn() {
      window.location.href = "list?table=routes&page=1";
    },
    submitBtn() {
      if (this.title === "" || this.description === "" || this.targetVehicle < 1 || this.selectedBinIDs.length < 1) {
        alert('Please fill all the needed info!');
        return;
      }

      $.ajax({
        url: "api/new_route",
        method: "POST",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
        data: JSON.stringify({
          user_id: localStorage.getItem("userId"),
          route_title: this.title,
          route_description: this.description,
          route_bins: this.selectedBinIDs,
          vehicle_id: this.targetVehicle,
          type: this.type,
        }),
        contentType: "application/json",
        success: (res) => {
          window.location.href = "list?table=routes&page=1";
        },
        error: (err) => {
          alert("There was an error! Please try again later.");
          console.log(err);
        },
      });
    },
  },
  created() {
    $.ajax({
      url: `api/vehicles?order=municipality`,
      method: "GET",
      dataType: "json",
      context: this,
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
      success: (res) => {
        let temp = res;
        if (res.hasOwnProperty("data")) temp = res.data;
        if (res.hasOwnProperty("results")) temp = res.results;

        this.vehicles = [...temp];
      },
      error: (err) => {
        console.error(err);
      },
    });
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
