<!-- Inline Script for colors and config objects; used by various external scripts; -->
<script>
    var colors = {
        "danger-color": "#e74c3c",
        "success-color": "#81b53e",
        "warning-color": "#f0ad4e",
        "inverse-color": "#2c3e50",
        "info-color": "#2d7cb5",
        "default-color": "#6e7882",
        "default-light-color": "#cfd9db",
        "purple-color": "#9D8AC7",
        "mustard-color": "#d4d171",
        "lightred-color": "#e15258",
        "body-bg": "#f6f6f6"
    };
    var config = {
        theme: "html",
        skins: {
            "default": {
                "primary-color": "#16ae9f"
            }
        }
    };
</script>
<?php
if (isset($includeMap) && $includeMap) {
?>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAWBSaVKwwQ4OFN7xtLC6sCmqkMOW-zJ-8&amp;callback=initMap" async defer></script>
    <script src="{{asset('js/wanet/map.js')}}"></script>
<?php
}
?>
<script src="{{asset('js/vendor/all.js')}}"></script>
<script src="{{asset('js/app/essentials.js')}}"></script>
<script src="{{asset('js/app/layout.js')}}"></script>
<script src="{{asset('js/app/sidebar.js')}}"></script>
<script src="{{asset('js/app/media.js')}}"></script>
<script src="{{asset('js/app/main.js')}}"></script>
<script src="{{asset('js/wanet/main.js')}}"></script>
<script src="{{asset('js/wanet/api.js')}}"></script>
<script src="{{asset('js/wanet/list.js')}}"></script>
