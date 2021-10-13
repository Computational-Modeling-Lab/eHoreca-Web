<head>
    @include('includes/head')
    <script>
        $(document).ready(() => {
            userAuth()
            const userObj = JSON.parse(localStorage.getItem('user'))
            if (userObj.role !== 'admin') window.location.href = '/login'
        });
    </script>
</head>

<body>
    <div class="st-container" id="vue-container">
        @include('navbar');
        @verbatim
        <template>
            <div id="app">
                <create-bin v-if="entity === 'bins'" :instance="'admin'" :w_producer="{id: null}"></create-bin>
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
            }
        })
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAWBSaVKwwQ4OFN7xtLC6sCmqkMOW-zJ-8" async defer></script>
    @include('includes/scripts', ['includeMap' => false])
</body>
