// const URL = "http://localhost:3000/api";
const URL = "https://ehoreca.cmodlab-iu.edu.gr/api";

var endpointsCalled = [];

function logIn(email, password, callback) {
    const body = {
        email,
        password
    };
    $.post(`${URL}/login`, body)
        .done(function(res) {
            localStorage.setItem("token", res.token);
            localStorage.setItem("userId", res.id);
            getUser(false);
        })
        .error(err => {
            callback(err);
        });
}

window.logOut = () => {
    $.ajax({
        url: `${URL}/logout`,
        type: "POST",
        headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`
        },
        success: res => {
            localStorage.clear();
            window.location.href = "/";
            return;
        },
        error: err => {}
    });
};

function getUser(isProfilePage = true) {
    $.ajax({
        url: `${URL}/users/${localStorage.getItem("userId")}`,
        method: "GET",
        headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`
        },
        success: res => {
            if (isProfilePage) {
                $("#user_name").text(res.name + " " + res.surname);
                $("#user_email").text(res.email);
                $("#user_role").text(res.role);
                return;
            }
            localStorage.setItem(
                "user",
                JSON.stringify({
                    name: res.name,
                    surname: res.surname,
                    role: res.role
                })
            );
            window.location.href = "/";
        }
    });
}

function userAuth(redirectTo = "login") {
    if (!localStorage.getItem("token")) window.location.href = redirectTo;
}

/**
 * Entity will most likely always be bin. Maybe we could store it in entity-type format (e.g. bin-mixed or bin-recycle)
 * The 'mixed' or 'recycle' could be the id of the li that is clicked
 */

function getBins(type) {
    bounds = new google.maps.LatLngBounds();
    if (!endpointsCalled.includes(type)) {
        $("#" + type).addClass("active");
        endpointsCalled.push(type);
    } else {
        $("#" + type).removeClass("active");
        endpointsCalled.pop(type);
        clearMap(type);
        return;
    }
    newURL = `${URL}/bins?type=${type}`;

    $.ajax({
        url: newURL,
        crossDomain: true
    }).done(function(data) {
        // Check if there are bins returned from the database
        if (!$.isEmptyObject(data)) {
            // icon = type == "mixed" ? "green" : "blue";

            switch (type)
            {
                case 'compost':
                    icon = 'yellow'
                    break;
                case 'glass':
                    icon = 'blue'
                    break;
                case 'recyclable':
                    icon = 'blue'
                    break;
                case 'mixed':
                    icon = 'green'
                    break;
                case 'metal':
                    icon = 'blue'
                    break;
                case 'paper':
                    icon = 'yellow'
                    break;
                case 'plastic':
                    icon = 'blue'
                    break;
            }

            addMarks2Map(data);
        }
    });
}

function getRoutes() {
    const url =
        JSON.parse(localStorage.getItem("user")).role !== "admin"
            ? `${URL}/users/${localStorage.getItem("userId")}/routes`
            : `${URL}/routes`;
    $.ajax({
        url,
        method: "GET",
        headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`
        },
        success: res => {
            $("#numberOfRoutes").html(res.results.length);
        },
        error: err => {
            $("#numberOfRoutes").html(0);
        }
    });
}

function getHeatmaps() {
    const url =
        JSON.parse(localStorage.getItem("user")).role !== "admin"
            ? `${URL}/users/${localStorage.getItem("userId")}/heatmaps`
            : `${URL}/heatmaps`;
    $.ajax({
        url,
        method: "GET",
        headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`
        },
        success: res => {
            $("#numberOfHeatmaps").html(res.results.length);
        },
        error: err => {
            $("#numberOfHeatmaps").html(0);
        }
    });
}

function getReports() {
    const url =
        JSON.parse(localStorage.getItem("user")).role !== "admin"
            ? `${URL}/users/${localStorage.getItem("userId")}/reports`
            : `${URL}/reports`;
    $.ajax({
        url,
        method: "GET",
        headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`
        },
        success: res => {
            $("#numberOfReports").html(res.results.length);
        },
        error: err => {
            $("#numberOfReports").html(0);
        }
    });
}

function getVehicles() {
    $.ajax({
        url: `${URL}/vehicles`,
        method: "GET",
        headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`
        },
        success: res => {
            $("#numberOfVehicles").html(res.results.length);
        },
        error: err => {
            $("#numberOfVehicles").html(0);
        }
    });
}
