<head>
    @include('includes/head')
    <script>
        $(document).ready(() => {
            userAuth()
            const userObj = JSON.parse(localStorage.getItem('user'))
        });
    </script>
</head>

<body>
    <div class="st-container" id="vue-container">
        @include('navbar');
        @verbatim
        <template>
            <div id="app">
                <create-bin v-if="entity === 'bins'" :instance="role" :producerid="pid"></create-bin>
                <create-route v-if="entity === 'routes'"></create-route>
                <create-heatmap v-if="entity === 'heatmaps'"></create-heatmap>
                <create-vehicle v-if="entity === 'vehicles'"></create-vehicle>
                <create-user v-if="entity === 'users'"></create-user>
                <create-wproducer v-if="entity === 'w_producers'"></create-wproducer>
            </div>
        </template>
        @endverbatim
        @include('footer')
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
    <script type="module">
        var app = new Vue({
            el: '#vue-container',
            data: {
                entity: new URLSearchParams(window.location.search).get('entity'),
                token: localStorage.getItem('token'),
                userId: localStorage.getItem('userId'),
                role: localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')).role : '',
                pid: null
            },
            methods: {
                async getProducerInfo() {
                    try {
                        const userData = await getProducerFromUserId();
                        console.log('producer:', userData.data.id);
                        if (userData) this.pid = userData.data.id;
                    } catch (error) {
                        console.log('userdata error:', error);
                    }
                }
            },
            created() {
                if (this.role === 'w_producer' || this.role === 'w_producer_employee') this.getProducerInfo();
            }
        })
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAWBSaVKwwQ4OFN7xtLC6sCmqkMOW-zJ-8" async defer></script>
    @include('includes/scripts', ['includeMap' => false])
</body>
