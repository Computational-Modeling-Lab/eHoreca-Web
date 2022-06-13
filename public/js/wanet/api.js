const URL = window.API_URL;
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
            getUser();
        })
        .error(err => {
            callback(err);
        });
}

window.logOut = () => {
    $.ajax({
        url: `${URL}/webLogout`,
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

function getUser() {
    $.ajax({
        url: `${URL}/users/${localStorage.getItem("userId")}`,
        method: "GET",
        headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`
        },
        success: res => {
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

function getUserInfo() {
    return axios.get(
        `${URL}/users/${localStorage.getItem("userId")}`,
        {
            headers: {
              Authorization: `Bearer ${localStorage.getItem("token")}`
            }
        }
    );
}

function getProducerFromUserId () {
    return axios.get(
        `${URL}/w_producers/from_user_id/${localStorage.getItem("userId")}`,
        {
            headers: {
              Authorization: `Bearer ${localStorage.getItem("token")}`
            }
        }
    );
}

function userAuth(redirectTo = "login") {
    if (!localStorage.getItem("token")) window.location.href = redirectTo;
}

/**
 * Entity will most likely always be bin. Maybe we could store it in entity-type format (e.g. bin-mixed or bin-recycle)
 * The 'mixed' or 'recycle' could be the id of the li that is clicked
 */

function getBins(type) {
    let role;
    const localStorageUser = localStorage.getItem("user");
    if (localStorageUser) {
        const parsed = JSON.parse(localStorageUser)
        role = parsed.role;
     }

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
    let newURL = `${URL}/bins?type=${type}`;
    if (role === 'public' || !role) newURL += `&public=1`;
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
