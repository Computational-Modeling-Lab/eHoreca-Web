<template>
  <div class="mt-5 px-1">
    <h1>Update Bin</h1>

    <table class="data_table" v-if="bin">
      <tr>
        <th>Type <span class="red-text">*</span></th>
        <td>
          <select v-model="bin.type">
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
      <tr>
        <th>Quantity (number of bins) <span class="red-text">*</span></th>
        <td>
          <input type="number" name="quantity" v-model="bin.quantity" />
        </td>
      </tr>
      <tr>
        <th>Capacity <span class="red-text">*</span></th>
        <td class="same-line">
          <input type="number" name="capacity" v-model="bin.capacity" />
          <select v-model="bin.capacity_unit">
            <option value="litres">Litre</option>
            <option value="millilitres">Mililitre</option>
            <option value="cubic meters">Cubic Metre</option>
            <option value="gallons">Gallons</option>
            <option value="barrels">Barrels</option>
            <option value="cubic feet">Cubic Feet</option>
            <option value="cubic Inches">Cubic Inches</option>
            <option value="pints">Pints</option>
          </select>
        </td>
      </tr>
      <tr v-if="bin.location">
        <th>Location <span class="red-text">*</span></th>
        <td>
          <h2>Click on the map to move the point.</h2>
          <div id="map" class="map-td">
            {{ bin.location.lat }}-{{ bin.location.lng }}
          </div>
          <p>
            <b>Latitude:</b> {{ bin.location.lat }} - <b>Longitude:</b>
            {{ bin.location.lng }}
          </p>
        </td>
      </tr>
      <tr>
        <th>Description</th>
        <td>
          <textarea name="description" v-model="bin.description" />
        </td>
      </tr>
    </table>
  </div>
</template>

<script>
export default {
  data() {
    return {
      endpoint: "",
    };
  },
  props: {
    bin: {
      type: Object | Array,
      required: true,
    },
  },
  methods: {
    mapController() {
      const mapElement = document.getElementById(`map`);
      const latLng = new google.maps.LatLng(
        parseFloat(this.bin.location.lat),
        parseFloat(this.bin.location.lng)
      );

      const map = new google.maps.Map(mapElement, {
        center: latLng,
        zoom: 15,
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

      const marker = new google.maps.Marker({
        position: latLng,
        map: map,
      });

      map.addListener("click", (e) => {
        this.bin.location.lat = e.latLng.lat();
        this.bin.location.lng = e.latLng.lng();
        marker.setPosition(e.latLng);
        map.panTo(e.latLng);
      });
      a;
    },
    submitBtn() {
      $.ajax({
        url: this.endpoint,
        method: "PUT",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
        data: JSON.stringify({
          lat: parseFloat(this.bin.location.lat),
          lng: parseFloat(this.bin.location.lng),
          capacity: parseFloat(this.bin.capacity),
          capacity_unit: this.bin.capacity_unit,
          type: this.bin.type,
          description: this.bin.description,
          quantity: parseInt(this.bin.quantity),
        }),
        contentType: "application/json",
        success: (res) => {
          this.$emit("submitevent");
        },
        error: (err) => {
          alert("There was an error! Please try again later.");
          console.log(err);
        },
      });
    },
    cancelBtn() {
      this.$emit("cancelevent");
    },
  },
  mounted() {
    // const obj = Object.assign({}, this.bin);
    // this.bin = obj;
    this.endpoint = `api/bins/${this.bin.id}`;
    setTimeout(() => this.mapController(), 1000);
  },
};
</script>

<style scoped>
.same-line {
  display: flex;
  flex-direction: row;
}
</style>
