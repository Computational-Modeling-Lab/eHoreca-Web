<template>
    <div class="mt-5 px-1">
        <table class="data_table">
            <tr>
                <th>Title</th>
                <td>
                    <input
                        type="text"
                        name="title"
                        id="title"
                        v-model="title"
                    />
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
                        v-model="join_pin"
                    />
                </td>
            </tr>
            <tr>
                <th>Location</th>
                <td id="map"></td>
            </tr>
            <tr>
                <th>Person of contact</th>
                <td>
                    <input
                        type="text"
                        name="contact_name"
                        id="contact_name"
                        v-model="contact_name"
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
                        v-model="contact_telephone"
                    />
                </td>
            </tr>
            <tr>
                <th>Description</th>
                <td>
                    <textarea
                        name="description"
                        id="description"
                        v-model="description"
                        cols="30"
                        rows="10"
                    ></textarea>
                </td>
            </tr>
        </table>

        <br>

        <button class="button text-white" @click="submitBtn">Submit</button>
        <button class="button text-white" @click="cancelBtn">Cancel</button>
    </div>
</template>

<script>
export default {
    data() {
        return {
            title: "",
            join_pin: "",
            contact_name: "",
            contact_telephone: "",
            description: "",
            lat: 39.625795,
            lng: 19.922098
        };
    },
    methods: {
        submitBtn() {
            $.ajax({
                url: `api/w_producers`,
                method: "POST",
                headers: {
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                },
                data: JSON.stringify({
                    title: this.title,
                    join_pin: this.join_pin,
                    lat: this.lat,
                    lng: this.lng,
                    contact_name: this.contact_name,
                    contact_telephone: this.contact_telephone,
                    description: this.description,
                    users: [],
                    bins: []
                }),
                dataType: "json",
                contentType: "application/json",
                success: res =>
                    (window.location.href = "/list?table=w_producers&page=1")
            });
        },
        cancelBtn() {
            window.location.href = "/list?table=w_producers&page=1"
        },
        initMap() {
            const mapElement = document.getElementById(`map`);
            const latLng = new google.maps.LatLng(this.lat, this.lng);

            const map = new google.maps.Map(mapElement, {
                center: latLng,
                zoom: 15,
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                    position: google.maps.ControlPosition.TOP_LEFT
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

            const marker = new google.maps.Marker({
                position: latLng,
                map: map,
                draggable: true
            });
            marker.addListener("dragend", event => {
                this.lat = event.latLng.lat();
                this.lng = event.latLng.lng();
            });

            map.addListener("click", e => {
                this.lat = e.latLng.lat();
                this.lng = e.latLng.lng();
                marker.setPosition(e.latLng);
                map.panTo(e.latLng);
            });
        }
    },
    mounted() {
        setTimeout(() => {
            this.initMap();
        }, 250);
    }
};
</script>

<style scoped>
#map {
    height: 500px;
}
</style>
