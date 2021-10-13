<head>
    @include('includes/head')
    <script>
        $(document).ready(() => {
            userAuth()
            const userObj = JSON.parse(localStorage.getItem('user'))
            if (userObj.role !== 'w_producer' && userObj.role !== 'w_producer_employee') window.location.href = '/login'
        });
    </script>
</head>

<body>
    <div class="st-container" id="vue-container">
        @include('navbar');

        @verbatim
        <div id="main-content" ref="mainContent">
            <div class="container text-center list_header">
                <h1>{{values.title | capitalize}}'s Management Panel</h1>
            </div>

            <div id="main-content-centered">

                <!-- Base Page -->
                <template v-if="action === 'nothing'">
                    <div class="stats">
                        <div v-if="values" class="column">
                            <h2><b>Contact Name:</b></h2>
                            <h3>{{values.contact_name}}</h3>
                            <h2><b>Contact Telephone:</b></h2>
                            <div class="telephones">
                                <h3 style="text-decoration: underline" v-if="!isMobile()">{{values.contact_telephone}}</h3>
                                <h3 style="text-decoration: underline" v-else @click="callPhone">{{values.contact_telephone}}</h3>
                            </div>
                            <h2><b>Description:</b></h2>
                            <h3>{{values.description}}</h3>
                        </div>
                        <div v-if="values" class="column">
                            <div id="main-map" ref="main-map" class="map-td" v-if="values.location">
                                {{values.location.lat}}-{{values.location.lng}}
                            </div>
                            <h2><b>Number of Employees:</b></h2>
                            <h3 v-if="values.users">{{values.users.length}}</h3>
                            <h2><b>Number of Bins:</b></h2>
                            <h3 v-if="values.bins">{{values.bins.length}}</h3>
                        </div>
                    </div><!-- end of stats -->

                    <button class="action" @click="changeAction('view_reports')">View Reports</button>
                    <button class="action" @click="changeAction('view_bins')">View Bins</button>
                </template><!-- end of action nothing -->

                <!-- View reports Action -->
                <div v-if="action === 'view_reports'">
                    <table class="data_table">
                        <tbody id="table_body">
                            <col v-for="title in titles" v-if="title !=='id'" :class="[title==='location' ? 'loc' : '',title.includes('photos') ? 'photos' : '']">
                            </col>
                            <tr class="titles">
                                <th v-for="title in titles" v-if="title !=='id'">{{title | capitalize}}</th>
                            </tr>
                            <tr v-for="value in reports" :key="value.id">
                                <td v-for="(val, title) in value" v-if="title !=='id'">
                                    <!-- Maps in td -->
                                    <div :id="'map' + value.id" :ref="'map' + value.id" class="map-td" v-if="title==='location'">
                                        {{val.lat}}-{{val.lng}}
                                    </div>

                                    <!-- Images -->
                                    <div v-else-if="title.includes('photos')" class="img-container">
                                        <div v-if="val.length > 0">
                                            Total images: {{val.length}}<br>
                                            <img :src="val[0]">
                                        </div>
                                        <div v-if="val.length <= 0">No available images</div>
                                    </div>

                                    <!-- Bins -->
                                    <div v-else-if="title==='bins'">
                                        <a v-for="bin in val" href="#" class="bin">
                                            {{bin}}
                                        </a>
                                    </div>

                                    <!-- Created / Updated dates -->
                                    <span v-else-if="title.includes('at') || title.includes('from') || title.includes('to')">
                                        {{val | date}}
                                    </span>

                                    <!-- Everything else -->
                                    <span v-else>
                                        {{val}}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <button class="action" @click="changeAction('view_bins')">View Bins</button>
                    <button class="action" @click="changeAction('nothing')">Back</button>
                </div><!-- end of action view_reports -->

                <!-- View bins Action -->
                <div v-if="action === 'view_bins'">
                    <div v-for="(bin, index) in bins" :key="bin.id">
                        <h2>
                            <p style="text-decoration: underline; font-size: 1.5em;display: inline;margin-right:1rem;">Bin #{{index + 1}}:</p>
                            <button class="info-edit" @click="showInfo(index)">Info</button>
                            <button class="info-edit" @click="newReport(bin.id, bin.location)">New Report</button>
                            <button class="info-edit" style="background-color: red" @click="deleteBin(index)">Delete</button>
                        </h2>
                        <div class="bin-stats" style="display: none" :id="'bin' + index" :ref="'bin' + index">
                            <p><b style="text-decoration: underline">Type:</b> {{bin.type + " " | capitalize_singular}}</p>
                            <p><b style="text-decoration: underline">Number of Bins:</b> {{bin.quantity}}</p>
                            <p><b style="text-decoration: underline">Capacity:</b> {{bin.capacity}} {{bin.capacity_unit + " "| capitalize_singular}}</p>
                            <div :id="'map' + bin.id" :ref="'map' + bin.id" class="map-td bin-map">
                                {{bin.location.lat}}-{{bin.location.lng}}
                            </div>
                            <p class="description"><b style="text-decoration: underline">Bin Description:</b> {{bin.description}}</p>
                            <button class="info-edit" @click="editBin(index)">Edit Bin</button>
                        </div>
                    </div>

                    <button class="action" @click="changeAction('add_bin')">Add Bin</button>
                    <button class="action" @click="changeAction('nothing')">Back</button>
                </div><!-- end of action view_bins -->

                <template v-if="action==='add_bin'">
                    <div id="app">
                        <!-- Add bin -->
                        <create-bin :w_producer="values" :instance="'w_producer'" @cancelevent="onCancelCreate" @submitevent="onSubmitCreate"></create-bin>
                    </div>
                </template>

                <template v-if="action==='edit_bin'">
                    <div id="app">
                        <!-- Edit bin -->
                        <update-bin :bin="targetBin" @cancelevent="onCancelUpdate" @submitevent="onSubmitUpdate"></update-bin>
                    </div>
                </template>

            </div><!-- end of main-content-centered -->
        </div><!-- end of main-content -->
        @endverbatim

        @include('footer');
    </div>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAWBSaVKwwQ4OFN7xtLC6sCmqkMOW-zJ-8" async defer></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script type="module">
        var app = new Vue({
            el: '#vue-container',
            data: {
                action: 'nothing',
                values: [],
                bins: [],
                reprots: [],
                titles: [],
                targetBin: null,
            },
            filters: {
                capitalize: function(value) {
                    if (!value) return ''
                    value = value.toString()
                    return value.charAt(0).toUpperCase() + value.slice(1)
                },
                date: function(value) {
                    if (value) {
                        const date = moment(String(value)).format('DD/MM/YYYY HH:mm');
                        return date.toLowerCase() !== 'invalid date' ? date : value
                    }
                },
                capitalize_singular: function(value) {
                    if (!value) return ''
                    value = value.toString()
                    value = value.charAt(0).toUpperCase() + value.slice(1)
                    return value.slice(0, -1);
                }
            },
            methods: {
                initMap() {
                    const mapElement = document.getElementById("main-map");
                    const latLng = new google.maps.LatLng(parseFloat(mapElement.textContent.split("-")[0]), parseFloat(mapElement.textContent.split("-")[1]))

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
                        scaleControl:false,
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
                        title: "Business Location"
                    });
                },
                fixHeight(event) {
                    // Main conent takes up more than one viewport in height, set its height to auto so the footer does not overlap
                    this.$refs.mainContent.style.marginTop = '30px'; // this.$refs.navBar.clientHeight + 'px';
                    // this.$refs.mainContent.style.marginBottom = '30px';

                    // $("#main-content").css('margin-top', $(".navbar-fixed-top").height() + 20);
                },
                getTitles() {
                    const firstElement = this.reports[0];
                    this.titles = Object.keys(firstElement);
                },
                changeAction(action) {
                    this.action = action;

                    if (action === 'nothing')
                        setTimeout(()=>this.initMap(), 100)
                },
                editBin(binIndex) {
                    this.targetBin = this.bins[binIndex];
                    this.action = 'edit_bin';
                },
                deleteBin(binIndex) {
                    if(confirm("Are you sure you want to delete this bin? This action is not reversable!"))
                    {
                        $.ajax({
                            url: `api/w_producers/${this.values.id}/bins`,
                            method: "DELETE",
                            headers: {
                                Authorization: `Bearer ${localStorage.getItem("token")}`
                            },
                            data: JSON.stringify({
                                bin_id: this.bins[binIndex].id
                            }),
                            contentType: "application/json",
                            success: res => {
                                this.bins.splice(binIndex, 1);
                            },
                            error: err => {
                                alert("There was an error! Please try again later.");
                                console.log(err);
                            }
                        });
                    }
                },
                newReport(binId, location) {
                    window.location.href = `/report?bin=${binId}&lat=${location.lat}&lng=${location.lng}&w_producer=${this.values.id}`
                },
                getReports() {
                    $.ajax({
                        url: `api/reports/w_producer/${this.values.id}`,
                        method: "GET",
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem("token")}`,
                        },
                        success: (res) => {
                            this.reports = res;
                            if (res.hasOwnProperty('data')) this.reports = res.data;
                            if (res.hasOwnProperty('results')) this.reports = res.results;

                            this.getTitles();
                        },
                        error: (err) => {
                            console.log(err)
                        }
                    });
                },
                isMobile() {
                    if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) return true
                    return false
                },
                callPhone() {
                    window.open(`tel:+30${this.phoneNumber}`)
                },
                onCancelCreate(){
                    // update-bin cancel
                    this.changeAction("view_bins");
                },
                onSubmitCreate(){
                    // update-bin submit
                    window.location.href = window.location;
                },
                onCancelUpdate(){
                    // update-bin cancel
                    this.changeAction("view_bins");
                },
                onSubmitUpdate(){
                    // update-bin submit
                    window.location.href = window.location;
                },
                showInfo(index) {
                    const targetId = `bin${index}`;

                    if (this.$refs[targetId][0].style.display === 'none')
                    {
                        this.$refs[targetId][0].style.display = 'unset';

                        setTimeout(()=>{
                            const mapElement = document.getElementById(`map${this.bins[index].id}`);
                            const latLng = new google.maps.LatLng(parseFloat(mapElement.textContent.split("-")[0]), parseFloat(mapElement.textContent.split("-")[1]))

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
                                scaleControl:false,
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

                            let icon = "/images/markers/";

                            switch (this.bins[index].type)
                            {
                                case 'compost':
                                    icon += "waste_container_yellow.vsmall.png";
                                    break;
                                case 'glass':
                                    icon += "waste_container_blue.vsmall.png";
                                    break;
                                case 'recyclable':
                                    icon += "waste_container_blue.vsmall.png";
                                    break;
                                case 'mixed':
                                    icon += "waste_container_green.vsmall.png";
                                    break;
                                case 'metal':
                                    icon += "waste_container_blue.vsmall.png";
                                    break;
                                case 'paper':
                                    icon += "waste_container_yellow.vsmall.png";
                                    break;
                                case 'plastic':
                                    icon += "waste_container_blue.vsmall.png";
                                    break;
                            }

                            const marker = new google.maps.Marker({
                                position: latLng,
                                icon: icon,
                                map: map,
                            });
                        }, 100);
                    }
                    else
                        this.$refs[targetId][0].style.display = 'none'
                }
            },
            created() {
                $.ajax({
                    url: `api/w_producers/from_user_id/${localStorage.getItem('userId')}`,
                    method: "GET",
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                    success: (res) => {
                        this.values = res[0];

                        if (res.hasOwnProperty('data')) this.values = res.data;
                        if (res.hasOwnProperty('results')) this.values = res.results;

                        if (this.values.bins.length > 0)
                        {
                            this.values.bins.forEach(binId => {
                                $.ajax({
                                    url: `api/bins/${binId}`,
                                    method: "GET",
                                    headers: {
                                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                                    },
                                    success: (res) => {
                                        this.bins.push(res);
                                    },
                                    error: (err) => {
                                        console.error(err)
                                    },
                                });
                            });
                        }
                    },
                    error: (err) => {
                        console.error(err)
                    },
                });
            },
            mounted() {
                setTimeout(() =>
                {
                    this.fixHeight();
                    this.initMap();

                    this.getReports();

                    window.addEventListener('resize', this.fixHeight());

                    window.addEventListener('beforeunload', (event) => {
                        window.removeEventListener('resize', this.fixHeight());

                        delete e['returnValue'];
                    });
                }, 250);
            }
        });
    </script>

    <style scoped>
        #main-content-centered {
            text-align: center;
        }

        .stats {
            margin: 3em auto;
            max-width: 50%;
        }

        .column {
            float: left;
            width: 50%;
        }

        .stats:after {
            content: "";
            display: table;
            clear: both;
        }

        .bin-stats {
            font-size: 1.5em;
        }

        .bin-map {
            margin: 1em auto;
            max-width: 25%;
        }

        .description {
            max-width: 50%;
            margin: auto;
        }

        .action {
            height: 3em;
            font-size: 3em;
            color: black;
            background-color: #3066be;
            padding: 1em;
            border-radius: 25px;
            margin: 2em 1em;
        }

        .info-edit {
            margin: 0.25em;
            border-radius: 15px;
            color: black;
            background-color: #3066be;
            padding: 0.5em;
        }
    </style>

    @include('includes/scripts', ['includeMap' => false])
</body>
