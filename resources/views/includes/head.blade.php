<script>
    const {
        pathname
    } = window.location

    if(pathname !== '/register' && pathname !== '/login' && pathname !== '/' && pathname !== '/privacy'){
        if(!localStorage.getItem('token')){
            window.location.replace("/login");
        }
    }

    window.API_URL = `{{ config('app.api_url')}}`;
</script>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<title>e-HORECA WANET</title>

<link href="{{ asset('css/vendor/all.css') }}" rel="stylesheet">
<link href="{{asset('css/app/custom.css')}}" rel="stylesheet">
<link href="{{asset('css/app/app.css')}}" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/luxon/1.25.0/luxon.min.js" integrity="sha512-OyrI249ZRX2hY/1CAD+edQR90flhuXqYqjNYFJAiflsKsMxpUYg5kbDDAVA8Vp0HMlPG/aAl1tFASi1h4eRoQw==" crossorigin="anonymous"></script>
<!-- <script src="weekstart.js"></script> -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vue-datetime@1.0.0-beta.10/dist/vue-datetime.min.css">
<script src="https://cdn.jsdelivr.net/npm/vue-datetime@1.0.0-beta.10/dist/vue-datetime.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
