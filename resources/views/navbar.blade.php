<!-- Fixed navbar -->
<nav id="vue-navbar" class="navbar navbar-default navbar-fixed-top" role="navigation" ref="navBar">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-nav">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="#sidebar-menu" data-toggle="sidebar-menu" class="toggle pull-left visible-xs"><i class="fa fa-bars"></i></a>

            <!-- This button (burger-menu) is shown in the web app. I commented it out for now because there is no purpose for it since it's not populated. -->

            <!-- <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-nav">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button> -->

            <a class="navbar-brand" href="/">e-HORECA WANET</a>
        </div>

        <div class="collapse navbar-collapse" id="main-nav">
            {{-- <form class="navbar-form navbar-left margin-none ">
                <div class="search-1">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="icon-search"></i></span>
                        <input type="text" class="form-control form-control-w-150" placeholder="Search ..">
                    </div>
                </div>
            </form> --}}
            <ul class="nav navbar-nav navbar-right ">
                <!-- Admin -->
                <li class="dropdown" v-if="token !== null && role!=='public'">
                    <a href="list?table=reports&page=1" class="dropdown-toggle">
                        <!-- <i class="fa fa-fw fa-lock"></i> -->
                        Reports
                    </a>
                </li>
                <li class="dropdown" v-if="token !== null">
                    <a href="list?table=bins&page=1" class="dropdown-toggle">
                        <!-- <i class="fa fa-fw fa-plus"></i>  -->
                        Bins
                    </a>
                </li>
                <li class="dropdown" v-if="role === 'admin'">
                    <a href="list?table=routes&page=1" class="dropdown-toggle">
                        <!-- <i class="fa fa-fw fa-plus"></i>  -->
                        Routes
                    </a>
                </li>
                <li class="dropdown" v-if="role === 'admin'">
                    <a href="list?table=vehicles&page=1" class="dropdown-toggle">
                        <!-- <i class="fa fa-fw fa-lock"></i> -->
                        Vehicles
                    </a>
                </li>
                <li class="dropdown" v-if="role === 'admin'">
                    <a href="list?table=users&page=1" class="dropdown-toggle">
                        <!-- <i class="fa fa-fw fa-lock"></i> -->
                        Users
                    </a>
                </li>
                <li class="dropdown" v-if="role === 'admin'">
                    <a href="list?table=w_producers&page=1" class="dropdown-toggle">
                        <!-- <i class="fa fa-fw fa-lock"></i> -->
                        Large Producers
                    </a>
                </li>

                <!-- user -->
                <li class="dropdown">
                    <a href="privacy" class="dropdown-toggle">
                        <!-- <i class="fa fa-fw fa-lock"></i> -->
                        Privacy
                    </a>
                </li>
                <li class="dropdown user">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        @verbatim
                        {{name}}<span class="caret"></span>
                        @endverbatim
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li v-if="token !== null"><a href="/profile"><i class="fa fa-user"></i>Profile</a></li>
                        <!-- <li><a href="#"><i class="fa fa-wrench"></i>Settings</a></li> -->
                        <li v-if="token !== null">
                            <a href="javascript:logOut()"><i class="fa fa-sign-out"></i>Logout</a>
                        </li>

                        <li v-if="token === null">
                            <a href="/login"><i class="fa fa-sign-in"></i>Log in</a>
                        </li>
                    </ul>
                </li>
                <!-- // END user -->
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
</nav>

<script type="application/javascript">
    var app = new Vue({
        el: '#vue-navbar',
        data: {
            name: localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')).name : '',
            role: localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')).role : '',
            token: localStorage.getItem('token'),
        },
        methods: {
            logOut() {
                window.logOut()
            }
        }
    })
</script>
