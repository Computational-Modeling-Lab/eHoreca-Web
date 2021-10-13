<!DOCTYPE html>
<html>

<head>
    @include('includes/head')
    <script>
        $(document).ready(() => {
            userAuth()
            getUser();
            getRoutes();
            getHeatmaps();
            getReports();
            getVehicles();
        });
    </script>
</head>

<body>
    <div class="st-container" id="profile-container">
        @include('navbar')
        <div id="main-content">
            <div id="profile-menu" class="text-center">
                {{-- <div class="profile-img">
                    <img src="./images/people/guy-6.jpg" alt="bill">
                </div> --}}
                <h2 id="user_name"></h2>
                <i id="user_email"></i>
                <h5 id="user_role"></h5>
            </div>
            <div class="inner-container">
                <a href="list?table=routes&page=1" class="profile-nav">
                    Routes
                    <span id="numberOfRoutes"></span>
                </a>
                {{-- <a href="list?table=heatmaps&page=1" class="profile-nav">
                    Heatmaps
                    <span id="numberOfHeatmaps"></span>
                </a> --}}
                <a class="profile-nav">
                    Heatmaps, not yet implemented
                    <span id="numberOfHeatmaps"></span>
                </a>
                <a href="list?table=reports&page=1" class="profile-nav">
                    Reports
                    <span id="numberOfReports"></span>
                </a>
                <a v-if="role === 'admin'" href="list?table=vehicles&page=1" class="profile-nav">
                    Vehicles
                    <span id="numberOfVehicles"></span>
                </a>
            </div>
            <div v-if="role === 'admin'" class="inner-container">
                @verbatim
                <h3>Sysadmin</h3>
                <a href="list?table=users&page=1" class="profile-nav">
                    Users
                    <span>{{usersCount}}</span>
                </a>
                <a href="list?table=w_producers&page=1" class="profile-nav">
                    Large producers
                    <span>{{wproducersCount}}</span>
                </a>
                @endverbatim
            </div>
            <div id="delete-account-container" class="mt-5" v-if="role === 'public'">
                <p class="red-text underline text-center" @click="onDeleteHandler">Delete my account</p>
            </div>
        </div>

        @include('footer')
    </div>

    <script>
        $(document).ready(function() {
            $("#main-content").height($(window).height() - $(".navbar-fixed-top").height() - $("#footer").height() - 70);
            $("#main-content").css('margin-top', $(".navbar-fixed-top").height() + 20);
        });
    </script>
    <script>
        var app = new Vue({
            el: '#profile-container',
            data: {
                role: JSON.parse(localStorage.getItem('user')).role,
                usersCount: 0,
                wproducersCount: 0,
            },
            mounted() {
                if (this.role === 'admin') {
                    this.getUsers();
                    this.getProducers();
                }
            },
            methods: {
                onDeleteHandler() {
                    if (confirm('Are you sure you want to delete your account? You cannot undo this action.')) {
                        $.ajax({
                            url: `api/users/${localStorage.getItem('userId')}`,
                            method: 'DELETE',
                            headers: {
                                Authorization: `Bearer ${localStorage.getItem('token')}`
                            },
                            success: res => {
                                localStorage.clear()
                                window.location.href = '/'
                            }
                        })
                    }
                },
                getUsers() {
                    $.ajax({
                        url: `api/users`,
                        method: 'GET',
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem('token')}`
                        },
                        success: res => {
                            this.usersCount = res.total_results
                        }
                    })
                },
                getProducers() {
                    $.ajax({
                        url: `api/w_producers`,
                        method: 'GET',
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem('token')}`
                        },
                        success: res => {
                            this.wproducersCount = res.total_results
                        }
                    })
                }
            }
        })
    </script>

    @include('includes/scripts', ['includeMap' => false])

</body>

</html>
