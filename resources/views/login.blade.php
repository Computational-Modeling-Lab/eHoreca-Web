<!DOCTYPE html>
<html>

<head>
    @include('includes/head')

</head>

<body>
    <div class="st-container">
        <!-- Fixed navbar -->
        <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a href="#sidebar-menu" data-toggle="sidebar-menu" class="toggle pull-left visible-xs"><i class="fa fa-bars"></i></a>

                    <a class="navbar-brand" href="/login">e-HORECA WANET</a>
                </div>

                <div class="collapse navbar-collapse" id="main-nav">
                    <!-- // END user -->
                    </ul>
                </div>
                <!-- /.navbar-collapse -->
            </div>
        </nav>

        <div id="login-content" class="container">
            <div class="form-container">
                <div class="header-container text-center">
                    <h1>Log in</h1>
                </div>
                <form action="" @submit.prevent="onSubmitFormHandler">
                    <div class="input-group form-group">
                        <label for="">E-mail</label>
                        <input id="email" type="email" class="form-control" placeholder="your e-mail" v-model="email">
                    </div>
                    <div class="input-group form-group">
                        <label for="">Password</label>
                        <input id="password" type="password" class="form-control" placeholder="password" v-model="password">
                    </div>
                    @verbatim
                    <div class="input-group form-group">
                        <p v-if="errMsg !== ''" class="mt-0 red-text">{{errMsg}}</p>
                    </div>
                    @endverbatim
                    <div class="button-container text-center">
                        <input type="submit" id="logInButton" value="Log in">
                    </div>
                </form>
            </div>
            <div class="text-center signUpPrompt">
                <p>Don't have an account?<a href="/register">Sign up.</a></p>
            </div>
        </div>

        @include('footer')

        <script type="application/javascript">
            var app = new Vue({
                el: '#login-content',
                data: {
                    email: '',
                    password: '',
                    errMsg: '',
                },
                methods: {
                    onSubmitFormHandler(e) {
                        this.errMsg = ''
                        logIn(this.email, this.password, this.errorHandler)
                    },
                    errorHandler(ajaxError) {
                        if (ajaxError.status.toString().charAt(0) == '4') this.errMsg = ajaxError.responseJSON.message
                    }
                }
            })
        </script>


        @include('includes/scripts', ['includeMap' => false])
    </div>
</body>

</html>