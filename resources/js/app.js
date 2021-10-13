/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");

window.Vue = require("vue");

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component("update-route", require("./components/UpdateRoute.vue").default);
Vue.component(
    "update-heatmap",
    require("./components/UpdateHeatmap.vue").default
);
Vue.component(
    "update-vehicle",
    require("./components/UpdateVehicle.vue").default
);
Vue.component(
    "update-report",
    require("./components/UpdateReport.vue").default
);
Vue.component("update-bin", require("./components/UpdateBin.vue").default);
Vue.component("create-route", require("./components/CreateRoute.vue").default);
Vue.component(
    "create-heatmap",
    require("./components/CreateHeatmap.vue").default
);
Vue.component(
    "create-vehicle",
    require("./components/CreateVehicle.vue").default
);
Vue.component("create-bin", require("./components/CreateBin.vue").default);

Vue.component("create-user", require("./components/CreateUser.vue").default);
Vue.component("update-user", require("./components/UpdateUser.vue").default);

Vue.component(
    "create-wproducer",
    require("./components/CreateWProducer.vue").default
);
Vue.component(
    "update-wproducer",
    require("./components/UpdateWProducer.vue").default
);

Vue.component("vue-datetime", window.VueDatetime.Datetime);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: "#app",
    methods: {}
});
