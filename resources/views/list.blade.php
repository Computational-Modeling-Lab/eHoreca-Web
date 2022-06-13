<head>
    @include('includes/head')
    <script>
        $(document).ready(() => {
            if (new URLSearchParams(window.location.search).get('table') !== 'reports') userAuth()
        });
    </script>
</head>

<body>
    <div class="st-container" id="vue-container">
        @include('navbar');

        @verbatim
        <div id="main-content" ref="mainContent">
            <div class="container text-center list_header">
                <h1>{{title | titleSanitize}}</h1>
                <div v-if="token !== null && (role==='admin' || (role==='w_producer' && title==='bins'))">
                    <button v-if="title !== 'reports'" class="btn btn-secondary" type="button" @click="onCreateNewHandler">Create new {{title.slice(0, -1) | titleSanitize}}</button>
                </div>
            </div>
            <table class="data_table">
                <tbody id="table_body">
                    <col v-for="title in titles" v-if="title !=='id' && title!=='created at' && title!=='updated at' && title!=='created_at' && title!=='updated_at'" :class="[title==='location' ? 'loc' : '',title.includes('photos') ? 'photos' : '']">
                    </col>
                    <tr class="titles">
                        <th v-for="title in titles" v-if="title !=='id' && title!=='created at' && title!=='updated at' && title!=='created_at' && title!=='updated_at'">{{title | titleSanitize}}</th>
                    </tr>
                    <tr v-for="value in toShow" :key="value.id" @click="onRowClickHandler(value.id)">
                        <td v-for="(val, title) in value" v-if="title !=='id' && title!=='created at' && title!=='updated at' && title!=='created_at' && title!=='updated_at'">
                            <!-- Maps in td -->
                            <div :id="'map' + value.id" :ref="'map' + value.id" class="map-td" v-if="title==='location'">
                                {{val.lat}}-{{val.lng}}
                            </div>

                            <!-- Images -->
                            <div v-else-if="title.includes('photos')" class="img-container">
                                <div v-if="val.length > 0">
                                    <img :src="assets + '/' + val[0]" class="report-photo">
                                </div>
                                <div v-if="val.length <= 0">No available images</div>
                            </div>

                            <!-- Bins and users -->
                            <div v-else-if="title==='bins' || title==='users'">
                                <span v-for="item in val" class="bin">
                                    <a :href="'/row?table=' + title + '&id=' + item">
                                        {{ item }}
                                    </a>
                                </span>
                            </div>

                            <!-- Bin in report -->
                            <div v-else-if="title==='bin'" class="bin">
                                <span>
                                    <a :href="'/row?table=bins&id=' + val">
                                    {{ val }}
                                </a>
                                </span>
                            </div>

                            <!-- Is public -->
                            <span v-else-if="title.toLowerCase()==='ispublic' || title.toLowerCase()==='is public'">{{ val | yesNo }}</span>

                            <!-- Outcome -->
                            <span v-else-if="title.toLowerCase() === 'outcome'">{{ (val == null || val == undefined) ? 'Incomplete' : 'Complete' }}</span>

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
        </div>

        <div class="page-selector">
            <span class="page"><b>Page:</b></span>
            <span :class="[parseInt(currentPage) - 1 <= 0 ? 'currentPage' : 'pageNumbers']" @click="pageSelector('-1')">Prev</span>
            <span v-for="page of toShowPages" :class="[page == parseInt(currentPage) ? 'currentPage' : 'pageNumbers']" @click="pageSelector(page)">{{page}}</span>
            <span :class="[parseInt(currentPage) + 1 > totalPages ? 'currentPage' : 'pageNumbers']" @click="pageSelector('+1')">Next</span>
        </div>
        @endverbatim

        @include('footer')
    </div>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAWBSaVKwwQ4OFN7xtLC6sCmqkMOW-zJ-8&amp;callback=initMaps" async defer></script>

    <script type="application/javascript">
        var app = new Vue({
            el: '#vue-container',
            data: {
                title: new URLSearchParams(window.location.search).get('table'),
                currentPage: new URLSearchParams(window.location.search).get('page') || 1,
                titles: [],
                values: [],
                ownValues: [],
                toShow: [],
                totalPages: 0,
                ownTotalPages: 0,
                toShowPages: 0,
                viewOwn: (localStorage.getItem('showOwn') === 'true') || false,
                token: localStorage.getItem('token'),
                role: localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')).role : '',
                assets: "{{ asset('storage') }}",
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
                    if (value === 'w_producers') {
                        return 'Large Producers'
                    }
                    if (value.includes('w_producer')) {
                        return 'Large Producer'
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
                onRowClickHandler(id) {
                    localStorage.page = this.currentPage
                    window.location.href = `row?table=${this.title}&id=${id}`
                },
                pageSelector(page) {
                    const totalPages = this.viewOwn ? this.ownTotalPages : this.totalPages;
                    var targetPage = parseInt(this.currentPage);

                    if (page === parseInt(this.currentPage))
                        return;
                    else if (page === "-1") {
                        if (targetPage - 1 <= 0)
                            return;

                        targetPage = targetPage + parseInt(page);
                    } else if (page === "+1") {
                        if (targetPage + 1 > totalPages)
                            return;

                        targetPage = targetPage + parseInt(page);
                    } else
                        targetPage = parseInt(page);

                    window.location.href = `list?table=${this.title}&page=${targetPage}`;
                },
                changeShowing() {
                    if (!this.viewOwn) {
                        this.toShow = this.ownValues;
                        this.toShowPages = this.ownTotalPages;
                    } else {
                        this.toShow = this.values;
                        this.toShowPages = this.totalPages;
                    }

                    window.initMaps();
                },
                onCreateNewHandler() {
                    if(this.title === 'reports')
                        window.location.href = '/report'
                    else
                        window.location.href = `/create?entity=${this.title}`
                }
            },
            created() {
                const binId = new URLSearchParams(window.location.search).get('bin_id');
                let url = `api/${this.title}?page=${this.currentPage}`;
                const userId = localStorage.getItem('userId');
                if (userId) url = url + `&user_id=${userId}`;
                if (binId && this.title === 'reports') url = `api/bins/reports/${binId}`
                
                if (this.values.length <= 0) {
                    // All values
                    $.ajax({
                        url,
                        method: "GET",
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem("token")}`,
                        },
                        success: (res) => {
                            this.values = res;

                            if (res.hasOwnProperty('data')) this.values = res.data;
                            if (res.hasOwnProperty('results')) this.values = res.results;
                            if (res.hasOwnProperty('total_pages')) this.totalPages = res.total_pages;

                            this.toShow = this.values;
                            this.toShowPages = this.totalPages;

                            this.getTitles();
                        },
                        error: (err) => {
                            console.error(err)
                        },
                    });
                    // Own values
                    // if (['reports', 'bins'].indexOf(this.title) !== false)
                    //     $.ajax({
                    //         url: `api/${this.title}?page=${this.currentPage}&user_id=${localStorage.getItem('userId')}`,
                    //         method: "GET",
                    //         headers: {
                    //             Authorization: `Bearer ${localStorage.getItem("token")}`,
                    //         },
                    //         success: (res) => {
                    //             this.ownValues = res;

                    //             if (res.hasOwnProperty('data')) this.ownValues = res.data;
                    //             if (res.hasOwnProperty('results')) this.ownValues = res.results;
                    //             if (res.hasOwnProperty('total_pages')) this.ownTotalPages = res.total_pages;

                    //             if (this.viewOwn && ['reports', 'bins'].indexOf(this.title) !== false) {
                    //                 this.toShow = this.ownValues;
                    //                 this.toShowPages = this.ownTotalPages;
                    //             }
                    //         },
                    //         error: (err) => {
                    //             console.error(err)
                    //         },
                    //     });
                }
            },
            mounted() {
                this.fixHeight();
                window.addEventListener('resize', this.fixHeight());

                window.addEventListener('beforeunload', (event) => {
                    window.removeEventListener('resize', this.fixHeight());
                    localStorage.setItem('showOwn', this.viewOwn);

                    delete e['returnValue'];
                });
            }
        })
    </script>
        <style scoped lang=scss>
        .report-photo{
            max-width: 300px;
        }
    </style>
    @include('includes/scripts', ['includeMap' => false])

</body>
