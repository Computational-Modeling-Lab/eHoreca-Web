<template>
  <table class="data_table">
    <tr>
      <th>Id</th>
      <td>{{ report.id }}</td>
    </tr>
    <tr>
      <th>Bin</th>
      <td>
        <input type="number" name="bin" id="bin" v-model="report.bin" />
      </td>
    </tr>
    <tr>
      <th>Created by</th>
      <td>{{ report["created by"] }}</td>
    </tr>
    <tr v-if="report.location">
      <th>Location</th>
      <td id="map"></td>
    </tr>
    <tr>
      <th>Location accuracy</th>
      <td>
        <input
          type="text"
          name="location_accuracy"
          id="location_accuracy"
          v-model="report['location accuracy']"
        />
      </td>
    </tr>
    <tr>
      <th>Issue</th>
      <td>
        <select v-model="report.issue">
          <option disabled value="">Please select one</option>
          <option value="bin full">bin full</option>
          <option value="bin almost full">bin almost full</option>
          <option class="bin damaged">bin damaged</option>
          <option class="bin missing">bin missing</option>
        </select>
      </td>
    </tr>
    <tr v-for="(photo, index) in report['report photos']" :key="photo">
      <th>Photo {{ index + 1 }}</th>
      <td>
        <div class="edit-page-photo-container">
          <img :src="photo" />
          <span
            ><img
              src="images/icons/delete-black-18dp.svg"
              alt="delete photo"
              @click="onClickDeletePhotoHandler(index)"
          /></span>
        </div>
      </td>
    </tr>
    <tr>
      <th>Comment</th>
      <td>
        <textarea
          name="comment"
          id="comment"
          rows="5"
          v-model="report.comment"
        ></textarea>
      </td>
    </tr>
    <tr>
      <th>Approved</th>
      <td>
        <fieldset>
          <div class="radio-container">
            <input
              type="radio"
              id="yes"
              value="Yes"
              v-model="report.approved"
            />
            <label for="yes">Yes</label>
          </div>
          <div class="radio-container">
            <input type="radio" id="no" value="No" v-model="report.approved" />
            <label for="no">No</label>
          </div>
        </fieldset>
      </td>
    </tr>
    <tr>
      <th>Created at</th>
      <td>
        {{ report["created at"] }}
      </td>
    </tr>
    <tr>
      <th>Updated at</th>
      <td>
        {{ report["updated at"] }}
      </td>
    </tr>
  </table>
</template>

<script>
export default {
  data() {
    return {};
  },
  props: {
    report: {
      type: Object | Array,
      required: true,
    },
  },
  methods: {
    onClickDeletePhotoHandler(imgIndex) {
      if (confirm("This action cannot be reversed. Are you sure?")) {
        axios
          .delete(`api/reports/${this.report.id}/photos`, {
            headers: {
              Authorization: `Bearer ${localStorage.getItem("token")}`,
            },
            data: {
              index: imgIndex,
            },
          })
          .then((res) => {
            window.location.reload();
          });
      }
    },
    initMap() {
      const mapElement = document.getElementById(`map`);

      const latLng = new google.maps.LatLng(
        parseFloat(this.report.location.lat),
        parseFloat(this.report.location.lng)
      );

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

      const marker = new google.maps.Marker({
        position: latLng,
        map: map,
        draggable: true,
      });
      marker.addListener("dragend", (event) => {
        this.report.location.lat = event.latLng.lat();
        this.report.location.lng = event.latLng.lng();
      });

      map.addListener("click", (e) => {
        this.report.location.lat = e.latLng.lat();
        this.report.location.lng = e.latLng.lng();
        marker.setPosition(e.latLng);
        map.panTo(e.latLng);
      });
    },
  },
  mounted() {
    setTimeout(() => {
      this.initMap();
    }, 1000);
  },
};
</script>

<style scoped>
#map {
  height: 500px;
}
</style>
