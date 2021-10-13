<head>
    @include('includes/head')
    <script>
        $(document).ready(() => {
            userAuth()
        });
    </script>
</head>

<body>
    <div class="st-container" id="vue-container">
        @include('navbar');
        <div id="main-content" ref="mainContent">
            @verbatim
            <div class="container text-center list_header">
                <h1>{{title | titleSanitize}}</h1>
            </div>
            <template v-if="!isEditing">
                <table class="data_table">
                    <template v-for="(value, title) in values">
                        <template v-if="!title.includes('photos') && !title.includes('Vehicle') && !title.includes('VehicleId')">
                            <tr>
                                <th>{{ title | titleSanitize }}</th>
                                <td>
                                    <div v-if="title==='bins' || title==='users'">
                                        <span v-for="item in value" class="bin">
                                            <a :href="'/row?table=' + title + '&id=' + item">
                                                {{ item }}
                                            </a>
                                        </span>
                                    </div>
                                    <div v-else-if="title==='location'" class="map-td" :id="'map' + id" :ref="'map' + id">{{ value.lat }}-{{ value.lng }}</div>
                                    <span v-else-if="title.includes('at') || title.includes('from') || title.includes('to')">{{ value | date }}</span>
                                    <span v-else-if="title.toLowerCase()==='ispublic' || title.toLowerCase()==='is_public'">{{ value | yesNo }}</span>
                                    <span v-else-if="title.toLowerCase() === 'outcome'">{{ (value == null || value == undefined) ? 'Incomplete' : 'Completed' }}</span>
                                    <span v-else>{{ value | capitalize }}</span>
                                </td>
                            </tr>
                        </template>
                        <template v-else-if="title==='targetVehicle'">
                            <tr>
                                <th>Target Vehicle</th>
                                <td>
                                    {{ value.municipality }} <b>|</b> {{ value.plates }} <b>|</b> {{ value.type }}
                                </td>
                        </tr>
                        </template>
                        <template v-else-if="!title.includes('VehicleId')">
                            <tr v-for="(img, index) in value">
                                <th>{{title | titleSanitize}} {{index + 1}}</th>
                                <td><img :src="img"></td>
                            </tr>
                        </template>
                    </template>
                </table>
            </template>

            <!-- Edit mode -->
            <template v-else>
                <div id="app">
                    <h1>Edit Mode</h1>
                    <template v-if="table==='routes'">
                        <update-route :route="values" />
                    </template>
                    <template v-else-if="table==='heatmaps'">
                        <update-heatmap :heatmap="values" />
                    </template>
                    <template v-else-if="table==='reports'">
                        <update-report :report="values" />
                    </template>
                    <template v-else-if="table==='users'">
                        <update-user :user="values" />
                    </template>
                    <template v-else-if="table==='vehicles'">
                        <update-vehicle :vehicle="values" />
                    </template>
                    <template v-else-if="table==='bins'">
                        <update-bin :bin="values" />
                    </template>
                    <template v-else>
                        <update-wproducer :w_producer="values" />
                    </template>

                </div>
            </template>
        </div>

        <div class="control-buttons">
            <button v-if="!isEditing && role === 'admin' && ((table === 'reports' && values.approved !== 'Yes') || (table === 'w_producers' && values.is_approved !== 'Yes'))" class="button text-white" @click="approveReport">Approve</button>
            <button v-if="!isEditing && role === 'admin' && values.approved !== 'Yes'" class="button text-white" @click="edit">Edit</button>
            <button v-if="!isEditing && role === 'admin'" class="button text-white" @click="destroy">Delete</button>
            <button v-if="isEditing  && (role === 'admin' || ((role === 'w_producer' || role === 'w_producer_employee') && table === 'bins'))" class="button text-white" @click="saveEdit">Save</button>
            <button v-if="isEditing  && (role === 'admin' || ((role === 'w_producer' || role === 'w_producer_employee') && table === 'bins'))" class="button text-white" @click="cancelEdit">Cancel</button>
            <button v-if="!isEditing" class="button text-white" @click="goBack">Go Back</button>
        </div>
        @endverbatim

        @include('footer')
    </div>
    <script src="{{ asset('js/app.js') }}"></script>

    <script type="module">
        var app = new Vue({
            el: '#vue-container',
            data: {
                title: "",
                table: new URLSearchParams(window.location.search).get('table'),
                id: new URLSearchParams(window.location.search).get('id'),
                isEditing: new URLSearchParams(window.location.search).get('edit') || false,
                page: localStorage.page,
                role: localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')).role : null,
                titles: [],
                values: [],
                nonEditable: ['id', 'bin', 'created_at', 'updated_at'],
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
                },
                titleSanitize: function(value) {
                    if (!value) return ''
                    if (value.includes('w_producer')) {
                        return `Large Producer ${value.charAt(value.length - 1)}`
                    }
                    value = value.replace(/_/g, ' ')
                    return value.charAt(0).toUpperCase() + value.slice(1)
                },
                yesNo: function(value) {
                    return (value == 1) ? 'Yes' : 'No'
                }
            },
            methods: {
                fixHeight(event) {
                    // Main conent takes up more than one viewport in height, set its height to auto so the footer does not overlap
                    this.$refs.mainContent.style.marginTop = '25px'; // this.$refs.navBar.clientHeight + 'px';
                    this.$refs.mainContent.style.marginBottom = '25px';

                    // $("#main-content").css('margin-top', $(".navbar-fixed-top").height() + 20);
                },
                getTitles() {
                    const firstElement = this.values[0];
                    this.titles = Object.keys(firstElement)
                },
                approveReport()
                {
                    // PUT request with approved field
                    $.ajax({
                        url: `api/${this.table}/approve/${this.id}`,
                        method: "PUT",
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem("token")}`,
                        },
                        success: (res) => {
                            this.cancelEdit();
                        },
                        error: (err) =>
                        {
                            alert('Error!')
                            console.error(err)
                        },
                    });
                },
                edit()
                {
                    window.location.href = window.location.search + "&edit=true"
                },
                destroy()
                {
                    if (confirm("This action cannot be reversed. Are you sure?"))
                    {
                        // DELETE request
                        $.ajax({
                            url: `api/${this.table}/${this.id}`,
                            method: "DELETE",
                            headers: {
                                Authorization: `Bearer ${localStorage.getItem("token")}`,
                            },
                            success: (res) => {
                                this.goBack();
                            },
                            error: (err) =>
                            {
                                alert('Error!')
                                console.error(err)
                            },
                        });
                    }
                },
                saveEdit()
                {
                    var url = `api/${this.table}/${this.id}`;

                    if(this.table === 'routes'){
                        // this.values.bins = this.values.bins.map(bin => parseInt(bin.value))
                        this.values.bins = this.values.bins.map(Number)

                        url = `api/vehicle_route/${this.id}`;

                        this.values.targetVehicle = this.values.targetVehicleId;

                        delete this.values.targetVehicleId;
                    }

                    if(this.table === 'reports'){
                        this.values['lat'] = this.values.location.lat;
                        this.values['lng'] = this.values.location.lng;
                        delete this.values.location;
                    }

                    if(this.table === 'bins'){
                        this.values['lat'] = this.values.location.lat;
                        this.values['lng'] = this.values.location.lng;
                        delete this.values.location;
                    }

                    if(this.table === 'w_producers'){
                        this.values['lat'] = this.values.location.lat;
                        this.values['lng'] = this.values.location.lng;
                        delete this.values.location;

                        this.values.bins = this.values.bins.map(bin => parseInt(bin.value))
                        this.values.users = this.values.users.map(user => parseInt(user.value))
                    }

                    delete this.values['created by'];
                    delete this.values['created at'];
                    delete this.values['updated at'];

                    // PUT request here with the edited values
                    $.ajax({
                        url: url,
                        method: "PUT",
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem("token")}`,
                            contentType: 'application/json',
                        },
                        data: this.values,
                        success: (res) => {
                            this.cancelEdit();
                        },
                        error: (err) =>
                        {
                            alert('Error!')
                            console.error(err)
                        },
                    });
                },
                cancelEdit()
                {
                    window.location.href = window.location.search.split("&edit")[0];
                },
                goBack()
                {
                    if (this.page)
                        window.location.href = `list?table=${this.table}&page=${this.page}`
                    else
                        window.location.href = `list?table=${this.table}&page=1`
                }
            },
            created() {
                $.ajax({
                    url: `api/${this.table}/${this.id}`,
                    method: "GET",
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                    success: (res) => {
                        this.values = res;
                        this.title = this.table.slice(0, -1) + ' ' + this.id;

                        if (res.hasOwnProperty('data')) this.values = res.data

                        if (this.table === 'routes')
                        {
                            this.values.type = '';
                            this.values.targetVehicle = {};

                            $.ajax({
                                url: `api/vehicle_route/${this.id}`,
                                method: "GET",
                                headers: {
                                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                                },
                                success: res =>
                                {
                                    this.values.type = res.type;

                                    $.ajax({
                                        url: `api/vehicles/${res.vehicle_id}`,
                                        method: "GET",
                                        dataType: "json",
                                        context: this,
                                        headers: {
                                            Authorization: `Bearer ${localStorage.getItem("token")}`,
                                        },
                                        success: vehicle =>
                                        {
                                            this.values.targetVehicle = vehicle.data
                                            this.values.targetVehicleId = this.values.targetVehicle.id
                                            this.$forceUpdate();
                                        },
                                        error: (err) =>
                                        {
                                            console.error(err)
                                        },
                                    })
                                }
                            })
                        }
                    },
                    error: (err) =>
                    {
                        console.error(err)
                    },
                });
            },
            mounted() {
                this.fixHeight();
                localStorage.removeItem('page');
                window.addEventListener('resize', this.fixHeight())
            },
            beforeDestroy()
            {
                window.removeEventListener('resize', this.fixHeight())
            }
        })
    </script>
    @include('includes/scripts', ['includeMap' => false])
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAWBSaVKwwQ4OFN7xtLC6sCmqkMOW-zJ-8&amp;callback=initMaps" async defer></script>

</body>
