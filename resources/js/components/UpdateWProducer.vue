<template>
  <table class="data_table">
    <tr>
      <th>Id</th>
      <td>{{ w_producer.id }}</td>
    </tr>
    <tr>
      <th>Title</th>
      <td>
        <input type="text" name="title" id="title" v-model="w_producer.title" />
      </td>
    </tr>
    <tr>
      <th>Join pin</th>
      <td>
        <input
          type="text"
          maxlength="4"
          name="join_pin"
          id="join_pin"
          v-model="w_producer.join_pin"
        />
      </td>
    </tr>
    <tr v-if="w_producer.location">
      <th>Location</th>
      <td>
        <h2>Click on the map to move the point.</h2>
        <div id="map" class="map-td">
          {{ w_producer.location.lat }}-{{ w_producer.location.lng }}
        </div>
        <p>
          <b>Latitude:</b> {{ w_producer.location.lat }} - <b>Longitude:</b>
          {{ w_producer.location.lng }}
        </p>
      </td>
    </tr>
    <tr>
      <th>Person of contact</th>
      <td>
        <input
          type="text"
          name="contact_name"
          id="contact_name"
          v-model="w_producer.contact_name"
        />
      </td>
    </tr>
    <tr>
      <th>Contact number</th>
      <td>
        <input
          type="text"
          name="contact_telephone"
          id="contact_telephone"
          v-model="w_producer.contact_telephone"
        />
      </td>
    </tr>
    <tr>
      <th>Description</th>
      <td>
        <textarea
          name="description"
          id="description"
          v-model="w_producer.description"
          cols="30"
          rows="10"
        ></textarea>
      </td>
    </tr>
    <tr>
      <th>Users</th>
      <td>
        <span
          v-for="(user, index) in w_producer.users"
          :key="'user' + index"
          type="number"
          :name="'user' + index"
          :id="'user' + index"
          class="bin"
        >
          <a :href="'/row?table=users&id=' + user.value">{{ user.value }}</a>
        </span>
      </td>
    </tr>
    <tr>
      <th>Bins</th>
      <td>
        <span
          v-for="(bin, index) in w_producer.bins"
          :key="'bin' + index"
          type="number"
          :name="'bin' + index"
          :id="'bin' + index"
          class="bin"
        >
          <a :href="'/row?table=bins&id=' + bin.value">{{ bin.value }}</a>
        </span>
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
              value="1"
              v-model="w_producer.is_approved"
            />
            <label for="yes">Yes</label>
          </div>
          <div class="radio-container">
            <input
              type="radio"
              id="no"
              value="0"
              v-model="w_producer.is_approved"
            />
            <label for="no">No</label>
          </div>
        </fieldset>
      </td>
    </tr>
    <tr>
      <th>Created at</th>
      <td>
        {{ w_producer["created_at"] }}
      </td>
    </tr>
    <tr>
      <th>Updated at</th>
      <td>
        {{ w_producer["updated_at"] }}
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
    w_producer: {
      type: Object | Array,
      required: true,
    },
  },
  methods: {
    mapController() {
      const mapElement = document.getElementById(`map`);
      const latLng = new google.maps.LatLng(
        parseFloat(this.w_producer.location.lat),
        parseFloat(this.w_producer.location.lng)
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
        this.w_producer.location.lat = e.latLng.lat();
        this.w_producer.location.lng = e.latLng.lng();
        marker.setPosition(e.latLng);
        map.panTo(e.latLng);
      });
      a;
    },
  },
  mounted() {
    setTimeout(() => {
      this.w_producer.bins = this.w_producer.bins.map((bin) => {
        return { value: bin };
      });
      this.w_producer.users = this.w_producer.users.map((user) => {
        return { value: user };
      });

      this.w_producer.is_approved =
        this.w_producer.is_approved === "yes" ? 1 : 0;

      this.mapController();
    }, 750);
  },
};
</script>
