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

                    <a class="navbar-brand" href="/">e-HORECA WANET</a>
                </div>

                <div class="collapse navbar-collapse" id="main-nav">
                    <!-- // END user -->
                    </ul>
                </div>
                <!-- /.navbar-collapse -->
            </div>
        </nav>

        <div :id="!isProducer ? 'signup-content' : 'producer-signup-content'" class="container">
            <div id="vue-container" class="form-container">
                <div class="header-container text-center">
                    <div v-if="!isProducer">
                        <h1>Sign up</h1>
                        <h2>Join the program and help us gather information for a better result!</h2>
                        <h3><small>Own a company? <a href="/register?r=producer">Sign up as a large producer.</a></small></h3>
                    </div>
                    <div v-else>
                        <h1>Producer sign up</h1>
                        <h2>Join the program and help us gather information for a better result!</h2>
                        <p class="red-text">
                            New companies need to await approval from an admin.
                            <br>
                            No actions will be available until then!
                        </p>
                        <h3><small>Just a civilian? <a href="/register">Sign up as a public user.</a></small></h3>
                    </div>
                </div>
                <form action="">
                    <h3 class="input-group form-group">Your user information:</h3>
                    <div class="input-group form-group">
                        <label for="">First name</label>
                        <input id="firstName" type="text" class="form-control" placeholder="First name" v-model="name">
                    </div>
                    <div class="input-group form-group">
                        <label for="">Last name</label>
                        <input id="lastName" type="text" class="form-control" placeholder="Last name" v-model="surname">
                    </div>
                    <div class="input-group form-group">
                        <label for="">Email</label>
                        <input id="email" type="email" class="form-control" placeholder="Email" v-model="email">
                    </div>
                    <div class="input-group form-group">
                        <label for="">Username</label>
                        <input id="username" type="text" class="form-control" placeholder="Username123" v-model="username">
                    </div>

                    <div class="input-group form-group">
                        <label for="">Password</label>
                        <input id="password" type="password" class="form-control" placeholder="Password" v-model="password">
                    </div>

                    <div class="input-group form-group">
                        <label for="">Confirm Password</label>
                        <input id="passwordConfirm" type="password" class="form-control" placeholder="Password" v-model="passwordConfirm">
                    </div>

                    @verbatim
                        <div class="input-group form-group">
                            <p v-if="errMsg !== ''" class="mt-0 red-text">{{errMsg}}</p>
                        </div>
                    @endverbatim

                    <div v-if="isProducer" class="company-info-conatiner form-group mt-5">
                        <h3>Company Information</h3>
                        <div class="input-group">
                            <h4>Choose your role</h4>
                            <div class="radio-container">
                                <input type="radio" id="producer" value="w_producer" v-model="role" />
                                <label for="producer">Employer</label>
                            </div>
                            <div class="radio-container">
                                <input type="radio" id="employee" value="w_producer_employee" v-model="role" />
                                <label for="employee">Employee</label>
                            </div>
                        </div>

                        @verbatim
                            <div class="input-group mt-1">
                                <label for="company">Select a company</label>
                                <select id="company" name="company" v-model="company" @change="onChangCompanyHandler">
                                    <option v-if="role==='w_producer'" value="">Create new</option>
                                    <option v-for="comp in companies" :value="comp.id">{{comp.title}}</option>
                                </select>
                            </div>

                            <div class="input-group" v-if="company !== 'Create new' && company !== ''">
                                <label for="join_pin">Company join pin</label>
                                <p>The employer will provide this, in order to not randomly join other companies!</p>
                                <input class="form-control" type="number" id="join_pin" name="join_pin" v-model="join_pin">
                            </div>
                        @endverbatim

                        <div v-if="toAddNew" class="mt-2">
                            <h3>Company information</h3>
                            <div class="input-group">
                                <label for="title">Company title</label>
                                <p>The name of the company</p>
                                <input class="form-control" type="text" id="title" name="title" v-model="title">
                            </div>

                            <div class="input-group">
                                <label for="join_pin">Company pin</label>
                                <p>A 4-digit pin to make sure only your employees will be able to join your company</p>
                                <input class="form-control" type="number" id="join_pin" name="join_pin" v-model="join_pin">
                            </div>

                            <div class="input-group">
                                <label for="contact_name">Person of contact</label>
                                <p>The full name of the person to contact for project matters</p>
                                <input class="form-control" type="text" id="contact_name" name="contact_name" v-model="contact_name">
                            </div>

                            <div class="input-group">
                                <label for="contact_telephone">Contact number</label>
                                <p>The telephone to call for project matters. Pattern: 2661012345</p>
                                <input class="form-control" type="tel" pattern="[0-9]{10}" id="contact_telephone" name="contact_telephone" v-model="contact_telephone" @input="acceptNumber">
                                @verbatim
                                    <div class="input-group form-group">
                                        <p v-if="!isValidPhone" class="mt-0 red-text">The phone number is invalid</p>
                                    </div>
                                @endverbatim
                            </div>

                            <div class="input-group" id="map-container">
                                <label for="location">Pin location on the map</label>
                                <p>Drag the marker on the map to set the location of the main office building of your company</p>
                                <div id="map"></div>
                            </div>
                        </div>
                    </div>
                    <div class="button-container text-center">
                        <input type="submit" id="signUpButton" value="Create account" @click.prevent="onSubmitFormHandler">
                    </div>
                </form>
            </div>
            <div class="text-center signUpPrompt">
                <p>Already have an account?<a href="/login">Log in.</a></p>
            </div>
        </div>

        @include('footer')
    </div>
    <script type="application/javascript">
        var app = new Vue({
            el: '#vue-container',
            data: {
                name: '',
                surname: '',
                email: '',
                username: '',
                password: '',
                passwordConfirm : '',
                role: 'w_producer',
                company: 'Create new',
                isProducer: new URLSearchParams(window.location.search).get('r') === 'producer',
                companies: [],
                toAddNew: false,

                title: '',
                join_pin: '',
                contact_name: '',
                contact_telephone: '',
                lat: 39.625795,
                lng: 19.922098,
                errMsg: '',

                isValidPhone: false,
            },
            mounted() {
                if (this.isProducer) {
                    $.ajax({
                        url: 'api/w_producers',
                        method: 'GET',
                        success: res => {
                            this.companies = res.results
                        }
                    })
                }
            },
            methods: {
                initMap() {
                    const center = {
                        lat: this.lat,
                        lng: this.lng
                    }

                    map = new google.maps.Map(document.getElementById("map"), {
                        center,
                        zoom: 14,
                        streetViewControl: false,
                        mapTypeControlOptions: {
                            style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                            position: google.maps.ControlPosition.TOP_RIGHT
                        },
                        styles: [{
                                featureType: "poi.attraction",
                                stylers: [{
                                    visibility: "off"
                                }]
                            },
                            {
                                featureType: "poi.business",
                                stylers: [{
                                    visibility: "off"
                                }]
                            },
                            {
                                featureType: "poi.government",
                                stylers: [{
                                    visibility: "off"
                                }]
                            },
                            {
                                featureType: "poi.place_of_worship",
                                stylers: [{
                                    visibility: "off"
                                }]
                            },
                            {
                                featureType: "poi.school",
                                stylers: [{
                                    visibility: "off"
                                }]
                            },
                            {
                                featureType: "poi.park",
                                elementType: "labels",
                                stylers: [{
                                    visibility: "off"
                                }]
                            },
                            {
                                featureType: "road.local",
                                elementType: "geometry.fill",
                                stylers: [{
                                    color: "#ffffff"
                                }]
                            }
                        ]
                    });

                    marker = new google.maps.Marker({
                        position: center,
                        map,
                        title: 'My organization location',
                        draggable: true,
                        animation: google.maps.Animation.DROP
                    })

                    marker.addListener('dragend', this.getMarkerLocation);
                },
                getMarkerLocation(event) {
                    this.lat = event.latLng.lat()
                    this.lng = event.latLng.lng()
                },
                onSubmitFormHandler() {
                    this.errMsg = ''
                    if (this.password !== this.passwordConfirm) {
                        this.errMsg = 'Password missmatch'
                        return
                    }

                    if (!this.isProducer) {
                        return this.registerPublicUser()
                    }

                    if (this.role === 'w_producer') return this.registerProducer()

                    return this.registerEmployee()
                },
                registerPublicUser() {
                    if (this.name === '' || this.surname === '' || this.email === '' || this.username === '' || this.password === '') {
                        alert('Please fill all data'); // change into something more user friendly
                        return;
                    }

                    this.ajaxRegister('api/users', {
                        name: this.name,
                        surname: this.surname,
                        email: this.email,
                        username: this.username,
                        password: this.password
                    })
                },
                registerProducer() {
                    if (this.name === '' || this.surname === '' || this.email === '' || this.username === '' || this.password === '' || (this.company === '' && !this.toAddNew) || this.contact_telephone === '' || this.join_pin === '' || !this.isValidPhone) {
                        alert('Please fill all data'); // change into something more user friendly
                        return;
                    }

                    data = {
                        name: this.name,
                        surname: this.surname,
                        email: this.email,
                        username: this.username,
                        password: this.password,
                        company_id: this.company.split('-')[0],
                        join_pin: this.join_pin,
                        contact_telephone: this.contact_telephone,
                    }

                    if (this.toAddNew) {
                        delete data.company_id
                        data.title = this.title
                        data.join_pin = this.join_pin
                        data.contact_name = this.contact_name
                        data.lat = this.lat
                        data.lng = this.lng
                    }

                    this.ajaxRegister('api/w_producers/employer', data)

                },
                registerEmployee() {
                    if (this.name === '' || this.surname === '' || this.email === '' || this.username === '' || this.password === '' || this.company === '' || this.join_pin === '') {
                        alert('Please fill all data'); // change into something more user friendly
                        return;
                    }

                    this.ajaxRegister('api/w_producers/employee', {
                        name: this.name,
                        surname: this.surname,
                        email: this.email,
                        username: this.username,
                        password: this.password,
                        join_pin: this.join_pin,
                        w_producer_id: this.company
                    })
                },
                onChangCompanyHandler(event) {
                    this.toAddNew = this.company === ''
                    // wait for div to be rendered
                    setTimeout(() => {
                        this.initMap()
                    }, 100)
                },
                acceptNumber() {
                    this.isValidPhone = this.contact_telephone.match(/[0-9]{10}/g) && this.contact_telephone.toString().length === 10
                },
                ajaxRegister(url, data) {
                    $.ajax({
                        url,
                        method: 'POST',
                        data,
                        success: res => logIn(this.email, this.password, this.errorHandler),
                        error: (err) => this.errorHandler(err)
                    })
                },
                errorHandler(ajaxError) {
                    if (ajaxError.status.toString().charAt(0) == '4') this.errMsg = ajaxError.responseJSON.message
                }
            },
        })
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAWBSaVKwwQ4OFN7xtLC6sCmqkMOW-zJ-8" async defer></script>
    @include('includes/scripts', ['includeMap' => false])
</body>

</html>
