<template>
  <div class="mt-5 px-1">
    <h1>New Bin</h1>
    <table class="data_table">
      <tr v-if="instance==='admin'">
        <th>Public bin<span class="red-text">*</span></th>
        <td>
          <select v-model="publicSelection">
            <option selected value="false">No</option>
            <option value="true">Yes</option>
          </select>
        </td>
      </tr>
      <tr v-if="!isPublic && instance==='admin'">
        <th>Select large producer<span class="red-text">*</span></th>
        <td>
          <select v-model="w_producer_id">
            <option disabled selected :value="undefined">Please select producer</option>
            <option v-for="producer in largeProducers" :value="producer.id" :key="producer.id" >{{ producer.title }}</option>
          </select>
        </td>
      </tr>
      <tr>
        <th>Type <span class="red-text">*</span></th>
        <td>
          <select v-model="type">
            <option disabled value="">Please select one</option>
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
          <input type="number" name="quantity" v-model="quantity" />
        </td>
      </tr>
      <tr>
        <th>Capacity <span class="red-text">*</span></th>
        <td class="same-line">
          <input type="number" name="capacity" v-model="capacity" />
          <select v-model="capacity_unit">
            <option disabled value="">Please select one</option>
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
      <tr v-if="location">
        <th>Location <span class="red-text">*</span></th>
        <td>
          <h4>Click on the map to move the point.</h4>
          <div id="map" class="map-td">
            {{ location.lat }}-{{ location.lng }}
          </div>
          <p>
            <b>Latitude:</b> {{ location.lat }} - <b>Longitude:</b>
            {{ location.lng }}
          </p>
        </td>
      </tr>
      <tr>
        <th>Description</th>
        <td>
          <textarea name="description" v-model="description" />
        </td>
      </tr>
    </table>

    <br />

    <button class="button text-white" @click="submitBtn">Submit</button>
    <button class="button text-white" @click="cancelBtn">Cancel</button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      endpoint: "",
      location: {
        lat: 39.625795,
        lng: 19.922098,
      },
      capacity: 0,
      capacity_unit: "",
      type: "",
      description: "",
      quantity: 0,
      publicSelection: 'false',
      largeProducers: [],
      w_producer_id: undefined,
    };
  },
  props: {
    instance: {
      type: String,
      required: false,
    },
  producerid: {
      type: Number,
      required: false,
    },
  },
  computed: {
      isPublic() {
        if (this.publicSelection === 'true') return true;
        else return false;
    }
  },
  created() {
    if (this.instance === 'admin') this.getLargeProducers();
  },
  methods: {
    getLargeProducers () {
      $.ajax({
        url: `api/w_producers`,
        method: "GET",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
        contentType: "application/json",
        success: (res) => {
          this.largeProducers = res.results;
        },
        error: (err) => {
          console.error('Get producer errors', err);
        },
      });
    },
    mapController() {
      const mapElement = document.getElementById(`map`);
      const latLng = new google.maps.LatLng(
        parseFloat(mapElement.textContent.split("-")[0]),
        parseFloat(mapElement.textContent.split("-")[1])
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
        this.location.lat = e.latLng.lat();
        this.location.lng = e.latLng.lng();
        marker.setPosition(e.latLng);
        map.panTo(e.latLng);
      });
    },
    submitBtn() {
      var data;
      const bin ={
            lat: parseFloat(this.location.lat),
            lng: parseFloat(this.location.lng),
            capacity: parseFloat(this.capacity),
            capacity_unit: this.capacity_unit,
            type: this.type,
            description: this.description,
            quantity: parseInt(this.quantity),
            isPublic: this.isPublic
      };
      if (this.instance === 'admin' && !this.isPublic) {
        if (!this.w_producer_id) return alert('Please select a producer');
        bin.w_producer_id = this.w_producer_id;
      }

      console.log('bin:', bin);
      if (this.instance === "w_producer") {
        console.log('w_producer_id:', this.producerid);
        data = JSON.stringify({
          w_producer_id: this.producerid,
          bin,
        });
      } else {
        data = JSON.stringify(bin);
      }

      $.ajax({
        url: this.endpoint,
        method: "POST",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
        data,
        contentType: "application/json",
        success: (res) => {
          switch (this.instance) {
            case "w_producer":
              this.$emit("submitevent");
              window.location.href = "/list?table=bins&page=1";
              break;
            case "admin":
              window.location.href = "/list?table=bins&page=1";
              break;
            default:
              window.location.href = "/list?table=bins&page=1";
              break;
          }
        },
        error: (err) => {
          alert("There was an error! Please try again later.");
          console.error(err);
        },
      });
    },
    cancelBtn() {
      switch (this.instance) {
        case "w_producer":
          this.$emit("cancelevent");
          break;
        case "admin":
          window.location.href = "/list?table=bins&page=1";
          break;
        default:
          window.location.href = "/list?table=bins&page=1";
          break;
      }
    },
  },
  mounted() {
    switch (this.instance) {
      case "w_producer":
        this.endpoint = "api/w_producers/new_bin";
        break;
      case "admin":
        this.endpoint = "api/bins";
        break;
      default:
        this.endpoint = "api/bins";
        break;
    }

    navigator.geolocation.getCurrentPosition(
      async (pos) => {
        this.location = await {
          lat: pos.coords.latitude,
          lng: pos.coords.longitude,
        };

        setTimeout(() => {
          this.mapController();
        }, 250);
      },
      () => this.mapController()
    );
  },
};
</script>

<style scoped>
.same-line {
  display: flex;
  flex-direction: row;
}
</style>
